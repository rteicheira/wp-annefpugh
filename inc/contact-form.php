<?php
/**
 * Contact form handler (both the Contact page form and the homepage
 * "quick" form), sending to the email set under Practice Info.
 *
 * Spam defenses, in order: honeypot field, signed form timestamp (catches
 * instant bot submissions; unlike a nonce it never expires, so it works on
 * cached pages), per-visitor rate limit, optional reCAPTCHA.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const ANNEFPUGH_FORM_MIN_SECONDS = 3;          // Faster than this is a bot.
const ANNEFPUGH_RATE_LIMIT       = 5;          // Messages per visitor...
const ANNEFPUGH_RATE_WINDOW      = 600;        // ...per 10 minutes.
const ANNEFPUGH_HONEYPOT_FIELD   = 'afp_extra_field';

/**
 * Field length limits, enforced on the server (the form's maxlength
 * attributes only stop browsers).
 */
function annefpugh_contact_limits() {
	return array(
		'annefpugh_name'    => 100,
		'annefpugh_email'   => 150,
		'annefpugh_phone'   => 30,
		'annefpugh_message' => 2000,
	);
}

/**
 * "<unix time>.<hmac>" — printed into each form when it's rendered.
 */
function annefpugh_form_token() {
	$time = (string) time();
	return $time . '.' . hash_hmac( 'sha256', $time, wp_salt( 'nonce' ) );
}

/**
 * Seconds since a form token was rendered, or false if it's missing/forged.
 */
function annefpugh_form_token_age( $token ) {
	$parts = explode( '.', (string) $token, 2 );
	if ( 2 !== count( $parts ) || ! ctype_digit( $parts[0] ) ) {
		return false;
	}
	if ( ! hash_equals( hash_hmac( 'sha256', $parts[0], wp_salt( 'nonce' ) ), $parts[1] ) ) {
		return false;
	}
	return time() - (int) $parts[0];
}

function annefpugh_rate_limit_key() {
	return 'annefpugh_rl_' . md5( annefpugh_client_ip() . wp_salt( 'nonce' ) );
}

function annefpugh_contact_redirect( $status, $base, $form_id ) {
	wp_safe_redirect(
		add_query_arg(
			array(
				'contact' => $status,
				'form'    => $form_id,
			),
			$base
		) . '#' . $form_id
	);
	exit;
}

/**
 * A POST value as a trimmed, length-limited string ('' for arrays/missing).
 */
function annefpugh_contact_field( $key ) {
	$limits = annefpugh_contact_limits();
	// phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput -- public form (see header); sanitized by the caller.
	$value = isset( $_POST[ $key ] ) && is_string( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';
	return isset( $limits[ $key ] ) ? mb_substr( $value, 0, $limits[ $key ] ) : $value;
}

function annefpugh_handle_contact_form() {
	// phpcs:disable WordPress.Security.NonceVerification -- public, logged-out form; protected by the signed timestamp, rate limit, and reCAPTCHA instead of a nonce (nonces expire on cached pages).
	$base    = wp_get_referer() ? remove_query_arg( array( 'contact', 'form' ), wp_get_referer() ) : home_url( '/' );
	$form_id = sanitize_key( annefpugh_contact_field( 'annefpugh_form' ) );
	$form_id = '' !== $form_id ? $form_id : 'contact-form';

	// 1. Honeypot: humans never see or fill this field. Pretend it worked.
	if ( '' !== annefpugh_contact_field( ANNEFPUGH_HONEYPOT_FIELD ) ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'annefpugh contact form: honeypot filled, message discarded.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
		}
		annefpugh_contact_redirect( 'sent', $base, $form_id );
	}

	// 2. Signed timestamp: missing/forged → stale page; too fast → bot.
	$age = annefpugh_form_token_age( annefpugh_contact_field( 'annefpugh_ts' ) );
	if ( false === $age ) {
		annefpugh_contact_redirect( 'expired', $base, $form_id );
	}
	if ( $age < ANNEFPUGH_FORM_MIN_SECONDS ) {
		annefpugh_contact_redirect( 'spam', $base, $form_id );
	}

	// 3. Rate limit per visitor.
	$rate_key = annefpugh_rate_limit_key();
	$hits     = (int) get_transient( $rate_key );
	if ( $hits >= ANNEFPUGH_RATE_LIMIT ) {
		annefpugh_contact_redirect( 'busy', $base, $form_id );
	}

	// 4. reCAPTCHA (when configured).
	$captcha = annefpugh_recaptcha_verify( sanitize_text_field( annefpugh_contact_field( 'g-recaptcha-response' ) ) );
	if ( 'fail' === $captcha ) {
		annefpugh_contact_redirect( 'spam', $base, $form_id );
	}

	// 5. Validate.
	$quick   = 'quick' === annefpugh_contact_field( 'annefpugh_variant' );
	$name    = sanitize_text_field( annefpugh_contact_field( 'annefpugh_name' ) );
	$email   = sanitize_email( annefpugh_contact_field( 'annefpugh_email' ) );
	$phone   = sanitize_text_field( annefpugh_contact_field( 'annefpugh_phone' ) );
	$message = sanitize_textarea_field( annefpugh_contact_field( 'annefpugh_message' ) );
	// phpcs:enable

	$phone_ok = strlen( preg_replace( '/\D/', '', $phone ) ) >= 7;
	$email_ok = (bool) is_email( $email );
	// Quick form: phone is the way to reply. Full form: email is.
	$reachable = $quick ? $phone_ok : $email_ok;

	if ( '' === $name || '' === $message || ! $reachable ) {
		annefpugh_contact_redirect( 'invalid', $base, $form_id );
	}

	// 6. Send.
	set_transient( $rate_key, $hits + 1, ANNEFPUGH_RATE_WINDOW );

	$to       = is_email( annefpugh_mod( 'email' ) ) ? annefpugh_mod( 'email' ) : get_option( 'admin_email' );
	$site     = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$subject  = $quick
		/* translators: %s: site name */
		? sprintf( __( 'Call-back request — %s', 'annefpugh' ), $site )
		/* translators: %s: site name */
		: sprintf( __( 'New consultation request — %s', 'annefpugh' ), $site );
	if ( 'unverified' === $captcha ) {
		$subject = __( '[Unverified] ', 'annefpugh' ) . $subject;
	}

	$lines = array( 'Name: ' . $name );
	if ( $email_ok ) {
		$lines[] = 'Email: ' . $email;
	}
	if ( '' !== $phone ) {
		$lines[] = 'Phone: ' . $phone;
	}
	$lines[] = '';
	$lines[] = $quick ? 'What they\'re looking for:' : 'Message:';
	$lines[] = $message;
	if ( 'unverified' === $captcha ) {
		$lines[] = '';
		$lines[] = '(Spam protection couldn\'t check this message — the sender\'s browser may block Google. It\'s probably a real person, but use your judgment.)';
	}

	// wp_mail() splits Reply-To on commas, so strip address-syntax characters from the name.
	$reply_name = trim( preg_replace( '/\s+/', ' ', preg_replace( '/[",;<>]+/', ' ', $name ) ) );
	$headers    = $email_ok ? array( 'Reply-To: ' . $reply_name . ' <' . $email . '>' ) : array();

	$log_failure = function ( $error ) {
		error_log( 'annefpugh contact form: wp_mail failed: ' . $error->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
	};
	add_action( 'wp_mail_failed', $log_failure );
	$sent = wp_mail( $to, $subject, implode( "\n", $lines ), $headers );
	remove_action( 'wp_mail_failed', $log_failure );

	annefpugh_contact_redirect( $sent ? 'sent' : 'error', $base, $form_id );
}
add_action( 'admin_post_annefpugh_contact', 'annefpugh_handle_contact_form' );
add_action( 'admin_post_nopriv_annefpugh_contact', 'annefpugh_handle_contact_form' );

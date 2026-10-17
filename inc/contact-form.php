<?php
/**
 * Contact form handler (both the Contact page form and the homepage
 * "quick" form), sending to the email set under Practice Info.
 *
 * Spam defenses come from inc/form-guard.php: honeypot, signed timestamp,
 * per-visitor rate limit, optional Turnstile/reCAPTCHA, and a link check.
 * Doubtful messages are delivered with a warning in the subject, never dropped.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
	// phpcs:disable WordPress.Security.NonceVerification -- public, logged-out form; protected by the signed timestamp, rate limit, and Turnstile/reCAPTCHA instead of a nonce (nonces expire on cached pages).
	$base    = wp_get_referer() ? remove_query_arg( array( 'contact', 'form' ), wp_get_referer() ) : home_url( '/' );
	$form_id = sanitize_key( annefpugh_contact_field( 'annefpugh_form' ) );
	$form_id = '' !== $form_id ? $form_id : 'contact-form';

	// 1. Spam checks (inc/form-guard.php). A filled honeypot pretends it worked.
	$guard    = annefpugh_form_guard_check( 'contact', array( 'text' => annefpugh_contact_field( 'annefpugh_message' ) ) );
	$redirect = array(
		'honeypot' => 'sent',
		'expired'  => 'expired',
		'too_fast' => 'spam',
		'busy'     => 'busy',
		'spam'     => 'spam',
	);
	if ( isset( $redirect[ $guard['status'] ] ) ) {
		annefpugh_contact_redirect( $redirect[ $guard['status'] ], $base, $form_id );
	}
	$unverified = in_array( 'unverified', $guard['flags'], true );
	$links      = in_array( 'links', $guard['flags'], true );

	// 2. Validate.
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

	// 3. Send.
	annefpugh_form_guard_record( 'contact' );

	$to       = is_email( annefpugh_mod( 'email' ) ) ? annefpugh_mod( 'email' ) : get_option( 'admin_email' );
	$site     = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$subject  = $quick
		/* translators: %s: site name */
		? sprintf( __( 'Call-back request — %s', 'annefpugh' ), $site )
		/* translators: %s: site name */
		: sprintf( __( 'New consultation request — %s', 'annefpugh' ), $site );
	if ( $links ) {
		$subject = __( '[Possible spam] ', 'annefpugh' ) . $subject;
	} elseif ( $unverified ) {
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
	if ( $links ) {
		$lines[] = '';
		$lines[] = '(This message contains several links, which is common in spam. Don\'t click them unless you recognize the sender.)';
	}
	if ( $unverified ) {
		$lines[] = '';
		$lines[] = '(Spam protection couldn\'t check this message — the sender\'s browser may block the security check. It\'s probably a real person, but use your judgment.)';
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

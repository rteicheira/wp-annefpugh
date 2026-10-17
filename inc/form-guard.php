<?php
/**
 * Anti-spam guard for every form the public can submit.
 *
 * Defenses, cheapest first:
 *  1. Honeypot field: hidden from people; bots fill it in.
 *  2. Signed timestamp: rejects forged, missing, or too-fast submissions.
 *     Unlike a nonce it never expires, so forms keep working on cached pages.
 *  3. Rate limit per visitor (IP), per form.
 *  4. Turnstile / reCAPTCHA when switched on (inc/spam-protection.php).
 *  5. Link check: messages stuffed with links are flagged, never dropped.
 *
 * Forms covered: the contact forms (inc/contact-form.php), password-protected
 * pages, password reset, and log in (inc/spam-protection.php). Registration
 * is switched off: this site has no member accounts.
 *
 * With Wordfence active, the log-in side (page passwords, password reset,
 * and the log-in captcha) is left to Wordfence; the contact-form guard
 * always stays on. See annefpugh_defer_to_wordfence().
 *
 * ADDING A NEW PUBLIC FORM — every new form must use this:
 *  1. Inside the <form>, echo annefpugh_form_guard_fields( 'my-form' ), and
 *     annefpugh_captcha_field_html( 'contact' ) just before the submit button.
 *  2. In the handler, first call annefpugh_form_guard_check( 'my-form' ) and
 *     stop unless its status is 'ok'.
 *  3. Once the submission is accepted, call annefpugh_form_guard_record( 'my-form' ).
 *  4. Treat the 'unverified' and 'links' flags as "deliver, but mark as
 *     possibly spam" — a real person's message should never be lost.
 */

defined( 'ABSPATH' ) || exit;

const ANNEFPUGH_FORM_MIN_SECONDS = 3;          // Faster than this is a bot.
const ANNEFPUGH_RATE_LIMIT       = 5;          // Submissions per visitor...
const ANNEFPUGH_RATE_WINDOW      = 600;        // ...per 10 minutes.
const ANNEFPUGH_HONEYPOT_FIELD   = 'afp_extra_field';
const ANNEFPUGH_MAX_LINKS        = 2;          // More links than this: flagged as possible spam.


// ── SIGNED TIMESTAMP ──────────────────────────────────────────
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


// ── RATE LIMIT ────────────────────────────────────────────────
function annefpugh_rate_limit_key( $form_id = 'contact' ) {
	return 'annefpugh_rl_' . md5( $form_id . '|' . annefpugh_client_ip() . wp_salt( 'nonce' ) );
}

/**
 * Whether this visitor has used up the form's allowance.
 */
function annefpugh_rate_limited( $form_id, $limit = ANNEFPUGH_RATE_LIMIT ) {
	return (int) get_transient( annefpugh_rate_limit_key( $form_id ) ) >= $limit;
}

/**
 * Count one submission (or attempt) against the visitor's allowance.
 */
function annefpugh_form_guard_record( $form_id, $window = ANNEFPUGH_RATE_WINDOW ) {
	$key = annefpugh_rate_limit_key( $form_id );
	set_transient( $key, (int) get_transient( $key ) + 1, $window );
}


// ── FORM MARKUP ───────────────────────────────────────────────
/**
 * Hidden timestamp + honeypot, printed inside the form.
 *
 * @param string $form_id Unique per form on the page (used for the field id).
 */
function annefpugh_form_guard_fields( $form_id ) {
	$id = sanitize_html_class( $form_id ) . '-afp-extra';
	return '<input type="hidden" name="annefpugh_ts" value="' . esc_attr( annefpugh_form_token() ) . '">'
		// Spam trap: hidden from people; deliberately not named like anything browsers autofill.
		. '<div class="form-field form-field--hp" aria-hidden="true">'
		. '<label for="' . esc_attr( $id ) . '">Leave this field empty</label>'
		. '<input type="text" id="' . esc_attr( $id ) . '" name="' . esc_attr( ANNEFPUGH_HONEYPOT_FIELD ) . '" value="" tabindex="-1" autocomplete="new-password">'
		. '</div>';
}


// ── CHECK ─────────────────────────────────────────────────────
/**
 * Number of links in a piece of text (URLs, www. addresses, BBCode/HTML links).
 */
function annefpugh_count_links( $text ) {
	return (int) preg_match_all( '~(?:https?://|www\.|\[url|<a\s)~i', (string) $text );
}

/**
 * Run every check for a submitted form.
 *
 * @param string $form_id Form name, also the rate-limit bucket.
 * @param array  $args {
 *     @type string|false $captcha     Provider action to verify ('contact', 'login', 'comment'), or false.
 *     @type bool         $strict      Refuse a missing captcha token instead of flagging it (log-in style).
 *     @type int          $limit       Submissions allowed per window.
 *     @type int          $min_seconds Minimum time on the page.
 *     @type string       $text        Message text to scan for links.
 * }
 * @return array {
 *     @type string $status 'ok', 'honeypot', 'expired', 'too_fast', 'busy', or 'spam'.
 *     @type array  $flags  'unverified' (captcha couldn't run) and/or 'links' (link-stuffed text).
 * }
 */
function annefpugh_form_guard_check( $form_id, $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'captcha'     => 'contact',
			'strict'      => false,
			'limit'       => ANNEFPUGH_RATE_LIMIT,
			'min_seconds' => ANNEFPUGH_FORM_MIN_SECONDS,
			'text'        => '',
		)
	);
	$result = array(
		'status' => 'ok',
		'flags'  => array(),
	);

	// phpcs:disable WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput -- public forms; see file header. Values are only compared.
	$honeypot = isset( $_POST[ ANNEFPUGH_HONEYPOT_FIELD ] ) ? (string) wp_unslash( $_POST[ ANNEFPUGH_HONEYPOT_FIELD ] ) : '';
	$token    = isset( $_POST['annefpugh_ts'] ) && is_string( $_POST['annefpugh_ts'] ) ? wp_unslash( $_POST['annefpugh_ts'] ) : '';
	// phpcs:enable

	if ( '' !== $honeypot ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'annefpugh form guard: honeypot filled on ' . $form_id . ', submission discarded.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
		}
		$result['status'] = 'honeypot';
		return $result;
	}

	$age = annefpugh_form_token_age( $token );
	if ( false === $age ) {
		$result['status'] = 'expired';
		return $result;
	}
	if ( $age < $args['min_seconds'] ) {
		$result['status'] = 'too_fast';
		return $result;
	}

	if ( annefpugh_rate_limited( $form_id, $args['limit'] ) ) {
		$result['status'] = 'busy';
		return $result;
	}

	if ( $args['captcha'] && annefpugh_captcha_protects( $args['captcha'] ) ) {
		$captcha_token = annefpugh_captcha_posted_token();
		if ( $args['strict'] && '' === $captcha_token ) {
			$result['status'] = 'spam';
			return $result;
		}
		$verdict = annefpugh_captcha_verify( $captcha_token, $args['captcha'] );
		if ( 'fail' === $verdict ) {
			$result['status'] = 'spam';
			return $result;
		}
		if ( 'unverified' === $verdict ) {
			$result['flags'][] = 'unverified';
		}
	}

	if ( '' !== $args['text'] && annefpugh_count_links( $args['text'] ) > (int) apply_filters( 'annefpugh_max_links', ANNEFPUGH_MAX_LINKS ) ) {
		$result['flags'][] = 'links';
	}

	return $result;
}


// ── PASSWORD-PROTECTED PAGES ──────────────────────────────────
// The page-password form posts to wp-login.php?action=postpass, which
// WordPress doesn't rate-limit, so it could be used to guess page passwords.
const ANNEFPUGH_POSTPASS_LIMIT = 10; // Attempts per visitor per 10 minutes.

function annefpugh_guard_password_form( $form ) {
	if ( annefpugh_defer_to_wordfence() ) {
		return $form;
	}
	$form = preg_replace( '~(<form\b[^>]*>)~i', '$1' . annefpugh_form_guard_fields( 'postpass' ), $form, 1 );
	// Same Turnstile/reCAPTCHA setting as the log-in page.
	return str_ireplace( '</form>', annefpugh_captcha_field_html( 'login' ) . '</form>', $form );
}
add_filter( 'the_password_form', 'annefpugh_guard_password_form', 20 );

function annefpugh_guard_postpass() {
	if ( annefpugh_defer_to_wordfence() ) {
		return;
	}
	if ( 'POST' !== ( isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : '' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- compared to a literal.
		return;
	}
	$check = annefpugh_form_guard_check(
		'postpass',
		array(
			'captcha'     => 'login',
			'strict'      => true,
			'limit'       => ANNEFPUGH_POSTPASS_LIMIT,
			'min_seconds' => 1,
		)
	);
	// Every attempt counts, right or wrong: this limit is about guessing.
	annefpugh_form_guard_record( 'postpass' );

	if ( 'ok' === $check['status'] ) {
		return;
	}
	$messages = array(
		'busy'    => __( 'Too many password attempts. Please wait 10 minutes, then try again.', 'annefpugh' ),
		'expired' => __( 'This page was open too long or didn\'t load fully. Please go back, refresh the page, and try again.', 'annefpugh' ),
		'spam'    => __( 'Please complete the security check, then try again.', 'annefpugh' ),
	);
	$status = isset( $messages[ $check['status'] ] ) ? $check['status'] : 'spam';
	wp_die(
		esc_html( $messages[ $status ] ),
		esc_html__( 'Please try again', 'annefpugh' ),
		array(
			'response'  => 'busy' === $status ? 429 : 403,
			'back_link' => true,
		)
	);
}
add_action( 'login_form_postpass', 'annefpugh_guard_postpass', 1 );


// ── PASSWORD RESET ────────────────────────────────────────────
// Stops the "Lost your password?" form being used to flood an inbox with
// reset emails, with or without Turnstile/reCAPTCHA switched on.
const ANNEFPUGH_RESET_LIMIT = 5; // Requests per visitor per hour.

function annefpugh_guard_lost_password( $errors ) {
	if ( annefpugh_defer_to_wordfence() ) {
		return;
	}
	if ( annefpugh_rate_limited( 'lostpassword', ANNEFPUGH_RESET_LIMIT ) ) {
		$errors->add( 'afp_reset_busy', __( '<strong>Error:</strong> Too many password reset requests. Please wait an hour, then try again.', 'annefpugh' ) );
		return;
	}
	annefpugh_form_guard_record( 'lostpassword', HOUR_IN_SECONDS );
}
add_action( 'lostpassword_post', 'annefpugh_guard_lost_password', 5 );


// ── NO SIGN-UPS ───────────────────────────────────────────────
// No member accounts on this site, so "Anyone can register" is always off:
// no spam accounts, and wp-login.php?action=register just goes back to log in.
add_filter( 'pre_option_users_can_register', '__return_zero' );

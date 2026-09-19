<?php
/**
 * Contact form submission handler.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function annefpugh_handle_contact_form() {
	$redirect_base = wp_get_referer() ? wp_get_referer() : home_url( '/' );

	if (
		! isset( $_POST['annefpugh_contact_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['annefpugh_contact_nonce'] ) ), 'annefpugh_contact' )
	) {
		wp_safe_redirect( add_query_arg( 'annefpugh_error', 'nonce', $redirect_base ) );
		exit;
	}

	// Honeypot: real visitors never fill this hidden field in.
	if ( ! empty( $_POST['annefpugh_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'annefpugh_sent', '1', $redirect_base ) );
		exit;
	}

	$name    = isset( $_POST['annefpugh_name'] ) ? sanitize_text_field( wp_unslash( $_POST['annefpugh_name'] ) ) : '';
	$email   = isset( $_POST['annefpugh_email'] ) ? sanitize_email( wp_unslash( $_POST['annefpugh_email'] ) ) : '';
	$message = isset( $_POST['annefpugh_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['annefpugh_message'] ) ) : '';

	if ( '' === $name || '' === $message || ! is_email( $email ) ) {
		wp_safe_redirect( add_query_arg( 'annefpugh_error', 'invalid', $redirect_base ) );
		exit;
	}

	$to      = get_theme_mod( 'annefpugh_email', get_option( 'admin_email' ) );
	$subject = sprintf(
		/* translators: %s: site name. */
		__( 'New contact form message — %s', 'annefpugh' ),
		wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES )
	);
	$body = sprintf(
		"Name: %s\nEmail: %s\n\nMessage:\n%s",
		$name,
		$email,
		$message
	);
	$headers = array( 'Reply-To: ' . $name . ' <' . $email . '>' );

	$sent = wp_mail( $to, $subject, $body, $headers );

	wp_safe_redirect( add_query_arg( $sent ? 'annefpugh_sent' : 'annefpugh_error', $sent ? '1' : 'mail', $redirect_base ) );
	exit;
}
add_action( 'admin_post_annefpugh_contact', 'annefpugh_handle_contact_form' );
add_action( 'admin_post_nopriv_annefpugh_contact', 'annefpugh_handle_contact_form' );

<?php
/**
 * Native contact form: nonce, honeypot, sanitized input, wp_mail().
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function annefpugh_contact_redirect( $status, $base ) {
	wp_safe_redirect( add_query_arg( 'contact', $status, $base ) . '#contact-form' );
	exit;
}

function annefpugh_handle_contact_form() {
	$base = wp_get_referer() ? remove_query_arg( 'contact', wp_get_referer() ) : home_url( '/' );

	$nonce = isset( $_POST['annefpugh_contact_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['annefpugh_contact_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'annefpugh_contact' ) ) {
		annefpugh_contact_redirect( 'error', $base );
	}

	// Honeypot: humans never see or fill this field.
	if ( ! empty( $_POST['annefpugh_website'] ) ) {
		annefpugh_contact_redirect( 'sent', $base );
	}

	$name    = isset( $_POST['annefpugh_name'] ) ? sanitize_text_field( wp_unslash( $_POST['annefpugh_name'] ) ) : '';
	$email   = isset( $_POST['annefpugh_email'] ) ? sanitize_email( wp_unslash( $_POST['annefpugh_email'] ) ) : '';
	$phone   = isset( $_POST['annefpugh_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['annefpugh_phone'] ) ) : '';
	$message = isset( $_POST['annefpugh_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['annefpugh_message'] ) ) : '';

	if ( '' === $name || '' === $message || ! is_email( $email ) ) {
		annefpugh_contact_redirect( 'invalid', $base );
	}

	$to      = is_email( annefpugh_mod( 'email' ) ) ? annefpugh_mod( 'email' ) : get_option( 'admin_email' );
	$subject = sprintf(
		/* translators: %s: site name */
		__( 'New consultation request — %s', 'annefpugh' ),
		wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES )
	);
	$body    = sprintf( "Name: %s\nEmail: %s\nPhone: %s\n\n%s", $name, $email, $phone, $message );
	$headers = array( 'Reply-To: ' . $name . ' <' . $email . '>' );

	annefpugh_contact_redirect( wp_mail( $to, $subject, $body, $headers ) ? 'sent' : 'error', $base );
}
add_action( 'admin_post_annefpugh_contact', 'annefpugh_handle_contact_form' );
add_action( 'admin_post_nopriv_annefpugh_contact', 'annefpugh_handle_contact_form' );

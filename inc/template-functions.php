<?php
/**
 * Defaults and small helpers shared across templates.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Every Customizer setting's default, in one place.
 */
function annefpugh_defaults() {
	return array(
		// Colors.
		'color_primary'        => '#1d5fa8',
		'color_secondary'      => '#4f5e1f',
		'color_text'           => '#1b2a4a',
		'color_heading'        => '#1b2a4a',
		'color_surface'        => '#f7f4ec',
		'color_footer_bg'      => '#1b2a4a',
		'hero_overlay_color'   => '#1b2a4a',
		'hero_overlay_opacity' => 45,

		// Hero.
		'hero_image'           => 0,
		'hero_heading'         => __( 'Warm, practical support for life\'s hardest seasons', 'annefpugh' ),
		'hero_subheading'      => __( 'Individual therapy for adults — in person and via telehealth.', 'annefpugh' ),
		'hero_button_text'     => __( 'Request a free consultation', 'annefpugh' ),

		// Therapist.
		'therapist_name'        => '',
		'therapist_credentials' => '',
		'therapist_photo'       => 0,
		'therapist_intro'       => __( 'Write a short, welcoming introduction here: who you work with, how you work, and what clients can expect from a first session.', 'annefpugh' ),

		// Practice info.
		'phone'                => '',
		'email'                => '',
		'address'              => '',
		'map_url'              => '',
		'session_format'       => __( 'In person and telehealth', 'annefpugh' ),
		'hours'                => '',
		'fees'                 => '',
		'insurance'            => '',
		'license'              => '',

		// Homepage sections.
		'services_heading'     => __( 'How I can help', 'annefpugh' ),
		'services_intro'       => '',
		'services_count'       => 6,
		'cta_heading'          => __( 'Ready to take the first step?', 'annefpugh' ),
		'cta_text'             => __( 'Reach out for a free 15-minute consultation to see whether we\'re a good fit.', 'annefpugh' ),

		// Crisis & safety.
		'show_crisis_bar'      => true,

		// Page links.
		'page_about'           => 0,
		'page_services'        => 0,
		'page_contact'         => 0,
	);
}

/**
 * get_theme_mod() with the theme's own default applied.
 */
function annefpugh_mod( $key ) {
	$defaults = annefpugh_defaults();
	return get_theme_mod( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

function annefpugh_therapist_name() {
	$name = annefpugh_mod( 'therapist_name' );
	return $name ? $name : get_bloginfo( 'name' );
}

/**
 * Permalink for a page chosen under Customizer → Page Links, or '' if unset.
 */
function annefpugh_page_url( $key ) {
	$page_id = absint( annefpugh_mod( $key ) );
	if ( ! $page_id || 'publish' !== get_post_status( $page_id ) ) {
		return '';
	}
	return get_permalink( $page_id );
}

function annefpugh_tel_href( $phone ) {
	return 'tel:' . preg_replace( '/[^0-9+]/', '', $phone );
}

function annefpugh_hex_to_rgb( $hex ) {
	$hex = ltrim( (string) $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	if ( ! preg_match( '/^[0-9a-fA-F]{6}$/', $hex ) ) {
		return array( 0, 0, 0 );
	}
	return array( hexdec( substr( $hex, 0, 2 ) ), hexdec( substr( $hex, 2, 2 ) ), hexdec( substr( $hex, 4, 2 ) ) );
}

/**
 * WCAG relative luminance of a hex color.
 */
function annefpugh_luminance( $hex ) {
	$channels = array_map(
		function ( $c ) {
			$c = $c / 255;
			return $c <= 0.03928 ? $c / 12.92 : pow( ( $c + 0.055 ) / 1.055, 2.4 );
		},
		annefpugh_hex_to_rgb( $hex )
	);
	return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
}

/**
 * Pick white or near-black text for a background, whichever contrasts
 * more — keeps buttons and the footer legible whatever colors are chosen.
 */
function annefpugh_readable_text_color( $background ) {
	$bg          = annefpugh_luminance( $background );
	$vs_white    = 1.05 / ( $bg + 0.05 );
	$vs_dark     = ( $bg + 0.05 ) / ( annefpugh_luminance( '#111111' ) + 0.05 );
	return $vs_white >= $vs_dark ? '#ffffff' : '#111111';
}

/**
 * Output an attachment image, or nothing when no image is set.
 */
function annefpugh_image( $attachment_id, $size, $attr = array() ) {
	$attachment_id = absint( $attachment_id );
	if ( ! $attachment_id ) {
		return '';
	}
	return wp_get_attachment_image( $attachment_id, $size, false, $attr );
}

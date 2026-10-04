<?php
/**
 * Turn Customizer colors into CSS custom properties.
 *
 * The site owner picks brand/background colors; every text, link, and focus
 * color is then derived so it meets WCAG AA contrast against the background
 * it actually sits on (4.5:1 for text, 3:1 for focus rings).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * A color setting, falling back to its default if it was cleared.
 */
function annefpugh_color( $key ) {
	$color    = sanitize_hex_color( annefpugh_mod( $key ) );
	$defaults = annefpugh_defaults();
	return $color ? $color : $defaults[ $key ];
}

/**
 * Page background from core's Background Color setting.
 */
function annefpugh_page_background() {
	$color = sanitize_hex_color_no_hash( get_background_color() );
	return $color ? '#' . $color : '#ffffff';
}

/**
 * All derived color values, keyed by CSS custom property name.
 */
function annefpugh_color_vars() {
	$bg        = annefpugh_page_background();
	$surface   = annefpugh_color( 'color_surface' );
	$header_bg = annefpugh_color( 'color_header_bg' );
	$card_bg   = annefpugh_color( 'color_card_bg' );
	$footer_bg = annefpugh_color( 'color_footer_bg' );
	$primary   = annefpugh_color( 'color_primary' );
	$secondary = annefpugh_color( 'color_secondary' );
	$text      = annefpugh_color( 'color_text' );
	$heading   = annefpugh_color( 'color_heading' );

	$overlay_hex = annefpugh_color( 'hero_overlay_color' );
	$overlay     = annefpugh_hex_to_rgb( $overlay_hex );
	$opacity     = annefpugh_sanitize_opacity( annefpugh_mod( 'hero_overlay_opacity' ) ) / 100;

	// Links and focus rings get their own shade per area (page, surface,
	// header, card) — one color can't contrast with both a light and a dark
	// background. main.css swaps them in via variable scoping.
	return array(
		'--color-bg'              => $bg,
		'--color-text'            => annefpugh_accessible_color( $text, array( $bg ) ),
		'--color-heading'         => annefpugh_accessible_color( $heading, array( $bg ) ),
		'--color-surface'         => $surface,
		'--color-surface-text'    => annefpugh_accessible_color( $text, array( $surface ) ),
		'--color-surface-heading' => annefpugh_accessible_color( $heading, array( $surface ) ),
		'--color-header-bg'       => $header_bg,
		'--color-header-text'     => annefpugh_accessible_color( $text, array( $header_bg ) ),
		'--color-card-bg'         => $card_bg,
		'--color-card-text'       => annefpugh_accessible_color( $text, array( $card_bg ) ),
		'--color-card-heading'    => annefpugh_accessible_color( $heading, array( $card_bg ) ),
		'--color-primary'         => $primary,
		'--color-on-primary'      => annefpugh_readable_text_color( $primary ),
		'--color-link'            => annefpugh_accessible_color( $primary, array( $bg ) ),
		'--color-focus'           => annefpugh_accessible_color( $primary, array( $bg ), 3.0 ),
		'--color-surface-link'    => annefpugh_accessible_color( $primary, array( $surface ) ),
		'--color-header-link'     => annefpugh_accessible_color( $primary, array( $header_bg ) ),
		'--color-card-link'       => annefpugh_accessible_color( $primary, array( $card_bg ) ),
		'--color-secondary'       => $secondary,
		'--color-eyebrow'         => annefpugh_accessible_color( $secondary, array( $bg ) ),
		'--color-footer-bg'       => $footer_bg,
		'--color-footer-text'     => annefpugh_readable_text_color( $footer_bg ),
		'--color-on-overlay'      => annefpugh_readable_text_color( $overlay_hex ),
		'--hero-overlay'          => sprintf( 'rgba(%d, %d, %d, %.2f)', $overlay[0], $overlay[1], $overlay[2], $opacity ),
	);
}

function annefpugh_custom_properties() {
	$css = ':root{';
	foreach ( annefpugh_color_vars() as $name => $value ) {
		$css .= $name . ':' . $value . ';';
	}
	$css .= '}';

	wp_add_inline_style( 'annefpugh-main', $css );
}
add_action( 'wp_enqueue_scripts', 'annefpugh_custom_properties', 20 );

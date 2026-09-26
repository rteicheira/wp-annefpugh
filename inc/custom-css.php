<?php
/**
 * Turn Customizer colors into CSS custom properties.
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

function annefpugh_custom_properties() {
	$primary   = annefpugh_color( 'color_primary' );
	$footer_bg = annefpugh_color( 'color_footer_bg' );
	$overlay_hex = annefpugh_color( 'hero_overlay_color' );
	$overlay     = annefpugh_hex_to_rgb( $overlay_hex );
	$opacity   = annefpugh_sanitize_opacity( annefpugh_mod( 'hero_overlay_opacity' ) ) / 100;

	$vars = array(
		'--color-primary'     => $primary,
		'--color-on-primary'  => annefpugh_readable_text_color( $primary ),
		'--color-secondary'   => annefpugh_color( 'color_secondary' ),
		'--color-text'        => annefpugh_color( 'color_text' ),
		'--color-heading'     => annefpugh_color( 'color_heading' ),
		'--color-surface'     => annefpugh_color( 'color_surface' ),
		'--color-footer-bg'   => $footer_bg,
		'--color-footer-text' => annefpugh_readable_text_color( $footer_bg ),
		'--color-on-overlay'  => annefpugh_readable_text_color( $overlay_hex ),
		'--hero-overlay'      => sprintf( 'rgba(%d, %d, %d, %.2f)', $overlay[0], $overlay[1], $overlay[2], $opacity ),
	);

	$css = ':root{';
	foreach ( $vars as $name => $value ) {
		$css .= $name . ':' . $value . ';';
	}
	$css .= '}';

	wp_add_inline_style( 'annefpugh-main', $css );
}
add_action( 'wp_enqueue_scripts', 'annefpugh_custom_properties', 20 );

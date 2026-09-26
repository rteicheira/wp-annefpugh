<?php
/**
 * Local-search structured data for the practice. Skipped when a major
 * SEO plugin is active, since those output their own schema.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function annefpugh_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' );
}

function annefpugh_output_schema() {
	if ( annefpugh_seo_plugin_active() || ! is_front_page() ) {
		return;
	}

	$provider = array(
		'@type' => 'Person',
		'name'  => annefpugh_therapist_name(),
	);
	if ( annefpugh_mod( 'therapist_credentials' ) ) {
		$provider['honorificSuffix'] = annefpugh_mod( 'therapist_credentials' );
	}

	$schema = array(
		'@context' => 'https://schema.org',
		'@type'    => 'MedicalBusiness',
		'name'     => get_bloginfo( 'name' ),
		'url'      => home_url( '/' ),
		'employee' => $provider,
	);

	if ( annefpugh_mod( 'phone' ) ) {
		$schema['telephone'] = annefpugh_mod( 'phone' );
	}
	if ( annefpugh_mod( 'address' ) ) {
		$schema['address'] = array(
			'@type'         => 'PostalAddress',
			'streetAddress' => preg_replace( '/\s*\n\s*/', ', ', trim( annefpugh_mod( 'address' ) ) ),
		);
	}
	if ( annefpugh_mod( 'map_url' ) ) {
		$schema['hasMap'] = annefpugh_mod( 'map_url' );
	}
	$logo_id = get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		$schema['image'] = wp_get_attachment_image_url( $logo_id, 'full' );
	}

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES ) . "</script>\n";
}
add_action( 'wp_head', 'annefpugh_output_schema' );

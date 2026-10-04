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
		$schema['address'] = annefpugh_schema_address( annefpugh_mod( 'address' ) );
	}
	if ( annefpugh_mod( 'map_url' ) ) {
		$schema['hasMap'] = annefpugh_mod( 'map_url' );
	}
	$logo_id = get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		$schema['image'] = wp_get_attachment_image_url( $logo_id, 'full' );
	}

	// JSON_HEX_TAG escapes < and > so no value can close the <script> element.
	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_HEX_TAG | JSON_HEX_AMP ) . "</script>\n";
}

/**
 * Turn the free-text office address into a schema.org PostalAddress.
 * Recognises a last line like "Berkeley, CA 94707" (or "City, ST, 12345");
 * anything else is kept whole as the street address.
 */
function annefpugh_schema_address( $address ) {
	$lines = array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', trim( $address ) ) ) ) );
	$out   = array( '@type' => 'PostalAddress' );

	$last = end( $lines );
	if ( count( $lines ) > 1 && preg_match( '/^(.+?),\s*([A-Za-z]{2})\.?,?\s+(\d{5}(?:-\d{4})?)$/', $last, $m ) ) {
		array_pop( $lines );
		$out['streetAddress']   = implode( ', ', $lines );
		$out['addressLocality'] = $m[1];
		$out['addressRegion']   = strtoupper( $m[2] );
		$out['postalCode']      = $m[3];
		$out['addressCountry']  = 'US';
	} else {
		$out['streetAddress'] = implode( ', ', $lines );
	}
	return $out;
}
add_action( 'wp_head', 'annefpugh_output_schema' );

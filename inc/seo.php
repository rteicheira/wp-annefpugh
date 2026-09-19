<?php
/**
 * Lightweight local-SEO output: meta description, Open Graph tags,
 * canonical link, and MedicalBusiness structured data for Bay Area
 * local search. No plugin dependency.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function annefpugh_meta_description() {
	if ( is_front_page() ) {
		$description = get_theme_mod( 'annefpugh_meta_description' );
	} elseif ( is_singular() ) {
		$post        = get_queried_object();
		$description = $post && $post->post_excerpt
			? $post->post_excerpt
			: wp_strip_all_tags( wp_trim_words( get_the_content( null, false, $post ), 30 ) );
	} else {
		$description = '';
	}

	return trim( (string) $description );
}

function annefpugh_head_meta() {
	$description = annefpugh_meta_description();
	$site_name   = get_bloginfo( 'name' );
	$url         = is_front_page() ? home_url( '/' ) : get_permalink();

	if ( $description ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
	}

	printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );

	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( $site_name ) );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( wp_get_document_title() ) );
	printf( '<meta property="og:type" content="%s">' . "\n", is_front_page() ? 'website' : 'article' );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
	if ( $description ) {
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $description ) );
	}
	printf( '<meta name="twitter:card" content="summary">' . "\n" );
}
add_action( 'wp_head', 'annefpugh_head_meta' );

/**
 * MedicalBusiness structured data (schema.org) for local search —
 * output site-wide since this is a single-location practice.
 */
function annefpugh_local_business_schema() {
	$phone   = get_theme_mod( 'annefpugh_phone' );
	$email   = get_theme_mod( 'annefpugh_email' );
	$address = get_theme_mod( 'annefpugh_address' );
	$hours   = get_theme_mod( 'annefpugh_hours' );
	$pt_url  = get_theme_mod( 'annefpugh_psychology_today_url' );

	$schema = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'MedicalBusiness',
		'name'        => get_bloginfo( 'name' ),
		'url'         => home_url( '/' ),
		'description' => get_theme_mod( 'annefpugh_meta_description' ),
		'areaServed'  => array( 'Berkeley, CA', 'San Francisco, CA', 'San Francisco Bay Area' ),
	);

	if ( $phone ) {
		$schema['telephone'] = $phone;
	}
	if ( $email ) {
		$schema['email'] = $email;
	}
	if ( $address ) {
		$schema['address'] = array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => $address,
			'addressRegion'   => 'CA',
			'addressCountry'  => 'US',
		);
	}
	if ( $hours ) {
		$schema['openingHours'] = $hours;
	}
	if ( $pt_url ) {
		$schema['sameAs'] = array( $pt_url );
	}

	$schema = array_filter( $schema );

	echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
}
add_action( 'wp_head', 'annefpugh_local_business_schema' );

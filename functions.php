<?php
/**
 * Single Provider Therapy theme bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ANNEFPUGH_VERSION', '1.0.0' );

foreach ( array( 'template-functions', 'setup', 'post-types', 'customizer', 'custom-css', 'contact-form', 'schema', 'admin-practice-info' ) as $annefpugh_file ) {
	require get_template_directory() . '/inc/' . $annefpugh_file . '.php';
}

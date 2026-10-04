<?php
/**
 * Single Provider Therapy theme bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ANNEFPUGH_VERSION', '1.1.0' );

foreach ( array( 'template-functions', 'setup', 'post-types', 'customizer', 'custom-css', 'recaptcha', 'contact-form', 'schema', 'admin-practice-info', 'admin-service-order' ) as $annefpugh_file ) {
	require get_template_directory() . '/inc/' . $annefpugh_file . '.php';
}

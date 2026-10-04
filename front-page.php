<?php
/**
 * Homepage — built from template parts, no post loop.
 */

defined( 'ABSPATH' ) || exit;

get_header();

foreach ( array( 'hero', 'about', 'services', 'practice-info', 'cta' ) as $annefpugh_section ) {
	get_template_part( 'template-parts/home/' . $annefpugh_section );
}

get_footer();

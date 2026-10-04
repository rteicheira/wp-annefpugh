<?php
/**
 * Default page template — About, Fees & FAQ, and legal pages.
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/content/page' );
endwhile;

get_footer();

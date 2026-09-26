<?php
/**
 * Default page template — About, Fees & FAQ, and legal pages.
 */

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/content/page' );
endwhile;

get_footer();

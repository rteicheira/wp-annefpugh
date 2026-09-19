<?php
/**
 * Home page.
 *
 * Editable via the block editor — sections below wrap whatever content
 * is entered for the front page, plus a services teaser and CTA.
 */

get_header();
?>

<?php get_template_part( 'template-parts/hero' ); ?>

<div class="front-page-content">
	<?php
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	?>
</div>

<?php get_template_part( 'template-parts/services-teaser' ); ?>
<?php get_template_part( 'template-parts/cta' ); ?>

<?php
get_footer();

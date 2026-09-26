<?php
/**
 * Home page.
 *
 * Fully static — built entirely from template parts, no post loop.
 * This site has no blog, so the homepage never depends on the main
 * query or Reading Settings.
 */

get_header();
?>

<?php get_template_part( 'template-parts/hero-splash' ); ?>
<?php get_template_part( 'template-parts/therapist-intro' ); ?>
<?php get_template_part( 'template-parts/services' ); ?>
<?php get_template_part( 'template-parts/qualifications' ); ?>
<?php get_template_part( 'template-parts/cta' ); ?>

<?php
get_footer();

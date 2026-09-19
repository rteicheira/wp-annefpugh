<?php
/**
 * Fallback template — required by WordPress. This site has no blog,
 * so this only renders if something falls through page.php/front-page.php.
 */

get_header();
?>

<article <?php post_class( 'page-content' ); ?>>
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<h1 class="page-title"><?php the_title(); ?></h1>
		<div class="page-body">
			<?php the_content(); ?>
		</div>
		<?php
	endwhile;
	?>
</article>

<?php
get_footer();

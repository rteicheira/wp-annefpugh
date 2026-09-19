<?php
/**
 * Generic page template — used for About, Services, and the legal
 * pages (Privacy Policy, Cookie Policy, Terms of Service).
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

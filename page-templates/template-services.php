<?php
/**
 * Template Name: Services
 *
 * Page intro (from the editor), then every service with its image,
 * title, and full description in a two-column grid.
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/content/page' );
endwhile;

$annefpugh_services = annefpugh_get_services();

global $post;
?>

<?php if ( $annefpugh_services ) : ?>
	<section class="section services-list" aria-label="<?php esc_attr_e( 'Services', 'annefpugh' ); ?>">
		<div class="container services-list__grid">
			<?php
			foreach ( $annefpugh_services as $post ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride -- set up so blocks/shortcodes in the content see this service as the current post.
				setup_postdata( $post );
				?>
				<article id="<?php echo esc_attr( annefpugh_service_anchor( $post ) ); ?>" class="service-detail">
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="service-detail__media">
							<?php the_post_thumbnail( 'annefpugh-card' ); // Alt text comes from the Media Library. ?>
						</div>
					<?php endif; ?>
					<div class="service-detail__body">
						<h2><?php the_title(); ?></h2>
						<div class="entry-content">
							<?php the_content(); ?>
						</div>
					</div>
				</article>
				<?php
			endforeach;
			wp_reset_postdata();
			?>
		</div>
	</section>
<?php endif; ?>

<?php
get_template_part( 'template-parts/home/cta' );
get_footer();

<?php
/**
 * Template Name: Services
 *
 * Page intro (from the editor), then every service with its image,
 * title, and full description in a two-column grid.
 */

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/content/page' );
endwhile;

$annefpugh_services = annefpugh_get_services();
?>

<?php if ( $annefpugh_services ) : ?>
	<section class="section services-list" aria-label="<?php esc_attr_e( 'Services', 'annefpugh' ); ?>">
		<div class="container services-list__grid">
			<?php foreach ( $annefpugh_services as $annefpugh_service ) : ?>
				<article id="<?php echo esc_attr( annefpugh_service_anchor( $annefpugh_service ) ); ?>" class="service-detail">
					<?php if ( has_post_thumbnail( $annefpugh_service ) ) : ?>
						<div class="service-detail__media">
							<?php echo get_the_post_thumbnail( $annefpugh_service, 'annefpugh-card', array( 'alt' => '' ) ); ?>
						</div>
					<?php endif; ?>
					<div class="service-detail__body">
						<h2><?php echo esc_html( get_the_title( $annefpugh_service ) ); ?></h2>
						<div class="entry-content">
							<?php echo apply_filters( 'the_content', $annefpugh_service->post_content ); // phpcs:ignore WordPress.Security.EscapeOutput -- core content filter. ?>
						</div>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</section>
<?php endif; ?>

<?php
get_template_part( 'template-parts/home/cta' );
get_footer();

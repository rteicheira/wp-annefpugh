<?php
/**
 * Homepage services: responsive card grid from the Service post type.
 */

defined( 'ABSPATH' ) || exit;

$annefpugh_services     = annefpugh_get_services( annefpugh_mod( 'services_count' ) );
$annefpugh_services_url = annefpugh_page_url( 'page_services' );

if ( ! $annefpugh_services ) {
	if ( current_user_can( 'edit_posts' ) ) {
		printf(
			'<section class="section section--surface"><div class="container"><p class="admin-notice">%s <a href="%s">%s</a></p></div></section>',
			esc_html__( 'No services yet — this section is hidden from visitors until you add one.', 'annefpugh' ),
			esc_url( admin_url( 'post-new.php?post_type=annefpugh_service' ) ),
			esc_html__( 'Add a service', 'annefpugh' )
		);
	}
	return;
}
?>
<section class="section section--surface home-services" aria-labelledby="home-services-heading">
	<div class="container">
		<header class="section-header">
			<h2 id="home-services-heading"><?php echo esc_html( annefpugh_mod( 'services_heading' ) ); ?></h2>
			<?php if ( annefpugh_mod( 'services_intro' ) ) : ?>
				<p><?php echo esc_html( annefpugh_mod( 'services_intro' ) ); ?></p>
			<?php endif; ?>
		</header>

		<ul class="card-grid" role="list">
			<?php foreach ( $annefpugh_services as $annefpugh_service ) : ?>
				<li>
					<?php
					get_template_part(
						'template-parts/content/service-card',
						null,
						array(
							'service' => $annefpugh_service,
							'link'    => $annefpugh_services_url ? $annefpugh_services_url . '#' . annefpugh_service_anchor( $annefpugh_service ) : '',
						)
					);
					?>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ( $annefpugh_services_url ) : ?>
			<p class="section-footer">
				<a class="button" href="<?php echo esc_url( $annefpugh_services_url ); ?>"><?php esc_html_e( 'View all services', 'annefpugh' ); ?></a>
			</p>
		<?php endif; ?>
	</div>
</section>

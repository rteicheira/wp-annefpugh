<?php
/**
 * Homepage services teaser — links through to the Services page.
 * The specialty list is fixed here so services are clearly visible
 * on the homepage even before the Services page copy is written.
 */

$services_page = get_page_by_path( 'services' );
$specialties    = array(
	__( 'Chronic illness', 'annefpugh' ),
	__( 'Pregnancy, prenatal & postpartum', 'annefpugh' ),
	__( 'Grief', 'annefpugh' ),
	__( 'Anxiety', 'annefpugh' ),
	__( 'Burnout', 'annefpugh' ),
	__( 'Depression', 'annefpugh' ),
	__( 'Chronic pain', 'annefpugh' ),
	__( 'Life transitions', 'annefpugh' ),
);
?>
<section class="services-teaser">
	<h2><?php esc_html_e( 'Services', 'annefpugh' ); ?></h2>

	<?php if ( $services_page && $services_page->post_content ) : ?>
		<p><?php echo wp_kses_post( wp_trim_words( $services_page->post_content, 30 ) ); ?></p>
	<?php endif; ?>

	<ul class="specialty-list">
		<?php foreach ( $specialties as $specialty ) : ?>
			<li><?php echo esc_html( $specialty ); ?></li>
		<?php endforeach; ?>
	</ul>

	<?php if ( $services_page ) : ?>
		<a class="services-teaser-link" href="<?php echo esc_url( get_permalink( $services_page ) ); ?>">
			<?php esc_html_e( 'View Services', 'annefpugh' ); ?>
		</a>
	<?php endif; ?>
</section>

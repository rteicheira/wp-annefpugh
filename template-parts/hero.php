<?php
/**
 * Homepage hero section.
 */

$service_area = get_theme_mod( 'annefpugh_service_area' );
$contact_page = get_page_by_path( 'contact' );
?>
<section class="hero">
	<div class="hero-inner">
		<h1 class="hero-title"><?php bloginfo( 'name' ); ?></h1>
		<?php if ( get_bloginfo( 'description' ) ) : ?>
			<p class="hero-tagline"><?php bloginfo( 'description' ); ?></p>
		<?php endif; ?>
		<?php if ( $service_area ) : ?>
			<p class="hero-service-area"><?php echo esc_html( $service_area ); ?></p>
		<?php endif; ?>
		<?php if ( $contact_page ) : ?>
			<a class="hero-cta button" href="<?php echo esc_url( get_permalink( $contact_page ) ); ?>">
				<?php esc_html_e( 'Get in Touch', 'annefpugh' ); ?>
			</a>
		<?php endif; ?>
	</div>
</section>

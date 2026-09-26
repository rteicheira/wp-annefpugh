<?php
/**
 * Homepage splash — large top-of-page image with an overlaid content
 * card, closed off with a rolling-hills divider (California motif).
 * Falls back to a sky/hills gradient when no image has been set yet.
 */

$hero_image     = get_theme_mod( 'annefpugh_hero_image' );
$hero_image_alt = get_theme_mod( 'annefpugh_hero_image_alt' );
$service_area   = get_theme_mod( 'annefpugh_service_area' );
$contact_page   = get_page_by_path( 'contact' );
?>
<section class="hero-splash<?php echo $hero_image ? '' : ' hero-splash--placeholder'; ?>">
	<?php if ( $hero_image ) : ?>
		<img
			class="hero-splash-media"
			src="<?php echo esc_url( $hero_image ); ?>"
			alt="<?php echo esc_attr( $hero_image_alt ); ?>"
		>
	<?php endif; ?>

	<div class="hero-splash-wrap">
		<div class="hero-card">
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
	</div>

	<svg class="hills-divider" viewBox="0 0 1440 90" preserveAspectRatio="none" aria-hidden="true" focusable="false">
		<path d="M0,60 C240,10 480,90 720,50 C960,10 1200,80 1440,40 L1440,90 L0,90 Z" fill="var(--color-bg)"></path>
	</svg>
</section>

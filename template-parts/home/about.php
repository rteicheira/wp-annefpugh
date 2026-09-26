<?php
/**
 * Homepage "meet the therapist": photo + introduction, two-column grid.
 */

$annefpugh_photo     = annefpugh_mod( 'therapist_photo' );
$annefpugh_about_url = annefpugh_page_url( 'page_about' );
?>
<section class="section home-about" aria-labelledby="home-about-heading">
	<div class="container home-about__grid">
		<div class="home-about__media">
			<?php if ( $annefpugh_photo ) : ?>
				<?php echo annefpugh_image( $annefpugh_photo, 'annefpugh-portrait', array( 'class' => 'home-about__photo' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php else : ?>
				<div class="image-placeholder image-placeholder--portrait" aria-hidden="true"></div>
			<?php endif; ?>
		</div>

		<div class="home-about__text">
			<p class="eyebrow"><?php esc_html_e( 'Meet your therapist', 'annefpugh' ); ?></p>
			<h2 id="home-about-heading">
				<?php echo esc_html( annefpugh_therapist_name() ); ?>
				<?php if ( annefpugh_mod( 'therapist_credentials' ) ) : ?>
					<span class="credentials">, <?php echo esc_html( annefpugh_mod( 'therapist_credentials' ) ); ?></span>
				<?php endif; ?>
			</h2>
			<?php echo wpautop( esc_html( annefpugh_mod( 'therapist_intro' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped before wpautop. ?>
			<?php if ( $annefpugh_about_url ) : ?>
				<a class="text-link" href="<?php echo esc_url( $annefpugh_about_url ); ?>"><?php esc_html_e( 'More about my approach', 'annefpugh' ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>

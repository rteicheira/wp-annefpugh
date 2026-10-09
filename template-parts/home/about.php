<?php
/**
 * Homepage "meet the therapist": photo + introduction, two-column grid.
 */

defined( 'ABSPATH' ) || exit;

$annefpugh_photo_html  = annefpugh_image( annefpugh_mod( 'therapist_photo' ), 'annefpugh-portrait', array( 'class' => 'home-about__photo' ) );
$annefpugh_about_url   = annefpugh_page_url( 'page_about' );
$annefpugh_credentials = annefpugh_mod( 'therapist_credentials' );
?>
<section class="section home-about" aria-labelledby="home-about-heading">
	<div class="container home-about__grid">
		<div class="home-about__media">
			<?php if ( $annefpugh_photo_html ) : ?>
				<?php echo $annefpugh_photo_html; // phpcs:ignore WordPress.Security.EscapeOutput -- wp_get_attachment_image() output. ?>
			<?php else : ?>
				<div class="image-placeholder image-placeholder--portrait" aria-hidden="true"></div>
			<?php endif; ?>
		</div>

		<div class="home-about__text">
			<p class="eyebrow"><?php esc_html_e( 'Meet your therapist', 'annefpugh' ); ?></p>
			<h2 id="home-about-heading"><?php echo esc_html( annefpugh_therapist_name() ); ?><?php if ( $annefpugh_credentials ) : ?><span class="credentials">, <?php echo esc_html( $annefpugh_credentials ); ?></span><?php endif; ?></h2>
			<div class="home-about__bio" id="home-about-bio">
				<?php echo annefpugh_therapist_intro_html(); // phpcs:ignore WordPress.Security.EscapeOutput -- filtered by wp_kses() inside. ?>
			</div>
			<?php // Shown by assets/js/about-bio.js only when the bio is long enough to collapse. ?>
			<button type="button" class="home-about__more" aria-expanded="false" aria-controls="home-about-bio" hidden
				data-more="<?php esc_attr_e( 'Continue reading', 'annefpugh' ); ?>" data-less="<?php esc_attr_e( 'Show less', 'annefpugh' ); ?>">
				<?php esc_html_e( 'Continue reading', 'annefpugh' ); ?>
			</button>
			<?php wp_enqueue_script( 'annefpugh-about-bio' ); ?>
			<?php if ( $annefpugh_about_url ) : ?>
				<a class="text-link" href="<?php echo esc_url( $annefpugh_about_url ); ?>"><?php esc_html_e( 'More about my approach', 'annefpugh' ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>

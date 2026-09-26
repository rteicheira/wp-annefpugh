<?php
/**
 * Homepage "meet the therapist" section — photo + short intro,
 * linking through to the About page for the full bio.
 */

$photo      = get_theme_mod( 'annefpugh_therapist_photo' );
$photo_alt  = get_theme_mod( 'annefpugh_therapist_photo_alt' );
$about_page = get_page_by_path( 'about' );
?>
<section class="therapist-intro">
	<div class="therapist-photo">
		<?php if ( $photo ) : ?>
			<img src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( $photo_alt ); ?>">
		<?php else : ?>
			<div class="therapist-photo-placeholder" role="img" aria-label="<?php esc_attr_e( 'Therapist photo coming soon', 'annefpugh' ); ?>">
				<svg viewBox="0 0 24 24" width="56" height="56" aria-hidden="true" focusable="false">
					<circle cx="12" cy="8" r="4" fill="currentColor"></circle>
					<path d="M4 20c0-4.418 3.582-7 8-7s8 2.582 8 7" fill="currentColor"></path>
				</svg>
			</div>
		<?php endif; ?>
	</div>

	<div class="therapist-bio">
		<h2><?php esc_html_e( "Hi, I'm Anne", 'annefpugh' ); ?></h2>
		<p>
			<?php esc_html_e( 'My approach is direct, warm, and grounded in what\'s actually useful to you. We\'ll talk about what outcome you want and the pace that\'s comfortable for you — whether that\'s a structured plan or an open conversation that follows where your thoughts take you.', 'annefpugh' ); ?>
		</p>
		<?php if ( $about_page ) : ?>
			<a class="therapist-bio-link" href="<?php echo esc_url( get_permalink( $about_page ) ); ?>">
				<?php esc_html_e( 'More about my approach', 'annefpugh' ); ?>
			</a>
		<?php endif; ?>
	</div>
</section>

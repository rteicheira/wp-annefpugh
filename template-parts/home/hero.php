<?php
/**
 * Homepage hero: full-width image (or solid color) with overlay,
 * heading, subheading, and primary/phone calls to action.
 */

$annefpugh_image       = annefpugh_mod( 'hero_image' );
$annefpugh_contact_url = annefpugh_page_url( 'page_contact' );
$annefpugh_phone       = annefpugh_mod( 'phone' );
?>
<section class="hero<?php echo $annefpugh_image ? ' hero--has-image' : ''; ?>" aria-labelledby="hero-heading">
	<?php
	// Decorative: the heading carries the meaning, so alt is empty.
	echo annefpugh_image( // phpcs:ignore WordPress.Security.EscapeOutput -- wp_get_attachment_image() output.
		$annefpugh_image,
		'annefpugh-hero',
		array(
			'class'         => 'hero__image',
			'alt'           => '',
			'loading'       => 'eager',
			'fetchpriority' => 'high',
		)
	);
	?>
	<div class="container hero__content">
		<h1 id="hero-heading" class="hero__heading"><?php echo esc_html( annefpugh_mod( 'hero_heading' ) ); ?></h1>
		<?php if ( annefpugh_mod( 'hero_subheading' ) ) : ?>
			<p class="hero__subheading"><?php echo esc_html( annefpugh_mod( 'hero_subheading' ) ); ?></p>
		<?php endif; ?>
		<div class="hero__actions">
			<?php if ( $annefpugh_contact_url ) : ?>
				<a class="button button--large" href="<?php echo esc_url( $annefpugh_contact_url ); ?>"><?php echo esc_html( annefpugh_mod( 'hero_button_text' ) ); ?></a>
			<?php endif; ?>
			<?php if ( $annefpugh_phone ) : ?>
				<a class="button button--large button--ghost" href="<?php echo esc_attr( annefpugh_tel_href( $annefpugh_phone ) ); ?>">
					<?php
					/* translators: %s: phone number */
					printf( esc_html__( 'Call %s', 'annefpugh' ), esc_html( $annefpugh_phone ) );
					?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>

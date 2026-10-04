<?php
/**
 * Homepage hero: full-width image (or solid color) with overlay,
 * heading, subheading, and primary/phone calls to action.
 */

defined( 'ABSPATH' ) || exit;

// Decorative: the heading carries the meaning, so alt is empty.
$annefpugh_image_html  = annefpugh_image(
	annefpugh_mod( 'hero_image' ),
	'annefpugh-hero',
	array(
		'class'         => 'hero__image',
		'alt'           => '',
		'loading'       => 'eager',
		'fetchpriority' => 'high',
	)
);
$annefpugh_contact_url = annefpugh_page_url( 'page_contact' );
$annefpugh_phone       = annefpugh_mod( 'phone' );

// The site icon shows the practice name, so it gets real alt text here.
$annefpugh_icon_alt = get_post_meta( (int) get_option( 'site_icon' ), '_wp_attachment_image_alt', true );
/* translators: %s: site name */
$annefpugh_icon_alt = $annefpugh_icon_alt ? $annefpugh_icon_alt : sprintf( __( '%s logo', 'annefpugh' ), get_bloginfo( 'name' ) );
$annefpugh_mark     = annefpugh_site_icon( 'hero', 280, $annefpugh_icon_alt );
?>
<section class="hero<?php echo $annefpugh_image_html ? ' hero--has-image' : ''; ?>" aria-labelledby="hero-heading">
	<?php echo $annefpugh_image_html; // phpcs:ignore WordPress.Security.EscapeOutput -- wp_get_attachment_image() output. ?>
	<div class="container hero__content<?php echo $annefpugh_mark ? ' hero__content--with-mark' : ''; ?>">
		<?php if ( $annefpugh_mark ) : ?>
			<div class="hero__mark"><?php echo $annefpugh_mark; // phpcs:ignore WordPress.Security.EscapeOutput -- wp_get_attachment_image() output. ?></div>
		<?php endif; ?>
		<div class="hero__text">
		<h1 id="hero-heading" class="hero__heading"><?php echo esc_html( annefpugh_mod( 'hero_heading' ) ); ?></h1>
		<?php if ( annefpugh_mod( 'hero_subheading' ) ) : ?>
			<p class="hero__subheading"><?php echo esc_html( annefpugh_mod( 'hero_subheading' ) ); ?></p>
		<?php endif; ?>
		<div class="hero__actions">
			<?php if ( $annefpugh_contact_url ) : ?>
				<a class="button button--large" href="<?php echo esc_url( $annefpugh_contact_url ); ?>"><span class="button__label"><?php echo esc_html( annefpugh_mod( 'hero_button_text' ) ); ?></span></a>
			<?php endif; ?>
			<?php if ( $annefpugh_phone ) : ?>
				<a class="button button--large button--ghost" href="<?php echo esc_attr( annefpugh_tel_href( $annefpugh_phone ) ); ?>">
					<span class="button__label">
					<?php
					/* translators: %s: phone number */
					printf( esc_html__( 'Call %s', 'annefpugh' ), esc_html( $annefpugh_phone ) );
					?>
					</span>
				</a>
			<?php endif; ?>
		</div>
		</div>
	</div>
</section>

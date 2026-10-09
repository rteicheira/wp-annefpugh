<?php
/**
 * Closing call to action: heading, text, the call-back button, and the phone
 * number. Used on the homepage and the Services page.
 *
 * The button either links to an external site (Customizer → Homepage:
 * Sections → Call-back button link, e.g. an online booking page) or, by
 * default, reveals a short form (name, phone, what they're looking for).
 * For the form, the button and collapsed state are added by
 * assets/js/callback-toggle.js, so without JavaScript the form is simply shown.
 */

defined( 'ABSPATH' ) || exit;

$annefpugh_phone        = annefpugh_mod( 'phone' );
$annefpugh_contact_url  = annefpugh_page_url( 'page_contact' );
$annefpugh_button_text  = annefpugh_required_text( 'callback_button_text' );
$annefpugh_external_url = esc_url( annefpugh_mod( 'callback_url' ) );

if ( ! $annefpugh_external_url ) {
	wp_enqueue_script( 'annefpugh-callback-toggle' );
}

// Reopen the form when the visitor lands back here after submitting it.
$annefpugh_returning = isset( $_GET['form'] ) && 'callback-form' === sanitize_key( wp_unslash( $_GET['form'] ) ); // phpcs:ignore WordPress.Security.NonceVerification -- display-only.
?>
<section id="get-started" class="section cta-band" aria-labelledby="cta-heading">
	<div class="container cta-band__inner">
		<h2 id="cta-heading"><?php echo esc_html( annefpugh_mod( 'cta_heading' ) ); ?></h2>
		<?php if ( annefpugh_mod( 'cta_text' ) ) : ?>
			<p class="cta-band__lead"><?php echo esc_html( annefpugh_mod( 'cta_text' ) ); ?></p>
		<?php endif; ?>

		<div class="cta-band__actions">
			<?php if ( $annefpugh_external_url ) : ?>
				<a class="button button--large" href="<?php echo $annefpugh_external_url; // phpcs:ignore WordPress.Security.EscapeOutput -- esc_url() above. ?>" target="_blank" rel="noopener">
					<span class="button__label"><?php echo esc_html( $annefpugh_button_text ); ?></span>
					<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'annefpugh' ); ?></span>
				</a>
			<?php else : ?>
				<button type="button" class="button button--large cta-band__toggle" aria-expanded="false" aria-controls="callback-panel" hidden>
					<span class="button__label"><?php echo esc_html( $annefpugh_button_text ); ?></span>
				</button>
			<?php endif; ?>
			<?php if ( $annefpugh_phone ) : ?>
				<span class="cta-band__call">
					<?php esc_html_e( 'or call', 'annefpugh' ); ?>
					<a href="<?php echo esc_attr( annefpugh_tel_href( $annefpugh_phone ) ); ?>"><?php echo esc_html( $annefpugh_phone ); ?></a>
				</span>
			<?php endif; ?>
		</div>

		<?php if ( ! $annefpugh_external_url ) : ?>
			<div id="callback-panel" class="cta-band__panel" data-open="<?php echo $annefpugh_returning ? '1' : '0'; ?>">
				<?php
				get_template_part(
					'template-parts/contact-form',
					null,
					array(
						'id'      => 'callback-form',
						'variant' => 'quick',
					)
				);
				?>
			</div>
		<?php endif; ?>

		<?php if ( $annefpugh_contact_url ) : ?>
			<p class="cta-band__more"><a class="text-link" href="<?php echo esc_url( $annefpugh_contact_url ); ?>"><?php esc_html_e( 'More ways to get in touch', 'annefpugh' ); ?></a></p>
		<?php endif; ?>
	</div>
</section>

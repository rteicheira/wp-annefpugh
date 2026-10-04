<?php
/**
 * Closing call to action: heading, text, a "Request a call back" button that
 * reveals a short form (name, phone, what they're looking for), and the phone
 * number. Used on the homepage and the Services page.
 *
 * The button and collapsed state are added by assets/js/callback-toggle.js,
 * so without JavaScript the form is simply shown.
 */

defined( 'ABSPATH' ) || exit;

wp_enqueue_script( 'annefpugh-callback-toggle' );

$annefpugh_phone       = annefpugh_mod( 'phone' );
$annefpugh_contact_url = annefpugh_page_url( 'page_contact' );

// Reopen the form when the visitor lands back here after submitting it.
$annefpugh_returning = isset( $_GET['form'] ) && 'callback-form' === sanitize_key( wp_unslash( $_GET['form'] ) ); // phpcs:ignore WordPress.Security.NonceVerification -- display-only.
?>
<section class="section cta-band" aria-labelledby="cta-heading">
	<div class="container cta-band__inner">
		<h2 id="cta-heading"><?php echo esc_html( annefpugh_mod( 'cta_heading' ) ); ?></h2>
		<?php if ( annefpugh_mod( 'cta_text' ) ) : ?>
			<p class="cta-band__lead"><?php echo esc_html( annefpugh_mod( 'cta_text' ) ); ?></p>
		<?php endif; ?>

		<div class="cta-band__actions">
			<button type="button" class="button button--large cta-band__toggle" aria-expanded="false" aria-controls="callback-panel" hidden>
				<?php esc_html_e( 'Request a call back', 'annefpugh' ); ?>
			</button>
			<?php if ( $annefpugh_phone ) : ?>
				<span class="cta-band__call">
					<?php esc_html_e( 'or call', 'annefpugh' ); ?>
					<a href="<?php echo esc_attr( annefpugh_tel_href( $annefpugh_phone ) ); ?>"><?php echo esc_html( $annefpugh_phone ); ?></a>
				</span>
			<?php endif; ?>
		</div>

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

		<?php if ( $annefpugh_contact_url ) : ?>
			<p class="cta-band__more"><a class="text-link" href="<?php echo esc_url( $annefpugh_contact_url ); ?>"><?php esc_html_e( 'More ways to get in touch', 'annefpugh' ); ?></a></p>
		<?php endif; ?>
	</div>
</section>

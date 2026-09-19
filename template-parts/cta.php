<?php
/**
 * Homepage closing call-to-action — links through to the Contact page.
 */

$contact_page = get_page_by_path( 'contact' );
$phone        = get_theme_mod( 'annefpugh_phone' );

if ( ! $contact_page && ! $phone ) {
	return;
}
?>
<section class="cta">
	<p class="cta-text"><?php esc_html_e( 'Reach out to schedule a session or ask a question.', 'annefpugh' ); ?></p>
	<div class="cta-actions">
		<?php if ( $contact_page ) : ?>
			<a class="cta-link button" href="<?php echo esc_url( get_permalink( $contact_page ) ); ?>">
				<?php esc_html_e( 'Contact', 'annefpugh' ); ?>
			</a>
		<?php endif; ?>
		<?php if ( $phone ) : ?>
			<a class="cta-phone" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>">
				<?php echo esc_html( $phone ); ?>
			</a>
		<?php endif; ?>
	</div>
</section>

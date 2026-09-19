<?php
/**
 * Homepage closing call-to-action — links through to the Contact page.
 */

$contact_page = get_page_by_path( 'contact' );
if ( ! $contact_page ) {
	return;
}
?>
<section class="cta">
	<p class="cta-text"><?php esc_html_e( 'Reach out to schedule a session or ask a question.', 'annefpugh' ); ?></p>
	<a class="cta-link" href="<?php echo esc_url( get_permalink( $contact_page ) ); ?>">
		<?php esc_html_e( 'Contact', 'annefpugh' ); ?>
	</a>
</section>

<?php
/**
 * Homepage closing call to action.
 */

$annefpugh_contact_url = annefpugh_page_url( 'page_contact' );
$annefpugh_phone       = annefpugh_mod( 'phone' );

if ( ! $annefpugh_contact_url && ! $annefpugh_phone ) {
	return;
}
?>
<section class="section cta-band" aria-labelledby="cta-heading">
	<div class="container cta-band__inner">
		<div>
			<h2 id="cta-heading"><?php echo esc_html( annefpugh_mod( 'cta_heading' ) ); ?></h2>
			<?php if ( annefpugh_mod( 'cta_text' ) ) : ?>
				<p><?php echo esc_html( annefpugh_mod( 'cta_text' ) ); ?></p>
			<?php endif; ?>
		</div>
		<div class="cta-band__actions">
			<?php if ( $annefpugh_contact_url ) : ?>
				<a class="button button--large" href="<?php echo esc_url( $annefpugh_contact_url ); ?>"><?php esc_html_e( 'Contact me', 'annefpugh' ); ?></a>
			<?php endif; ?>
			<?php if ( $annefpugh_phone ) : ?>
				<a class="text-link" href="<?php echo esc_attr( annefpugh_tel_href( $annefpugh_phone ) ); ?>"><?php echo esc_html( $annefpugh_phone ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php
/**
 * Homepage services teaser — links through to the Services page.
 */

$services_page = get_page_by_path( 'services' );
if ( ! $services_page ) {
	return;
}
?>
<section class="services-teaser">
	<h2><?php esc_html_e( 'Services', 'annefpugh' ); ?></h2>
	<p>
		<?php echo wp_kses_post( wp_trim_words( $services_page->post_content, 30 ) ); ?>
	</p>
	<a class="services-teaser-link" href="<?php echo esc_url( get_permalink( $services_page ) ); ?>">
		<?php esc_html_e( 'View Services', 'annefpugh' ); ?>
	</a>
</section>

<?php
/**
 * One service card: image, title, short summary, optional link — plus, when
 * the full description says more than the summary, a "Read more" button that
 * opens the whole description in an overlay (<dialog>).
 *
 * Expects the service to be set up as the current post (setup_postdata) so
 * the_content() filters see the right post.
 *
 * @var array $args { service: WP_Post, link: string, more: bool }
 */

defined( 'ABSPATH' ) || exit;

$annefpugh_service = $args['service'];
$annefpugh_link    = isset( $args['link'] ) ? $args['link'] : '';
$annefpugh_title   = get_the_title( $annefpugh_service );
$annefpugh_full    = trim( wp_strip_all_tags( $annefpugh_service->post_content ) );

if ( has_excerpt( $annefpugh_service ) ) {
	$annefpugh_summary  = get_the_excerpt( $annefpugh_service );
	$annefpugh_has_more = '' !== $annefpugh_full && trim( $annefpugh_summary ) !== $annefpugh_full;
} else {
	$annefpugh_summary  = wp_trim_words( $annefpugh_full, 28 );
	$annefpugh_has_more = $annefpugh_summary !== $annefpugh_full;
}
$annefpugh_has_more = $annefpugh_has_more && ! empty( $args['more'] );
$annefpugh_dialog   = 'service-dialog-' . $annefpugh_service->ID;
?>
<article class="card">
	<?php if ( has_post_thumbnail( $annefpugh_service ) ) : ?>
		<div class="card__media">
			<?php echo get_the_post_thumbnail( $annefpugh_service, 'annefpugh-card', array( 'class' => 'card__image', 'alt' => '' ) ); ?>
		</div>
	<?php endif; ?>
	<div class="card__body">
		<h3 class="card__title">
			<?php if ( $annefpugh_link ) : ?>
				<a class="card__link" href="<?php echo esc_url( $annefpugh_link ); ?>"><?php echo esc_html( $annefpugh_title ); ?></a>
			<?php else : ?>
				<?php echo esc_html( $annefpugh_title ); ?>
			<?php endif; ?>
		</h3>
		<?php if ( $annefpugh_summary ) : ?>
			<p class="card__summary"><?php echo esc_html( $annefpugh_summary ); ?></p>
		<?php endif; ?>
		<?php if ( $annefpugh_has_more ) : ?>
			<?php // Hidden until assets/js/service-dialogs.js can open the overlay; without JS the title links to the Services page. ?>
			<button type="button" class="card__more" data-dialog-open="<?php echo esc_attr( $annefpugh_dialog ); ?>" aria-haspopup="dialog" hidden>
				<?php esc_html_e( 'Read more', 'annefpugh' ); ?><span class="screen-reader-text"> <?php /* translators: %s: service name */ printf( esc_html__( 'about %s', 'annefpugh' ), esc_html( $annefpugh_title ) ); ?></span>
				<span aria-hidden="true">→</span>
			</button>
		<?php endif; ?>
	</div>
</article>

<?php if ( $annefpugh_has_more ) : ?>
	<dialog id="<?php echo esc_attr( $annefpugh_dialog ); ?>" class="service-dialog" aria-labelledby="<?php echo esc_attr( $annefpugh_dialog ); ?>-title">
		<div class="service-dialog__panel">
			<button type="button" class="service-dialog__close" data-dialog-close>
				<span class="screen-reader-text"><?php esc_html_e( 'Close', 'annefpugh' ); ?></span>
				<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" fill="none"/></svg>
			</button>
			<?php if ( has_post_thumbnail( $annefpugh_service ) ) : ?>
				<div class="service-dialog__media">
					<?php echo get_the_post_thumbnail( $annefpugh_service, 'annefpugh-card', array( 'alt' => '', 'loading' => 'lazy' ) ); ?>
				</div>
			<?php endif; ?>
			<div class="service-dialog__body">
				<h2 id="<?php echo esc_attr( $annefpugh_dialog ); ?>-title"><?php echo esc_html( $annefpugh_title ); ?></h2>
				<div class="entry-content">
					<?php the_content(); ?>
				</div>
				<div class="service-dialog__actions">
					<?php
					$annefpugh_contact = annefpugh_page_url( 'page_contact' );
					$annefpugh_contact = $annefpugh_contact ? $annefpugh_contact : home_url( '/#get-started' );
					?>
					<a class="button" href="<?php echo esc_url( $annefpugh_contact ); ?>"><span class="button__label"><?php echo esc_html( annefpugh_required_text( 'header_button_text' ) ); ?></span></a>
					<?php if ( $annefpugh_link ) : ?>
						<a class="text-link" href="<?php echo esc_url( annefpugh_page_url( 'page_services' ) ); ?>"><?php esc_html_e( 'See all services', 'annefpugh' ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</dialog>
<?php endif; ?>

<?php
/**
 * Homepage services: responsive card grid from the Service post type.
 */

defined( 'ABSPATH' ) || exit;

$annefpugh_services     = annefpugh_get_services( annefpugh_mod( 'services_count' ) );
$annefpugh_services_url = annefpugh_page_url( 'page_services' );

if ( ! $annefpugh_services ) {
	if ( current_user_can( 'edit_posts' ) ) {
		printf(
			'<section class="section section--surface"><div class="container"><p class="admin-notice">%s <a href="%s">%s</a></p></div></section>',
			esc_html__( 'No services yet — this section is hidden from visitors until you add one.', 'annefpugh' ),
			esc_url( admin_url( 'post-new.php?post_type=annefpugh_service' ) ),
			esc_html__( 'Add a service', 'annefpugh' )
		);
	}
	return;
}
?>
<section class="section section--surface home-services" aria-labelledby="home-services-heading">
	<div class="container">
		<header class="section-header">
			<h2 id="home-services-heading"><?php echo esc_html( annefpugh_mod( 'services_heading' ) ); ?></h2>
			<?php if ( annefpugh_mod( 'services_intro' ) ) : ?>
				<p><?php echo esc_html( annefpugh_mod( 'services_intro' ) ); ?></p>
			<?php endif; ?>
		</header>

		<ul class="card-grid card-grid--services" role="list">
			<?php
			global $post;
			foreach ( $annefpugh_services as $post ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride -- set up so the "Read more" overlay's the_content() renders this service.
				setup_postdata( $post );
				?>
				<li>
					<?php
					get_template_part(
						'template-parts/content/service-card',
						null,
						array(
							'service' => $post,
							'link'    => $annefpugh_services_url ? $annefpugh_services_url . '#' . annefpugh_service_anchor( $post ) : '',
							'more'    => true,
						)
					);
					?>
				</li>
				<?php
			endforeach;
			wp_reset_postdata();

			/*
			 * Welcome card: fills the gap in the last row. The grid is 2 columns
			 * on tablets and 3 on desktop, so the empty slots differ per size;
			 * CSS shows the card (at the right width) only where there's a gap.
			 */
			$annefpugh_count = count( $annefpugh_services );
			$annefpugh_gap_3 = ( 3 - $annefpugh_count % 3 ) % 3; // 0, 1 or 2 empty slots on desktop.
			$annefpugh_gap_2 = $annefpugh_count % 2;             // 0 or 1 empty slot on tablets.
			if ( annefpugh_mod( 'show_welcome_card' ) && ( $annefpugh_gap_3 || $annefpugh_gap_2 ) ) :
				$annefpugh_fill_classes = array( 'card-grid__fill' );
				if ( $annefpugh_gap_3 ) {
					$annefpugh_fill_classes[] = 'card-grid__fill--desktop-' . $annefpugh_gap_3;
				}
				if ( $annefpugh_gap_2 ) {
					$annefpugh_fill_classes[] = 'card-grid__fill--tablet';
				}
				$annefpugh_welcome_url = annefpugh_page_url( 'page_contact' );
				$annefpugh_welcome_url = $annefpugh_welcome_url ? $annefpugh_welcome_url : home_url( '/#get-started' );
				?>
				<li class="<?php echo esc_attr( implode( ' ', $annefpugh_fill_classes ) ); ?>">
					<div class="welcome-card">
						<div class="welcome-card__text">
							<h3 class="welcome-card__title"><?php echo esc_html( annefpugh_required_text( 'welcome_card_heading' ) ); ?></h3>
							<p><?php echo esc_html( annefpugh_required_text( 'welcome_card_text' ) ); ?></p>
						</div>
						<a class="button welcome-card__button" href="<?php echo esc_url( $annefpugh_welcome_url ); ?>"><span class="button__label"><?php echo esc_html( annefpugh_required_text( 'header_button_text' ) ); ?></span></a>
					</div>
				</li>
			<?php endif; ?>
		</ul>
		<?php wp_enqueue_script( 'annefpugh-service-dialogs' ); ?>

		<?php if ( $annefpugh_services_url ) : ?>
			<p class="section-footer">
				<a class="button" href="<?php echo esc_url( $annefpugh_services_url ); ?>"><span class="button__label"><?php esc_html_e( 'View all services', 'annefpugh' ); ?></span></a>
			</p>
		<?php endif; ?>
	</div>
</section>

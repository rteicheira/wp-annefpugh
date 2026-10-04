<?php
/**
 * One service card: image, title, short summary, optional link.
 *
 * @var array $args { service: WP_Post, link: string }
 */

defined( 'ABSPATH' ) || exit;

$annefpugh_service = $args['service'];
$annefpugh_link    = isset( $args['link'] ) ? $args['link'] : '';
$annefpugh_summary = has_excerpt( $annefpugh_service )
	? get_the_excerpt( $annefpugh_service )
	: wp_trim_words( wp_strip_all_tags( $annefpugh_service->post_content ), 28 );
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
				<a class="card__link" href="<?php echo esc_url( $annefpugh_link ); ?>"><?php echo esc_html( get_the_title( $annefpugh_service ) ); ?></a>
			<?php else : ?>
				<?php echo esc_html( get_the_title( $annefpugh_service ) ); ?>
			<?php endif; ?>
		</h3>
		<?php if ( $annefpugh_summary ) : ?>
			<p class="card__summary"><?php echo esc_html( $annefpugh_summary ); ?></p>
		<?php endif; ?>
	</div>
</article>

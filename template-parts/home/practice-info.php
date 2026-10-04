<?php
/**
 * Homepage practice details: location, sessions & hours, fees & insurance —
 * a three-column grid. Each block only shows once it has content.
 */

defined( 'ABSPATH' ) || exit;

$annefpugh_address   = annefpugh_mod( 'address' );
$annefpugh_map_url   = annefpugh_mod( 'map_url' );
$annefpugh_format    = annefpugh_mod( 'session_format' );
$annefpugh_hours     = annefpugh_mod( 'hours' );
$annefpugh_fees      = annefpugh_mod( 'fees' );
$annefpugh_insurance = annefpugh_mod( 'insurance' );

$annefpugh_has_location = $annefpugh_address || $annefpugh_map_url;
$annefpugh_has_sessions = $annefpugh_format || $annefpugh_hours;
$annefpugh_has_fees     = $annefpugh_fees || $annefpugh_insurance;

if ( ! $annefpugh_has_location && ! $annefpugh_has_sessions && ! $annefpugh_has_fees ) {
	return;
}
?>
<section class="section practice-info" aria-labelledby="practice-info-heading">
	<div class="container">
		<header class="section-header">
			<h2 id="practice-info-heading"><?php esc_html_e( 'Practice details', 'annefpugh' ); ?></h2>
		</header>

		<div class="info-grid">
			<?php if ( $annefpugh_has_location ) : ?>
				<div class="info-block">
					<h3><?php esc_html_e( 'Location', 'annefpugh' ); ?></h3>
					<?php if ( $annefpugh_address ) : ?>
						<address><?php echo nl2br( esc_html( $annefpugh_address ) ); ?></address>
					<?php endif; ?>
					<?php if ( $annefpugh_map_url ) : ?>
						<a class="text-link" href="<?php echo esc_url( $annefpugh_map_url ); ?>" rel="noopener">
							<?php esc_html_e( 'Get directions', 'annefpugh' ); ?><span class="screen-reader-text"> <?php esc_html_e( '(opens map)', 'annefpugh' ); ?></span>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $annefpugh_has_sessions ) : ?>
				<div class="info-block">
					<h3><?php esc_html_e( 'Sessions & hours', 'annefpugh' ); ?></h3>
					<?php if ( $annefpugh_format ) : ?>
						<p><?php echo esc_html( $annefpugh_format ); ?></p>
					<?php endif; ?>
					<?php if ( $annefpugh_hours ) : ?>
						<p><?php echo nl2br( esc_html( $annefpugh_hours ) ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $annefpugh_has_fees ) : ?>
				<div class="info-block">
					<h3><?php esc_html_e( 'Fees & insurance', 'annefpugh' ); ?></h3>
					<?php if ( $annefpugh_fees ) : ?>
						<p><?php echo nl2br( esc_html( $annefpugh_fees ) ); ?></p>
					<?php endif; ?>
					<?php if ( $annefpugh_insurance ) : ?>
						<p><?php echo nl2br( esc_html( $annefpugh_insurance ) ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>

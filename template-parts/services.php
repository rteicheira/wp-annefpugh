<?php
/**
 * Homepage services section — a numbered card per primary focus area
 * (each with real explanatory copy, not just a label), plus a
 * shorter "also treats" list and a link through to the full Services
 * page. Modeled on the card-per-service pattern at lapsych.com, with
 * Anne's own specialties and tone.
 */

$services_page = get_page_by_path( 'services' );

$primary_services = array(
	array(
		'title'       => __( 'Chronic Illness', 'annefpugh' ),
		'description' => __( 'A new diagnosis, a health scare, or the daily weight of a body that won\'t cooperate can bring on a depression that\'s often invisible to people around you. We work through what you\'re actually facing — not silver linings — and build something steadier from there.', 'annefpugh' ),
	),
	array(
		'title'       => __( 'Pregnancy, Prenatal & Postpartum', 'annefpugh' ),
		'description' => __( 'Pregnancy and the postpartum period bring real emotional shifts, and struggling with them doesn\'t mean you\'re doing anything wrong. We\'ll talk through what you\'re navigating — mood, identity, the pressure to feel grateful — at whatever pace is comfortable for you.', 'annefpugh' ),
	),
	array(
		'title'       => __( 'Grief', 'annefpugh' ),
		'description' => __( 'Loss doesn\'t move in a straight line, and there\'s no timeline you\'re supposed to be following. Whether it\'s recent or long-standing, we make room for what you\'re carrying and find a way to keep going alongside it.', 'annefpugh' ),
	),
);

$also_treats = array(
	__( 'anxiety', 'annefpugh' ),
	__( 'burnout', 'annefpugh' ),
	__( 'caregiving', 'annefpugh' ),
	__( 'chronic pain', 'annefpugh' ),
	__( 'coping skills', 'annefpugh' ),
	__( 'depression', 'annefpugh' ),
	__( 'aging & older adulthood', 'annefpugh' ),
	__( 'life transitions', 'annefpugh' ),
	__( 'mood disorders', 'annefpugh' ),
	__( 'relationship issues', 'annefpugh' ),
	__( 'self-esteem', 'annefpugh' ),
	__( 'stress', 'annefpugh' ),
);
?>
<section class="services">
	<h2><?php esc_html_e( 'How I can help', 'annefpugh' ); ?></h2>

	<div class="service-card-list">
		<?php foreach ( $primary_services as $index => $service ) : ?>
			<div class="service-card">
				<span class="service-number" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
				<div class="service-body">
					<h3><?php echo esc_html( $service['title'] ); ?></h3>
					<p><?php echo esc_html( $service['description'] ); ?></p>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<p class="services-also">
		<?php
		printf(
			/* translators: %s: comma-separated list of additional focus areas. */
			esc_html__( 'Also: %s.', 'annefpugh' ),
			esc_html( implode( ', ', $also_treats ) )
		);
		?>
	</p>

	<?php if ( $services_page ) : ?>
		<a class="button services-link" href="<?php echo esc_url( get_permalink( $services_page ) ); ?>">
			<?php esc_html_e( 'View All Services', 'annefpugh' ); ?>
		</a>
	<?php endif; ?>
</section>

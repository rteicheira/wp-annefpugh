<?php
/**
 * Shown for 404s and empty results.
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="page-article">
	<header class="page-header">
		<div class="container">
			<h1 class="page-header__title"><?php esc_html_e( 'Page not found', 'annefpugh' ); ?></h1>
		</div>
	</header>
	<div class="container entry-content">
		<p><?php esc_html_e( 'Sorry, we couldn\'t find that page.', 'annefpugh' ); ?></p>
		<p><a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><span class="button__label"><?php esc_html_e( 'Back to the homepage', 'annefpugh' ); ?></span></a></p>
	</div>
</section>

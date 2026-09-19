<?php
/**
 * Site header.
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'annefpugh' ); ?></a>

<header class="site-header">
	<div class="site-branding">
		<?php if ( has_custom_logo() ) : ?>
			<?php the_custom_logo(); ?>
		<?php else : ?>
			<a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
		<?php endif; ?>
	</div>

	<button type="button" class="nav-toggle" aria-expanded="false" aria-controls="primary-nav">
		<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'annefpugh' ); ?></span>
		<span class="nav-toggle-icon" aria-hidden="true"></span>
	</button>

	<nav id="primary-nav" class="primary-nav" aria-label="<?php esc_attr_e( 'Primary', 'annefpugh' ); ?>">
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'fallback_cb'    => false,
			)
		);
		?>
	</nav>
</header>

<main id="main" class="site-main">

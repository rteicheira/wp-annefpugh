<?php
/**
 * Site header.
 */

defined( 'ABSPATH' ) || exit;

// Header button: the Contact page if one is set, otherwise the homepage's
// "Request a call back" section (which opens the form when linked to).
$annefpugh_cta_url = annefpugh_page_url( 'page_contact' );
$annefpugh_cta_url = $annefpugh_cta_url ? $annefpugh_cta_url : home_url( '/#get-started' );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to main content', 'annefpugh' ); ?></a>

<?php
if ( annefpugh_mod( 'show_crisis_bar' ) ) {
	get_template_part( 'template-parts/crisis-bar' );
}
?>

<header class="site-header">
	<div class="container site-header__inner">
		<div class="site-branding">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<?php
				$annefpugh_mark = annefpugh_site_icon( 'header', 52 );
				if ( $annefpugh_mark ) :
					// Decorative duplicate of the title link: clickable, but skipped by keyboard and screen readers.
					?>
					<a class="site-branding__mark" href="<?php echo esc_url( home_url( '/' ) ); ?>" tabindex="-1" aria-hidden="true"><?php echo $annefpugh_mark; // phpcs:ignore WordPress.Security.EscapeOutput -- wp_get_attachment_image() output. ?></a>
				<?php endif; ?>
				<div class="site-branding__text">
					<a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
					<?php if ( annefpugh_mod( 'therapist_credentials' ) ) : ?>
						<span class="site-credentials"><?php echo esc_html( annefpugh_mod( 'therapist_credentials' ) ); ?></span>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

		<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-navigation">
			<span class="nav-toggle__bars" aria-hidden="true"></span>
			<span class="nav-toggle__label"><?php esc_html_e( 'Menu', 'annefpugh' ); ?></span>
		</button>

		<nav id="site-navigation" class="site-nav" aria-label="<?php esc_attr_e( 'Main', 'annefpugh' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'site-nav__list',
					'fallback_cb'    => false,
					'depth'          => 1,
				)
			);
			?>
			<a class="button site-nav__cta" href="<?php echo esc_url( $annefpugh_cta_url ); ?>"><span class="button__label"><?php echo esc_html( annefpugh_required_text( 'header_button_text' ) ); ?></span></a>
		</nav>
	</div>
</header>

<main id="main" class="site-main" tabindex="-1">

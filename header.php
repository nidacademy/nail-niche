<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="nn-skip" href="#main"><?php esc_html_e( 'Skip to content', 'nail-niche' ); ?></a>

<header class="nn-header">
	<div class="nn-wrap nn-header__top">
		<?php $nn_brand_tag = is_front_page() && ! nn_home_has_h1() ? 'h1' : 'div'; ?>
		<<?php echo $nn_brand_tag; // phpcs:ignore ?> class="nn-brand"><?php echo nn_logo(); // phpcs:ignore ?></<?php echo $nn_brand_tag; // phpcs:ignore ?>>
		<div class="nn-header__search"><?php get_search_form(); ?></div>
		<button type="button" class="nn-menu-btn" aria-expanded="false" aria-controls="nn-nav"><?php esc_html_e( 'Menu', 'nail-niche' ); ?></button>
	</div>
	<nav class="nn-nav" id="nn-nav" aria-label="<?php esc_attr_e( 'Main', 'nail-niche' ); ?>">
		<div class="nn-wrap">
			<?php echo nn_menu( 'primary', 'nn-menu' ); // phpcs:ignore ?>
			<div class="nn-nav__search"><?php get_search_form(); ?></div>
		</div>
	</nav>
</header>

<main id="main" class="nn-main">
<?php
?>

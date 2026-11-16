<?php
/**
 * Nail Niche theme functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/inc/updater.php';

/* ---------- Setup ---------- */

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'custom-logo', array( 'height' => 60, 'width' => 280, 'flex-width' => true, 'flex-height' => true ) );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );

	register_nav_menus( array(
		'primary' => __( 'Main menu', 'nail-niche' ),
		'footer'  => __( 'Footer menu', 'nail-niche' ),
	) );

	add_image_size( 'nn-card', 800, 1000, true );
	add_image_size( 'nn-thumb', 240, 240, true );
} );

add_action( 'wp_enqueue_scripts', function () {
	$dir = get_template_directory();
	$uri = get_template_directory_uri();
	wp_enqueue_style( 'nn-main', $uri . '/assets/css/main.css', array(), filemtime( $dir . '/assets/css/main.css' ) );
	$accent = sanitize_hex_color( nn_opt( 'accent' ) );
	if ( $accent && strtolower( $accent ) !== nn_defaults()['accent'] ) {
		wp_add_inline_style( 'nn-main', ':root{--accent:' . $accent . ';}' );
	}
	wp_enqueue_script( 'nn-main', $uri . '/assets/js/main.js', array(), filemtime( $dir . '/assets/js/main.js' ), array( 'strategy' => 'defer', 'in_footer' => true ) );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
} );

add_action( 'wp_head', function () {
	echo '<link rel="preload" href="' . esc_url( get_template_directory_uri() . '/assets/fonts/libre-franklin.woff2' ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
}, 1 );

add_filter( 'excerpt_length', function () {
	return 26;
} );
add_filter( 'excerpt_more', function () {
	return '&hellip;';
} );

/* ---------- Customizer ---------- */

function nn_defaults() {
	return array(
		'about_title' => 'About me',
		'about_text'  => 'I save the nail ideas I would actually wear and sort them so you can find one fast. I also write down what to ask for at the salon.',
		'about_image' => '',
		'guides_slug' => 'nail-care',
		'guides_title'  => 'Before you book',
		'colors_slug'   => 'colors',
		'accent'        => '#c2255c',
		'home_design'        => 'classic',
		'home_welcome_title' => 'Welcome to my nail notes.',
		'home_welcome_text'  => 'Here I save the nail ideas I would actually wear: colors, shapes and looks for every season, with what to ask for at the salon.',
	);
}

function nn_opt( $key ) {
	$d = nn_defaults();
	return get_theme_mod( 'nn_' . $key, isset( $d[ $key ] ) ? $d[ $key ] : '' );
}

add_action( 'customize_register', function ( $wp_customize ) {
	$d = nn_defaults();
	$wp_customize->add_section( 'nn_about', array( 'title' => __( 'About box', 'nail-niche' ), 'priority' => 30, 'description' => __( 'The short "about the writer" box in the sidebar and under posts.', 'nail-niche' ) ) );

	$wp_customize->add_setting( 'nn_about_image', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'nn_about_image', array( 'label' => __( 'Photo', 'nail-niche' ), 'section' => 'nn_about' ) ) );

	$wp_customize->add_setting( 'nn_about_title', array( 'default' => $d['about_title'], 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'nn_about_title', array( 'label' => __( 'Heading', 'nail-niche' ), 'section' => 'nn_about', 'type' => 'text' ) );

	$wp_customize->add_setting( 'nn_about_text', array( 'default' => $d['about_text'], 'sanitize_callback' => 'sanitize_textarea_field' ) );
	$wp_customize->add_control( 'nn_about_text', array( 'label' => __( 'Text', 'nail-niche' ), 'section' => 'nn_about', 'type' => 'textarea' ) );

	// Theme options: categories the theme uses, and the accent colour.
	$wp_customize->add_section( 'nn_options', array( 'title' => __( 'Theme options', 'nail-niche' ), 'priority' => 26 ) );
	$slugs = array( '' => __( '— None —', 'nail-niche' ) );
	foreach ( get_categories( array( 'hide_empty' => false ) ) as $c ) {
		$slugs[ $c->slug ] = $c->name;
	}
	$wp_customize->add_setting( 'nn_guides_slug', array( 'default' => $d['guides_slug'], 'sanitize_callback' => 'sanitize_title' ) );
	$wp_customize->add_control( 'nn_guides_slug', array( 'label' => __( 'Guides category', 'nail-niche' ), 'description' => __( 'Its posts get their own list in the sidebar and stay out of the nail idea feeds.', 'nail-niche' ), 'section' => 'nn_options', 'type' => 'select', 'choices' => $slugs ) );
	$wp_customize->add_setting( 'nn_guides_title', array( 'default' => $d['guides_title'], 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'nn_guides_title', array( 'label' => __( 'Guides list heading', 'nail-niche' ), 'section' => 'nn_options', 'type' => 'text' ) );
	$wp_customize->add_setting( 'nn_colors_slug', array( 'default' => $d['colors_slug'], 'sanitize_callback' => 'sanitize_title' ) );
	$wp_customize->add_control( 'nn_colors_slug', array( 'label' => __( 'Colors category', 'nail-niche' ), 'description' => __( 'Its sub-categories become the swatches of the Color shop home design.', 'nail-niche' ), 'section' => 'nn_options', 'type' => 'select', 'choices' => $slugs ) );
	$wp_customize->add_setting( 'nn_accent', array( 'default' => $d['accent'], 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'nn_accent', array( 'label' => __( 'Accent colour', 'nail-niche' ), 'description' => __( 'Links, category names and highlights.', 'nail-niche' ), 'section' => 'nn_options' ) ) );

	// Home design: four ready-made home page layouts.
	$wp_customize->add_section( 'nn_home', array(
		'title'       => __( 'Home design', 'nail-niche' ),
		'priority'    => 25,
		'description' => __( 'Pick how the home page looks. The preview on the right changes when you pick one.', 'nail-niche' ),
	) );
	$wp_customize->add_setting( 'nn_home_design', array( 'default' => $d['home_design'], 'sanitize_callback' => 'nn_sanitize_home_design' ) );
	$wp_customize->add_control( 'nn_home_design', array(
		'label'   => __( 'Design', 'nail-niche' ),
		'section' => 'nn_home',
		'type'    => 'radio',
		'choices' => nn_home_designs(),
	) );
	$personal = function () {
		return 'personal' === nn_opt( 'home_design' );
	};
	$wp_customize->add_setting( 'nn_home_welcome_title', array( 'default' => $d['home_welcome_title'], 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'nn_home_welcome_title', array( 'label' => __( 'Welcome heading', 'nail-niche' ), 'section' => 'nn_home', 'type' => 'text', 'active_callback' => $personal ) );
	$wp_customize->add_setting( 'nn_home_welcome_text', array( 'default' => $d['home_welcome_text'], 'sanitize_callback' => 'sanitize_textarea_field' ) );
	$wp_customize->add_control( 'nn_home_welcome_text', array( 'label' => __( 'Welcome text', 'nail-niche' ), 'description' => __( 'Shown next to the About box photo.', 'nail-niche' ), 'section' => 'nn_home', 'type' => 'textarea', 'active_callback' => $personal ) );

	// Seasonal: which category the banner shows (automatic = picked by today's date).
	$cats = array( 0 => __( 'Automatic (by today\'s date)', 'nail-niche' ) );
	foreach ( get_categories( array( 'hide_empty' => false ) ) as $c ) {
		$cats[ $c->term_id ] = $c->name;
	}
	$wp_customize->add_setting( 'nn_home_season_cat', array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( 'nn_home_season_cat', array(
		'label'           => __( 'Season banner shows', 'nail-niche' ),
		'description'     => __( 'Automatic picks Christmas in December, Valentine\'s in February, and so on, when you have that category.', 'nail-niche' ),
		'section'         => 'nn_home',
		'type'            => 'select',
		'choices'         => $cats,
		'active_callback' => function () {
			return 'seasonal' === nn_opt( 'home_design' );
		},
	) );
} );

/** The home page designs (Customize → Home design). */
function nn_home_designs() {
	return array(
		'classic'  => __( 'Classic: lead post, latest posts and category rows, with the sidebar', 'nail-niche' ),
		'magazine' => __( 'Magazine: big featured posts, full width, no sidebar', 'nail-niche' ),
		'board'    => __( 'Pinterest board: a wall of tall photos with category buttons', 'nail-niche' ),
		'personal' => __( 'Personal blog: a welcome from you, then a simple list of posts', 'nail-niche' ),
		'colors'   => __( 'Color shop: color swatches, then a row of posts for each color', 'nail-niche' ),
		'seasonal' => __( 'Seasonal: a banner for the season or holiday, then more ideas', 'nail-niche' ),
		'journal'  => __( 'Minimal list: a short intro and post titles by month', 'nail-niche' ),
	);
}

function nn_sanitize_home_design( $value ) {
	return array_key_exists( $value, nn_home_designs() ) ? $value : 'classic';
}

/** Post ids for the home page feeds (guides left out). */
function nn_home_posts( $number, $offset = 0 ) {
	$guides = nn_guides_cat();
	return get_posts( array(
		'posts_per_page'      => $number,
		'offset'              => $offset,
		'ignore_sticky_posts' => true,
		'fields'              => 'ids',
		'no_found_rows'       => true,
		'category__not_in'    => $guides ? array( $guides->term_id ) : array(),
	) );
}

/** A category by slug, or null. */
function nn_cat( $slug ) {
	$t = get_category_by_slug( $slug );
	return $t ? $t : null;
}

/** Child categories (with posts) of a top-level category. */
function nn_children( $parent_slug ) {
	$parent = nn_cat( $parent_slug );
	return $parent ? get_categories( array( 'parent' => $parent->term_id, 'orderby' => 'name' ) ) : array();
}

/** Swatch color for a color category, from words in its slug. */
function nn_color_hex( $slug ) {
	$map = array(
		'burgundy' => '#6d1a2b', 'wine' => '#6d1a2b', 'cherry' => '#9e1b32', 'red' => '#b3202f',
		'nude' => '#d9b8a0', 'beige' => '#d9c2a7', 'milky' => '#f3eee6', 'white' => '#f7f5f0',
		'baby-blue' => '#a9cbe8', 'navy' => '#24345c', 'blue' => '#3f6fb5', 'chocolate' => '#5a3a2a', 'brown' => '#7a5136',
		'pink' => '#e48aa6', 'rose' => '#d98c9a', 'black' => '#1c1c1c', 'green' => '#5f8f6a', 'sage' => '#9fb59a', 'mint' => '#a8dcc5',
		'lavender' => '#b7a3d6', 'purple' => '#7d4f9e', 'lilac' => '#c8b2dd', 'gold' => '#c9a24a', 'silver' => '#c0c4c8', 'chrome' => '#c0c4c8',
		'coral' => '#f07a5a', 'orange' => '#e8803a', 'yellow' => '#f2d15a', 'grey' => '#9a9a9a', 'gray' => '#9a9a9a',
	);
	foreach ( $map as $word => $hex ) {
		if ( false !== strpos( $slug, $word ) ) {
			return $hex;
		}
	}
	return '#dcdcdc';
}

/** The season or holiday for today: label and category slugs to look for. */
function nn_season() {
	$m = (int) wp_date( 'n' );
	$d = (int) wp_date( 'j' );
	if ( 12 === $m || ( 11 === $m && $d >= 20 ) ) {
		return array( __( 'Christmas nails', 'nail-niche' ), array( 'christmas', 'holiday', 'winter' ) );
	}
	if ( 2 === $m || ( 1 === $m && $d >= 20 ) ) {
		return array( __( "Valentine's nails", 'nail-niche' ), array( 'valentines', 'valentine', 'valentines-day' ) );
	}
	if ( $m >= 3 && $m <= 5 ) {
		return array( __( 'Spring nails', 'nail-niche' ), array( 'spring', 'easter', 'wedding' ) );
	}
	if ( $m >= 6 && $m <= 8 ) {
		return array( __( 'Summer nails', 'nail-niche' ), array( 'summer', 'vacation', 'beach' ) );
	}
	if ( 10 === $m ) {
		return array( __( 'Halloween and fall nails', 'nail-niche' ), array( 'halloween', 'fall', 'autumn' ) );
	}
	if ( $m >= 9 ) {
		return array( __( 'Fall nails', 'nail-niche' ), array( 'fall', 'autumn', 'thanksgiving', 'christmas' ) );
	}
	return array( __( 'Winter nails', 'nail-niche' ), array( 'winter', 'new-year', 'christmas' ) );
}

/** The category the Seasonal banner shows, with its heading: [ term|null, label ]. */
function nn_season_cat() {
	$picked = (int) get_theme_mod( 'nn_home_season_cat', 0 );
	if ( $picked && ( $t = get_category( $picked ) ) && ! is_wp_error( $t ) ) {
		/* translators: %s: category name */
		return array( $t, sprintf( __( '%s nails', 'nail-niche' ), $t->name ) );
	}
	$season = nn_season();
	foreach ( $season[1] as $i => $slug ) {
		$t = nn_cat( $slug );
		if ( $t && $t->count ) {
			/* translators: %s: category name */
			return array( $t, 0 === $i ? $season[0] : sprintf( __( '%s nails', 'nail-niche' ), $t->name ) );
		}
	}
	return array( null, __( 'New nail ideas', 'nail-niche' ) );
}

/** Home designs that print their own main heading (then the logo is not the h1). */
function nn_home_has_h1() {
	$design = nn_sanitize_home_design( nn_opt( 'home_design' ) );
	if ( 'colors' === $design ) {
		return (bool) nn_children( nn_opt( 'colors_slug' ) ); // Without color categories it shows Classic.
	}
	return in_array( $design, array( 'personal', 'journal' ), true );
}

/* ---------- Helpers ---------- */

function nn_guides_cat() {
	$term = get_category_by_slug( nn_opt( 'guides_slug' ) );
	return $term ? $term : null;
}

function nn_ideas_url() {
	$page = (int) get_option( 'page_for_posts' );
	return $page ? get_permalink( $page ) : home_url( '/' );
}

/** Top-level categories that hold nail ideas (not guides, not the default). */
function nn_groups( $number = 4 ) {
	$guides = nn_guides_cat();
	return get_categories( array(
		'parent'  => 0,
		'orderby' => 'count',
		'order'   => 'DESC',
		'exclude' => array_filter( array( $guides ? $guides->term_id : 0, (int) get_option( 'default_category' ) ) ),
		'number'  => $number,
	) );
}

/** Top-level category a post belongs to. */
function nn_primary_cat( $post_id ) {
	$cats = get_the_category( $post_id );
	if ( empty( $cats ) ) {
		return null;
	}
	$cat = $cats[0];
	foreach ( $cats as $c ) {
		if ( 0 === (int) $c->parent ) {
			$cat = $c;
			break;
		}
	}
	while ( $cat->parent ) {
		$parent = get_category( $cat->parent );
		if ( ! $parent || is_wp_error( $parent ) ) {
			break;
		}
		$cat = $parent;
	}
	return $cat;
}

function nn_cat_link( $post_id ) {
	$cat = nn_primary_cat( $post_id );
	if ( ! $cat ) {
		return '';
	}
	return '<a class="nn-cat" href="' . esc_url( get_category_link( $cat ) ) . '">' . esc_html( $cat->name ) . '</a>';
}

function nn_thumb( $post_id, $size = 'nn-card', $eager = false ) {
	if ( has_post_thumbnail( $post_id ) ) {
		$attr = $eager ? array( 'loading' => 'eager', 'fetchpriority' => 'high' ) : array( 'loading' => 'lazy' );
		return get_the_post_thumbnail( $post_id, $size, $attr );
	}
	return '';
}

/**
 * Post card. Variants: tile (photo on top), row (small photo left).
 */
function nn_card( $post_id, $variant = 'tile' ) {
	$url  = get_permalink( $post_id );
	$out  = '<article class="nn-card nn-card--' . esc_attr( $variant ) . '">';
	$out .= '<a class="nn-card__img" href="' . esc_url( $url ) . '" tabindex="-1" aria-hidden="true">' . nn_thumb( $post_id, 'row' === $variant ? 'nn-thumb' : 'nn-card' ) . '</a>';
	$out .= '<div class="nn-card__body">' . nn_cat_link( $post_id );
	$out .= '<h3 class="nn-card__title"><a href="' . esc_url( $url ) . '">' . esc_html( get_the_title( $post_id ) ) . '</a></h3>';
	$out .= '<time class="nn-date" datetime="' . esc_attr( get_the_date( 'c', $post_id ) ) . '">' . esc_html( get_the_date( '', $post_id ) ) . '</time>';
	$out .= '</div></article>';
	return $out;
}

function nn_heading( $title, $link = '', $link_text = '' ) {
	$out = '<div class="nn-heading"><h2>' . esc_html( $title ) . '</h2>';
	if ( $link ) {
		$out .= '<a href="' . esc_url( $link ) . '">' . esc_html( $link_text ) . '</a>';
	}
	return $out . '</div>';
}

function nn_logo() {
	if ( has_custom_logo() ) {
		return get_custom_logo();
	}
	return '<a class="nn-logo" href="' . esc_url( home_url( '/' ) ) . '" rel="home">' . esc_html( get_bloginfo( 'name' ) ) . '</a>';
}

function nn_menu( $location, $class ) {
	if ( has_nav_menu( $location ) ) {
		return wp_nav_menu( array(
			'theme_location' => $location,
			'container'      => false,
			'menu_class'     => $class,
			'echo'           => false,
			'depth'          => 'footer' === $location ? 1 : 2,
		) );
	}
	$out  = '<ul class="' . esc_attr( $class ) . '">';
	$out .= wp_list_categories( array( 'title_li' => '', 'depth' => 1, 'number' => 6, 'orderby' => 'count', 'order' => 'DESC', 'echo' => false ) );
	$out .= '</ul>';
	return $out;
}

function nn_archive_title() {
	if ( is_search() ) {
		/* translators: %s: search query */
		return sprintf( __( 'Search results for "%s"', 'nail-niche' ), get_search_query() );
	}
	if ( is_home() ) {
		$page = (int) get_option( 'page_for_posts' );
		return $page ? get_the_title( $page ) : __( 'Nail ideas', 'nail-niche' );
	}
	if ( is_category() || is_tag() || is_tax() ) {
		return single_term_title( '', false );
	}
	return wp_strip_all_tags( get_the_archive_title() );
}

function nn_breadcrumb() {
	if ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
		ob_start();
		rank_math_the_breadcrumbs();
		$crumbs = ob_get_clean();
		if ( $crumbs ) {
			return '<div class="nn-crumbs">' . $crumbs . '</div>';
		}
	}
	$items = array( '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'nail-niche' ) . '</a>' );
	if ( is_singular( 'post' ) ) {
		$cat = nn_primary_cat( get_the_ID() );
		if ( $cat ) {
			$items[] = '<a href="' . esc_url( get_category_link( $cat ) ) . '">' . esc_html( $cat->name ) . '</a>';
		}
	} elseif ( is_category() ) {
		$term = get_queried_object();
		if ( $term->parent ) {
			$parent = get_category( $term->parent );
			if ( $parent && ! is_wp_error( $parent ) ) {
				$items[] = '<a href="' . esc_url( get_category_link( $parent ) ) . '">' . esc_html( $parent->name ) . '</a>';
			}
		}
		$items[] = '<span>' . esc_html( $term->name ) . '</span>';
	} else {
		return '';
	}
	return '<nav class="nn-crumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'nail-niche' ) . '">' . implode( ' <span aria-hidden="true">&rsaquo;</span> ', $items ) . '</nav>';
}

/** Short author box shown under every article. */
function nn_author_box() {
	$about = get_page_by_path( 'about' );
	$photo = nn_opt( 'about_image' );
	$out   = '<aside class="nn-author">';
	$out  .= $photo ? '<img class="nn-about__photo" src="' . esc_url( $photo ) . '" alt="" width="72" height="72" loading="lazy">' : '';
	$out  .= '<div><p class="nn-author__name">' . sprintf( /* translators: %s: author name */ esc_html__( 'Written by %s', 'nail-niche' ), esc_html( get_the_author() ) ) . '</p>';
	$out  .= '<p>' . esc_html( nn_opt( 'about_text' ) ) . '</p>';
	if ( $about ) {
		$out .= '<a href="' . esc_url( get_permalink( $about ) ) . '">' . esc_html__( 'More about me', 'nail-niche' ) . '</a>';
	}
	return $out . '</div></aside>';
}

/* ---------- Sidebar widgets ---------- */

/** Widget areas: add any block (Custom HTML, Image, Latest Posts…) in Appearance → Widgets. */
function nn_widget_areas() {
	return array(
		'nn-side-home' => array( __( 'Home page sidebar', 'nail-niche' ), __( 'Shown in the home page sidebar, under the About box and the built-in lists.', 'nail-niche' ) ),
		'nn-side'      => array( __( 'Post and archive sidebar', 'nail-niche' ), __( 'Shown in the sidebar of posts, categories, search and other lists, under the built-in sections.', 'nail-niche' ) ),
	);
}

add_action( 'widgets_init', function () {
	foreach ( nn_widget_areas() as $id => $area ) {
		register_sidebar( array(
			'id'            => $id,
			'name'          => $area[0],
			'description'   => $area[1],
			'before_widget' => '<section id="%1$s" class="nn-side__block nn-widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2>',
			'after_title'   => '</h2>',
		) );
	}
} );

/** The sidebar widget area for the current page. */
function nn_widget_area() {
	return is_front_page() ? 'nn-side-home' : 'nn-side';
}

/* ---------- Affiliate links ---------- */

function nn_affiliate_hosts() {
	return apply_filters( 'nn_affiliate_hosts', array( 'amzn.to', 'amazon.', 'shareasale.com', 'awin1.com', 'rstyle.me', 'shopstyle.it', 'skimresources.com', 'linksynergy.com', 'howl.me', 'ltk.app', 'liketk.it', 'sovrn.co', 'pntra.com', 'anrdoezrs.net', 'jdoqocy.com', 'tkqlhce.com', 'dpbolvw.net', 'kqzyfj.com', 'sjv.io', 'pxf.io' ) );
}

/**
 * Marks affiliate links the way Google asks (rel="sponsored nofollow") and opens them in a new tab.
 * A link counts as affiliate when it points to a known network or has the class "affiliate".
 * Returns array( html, found ).
 */
function nn_affiliate_links( $content ) {
	if ( ! class_exists( 'WP_HTML_Tag_Processor' ) || false === stripos( $content, '<a ' ) ) {
		return array( $content, false );
	}
	$own   = wp_parse_url( home_url(), PHP_URL_HOST );
	$found = false;
	$tags  = new WP_HTML_Tag_Processor( $content );
	while ( $tags->next_tag( 'a' ) ) {
		$host = wp_parse_url( (string) $tags->get_attribute( 'href' ), PHP_URL_HOST );
		if ( ! $host || $host === $own ) {
			continue;
		}
		$rel   = (string) $tags->get_attribute( 'rel' );
		$class = (string) $tags->get_attribute( 'class' );
		$is    = false !== strpos( $rel, 'sponsored' ) || preg_match( '/\baffiliate\b/', $class );
		if ( ! $is ) {
			foreach ( nn_affiliate_hosts() as $needle ) {
				if ( false !== stripos( $host, $needle ) ) {
					$is = true;
					break;
				}
			}
		}
		if ( $is ) {
			$found = true;
			$tags->set_attribute( 'rel', implode( ' ', array_unique( array_filter( array_merge( explode( ' ', $rel ), array( 'sponsored', 'nofollow', 'noopener' ) ) ) ) ) );
			$tags->set_attribute( 'target', '_blank' );
		}
	}
	return array( $tags->get_updated_html(), $found );
}

add_filter( 'the_content', function ( $content ) {
	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}
	list( $content, $found ) = nn_affiliate_links( $content );
	if ( $found ) {
		$page   = get_page_by_path( 'disclosure' );
		$more   = $page ? ' <a href="' . esc_url( get_permalink( $page ) ) . '">' . esc_html__( 'How this works', 'nail-niche' ) . '</a>' : '';
		$notice = '<p class="nn-notice">' . esc_html__( 'This post contains affiliate links. If you buy through one, I may earn a small commission at no extra cost to you.', 'nail-niche' ) . $more . '</p>';
		$content = $notice . $content;
	}
	return $content;
}, 20 );

/* ---------- Search engines ---------- */

/**
 * A staging or local copy of the site: WordPress environment type, or a host name
 * that hosts use for temporary addresses. Filter "nn_is_staging" to change it.
 */
function nn_is_staging() {
	$host = isset( $_SERVER['HTTP_HOST'] ) ? strtolower( sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) ) : '';
	$is   = in_array( wp_get_environment_type(), array( 'staging', 'development', 'local' ), true );
	foreach ( array( 'hostingersite.com', 'staging.', '.staging', 'stage.', '.local', 'localhost', '.test', 'instawp', 'wpstaging', '.wpengine.com', '.kinsta.cloud', '.cloudwaysapps.com' ) as $needle ) {
		$is = $is || false !== strpos( $host, $needle );
	}
	return (bool) apply_filters( 'nn_is_staging', $is, $host );
}

// Keep staging and local copies out of search results. The real domain is not affected.
add_filter( 'wp_robots', function ( $robots ) {
	return nn_is_staging() ? array( 'noindex' => true, 'nofollow' => true ) : $robots;
}, 99 );

// Basic description and share tags, only until an SEO plugin (Rank Math, Yoast) takes over.
add_action( 'wp_head', function () {
	if ( defined( 'RANK_MATH_VERSION' ) || defined( 'WPSEO_VERSION' ) || defined( 'AIOSEO_VERSION' ) ) {
		return;
	}
	$desc  = get_bloginfo( 'description' );
	$title = wp_get_document_title();
	$url   = home_url( '/' );
	$image = '';
	$type  = 'website';
	if ( is_singular() ) {
		$id    = get_queried_object_id();
		$desc  = has_excerpt( $id ) ? get_the_excerpt( $id ) : wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', $id ) ), 28, '' );
		$url   = get_permalink( $id );
		$image = get_the_post_thumbnail_url( $id, 'large' );
		$type  = is_singular( 'post' ) ? 'article' : 'website';
	} elseif ( is_category() || is_tag() ) {
		$term = get_queried_object();
		$desc = $term->description ? wp_strip_all_tags( $term->description ) : $desc;
		$url  = get_term_link( $term );
	}
	$desc = trim( preg_replace( '/\s+/', ' ', (string) $desc ) );
	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
		echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta property="og:type" content="' . esc_attr( $type ) . '">' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
	if ( ! is_wp_error( $url ) ) {
		echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	}
	if ( $image ) {
		echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	}
}, 5 );

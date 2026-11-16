<?php
/**
 * Demo content: Appearance → Demo content.
 *
 * One click makes a fresh site look like the theme preview: categories, posts with
 * photos, pages, menus, sidebar widgets and Customizer settings. The pack (demo.json
 * plus photos) is downloaded from the theme's GitHub release "demo-1", so the theme
 * itself stays small. Everything created is recorded, so "Remove demo content" deletes
 * only those items and puts the earlier settings back.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Where the demo pack comes from. Filter "nn_demo_pack_url" to host it elsewhere. */
function nn_demo_pack_url() {
	return (string) apply_filters( 'nn_demo_pack_url', 'https://github.com/' . nn_github_repo() . '/releases/download/demo-1/nail-niche-demo.zip' );
}

/** What was imported (empty array = nothing). */
function nn_demo_record() {
	$r = get_option( 'nn_demo_import', array() );
	return is_array( $r ) ? $r : array();
}

function nn_demo_save_record( array $r ) {
	update_option( 'nn_demo_import', $r, false );
}

/** The working folder for the unpacked pack. */
function nn_demo_dir() {
	$up = wp_upload_dir( null, false );
	return trailingslashit( $up['basedir'] ) . 'nn-demo-pack';
}

/* ---------- Admin page ---------- */

add_action( 'admin_menu', function () {
	add_theme_page( __( 'Demo content', 'nail-niche' ), __( 'Demo content', 'nail-niche' ), 'edit_theme_options', 'nn-demo', 'nn_demo_page' );
} );

function nn_demo_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$rec = nn_demo_record();
	$has = ! empty( $rec['posts'] ) || ! empty( $rec['pages'] );
	?>
	<div class="wrap nn-demo">
		<h1><?php esc_html_e( 'Demo content', 'nail-niche' ); ?></h1>
		<p class="nn-demo__lead"><?php esc_html_e( 'Make your site look like the theme preview in one click, then replace the demo posts with your own.', 'nail-niche' ); ?></p>

		<div class="nn-demo__card">
			<h2><?php esc_html_e( 'What gets added', 'nail-niche' ); ?></h2>
			<ul class="nn-demo__list">
				<li><?php esc_html_e( 'About 20 nail idea posts and salon guides, each with photos', 'nail-niche' ); ?></li>
				<li><?php esc_html_e( 'Categories: Colors, Shapes, Styles, Occasions and Nail Care, with sub-categories', 'nail-niche' ); ?></li>
				<li><?php esc_html_e( 'Pages: Home, Nail Ideas, About, Contact, Privacy Policy and Disclosure', 'nail-niche' ); ?></li>
				<li><?php esc_html_e( 'Main menu and footer menu', 'nail-niche' ); ?></li>
				<li><?php esc_html_e( 'A sidebar block and the theme settings (About box, guides, colors)', 'nail-niche' ); ?></li>
			</ul>
			<p class="description"><?php esc_html_e( 'Your existing posts, pages and media are not changed. The photos (about 5 MB) are downloaded from the theme\'s GitHub page. The demo photos are AI-generated and free to keep.', 'nail-niche' ); ?></p>

			<?php if ( $has ) : ?>
				<p class="nn-demo__done"><span class="dashicons dashicons-yes-alt" aria-hidden="true"></span>
					<?php
					/* translators: 1: number of posts, 2: date */
					echo esc_html( sprintf( __( 'Demo content is installed (%1$d posts, imported %2$s).', 'nail-niche' ), count( (array) ( $rec['posts'] ?? array() ) ), ! empty( $rec['at'] ) ? wp_date( get_option( 'date_format' ), (int) $rec['at'] ) : '' ) );
					?>
				</p>
			<?php endif; ?>

			<p class="nn-demo__actions">
				<button type="button" class="button button-primary button-hero" id="nn-demo-import" <?php disabled( $has ); ?>><?php esc_html_e( 'Import demo content', 'nail-niche' ); ?></button>
				<?php if ( $has ) : ?>
					<button type="button" class="button button-link-delete" id="nn-demo-remove"><?php esc_html_e( 'Remove demo content', 'nail-niche' ); ?></button>
				<?php endif; ?>
			</p>
			<div class="nn-demo__progress" hidden>
				<div class="nn-demo__bar"><span></span></div>
				<p class="nn-demo__status" aria-live="polite"></p>
			</div>
			<?php if ( $has ) : ?>
				<p class="description"><?php esc_html_e( 'Remove deletes only what the import added (posts, their photos, pages, menus, empty categories) and puts your earlier homepage, menus, widgets and theme settings back.', 'nail-niche' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
	<style>
		.nn-demo__lead { font-size: 15px; max-width: 640px; }
		.nn-demo__card { max-width: 680px; margin-top: 18px; padding: 22px 26px; background: #fff; border: 1px solid #dcdcde; border-radius: 6px; }
		.nn-demo__card h2 { margin-top: 0; }
		.nn-demo__list { margin: 0 0 14px 18px; list-style: disc; }
		.nn-demo__done { color: #1e7a3c; font-weight: 600; }
		.nn-demo__actions { display: flex; align-items: center; gap: 16px; margin-top: 18px; }
		.nn-demo__bar { height: 10px; margin-top: 16px; overflow: hidden; border-radius: 6px; background: #f0f0f1; }
		.nn-demo__bar span { display: block; width: 0; height: 100%; background: #c2255c; transition: width .3s; }
		.nn-demo__status { margin: 8px 0 0; }
	</style>
	<script>
	( function () {
		var ajax = <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>;
		var nonce = <?php echo wp_json_encode( wp_create_nonce( 'nn_demo' ) ); ?>;
		var box = document.querySelector( '.nn-demo__progress' );
		var bar = document.querySelector( '.nn-demo__bar span' );
		var status = document.querySelector( '.nn-demo__status' );
		function call( step ) {
			var body = new URLSearchParams( { action: 'nn_demo', step: step, _ajax_nonce: nonce } );
			return fetch( ajax, { method: 'POST', credentials: 'same-origin', body: body } ).then( function ( r ) { return r.json(); } );
		}
		function run( step, buttons ) {
			box.hidden = false;
			buttons.forEach( function ( b ) { if ( b ) { b.disabled = true; } } );
			( function next( s ) {
				call( s ).then( function ( res ) {
					if ( ! res || ! res.success ) {
						status.textContent = ( res && res.data && res.data.message ) || 'Something went wrong. Please try again.';
						buttons.forEach( function ( b ) { if ( b ) { b.disabled = false; } } );
						return;
					}
					bar.style.width = ( res.data.percent || 0 ) + '%';
					status.textContent = res.data.message || '';
					if ( res.data.next ) {
						next( res.data.next );
					} else {
						setTimeout( function () { window.location.reload(); }, 1200 );
					}
				} ).catch( function () {
					status.textContent = 'The server stopped answering. Please reload the page and try again: finished steps are kept.';
					buttons.forEach( function ( b ) { if ( b ) { b.disabled = false; } } );
				} );
			} )( step );
		}
		var imp = document.getElementById( 'nn-demo-import' );
		var rem = document.getElementById( 'nn-demo-remove' );
		if ( imp ) {
			imp.addEventListener( 'click', function () { run( 'download', [ imp, rem ] ); } );
		}
		if ( rem ) {
			rem.addEventListener( 'click', function () {
				if ( window.confirm( <?php echo wp_json_encode( __( 'Remove the demo posts, pages, photos and menus, and put your earlier settings back?', 'nail-niche' ) ); ?> ) ) {
					run( 'remove', [ imp, rem ] );
				}
			} );
		}
	} )();
	</script>
	<?php
}

// After switching to the theme: point to the demo on a site that has almost no posts.
add_action( 'admin_notices', function () {
	global $pagenow;
	if ( 'themes.php' !== $pagenow || isset( $_GET['page'] ) || ! current_user_can( 'edit_theme_options' ) || nn_demo_record() || get_option( 'nn_demo_notice_off' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	if ( (int) wp_count_posts( 'post' )->publish > 3 ) {
		return;
	}
	echo '<div class="notice notice-info"><p><strong>' . esc_html__( 'Nail Niche is active.', 'nail-niche' ) . '</strong> ' . esc_html__( 'Want your site to look like the preview?', 'nail-niche' ) . ' <a class="button button-primary" style="margin-left:8px" href="' . esc_url( admin_url( 'themes.php?page=nn-demo' ) ) . '">' . esc_html__( 'Import demo content', 'nail-niche' ) . '</a></p></div>';
} );

/* ---------- Steps (AJAX) ---------- */

add_action( 'wp_ajax_nn_demo', function () {
	check_ajax_referer( 'nn_demo' );
	if ( ! current_user_can( 'edit_theme_options' ) || ! current_user_can( 'upload_files' ) || ! current_user_can( 'publish_posts' ) ) {
		wp_send_json_error( array( 'message' => __( 'You are not allowed to do this.', 'nail-niche' ) ) );
	}
	$step = isset( $_POST['step'] ) ? sanitize_key( wp_unslash( $_POST['step'] ) ) : '';
	@set_time_limit( 120 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	switch ( $step ) {
		case 'download':
			$r = nn_demo_step_download();
			break;
		case 'terms':
			$r = nn_demo_step_terms();
			break;
		case 'posts':
			$r = nn_demo_step_posts();
			break;
		case 'site':
			$r = nn_demo_step_site();
			break;
		case 'remove':
			$r = nn_demo_step_remove();
			break;
		default:
			$r = new WP_Error( 'nn_demo', __( 'Unknown step.', 'nail-niche' ) );
	}
	if ( is_wp_error( $r ) ) {
		wp_send_json_error( array( 'message' => $r->get_error_message() ) );
	}
	wp_send_json_success( $r );
} );

/** The unpacked demo.json. */
function nn_demo_data() {
	$file = nn_demo_dir() . '/demo.json';
	if ( ! file_exists( $file ) ) {
		return new WP_Error( 'nn_demo', __( 'The demo pack is missing. Please start again.', 'nail-niche' ) );
	}
	$data = json_decode( (string) file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	return is_array( $data ) ? $data : new WP_Error( 'nn_demo', __( 'The demo pack could not be read.', 'nail-niche' ) );
}

function nn_demo_step_download() {
	if ( nn_demo_record() && ! empty( nn_demo_record()['done'] ) ) {
		return new WP_Error( 'nn_demo', __( 'Demo content is already installed.', 'nail-niche' ) );
	}
	$tmp = download_url( nn_demo_pack_url(), 120 );
	if ( is_wp_error( $tmp ) ) {
		/* translators: %s: error */
		return new WP_Error( 'nn_demo', sprintf( __( 'Could not download the demo pack: %s', 'nail-niche' ), $tmp->get_error_message() ) );
	}
	global $wp_filesystem;
	WP_Filesystem();
	$dir = nn_demo_dir();
	if ( $wp_filesystem && $wp_filesystem->exists( $dir ) ) {
		$wp_filesystem->delete( $dir, true );
	}
	$ok = unzip_file( $tmp, $dir );
	@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.unlink_unlink
	if ( is_wp_error( $ok ) ) {
		return $ok;
	}
	$data = nn_demo_data();
	if ( is_wp_error( $data ) ) {
		return $data;
	}
	// An import that stopped half-way carries on (nothing is added twice).
	if ( nn_demo_record() ) {
		return array( 'next' => 'terms', 'percent' => 8, 'message' => __( 'Demo pack downloaded. Carrying on where the last import stopped…', 'nail-niche' ) );
	}
	// Start the record, with the settings to put back on Remove.
	$mods = array();
	foreach ( (array) ( $data['theme_mods'] ?? array() ) as $k => $v ) {
		$mods[ $k ] = get_theme_mod( $k, '__nn_unset__' );
	}
	nn_demo_save_record(
		array(
			'at'    => time(),
			'done'  => false,
			'terms' => array(),
			'posts' => array(),
			'pages' => array(),
			'media' => array(),
			'menus' => array(),
			'map'   => array(),
			'prev'  => array(
				'show_on_front'  => get_option( 'show_on_front' ),
				'page_on_front'  => (int) get_option( 'page_on_front' ),
				'page_for_posts' => (int) get_option( 'page_for_posts' ),
				'locations'      => get_theme_mod( 'nav_menu_locations', array() ),
				'sidebars'       => get_option( 'sidebars_widgets' ),
				'widget_block'   => get_option( 'widget_block' ),
				'mods'           => $mods,
			),
		)
	);
	return array(
		'next'    => 'terms',
		'percent' => 8,
		'message' => __( 'Demo pack downloaded. Adding categories…', 'nail-niche' ),
	);
}

function nn_demo_step_terms() {
	$data = nn_demo_data();
	if ( is_wp_error( $data ) ) {
		return $data;
	}
	$rec = nn_demo_record();
	foreach ( (array) ( $data['categories'] ?? array() ) as $c ) {
		$parent = 0;
		if ( ! empty( $c['parent'] ) ) {
			$p      = get_category_by_slug( $c['parent'] );
			$parent = $p ? (int) $p->term_id : 0;
		}
		$have = get_category_by_slug( $c['slug'] );
		if ( $have ) {
			continue; // Keep the site's own category.
		}
		$t = wp_insert_term( $c['name'], 'category', array( 'slug' => $c['slug'], 'parent' => $parent, 'description' => (string) ( $c['description'] ?? '' ) ) );
		if ( ! is_wp_error( $t ) ) {
			$rec['terms'][] = (int) $t['term_id'];
		}
	}
	nn_demo_save_record( $rec );
	return array(
		'next'    => 'posts',
		'percent' => 14,
		'message' => __( 'Categories added. Adding posts and photos…', 'nail-niche' ),
	);
}

/** Adds a photo from the pack once (the same file used twice is added once). */
function nn_demo_media( $file, $parent, $alt, array &$rec ) {
	if ( isset( $rec['map'][ $file ] ) && get_post( (int) $rec['map'][ $file ] ) ) {
		return (int) $rec['map'][ $file ];
	}
	$src = nn_demo_dir() . '/images/' . basename( $file );
	if ( ! file_exists( $src ) ) {
		return 0;
	}
	$tmp = wp_tempnam( $file );
	copy( $src, $tmp );
	$id = media_handle_sideload( array( 'name' => basename( $file ), 'tmp_name' => $tmp ), $parent );
	if ( is_wp_error( $id ) ) {
		@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.unlink_unlink
		return 0;
	}
	update_post_meta( $id, '_wp_attachment_image_alt', sanitize_text_field( $alt ) );
	update_post_meta( $id, '_nn_demo_pack', 1 );
	$rec['map'][ $file ] = (int) $id;
	$rec['media'][]      = (int) $id;
	return (int) $id;
}

/** Fills the photo placeholders of a post body: {{src:file}}, {{id:file}}. */
function nn_demo_fill( $content, array $images, $parent, array &$rec ) {
	foreach ( $images as $file => $alt ) {
		$id = nn_demo_media( $file, $parent, $alt, $rec );
		$content = str_replace( array( '{{src:' . $file . '}}', '{{id:' . $file . '}}' ), array( $id ? (string) wp_get_attachment_image_url( $id, 'large' ) : '', (string) $id ), $content );
	}
	return $content;
}

function nn_demo_step_posts() {
	$data = nn_demo_data();
	if ( is_wp_error( $data ) ) {
		return $data;
	}
	$rec   = nn_demo_record();
	$posts = (array) ( $data['posts'] ?? array() );
	$total = count( $posts );
	$i     = count( $rec['posts'] );
	if ( $i >= $total ) {
		return array( 'next' => 'site', 'percent' => 85, 'message' => __( 'Posts added. Setting up pages and menus…', 'nail-niche' ) );
	}
	$p   = $posts[ $i ];
	$cat = array();
	foreach ( (array) ( $p['categories'] ?? array() ) as $slug ) {
		$t = get_category_by_slug( $slug );
		if ( $t ) {
			$cat[] = (int) $t->term_id;
		}
	}
	// Newest first, a couple of days apart, ending today.
	$date = gmdate( 'Y-m-d H:i:s', time() - ( $total - 1 - $i ) * 2 * DAY_IN_SECONDS - $i * 37 * MINUTE_IN_SECONDS );
	$id   = wp_insert_post(
		array(
			'post_type'     => 'post',
			'post_status'   => 'publish',
			'post_title'    => wp_slash( (string) $p['title'] ),
			'post_name'     => sanitize_title( (string) ( $p['slug'] ?? $p['title'] ) ),
			'post_excerpt'  => wp_slash( (string) ( $p['excerpt'] ?? '' ) ),
			'post_content'  => '',
			'post_category' => $cat,
			'post_date_gmt' => $date,
			'post_date'     => get_date_from_gmt( $date ),
		),
		true
	);
	if ( is_wp_error( $id ) ) {
		return $id;
	}
	update_post_meta( $id, '_nn_demo_pack', 1 );
	$rec['posts'][] = (int) $id;
	$images         = (array) ( $p['images'] ?? array() );
	$content        = nn_demo_fill( (string) $p['content'], $images, $id, $rec );
	wp_update_post( array( 'ID' => $id, 'post_content' => wp_slash( $content ) ) );
	if ( ! empty( $p['featured'] ) ) {
		$f = nn_demo_media( (string) $p['featured'], $id, (string) ( $images[ $p['featured'] ] ?? $p['title'] ), $rec );
		if ( $f ) {
			set_post_thumbnail( $id, $f );
		}
	}
	nn_demo_save_record( $rec );
	return array(
		'next'    => 'posts',
		'percent' => 14 + (int) round( ( $i + 1 ) / max( 1, $total ) * 70 ),
		/* translators: 1: number done, 2: total */
		'message' => sprintf( __( 'Added post %1$d of %2$d…', 'nail-niche' ), $i + 1, $total ),
	);
}

function nn_demo_step_site() {
	$data = nn_demo_data();
	if ( is_wp_error( $data ) ) {
		return $data;
	}
	$rec   = nn_demo_record();
	$pages = array();
	foreach ( (array) ( $data['pages'] ?? array() ) as $p ) {
		$id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => wp_slash( (string) $p['title'] ),
				'post_name'    => sanitize_title( (string) $p['slug'] ),
				'post_content' => '',
			),
			true
		);
		if ( is_wp_error( $id ) ) {
			continue;
		}
		update_post_meta( $id, '_nn_demo_pack', 1 );
		$content = nn_demo_fill( (string) ( $p['content'] ?? '' ), (array) ( $p['images'] ?? array() ), $id, $rec );
		wp_update_post( array( 'ID' => $id, 'post_content' => wp_slash( $content ) ) );
		$rec['pages'][]       = (int) $id;
		$pages[ $p['slug'] ]  = (int) $id;
	}
	// Reading: a static home page and a posts page.
	if ( ! empty( $pages['home'] ) && ! empty( $pages['nail-ideas'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $pages['home'] );
		update_option( 'page_for_posts', $pages['nail-ideas'] );
	}
	// Menus.
	$locations = (array) get_theme_mod( 'nav_menu_locations', array() );
	foreach ( (array) ( $data['menus'] ?? array() ) as $location => $m ) {
		$menu_id = wp_create_nav_menu( (string) $m['name'] . ( wp_get_nav_menu_object( $m['name'] ) ? ' (demo)' : '' ) );
		if ( is_wp_error( $menu_id ) ) {
			continue;
		}
		$rec['menus'][] = (int) $menu_id;
		foreach ( (array) $m['items'] as $item ) {
			$args = array( 'menu-item-title' => (string) $item['title'], 'menu-item-status' => 'publish' );
			if ( 'category' === $item['type'] ) {
				$t = get_category_by_slug( $item['slug'] );
				if ( ! $t ) {
					continue;
				}
				$args += array( 'menu-item-type' => 'taxonomy', 'menu-item-object' => 'category', 'menu-item-object-id' => (int) $t->term_id );
			} elseif ( 'page' === $item['type'] ) {
				$pid = $pages[ $item['slug'] ] ?? 0;
				if ( ! $pid ) {
					continue;
				}
				$args += array( 'menu-item-type' => 'post_type', 'menu-item-object' => 'page', 'menu-item-object-id' => $pid );
			} else {
				$args += array( 'menu-item-type' => 'custom', 'menu-item-url' => esc_url_raw( (string) $item['url'] ) );
			}
			wp_update_nav_menu_item( $menu_id, 0, $args );
		}
		$locations[ $location ] = (int) $menu_id;
	}
	set_theme_mod( 'nav_menu_locations', $locations );
	// Sidebar blocks.
	$blocks   = get_option( 'widget_block', array() );
	$blocks   = is_array( $blocks ) ? $blocks : array( '_multiwidget' => 1 );
	$sidebars = get_option( 'sidebars_widgets', array() );
	foreach ( (array) ( $data['widgets'] ?? array() ) as $area => $list ) {
		foreach ( (array) $list as $html ) {
			$n            = max( array_merge( array( 1 ), array_filter( array_keys( $blocks ), 'is_int' ) ) ) + 1;
			$blocks[ $n ] = array( 'content' => (string) $html );
			$sidebars[ $area ][] = 'block-' . $n;
		}
	}
	update_option( 'widget_block', $blocks );
	update_option( 'sidebars_widgets', $sidebars );
	// Theme settings (an image setting can name a pack photo: "{{media:file}}").
	foreach ( (array) ( $data['theme_mods'] ?? array() ) as $k => $v ) {
		if ( is_string( $v ) && preg_match( '/^\{\{media:(.+)\}\}$/', $v, $m ) ) {
			$mid = nn_demo_media( $m[1], 0, '', $rec );
			$v   = $mid ? (string) wp_get_attachment_image_url( $mid, 'medium' ) : '';
		}
		set_theme_mod( $k, $v );
	}
	$rec['done'] = true;
	nn_demo_save_record( $rec );
	// Tidy up.
	global $wp_filesystem;
	WP_Filesystem();
	if ( $wp_filesystem ) {
		$wp_filesystem->delete( nn_demo_dir(), true );
	}
	return array(
		'next'    => '',
		'percent' => 100,
		'message' => __( 'Done! Your site now looks like the preview. Reloading…', 'nail-niche' ),
	);
}

function nn_demo_step_remove() {
	$rec = nn_demo_record();
	if ( ! $rec ) {
		return array( 'next' => '', 'percent' => 100, 'message' => __( 'There is no demo content to remove.', 'nail-niche' ) );
	}
	// Posts and pages (and only photos the import added).
	foreach ( array_merge( (array) $rec['posts'], (array) $rec['pages'] ) as $id ) {
		if ( get_post_meta( (int) $id, '_nn_demo_pack', true ) ) {
			wp_delete_post( (int) $id, true );
		}
	}
	foreach ( (array) $rec['media'] as $id ) {
		if ( get_post_meta( (int) $id, '_nn_demo_pack', true ) ) {
			wp_delete_attachment( (int) $id, true );
		}
	}
	foreach ( (array) $rec['menus'] as $id ) {
		wp_delete_nav_menu( (int) $id );
	}
	// Categories the import made, when nothing else uses them (children first).
	foreach ( array_reverse( (array) $rec['terms'] ) as $tid ) {
		$t = get_term( (int) $tid, 'category' );
		if ( $t && ! is_wp_error( $t ) && 0 === (int) $t->count && ! get_term_children( (int) $tid, 'category' ) ) {
			wp_delete_term( (int) $tid, 'category' );
		}
	}
	// Earlier settings back.
	$prev = (array) ( $rec['prev'] ?? array() );
	if ( isset( $prev['show_on_front'] ) ) {
		update_option( 'show_on_front', $prev['show_on_front'] );
		update_option( 'page_on_front', (int) $prev['page_on_front'] );
		update_option( 'page_for_posts', (int) $prev['page_for_posts'] );
	}
	set_theme_mod( 'nav_menu_locations', (array) ( $prev['locations'] ?? array() ) );
	if ( isset( $prev['sidebars'] ) ) {
		update_option( 'sidebars_widgets', $prev['sidebars'] );
		update_option( 'widget_block', $prev['widget_block'] );
	}
	foreach ( (array) ( $prev['mods'] ?? array() ) as $k => $v ) {
		if ( '__nn_unset__' === $v ) {
			remove_theme_mod( $k );
		} else {
			set_theme_mod( $k, $v );
		}
	}
	delete_option( 'nn_demo_import' );
	return array( 'next' => '', 'percent' => 100, 'message' => __( 'Demo content removed. Reloading…', 'nail-niche' ) );
}

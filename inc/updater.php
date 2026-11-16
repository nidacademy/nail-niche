<?php
/**
 * Updates from GitHub: when a newer release of this theme is published
 * (github.com/NIDACADEMY/nail-niche, a tag like v3.6.0 with a nail-niche.zip
 * attached), it shows under Dashboard → Updates like any other theme update.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** The GitHub repository ("owner/name"). Filter "nn_github_repo" to point a copy elsewhere. */
function nn_github_repo() {
	return (string) apply_filters( 'nn_github_repo', 'NIDACADEMY/nail-niche' );
}

/**
 * The latest release: version, zip and page, or null. Asked at most every 12 hours.
 *
 * @return array{version:string,package:string,url:string}|null
 */
function nn_latest_release() {
	$cached = get_site_transient( 'nn_github_release' );
	if ( false !== $cached ) {
		return is_array( $cached ) ? $cached : null;
	}
	$out = null;
	$res = wp_remote_get(
		'https://api.github.com/repos/' . nn_github_repo() . '/releases/latest',
		array(
			'timeout' => 10,
			'headers' => array(
				'Accept'     => 'application/vnd.github+json',
				'User-Agent' => 'WordPress theme updater',
			),
		)
	);
	if ( ! is_wp_error( $res ) && 200 === wp_remote_retrieve_response_code( $res ) ) {
		$json    = json_decode( wp_remote_retrieve_body( $res ), true );
		$version = ltrim( (string) ( $json['tag_name'] ?? '' ), 'vV' );
		$package = '';
		foreach ( (array) ( $json['assets'] ?? array() ) as $asset ) {
			if ( preg_match( '/\.zip$/i', (string) ( $asset['name'] ?? '' ) ) ) {
				$package = (string) $asset['browser_download_url'];
				break;
			}
		}
		if ( preg_match( '/^\d+(\.\d+)*$/', $version ) && 0 === strpos( $package, 'https://github.com/' ) ) {
			$out = array(
				'version' => $version,
				'package' => $package,
				'url'     => (string) ( $json['html_url'] ?? 'https://github.com/' . nn_github_repo() ),
			);
		}
	}
	set_site_transient( 'nn_github_release', $out ? $out : 0, $out ? 12 * HOUR_IN_SECONDS : HOUR_IN_SECONDS );
	return $out;
}

add_filter( 'pre_set_site_transient_update_themes', function ( $transient ) {
	if ( ! is_object( $transient ) ) {
		return $transient;
	}
	$slug    = get_template();
	$current = (string) wp_get_theme( $slug )->get( 'Version' );
	$latest  = nn_latest_release();
	if ( ! $latest || ! $current ) {
		return $transient;
	}
	$item = array(
		'theme'        => $slug,
		'new_version'  => $latest['version'],
		'url'          => $latest['url'],
		'package'      => $latest['package'],
		'requires'     => '6.4',
		'requires_php' => '7.4',
	);
	if ( version_compare( $latest['version'], $current, '>' ) ) {
		$transient->response[ $slug ] = $item;
	} else {
		$item['new_version']           = $current;
		$transient->no_update[ $slug ] = $item; // Lets the "Enable auto-updates" link show.
	}
	return $transient;
} );

// After an update, ask GitHub again next time.
add_action( 'upgrader_process_complete', function () {
	delete_site_transient( 'nn_github_release' );
} );

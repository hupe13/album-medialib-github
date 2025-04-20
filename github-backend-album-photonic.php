<?php
/**
 * Backend Menus
 *
 * @package Extensions for Leaflet Map Github Version
 **/

// Direktzugriff auf diese Datei verhindern.
defined( 'ABSPATH' ) || die();

// for translating a plugin
function leafext_extensions_textdomain() {
	if ( get_locale() === 'de_DE' ) {
		load_plugin_textdomain( 'leafext-update-github', false, PHOTONIC_ALBUM_NAME . '/github/lang/' );
	}
}
add_action( 'plugins_loaded', 'leafext_extensions_textdomain' );

// prevent unnecessary API calls to wordpress.org
function leafext_prevent_requests( $res, $action, $args ) {
	if ( 'plugin_information' !== $action ) {
		return $res;
	}
	if ( $args->slug !== PHOTONIC_ALBUM_NAME ) {
		return $res;
	}
	$plugin_data = get_plugin_data( __FILE__, true, false );
	$res         = new stdClass();
	$res->name   = $plugin_data['Name'];
	return $res;
}
add_filter( 'plugins_api', 'leafext_prevent_requests', 10, 3 );

if ( ! function_exists( 'leafext_get_repos' ) ) {
	require_once PHOTONIC_ALBUM_DIR . 'github/github-functions.php';
}
if ( is_main_site() && ! function_exists( 'leafext_update_puc_error' ) ) {
	require_once PHOTONIC_ALBUM_DIR . 'github/github-settings.php';
	require_once PHOTONIC_ALBUM_DIR . 'github/github-check-update.php';
}

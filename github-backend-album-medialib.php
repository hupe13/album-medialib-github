<?php
/**
 * Backend Menus
 *
 * @package album-medialib
 **/

// Direktzugriff auf diese Datei verhindern.
defined( 'ABSPATH' ) || die();

// for translating a plugin
function album_medialib_textdomain() {
	if ( get_locale() === 'de_DE' ) {
		load_plugin_textdomain( 'album-media-library', false, ALBUM_MEDIALIB_NAME . '/lang/' );
	}
}
add_action( 'plugins_loaded', 'album_medialib_textdomain' );

if ( ! function_exists( 'leafext_get_repos' ) ) {
	require_once ALBUM_MEDIALIB_DIR . 'github/github-functions.php';
}
if ( is_main_site() && ! function_exists( 'leafext_update_puc_error' ) ) {
	require_once ALBUM_MEDIALIB_DIR . 'github/github-settings.php';
	require_once ALBUM_MEDIALIB_DIR . 'github/github-check-update.php';
}

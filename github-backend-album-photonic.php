<?php
/**
 * Backend Menus
 *
 * @package Extensions for Leaflet Map Github Version
 **/

// Direktzugriff auf diese Datei verhindern.
defined( 'ABSPATH' ) || die();

if ( ! function_exists( 'leafext_get_repos' ) ) {
	require_once PHOTONIC_ALBUM_DIR . 'github/github-functions.php';
}
if ( is_main_site() && ! function_exists( 'leafext_update_puc_error' ) ) {
	require_once PHOTONIC_ALBUM_DIR . 'github/github-settings.php';
	require_once PHOTONIC_ALBUM_DIR . 'github/github-check-update.php';
}

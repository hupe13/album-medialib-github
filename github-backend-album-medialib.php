<?php
/**
 * Backend Menus
 *
 * @package Extensions for Leaflet Map Github Version
 **/

// Direktzugriff auf diese Datei verhindern.
defined( 'ABSPATH' ) || die();

// Add settings to plugin page
function album_medialib_add_action_links( $actions ) {
	$actions[] = '<a href="' . esc_url( admin_url( 'admin.php' ) . '?page=github-settings' ) . '">' . esc_html__( 'Settings', 'album-medialib' ) . '</a>';
	return $actions;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'album_medialib_add_action_links' );

if ( ! function_exists( 'leafext_get_repos' ) ) {
	require_once ALBUM_MEDIALIB_DIR . 'github/github-functions.php';
}
if ( is_main_site() && ! function_exists( 'leafext_update_puc_error' ) ) {
	require_once ALBUM_MEDIALIB_DIR . 'github/github-settings.php';
	require_once ALBUM_MEDIALIB_DIR . 'github/github-check-update.php';
}

<?php
/**
 * Plugin Name:       Display photos selected by path from the Media Library
 * Description:       Organize your photos in folders, select a path and display these photos with any gallery shortcode.
 * Update URI:        https://github.com/hupe13/album-medialib-github
 * Version:           250424
 * Requires PHP:      8.1
 * Author:            hupe13
 * Author URI:        https://leafext.de/en/
 * License:           GPL v2 or later
 *
 * @package album-medialib
 **/

// Direktzugriff auf diese Datei verhindern.
defined( 'ABSPATH' ) || die();

function get_album_medialib_shortcode() {
	$default   = array(
		'alternative_shortcode' => 'gallery',
	);
	$options   = shortcode_atts( $default, get_option( 'album_medialib_options' ) );
	$shortcode = $options['alternative_shortcode'] !== '' ? $options['alternative_shortcode'] : 'gallery';
	return $shortcode;
}

function album_medialib_params() {
	$params = array(
		array(
			'param'   => 'shortcode',
			'desc'    => __( 'Your Shortcode', 'album-medialib' ),
			'default' => 'gallery',
		),
		array(
			'param'   => 'ids',
			'desc'    => __( 'Name of the list option', 'album-medialib' ),
			'default' => 'ids',
		),
	);
	return $params;
}

function album_medialib_settings() {
	$defaults = array();
	$params   = album_medialib_params();
	foreach ( $params as $param ) {
		$defaults[ $param['param'] ] = $param['default'];
	}
	$setting = get_option( 'album_medialib', $defaults );
	return $setting;
}

add_filter(
	'pre_do_shortcode_tag',
	function ( $output, $shortcode, $attr ) {
		if ( is_singular() || is_archive() || is_home() || is_front_page() ) {
			$setting = album_medialib_settings();
			if ( $setting['shortcode'] === $shortcode ) {
				if ( $attr['path'] && $attr['path'] !== '' ) {
					global $wpdb;
					$image = '%' . $attr['path'] . '%';
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
					$results = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_value LIKE %s", $image ) );
					if ( count( $results ) > 0 ) {
						$ids = '';
						foreach ( $results as $result ) {
							$ids .= $result->post_id . ',';
						}
						$new_shortcode = '[' . $shortcode . ' ' . $setting['ids'] . '="' . $ids . '" ';
						foreach ( $attr as $key => $item ) {
							if ( $key !== 'path' ) {
								$new_shortcode .= "$key='$item' ";
							}
						}
						$new_shortcode .= ']';
						return do_shortcode( $new_shortcode );
					}
				}
			}
		}
		return $output;
	},
	10,
	3
);

if ( is_admin() ) {

	define( 'ALBUM_MEDIALIB_FILE', __FILE__ ); // /pfad/wp-content/plugins/album_medialib-update-github/album_medialib-update-github.php .
	define( 'ALBUM_MEDIALIB_DIR', plugin_dir_path( __FILE__ ) ); // /pfad/wp-content/plugins/album_medialib-update-github/ .
	define( 'ALBUM_MEDIALIB_URL', WP_PLUGIN_URL . '/' . basename( ALBUM_MEDIALIB_DIR ) ); // https://url/wp-content/plugins/album_medialib-update-github/ .
	define( 'ALBUM_MEDIALIB_NAME', basename( ALBUM_MEDIALIB_DIR ) ); // album_medialib-update-github

	function album_medialib_action_links( $actions ) {
		$actions[] = '<a href="' . esc_url( admin_url( 'admin.php' ) . '?page=album-medialib' ) . '">' . esc_html__( 'Settings', 'album-medialib' ) . '</a>';
		return $actions;
	}
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'album_medialib_action_links' );

	include_once ALBUM_MEDIALIB_DIR . 'admin.php';
	require_once ALBUM_MEDIALIB_DIR . 'github-backend-album-medialib.php';
}

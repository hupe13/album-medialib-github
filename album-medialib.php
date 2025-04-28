<?php
/**
 * Plugin Name:       Display photos located below a specific folder from the media library
 * Description:       Organize your photos in folders, select a path and display these photos with any gallery shortcode.
 * Update URI:        https://github.com/hupe13/album-medialib-github
 * Version:           250428
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
			/* translators: %s is an option. */
			'desc'    => sprintf( __( 'Name of the %s option', 'album-medialib' ), 'list' ),
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
				if ( array_key_exists( 'path', $attr ) ) {
					if ( $attr['path'] !== '' ) {
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
		}
		return $output;
	},
	10,
	3
);

if ( is_admin() ) {

	define( 'ALBUM_MEDIALIB_FILE', __FILE__ ); // /pfad/wp-content/plugins/album-medialib-github/album-medialib.php .
	define( 'ALBUM_MEDIALIB_DIR', plugin_dir_path( __FILE__ ) ); // /pfad/wp-content/plugins/album-medialib-github/ .
	// define( 'ALBUM_MEDIALIB_URL', WP_PLUGIN_URL . '/' . basename( ALBUM_MEDIALIB_DIR ) ); // https://url/wp-content/plugins/album-medialib-github/ .
	define( 'ALBUM_MEDIALIB_NAME', basename( ALBUM_MEDIALIB_DIR ) ); // album-medialib-github

	include_once ALBUM_MEDIALIB_DIR . 'admin.php';
	require_once ALBUM_MEDIALIB_DIR . 'github-backend-album-medialib.php';
}

<?php
/**
 * Plugin Name:       Album of photos from a folder in the Media Library Github
 * Plugin URI:        https://leafext.de/hp/categories/medialib/
 * Description:       Organize your photos in folders, select a path and display these photos with any gallery shortcode.
 * Update URI:        https://github.com/hupe13/album-medialib-github
 * Version:           250530
 * Requires PHP:      8.1
 * Author:            hupe13
 * Author URI:        https://leafext.de/hp/
 * License:           GPL v2 or later
 *
 * @package album-medialib
 **/

// Direktzugriff auf diese Datei verhindern.
defined( 'ABSPATH' ) || die();

function album_medialib_params() {
	$params = array(
		array(
			'param'   => 'shortcode',
			'desc'    => __( 'Your Shortcode', 'album-media-library' ),
			'default' => 'gallery',
		),
		array(
			'param'   => 'ids',
			/* translators: %s is an option. */
			'desc'    => sprintf( __( 'Name of the %s option', 'album-media-library' ), 'list' ),
			'default' => 'ids',
		),
		array(
			'param'    => 'transients',
			'desc'     => __( 'Use transients', 'album-media-library' ),
			'longdesc' => sprintf(
				/* translators: %s is "false". */
				__(
					'Delete transients or set it to %s, if you have trouble to get the right images in the album.',
					'album-media-library'
				),
				'<code>false</code> (<code>0</code>)'
			),
			'values'   => array(
				true,
				false,
				__( 'Delete transients', 'album-media-library' ),
			),
			'default'  => true,
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
	$setting = shortcode_atts( $defaults, get_option( 'album_medialib', $defaults ) );
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
						if ( $setting['transients'] ) {
							$ids = get_transient( 'album_medialib_' . $attr['path'] );
						} else {
							$ids = false;
						}
						if ( false === $ids ) {
							global $wpdb;
							$image = '%' . $attr['path'] . '%';
							// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
							$results = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_value LIKE %s", $image ) );
							$ids     = '';
							if ( count( $results ) > 0 ) {
								foreach ( $results as $result ) {
									$ids .= $result->post_id . ',';
								}
							}
							if ( $setting['transients'] && $ids !== '' ) {
								set_transient( 'album_medialib_' . $attr['path'], $ids, DAY_IN_SECONDS );
							}
						}
						if ( $ids !== '' ) {
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

	// Create a function to delete our transient
	function album_medialib_delete_transient( $attachment_id ) {
		global $wpdb;
		$image_path = wp_get_original_image_path( $attachment_id );
		// var_dump($image_path);
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$query = $wpdb->get_col( $wpdb->prepare( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE(%s)", '\_transient\_album\_medialib\_%' ) );
		if ( $query ) {
			foreach ( $query as $option_name ) {
				// var_dump($option_name);
				$path = str_replace( '_transient_album_medialib_', '', $option_name );
				// var_dump($path);
				if ( strpos( $image_path, $path ) !== false ) {
					delete_transient( str_replace( '_transient_', '', $option_name ) );
				}
			}
		}
	}
	add_action( 'add_attachment', 'album_medialib_delete_transient', 10, 1 );
	add_action( 'delete_attachment', 'album_medialib_delete_transient' );

	define( 'ALBUM_MEDIALIB_FILE', __FILE__ ); // /pfad/wp-content/plugins/album-media-library/album-media-library.php .
	define( 'ALBUM_MEDIALIB_DIR', plugin_dir_path( __FILE__ ) ); // /pfad/wp-content/plugins/album-media-library/ .
	// define( 'ALBUM_MEDIALIB_URL', WP_PLUGIN_URL . '/' . basename( ALBUM_MEDIALIB_DIR ) ); // https://url/wp-content/plugins/album-media-library/ .
	define( 'ALBUM_MEDIALIB_NAME', basename( ALBUM_MEDIALIB_DIR ) ); // album-media-library

	function album_medialib_action_links( $actions ) {
		$actions[] = '<a href="' . esc_url( get_admin_url( null, 'upload.php?page=album-media-library' ) ) . '">' . esc_html__( 'Settings', 'album-media-library' ) . '</a>';
		return $actions;
	}
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'album_medialib_action_links' );

	include_once ALBUM_MEDIALIB_DIR . 'admin.php';
	require_once ALBUM_MEDIALIB_DIR . 'github-album-medialib.php';
}

<?php
/**
 * Plugin Name:       Photonic Album from Media Library Github
 * Description:       Display Photonic album from photos in Media Library by path
 * Version:           250420
 * Requires PHP:      8.2
 * Requires Plugins:  photonic
 * Author:            hupe13
 * Author URI:        https://leafext.de/en/
 * License:           GPL v2 or later
 *
 * @package Photonic album
 **/

// Direktzugriff auf diese Datei verhindern.
defined( 'ABSPATH' ) || die();

function photonic_album_function( $atts ) {
	if ( is_singular() || is_archive() || is_home() || is_front_page() ) {
		if ( ! $atts['path'] ) {
			$text = '[album-photonic ';
			foreach ( $atts as $key => $item ) {
				$text = $text . "$key=$item ";
			}
			$text = $text . ']';
			return $text;
		}
		global $wpdb;
		$image = '%' . $atts['path'] . '%';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$results = $wpdb->get_results( $wpdb->prepare( 'SELECT post_id FROM wp_postmeta WHERE meta_value LIKE %s', $image ) );
		if ( count( $results ) > 0 ) {
			$shortcode = '[photonic ids="';
			foreach ( $results as $result ) {
				$shortcode .= $result->post_id . ',';
			}
			$shortcode .= '" ';
			foreach ( $atts as $key => $item ) {
				if ( $key !== 'path' ) {
					$shortcode .= "$key='$item' ";
				}
			}
			$shortcode .= ']';
			echo do_shortcode( $shortcode );
		}
	}
}
add_shortcode( 'album-photonic', 'photonic_album_function' );

if ( is_admin() ) {

	define( 'PHOTONIC_ALBUM_FILE', __FILE__ ); // /pfad/wp-content/plugins/photonic_album-update-github/photonic_album-update-github.php .
	define( 'PHOTONIC_ALBUM_DIR', plugin_dir_path( __FILE__ ) ); // /pfad/wp-content/plugins/photonic_album-update-github/ .
	define( 'PHOTONIC_ALBUM_URL', WP_PLUGIN_URL . '/' . basename( PHOTONIC_ALBUM_DIR ) ); // https://url/wp-content/plugins/photonic_album-update-github/ .
	define( 'PHOTONIC_ALBUM_NAME', basename( PHOTONIC_ALBUM_DIR ) ); // photonic_album-update-github

	if ( ! function_exists( 'get_plugin_data' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	// string $plugin_file, bool $markup = true, bool $translate = true
	$plugin_data = get_plugin_data( __FILE__, true, false );
	define( 'PHOTONIC_ALBUM_VERSION', $plugin_data['Version'] );

	if ( ! function_exists( 'leafext_plugin_active' ) ) {
		function leafext_plugin_active( $slug ) {
			$plugins = glob( WP_PLUGIN_DIR . '/*/' . $slug . '.php' );
			foreach ( $plugins as $plugin ) {
				if ( is_plugin_active( plugin_basename( $plugin ) ) ) {
					$dir = dirname( plugin_basename( $plugin ) );
					if ( $dir === 'leafext-update-github' ) {
						return true;
					}
					if ( $dir !== $slug ) {
						return 'github';
					}
					return true;
				}
			}
			return false;
		}
	}

	if ( leafext_plugin_active( 'photonic' ) ) {
		// Add settings to plugin page
		function leafext_add_action_photonic_album_links( $actions ) {
			$actions[] = '<a href="' . esc_url( admin_url( 'admin.php' ) . '?page=github-settings' ) . '">' . esc_html__( 'Settings', 'album-photonic' ) . '</a>';
			return $actions;
		}
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'leafext_add_action_photonic_album_links' );
	}

	// WP < 6.5, ClassicPress
	function photonic_album_require() {
		if ( ! leafext_plugin_active( 'photonic' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			$message = '<div><p>' . sprintf(
				/* translators: %s is a link. */
				esc_html__( 'Please install and activate %1$sPhotonic%2$s before using Photonic album.', 'album-photonic' ),
				'<a href="https://wordpress.org/plugins/photonic/">',
				'</a>'
			) . '</p><p><a href="' . esc_html( network_admin_url( 'plugins.php' ) ) . '">' .
				__( 'Manage plugins', 'album-photonic' ) . '</a>.</p></div>';
			$error = new WP_Error(
				'error',
				$message,
				array(
					'title'    => __( 'Plugin Error', 'album-photonic' ),
					'response' => '406',
				)
			);
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- it is an WP Error
			wp_die( $error, '', wp_kses_post( $error->get_error_data() ) );
		}
	}
	register_activation_hook( __FILE__, 'photonic_album_require' );

	require_once PHOTONIC_ALBUM_DIR . 'github-backend-album-photonic.php';
}

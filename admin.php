<?php
/**
 *  Admin album-medialib Settings
 *
 * @package album-medialib
 **/

// Direktzugriff auf diese Datei verhindern.
defined( 'ABSPATH' ) || die();

function album_medialib_add_sub_page() {
	add_submenu_page(
		'upload.php',
		__( 'Media Album', 'album-media-library' ),
		__( 'Media Album', 'album-media-library' ),
		'manage_options',
		ALBUM_MEDIALIB_NAME,
		'album_medialib_admin',
	);
}
add_action( 'admin_menu', 'album_medialib_add_sub_page' );

// Admin page for the plugin
function album_medialib_admin() {
	echo '<h2>' . esc_html__( 'Album of photos from a folder in the Media Library', 'album-media-library' ) . '</h2>';
	if ( function_exists( 'album_medialib_updates_from_github' ) ) {
		album_medialib_updates_from_github();
	}
	echo '<h3>' . esc_html__( 'Help and Options', 'album-media-library' ) . '</h3>';
	$allowed_html          = wp_kses_allowed_html( 'post' );
	$allowed_html['style'] = true;
	echo wp_kses( album_medialib_help(), $allowed_html );
	if ( current_user_can( 'manage_options' ) ) {
		echo '<form method="post" action="options.php">';
	} else {
		echo '<form>';
	}
	settings_fields( 'album_medialib_settings' );
	do_settings_sections( 'album_medialib_settings' );
	if ( current_user_can( 'manage_options' ) ) {
		wp_nonce_field( 'album_medialib', 'album_medialib_nonce' );
		submit_button();
		submit_button( esc_html__( 'Reset', 'album-media-library' ), 'delete', 'delete', false );
	}
	echo '</form>';
}

// Init settings
function album_medialib_init() {
	add_settings_section( 'album_medialib_settings', '', '', 'album_medialib_settings' );
	$fields = album_medialib_params();
	foreach ( $fields as $field ) {
		add_settings_field( 'album_medialib[' . $field['param'] . ']', $field['desc'], 'album_medialib_form', 'album_medialib_settings', 'album_medialib_settings', $field['param'] );
	}
	register_setting( 'album_medialib_settings', 'album_medialib', 'album_medialib_validate' );
}
add_action( 'admin_init', 'album_medialib_init' );

// Baue Abfrage der Params
function album_medialib_form( $field ) {
	$setting = album_medialib_settings();
	$options = album_medialib_params();
	if ( ! current_user_can( 'manage_options' ) ) {
		$disabled = ' disabled ';
	} else {
		$disabled = '';
	}

	foreach ( $options as $option ) {
		if ( $option['param'] === $field ) {
			if ( isset( $option['longdesc'] ) ) {
				echo '<p>' . wp_kses_post( $option['longdesc'] ) . '</p>';
			}
			if ( isset( $option['values'] ) ) {
				echo '<select ' . esc_attr( $disabled ) . ' name="album_medialib[' . esc_attr( $field ) . ']">';
				foreach ( $option['values'] as $para ) {
					echo '<option ';
					if ( $para === $setting[ $field ] ) {
						echo ' selected="selected" ';
					}
					if ( is_bool( $para ) ) {
						$para = ( $para ? '1' : '0' );
					}
					echo 'value="' . esc_attr( $para ) . '">' . esc_attr( $para ) . '</option>';
				}
				echo '</select>';
			} else {
				echo '<input ' . esc_attr( $disabled ) . 'name="album_medialib[' . esc_attr( $field ) . ']" value="' . esc_attr( $setting[ $field ] ) . '"' . esc_attr( $setting[ $field ] ) . '/>';
			}
		}
	}
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function album_medialib_validate( $input ) {
	if ( ! empty( $_POST ) && check_admin_referer( 'album_medialib', 'album_medialib_nonce' ) ) {
		if ( isset( $_POST['submit'] ) ) {
			if ( $input['transients'] === __( 'Delete transients', 'album-media-library' ) ) {
				global $wpdb;
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
				$query = $wpdb->get_col( $wpdb->prepare( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE(%s)", '\_transient\_album\_medialib\_%' ) );
				if ( $query ) {
					foreach ( $query as $option_name ) {
						delete_transient( str_replace( '_transient_', '', $option_name ) );
					}
				}
				$input['transients'] = true;
			} else {
				$input['transients'] = (bool) $input['transients'];
			}
			return $input;
		}
		if ( isset( $_POST['delete'] ) ) {
			// delete transients
			global $wpdb;
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			$query = $wpdb->get_col( $wpdb->prepare( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE(%s)", '\_transient\_album\_medialib\_%' ) );
			if ( $query ) {
				foreach ( $query as $option_name ) {
					delete_transient( str_replace( '_transient_', '', $option_name ) );
				}
			}
			delete_option( 'album_medialib' );
		}
		return false;
	}
}

function album_medialib_help() {
	$text  = '<ul style="max-width: 750px;"><li style="list-style-type:disc;margin-left: 1.5em;">';
	$text .= __( 'Organize your photos in directories. Store the photos for each album in a subdirectory in the WordPress upload directory.', 'album-media-library' );
	$text .= ' ' . sprintf(
		/* translators: %1$s, %2$s is an url. */
		__( 'You can use a %1$splugin%2$s to do this.', 'album-media-library' ),
		'<a href="https://wordpress.org/plugins/search/media+library+folder/">',
		'</a>'
	);
	$text .= '</li><li style="list-style-type:disc;margin-left: 1.5em;"> ';
	$text .= __( 'Import these photos to the Media Library. Maybe your plugin has this function too.', 'album-media-library' );
	$text .= '</li><li style="list-style-type:disc;margin-left: 1.5em;"> ';
	$text .= sprintf(
		/* translators: %1$s is a url to a plugin. */
		__( 'I use %1$s to import the photos to the Media Library, which I uploaded before with sftp.', 'album-media-library' ),
		'<a href="https://wordpress.org/plugins/bulk-media-register/">Bulk Media Register</a>'
	);
	$text .= '</li><li style="list-style-type:disc;margin-left: 1.5em;"> ';
	$text .= sprintf(
		/* translators: %1$s is "gallery", %2$s is "Photonic" */
		__( 'Use the default %1$s shortcode or install a plugin to display photos from Media Library, which has a shortcode for this, for example %2$s.', 'album-media-library' ),
		'<code>gallery</code>',
		'Photonic'
	);
	$text .= '</li><li style="list-style-type:disc;margin-left: 1.5em;"> ';
	$text .= sprintf(
		/* translators: %1$s is "gallery", %2$s "ids". */
		__( 'Configure the name of this shortcode (default %1$s) and the list option (default %2$s).', 'album-media-library' ),
		'<code>gallery</code>',
		'<code>ids</code>'
	);
	$text .= '</li><li style="list-style-type:disc;margin-left: 1.5em;"> ';
	$text .= sprintf(
		/* translators: %s is "path". */
		__( 'Write your shortcode as usual, omit the list option and use an extra option %s.', 'album-media-library' ),
		'<code>path</code>'
	);
	$text .= '</li><li style="list-style-type:disc;margin-left: 1.5em;"> ';
	$text .= sprintf(
		/* translators: %1$s is "path", %2$s an example. */
		__( '%1$s is a substring of the directory path, for example %2$s.', 'album-media-library' ),
		'<code>path</code>',
		'<code>holidays25/day1</code>'
	);
	$text .= '</li></ul>';
	$text .= '<h3>';
	$text .= sprintf(
		/* translators: %s is a plugin. */
		__( 'Workflow using the example of %s', 'album-media-library' ),
		'<a href="https://wordpress.org/plugins/photonic/">Photonic Gallery & Lightbox for Flickr, SmugMug & Others</a>'
	);
	$text .= '</h3>';
	$text .= '<ul style="max-width: 750px;"><li style="list-style-type:disc;margin-left: 1.5em;"> ';
	$text .= __( 'Use the Photonic Gallery block to generate the display of photos.', 'album-media-library' );
	$text .= '</li><li style="list-style-type:disc;margin-left: 1.5em;"> ';
	$text .= __( 'Choose as Gallery Source "WordPress".', 'album-media-library' );
	$text .= '</li><li style="list-style-type:disc;margin-left: 1.5em;"> ';
	$text .= __( 'Choose as Type of Gallery "Photos from Media Library".', 'album-media-library' );
	$text .= '</li><li style="list-style-type:disc;margin-left: 1.5em;"> ';
	$text .= __( 'Select one any photo.', 'album-media-library' );
	$text .= '</li><li style="list-style-type:disc;margin-left: 1.5em;"> ';
	$text .= __( 'Pick Your Layout.', 'album-media-library' );
	$text .= '</li><li style="list-style-type:disc;margin-left: 1.5em;"> ';
	$text .= __( 'Configure Your Layout.', 'album-media-library' );
	$text .= '</li><li style="list-style-type:disc;margin-left: 1.5em;"> ';
	$text .= __( 'In window "Your Gallery" select the shortcode and paste this into a Shortcode block.', 'album-media-library' );
	$text .= '</li><li style="list-style-type:disc;margin-left: 1.5em;"> ';
	$text .= sprintf(
		/* translators: %s are options. */
		__( 'Change %1$s to %2$s.', 'album-media-library' ),
		'<code>ids=...</code>',
		'<code>path=holidays25/day1</code>'
	);
	$text   .= '</li></ul>';
	$setting = album_medialib_settings();
	$text   .= '<h3>' . __( 'Shortcode with your gallery plugin', 'album-media-library' ) . '</h3>';
	$text   .= '<pre><code>&#091;' . $setting['shortcode'] . ' ' . $setting['ids'] . '="1,2,3,4" option1=... option2=...  ...]</code></pre>';
	$text   .= '<h3>' . __( 'becomes', 'album-media-library' ) . '</h3>';
	$text   .= '<pre><code>&#091;' . $setting['shortcode'] . ' path="holidays25/day1" option1=... option2=...  ...]</code></pre>';
	return $text;
}

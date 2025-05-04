<?php
/**
 *  Admin album-medialib Settings
 *
 * @package album-medialib
 **/

// Direktzugriff auf diese Datei verhindern.
defined( 'ABSPATH' ) || die();

function album_medialib_add_page() {
	add_menu_page(
		'album-medialib',
		__( 'Medialib Album', 'album-medialib' ),
		'manage_options',
		'album-medialib',
		'album_medialib_admin',
		'' // icon
	);
}
add_action( 'admin_menu', 'album_medialib_add_page' );

function album_medialib_action_links( $actions ) {
	$actions[] = '<a href="' . esc_url( admin_url( 'admin.php' ) . '?page=album-medialib' ) . '">' . esc_html__( 'Settings', 'album-medialib' ) . '</a>';
	return $actions;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'album_medialib_action_links' );

// Admin page for the plugin
function album_medialib_admin() {
	echo '<h3>' . esc_html__( 'Display photos located below a specific folder from the media library', 'album-medialib' ) . ' - ' . esc_html__( 'Help and Options', 'album-medialib' ) . '</h3>';
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
		submit_button( esc_html__( 'Reset', 'album-medialib' ), 'delete', 'delete', false );
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
	if ( ! current_user_can( 'manage_options' ) ) {
		$disabled = ' disabled ';
	} else {
		$disabled = '';
	}
	if ( is_bool( $setting[ $field ] ) ) {
		echo '<input ' . esc_attr( $disabled ) . ' type="radio" name="album_medialib[' . esc_attr( $field ) . ']" value="1" ';
		echo $setting[ $field ] ? 'checked' : '';
		echo '> true &nbsp;&nbsp; ';
		echo '<input ' . esc_attr( $disabled ) . ' type="radio" name="album_medialib[' . esc_attr( $field ) . ']" value="0" ';
		echo ( ! $setting[ $field ] ) ? 'checked' : '';
		echo '> false ';
	} else {
		echo '<input ' . esc_attr( $disabled ) . 'name="album_medialib[' . esc_attr( $field ) . ']" value="' . esc_attr( $setting[ $field ] ) . '"' . esc_attr( $setting[ $field ] ) . '/>';
	}
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function album_medialib_validate( $input ) {
	if ( ! empty( $_POST ) && check_admin_referer( 'album_medialib', 'album_medialib_nonce' ) ) {
		if ( isset( $_POST['submit'] ) ) {
			$input['transients'] = (bool) ( $input['transients'] );
			return $input;
		}
		if ( isset( $_POST['delete'] ) ) {
			delete_option( 'album_medialib' );
			// delete transients
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			$query = $wpdb->get_col( $wpdb->prepare( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE(%s)", '_transient_album_medialib\_%' ) );
			if ( $query ) {
				foreach ( $query as $option_name ) {
					delete_transient( $option_name );
				}
			}
		}
		return false;
	}
}

function album_medialib_help() {
	$text  = '<style>li {list-style-type: disc;margin-left: 1.5em;}</style>';
	$text .= '<ul><li> ';
	$text .= __( 'Organize your photos in directories. Store the photos for each album in a subdirectory in the WordPress upload directory.', 'album-medialib' );
	$text .= '</li><li> ';
	$text .= sprintf(
		/* translators: %1$s, %2$s is an url, %3$s is a url to a plugin. */
		__( 'You can use a %1$splugin%2$s to do this. I use %3$s to import the photos to the Media Library, which I uploaded before with sftp.', 'album-medialib' ),
		'<a href="https://wordpress.org/plugins/search/media+library+folder/">',
		'</a>',
		'<a href="https://wordpress.org/plugins/bulk-media-register/">Bulk Media Register</a>'
	);
	$text .= '</li><li> ';
	$text .= sprintf(
		/* translators: %1$s is "shortcode", %2$s is a url to a plugin. */
		__( 'Use the default %1$s shortcode or install a plugin to display photos from Media Library, which has a shortcode for this, for example %2$s.', 'album-medialib' ),
		'<code>gallery</code>',
		'<a href="https://wordpress.org/plugins/photonic/">Photonic Gallery & Lightbox for Flickr, SmugMug & Others</a>'
	);
	$text .= '</li><li> ';
	$text .= sprintf(
		/* translators: %1$s is "gallery", %2$s "ids". */
		__( 'Configure the name of this shortcode (default %1$s) and the list option (default %2$s).', 'album-medialib' ),
		'<code>gallery</code>',
		'<code>ids</code>'
	);
	$text .= '</li><li> ';
	$text .= sprintf(
		/* translators: %s is "path". */
		__( 'Write your shortcode as usual, omit the list option and use an extra option %s.', 'album-medialib' ),
		'<code>path</code>'
	);
	$text .= '</li><li> ';
	$text .= sprintf(
		/* translators: %1$s is "path", %2$s an example. */
		__( '%1$s is a substring of the directory path, for example %2$s.', 'album-medialib' ),
		'<code>path</code>',
		'<code>holidays25/day1</code>'
	);
	$text   .= '</li></ul>';
	$setting = album_medialib_settings();
	$text   .= '<h3>' . __( 'Shortcode with your gallery plugin', 'album-medialib' ) . '</h3>';
	$text   .= '<pre><code>&#091;' . $setting['shortcode'] . ' ' . $setting['ids'] . '"1,2,3,4" option1=... option2=...  ...]</code></pre>';
	$text   .= '<h3>' . __( 'becomes', 'album-medialib' ) . '</h3>';
	$text   .= '<pre><code>&#091;' . $setting['shortcode'] . ' path="holidays25/day1" option1=... option2=...  ...]</code></pre>';

	return $text;
}

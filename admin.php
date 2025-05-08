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

function album_medialib_add_sub_page() {
	add_submenu_page(
		'upload.php',
		__( 'Medialib Album', 'album-medialib' ),
		__( 'Medialib Album', 'album-medialib' ),
		'manage_options',
		'album-medialib',
		'album_medialib_admin',
	);
}
add_action( 'admin_menu', 'album_medialib_add_sub_page' );

// Admin page for the plugin
function album_medialib_admin() {
	echo '<h2>' . esc_html__( 'Album of photos from a folder in the Media Library', 'album-medialib' ) . '</h2>';
	echo '<h3>' . esc_html__( 'Help and Options', 'album-medialib' ) . '</h3>';
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
			global $wpdb;
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			$query = $wpdb->get_col( $wpdb->prepare( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE(%s)", '\_transient\_album\_medialib\_%' ) );
			if ( $query ) {
				foreach ( $query as $option_name ) {
					delete_transient( str_replace( '_transient_', '', $option_name ) );
				}
			}
		}
		return false;
	}
}

function album_medialib_help() {
	$text  = '<style>li {list-style-type: disc;margin-left: 1.5em;}ul {max-width: 750px;}</style>';
	$text .= '<ul><li> ';
	$text .= __( 'Organize your photos in directories. Store the photos for each album in a subdirectory in the WordPress upload directory.', 'album-medialib' );
	$text .= ' ' . sprintf(
		/* translators: %1$s, %2$s is an url. */
		__( 'You can use a %1$splugin%2$s to do this.', 'album-medialib' ),
		'<a href="https://wordpress.org/plugins/search/media+library+folder/">',
		'</a>'
	);
	$text .= '</li><li> ';
	$text .= __( 'Import these photos to the Media Library. Maybe your plugin has this function too.', 'album-medialib' );
	$text .= '</li><li> ';
	$text .= sprintf(
		/* translators: %1$s is a url to a plugin. */
		__( 'I use %1$s to import the photos to the Media Library, which I uploaded before with sftp.', 'album-medialib' ),
		'<a href="https://wordpress.org/plugins/bulk-media-register/">Bulk Media Register</a>'
	);
	$text .= '</li><li> ';
	$text .= sprintf(
		/* translators: %1$s is "gallery", %2$s is "Photonic" */
		__( 'Use the default %1$s shortcode or install a plugin to display photos from Media Library, which has a shortcode for this, for example %2$s.', 'album-medialib' ),
		'<code>gallery</code>',
		'Photonic'
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
	$text .= '</li></ul>';
	$text .= '<h3>';
	$text .= sprintf(
		/* translators: %s is a plugin. */
		__( 'Workflow using the example of %s', 'album-medialib' ),
		'<a href="https://wordpress.org/plugins/photonic/">Photonic Gallery & Lightbox for Flickr, SmugMug & Others</a>'
	);
	$text .= '</h3>';
	$text .= '<ul><li> ';
	$text .= __( 'Use the Photonic Gallery block to generate the display of photos.', 'album-medialib' );
	$text .= '</li><li> ';
	$text .= __( 'Choose as Gallery Source "WordPress".', 'album-medialib' );
	$text .= '</li><li> ';
	$text .= __( 'Choose as Type of Gallery "Photos from Media Library".', 'album-medialib' );
	$text .= '</li><li> ';
	$text .= __( 'Select one any photo.', 'album-medialib' );
	$text .= '</li><li> ';
	$text .= __( 'Pick Your Layout.', 'album-medialib' );
	$text .= '</li><li> ';
	$text .= __( 'Configure Your Layout.', 'album-medialib' );
	$text .= '</li><li> ';
	$text .= __( 'In window "Your Gallery" select the shortcode and paste this into a Shortcode block.', 'album-medialib' );
	$text .= '</li><li> ';
	$text .= sprintf(
		/* translators: %s are options. */
		__( 'Change %1$s to %2$s.', 'album-medialib' ),
		'<code>ids=...</code>',
		'<code>path=holidays25/day1</code>'
	);
	$text   .= '</li></ul>';
	$setting = album_medialib_settings();
	$text   .= '<h3>' . __( 'Shortcode with your gallery plugin', 'album-medialib' ) . '</h3>';
	$text   .= '<pre><code>&#091;' . $setting['shortcode'] . ' ' . $setting['ids'] . '"1,2,3,4" option1=... option2=...  ...]</code></pre>';
	$text   .= '<h3>' . __( 'becomes', 'album-medialib' ) . '</h3>';
	$text   .= '<pre><code>&#091;' . $setting['shortcode'] . ' path="holidays25/day1" option1=... option2=...  ...]</code></pre>';
	return $text;
}

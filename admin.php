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

// Admin page for the plugin
function album_medialib_admin() {
	echo '<h3>' . esc_html__( 'Display photos selected by path from the Media Library - Options and Help', 'album-medialib' ) . '</h3>';
	$allowed_html = wp_kses_allowed_html( 'post' );
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
	echo '<input ' . esc_attr( $disabled ) . 'name="album_medialib[' . esc_attr( $field ) . ']" value="' . esc_attr( $setting[ $field ] ) . '"' . esc_attr( $setting[ $field ] ) . '/>';
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function album_medialib_validate( $input ) {
	if ( ! empty( $_POST ) && check_admin_referer( 'album_medialib', 'album_medialib_nonce' ) ) {
		if ( isset( $_POST['submit'] ) ) {
			return $input;
		}
		if ( isset( $_POST['delete'] ) ) {
			delete_option( 'album_medialib' );
		}
		return false;
	}
}

function album_medialib_help() {
	$text = '
	<style>li {
		list-style-type: disc;
		margin-left: 1.5em;
	}</style>
	<ul>
	<li> Organize your photos in directories. Store the photos for each album in a subdirectory in the WordPress upload directory.</li>
	<li> Useful plugins:
	<ul>
	 <li> <a href="https://wordpress.org/plugins/bulk-media-register/">Bulk Media Register</a> to import the photos to the Media Library.</li>
	 <li> <a href="https://wordpress.org/plugins/upload-media-exif-date/">Upload Media Exif Date</a> to store to the date/time of Exif information.</li>
	 <li> <a href="https://wordpress.org/plugins/exif-caption/">Exif Caption</a> to insert the Exif data to the caption of the media.</li>
	</ul>
	</li>
	<li> Use the default <code>gallery</code> shortcode or install a plugin to display photos from Media Library, which has a shortcode for this, for example <a href="https://wordpress.org/plugins/photonic/">Photonic Gallery & Lightbox for Flickr, SmugMug & Others</a>.</li>
	<li> Configure the name of this shortcode (default <code>gallery</code>) and the list option (default <code>ids</code>) in admin backend.</li>
	<li> Write your shortcode as usual, omit the list option and use an extra option <code>path</code>.</li>
	<li> <code>path</code> is a substring of the directory path, for example <code>holidays25/day1</code>.</li>
	</ul>
	';
	return $text;
}

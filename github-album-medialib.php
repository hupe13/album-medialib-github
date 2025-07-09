<?php
/**
 * Backend Menus
 *
 * @package album-medialib
 **/

// Direktzugriff auf diese Datei verhindern.
defined( 'ABSPATH' ) || die();

// for translating, geklaut von PUC
function album_medialib_textdomain() {
	$domain  = 'album-media-library';
	$locale  = apply_filters(
		'plugin_locale',
		( is_admin() && function_exists( 'get_user_locale' ) ) ? get_user_locale() : get_locale(),
		$domain
	);
	$mo_file = $domain . '-' . $locale . '.mo';
	$path    = realpath( __DIR__ ) . '/lang/';
	if ( $path && file_exists( $path ) ) {
		load_textdomain( $domain, $path . $mo_file );
	}
}
add_action( 'plugins_loaded', 'album_medialib_textdomain' );

// Updates from Github
function leafext_album_updates() {
	echo '<h2>' . esc_html__( 'Updates in WordPress way', 'album-media-library' ) . '</h2>';
	if ( is_multisite() ) {
		if ( strpos(
			implode(
				',',
				array_keys(
					get_site_option( 'active_sitewide_plugins', array() )
				)
			),
			'leafext-update-github.php'
		) !== false ) {
					printf(
						/* translators: %s is a link. */
						esc_html__(
							'To manage and receive updates, open %1$sGithub settings%2$s.',
							'album-media-library'
						),
						'<a href="' . esc_url( get_site_url( get_main_site_id() ) ) . '/wp-admin/admin.php?page=github-settings">',
						'</a>'
					);
		} else {
			printf(
				/* translators: %s is a link. */
				esc_html__(
					'To receive updates, go to the %1$snetwork dashboard%2$s and install and activate %3$s.',
					'album-media-library'
				),
				'<a href="' . esc_url( network_admin_url() ) . 'plugins.php">',
				'</a>',
				'<a href="https://github.com/hupe13/leafext-update-github">Updates for plugins from hupe13 hosted on Github</a>'
			);
		}
	} elseif ( strpos(
		implode(
			',',
			get_option( 'active_plugins', array() )
		),
		'leafext-update-github.php'
	) !== false ) {
					printf(
						/* translators: %s is a link. */
						esc_html__(
							'To manage and receive updates, open %1$sGithub settings%2$s.',
							'album-media-library'
						),
						'<a href="' . esc_url( get_site_url( get_main_site_id() ) ) . '/wp-admin/admin.php?page=github-settings">',
						'</a>'
					);
	} else {
		printf(
			/* translators: %s is a link. */
			esc_html__(
				'To receive updates, go to the %1$sdashboard%2$s and install and activate %3$s.',
				'album-media-library'
			),
			'<a href="' . esc_url( network_admin_url() ) . 'plugins.php">',
			'</a>',
			'<a href="https://github.com/hupe13/leafext-update-github">Updates for plugins from hupe13 hosted on Github</a>'
		);
	}
}

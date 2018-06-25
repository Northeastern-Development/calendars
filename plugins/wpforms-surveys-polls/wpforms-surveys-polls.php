<?php
/**
 * Plugin Name: WPForms Surveys and Polls
 * Plugin URI:  https://wpforms.com
 * Description: Create Surveys and Polls with WPForms.
 * Author:      WPForms
 * Author URI:  https://wpforms.com
 * Version:     1.1.0
 * Text Domain: wpforms-surveys-polls
 * Domain Path: languages
 *
 * WPForms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WPForms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WPForms. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    WPFormsSurveys
 * @since      1.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2018, WP Forms LLC
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Requires PHP 5.4+.
 */
if ( version_compare( PHP_VERSION, '5.4', '<' ) ) {

	/**
	 * Deactivate plugin.
	 *
	 * @since 1.0.0
	 */
	function wpforms_surveys_polls_deactivate() {

		deactivate_plugins( plugin_basename( __FILE__ ) );
	}

	add_action( 'admin_init', 'wpforms_surveys_polls_deactivate' );

	/**
	 * Display notice after deactivation.
	 *
	 * @since 1.0.0
	 */
	function wpforms_surveys_polls_deactivate_msg() {

		echo '<div class="notice notice-error"><p>';
		printf(
			wp_kses(
				/* translators: %s - WPForms.com documentation page URL. */
				__( 'The WPForms Surveys and Poll plugin has been deactivated. Your site is running an outdated version of PHP that is no longer supported and is not compatible with the Surveys and Polls addon. <a href="%s" target="_blank" rel="noopener noreferrer">Read more</a> for additional information.', 'wpforms-surveys-polls' ),
				array(
					'a' => array(
						'href'   => array(),
						'rel'    => array(),
						'target' => array(),
					),
				)
			),
			'https://wpforms.com/docs/supported-php-version/'
		);
		echo '</p></div>';

		if ( isset( $_GET['activate'] ) ) { // WPCS: CSRF ok.
			unset( $_GET['activate'] ); // WPCS: CSRF ok.
		}
	}

	add_action( 'admin_notices', 'wpforms_surveys_polls_deactivate_msg' );

	return;
}

// Plugin constants.
define( 'WPFORMS_SURVEYS_POLLS_VERSION', '1.1.0' );
define( 'WPFORMS_SURVEYS_POLLS_FILE', __FILE__ );

/**
 * Load the class only when it's requested.
 * Inspired by the official example implementations of PSR-4:
 * https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-examples.md
 *
 * @since 1.0.0
 *
 * @param string $class
 */
spl_autoload_register( function( $class ) {

	// Our base namespace for all plugin classes.
	$prefix = 'WPFormsSurveys\\';

	// Does the class use the namespace prefix?
	$len = strlen( $prefix );
	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		// No, move to the next registered autoloader.
		return;
	}

	// Base directory for the namespace prefix.
	$base_dir = __DIR__ . '/src/';

	// Get the relative class name.
	$relative_class = substr( $class, $len );

	// Replace the namespace prefix with the base directory.
	// Replace namespace separators with directory separators in the relative
	// class name. Append with .php.
	$file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

	// Finally, require the file.
	if ( file_exists( $file ) ) {
		require_once $file;
	}
} );

/**
 * Get the instance of the plugin main class,
 * which actually loads all the code.
 *
 * @since 1.0.0
 *
 * @return \WPFormsSurveys\Loader
 */
function wpforms_surveys_polls() {
	return \WPFormsSurveys\Loader::get_instance();
}
wpforms_surveys_polls();

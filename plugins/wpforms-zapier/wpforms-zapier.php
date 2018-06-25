<?php
/**
 * Plugin Name: WPForms Zapier
 * Plugin URI:  https://wpforms.com
 * Description: Zapier integration with WPForms.
 * Author:      WPForms
 * Author URI:  https://wpforms.com
 * Version:     1.0.5
 * Text Domain: wpforms_zapier
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
 * @package    WPFormsZapier
 * @since      1.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WP Forms LLC
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin version
define( 'WPFORMS_ZAPIER_VERSION', '1.0.5' );

/**
 * Load the provider class.
 *
 * @since 1.0.0
 */
function wpforms_zapier() {

	// WPForms Pro is required.
	if ( ! class_exists( 'WPForms_Pro' ) ) {
		return;
	}

	load_plugin_textdomain( 'wpforms-zapier', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	require_once( plugin_dir_path( __FILE__ ) . 'class-zapier.php' );
}

add_action( 'wpforms_loaded', 'wpforms_zapier' );

/**
 * Load the plugin updater.
 *
 * @since 1.0.0
 *
 * @param string $key
 */
function wpforms_zapier_updater( $key ) {

	$args = array(
		'plugin_name' => 'WPForms Zapier',
		'plugin_slug' => 'wpforms-zapier',
		'plugin_path' => plugin_basename( __FILE__ ),
		'plugin_url'  => trailingslashit( plugin_dir_url( __FILE__ ) ),
		'remote_url'  => WPFORMS_UPDATER_API,
		'version'     => WPFORMS_ZAPIER_VERSION,
		'key'         => $key,
	);

	new WPForms_Updater( $args );
}

add_action( 'wpforms_updater', 'wpforms_zapier_updater' );

<?php
if (!defined('ABSPATH')) die('No direct access.');
/**
 * Easy Updates Manager utility class.
 */
class MPSUM_Utils {

	/**
	 * MPSUM_Utils constructor
	 */
	private function __construct() {

	}

	/**
	 * Returns instance of singleton pattern
	 *
	 * @return MPSUM_Utils
	 */
	public static function get_instance() {
		static $instance = null;
		if ( null === $instance ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * This function checks whether a specific plugin is installed, and returns information about it
	 *
	 * @param  string $name Specify "Plugin Name" to return details about it.
	 *
	 * @return array        Returns an array of details such as if installed, the name of the plugin and if it is active.
	 */
	public function is_installed($name) {

		// Needed to have the 'get_plugins()' function
		include_once(ABSPATH . 'wp-admin/includes/plugin.php');

		// Gets all plugins available
		$get_plugins = get_plugins();

		$active_plugins = $this->get_active_plugins();

		$plugin_info['installed'] = false;
		$plugin_info['active']    = false;

		// Loops around each plugin available.
		foreach ($get_plugins as $key => $value) {
			// If the plugin name matches that of the specified name, it will gather details.
			if ($value['Name'] != $name) {
				continue;
			}
			$plugin_info['installed'] = true;
			$plugin_info['name']      = $key;
			$plugin_info['version']   = $value['Version'];
			if (in_array($key, $active_plugins)) {
				$plugin_info['active'] = true;
			}
			break;
		}

		return $plugin_info;
	}

	/**
	 * Gets an array of plugins active on either the current site, or site-wide
	 *
	 * @return array - a list of plugin paths (relative to the plugin directory)
	 */
	private function get_active_plugins() {

		// Gets all active plugins on the current site
		$active_plugins = get_option('active_plugins');

		if (is_multisite()) {
			$network_active_plugins = get_site_option('active_sitewide_plugins');
			if (!empty($network_active_plugins)) {
				$network_active_plugins = array_keys($network_active_plugins);
				$active_plugins = array_merge($active_plugins, $network_active_plugins);
			}
		}

		return $active_plugins;
	}
}
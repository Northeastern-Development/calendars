<?php
if (!defined('ABSPATH')) die('No direct access.');

/**
 * Easy Updates Manager Admin Ajax
 * Handles ajax requests
 *
 * @package WordPress
 * @since 7.0.1
 */
class MPSUM_Admin_Ajax {

	/**
     * Holds the class instance.
     *
     * @access static
     * @var MPSUM_Admin_Ajax $instance
     */
	private static $instance = null;

	/**
     * Set a class instance.
     *
     * @access static
     */
	public static function run() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
	}

	/**
     * Class constructor.
     *
     * Initialize the class
	 *
	 * @access private
     */
	private function __construct() {
		add_action('wp_ajax_eum_axios_ajax', array($this, 'axios_ajax_handler'));
		add_action('wp_ajax_eum_ajax', array($this, 'ajax_handler'));
	}

	/**
	 * Updates plugins tab
	 */
	public function update_plugins_tab() {
		$this->render_plugins_tab();
	}

	/**
	 * Updates themes tab
	 */
	public function update_themes_tab() {
	    $this->render_themes_tab();
	}

	/**
	 * Updates logs tab
	 */
	public function update_logs_tab() {
		$this->render_logs_tab();
	}


	/**
	 * Prepares and return content to render plugins tab via ajax call
	 */
	private function render_plugins_tab() {
		$plugins_table = new MPSUM_Plugins_List_Table();
		$plugins_table->ajax_response();
	}

	/**
	 * Prepares and return content to render themes tab via ajax call
	 */
	private function render_themes_tab() {
		$themes_table = new MPSUM_Themes_List_Table();
		$themes_table->ajax_response();
	}

	/**
	 * Prepares and return content to render logs tab via ajax call
	 */
	private function render_logs_tab() {
		$logs_table = new MPSUM_Logs_List_Table();
		$logs_table->ajax_response();
	}

	/**
	 * Handles ajax all calls
	 */
	public function axios_ajax_handler() {

		if (!current_user_can('install_plugins')) return;

		parse_str(file_get_contents('php://input'), $data);
		$sub_action = isset($data['sub_action']) ? $data['sub_action']: 'get_core_options';
		$nonce = isset($data['_wpnonce']) ? $data['_wpnonce'] : '';
		if ( ! wp_verify_nonce($nonce, 'eum_nonce') || empty($sub_action) ) die('Security check');

		if ( method_exists( $this, $sub_action ) ) {
			$results = call_user_func( array( $this, $sub_action ), $data );

			if ( is_wp_error( $results ) ) {
				$results = array(
					'result' => false,
					'error_code' => $results->get_error_code(),
					'error_message' => $results->get_error_message(),
					'error_data' => $results->get_error_data(),
				);
			}

			wp_send_json($results);
		}
	}

	/**
	 * Handles ajax all calls
	 */
	public function ajax_handler() {

		if (!current_user_can('activate_plugins')) return;
		if (empty($_REQUEST)) return;

		extract($_REQUEST);

		if (!wp_verify_nonce($nonce, $nonce_key) || empty($subaction)) die('Security check');

		$results = array();
		$data = isset($data) ? $data : array();
		if (!method_exists($this, $subaction)) {
			do_action('eum_premium_ajax_handler', $subaction, $data);
			error_log("EUM: ajax_handler: no such command (".$subaction.")");
			die('No such command');
		} else {
			$results = call_user_func(array($this, $subaction), $data);

			// For WP List Table extended class (plugins, themes) result is already returned.
			if (is_wp_error($results)) {
				$results = array(
					'result' => false,
					'error_code' => $results->get_error_code(),
					'error_message' => $results->get_error_message(),
					'error_data' => $results->get_error_data(),
				);
			}

			// if nothing was returned for some reason, set as result null.
			if (empty($results)) {
				$results = array(
					'result' => null
				);
			}
		}

		$result = json_encode($results);

		$json_last_error = json_last_error();

		// if json_encode returned error then return error.
		if ($json_last_error) {
			$result = array(
				'result' => false,
				'error_code' => $json_last_error,
				'error_message' => 'json_encode error : '.$json_last_error,
				'error_data' => '',
			);

			$result = json_encode($result);
		}

		echo $result;

		die;

	}


	/**
	 * Saves core options. Ajax call method from React/Axios
	 *
	 * @param array $data An array of option to be saved
	 *
	 * @return array An array of core options
	 */
	public function save_core_options( $data ) {
		$id = $data['id'];
		$value = $data['value'];

		// Get options
		$options = MPSUM_Updates_Manager::get_options( 'core', true );
		if ( empty( $options ) ) {
			$options = MPSUM_Admin_Core::get_defaults();
		}

		$id = sanitize_text_field( $id );
		$value = sanitize_text_field( $value );

		$email_errors = false;
		switch ( $id ) {
			case 'automatic-updates-default':
				$options[ 'automatic_development_updates' ] = 'off';
				$options[ 'automatic_major_updates' ] = 'off';
				$options[ 'automatic_minor_updates' ] = 'on';
				$options[ 'automatic_plugin_updates' ] = 'default';
				$options[ 'automatic_theme_updates' ] = 'default';
				$options[ 'automatic_translation_updates' ] = 'on';
				$options[ 'automatic_updates' ] = 'default';
				break;
			case 'automatic-updates-on':
				$options[ 'automatic_development_updates' ] = 'off';
				$options[ 'automatic_major_updates' ] = 'on';
				$options[ 'automatic_minor_updates' ] = 'on';
				$options[ 'automatic_plugin_updates' ] = 'on';
				$options[ 'automatic_theme_updates' ] = 'on';
				$options[ 'automatic_translation_updates' ] = 'on';
				$options[ 'automatic_updates' ] = 'on';
				break;
			case 'automatic-updates-off':
				$options[ 'automatic_development_updates' ] = 'off';
				$options[ 'automatic_major_updates' ] = 'off';
				$options[ 'automatic_minor_updates' ] = 'off';
				$options[ 'automatic_plugin_updates' ] = 'off';
				$options[ 'automatic_theme_updates' ] = 'off';
				$options[ 'automatic_translation_updates' ] = 'off';
				$options[ 'automatic_updates' ] = 'off';
				break;
			case 'automatic-updates-custom':
				$options[ 'automatic_updates' ] = 'custom';
				break;
			case 'automatic-major-updates':
				if( 'on' == $value ) {
					$options[ 'automatic_major_updates' ] = 'on';
				} else {
					$options[ 'automatic_major_updates' ] = 'off';
				}
				$options[ 'automatic_updates' ] = 'custom';
				break;
			case 'automatic-minor-updates':
				if( 'on' == $value ) {
					$options[ 'automatic_minor_updates' ] = 'on';
				} else {
					$options[ 'automatic_minor_updates' ] = 'off';
				}
				$options[ 'automatic_updates' ] = 'custom';
				break;
			case 'automatic-development-updates':
				if( 'on' == $value ) {
					$options[ 'automatic_development_updates' ] = 'on';
				} else {
					$options[ 'automatic_development_updates' ] = 'off';
				}
				$options[ 'automatic_updates' ] = 'custom';
				break;
			case 'automatic-translation-updates':
				if( 'on' == $value ) {
					$options[ 'automatic_translation_updates' ] = 'on';
				} else {
					$options[ 'automatic_translation_updates' ] = 'off';
				}
				$options[ 'automatic_updates' ] = 'custom';
				break;
			case 'automatic-plugin-updates-default':
				$options[ 'automatic_plugin_updates' ] = 'default';
				$options[ 'automatic_updates' ] = 'custom';
				break;
			case 'automatic-plugin-updates-on':
				$options[ 'automatic_plugin_updates' ] = 'on';
				$options[ 'automatic_updates' ] = 'custom';
				break;
			case 'automatic-plugin-updates-off':
				$options[ 'automatic_plugin_updates' ] = 'off';
				$options[ 'automatic_updates' ] = 'custom';
				break;
			case 'automatic-plugin-updates-individual':
				$options[ 'automatic_plugin_updates' ] = 'individual';
				$options[ 'automatic_updates' ] = 'custom';
				break;
			case 'automatic-theme-updates-default':
				$options[ 'automatic_theme_updates' ] = 'default';
				$options[ 'automatic_updates' ] = 'custom';
				break;
			case 'automatic-theme-updates-on':
				$options[ 'automatic_theme_updates' ] = 'on';
				$options[ 'automatic_updates' ] = 'custom';
				break;
			case 'automatic-theme-updates-off':
				$options[ 'automatic_theme_updates' ] = 'off';
				$options[ 'automatic_updates' ] = 'custom';
				break;
			case 'automatic-theme-updates-individual':
				$options[ 'automatic_theme_updates' ] = 'individual';
				$options[ 'automatic_updates' ] = 'custom';
				break;
			case 'disable-updates':
				if( 'on' == $value ) {
					$options[ 'all_updates' ] = 'on';
				} else {
					$options[ 'all_updates' ] = 'off';
				}
				break;
			case 'logs':
				if( 'on' == $value ) {
					update_site_option( 'mpsum_log_table_version', 0 );
					$options[ 'logs' ] = 'on';
				} else {
					MPSUM_Logs::drop();
					update_site_option( 'mpsum_log_table_version', 0 );
					$options[ 'logs' ] = 'off';
				}
				break;
			case 'core-updates':
				if( 'on' == $value ) {
					$options[ 'core_updates' ] = 'on';
				} else {
					MPSUM_Logs::drop();
					$options[ 'core_updates' ] = 'off';
				}
				break;
			case 'plugin-updates':
				if( 'on' == $value ) {
					$options[ 'plugin_updates' ] = 'on';
				} else {
					MPSUM_Logs::drop();
					$options[ 'plugin_updates' ] = 'off';
				}
				break;
			case 'theme-updates':
				if( 'on' == $value ) {
					$options[ 'theme_updates' ] = 'on';
				} else {
					MPSUM_Logs::drop();
					$options[ 'theme_updates' ] = 'off';
				}
				break;
			case 'translation-updates':
				if( 'on' == $value ) {
					$options[ 'translation_updates' ] = 'on';
				} else {
					MPSUM_Logs::drop();
					$options[ 'translation_updates' ] = 'off';
				}
				break;
			case 'browser-nag':
				if( 'on' == $value ) {
					$options[ 'misc_browser_nag' ] = 'on';
				} else {
					$options[ 'misc_browser_nag' ] = 'off';
				}
				break;
			case 'version-footer':
				if( 'on' == $value ) {
					$options[ 'misc_wp_footer' ] = 'on';
				} else {
					$options[ 'misc_wp_footer' ] = 'off';
				}
				break;
			case 'email-notifications':
				if( 'on' == $value ) {
					$options[ 'notification_core_update_emails' ] = 'on';
				} else {
					$options[ 'notification_core_update_emails' ] = 'off';
				}
				break;
			case 'notification-emails':
				if ( 'unset' === $value ) {
					$options[ 'email_addresses' ] = array();
					break;
				}
				$emails = explode( ',', $value );
				foreach( $emails as $index => &$email ) {
					$email = trim( $email );
					if ( ! is_email ( $email ) ) {

						// Email error. Get out.
						$email_errors = true;
						break;
					}
				}

				if ( ! $email_errors ) {
					$options[ 'email_addresses' ] = $emails;
				}
				break;
		}
		// Save options
		MPSUM_Updates_Manager::update_options( $options, 'core' );

		// Return email addresses in format
		$value = trim( $value );
		if ( 'unset' === $value ) {
			$options[ 'email_addresses' ] = array();
		}
		if ( is_array( $options[ 'email_addresses' ] ) ) {
			$options[ 'email_addresses' ] = implode( ',', $options[ 'email_addresses' ] );
		} else {
			$options[ 'email_addresses' ] = array();
		}


		// Check automatic updates for fresh installation
		if ( ! isset( $options[ 'automatic_updates' ] ) || 'unset' == $options[ 'automatic_updates' ] ) {
			$options[ 'automatic_updates' ] = 'default';
		}

		// Add error to options for returning
		if ( $email_errors ) {
			$options[ 'errors' ] = true;
			$options[ 'email_addresses' ] = $value;
		}
		return $options;
	}

	/**
	 * Get all core options
	 *
	 * @param array $data Data for get options
	 * @return array - An array of core options
	 */
	public function get_core_options( $data ) {
		// Get options
		$options = MPSUM_Updates_Manager::get_options( 'core', true );
		if ( empty( $options ) ) {
			$options = MPSUM_Admin_Core::get_defaults();
		}

		// Set automatic updates defaults if none is selected
		if ( ! isset( $options[ 'automatic_updates' ] ) || 'unset' === $options[ 'automatic_updates' ] ) {

			// Check to see if automatic updates are on
			// Prepare for mad conditionals
			if ( 'off' == $options[ 'automatic_development_updates' ] && 'on' == $options[ 'automatic_major_updates' ] && 'on' == $options[ 'automatic_minor_updates' ] && 'on' == $options[ 'automatic_plugin_updates' ] && 'on' == $options[ 'automatic_theme_updates' ] && 'on' == $options[ 'automatic_translation_updates' ] ) {
				$options[ 'automatic_updates' ] = 'on';
			} elseif( 'off' == $options[ 'automatic_development_updates' ] && 'off' == $options[ 'automatic_major_updates' ] && 'off' == $options[ 'automatic_minor_updates' ] && 'off' == $options[ 'automatic_plugin_updates' ] && 'off' == $options[ 'automatic_theme_updates' ] && 'off' == $options[ 'automatic_translation_updates' ] ) {
				$options[ 'automatic_updates' ] = 'off';
			} elseif( 'off' == $options[ 'automatic_development_updates' ] && 'off' == $options[ 'automatic_major_updates' ] && 'on' == $options[ 'automatic_minor_updates' ] && 'default' == $options[ 'automatic_plugin_updates' ] && 'default' == $options[ 'automatic_theme_updates' ] && 'on' == $options[ 'automatic_translation_updates' ] ) {
				$options[ 'automatic_updates' ] = 'default';
			} else {
				$options[ 'automatic_updates' ] = 'custom';
			}
		}

		if ( ! is_array( $options[ 'email_addresses' ] ) ) {
			$options[ 'email_addresses' ] = array();
		}
		$options[ 'email_addresses' ] = implode( ',', $options[ 'email_addresses' ] );

		// return
		return $options;
	}

	/**
	 * Save the plugin options based on the passed data.
	 *
	 * @param string $data Action to take action on
	 *
	 * @return array Array of plugin update options
	 */
	public function save_plugins_update_options( $data ) {

		if ( !current_user_can( 'update_plugins' ) ) return;

		parse_str($data, $updated_options);
		$plugins = isset( $updated_options['plugins'] ) ? (array) $updated_options['plugins'] : array();
		$plugins_automatic = isset( $updated_options['plugins_automatic'] ) ? (array) $updated_options['plugins_automatic'] : array();
		$plugin_options = MPSUM_Updates_Manager::get_options( 'plugins' );
		$plugin_automatic_options = MPSUM_Updates_Manager::get_options( 'plugins_automatic' );
		foreach($plugins as $plugin => $choice) {
			if ("false" === $choice) {
				$plugin_options[] = $plugin;
				if ( ( $key = array_search( $plugin, $plugin_automatic_options ) ) !== false ) {
					unset( $plugin_automatic_options[ $key ] );
				}
			} else {
				if ( ( $key = array_search( $plugin, $plugin_options ) ) !== false ) {
					unset( $plugin_options[ $key ] );
				}
			}
		}


		foreach($plugins_automatic as $plugin => $choice) {
			if ("true" === $choice) {
				$plugin_automatic_options[] = $plugin;
				if ( ( $key = array_search( $plugin, $plugin_options ) ) !== false ) {
					unset( $plugin_options[ $key ] );
				}
			} else {
				if ( ( $key = array_search( $plugin, $plugin_automatic_options ) ) !== false ) {
					unset( $plugin_automatic_options[ $key ] );
				}
			}
		}

		$options = $this->plugins_update_all_options($plugin_options, $plugin_automatic_options);
		$this->render_plugins_tab();
		return $options;
	}

	/**
	 * Save the plugin options based on the passed action.
	 *
	 * @since 7.0.2
	 * @param string $data Data from ajax call
	 */
	public function bulk_action_plugins_update_options( $data ) {

		if ( !current_user_can( 'update_plugins' ) ) return;

		parse_str( $data, $updated_options );
		if ( isset( $updated_options['action'] ) && -1 != $updated_options['action'] ) {
			$action = $updated_options['action'];
		}
		if ( isset( $updated_options['action2'] ) && -1 != $updated_options['action2'] ) {
			$action = $updated_options['action2'];
		}

		$plugins = isset( $updated_options['checked'] ) ? (array) $updated_options['checked'] : array();
		$plugin_options = MPSUM_Updates_Manager::get_options( 'plugins' );
		$plugin_automatic_options = MPSUM_Updates_Manager::get_options( 'plugins_automatic' );

		switch ( $action ) {
			case 'disallow-update-selected':
				foreach ( $plugins as $plugin ) {
					$plugin_options[] = $plugin;
					if ( ( $key = array_search( $plugin, $plugin_automatic_options ) ) !== false ) {
						unset( $plugin_automatic_options[ $key ] );
					}
				}

				break;
			case 'allow-update-selected':
				foreach ( $plugins as $plugin ) {
					if ( ( $key = array_search( $plugin, $plugin_options ) ) !== false ) {
						unset( $plugin_options[ $key ] );
					}
				}
				break;
			case 'allow-automatic-selected':
				foreach ( $plugins as $plugin ) {
					$plugin_automatic_options[] = $plugin;
					if ( ( $key = array_search( $plugin, $plugin_options ) ) !== false ) {
						unset( $plugin_options[ $key ] );
					}
				}
				break;
			case 'disallow-automatic-selected':
				foreach ( $plugins as $plugin ) {
					if ( ( $key = array_search( $plugin, $plugin_automatic_options ) ) !== false ) {
						unset( $plugin_automatic_options[ $key ] );
					}
				}
				break;
			default:
				return;
		}

		$options = $this->plugins_update_all_options($plugin_options, $plugin_automatic_options);
		$this->render_plugins_tab();
	}

	/**
	 * Updates all plugin update options
	 *
	 * @param array $plugin_options           An array of plugin update options
	 * @param array $plugin_automatic_options An array of plugin automatic update options
	 *
	 * @return array
	 */
	private function plugins_update_all_options($plugin_options, $plugin_automatic_options) {
		$plugin_options = array_values( array_unique( $plugin_options ) );
		$plugin_automatic_options = array_values( array_unique( $plugin_automatic_options ) );
		$options = MPSUM_Updates_Manager::get_options();
		$options[ 'plugins' ] = $plugin_options;
		$options[ 'plugins_automatic' ] = $plugin_automatic_options;
		MPSUM_Updates_Manager::update_options( $options );
		return $options;
	}

	/**
	 * Save the theme options based on the passed data.
	 *
	 * @param string $data Action to take action on
	 *
	 * @return array Array of theme update options
	 */
	public function save_themes_update_options( $data ) {

		if ( !current_user_can( 'update_themes' ) ) return;

		parse_str( $data, $updated_options );
		$themes = isset( $updated_options['themes'] ) ? (array) $updated_options['themes'] : array();
		$themes_automatic = isset( $updated_options['themes_automatic'] ) ? (array) $updated_options['themes_automatic'] : array();
		$theme_options = MPSUM_Updates_Manager::get_options( 'themes' );
		$theme_automatic_options = MPSUM_Updates_Manager::get_options( 'themes_automatic' );
		foreach ( $themes as $theme => $choice ) {
			if ( "false" === $choice ) {
				$theme_options[] = $theme;
				if ( ( $key = array_search( $theme, $theme_automatic_options ) ) !== false ) {
					unset( $theme_automatic_options[ $key ] );
				}
			} else {
				if ( ( $key = array_search( $theme, $theme_options ) ) !== false ) {
					unset( $theme_options[ $key ] );
				}
			}
		}

		foreach ( $themes_automatic as $theme => $choice ) {
			if ( "true" === $choice ) {
				$theme_automatic_options[] = $theme;
				if ( ( $key = array_search( $theme, $theme_options ) ) !== false ) {
					unset( $theme_options[ $key ] );
				}
			} else {
				if ( ( $key = array_search( $theme, $theme_automatic_options ) ) !== false ) {
					unset( $theme_automatic_options[ $key ] );
				}
			}
		}

		$options = $this->themes_update_all_options( $theme_options, $theme_automatic_options );
		$this->render_themes_tab();
		return $options;
	}

	/**
	 * Save the theme options based on the passed action.
	 *
	 * @since 7.0.2
	 * @param string $data Data from ajax call
	 */
	public function bulk_action_themes_update_options( $data ) {
		if ( !current_user_can( 'update_themes' ) ) return;

		parse_str( $data, $updated_options );

		if ( isset( $updated_options['action'] ) && -1 != $updated_options['action'] ) {
			$action = $updated_options['action'];
		}
		if ( isset( $updated_options['action2'] ) && -1 != $updated_options['action2'] ) {
			$action = $updated_options['action2'];
		}

		$themes = isset( $updated_options[ 'checked' ] ) ? (array) $updated_options[ 'checked' ] : array();
		$theme_options = MPSUM_Updates_Manager::get_options( 'themes' );
		$theme_automatic_options = MPSUM_Updates_Manager::get_options( 'themes_automatic' );

		switch ( $action ) {

			case 'disallow-update-selected':
				foreach ( $themes as $theme ) {
					$theme_options[] = $theme;
					if ( ( $key = array_search( $theme, $theme_automatic_options ) ) !== false ) {
						unset( $theme_automatic_options[ $key ] );
					}
				}

				break;
			case 'allow-update-selected':
				foreach ( $themes as $theme ) {
					if ( ( $key = array_search( $theme, $theme_options ) ) !== false ) {
						unset( $theme_options[ $key ] );
					}
				}
				break;
			case 'allow-automatic-selected':
				foreach ( $themes as $theme ) {
					$theme_automatic_options[] = $theme;
					if ( ( $key = array_search( $theme, $theme_options ) ) !== false ) {
						unset( $theme_options[ $key ] );
					}
				}
				break;
			case 'disallow-automatic-selected':
				foreach ( $themes as $theme ) {
					if ( ( $key = array_search( $theme, $theme_automatic_options ) ) !== false ) {
						unset( $theme_automatic_options[ $key ] );
					}
				}
				break;
			default:
				return;
		}
		$options = $this->themes_update_all_options( $theme_options, $theme_automatic_options );
		$this->render_themes_tab();
	}

	/**
	 * Updates all theme update options
	 *
	 * @param array $theme_options           An array of theme update options
	 * @param array $theme_automatic_options An array of theme automatic update options
	 *
	 * @return array
	 */
	private function themes_update_all_options( $theme_options, $theme_automatic_options ) {
		$theme_options = array_values( array_unique( $theme_options ) );
		$theme_automatic_options = array_values( array_unique( $theme_automatic_options ) );
		$options = MPSUM_Updates_Manager::get_options();
		$options[ 'themes' ] = $theme_options;
		$options[ 'themes_automatic' ] = $theme_automatic_options;
		MPSUM_Updates_Manager::update_options( $options );
		return $options;
	}

	/**
	 * Checks for user permission
	 *
	 * @return bool - Returns true, if user has enough permissions else returns false
	 */
	public function permission_callback() {
		if ( current_user_can( 'install_plugins' ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Saves excluded users in options
	 *
	 * @param array $data An array of excludes users and other data
	 *
	 * @return mixed|string Returns update message if successful
	 */
	public function save_excluded_users( $data ) {
		parse_str( $data, $updated_options );
		$users = $updated_options[ 'mpsum_excluded_users' ];
		if ( !is_array( $users ) || empty( $users ) ) return;
		$users_to_save = array();
		foreach ( $users as $index => $user_id ) {
			$user_id = absint( $user_id );
			if ( 0 === $user_id ) continue;
			$users_to_save[] = $user_id;
		}
		MPSUM_Updates_Manager::update_options( $users_to_save, 'excluded_users' );
		$message = __( 'The exclusion of users option has been updated.', 'stops-core-theme-and-plugin-updates' );
		return $message;
	}

	/**
	 * Resets all update options
	 *
	 * @return mixed|string Returns update message, if successful.
	 */
	public function reset_options () {
		// Reset options
		MPSUM_Updates_Manager::update_options( array() );

		// Remove table version
		delete_site_option( 'mpsum_log_table_version' );

		// Remove logs table
		global $wpdb;
		$tablename = $wpdb->base_prefix . 'eum_logs';
		$sql = "drop table if exists $tablename";
		$wpdb->query( $sql );
		$message = __( 'The plugin settings have now been reset.', 'stops-core-theme-and-plugin-updates' );
		return $message;
	}

	/**
	 * Forces update to take place immediately
	 *
	 * @return mixed|string Returns update initialized message, if successful.
	 */
	public function force_updates () {
		$ran_immediately = false;
		if (function_exists('wp_maybe_auto_update')) {
			wp_maybe_auto_update();
			$ran_immediately = true;
		} else {
			$overdue = $this->howmany_overdue_crons();
			if ($overdue >=4) {
				wp_schedule_single_event(time() + 45, 'wp_maybe_auto_update');
			} else {
				wp_schedule_single_event(time(), 'wp_maybe_auto_update');
			}
		}
		delete_site_transient('MPSUM_PLUGINS');
		delete_site_transient('MPSUM_THEMES');
		$result = array(
			'message' => __('Force update checks have been initialized.', 'stops-core-theme-and-plugin-updates'),
			'ran_immediately' => $ran_immediately,
			'update_data' => $this->wp_get_update_data()
		);
		return $result;
	}

	/**
	 * Gets update data.
	 *
	 * Checks core, plugin, theme and translations has updates or not. If it has it includes its count
	 *
	 * @return array An array of update data
	 */
	private function wp_get_update_data() {
		$update_data = wp_get_update_data();
		if ($update_data['counts']['total'] > 0) {
			$update_data['admin_bar_link'] = sprintf('<a class="ab-item" href="%1$s" title="%2$s"><span class="ab-icon"></span><span class="ab-label">%3$s</span><span class="screen-reader-text">%2$s</span></a>', esc_url(self_admin_url('update-core.php')), $this->get_admin_bar_title($update_data), $update_data['counts']['total']);
			$update_data['updates_link'] = sprintf('%1$s<span class="update-plugins count-%2$s"><span class="update-count">%2$s</span></span>', __('Updates', 'stops-core-theme-and-plugin-updates'), $update_data['counts']['total']);
			$update_data['plugins_link'] = sprintf('%1$s <span class="update-plugins count-%2$s"><span class="plugin-count">%2$s</span></span>', __('Plugins', 'stops-core-theme-and-plugin-updates'), $update_data['counts']['plugins']);
			$update_data['themes_link'] = sprintf('%1$s <span class="update-plugins count-%2$s"><span class="plugin-count">%2$s</span></span>', __('Themes', 'stops-core-theme-and-plugin-updates'), $update_data['counts']['themes']);
		}
		$update_data['footer_upgrade_link'] = core_update_footer(); // This isn't working return old version
		return $update_data;
	}

	/**
	 * Constructs title attribute string based on update data
	 *
	 * @param array $update_data An array of update data
	 *
	 * @return string A string to be displayed as title attribute
	 */
	private function get_admin_bar_title($update_data) {
		$title = array();
		if ($update_data['counts']['wordpress'] > 0) {
			$title[] = sprintf(_n('%s WordPress Update', '%s WordPress Updates', $update_data['counts']['wordpress'], 'stops-core-theme-and-plugin-updates'), number_format_i18n($update_data['counts']['wordpress']));
		}
		if ($update_data['counts']['plugins'] > 0) {
			$title[] = sprintf(_n('%s Plugin Update', '%s Plugin Updates', $update_data['counts']['plugins'], 'stops-core-theme-and-plugin-updates'), number_format_i18n($update_data['counts']['plugins']));
		}
		if ($update_data['counts']['themes'] > 0) {
			$title[] = sprintf(_n('%s Theme Update', '%s Theme Updates', $update_data['counts']['themes'], 'stops-core-theme-and-plugin-updates'), number_format_i18n($update_data['counts']['themes']));
		}
		if ($update_data['counts']['translations'] > 0) {
			$title[] = __('Translation Updates', 'stops-core-theme-and-plugin-updates');
		}
		return implode(',', $title);
	}

	/**
	 * Get a count for the number of overdue cron jobs
	 *
	 * @return Integer - how many cron jobs are overdue
	 */
	private function howmany_overdue_crons() {
		$how_many_overdue = 0;
		if (function_exists('_get_cron_array') || (is_file(ABSPATH.WPINC.'/cron.php') && include_once(ABSPATH.WPINC.'/cron.php') && function_exists('_get_cron_array'))) {
			$crons = _get_cron_array();
			if (is_array($crons)) {
				$timenow = time();
				foreach ($crons as $jt => $job) {
					if ($jt < $timenow) $how_many_overdue++;
				}
			}
		}
		return $how_many_overdue;
	}

	/**
	 * Enables logs from advanced tab
	 *
	 * @return mixed|string Returns enabled message, if successful.
	 */
	public function enable_logs () {
		$options = MPSUM_Updates_Manager::get_options( 'core' );
		if ( empty( $options ) ) {
			$options = MPSUM_Admin_Core::get_defaults();
		}
		$options[ 'logs' ] = 'on';
		MPSUM_Updates_Manager::update_options( $options, 'core' );
		$message = __( 'Logs are now enabled', 'stops-core-theme-and-plugin-updates' );
		return $message;
	}

	/**
	 * Clears logs
	 *
	 * @return mixed|string Return logs emptied message, if successful.
	 */
	public function clear_logs () {
		MPSUM_Logs::clear();
		$message = __( 'Logs have been emptied', 'stops-core-theme-and-plugin-updates' );
		return $message;
	}
}

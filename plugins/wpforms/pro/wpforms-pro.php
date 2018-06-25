<?php
/**
 * WPForms Pro. Load Pro specific features/functionality.
 *
 * @since 1.2.1
 * @package WPForms
 */
class WPForms_Pro {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.2.1
	 */
	public function __construct() {

		$this->constants();
		$this->includes();

		add_action( 'wpforms_loaded',                      array( $this, 'objects'                        ), 1     );
		add_action( 'wpforms_loaded',                      array( $this, 'updater'                        ), 30    );
		add_action( 'wpforms_install',                     array( $this, 'install'                        ), 10    );
		add_filter( 'wpforms_settings_tabs',               array( $this, 'register_settings_tabs'         ), 5,  1 );
		add_filter( 'wpforms_settings_defaults',           array( $this, 'register_settings_fields'       ), 5,  1 );
		add_action( 'wpforms_process_entry_save',          array( $this, 'entry_save'                     ), 10, 4 );
		add_action( 'wpforms_form_settings_general',       array( $this, 'form_settings_general'          ), 10    );
		add_filter( 'wpforms_overview_table_columns',      array( $this, 'form_table_columns'             ), 10, 1 );
		add_filter( 'wpforms_overview_table_column_value', array( $this, 'form_table_columns_value'       ), 10, 3 );
		add_action( 'wpforms_form_settings_notifications', array( $this, 'form_settings_notifications'    ),  8, 1 );
		add_filter( 'wpforms_builder_strings',             array( $this, 'form_builder_strings'           ), 10, 2 );
		add_action( 'admin_notices',                       array( $this, 'conditional_logic_addon_notice' )        );
	}

	/**
	 * Include files.
	 *
	 * @since 1.0.0
	 */
	private function includes() {

		require_once WPFORMS_PLUGIN_DIR . 'pro/includes/class-db.php';
		require_once WPFORMS_PLUGIN_DIR . 'pro/includes/class-entry.php';
		require_once WPFORMS_PLUGIN_DIR . 'pro/includes/class-entry-meta.php';
		require_once WPFORMS_PLUGIN_DIR . 'pro/includes/class-conditional-logic-fields.php';
		require_once WPFORMS_PLUGIN_DIR . 'pro/includes/payments/class-payment.php';
		require_once WPFORMS_PLUGIN_DIR . 'pro/includes/payments/functions.php';

		if ( is_admin() ) {
			require_once WPFORMS_PLUGIN_DIR . 'pro/includes/admin/class-upgrades.php';
			require_once WPFORMS_PLUGIN_DIR . 'pro/includes/admin/ajax-actions.php';
			require_once WPFORMS_PLUGIN_DIR . 'pro/includes/admin/entries/class-entries-single.php';
			require_once WPFORMS_PLUGIN_DIR . 'pro/includes/admin/entries/class-entries-list.php';
			require_once WPFORMS_PLUGIN_DIR . 'pro/includes/admin/class-addons.php';
			require_once WPFORMS_PLUGIN_DIR . 'pro/includes/admin/class-updater.php';
			require_once WPFORMS_PLUGIN_DIR . 'pro/includes/admin/class-license.php';
		}
	}

	/**
	 * Setup objects.
	 *
	 * @since 1.2.1
	 */
	public function objects() {

		// Global objects
		wpforms()->entry        = new WPForms_Entry_Handler;
		wpforms()->entry_meta   = new WPForms_Entry_Meta_Handler;

		if ( is_admin() ) {
			wpforms()->license  = new WPForms_License;
		}
	}

	/**
	 * Setup plugin constants.
	 *
	 * @since 1.2.1
	 */
	public function constants() {

		// Plugin Updater API
		if ( ! defined( 'WPFORMS_UPDATER_API' ) ) {
			define( 'WPFORMS_UPDATER_API', 'https://wpforms.com/' );
		}
	}

	/**
	 * Load plugin updater.
	 *
	 * @since 1.0.0
	 */
	public function updater() {

		if ( !is_admin() ) {
			return;
		}

		$key = wpforms()->license->get();

		if ( !$key ) {
			return;
		}

		// Go ahead and initialize the updater.
		$args = array(
			'plugin_name' => 'WPForms',
			'plugin_slug' => 'wpforms',
			'plugin_path' => plugin_basename( WPFORMS_PLUGIN_FILE ),
			'plugin_url'  => trailingslashit( WPFORMS_PLUGIN_URL ),
			'remote_url'  => WPFORMS_UPDATER_API,
			'version'     => wpforms()->version,
			'key'         => $key,
		);
		$updater = new WPForms_Updater( $args );

		// Fire a hook for Addons to register their updater since we know the key is present.
		do_action( 'wpforms_updater', $key );
	}

	/**
	 * Handles plugin installation upon activation.
	 *
	 * @since 1.2.1
	*/
	public function install() {

		$wpforms_install             = new stdClass();
		$wpforms_install->entry      = new WPForms_Entry_Handler;
		$wpforms_install->entry_meta = new WPForms_Entry_Meta_Handler;

		// Entry tables
		$wpforms_install->entry->create_table();
		$wpforms_install->entry_meta->create_table();
	}

	/**
	 * Register Pro settings tabs.
	 *
	 * @since 1.3.9
	 * @param array $tabs
	 * @return array
	 */
	public function register_settings_tabs( $tabs ) {

		// Add Payments tab.
		$payments = array(
			'payments' => array(
				'name'   => __( 'Payments', 'wpforms' ),
				'form'   => true,
				'submit' => __( 'Save Settings', 'wpforms' ),
			),
		);

		$tabs = wpforms_array_insert( $tabs, $payments, 'validation' );

		return $tabs;
	}

	/**
	 * Register Pro settings fields.
	 *
	 * @since 1.3.9
	 * @param array $settings
	 * @return array
	 */
	public function register_settings_fields( $settings ) {

		// Format currencies for select element.
		$currencies = wpforms_get_currencies();
		foreach ( $currencies as $code => $currency ) {
			$currency_option[ $code ] = sprintf( '%s (%s %s)', $currency['name'], $code, $currency['symbol']  );
		}

		// Validation settings for fields only available in Pro.
		$settings['validation']['validation-fileextension'] = array(
			'id'        => 'validation-fileextension',
			'name'      => __( 'File Extension', 'wpforms' ),
			'type'      => 'text',
			'default'   => __( 'File type is not allowed.', 'wpforms' ),
		);
		$settings['validation']['validation-filesize'] = array(
			'id'        => 'validation-filesize',
			'name'      => __( 'File Size', 'wpforms' ),
			'type'      => 'text',
			'default'   => __( 'File exceeds max size allowed.', 'wpforms' ),
		);
		$settings['validation']['validation-time12h'] = array(
			'id'        => 'validation-time12h',
			'name'      => __( 'Time (12 hour)', 'wpforms' ),
			'type'      => 'text',
			'default'   => __( 'Please enter time in 12-hour AM/PM format (eg 8:45 AM).', 'wpforms' ),
		);
		$settings['validation']['validation-time24h'] = array(
			'id'        => 'validation-time24h',
			'name'      => __( 'Time (24 hour)', 'wpforms' ),
			'type'      => 'text',
			'default'   => __( 'Please enter time in 24-hour format (eg 22:45).', 'wpforms' ),
		);
		$settings['validation']['validation-requiredpayment'] = array(
			'id'        => 'validation-requiredpayment',
			'name'      => __( 'Payment Required', 'wpforms' ),
			'type'      => 'text',
			'default'   => __( 'Payment is required.', 'wpforms' ),
		);
		$settings['validation']['validation-creditcard'] = array(
			'id'        => 'validation-creditcard',
			'name'      => __( 'Credit Card', 'wpforms' ),
			'type'      => 'text',
			'default'   => __( 'Please enter a valid credit card number.', 'wpforms' ),
		);

		// Payment settings.
		$settings['payments']['payments-heading'] = array(
			'id'       => 'payments-heading',
			'content'  => '<h4>' . __( 'Payments', 'wpforms' ) . '</h4>',
			'type'     => 'content',
			'no_label' => true,
			'class'    => array( 'section-heading', 'no-desc' ),
		);
		$settings['payments']['payments-currency'] = array(
			'id'        => 'payments-currency',
			'name'      => __( 'Currency', 'wpforms' ),
			'type'      => 'select',
			'choicesjs' => true,
			'search'    => true,
			'default'   => 'USD',
			'options'   => $currency_option,
		);

		return $settings;
	}

	/**
	 * Saves entry to database.
	 *
	 * @since 1.2.1
	 * @param array $fields,
	 * @param array $entry_meta
	 * @param array $entry
	 * @param int $form_id
	 * @param array $form_data
	 */
	public function entry_save( $fields, $entry, $form_id, $form_data = '' ) {

		// Check if form has entries disabled
		if ( isset( $form_data['settings']['disable_entries'] ) ) {
			return;
		}

		// Provide the opportunity to override via a filter
		if ( ! apply_filters( 'wpforms_entry_save', true, $fields, $entry, $form_data ) ) {
			return;
		}

		$fields     = apply_filters( 'wpforms_entry_save_data', $fields, $entry, $form_data );
		$user_id    = is_user_logged_in() ? get_current_user_id() : 0;
		$user_ip    = wpforms_get_ip();
		$user_agent = !empty( $_SERVER['HTTP_USER_AGENT'] ) ? substr( $_SERVER['HTTP_USER_AGENT'], 0, 256 ) : '';
		$user_uuid  = !empty( $_COOKIE['_wpfuuid'] ) ? $_COOKIE['_wpfuuid'] : '';

		// Essential data
		$data = array(
			'form_id'    => absint( $form_id ),
			'user_id'    => absint( $user_id ),
			'fields'     => wp_json_encode( $fields ),
			'ip_address' => sanitize_text_field( $user_ip ),
			'user_agent' => sanitize_text_field( $user_agent ),
			'user_uuid'  => sanitize_text_field( $user_uuid ),
		);

		wpforms()->process->entry_id = wpforms()->entry->add( $data );
	}

	/**
	 * Add additional form settings to the General section.
	 *
	 * @since 1.2.1
	 * @param object $instance
	 */
	public function form_settings_general( $instance ) {

		// Don't provide this option if the user has configured payments.
		if ( ( ! empty( $instance->form_data['payments']['paypal_standard']['enable'] ) || ! empty( $instance->form_data['payments']['stripe']['enable'] ) ) && ! isset( $instance->form_data['settings']['disable_entries'] ) ) {
			return;
		}

		wpforms_panel_field(
			'checkbox',
			'settings',
			'disable_entries',
			$instance->form_data,
			__( 'Disable storing entry information in WordPress', 'wpforms' )
		);
	}

	/**
	 * Add entry counts column to form table.
	 *
	 * @since 1.2.1
	 * @param array $columns
	 * @return array
	 */
	public function form_table_columns( $columns ) {

		$columns['entries'] = __( 'Entries', 'wpforms' );

		return $columns;
	}

	/**
	 * Add entry counts value to entry count column.
	 *
	 * @since 1.2.1
	 * @param string $value
	 * @param object $form
	 * @param string $column_name
	 * @return string
	 */
	public function form_table_columns_value( $value, $form, $column_name ) {

		if ( 'entries' == $column_name ) {
			$count = wpforms()->entry->get_entries( array( 'form_id' => $form->ID ), true );
			$value = sprintf( '<a href="%s">%d</a>', add_query_arg( array( 'view' => 'list', 'form_id' => $form->ID ), admin_url( 'admin.php?page=wpforms-entries' ) ), $count );
		}

		return $value;
	}

	/**
	 * Form notification settings, supports multiple notifications.
	 *
	 * @since 1.2.3
	 * @param object $settings
	 */
	public function form_settings_notifications( $settings ) {

		$cc = wpforms_setting( 'email-carbon-copy', false );

		// Fetch next ID and handle backwards compatibility
		if ( !empty( $settings->form_data['settings']['notifications'] ) ) {
			$next_id = max( array_keys($settings->form_data['settings']['notifications'] ) ) + 1;
		} else {
			$next_id = 2;
			$settings->form_data['settings']['notifications'][1]['email']          = !empty( $settings->form_data['settings']['notification_email'] ) ? $settings->form_data['settings']['notification_email'] : '{admin_email}';
			$settings->form_data['settings']['notifications'][1]['subject']        = !empty( $settings->form_data['settings']['notification_subject'] ) ? $settings->form_data['settings']['notification_subject'] : sprintf( __( 'New %s Entry', 'wpforms ' ), $settings->form->post_title );
			$settings->form_data['settings']['notifications'][1]['sender_name']    = !empty( $settings->form_data['settings']['notification_fromname'] ) ? $settings->form_data['settings']['notification_fromname'] : get_bloginfo( 'name' );
			$settings->form_data['settings']['notifications'][1]['sender_address'] = !empty( $settings->form_data['settings']['notification_fromaddress'] ) ? $settings->form_data['settings']['notification_fromaddress'] : '{admin_email}';
			$settings->form_data['settings']['notifications'][1]['replyto']        = !empty( $settings->form_data['settings']['notification_replyto'] ) ? $settings->form_data['settings']['notification_replyto'] : '';
		}

		echo '<div class="wpforms-panel-content-section-title">';
			_e( 'Notifications', 'wpforms' );
			echo '<button class="wpforms-notifications-add" data-next-id="' . $next_id . '">' . __( 'Add New Notification', 'wpforms' ) . '</button>';
		echo '</div>';

		wpforms_panel_field(
			'select',
			'settings',
			'notification_enable',
			$settings->form_data,
			__( 'Notifications', 'wpforms' ),
			array(
				'default' => '1',
				'options' => array(
					'1' => __( 'On', 'wpforms' ),
					'0' => __( 'Off', 'wpforms' ),
				),
			)
		);

		foreach ( $settings->form_data['settings']['notifications'] as $id => $notification ) {

			$name = !empty( $notification['notification_name'] ) ? sanitize_text_field( $notification['notification_name'] ) : __( 'Default Notification', 'wpforms' );

			echo '<div class="wpforms-notification">';

				echo '<div class="wpforms-notification-header">';
					echo '<span>' . $name . '</span>';
					echo '<button class="wpforms-notification-delete"><i class="fa fa-times-circle"></i></button>';
					echo '<input type="hidden" name="settings[notifications][' . $id . '][notification_name]" value="' . esc_attr( $name ) . '">';
				echo '</div>';

				wpforms_panel_field(
					'text',
					'notifications',
					'email',
					$settings->form_data,
					__( 'Send To Email Address', 'wpforms' ),
					array(
						'default'    => '{admin_email}',
						'tooltip'    => __( 'Enter the email address to receive form entry notifications. For multiple notifications, separate email addresses with a comma.', 'wpforms' ),
						'smarttags'  => array(
							'type'   => 'fields',
							'fields' => 'email',
						),
						'parent'     => 'settings',
						'subsection' => $id,
						'class'      => 'email-recipient',
					)
				);
				if ( $cc ) :
				wpforms_panel_field(
					'text',
					'notifications',
					'carboncopy',
					$settings->form_data,
					__( 'CC', 'wpforms' ),
					array(
						'smarttags'  => array(
							'type'   => 'fields',
							'fields' => 'email',
						),
						'parent'     => 'settings',
						'subsection' => $id
					)
				);
				endif;
				wpforms_panel_field(
					'text',
					'notifications',
					'subject',
					$settings->form_data,
					__( 'Email Subject', 'wpforms' ),
					array(
						'default'    => __( 'New Entry: ' , 'wpforms' ) . $settings->form->post_title,
						'smarttags'  => array(
							'type'   => 'all'
						),
						'parent'     => 'settings',
						'subsection' => $id
					)
				);
				wpforms_panel_field(
					'text',
					'notifications',
					'sender_name',
					$settings->form_data,
					__( 'From Name', 'wpforms' ),
					array(
						'default'    => sanitize_text_field( get_option( 'blogname' ) ),
						'smarttags'  => array(
							'type'   => 'fields',
							'fields' => 'name,text',
						),
						'parent'     => 'settings',
						'subsection' => $id
					)
				);
				wpforms_panel_field(
					'text',
					'notifications',
					'sender_address',
					$settings->form_data,
					__( 'From Email', 'wpforms' ),
					array(
						'default'    => '{admin_email}',
						'smarttags'  => array(
							'type'   => 'fields',
							'fields' => 'email',
						),
						'parent'     => 'settings',
						'subsection' => $id
					)
				);
				wpforms_panel_field(
					'text',
					'notifications',
					'replyto',
					$settings->form_data,
					__( 'Reply-To', 'wpforms' ),
					array(
						'smarttags'  => array(
							'type'   => 'fields',
							'fields' => 'email',
						),
						'parent'     => 'settings',
						'subsection' => $id
					)
				);
				wpforms_panel_field(
					'textarea',
					'notifications',
					'message',
					$settings->form_data,
					__( 'Message', 'wpforms' ),
					array(
						'rows'       => 6,
						'default'    => '{all_fields}',
						'smarttags'  => array(
							'type'   => 'all'
						),
						'parent'     => 'settings',
						'subsection' => $id,
						'class'      => 'email-msg',
						'after'      => '<p class="note">' . __( 'To display all form fields, use the <code>{all_fields}</code> Smart Tag.', 'wpforms' ) . '</p>'
					)
				);

				// Conditional Logic, if addon is activated
				if ( function_exists( 'wpforms_conditional_logic' ) ) {
					wpforms_conditional_logic()->conditionals_block( array(
						'form'        => $settings->form_data,
						'type'        => 'panel',
						'panel'       => 'notifications',
						'parent'      => 'settings',
						'subsection'  => $id,
						'actions'     => array(
							'go'    => __( 'Send', 'wpforms' ),
							'stop'  => __( 'Don\'t send', 'wpforms' ),
						),
						'action_desc' => __( 'this notification if', 'wpforms' ),
						'reference'   => __( 'Email notifications', 'wpforms' ),
					) );
				} else {
					echo '<p class="note" style="padding:0 20px;">' . sprintf( __( 'Install the <a href="%s">Conditional Logic add-on</a> to enable conditional logic for Email Notifications.', 'wpforms' ), admin_url( 'admin.php?page=wpforms-addons' ) ) . '</p>';
				}

				// Hook for addons
				do_action( 'wpforms_form_settings_notifications_single_after', $settings, $id );

			echo '</div>';
		}
	}

	/**
	 * Append additional strings for form builder.
	 *
	 * @since 1.2.6
	 * @param array $strings
	 * @param object $form
	 * @return array
	 */
	public function form_builder_strings( $strings, $form ) {

		$currency   = wpforms_setting( 'currency', 'USD' );
		$currencies = wpforms_get_currencies();

		$strings['currency']            = sanitize_text_field( $currency );
		$strings['currency_name']       = sanitize_text_field( $currencies[$currency]['name'] );
		$strings['currency_decimal']    = sanitize_text_field( $currencies[$currency]['decimal_separator'] );
		$strings['currency_thousands']  = sanitize_text_field( $currencies[$currency]['thousands_separator'] );
		$strings['currency_symbol']     = sanitize_text_field( $currencies[$currency]['symbol'] );
		$strings['currency_symbol_pos'] = sanitize_text_field( $currencies[$currency]['symbol_pos'] );

		return $strings;
	}

	/**
	 * Check to see if the Conditional Logic addon is installed, if so notify
	 * the user that it can be removed.
	 *
	 * @since 1.3.8
	 */
	public function conditional_logic_addon_notice() {

		if ( file_exists( WP_PLUGIN_DIR . '/wpforms-conditional-logic/wpforms-conditional-logic.php' ) && ! defined( 'WPFORMS_DEBUG' ) ) {
			echo '<div class="notice notice-info"><p>';
			printf( __( 'Conditional logic functionality is now included in the core WPForms plugin! The WPForms Conditional Logic addon can be removed without affecting your forms. For more details <a href="%s" target="_blank" rel="noopener noreferrer">read our annoucement</a>.', 'wpforms' ), 'https://wpforms.com/' );
			echo '</p></div>';
		}
	}
}
new WPForms_Pro;

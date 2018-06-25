<?php

/**
 * Zapier integration.
 *
 * @since 1.0.0
 * @package WPFormsZapier
 */
class WPForms_Zapier extends WPForms_Provider {

	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->version  = WPFORMS_ZAPIER_VERSION;
		$this->name     = 'Zapier';
		$this->slug     = 'zapier';
		$this->priority = 60;
		$this->icon     = plugins_url( 'assets/images/addon-icon-zapier.png', __FILE__ );
		$this->type     = 'Zap';

		add_action( 'init', array( $this, 'zapier_callback' ) );
		add_filter( 'wpforms_providers_zapier_configured', array( $this, 'builder_sidebar_configured' ) );
	}

	/**
	 * Forms configured with Zapier do not have the connection information
	 * stored in the form_data, so the default indicator that shows if the form
	 * is configured will not work. Instead we filter the indicator and check
	 * the correct data location.
	 *
	 * @since 1.0.0
	 *
	 * @param string $configured
	 *
	 * @return string
	 */
	public function builder_sidebar_configured( $configured ) {

		if ( ! empty( $_GET['form_id'] ) ) {
			$zaps = get_post_meta( $_GET['form_id'], 'wpforms_zapier', true );
			if ( ! empty( $zaps ) ) {
				return 'configured';
			}
		}

		return '';
	}

	/**
	 * Build the entry field information to send to Zapier.
	 *
	 * @since 1.0.0
	 *
	 * @param array|int $form_data
	 * @param array|string $entry
	 * @param string|int $entry_id
	 *
	 * @return array
	 */
	public function format_fields( $form_data, $entry = '', $entry_id = '' ) {

		$data   = array();
		$fields = wpforms_get_form_fields( $form_data );

		if ( empty( $fields ) ) {
			return $data;
		}

		if ( ! empty( $entry_id ) ) {
			$data['id'] = absint( $entry_id );
		}

		foreach ( $fields as $field_id => $field ) {

			$label  = ! empty( $field['label'] ) ? sanitize_text_field( $field['label'] ) : sprintf( __( 'Field #%s', 'wpforms-zapier' ), $field_id );
			$extras = array();

			if ( ! empty( $entry[ $field_id ]['value'] ) ) {
				$value = wpforms_sanitize_textarea_field( $entry[ $field_id ]['value'] );
			} else {
				$value = '';
			}

			if ( in_array( $field['type'], array( 'checkbox' ), true ) ) {
				$value = implode( ', ', explode( "\n", trim( $entry[ $field_id ]['value'] ) ) );
			}

			if ( empty( $entry ) ) {
				$data[] = array(
					'key'   => 'field' . $field_id,
					'label' => $label,
					'type'  => 'unicode',
				);
			} else {
				$data[ 'field' . $field_id ] = $value;
			}

			// Add additional sub fields
			if ( 'name' === $field['type'] ) {

				$extras = array(
					'first'  => __( 'First', 'wpforms-zapier' ),
					'middle' => __( 'Middle', 'wpforms-zapier' ),
					'last'   => __( 'Last', 'wpforms-zapier' ),
				);

			} elseif ( 'checkbox' === $field['type'] ) {

				foreach ( $field['choices'] as $choice_id => $choice ) {
					$choice     = sanitize_text_field( $choice['label'] );
					$choice_val = ( strpos( $value, $choice ) !== false ) ? 'checked' : '';
					if ( empty( $entry ) ) {
						$data[] = array(
							'key'   => 'field' . $field_id . '_choice' . $choice_id,
							'label' => $choice,
							'type'  => 'unicode',
						);
					} else {
						$data[ 'field' . $field_id . '_choice' . $choice_id ] = $choice_val;
					}
				}
			} elseif ( 'address' === $field['type'] ) {

				$extras = array(
					'address1' => __( 'Line 1', 'wpforms-zapier' ),
					'address2' => __( 'Line 2', 'wpforms-zapier' ),
					'city'     => __( 'City', 'wpforms-zapier' ),
					'state'    => __( 'State', 'wpforms-zapier' ),
					'region'   => __( 'Region', 'wpforms-zapier' ),
					'postal'   => __( 'Postal', 'wpforms-zapier' ),
					'country'  => __( 'Country', 'wpforms-zapier' ),
				);

			} elseif ( 'date-time' === $field['type'] ) {

				$extras = array(
					'date' => __( 'Date', 'wpforms-zapier' ),
					'time' => __( 'Time', 'wpforms-zapier' ),
					'unix' => __( 'Unix Timestamp', 'wpforms-zapier' ),
				);

			} elseif ( in_array( $field['type'], array( 'payment-total', 'payment-single', 'payment-multiple', 'payment-select' ), true ) ) {

				// Decode for currency symbols
				if ( ! empty( $entry ) ) {
					$data[ 'field' . $field_id ] = html_entity_decode( $value );
				}

				// Send raw amount
				$extras = array(
					'amount_raw' => __( 'Plain Amount', 'wpforms-zapier' ),
				);
			}

			// Add extra fields
			if ( ! empty( $extras ) ) {
				foreach ( $extras as $extra_key => $extra ) {
					$extra_value = ! empty( $entry[ $field_id ][ $extra_key ] ) ? sanitize_text_field( $entry[ $field_id ][ $extra_key ] ) : '';
					$extra_label = sprintf( '%s (%s)', $label, $extra );
					if ( empty( $entry ) ) {
						$data[] = array(
							'key'   => 'field' . $field_id . '_' . $extra_key,
							'label' => $extra_label,
							'type'  => 'unicode',
						);
					} else {
						$data[ 'field' . $field_id . '_' . $extra_key ] = $extra_value;
					}
				}
			}
		}

		return $data;
	}

	/**
	 * Process and submit entry to provider.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields
	 * @param array $entry
	 * @param array $form_data
	 * @param string|int $entry_id
	 */
	public function process_entry( $fields, $entry, $form_data, $entry_id = 0 ) {

		// Only run if this form has connections for this provider and entry has fields
		$zaps = get_post_meta( $form_data['id'], 'wpforms_zapier', true );
		if ( empty( $zaps ) || empty( $fields ) ) {
			return;
		}

		$data = $this->format_fields( $form_data, $fields );

		// Fire for each Zap --------------------------------------//

		foreach ( $zaps as $zap_id => $zap ) :

			// Only process this Zap if it is enabled
			if ( empty( $zap['hook'] ) ) {
				continue;
			}

			$post_data = array(
				'ssl'       => true,
				'body'      => wp_json_encode( $data ),
			);
			$response  = wp_remote_post( $zap['hook'], $post_data );

			// Check for errors
			if ( is_wp_error( $response ) ) {
				wpforms_log(
					__( 'Zapier Zap error', 'wpforms-zapier' ),
					$post_data,
					array(
						'type'    => array( 'provider', 'error' ),
						'parent'  => $entry_id,
						'form_id' => $form_data['id'],
					)
				);
			}

		endforeach;
	}

	/**
	 * Returns WPForms Zapier API key.
	 *
	 * If one hasn't been generated yet then we create one and save it.
	 *
	 * @return string
	 */
	public function get_apikey() {

		$key = get_option( 'wpforms_zapier_apikey' );

		if ( empty( $key ) ) {

			$chars = array_merge( range( 0, 9 ), range( 'a', 'z' ) );
			$key   = '';
			for ( $i = 0; $i < 20; $i ++ ) {
				$key .= $chars[ mt_rand( 0, count( $chars ) - 1 ) ];
			}
			update_option( 'wpforms_zapier_apikey', $key );
		}

		return $key;
	}


	// ************************************************************************//
	//
	// API methods - these methods interact directly with the provider API.
	//
	// ************************************************************************//

	/**
	 * Callback to provide Zapier with specific information for forms and fields.
	 *
	 * @since 1.0.0
	 */
	public function zapier_callback() {

		$data = array();

		// WPForms Zapier API key is required
		if ( empty( $_GET['wpforms_zapier'] ) ) {
			return;
		}

		// Callback action is required
		if ( empty( $_GET['wpforms_action'] ) ) {
			return;
		}

		// Validate provided API Key
		$apikey = get_option( 'wpforms_zapier_apikey' );
		if ( empty( $apikey ) || trim( $_GET['wpforms_zapier'] ) !== $apikey ) {
			// Key is incorrect or missing
			nocache_headers();
			header( 'HTTP/1.1 401 Unauthorized' );
			echo wp_json_encode(
				array(
					'error' => __( 'Invalid WPForms Zapier API key', 'wpforms-zapier' ),
				)
			);
			exit;
		}

		// Provide available forms
		if ( 'forms' === $_GET['wpforms_action'] ) {

			$forms = wpforms()->form->get();

			if ( ! empty( $forms ) ) {

				foreach ( $forms as $form ) {
					$data['forms'][] = array(
						'id'   => $form->ID,
						'name' => wpforms_decode_string( sanitize_text_field( $form->post_title ) ),
					);
				}
			}
		}

		// Provide available fields from a recent form entry
		if ( 'entries' === $_GET['wpforms_action'] && ! empty( $_GET['wpforms_form'] ) ) {

			$entries = wpforms()->entry->get_entries(
				array(
					'form_id' => absint( $_GET['wpforms_form'] ),
				)
			);

			if ( ! empty( $entries ) ) {
				foreach ( $entries as $entry ) {
					$fields = json_decode( $entry->fields, true );
					$data[] = $this->format_fields( absint( $_GET['wpforms_form'] ), $fields, $entry->entry_id );
				}
			}
		}

		// Provide available fields
		if ( 'entry' === $_GET['wpforms_action'] && ! empty( $_GET['wpforms_form'] ) ) {

			$data = $this->format_fields( $_GET['wpforms_form'] );
		}

		// Subscribe/Add Zap
		if ( 'subscribe' === $_GET['wpforms_action'] ) {

			$form_id = absint( $_GET['wpforms_form'] );
			$hook    = ! empty( $_GET['hook_url'] ) ? esc_url_raw( $_GET['hook_url'] ) : '';
			$name    = ! empty( $_GET['zap_name'] ) ? sanitize_text_field( $_GET['zap_name'] ) : '';
			$link    = ! empty( $_GET['zap_link'] ) ? esc_url_raw( $_GET['zap_link'] ) : '';
			$live    = ! empty( $_GET['zap_live'] ) && strtolower( $_GET['zap_live'] ) === 'true' ? true : false;
			$id      = uniqid();

			$zaps = get_post_meta( $form_id, 'wpforms_zapier', true );

			if ( empty( $zaps ) ) {
				$zaps = array();
			}

			$zaps[ $id ] = array(
				'name' => $name,
				'hook' => $hook,
				'link' => $link,
				'live' => $live,
				'date' => time(),
			);

			update_post_meta( $form_id, 'wpforms_zapier', $zaps );

			$data = array(
				'status' => 'subscribed',
			);
		}

		// Unsubscribe/Delete Zap
		if ( 'unsubscribe' === $_GET['wpforms_action'] ) {

			$form_id = absint( $_GET['wpforms_form'] );
			$url     = ! empty( $_GET['hook_url'] ) ? esc_url_raw( $_GET['hook_url'] ) : '';

			$zaps = get_post_meta( $form_id, 'wpforms_zapier', true );

			if ( ! empty( $zaps ) ) {
				foreach ( $zaps as $zap_id => $zap ) {
					if ( $url === $zap['hook'] ) {
						unset( $zaps[ $zap_id ] );
					}
				}
				if ( empty( $zaps ) ) {
					delete_post_meta( $form_id, 'wpforms_zapier' );
				} else {
					update_post_meta( $form_id, 'wpforms_zapier', $zaps );
				}
			}

			$data = array(
				'status' => 'unsubscribed',
			);
		}


		// If data is empty something went wrong, so we stop
		if ( empty( $data ) ) {
			$data = array(
				'error' => __( 'No data', 'wpforms-zapier' ),
			);
		}

		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		echo wp_json_encode( $data );
		exit;
	}

	/**
	 * Post entry to Zapier webhook.
	 *
	 * @since 1.0.0
	 */
	public function zapier_post() {
	}


	// ************************************************************************//
	//
	// Builder methods - these methods _build_ the Builder.
	//
	// ************************************************************************//

	/**
	 * Custom Zapier builder content.
	 *
	 * @since 1.0.0
	 */
	public function builder_output() {

		$zaps = get_post_meta( absint( $_GET['form_id'] ), 'wpforms_zapier', true );
		?>
		<div class="wpforms-panel-content-section wpforms-panel-content-section-<?php echo esc_attr( $this->slug ); ?>" id="<?php echo esc_attr( $this->slug ); ?>-provider">

			<div class="wpforms-panel-content-section-title">

				<?php echo esc_html( $this->name ); ?>

			</div>

			<div class="wpforms-provider-connections-wrap wpforms-clear">

				<div class="wpforms-provider-connections">

					<p><?php printf( __( 'Your WPForms Zapier API key is %s', 'wpforms-zapier' ), '<code>' . $this->get_apikey() . '</code>' ); ?></p>

					<?php
					if ( empty( $zaps ) ) {
						echo '<p>' . __( 'No Zaps are connected to this form.', 'wpforms-zapier' ) . '</p>';
					} else {

						foreach ( $zaps as $zap_id => $zap ) {
							echo '<div class="wpforms-provider-connection">';
							$name = ! empty( $zap['name'] ) ? sanitize_text_field( $zap['name'] ) : __( 'No name', 'wpforms-zapier' );
							$live = $zap['live'] ? __( 'Yes', 'wpforms-zapier' ) : __( 'No', 'wpforms-zapier' );
							echo '<div class="wpforms-provider-connection-header"><span>' . $name . '</span></div>';
							echo '<div style="padding:0 20px;">';
							echo '<p><strong>' . __( 'Date Connected', 'wpforms-zapier' ) . '</strong><br>&nbsp;' . date_i18n( get_option( 'date_format', $zap['date'] ) ) . '</p>';
							echo '<p><strong>' . __( 'Live', 'wpforms-zapier' ) . '</strong><br>&nbsp;' . $live . '</p>';
							echo '<p><a href="' . esc_url( urldecode( $zap['link'] ) ) . '" target="_blank" rel="noopener">' . __( 'Edit this Zap', 'wpforms-zapier' ) . '</a></p>';
							echo '</div>';
							echo '</div>';
						}
					}

					printf( __( '<a href="%s" target="_blank" rel="noopener">Click here for documentation on connecting WPForms with Zapier.</a>', 'wpforms-zapier' ), 'https://wpforms.com/docs/how-to-install-and-use-zapier-addon-in-wpforms/' );
					?>

				</div>

			</div>

		</div>
		<?php
	}


	// ************************************************************************//
	//
	// Integrations tab methods - these methods relate to the settings page.
	//
	// ************************************************************************//

	/**
	 * Add custom Zapier panel to the Settings Integrations tab.
	 *
	 * @since 1.0.0
	 *
	 * @param array $active
	 * @param array $settings
	 */
	public function integrations_tab_options( $active, $settings ) {

		$args = array(
			'posts_per_page' => 999,
			'post_type'      => 'wpforms',
			'meta_query'     => array(
				array(
					'key'     => 'wpforms_zapier',
					'compare' => 'EXISTS',
				),
			),
		);
		$forms     = get_posts( $args );
		$slug      = esc_html( $this->slug );
		$name      = esc_html( $this->name );
		$connected = ! empty( $forms );
		$class     = $connected ? 'connected' : '';
		$arrow     = 'right';

		// This lets us highlight a specific service by a special link
		if ( ! empty( $_GET['wpforms-integration'] ) ) {
			if ( $this->slug === $_GET['wpforms-integration'] ) {
				$class .= ' focus-in';
				$arrow = 'down';
			} else {
				$class .= ' focus-out';
			}
		}
		?>

		<div id="wpforms-integration-<?php echo $slug; ?>" class="wpforms-settings-provider wpforms-clear <?php echo $slug; ?> <?php echo $class; ?>">

			<div class="wpforms-settings-provider-header wpforms-clear" data-provider="<?php echo $slug; ?>">

				<div class="wpforms-settings-provider-logo">
					<i title="Show Accounts" class="fa fa-chevron-<?php echo $arrow; ?>"></i>
					<img src="<?php echo $this->icon; ?>">
				</div>

				<div class="wpforms-settings-provider-info">
					<h3><?php echo $name; ?></h3>
					<p><?php printf( __( 'Integrate %s with WPForms', 'wpforms-zapier' ), $name ); ?></p>
					<span class="connected-indicator green"><i class="fa fa-check-circle-o"></i>&nbsp;<?php _e( 'Connected', 'wpforms-zapier' ); ?></span>
				</div>

			</div>

			<div class="wpforms-settings-provider-accounts" id="provider-<?php echo $slug; ?>">

				<p>
					<?php printf( __( 'Your WPForms Zapier API key is %s', 'wpforms-zapier' ), '<code>' . $this->get_apikey() . '</code>' ); ?>
				</p>

				<?php
				if ( empty( $forms ) ) {
					echo '<p>' . __( 'No forms are currently connected.', 'wpforms-zapier' ) . '</p>';
					echo '<p>' . sprintf( __( '<a href="%s" target="_blank">Click here for documentation on connecting WPForms with Zapier.</a>', 'wpforms-zapier' ), 'https://wpforms.com/docs/how-to-install-and-use-zapier-addon-in-wpforms/' ) . '</p>';

				} else {
					echo '<p>' . __( 'The forms below are currently connected to Zapier. ', 'wpforms-zapier' ) . '</p>';
					echo '<div class="wpforms-settings-provider-accounts-list">';
						echo '<ul>';
						foreach ( $forms as $form ) {
							echo '<li class="wpforms-clear">';
								echo '<span class="label">' . esc_html( $form->post_title ) . '</span>';
								echo '<span class="date">' . sprintf( _x( 'Connected on: %s', 'Connection date', 'wpforms-zapier' ), date_i18n( get_option( 'date_format', $form->post_date ) ) ) . '</span>';
							echo '</li>';
						}
						echo '</ul>';
					echo '</div>';
				}
				?>

			</div>

		</div>
		<?php
	}
}

new WPForms_Zapier;

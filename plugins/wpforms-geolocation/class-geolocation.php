<?php
/**
 * Geolocation.
 *
 * @since 1.0.0
 * @package WPFormsGeolocation
 */
class WPForms_Geolocation {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->init();
	}

	/**
	 * Initialize.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		add_action( 'wpforms_process_entry_save',    array( $this, 'entry_save_location'    ), 20, 4 );
		add_action( 'wpforms_email_message',         array( $this, 'entry_location_smarttag'), 10, 2 );
		add_action( 'wpforms_entry_details_init',    array( $this, 'entry_details_init'     ), 10, 1 );
		add_action( 'wpforms_entry_details_content', array( $this, 'entry_details_location' ), 20, 2 );
	}

	/**
	 * Maybe fetch geolocation data.
	 * 
	 * If a form is using the location smart tag in an email notification, then
	 * we need to process the geolocation data before emails are sent. Otherwise
	 * geolocation data is processed on-demand when viewing an individual entry.
	 *
	 * @param array $fields
	 * @param array $entry
	 * @param int $form_id
	 * @param array$form_data
	 */
	public function entry_save_location( $fields, $entry, $form_id, $form_data ) {

		if ( empty( $form_data['settings']['notifications'] ) )
			return;

		foreach( $form_data['settings']['notifications'] as $notification ) {

			if ( empty( wpforms()->process->entry_id ) )
				return;
				
			if ( strpos( $notification['message'], '{entry_geolocation}' ) !== false ) {

				$ip  = wpforms_get_ip();
				$loc = $this->get_location( $ip );

				if ( $loc ) {
					$data = array(
						'entry_id' => absint( wpforms()->process->entry_id ),
						'form_id'  => absint( $form_id ),
						'type'     => 'location',
						'data'     => json_encode( $loc ),
					);
					wpforms()->entry_meta->add( $data, 'entry_meta' );
				}

				return;
			}
		}
	}

	/**
	 * Checks for {entry_geolocation} Smart Tag inside email messages.
	 *
	 * @since 1.0.0
	 * @param string $message
	 * @param object $email
	 * @return string
	 */
	public function entry_location_smarttag( $message, $email ) {

		// Check to see if SmartTag is in email notification message
		if ( strpos( $message, '{entry_geolocation}' ) !== false ) {

			// Attempt to fetch location data, which should be in the database
			$location = wpforms()->entry_meta->get_meta( array( 'entry_id' => $email->entry_id, 'type' => 'location', 'number' => 1 ) );

			if ( empty( $location ) )
				return $message;

			$location = json_decode( $location[0]->data, true );

			if ( 'text/plain' == $email->get_content_type() ) {

				$geo  = "--- " . __( 'Entry Geolocation', 'wpforms_geolocation' )  . " ---\r\n";
				$geo .= $location['city'] . ', ' . $location['region'] . ', ' . $location['country'] . "\r\n";
				$geo .= $location['latitude'] . ', ' . $location['longitude'] . "\r\n\r\n";

			} else {

				$map = add_query_arg( 
					array(
						'q'      => $location['city'] . ',' . $location['region'],
						'll'     => $location['latitude'] . ',' . $location['longitude'],
						'z'      => apply_filters( 'wpforms_geolocation_map_zoom', '6' ),
					), 
					'https://maps.google.com/maps'
				);
				$img = add_query_arg( 
					array(
						'center'  => $location['city'] . ',' . $location['region'],
						'll'      => $location['latitude'] . ',' . $location['longitude'],
						'size'    => '300x100',
						'maptype' => 'roadmap',
						'zoom'    => apply_filters( 'wpforms_geolocation_map_zoom', '6' ),
						'markers' => 'color:red%7C' . $location['latitude'] . ',' . $location['longitude'],
					),
					'https://maps.googleapis.com/maps/api/staticmap'
				);

				ob_start();
				$email->get_template_part( 'field', $email->get_template(), true );
				$geo    = ob_get_clean();
				$geo    = str_replace( 'border-top:1px solid #dddddd;', '', $geo );
				$geo    = str_replace( '{field_name}', __( 'Entry Geolocation', 'wpforms_geolocation' ), $geo );
				$value  = $location['city'] . ', ' . $location['region'] . ', ' . $location['country'] . "<br>";
				$value .= $location['latitude'] . ', ' . $location['longitude'];
				if ( apply_filters( 'wpforms_geolocation_map_email', true ) ) {
					$value .= '<br><a href="' . esc_url( $map ) . '"><img src="' . esc_url( $img ) . '"></a>';
				}
				$geo    = str_replace( '{field_value}', $value, $geo );
			}

			$message = str_replace( '{entry_geolocation}', $geo, $message );
		}
		
		return $message;
	}

	/**
	 * Maybe process geolocation data when an individual entry is viewed.
	 *
	 * @since 1.0.0
	 * @param object $entries
	 */
	public function entry_details_init( $entries ) {

		$location = wpforms()->entry_meta->get_meta( array( 'entry_id' => $entries->entry->entry_id, 'type' => 'location', 'number' => 1 ) );

		if ( empty( $location ) ) {

			$location = $this->get_location( $entries->entry->ip_address );

			if ( $location ) {
				$data = array(
					'entry_id' => absint( $entries->entry->entry_id ),
					'form_id'  => absint( $entries->entry->form_id ),
					'type'     => 'location',
					'data'     => json_encode( $location ),
				);
				wpforms()->entry_meta->add( $data, 'entry_meta' );
			}
		} else {
			$location = json_decode( $location[0]->data, true );
		}

		$entries->entry->entry_location = $location;
	}

	/**
	 * Entry details location metabox, display the info and make it look fancy.
	 *
	 * @since 1.0.0
	 * @param object $entry
	 * @param array $form_data
	 */
	public function entry_details_location( $entry, $form_data ) {

		echo '<!-- Entry Location metabox -->';
		echo '<div id="wpforms-entry-geolocation" class="postbox">';
		
			echo '<h2 class="hndle"><span>' . __( 'Location', 'wpforms_geolocation' ) . '</span></h2>';

			echo '<div class="inside">';

				if ( empty( $entry->entry_location ) ) :

					echo '<p style="padding:0 10px 10px;">' . __( 'Unable to load location data for this entry. This usually means WPForms was unable to process the user\'s IP address or it is non-standard format.', 'wpforms_geolocation' ) . '</p>';

				else:

					array_map( 'sanitize_text_field', $entry->entry_location );
	
					$map = add_query_arg( 
						array(
							'q'      => $entry->entry_location['city'] . ',' . $entry->entry_location['region'],
							'll'     => $entry->entry_location['latitude'] . ',' . $entry->entry_location['longitude'],
							'z'      => apply_filters( 'wpforms_geolocation_map_zoom', '6' ),
							'output' => 'embed',
						), 
						'https://maps.google.com/maps'
					);
					echo '<iframe frameborder="0" src="' . esc_url( $map ) . '" style="width:100%;height:320px;"></iframe>';

					echo '<ul>';

						// General location
						echo '<li>';
							echo '<span class="wpforms-geolocation-meta">' . __( 'Location', 'wpforms_geolocation' ) . '</span>';
							echo '<span class="wpforms-geolocation-value"> '. $entry->entry_location['city'] . ', ' . $entry->entry_location['region'] . '</span>';
						echo '</li>';

						// Zipcode/postal
						echo '<li>';
							if ( 'US' == $entry->entry_location['country'] ) {
								echo '<span class="wpforms-geolocation-meta">' . __( 'Zipcode', 'wpforms_geolocation' ) . '</span>';
							} else {
								echo '<span class="wpforms-geolocation-meta">' . __( 'Postal', 'wpforms_geolocation' ) . '</span>';
							}
							echo '<span class="wpforms-geolocation-value">' . $entry->entry_location['postal'] . '</span>';
						echo '</li>';

						// Country
						echo '<li>';
							echo '<span class="wpforms-geolocation-meta">' . __( 'Country', 'wpforms_geolocation' ) . '</span>';
							echo '<span class="wpforms-geolocation-value"><span class="wpforms-flag wpforms-flag-' . strtolower( $entry->entry_location['country'] ) . '"></span>' .  $entry->entry_location['country'] . '</span>';
						echo '</li>';

						// Lat/long
						echo '<li>';
							echo '<span class="wpforms-geolocation-meta">' . __( 'Lat/Long', 'wpforms_geolocation' ) . '</span>';
							echo '<span class="wpforms-geolocation-value">' . $entry->entry_location['latitude'] . ', ' . $entry->entry_location['longitude'] . '</span>';
						echo '</li>';

					echo '</ul>';
			
				endif;
			
			echo '</div>';

		echo '</div>';
	}

	/**
	 * Get geolocation information from an IP address.
	 *
	 * @since 1.0.0
	 * @param string $ip
	 * @return mixed false or array
	 */
	public function get_location( $ip = '' ) {

		// Check for a non-local IP
		if ( empty( $ip ) || in_array( $ip, array( '127.0.0.1', '::1' ) ) ) {
			return false;
		}

		// Try http://ipinfo.io ----------------------------------------------//

		$request = wp_remote_get( 'http://ipinfo.io/' . $ip . '/json' );

		if ( !is_wp_error( $request ) ) {

			$request = json_decode( wp_remote_retrieve_body( $request ), true );

			$latlog = explode( ',', $request['loc'] );
			$request['latitude']  = $latlog['0'];
			$request['longitude'] = $latlog['1'];

			unset( $request['hostname'] );
			unset( $request['org'] );
			unset( $request['loc'] );

			return array_map( 'sanitize_text_field', $request );
		}

		// Try https://ipapi.co ----------------------------------------------//
	
		$request = wp_remote_get( 'https://ipapi.co/' . $ip . '/json', array( 'sslverify' => false ) );

		if ( !is_wp_error( $request ) ) {

			$request = json_decode( wp_remote_retrieve_body( $request ), true );

			return array_map( 'sanitize_text_field', $request );
		}

		// Try https://freegeoip.net -----------------------------------------//

		$request = wp_remote_get( 'https://freegeoip.net/json/' . $ip, array( 'sslverify' => false ) );

		if ( !is_wp_error( $request ) ) {

			$request = json_decode( wp_remote_retrieve_body( $request ), true );

			// Reformat to what we're expecting
			$request['country']   = $request['country_code'];
			$request['region']    = $request['region_name'];
			$request['timezone']  = $request['time_zone'];
			$request['postal']    = $request['zip_code'];

			foreach ( $request as $key => $item ) {
				if ( in_array( $key, array( 'metro_code', 'region_code', 'country_name', 'country_code', 'region_name', 'time_zone' ) ) ) {
					unset( $request[$key] );
				}
			}

			return array_map( 'sanitize_text_field', $request );
		}

		return false;
	}
}
new WPForms_Geolocation;
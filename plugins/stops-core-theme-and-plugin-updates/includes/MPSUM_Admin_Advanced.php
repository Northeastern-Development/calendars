<?php
if ( !defined('ABSPATH') ) die( 'No direct access.' );
/**
 * Controls the advanced tab and handles the saving of its options.
 *
 * @package WordPress
 * @since 5.0.0
 */
class MPSUM_Admin_Advanced {
	/**
     * Holds the slug to the admin panel page
     *
     * @since 5.0.0
     * @access private
     * @var string $slug
     */
	private $slug = '';

	/**
     * Holds the tab name
     *
     * @since 5.0.0
     * @access private
     * @var string $tab
     */
	private $tab = 'advanced';

	/**
     * Class constructor.
     *
     * Initialize the class
     *
     * @since 5.0.0
     * @access public
     *
     * @param string $slug Slug to the admin panel page
     */
	public function __construct( $slug = '' ) {
		$this->slug = $slug;
		// Admin Tab Actions
		add_action( 'mpsum_admin_tab_advanced', array( $this, 'tab_output' ) );
	}

	/**
     * Output the HTML interface for the advanced tab.
     *
     * Output the HTML interface for the advanced tab.
     *
     * @since 5.0.0
     * @access public
     * @see __construct
     * @internal Uses the mpsum_admin_tab_main action
     */
	public function tab_output() {
			?>
        <div id="result"></div>
		<h3><?php esc_html_e( 'Exclude Users', 'stops-core-theme-and-plugin-updates' ); ?></h3>
		<p><?php esc_html_e( 'Select which users to be excluded from the settings of this plugin. Default WordPress behavior will be used.', 'stops-core-theme-and-plugin-updates' ); ?></p>
		<p><?php esc_html_e( 'This option is useful if, for example, you would like to disable updates, but have a user account that can still update WordPress.', 'stops-core-theme-and-plugin-updates' ); ?></p>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Users to be Excluded', 'stops-core-theme-and-plugin-updates' ); ?></th>
				<td>
					<?php
						// Code from wp-admin/includes/class-wp-ms-users-list-table
						$users = array();
						if ( is_multisite() ) {
							global $wpdb;
							$logins = implode( "', '", get_super_admins() );
							$users = $wpdb->get_col( "SELECT ID FROM $wpdb->users WHERE user_login IN ('$logins') GROUP BY user_login" );

						} else {
							/**
							* Determine which role gets queried for admin users.
							*
							* Determine which role gets queried for admin users.
							*
							* @since 5.0.0
							*
							* @param string	$var administrator.
							*/
							$role = apply_filters( 'mpsum_admin_role', 'administrator' );
							$users = get_users( array( 'role' => $role, 'orderby' => 'display_name', 'order' => 'ASC', 'fields' => 'ID' ) );
						}
						if ( is_array( $users ) && !empty( $users ) ) {
							echo '<input type="hidden" value="0" name="mpsum_excluded_users[]" />';
							$excluded_users = MPSUM_Updates_Manager::get_options( 'excluded_users' );
							foreach( $users as $index => $user_id ) {
								$user = get_userdata( $user_id );
								$disabled = get_current_user_id() === absint($user_id) ? 'disabled="true"' : '';
								printf( '<input type="checkbox" name="mpsum_excluded_users[]" id="mpsum_user_%1$d" value="%1$d" %3$s %4$s />&nbsp;<label for="mpsum_user_%1$d">%2$s</label><br />', esc_attr( $user_id ), esc_html( $user->display_name ), checked( true, in_array( $user_id, $excluded_users ), false ), $disabled );
							}
						}
						?>
				</td>
			</tr>
		</table>
		<?php submit_button( __( 'Save Users', 'stops-core-theme-and-plugin-updates' ) , 'primary', 'submit', true, array('id' => 'save-excluded-users') ); ?>
		<?php do_action( 'eum-advanced' ); ?>
		<h3><?php esc_html_e( 'Force Automatic Updates', 'stops-core-theme-and-plugin-updates' ); ?></h3>
		<?php
		if ( defined( 'AUTOMATIC_UPDATER_DISABLED' ) && true == AUTOMATIC_UPDATER_DISABLED ) {
			?>
			<div class="mpsum-error"><p><strong><?php esc_html_e( 'Automatic updates are disabled. Please check your wp-config.php file for AUTOMATIC_UPDATER_DISABLED and remove the line.' ); ?> </strong></p></div>
			<?php
		}
		if ( defined( 'WP_AUTO_UPDATE_CORE' ) && false == WP_AUTO_UPDATE_CORE ) {
			?>
			<div class="mpsum-error"><p><strong><?php esc_html_e( 'Automatic updates for Core are disabled. Please check your wp-config.php file for WP_AUTO_UPDATE_CORE and remove the line.' ); ?> </strong></p></div>
			<?php
		}
		?>
		<p><?php esc_html_e( 'This will attempt to force automatic updates. This is useful for debugging.', 'stops-core-theme-and-plugin-updates' ); ?></p>
		<?php submit_button( __( 'Force Updates', 'stops-core-theme-and-plugin-updates' ) , 'primary', 'submit', true, array('id' => 'force-updates') ); ?>
 		<h3><?php esc_html_e( 'Reset Options', 'stops-core-theme-and-plugin-updates' ); ?></h3>
 		<p><?php esc_html_e( 'This will reset all options to as if you have just installed the plugin. WARNING!: This also disables and clears the logs.', 'stops-core-theme-and-plugin-updates' ); ?></p>
        <?php submit_button( __( 'Reset All Options', 'stops-core-theme-and-plugin-updates' ) , 'primary', 'submit', true, array('id' => 'reset-options') ); ?>
		<?php
	} //end tab_output
}

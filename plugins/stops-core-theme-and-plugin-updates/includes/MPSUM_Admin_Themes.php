<?php
if ( !defined( 'ABSPATH' ) ) die( 'No direct access.' );
/**
 * Controls the themes tab and handles the saving of its options.
 *
 * @package WordPress
 * @since 5.0.0
 */
class MPSUM_Admin_Themes {

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
	private $tab = 'themes';

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
		add_action( 'mpsum_admin_tab_themes', array( $this, 'tab_output' ) );
		add_filter( 'mpsum_theme_action_links', array( $this, 'theme_action_links' ), 11, 2 );
	}

	/**
     * Determine whether the themes can be updated or not.
     *
     * Determine whether the themes can be updated or not.
     *
     * @since 5.0.0
     * @access private
     *
     * @return bool True if the themes can be updated, false if not.
     */
	private function can_update() {
		$core_options = MPSUM_Updates_Manager::get_options( 'core' );
		if ( isset( $core_options[ 'all_updates' ] ) && 'off' == $core_options[ 'all_updates' ] ) {
			return false;
		}
		if ( isset( $core_options[ 'theme_updates' ] ) && 'off' == $core_options[ 'theme_updates' ] ) {
			return false;
		}
		return true;
	}

	/**
	 * Output the HTML interface for the themes tab.
	 *
	 * Output the HTML interface for the themes tab.
	 *
	 * @since 5.0.0
	 * @access public
	 * @see __construct
	 * @internal Uses the mpsum_admin_tab_themes action
	 */
	public function tab_output() {
		?>
        <form action="<?php echo esc_url( add_query_arg( array() ) ); ?>" method="post">
	    <?php
		$theme_status = isset( $_GET[ 'theme_status' ] ) ? $_GET[ 'theme_status' ] : false;
		if ( false !== $theme_status ) {
			printf( '<input type="hidden" name="theme_status" value="%s" />', esc_attr( $theme_status ) );
		}
		?>
        <h3><?php esc_html_e( 'Theme Update Options', 'stops-core-theme-and-plugin-updates' ); ?></h3>
        <?php
		$core_options = MPSUM_Updates_Manager::get_options( 'core' );

		if ( false === $this->can_update() ) {
			printf( '<div class="error"><p><strong>%s</strong></p></div>', esc_html__( 'All theme updates have been disabled.', 'stops-core-theme-and-plugin-updates' ) );
		}
        printf( '<div id="eum-save-settings-warning" class="warning"><p>%s</p></div>', esc_html__( 'Remember to save your settings', 'stops-core-theme-and-plugin-updates') );
		$theme_table = new MPSUM_Themes_List_Table( $args = array( 'screen' => $this->slug, 'tab' => $this->tab ) );
		$theme_table->prepare_items();
		$theme_table->views();
		$theme_table->display();
        submit_button('Save','primary','submit', true, array('id' => 'eum-save-settings'));
		?>
        </form>
    <?php
	} //end tab_output_plugins

	/**
	 * Outputs the theme action links beneath each theme row.
	 *
	 * Outputs the theme action links beneath each theme row.
	 *
	 * @since 5.0.0
	 * @access public
	 * @see __construct
	 * @internal uses mpsum_theme_action_links filter
	 *
	 * @param array    $settings Array of settings to output.
	 * @param WP_Theme $theme    The theme object to take action on.
     *
     * @return array Array of settings to output
	 */
	public function theme_action_links( $settings, $theme ) {
		return $settings;
	}
}

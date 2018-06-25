<?php
/**
 * Display list of all entries for a single form.
 *
 * @package    WPForms
 * @author     WPForms
 * @since      1.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WPForms LLC
*/
class WPForms_Entries_List {

	/**
	 * Holds admin alerts.
	 *
	 * @since 1.1.6
	 * @var array
	 */
	public $alerts = array();

	/**
	 * Abort. Bail on proceeding to process the page.
	 *
	 * @since 1.1.6
	 * @var bool
	 */
	public $abort = false;

	/**
	 * Form ID.
	 *
	 * @since 1.1.6
	 * @var int
	 */
	public $form_id;

	/**
	 * Form object.
	 *
	 * @since 1.1.6
	 * @var object
	 */
	public $form;

	/**
	 * Forms object.
	 *
	 * @since 1.1.6
	 * @var object
	 */
	public $forms;

	/**
	 * Entries object.
	 *
	 * @since 1.1.6
	 * @var object
	 */
	public $entries;

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Maybe load entries page
		add_action( 'admin_init', array( $this, 'init' ) );

		// Setup screen options - this needs to run early.
		add_action( 'load-wpforms_page_wpforms-entries', array( $this, 'screen_options' ) );
		add_filter( 'set-screen-option', array( $this, 'screen_options_set' ), 10, 3 );
	}

	/**
	 * Determing if the user is viewing the entries list page, if so, party on.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// Check what page and view.
		$page = !empty( $_GET['page'] ) ? $_GET['page'] : '';
		$view = !empty( $_GET['view'] ) ? $_GET['view'] : 'list';

		// Only load if we are actually on the overview page
		if ( 'wpforms-entries' === $page && 'list' === $view ) {

			// Enqueues.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueues' ) );

			// Load the classes that builds the entries table.
			if ( ! class_exists( 'WP_List_Table' ) ) {
				require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
			}
			require_once WPFORMS_PLUGIN_DIR . 'pro/includes/admin/entries/class-entries-list-table.php';

			// Preview page check.
			wpforms()->preview->form_preview_check();

			// Processing and setup.
			add_action( 'wpforms_entries_init', array( $this, 'process_export'),  8, 1 );
			add_action( 'wpforms_entries_init', array( $this, 'process_read'  ),  8, 1 );
			add_action( 'wpforms_entries_init', array( $this, 'setup'         ), 10, 1 );

			// Output.
			add_action( 'wpforms_admin_page',       array( $this, 'list_all'          )        );
			add_action( 'wpforms_entry_list_title', array( $this, 'list_form_actions' ), 10, 1 );

			// Provide hook for add-ons.
			do_action( 'wpforms_entries_init', 'list' );
		}
	}

	/**
	 * Add per-page screen option to the Entries table.
	 *
	 * @since 1.0.0
	 */
	public function screen_options() {

		$screen = get_current_screen();

		if ( 'wpforms_page_wpforms-entries' !== $screen->id ) {
			return;
		}

		add_screen_option(
			'per_page',
			array(
				'label'   => __( 'Number of entries per page:', 'wpforms' ),
				'option'  => 'wpforms_entries_per_page',
				'default' => apply_filters( 'wpforms_entries_per_page', 30 ),
			)
		);
	}

	/**
	 * Entries table per-page screen option value
	 *
	 * @since 1.0.0
	 * @param mixed $status
	 * @param string $option
	 * @param mixed $value
	 * @return mixed
	 */
	public function screen_options_set( $status, $option, $value ) {

		if ( 'wpforms_entries_per_page' === $option ) {
			return $value;
		}

		return $status;
	}

	/**
	 * Enqueue assets for the entries pages.
	 *
	 * @since 1.0.0
	 */
	public function enqueues() {

		// Hook for add-ons
		do_action( 'wpforms_entries_enqueue', 'list' );
	}

	/**
	 * Watches for and runs complete form exports.
	 *
	 * @since 1.1.6
	 */
	public function process_export() {

		// Check for run switch.
		if ( empty( $_GET['export'] ) || empty( $_GET['form_id'] ) || 'all' !== $_GET['export'] ) {
			return;
		}

		// Security check.
		if ( empty( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'wpforms_entry_list_export' ) ) {
			return;
		}

		require_once WPFORMS_PLUGIN_DIR . 'pro/includes/admin/entries/class-entries-export.php';

		$export = new WPForms_Entries_Export();
		$export->entry_type = 'all';
		$export->form_id    =  absint( $_GET['form_id'] );
		$export->export();
	}

	/**
	 * Watches for and runs complete marking all entries as read.
	 *
	 * @since 1.1.6
	 */
	public function process_read() {

		// Check for run switch.
		if ( empty( $_GET['action'] ) || empty( $_GET['form_id'] ) || 'markread' !== $_GET['action'] ) {
			return;
		}

		// Security check.
		if ( empty( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'wpforms_entry_list_markread' ) ) {
			return;
		}

		wpforms()->entry->mark_all_read( $_GET['form_id'] );

		$this->alerts[] = array(
			'type'    => 'success',
			'message' => __( 'All entries marked as read.', 'wpforms' ),
			'dismiss' => true,
		);
	}

	/**
	 * Setup entry list page data.
	 *
	 * This function does the error checking and variable setup.
	 *
	 * @since 1.1.6
	 */
	public function setup() {

		// Fetch all forms
		$this->forms = wpforms()->form->get( '',  array( 'orderby' => 'ID', 'order' => 'ASC' ) );

		// Check that that user has created at least one form
		if ( empty( $this->forms ) ) {

			$this->alerts[] = array(
				'type'    => 'info',
				'message' =>  sprintf( __( 'Whoops, you haven\'t created a form yet. Want to <a href="%s">give it a go</a>?', 'wpforms' ), admin_url( 'admin.php?page=wpforms-builder' ) ),
				'abort'   =>  true,
			);

		} else {
			$this->form_id = ! empty( $_GET['form_id'] ) ? absint( $_GET['form_id'] ) : apply_filters( 'wpforms_entry_list_default_form_id', absint( $this->forms[0]->ID ) );
			$this->form    = wpforms()->form->get( $this->form_id );
		}
	}

	/**
	 * List all entries in a specific form.
	 *
	 * @since 1.0.0
	 */
	public function list_all() {

		$form_data = ! empty( $this->form->post_content ) ? wpforms_decode( $this->form->post_content ) : '';
		?>
		<div id="wpforms-entries-list" class="wrap wpforms-admin-wrap">

			<h1 class="page-title"><?php _e( 'Entries', 'wpforms' ); ?></h1>

			<?php
			// Admin notices
			$this->display_alerts();
			if ( $this->abort ) {
				echo '</div>'; // close wrap
				return;
			}

			$this->entries = new WPForms_Entries_Table;
			$this->entries->form_id   = $this->form_id;
			$this->entries->form_data = $form_data;
			$this->entries->prepare_items();
			?>
			<div class="wpforms-admin-content">

				<?php do_action( 'wpforms_entry_list_title', $form_data, $this ); ?>

				<form id="wpforms-entries-table" method="get" action="<?php echo admin_url( 'admin.php?page=wpforms-entries' ); ?>">

						<input type="hidden" name="page" value="wpforms-entries" />
						<input type="hidden" name="view" value="list" />
						<input type="hidden" name="form_id" value="<?php echo $this->form_id; ?>" />

						<?php $this->entries->views(); ?>
						<?php $this->entries->display(); ?>

				</form>

			</div>

		</div>
		<?php
	}

	/**
	 * Entry list form actions.
	 *
	 * @since 1.1.6
	 */
	public function list_form_actions( $form_data ) {

		$base = add_query_arg(
			array(
				'page'    => 'wpforms-entries',
				'view'    => 'list',
				'form_id' => absint( $this->form_id ),
			),
			admin_url( 'admin.php' )
		);

		// Edit Form URL
		$edit_url = add_query_arg(
			array(
				'page'    => 'wpforms-builder',
				'view'    => 'fields',
				'form_id' => absint( $this->form_id ),
			 ),
			admin_url( 'admin.php' )
		);

		// Preview Entry URL
		$preview_url = esc_url( wpforms()->preview->form_preview_url( $this->form_id ) );

		// Export Entry URL
		$export_url = wp_nonce_url(
			add_query_arg(
				array(
					'export' => 'all',
				),
				$base
			),
			'wpforms_entry_list_export'
		);

		// Mark Read URL
		$read_url = wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'markread',
				),
				$base
			),
			'wpforms_entry_list_markread'
		);
		?>

		<div class="form-details wpforms-clear">

			<span class="form-details-sub"><?php _e( 'Select Form', 'wpforms' ); ?></span>

			<h3 class="form-details-title">
				<?php echo wp_strip_all_tags( $form_data['settings']['form_title'] ); ?>

				<div class="form-selector">
					<a href="#" title="<?php _e( 'Open form selector', 'wpforms' ); ?>" class="toggle dashicons dashicons-arrow-down-alt2"></a>
					<div class="form-list">
						<ul>
							<?php
							foreach( $this->forms as $key => $form ) {
								$form_url = add_query_arg(
									array(
										'page'    => 'wpforms-entries',
										'view'    => 'list',
										'form_id' => absint( $form->ID ),
									),
									admin_url( 'admin.php' )
								);
								echo '<li><a href="' . esc_url( $form_url ) . '">' . esc_html( $form->post_title ) . '</a></li>';
							}
							?>
						</ul>
					</div>
				</div>
			</h3>

			<div class="form-details-actions">

				<a href="<?php echo $edit_url; ?>" class="form-details-actions-edit">
					<span class="dashicons dashicons-edit"></span>
					<?php _e( 'Edit This Form', 'wpforms' ); ?>
				</a>

				<a href="<?php echo $preview_url; ?>" class="form-details-actions-preview" target="_blank" rel="noopener">
					<span class="dashicons dashicons-visibility"></span>
					<?php _e( 'Preview Form', 'wpforms' ); ?>
				</a>

				<a href="<?php echo $export_url; ?>" class="form-details-actions-export">
					<span class="dashicons dashicons-migrate"></span>
					<?php _e( 'Download Export (CSV)', 'wpforms' ); ?>
				</a>

				<a href="<?php echo $read_url; ?>" class="form-details-actions-read">
					<span class="dashicons dashicons-marker"></span>
					<?php _e( 'Mark All Read', 'wpforms' ); ?>
				</a>

			</div>

		</div>
		<?php
	}

	/**
	 * Display admin notices and errors.
	 *
	 * @since 1.1.6
	 * @todo Refactor or eliminate this
	 */
	function display_alerts( $display = '', $wrap = false ) {

		if ( empty( $this->alerts ) ) {
			return;

		} else {

			if ( empty( $display ) ) {
				$display = array( 'error', 'info', 'warning', 'success' );
			} else {
				$display = (array) $display;
			}

			foreach( $this->alerts as $alert ) {

				$type = ! empty( $alert['type'] ) ? $alert['type'] : 'info';

				if ( in_array( $type, $display, true ) ) {
					$class  = 'notice-' . $type;
					$class .= ! empty( $alert['dismiss'] ) ? ' is-dismissible' : '';
					$output = '<div class="notice ' . $class . '"><p>' . $alert['message'] . '</p></div>';
					if ( $wrap ) {
						echo '<div class="wrap">' . $output . '</div>';
					} else {
						echo $output;
					}
					if ( ! empty( $alert['abort'] ) ) {
						$this->abort = true;
						break;
					}
				}
			}
		}
	}
}
new WPForms_Entries_List;

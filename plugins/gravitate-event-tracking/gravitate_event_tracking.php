<?php

/**
 * @package Gravitate Event Tracking for Google Analytics
 */
/*
Plugin Name: Gravitate Event Tracking
Plugin URI: http://www.gravitatedesign.com
Description: This is Plugin allows you to add custom Tracking events for Google Analytics.
Version: 1.5.3
*/

define('GETGA_VERSION', '1.5.3');

/*
Here is a Description of the Plugin
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Gravitate GA Tracker.';
	exit;
}



//////////////////////////////////////////////////////////////
// Hooks and filters
//////////////////////////////////////////////////////////////

register_activation_hook( __FILE__, 'GETGA_activate' );
add_action('admin_menu', 'GETGA_create_menu');
add_action('wp_footer', 'GETGA_add_tracking_code');
add_action('wp_head', 'GETGA_add_ga_code');
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'GETGA_plugin_settings_link');

//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////

if(!empty($_GET['page']) && $_GET['page'] == 'getga_tracking' && !empty($_GET['export']))
{
	$getga_events_option = get_option('GETGA_EVENTS');

	$getga_settings_option = GETGA_get_advanced_settings();

	$getga_settings = array('EVENTS' => $getga_events_option, 'SETTINGS' => $getga_settings_option);

	header('Content-Type: application/octet-stream');
	header('Content-Transfer-Encoding: Binary');
	header('Content-disposition: attachment; filename="' . str_replace(' ', '_', get_bloginfo('name')) . '_GETGA_Settings.txt"');
	echo json_encode($getga_settings);
	exit;
}

function GETGA_plugin_settings_link($links)
{
  $settings_link = '<a href="options-general.php?page=getga_tracking">Settings</a>';
  array_unshift($links, $settings_link);
  return $links;
}


/**
 * GETGA_activate function
 * This Function Runs any Activation for the Plugin
 **/
function GETGA_activate()
{
	GETGA_check_and_create_option();
	update_option( 'GETGA_DISMISS_GA_WARNING', 0 );
}

/**
 * GETGA_presets function
 * This Function returns an array of Preset Actions to Track
 **/
function GETGA_presets()
{
	$presets = array();
	$presets[] = array(
		'selector' => 'a[href$=".pdf"], a[href$=".doc"], a[href$=".docx"], a[href$=".ods"], a[href$=".odt"], a[href$=".xls"], a[href$=".xlsx"], a[href$=".txt"], a[href$=".zip"], a[href$=".csv"]',
		'description' => 'Downloads - pdf, doc(x), xls(x), txt, zip, csv',
		'category' => 'Downloads',
		'action' => 'click',
		'action_label' => 'Downloaded',
		'label' => 'Download_{ITEM_TITLE}_{PAGE_RELATIVE_URL}_{LINK_RELATIVE_URL}'
		);

	$presets[] = array(
		'selector' => 'a[href^="mailto:"]',
		'description' => 'Email Link',
		'category' => 'Contact',
		'action' => 'click',
		'action_label' => 'Email',
		'label' => 'Contact_Email_FROM_{PAGE_URL}'
		);

	$presets[] = array(
		'selector' => 'a[href^="tel:"]',
		'description' => 'Mobile Click To Call',
		'category' => 'Contact',
		'action' => 'click',
		'action_label' => 'MobileCall',
		'label' => 'Contact_Click-to-Call_FROM_{PAGE_URL}'
		);

	$presets[] = array(
		'selector' => 'input[type=submit]',
		'description' => 'All Submit Buttons',
		'category' => 'Form Submits',
		'action' => 'click',
		'action_label' => 'Form Submitted',
		'label' => 'Form_Submitted_{TAG_HTML}_{PAGE_RELATIVE_URL}'
		);

	$presets[] = array(
		'selector' => 'form',
		'description' => 'All Form Submissions',
		'category' => 'Form Submits',
		'action' => 'submit',
		'action_label' => 'Form Submitted',
		'label' => 'Form_Submitted_{TAG_HTML}_{PAGE_RELATIVE_URL}'
		);

	$presets[] = array(
		'selector' => '.gtrackexternal',
		'description' => 'All External Links',
		'category' => 'Links',
		'action' => 'click',
		'action_label' => 'External Links',
		'label' => 'External_Link_Clicked_{TAG_HTML}_{PAGE_RELATIVE_URL}'
		);

	$presets[] = array(
		'selector' => 'window',
		'description' => 'Resized',
		'category' => 'Resized',
		'action' => 'resize',
		'action_label' => 'Resized',
		'label' => 'Resized_{PAGE_RELATIVE_URL}'
		);

	$presets[] = array(
		'selector' => 'window',
		'description' => 'Scrolled',
		'category' => 'Scrolled',
		'action' => 'scroll',
		'action_label' => 'Scrolled',
		'label' => 'Scrolled_{PAGE_RELATIVE_URL}'
		);

	$presets[] = array(
		'selector' => 'window',
		'description' => 'Scrolled Depth',
		'category' => 'Scrolled',
		'action' => 'scrolldepth',
		'action_label' => 'Scrolled Depth',
		'label' => 'Scrolled_{SCROLL_PERCENTAGE}_{PAGE_RELATIVE_URL}'
		);

	return $presets;
}

/**
 * GETGA_check_and_create_option function
 * This Function will check to see if the Option has been added if not it will create it.
 **/
//GETGA_check_and_create_option();
function GETGA_check_and_create_option()
{
	$getga_events_option = get_option('GETGA_EVENTS');
    if(empty($getga_events_option))
    {
    	$event = array();
    	$event['selector'] = '.gtrack';
    	$event['description'] = 'Generic Event Tracker';
    	$event['category'] = 'Default';
    	$event['action_type'] = 'click';
    	$event['action_label'] = 'Default Item Clicked';
    	$event['label'] = 'Default_{ITEM_TITLE}_{PAGE_URL}';
    	$event['status'] = 'active';

    	$events = array($event);

    	foreach (GETGA_presets() as $preset)
    	{
    		$event = array();
	    	$event['selector'] = htmlentities($preset['selector'], ENT_QUOTES);
	    	$event['description'] = $preset['description'];
	    	$event['category'] = $preset['category'];
	    	$event['action_type'] = $preset['action'];
	    	$event['action_label'] = $preset['action_label'];
	    	$event['label'] = $preset['label'];
	    	$event['status'] = 'active';

	    	$events[] = $event;
    	}

    	update_option( 'GETGA_EVENTS', $events );
    }

    $getga_settings_option = get_option('GETGA_SETTINGS');
    if(empty($getga_settings_option))
    {
    	$getga_settings = array('first_delay' => '1', 'second_delay' => '2', 'debug' => 'none');
    	update_option( 'GETGA_SETTINGS', $getga_settings );
    }
}

function GETGA_get_advanced_settings()
{
	$getga_settings = get_option('GETGA_SETTINGS');

	if(empty($getga_settings))
	{
		// Create Defaults
		$getga_settings = array('first_delay' => '1', 'second_delay' => '2');
	}

	if(!isset($getga_settings['first_delay']))
	{
		$getga_settings['first_delay'] = '1';
	}

	if(!isset($getga_settings['second_delay']))
	{
		$getga_settings['second_delay'] = '2';
	}

	return $getga_settings;

}

function GETGA_save_settings()
{
	if(!empty($_POST['save_settings']) && !empty($_POST['settings']))
	{
		if(update_option( 'GETGA_SETTINGS', $_POST['settings'] ) || serialize($_POST['settings']) == serialize(get_option('GETGA_SETTINGS')))
		{
			return 'Your Settings were saved Successfully!';
		}
	}

	return false;
}

/**
 * GETGA_create_menu function
 * This Function Runs hook to add the Page to the Settings Menu
 **/
function GETGA_create_menu()
{
	add_submenu_page( 'options-general.php', 'Gravitate Event Tracking', 'Gravitate Event Tracking', 'manage_options', 'getga_tracking', 'GETGA_tracker_page');
}

function GETGA_save_events()
{
	if(!empty($_POST['save_events']) && isset($_POST['selectors']))
	{
		$events = array();

		foreach ($_POST['selectors'] as $key => $selector)
		{
			$event = array();
	    	$event['selector'] = htmlentities($selector, ENT_QUOTES);
	    	$event['description'] = $_POST['descriptions'][$key];
	    	$event['category'] = (!empty($_POST['categories'][$key]) ? $_POST['categories'][$key] : '');
	    	$event['action_type'] = (!empty($_POST['action_types'][$key]) ? $_POST['action_types'][$key] : 'click');
	    	$event['action_label'] = (!empty($_POST['action_labels'][$key]) ? $_POST['action_labels'][$key] : '');
	    	$event['label'] = (!empty($_POST['labels'][$key]) ? $_POST['labels'][$key] : '');
	    	$event['status'] = (!empty($_POST['active'][$key]) ? 'active' : '0');

	    	$events[] = $event;
		}

		if(update_option( 'GETGA_EVENTS', $events ) || serialize($events) == serialize(get_option('GETGA_EVENTS')))
		{
			return 'Your Events were saved Successfully!';
		}
	}
	return false;
}

/**
 * GETGA_tracker_page function
 * This Function Displays the HTML Content and form for the Admin Page
 **/
function GETGA_tracker_page()
{
	if(!empty($_GET['page']) && $_GET['page'] == 'getga_tracking' && (!empty($_GET['import']) || !empty($_GET['imported'])))
	{
		if(!empty($_FILES['getga_attachment']) && !empty($_GET['imported']))
		{
			$settings = json_decode(file_get_contents($_FILES['getga_attachment']['tmp_name']), true);

			if(!empty($settings))
			{

				$getga_events_option = get_option('GETGA_EVENTS');

				$getga_events_option = array_merge($getga_events_option, (isset($settings['EVENTS']) ? $settings['EVENTS'] : $settings));
				update_option( 'GETGA_EVENTS', $getga_events_option );

				if(isset($settings['SETTINGS']))
				{
					update_option( 'GETGA_SETTINGS', $settings['SETTINGS'] );
				}

				$saved = "Your Settings have been Imported Successfully!";
			}
			else
			{

			}
		}
		else if(!empty($_GET['import']))
		{
			GETGA_import_settings_form();
			exit;
		}
	}

	if(!empty($_POST['save_events']) && isset($_POST['selectors']))
	{
		$saved = GETGA_save_events();
	}

	if(!empty($_POST['save_settings']) && !empty($_POST['settings']))
	{
		$saved = GETGA_save_settings();
	}

	if(!empty($_GET['dismiss_ga_warning']))
	{
		update_option( 'GETGA_DISMISS_GA_WARNING', 1 );
	}

	if(empty($saved) && !empty($_GET['page']) && $_GET['page'] == 'getga_tracking' && !empty($_GET['settings']))
	{
		GETGA_advanced_settings_form();
		exit;
	}

	$presets = GETGA_presets();

	?>
	<div class="wrap">
		<h2>Gravitate Event Tracking for Google Analytics</h2>
		<h4 style="margin: 6px 0;">Version <?php echo GETGA_VERSION;?></h4>
		<br>
		This Plugin only adds the Tracking Script to your website.  It does not offer any reports.  To view the Tracking details, you will need to login to your Google Analytics account that is associated with this website.  Google Analytics Reports for Event Tracking are in real time, so you should be able to see the results immediately from your Google Analytics account.
		<br>
		<br>
		<p class="right" style="text-align:right;">
			<a class="export-settings button" href="?page=getga_tracking&export=true">Export Settings</a> &nbsp; &nbsp;
			<a class="import-settings button" href="?page=getga_tracking&import=true">Import Settings</a> &nbsp; &nbsp;
			<a class="advanced-settings button" href="?page=getga_tracking&settings=true">Advanced Settings</a>
		</p>
		<form method="post">
		<input type="hidden" name="save_events" value="1">

		<h3 style="margin: 6px 0;">Custom</h3>
		<table cellspacing="0" class="wp-list-table widefat plugins">
			<thead>
				<tr>
					<th class="manage-column column-cb" id="cb" scope="col">
						Active
					</th>
					<th style="" class="manage-column column-description" id="description" scope="col">
						Title / Description<br>
						<span style="font-size: 10px; color: #777;">Used only for Reference and in HTML Comments</span>
					</th>
					<th style="" class="manage-column column-name" id="name" scope="col">
						Selector / Element<br>
						<span style="font-size: 10px; color: #777;">Use CSS Class's or ID's</span>
					</th>
					<th style="" class="manage-column column-name" id="name" scope="col">
						Category<br>
						<span style="font-size: 10px; color: #777;">Google Analytics Category Label</span>
					</th>
					<th style="" class="manage-column column-name" id="name" scope="col">
						Action<br>
						<span style="font-size: 10px; color: #777;">Google Analytics Action and Action Label</span>
					</th>
					<th style="" class="manage-column column-name" id="name" scope="col">
						Label<br>
						<span style="font-size: 10px; color: #777;">Google Analytics Label <br>Tags: {ITEM_TITLE} {PAGE_URL} {PAGE_RELATIVE_URL} {LINK_URL} {LINK_RELATIVE_URL} {IMAGE_SRC} {IMAGE_ALT} {TAG_HTML} {SCROLL_PERCENTAGE}</span>
					</th>
					<th class="manage-column" scope="col">
						&nbsp;
					</th>
				</tr>
			</thead>

			<tbody id="the-list">
				<?php
				$getga_events = get_option('GETGA_EVENTS');

				if(!empty($getga_events) && is_string($getga_events))
				{
					$getga_events = unserialize($getga_events);
				}

				if(!empty($getga_events) && is_array($getga_events))
				{
					$loaded = array();
					foreach($getga_events as $key => $getga_event)
					{
						$loaded_key = stripcslashes($getga_event['selector']).'_'.$getga_event['label'];
						if(!in_array($loaded_key ,$loaded))
						{
							$loaded[] = $loaded_key;
							?>
							<tr class="event <?php echo $getga_event['status'];?>">

								<th style="vertical-align:middle;" class="check-column" scope="row">
									<input type="hidden" name="active[]" class="hidden_event_status" value="<?php echo $getga_event['status'];?>">
									<input style="margin: 0 11px 8px;" class="event_status" type="checkbox" value="active" name="active_input[]" <?php checked($getga_event['status'], 'active');?>>
								</th>
								<td style="vertical-align:middle;">
									<input style="width: 100%; min-width: 280px;" class="track_description" placeholder="Title / Description" type="text" value="<?php echo $getga_event['description'];?>" name="descriptions[]">
								</td>
								<td style="vertical-align:middle;">
									<input type="text" style="width: 100%; min-width: 100px;" class="track_selector" placeholder="Selector / Element" value='<?php echo stripcslashes($getga_event['selector']);?>' name="selectors[]">
								</td>
								<td style="vertical-align:middle;">
									<input type="text" style="width: 100%; min-width: 100px;" class="track_category" placeholder="Google Analytics Category" value="<?php echo $getga_event['category'];?>" name="categories[]">
								</td>
								<td style="vertical-align:middle;">
									<select name="action_types[]" class="track_action">
										<option value="click" <?php selected($getga_event['action_type'], 'click');?>>On Mouse Click</option>
										<option value="submit" <?php selected($getga_event['action_type'], 'submit');?>>On Form Submit</option>
										<option value="change" <?php selected($getga_event['action_type'], 'change');?>>On Value Change</option>
										<option value="mouseover" <?php selected($getga_event['action_type'], 'mouseover');?>>On Mouse Over</option>
										<option value="keypress" <?php selected($getga_event['action_type'], 'keypress');?>>When Typing</option>
										<option value="resize" <?php selected($getga_event['action_type'], 'resize');?>>Resized</option>
										<option value="scroll" <?php selected($getga_event['action_type'], 'scroll');?>>Scrolled</option>
										<option value="scrolldepth" <?php selected($getga_event['action_type'], 'scrolldepth');?>>Scrolled Depth</option>
									</select>
									<input type="text" style="width: 100%; min-width: 100px;" class="track_action-label" placeholder="Action Label" value="<?php echo $getga_event['action_label'];?>" name="action_labels[]">
								</td>
								<td style="vertical-align:middle;">
									<input style="width: 100%; min-width: 160px;" class="track_label" placeholder="Google Analytics Label" type="text" value="<?php echo $getga_event['label'];?>" name="labels[]">
								</td>
								<th style="vertical-align:middle;" scope="row">
									<a class="delete button" style="border: 1px solid #ccc !important;">X</a>
								</th>
							</tr>

							<?php
						}
					}
				}
				?>
			</tbody>
		</table>

		<p class="right">
		<h3 style="margin: 6px 0;">Add Custom Event &nbsp; &nbsp; &nbsp; &nbsp; Add Presets</h3>
		<a class="add-tracking button" data-action="click">+ Add Custom Tracking</a> &nbsp; &nbsp;
		<?php
		foreach (GETGA_presets() as $preset)
		{
			?>
			<a class="add-tracking button"
				data-selector='<?php echo $preset['selector'];?>'
				data-category="<?php echo $preset['category'];?>"
				data-action="<?php echo $preset['action'];?>"
				data-action-label="<?php echo $preset['action_label'];?>"
				data-label="<?php echo $preset['label'];?>"><?php echo $preset['description'];?>
			</a>
			<?php
		}
		?>
		<br>
		</p>
		<p class="submit"><input type="submit" value="Save Changes" class="button button-primary" id="submit" name="submit"></p>
		<br>
		<br>
		<p style="text-align:right;">Plugin Created by <a target="_blank" href="http://www.gravitatedesign.com">Gravitate</a></p>


<script type="text/javascript">
(function($){
	var new_item;
	$('.add-tracking.button').on('click', function(e){
		e.preventDefault();
		new_item = $('#the-list .event:first').clone();
		new_item.find('.track_description').val($(this).html());
		new_item.find('.track_selector').val($(this).data('selector'));
		new_item.find('.track_category').val($(this).data('category'));
		new_item.find('.track_action').val($(this).data('action'));
		new_item.find('.track_action-label').val($(this).data('action-label'));
		new_item.find('.track_label').val($(this).data('label')); //event_status
		new_item.find('.hidden_event_status').val('active');
		new_item.find('.event_status').prop('checked', 'checked');
		new_item.appendTo( "#the-list" );

		$('.export-settings.button').attr('disabled', 'disabled');
		$('.export-settings.button').prop('disabled', true);
		$('.export-settings.button').attr('href', '#');
		$('.export-settings.button').attr('title', 'You Need to Save your Settings before you can Export.');

		getga_update_listeners();

		if($(this).data('selector') === '.gtrackexternal')
		{
			alert("This will use the selector .gtrackexternal\nUsing this selector will automatically find all external links for you.");
		}

		if($(this).data('action') === 'scrolldepth')
		{
			alert("Scrolled Depth is triggered at 25%, 50%, 75% and 100%.  To Trigger the first Scroll also included the Scrolled Preset");
		}
	});

	getga_update_listeners();

})(jQuery)

function getga_update_listeners()
{
	jQuery('.event_status').on('click', function(e){
		if(this.checked)
		{
			jQuery(this).prev().val('active');
		}
		else
		{
			jQuery(this).prev().val('0');
		}
	});

	jQuery('.delete.button').on('click', function(e){
		jQuery(this).parent().parent().remove();
	});
}
</script>

		<?php

		if(!get_option('GETGA_DISMISS_GA_WARNING'))
		{
			$default_socket_timeout = ini_get('default_socket_timeout');
			if(!empty($default_socket_timeout))
			{
				ini_set('default_socket_timeout', 5);
			}

			// Check for Google Analytics
			$home_page_content = file_get_contents(site_url());

			if(!empty($default_socket_timeout))
			{
				ini_set('default_socket_timeout', $default_socket_timeout);
			}

			if(!empty($home_page_content) && strpos($home_page_content, '</body>') && strpos($home_page_content, 'UA-') === false && strpos($home_page_content, 'google-analytics.com') === false)
			{
				?>
				<div class="error"><p>Your Website does not seem to have Google Analytics Installed.  We can't find it in your Home Page HTML.  Without Google Analytics installed this Plugin wont do anything. Click here to add it <a href="?page=getga_tracking&settings=true">Advanced Settings</a>. If this message is an error and Google Analytics is installed Properly or you are using Google Tag Manager to install it then just Dismiss it. &nbsp; &nbsp; &nbsp; - <a href="?page=<?php echo $_GET['page'];?>&amp;dismiss_ga_warning=true">Dismiss</a></p></div>
				<?php
			}
		}
		?>

		<?php if(!empty($saved)){ echo '<div class="updated"><p>' . $saved . '</p></div>'; } ?>
		<?php if(isset($saved) && !$saved){ echo '<div class="error"><p>Error Saving your Events. Please try again.</p></div>'; } ?>
		</form>
    </div>
	<?php
}


function GETGA_import_settings_form()
{

	?>
	<div class="wrap">
		<h2>Gravitate Event Tracking for Google Analytics</h2>
		<h4 style="margin: 6px 0;">Version <?php echo GETGA_VERSION;?></h4>
		<br>
		<h3 style="margin: 6px 0;">Import Settings</h3>
		Browse for the GETGA_Settings.txt file that you exported and upload here:
		<br>
		<form method="post" enctype="multipart/form-data" action="<?php echo str_replace('&import=true', '&imported=true', $_SERVER['REQUEST_URI']);?>">
			<input type="file" name="getga_attachment">
			<br><br>
			<a href="<?php echo str_replace('&import=true', '', $_SERVER['REQUEST_URI']);?>" class="button">Cancel</a> &nbsp; &nbsp; &nbsp; <input class="button-primary button" type="submit" name="submit" value="Submit">
		</form>
		<br>
		<br>
	</div>
	<?php
}

function GETGA_advanced_settings_form()
{
	$getga_settings = GETGA_get_advanced_settings();

	$has_ga_code = false;
	$has_gtm_code = false;

	if(empty($getga_settings['include_ga_code']))
	{
		$default_socket_timeout = ini_get('default_socket_timeout');
		if(!empty($default_socket_timeout))
		{
			ini_set('default_socket_timeout', 5);
		}

		// Check for Google Analytics
		$home_page_content = file_get_contents(site_url());

		if(!empty($default_socket_timeout))
		{
			ini_set('default_socket_timeout', $default_socket_timeout);
		}

		if(!empty($home_page_content) && strpos($home_page_content, '</body>') && strpos($home_page_content, 'UA-') !== false)
		{
			$has_ga_code = true;
		}
	}

	if(empty($getga_settings['include_gtm_code']))
	{
		$default_socket_timeout = ini_get('default_socket_timeout');
		if(!empty($default_socket_timeout))
		{
			ini_set('default_socket_timeout', 5);
		}

		// Check for Google Analytics
		$home_page_content = file_get_contents(site_url());

		if(!empty($default_socket_timeout))
		{
			ini_set('default_socket_timeout', $default_socket_timeout);
		}

		if(!empty($home_page_content) && strpos($home_page_content, '</body>') && strpos($home_page_content, 'GTM-') !== false)
		{
			$has_gtm_code = true;
		}
	}


	?>
	<div class="wrap">
		<h2>Gravitate Event Tracking for Google Analytics</h2>
		<h4 style="margin: 6px 0;">Version <?php echo GETGA_VERSION;?></h4>
		<br>
		<h3 style="margin: 6px 0;">Advanced Settings</h3>
		<br>
		<form method="post" enctype="multipart/form-data" action="<?php echo str_replace('&settings=true', '', $_SERVER['REQUEST_URI']);?>">
			<input type="hidden" name="save_settings" value="1">

			<?php if($has_ga_code) { ?>
			<div class="error" style="border-color: #DDA200;"><p>It looks like Google Analytics is already embedded on your website.  This might be done from another Plugin or direct inclusion. It is not recommended to embed it more then once.</p></div>
			<?php } ?>

			<?php if($has_gtm_code) { ?>
			<div class="error" style="border-color: #DDA200;"><p>It looks like Google Tag Manager is already embedded on your website.  This might be done from another Plugin or direct inclusion. It is not recommended to embed it more then once.</p></div>
			<?php } ?>

			<label id="include_ga_code_label">
			<input type="hidden" name="settings[include_ga_code]" value="0">
			<input id="include_ga_code" type="checkbox" value="1" name="settings[include_ga_code]" <?php checked($getga_settings['include_ga_code'], '1');?>>
			Embed Google Analytics Universal Tracking Code to my site
			</label>

			<span id="ga_property_id_box" style="opacity: <?php echo ($getga_settings['include_ga_code'] ? '1.0' : '0.5');?>">
			&nbsp; &nbsp; Your Property ID:
			<input type="text" <?php disabled($getga_settings['include_ga_code'], null);?> class="track_selector" placeholder="UA-XXXX-Y" id="ga_property_id" name="settings[ga_property_id]" value="<?php echo stripcslashes($getga_settings['ga_property_id']);?>">
			</span>
			<br><br>

			<label id="include_gtm_code_label">
			<input type="hidden" name="settings[include_gtm_code]" value="0">
			<input id="include_gtm_code" type="checkbox" value="1" name="settings[include_gtm_code]" <?php checked($getga_settings['include_gtm_code'], '1');?>>
			Embed Google Tag Manager Code to my site
			</label>

			<span id="gtm_property_id_box" style="opacity: <?php echo ($getga_settings['include_gtm_code'] ? '1.0' : '0.5');?>">
			&nbsp; &nbsp; Your Container ID:
			<input type="text" <?php disabled($getga_settings['include_gtm_code'], null);?> class="track_selector" placeholder="GTM-XXXX" id="gtm_property_id" name="settings[gtm_property_id]" value="<?php echo stripcslashes($getga_settings['gtm_property_id']);?>">
			</span>
			<br><br>

			<label for="first_delay"><strong>Delay Re-capture Time</strong></label>
			<br>
			This setting is used to make sure that any elements that are generated on the page after the initial load get tracked as well.<br>
			Ex. If you are importing some javascript that creates elements on your website, those elements are not always available immediately to Gravitate Event Tracking.<br>
			If the Elements are not available right away to Gravitate Event Tracking it will re-check for the elements after a period of time.<br><br>
			<span style="display: inline-block; width: 100px; text-align: right;"> First Try in</span> <input class="track_description" style="width: 120px;" type="text" value="<?php echo $getga_settings['first_delay'];?>" id="first_delay" name="settings[first_delay]"> <strong>Seconds</strong> (Set to 0 to Deactivate)
			<br>
			<span style="display: inline-block; width: 100px; text-align: right;"> Second Try in</span> <input class="track_description" style="width: 120px;" type="text" value="<?php echo $getga_settings['second_delay'];?>" name="settings[second_delay]"> <strong>Seconds</strong> after First Try (Requires First Try, Set to 0 to Deactivate)
			<br>
			<br>
			<label for="debug"><strong>Debug</strong></label>
			<br>
			<select id="debug" name="settings[debug]">
				<option value="none" <?php selected($getga_settings['debug'], 'none');?>>None</option>
				<option value="console" <?php selected($getga_settings['debug'], 'console');?>>Console Logs (Only shows in the Browser Console)</option>
				<option value="alert" <?php selected($getga_settings['debug'], 'alert');?>>Alert (Shows an alert box for each Call.  Not recommended on Production)</option>
			</select>
			<p class="submit"><a href="<?php echo str_replace('&settings=true', '', $_SERVER['REQUEST_URI']);?>" class="button">Cancel</a> &nbsp; &nbsp; &nbsp; <input type="submit" value="Save Changes" class="button button-primary" id="submit" name="submit"></p>
		</form>
		<br>
		<br>
	</div>

	<script type="text/javascript">

	(function($){

		$('#include_ga_code').on('click', function(e)
		{
			if($(this).is(':checked'))
			{
				$('#ga_property_id_box').css('opacity', '1.0');
				$('#ga_property_id').removeAttr('disabled');
			}
			else
			{
				$('#ga_property_id_box').css('opacity', '0.5');
				$('#ga_property_id').attr('disabled','disabled');
			}
		});

		$('#include_gtm_code').on('click', function(e)
		{
			if($(this).is(':checked'))
			{
				$('#gtm_property_id_box').css('opacity', '1.0');
				$('#gtm_property_id').removeAttr('disabled');
			}
			else
			{
				$('#gtm_property_id_box').css('opacity', '0.5');
				$('#gtm_property_id').attr('disabled','disabled');
			}
		});

	})(jQuery);

	</script>
	<?php
}



function GETGA_add_ga_code()
{
	$getga_settings = GETGA_get_advanced_settings();

	if(!empty($getga_settings['include_gtm_code']) && !empty($getga_settings['gtm_property_id']))
	{
		?>
		<!-- Google Tag Manager -->
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
		j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
		'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
		})(window,document,'script','dataLayer','<?php echo $getga_settings['gtm_property_id'];?>');</script>
		<!-- End Google Tag Manager -->

		<?php
	}

	if(!empty($getga_settings['include_ga_code']) && !empty($getga_settings['ga_property_id']))
	{
		?>
		<!-- Google Analytics added by Gravitate Event Tracking - This can be disabled in the Plugin Settings -->
		<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create', '<?php echo $getga_settings['ga_property_id'];?>', 'auto');
		ga('require', 'displayfeatures');
		ga('send', 'pageview');

		</script>
		<!-- End Google Analytics -->

		<?php
	}
}

/**
 * GETGA_add_tracking_code function
 * This Function adds the Javascript and jQuery Code to the Footer on the Front End for Every page.
 **/
function GETGA_add_tracking_code()
{
	$getga_events = get_option('GETGA_EVENTS');

	$getga_settings = GETGA_get_advanced_settings();

	if(!empty($getga_events) && is_string($getga_events))
	{
		$getga_events = unserialize($getga_events);
	}

	if(!empty($getga_events) && is_array($getga_events))
	{
	?>

	<script type="text/javascript">
	var GETGA_settings = <?php echo json_encode($getga_settings);?>;
	var GETGA_events = <?php echo json_encode($getga_events);?>;
	</script>
	<script type="text/javascript" defer="defer" src="<?php echo plugins_url( 'gravitate_event_tracking.js', __FILE__ );?>?v=<?php echo GETGA_VERSION;?>"></script>

	<?php
	}

	if(!empty($getga_settings['include_gtm_code']) && !empty($getga_settings['gtm_property_id']))
	{
	?>

	<!-- Google Tag Manager (noscript) -->
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo $getga_settings['gtm_property_id'];?>"
	height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->

	<?php
	}
}

<?php

	session_start();

	// echo 'WINDOW WIDTH: '.$_SESSION['windowsize']['width'];

// require_once "modules/is-debug.php";

/* **********************************************************************
Load External Files
********************************************************************** */




// function delete_post_type(){
// unregister_post_type( 'colleges' );
// }
// add_action('init','delete_post_type');



// $windowSize = $_SESSION['windowsize'];



/**
 * Responsive Image Helper Function
 *
 * @param string $image_id the id of the image (from ACF or similar)
 * @param string $image_size the size of the thumbnail image or custom image size
 * @param string $max_width the max width this image will be shown to build the sizes attribute
 */

function ar_responsive_image($image_id,$image_size,$max_width){

	// check the image ID is not blank
	if($image_id != '') {

		// set the default src image size
		$image_src = wp_get_attachment_image_url( $image_id, $image_size );

		// set the srcset with various image sizes
		$image_srcset = wp_get_attachment_image_srcset( $image_id, $image_size );

		// generate the markup for the responsive image
		return 'src="'.$image_src.'" srcset="'.$image_srcset.'" sizes="(max-width: '.$max_width.') 100vw, '.$max_width.'"';

	}
}


// this will handle selecting the best possible image size for background css images
// a = the image array to choose from
// function responsive_background_images(){
//
// 	// $windowSize = $_SESSION['windowsize'];
//
// 	switch (true) {
// 		case ($_SESSION['windowsize']['width'] > 1000):
// 				return array('url');
// 				break;
// 		case ($_SESSION['windowsize']['width'] > 780):
// 				//return $a['block_slide_image']['sizes']['large'];
// 				return array('sizes','large');
// 				break;
// 		case ($_SESSION['windowsize']['width'] > 200):
// 				// return $a['block_slide_image']['sizes']['medium'];
// 				return array('sizes','medium');
// 				break;
// 		}
//
// }



function responsive_background_images(){
	$windowSize = $_SESSION['windowsize'];

	// we will need to revisit this and improve the responsive facet to add in more size options, etc.

	// if($windowSize['width'] > 1000){
	// 	return array('block_slide_image','url');
	// }else if($windowSize['width'] > 780 && $windowSize['width'] < 1000){
	// 	return array('block_slide_image','sizes','large');
	// }else if($windowSize['width'] > 250 && $windowSize['width'] < 780){
	// 	return array('block_slide_image','sizes','medium');
	// }else if($windowSize['width'] < 250){
	// 	return array('block_slide_image','sizes','small');
	// }
	return array('block_slide_image','url');
}








// Load any external files you have here

// set up specific theme support if required
require_once(get_template_directory() . "/functions/themesupport.php");

// load navigation functions
require_once(get_template_directory() . "/functions/navigation.php");

// load page conditional script functions
require_once(get_template_directory() . "/functions/pageconditionalscripts.php");

// load page conditional script functions
require_once(get_template_directory() . "/functions/pageconditionalstyles.php");

// load tinymce functions
require_once(get_template_directory() . "/functions/tinymce.php");

// load sidebar functions
require_once(get_template_directory() . "/functions/sidebar.php");

// set up specific feeds for content that can be used by any other site
require_once(get_template_directory() . "/functions/comments.php");

// set up specific admin tools for administration
require_once(get_template_directory() . "/functions/admin-administration.php");






















// function wp_header() {
// 	do_action('wp_header');
// }



/**
 * Change WPForms capability requirement.
 *
 * @param string $cap
 * @return string
 */
function wpforms_custom_capability( $cap ) {
    // unfiltered_html by default means Editors and up.
    // See more about WordPress roles and capabilities
    // https://codex.wordpress.org/Roles_and_Capabilities
    return 'unfiltered_html';
}
// add_filter( 'wpforms_manage_cap', 'wpforms_custom_capability' );


// Async load
function nudev_async_scripts($url){
    if ( strpos( $url, '#asyncload') === false )
        return $url;
    else if ( is_admin() )
        return str_replace( '#asyncload', '', $url );
    else
	return str_replace( '#asyncload', '', $url )."' async='async";
    }
// add_filter( 'clean_url', 'nudev_async_scripts', 11, 1 );


function disable_embeds_init(){

    // Remove the REST API endpoint.
    remove_action('rest_api_init', 'wp_oembed_register_route');

    // Turn off oEmbed auto discovery.
    // Don't filter oEmbed results.
    remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);

    // Remove oEmbed discovery links.
    remove_action('wp_head', 'wp_oembed_add_discovery_links');

    // Remove oEmbed-specific JavaScript from the front-end and back-end.
    remove_action('wp_head', 'wp_oembed_add_host_js');
}

// add_action('init', 'disable_embeds_init', 9999);

//* Adding DNS Prefetching
function ism_dns_prefetch(){
    echo '<meta http-equiv="x-dns-prefetch-control" content="on">';
    // <link rel="dns-prefetch" href="https://fonts.googleapis.com/css?family=Roboto|Roboto+Condensed" />';
}


// Load nudev scripts (header.php)
function nudev_header_scripts(){
    if ($GLOBALS['pagenow'] != 'wp-login.php' && !is_admin()) {

      // Scripts minify
      //wp_register_script('nudevscripts-min', get_template_directory_uri() . '/js/scripts.min.js', array(), '1.0.0');
      // Enqueue Scripts
      //wp_enqueue_script('nudevscripts-min');

    }
}


// load custom jquery
function nudev_include_custom_jquery(){
  // die(get_template_directory_uri() . '/js/jquery.min.js');
	wp_deregister_script('jquery');

  wp_enqueue_script('jquery', get_template_directory_uri() . '/js/jquery.min.js', array(),'2.2.0');

}


// Remove Injected classes, ID's and Page ID's from Navigation <li> items
function my_css_attributes_filter($var){
  return is_array($var) ? array() : '';
}


// Remove invalid rel attribute values in the categorylist
function remove_category_rel_from_category_list($thelist){
  return str_replace('rel="category tag"', 'rel="tag"', $thelist);
}


// Add page slug to body class
function add_slug_to_body_class($classes){
  global $post;
  if (is_home()) {
      $key = array_search('blog', $classes);
      if ($key > -1) {
          unset($classes[$key]);
      }
  } elseif (is_page()) {
      $classes[] = sanitize_html_class($post->post_name);
  } elseif (is_singular()) {
      $classes[] = sanitize_html_class($post->post_name);
  }

  return $classes;
}


// Remove the width and height attributes from inserted images
function remove_width_attribute($html){
  $html = preg_replace( '/(width|height)="\d*"\s/', "", $html );
  return $html;
}



// Pagination for paged posts, Page 1, Page 2, Page 3, with Next and Previous Links, No plugin
function nudevwp_pagination(){
  global $wp_query;
  $big = 999999999;
  echo paginate_links(array(
    'base' => str_replace($big, '%#%', get_pagenum_link($big)),
    'format' => '?paged=%#%',
    'current' => max(1, get_query_var('paged')),
    'total' => $wp_query->max_num_pages,
    'prev_text' => '&#xf137;',
    'next_text' => '&#xf138;'
  ));
}


// Create 20 Word Callback for Index page Excerpts, call using nudevwp_excerpt('nudevwp_index');
function nudevwp_index($length){
  return 20;
}

function create_post_type_nudev() {
    register_post_type( 'spotlight',
        array(
            'labels' => array(
                'name' => __( 'Spotlight' ),
                'singular_name' => __( 'Spotlight' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'spotlight'),
        )
    );
    register_post_type( 'people',
        array(
            'labels' => array(
                'name' => __( 'People' ),
                'singular_name' => __( 'People' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'people'),
        )
    );
    register_post_type( 'opportunities',
        array(
            'labels' => array(
                'name' => __( 'Opportunities' ),
                'singular_name' => __( 'Opportunities' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'opportunities'),
        )
    );
}


// Create 40 Word Callback for Custom Post Excerpts, call using nudevwp_excerpt('nudevwp_custom_post');
function nudevwp_custom_post($length){
  return 40;
}



// Create the Custom Excerpts callback
function nudevwp_excerpt($length_callback = '',$more_callback = ''){
  global $post;
  if (function_exists($length_callback)){
    add_filter('excerpt_length', $length_callback);
  }
  if (function_exists($more_callback)) {
    add_filter('excerpt_more', $more_callback);
  }
  $output = get_the_excerpt();
  $output = apply_filters('wptexturize', $output);
  $output = apply_filters('convert_chars', $output);
  $output = '<p>' . $output . '</p>';
  echo $output;
}


// Custom View Article link to Post
function nudev_blank_view_article($more){
  global $post;
  return '... <a class="view-article" href="' . get_permalink($post->ID) . '">' . __('View Article', 'nudev') . '</a>';
}


// Remove Admin bar
function remove_admin_bar(){
  return false;
}


// Remove 'text/css' from our enqueued stylesheet
function nudev_style_remove($tag){
  return preg_replace('~\s+type=["\'][^"\']++["\']~', '', $tag);
}


// Removes scripts version number from script tags
function _remove_script_version($src){
	return remove_query_arg( 'ver',  $src  );
}


// Remove thumbnail width and height dimensions that prevent fluid images in the_thumbnail
function remove_thumbnail_dimensions($html){
  $html = preg_replace('/(width|height)=\"\d*\"\s/', "", $html);
  return $html;
}


// Custom Gravatar in Settings > Discussion
function nudevgravatar($avatar_defaults){
  $myavatar = get_template_directory_uri() . '/img/gravatar.jpg';
  $avatar_defaults[$myavatar] = "Custom Gravatar";
  return $avatar_defaults;
}


// set the colors of posts based on published status
function posts_status_color(){
?>
<style>
.status-draft .check-column {box-shadow: -12px 0 0 -3px rgba(237, 86, 68, 1.0) !important;}
.status-pending .check-column {box-shadow: -12px 0 0 -3px rgba(235, 138, 35, 1.0) !important;}
.status-publish .check-column {box-shadow: -12px 0 0 -3px rgba(81, 248, 0, 1.0) !important;}
.status-future .check-column {box-shadow: -12px 0 0 -3px #ffffff !important;}
.status-private .check-column {box-shadow: -12px 0 0 -3px #000000 !important;}
.active .check-column {border-left:4px solid rgba(81, 248, 0, 1.0) !important;}
.inactive .check-column {border-left:4px solid rgba(100, 100, 100, .3) !important;}
</style>
<?php
}





/*------------------------------------*\
    ShortCode Functions
\*------------------------------------*/

// Shortcode Demo with Nested Capability
function nudev_shortcode_demo($atts, $content = null)
{
    return '<div class="shortcode-demo">' . do_shortcode($content) . '</div>'; // do_shortcode allows for nested Shortcodes
}

// Shortcode Demo with simple <h2> tag
function nudev_shortcode_demo_2($atts, $content = null) // Demo Heading H2 shortcode, allows for nesting within above element. Fully expandable.
{
    return '<h2>' . $content . '</h2>';
}





/*------------------------------------*\
    Additional Functions
\*------------------------------------*/

function my_login_logo() { ?>
    <style type="text/css">
        body.login div#login h1 a {
            background-image: url(<?php echo get_template_directory_uri(); ?>/_ui/logo.svg);
            width:302px;
            background-size: 302px 63px;
          	height: 63px;
        }
        body.login #login_error, .login .message {
            border-left: 4px solid rgba(204, 0, 0, 1.0) !important;
        }
        body.login #backtoblog a, .login #nav a {
            color:rgba(51, 62, 71, 1.0) !important;
        }
        body.login #backtoblog a:hover, .login #nav a:hover {
            color:rgba(204, 0, 0, 1.0) !important;
        }
         .wp-core-ui .button-primary {
            background:rgba(204, 0, 0, 1.0) !important;
            border-color:rgba(0, 0, 0, 1.0) !important;
            text-shadow: none !important;
        }
        body.login label {
            color:rgba(51, 62, 71, 1.0) !important;
        }
    </style>
<?php }


function my_login_logo_url() {
    return home_url();
}


function my_login_logo_url_title() {
    return 'Site Name Here';
}


// block WP enum scans
// http://m0n.co/enum
if (!is_admin()){
	// default URL format
	if (preg_match('/author=([0-9]*)/i', $_SERVER['QUERY_STRING'])) die();
	add_filter('redirect_canonical', 'shapeSpace_check_enum', 10, 2);
}
function shapeSpace_check_enum($redirect, $request){
	// permalink URL format
	if (preg_match('/\?author=([0-9]*)(\/*)/i', $request)) die();
	else return $redirect;
}





/*------------------------------------*\
    Actions + Filters + ShortCodes
\*------------------------------------*/

// Add Actions
add_action('init', 'nudev_header_scripts'); // Add Custom Scripts to wp_head
add_action('wp_print_scripts', 'nudev_conditional_scripts'); // Add Conditional Page Scripts
add_action('get_header', 'enable_threaded_comments'); // Enable Threaded Comments
add_action('wp_head', 'ism_dns_prefetch', 0); // DNS Prefetch Google Fonts
add_action('wp_enqueue_scripts', 'nudev_conditional_styles'); // Add Theme Stylesheet
add_action('init', 'register_nudev_menu'); // Add nudev Menu
add_action('init', 'create_post_type_nudev'); // Add our nudev Custom Post Type
add_action('widgets_init', 'my_remove_recent_comments_style'); // Remove inline Recent Comment Styles from wp_head()
add_action('init', 'nudevwp_pagination'); // Add our nudev Pagination
add_action('init', 'disable_embeds_init', 9999);
add_action('wp_enqueue_scripts', 'nudev_include_custom_jquery');
add_action( 'wp_before_admin_bar_render', 'my_admin_bar_render' );
add_action('init', 'df_disable_comments_admin_bar');
add_action('admin_init', 'df_disable_comments_dashboard');
add_action('admin_init', 'df_disable_comments_admin_menu_redirect');
add_action('admin_menu', 'df_disable_comments_admin_menu');
add_action('admin_init', 'df_disable_comments_post_types_support');
add_action( 'login_enqueue_scripts', 'my_login_logo' );
add_action('admin_footer','posts_status_color');
add_action ( 'manage_nualerts_posts_custom_column', 'nualerts_custom_column', 10, 2 );


// Remove Actions
remove_action('wp_head', 'feed_links_extra', 3); // Display the links to the extra feeds such as category feeds
remove_action('wp_head', 'feed_links', 2); // Display the links to the general feeds: Post and Comment Feed
remove_action('wp_head', 'rsd_link'); // Display the link to the Really Simple Discovery service endpoint, EditURI link
remove_action('wp_head', 'wlwmanifest_link'); // Display the link to the Windows Live Writer manifest file.
remove_action('wp_head', 'wp_generator'); // Display the XHTML generator that is generated on the wp_head hook, WP version
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );//Removing Emoji code from header
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );//Removing Emoji code from header
remove_action( 'wp_print_styles', 'print_emoji_styles' );//Removing Emoji code from header
remove_action( 'admin_print_styles', 'print_emoji_styles' );//Removing Emoji code from header
// remove_action( 'load-update-core.php', 'wp_update_plugins' ); //Removes the Update Plugins option from WordPress
// add_filter( 'pre_site_transient_update_plugins', create_function( '$a', "return null;" ) ); //Removes the Update Plugins option from WordPress
// add_filter( 'pre_site_transient_update_core', create_function( '$a', "return null;" ) ); //Remove the "Please Update Now" option from WordPress


// Add Filters
add_filter('avatar_defaults', 'nudevgravatar'); // Custom Gravatar in Settings > Discussion
add_filter('body_class', 'add_slug_to_body_class'); // Add slug to body class (Starkers build)
add_filter('widget_text', 'do_shortcode'); // Allow shortcodes in Dynamic Sidebar
add_filter('widget_text', 'shortcode_unautop'); // Remove <p> tags in Dynamic Sidebars (better!)
add_filter('wp_nav_menu_args', 'my_wp_nav_menu_args'); // Remove surrounding <div> from WP Navigation
// add_filter('nav_menu_css_class', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> injected classes (Commented out by default)
// add_filter('nav_menu_item_id', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> injected ID (Commented out by default)
// add_filter('page_css_class', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> Page ID's (Commented out by default)
add_filter('the_category', 'remove_category_rel_from_category_list'); // Remove invalid rel attribute
add_filter('the_excerpt', 'shortcode_unautop'); // Remove auto <p> tags in Excerpt (Manual Excerpts only)
add_filter('the_excerpt', 'do_shortcode'); // Allows Shortcodes to be executed in Excerpt (Manual Excerpts only)
add_filter('excerpt_more', 'nudev_blank_view_article'); // Add 'View Article' button instead of [...] for Excerpts
add_filter('show_admin_bar', 'remove_admin_bar'); // Remove Admin bar
add_filter('style_loader_tag', 'nudev_style_remove'); // Remove 'text/css' from enqueued stylesheet
add_filter( 'script_loader_src', '_remove_script_version', 15, 1 ); // Removes scripts version number from script tags
add_filter( 'style_loader_src', '_remove_script_version', 15, 1 ); // Removes scripts version number from style tags
add_filter('post_thumbnail_html', 'remove_thumbnail_dimensions', 10); // Remove width and height dynamic attributes to thumbnails
add_filter('post_thumbnail_html', 'remove_width_attribute', 10 ); // Remove width and height dynamic attributes to post images
add_filter('image_send_to_editor', 'remove_width_attribute', 10 ); // Remove width and height dynamic attributes to post images
add_filter( 'wpforms_manage_cap', 'wpforms_custom_capability' );
add_filter( 'clean_url', 'nudev_async_scripts', 11, 1 );
add_filter('comments_array', 'df_disable_comments_hide_existing_comments', 10, 2);
add_filter('comments_open', 'df_disable_comments_status', 20, 2);
add_filter('pings_open', 'df_disable_comments_status', 20, 2);
add_filter( 'login_headerurl', 'my_login_logo_url' );
add_filter( 'login_headertitle', 'my_login_logo_url_title' );
add_filter( 'acf_the_content', 'wp_make_content_images_responsive' );
add_filter ( 'manage_nualerts_posts_columns', 'add_nualerts_acf_columns' );


// Remove Filters
remove_filter('the_excerpt', 'wpautop'); // Remove <p> tags from Excerpt altogether


// Add Shortcodes
add_shortcode('nudev_shortcode_demo', 'nudev_shortcode_demo'); // You can place [nudev_shortcode_demo] in Pages, Posts now.
add_shortcode('nudev_shortcode_demo_2', 'nudev_shortcode_demo_2'); // Place [nudev_shortcode_demo_2] in Pages, Posts now.

// Shortcodes above would be nested like this -
// [nudev_shortcode_demo] [nudev_shortcode_demo_2] Here's the page title! [/nudev_shortcode_demo_2] [/nudev_shortcode_demo]

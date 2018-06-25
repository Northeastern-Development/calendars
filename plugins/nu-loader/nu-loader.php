<?php
/**
* Plugin Name: NU Loader
* Plugin URI: http://brand.northeastern.edu/wp/plugins/loader
* Description: This plugin adds global university system functionality to your site, including: styles, super nav, utility nav, and footer.  If needed, this plugin will automatically add custom hooks to various locations within your sites theme files.  This is to allow custom content to be delivered and shown according to university brand guidelines.
* Version: 1.0.0
* Author: Northeastern Univerity System
* Author URI: http://www.northeastern.edu/externalaffairs/
* License: GPL2
*/

//$resourcesUrl = array('http://newnu.nudev.net');
$resourcesUrl = array('https://www.northeastern.edu');

//$liveUrl  = "https://www.northeastern.edu";
//$devUrl   = "http://dynamicnav.nudev.net";
//$devDataUrl  = "http://newnu.nudev.net";




/* ***********************************************************************

FUNCTION: PLUGIN SETTINGS PAGE

Description


Inputs:
NA

Outputs:
updated NU Loader Setting/options page

*********************************************************************** */


function nuloader_add_admin_menu(  ) {
 add_menu_page( 'NU Loader Settings', 'NU Loader', 'manage_options', 'nu_loader', 'nuloader_options_page', plugin_dir_url( __FILE__ ) . '_ui/n.png' );
}



if ( is_admin() ){ // admin actions
 add_action( 'admin_menu', 'nuloader_add_admin_menu' );//adds menu item to wp dashboard
 add_action( 'admin_init', 'register_mysettings' );
}

function register_mysettings() { // whitelist options
  register_setting( 'nu-loader-settings', 'global_material_icons' );
  register_setting( 'nu-loader-settings', 'global_header' );
  register_setting( 'nu-loader-settings', 'global_footer' );
}



 function nuloader_options_page() {
  ?>
    <div class="wrap">
      <?php settings_errors(); ?>
      <h1>NU Loader Settings</h1><br>
      <h3>Check off what you'd like to load into your site below.</h3>
      <form action="options.php" method="post">
        <?php
        settings_fields( 'nu-loader-settings' );
        do_settings_sections( 'nuloader_options_page' );
        ?>
        <table class="form-table">

          <tr valign="top">
            <th scope="row">Assets Needed</th>

            <td valign="top">
              <label>
                <input type="checkbox" name="global_material_icons" <?php echo esc_attr( get_option('global_material_icons') ) == 'on' ? 'checked="checked"' : ''; ?> />Global Google Material Icons?
              </label><br/>
              <p class="description" id="tagline-description">The <strong>Global Northeastern Header</strong> requires Google Material Icons.  If your theme is not loading them please check the box above.</p>
            </td>
          </tr>

          <tr valign="top">
            <th scope="row">Loader Options</th>
            <td valign="top">
              <label>
                <input type="checkbox" name="global_header" <?php echo esc_attr( get_option('global_header') ) == 'on' ? 'checked="checked"' : ''; ?> />Global Northeastern Header?
              </label><br/>
              <label>
                <input type="checkbox" name="global_footer" <?php echo esc_attr( get_option('global_footer') ) == 'on' ? 'checked="checked"' : ''; ?> />Global Northeastern Footer?
              </label>
            </td>
          </tr>

          <tr>
            <td><?php submit_button(); ?></td>
          </tr>

        </table>
        <h2>Need Help?</h2>
        <div id="nu_settings-help">
          If you need help or something isn't working please <a href="mailto:nudev@northeastern.edu?subject=NU Loader Plugin Help">contact us</a>.

        </div>

      </form>

    </div>
  <?php
 }

/* END PLUGIN SETTINGS PAGE ******************************************************** */



// specify base urls to be used in the code below to speed up making edits
// $baseUrls = array('http://newnu.local');
//
// echo get_site_url();







/* ***********************************************************************

FUNCTION: nu_check_plugin

Description
This function will check to ensure that the proper custom hooks are in
place within the template files of the site and add them if needed.

Inputs:
NA

Outputs:
updated wp theme files

*********************************************************************** */
function nu_check_plugin(){

  // check for the custom header hook and add it if needed
  if(!has_action('wp_globalheader', 'nu_header_block')){

    echo "WARNING: you need to reload your page now for the NU Loader plugin to be fully activated.";

    // we need to read the header.php file into memory to save the contents
    $p = get_template_directory()."/header.php";
    $f = fopen($p, "r") or die("Unable to open file!");
    $d = fread($f,filesize($p));
    fclose($f);

    // add in the custom hook by updating the code of header.php
    $d = str_replace('<header','<?php if(function_exists("wp_globalheader")){wp_globalheader();} ?><header',$d);
    $f = fopen($p, "w+") or die("Unable to open file!");
    fwrite($f,$d);
    fclose($f);

  }

  if(!has_action('wp_globalfooter', 'nu_footer_block')){

    //echo "WARNING: you need to reload your page now for the NU Loader plugin to be fully activated.";

    // we need to read the header.php file into memory to save the contents
    $p = get_template_directory()."/footer.php";
    $f = fopen($p, "r") or die("Unable to open file!");
    $d = fread($f,filesize($p));
    fclose($f);

    // add in the custom hook by updating the code of header.php

    $d = str_replace('</footer>','</footer><?php if(function_exists("wp_globalfooter")){wp_globalfooter();} ?>',$d);
    $f = fopen($p, "w+") or die("Unable to open file!");
    fwrite($f,$d);
    fclose($f);

  }

}
/* END FUNCTION ******************************************************** */







/* ***********************************************************************

FUNCTION: SCRIPTS & STYLES

Description
Css and Js files to be added

Inputs:
NA

Outputs:
updated wp theme files

*********************************************************************** */

function nu_headerfooterstyles(){
  global $resourcesUrl;

  echo '<link  rel="stylesheet" id="global-header-footer-css"  href="'.$resourcesUrl[0].'/nuglobalutils/common/css/headerfooter.css" /> ';
  echo "\n";
}

function nu_materialicons(){
  global $resourcesUrl;
  echo '<link  rel="stylesheet" id="global-font-css"  href="'.$resourcesUrl[0].'/nuglobalutils/common/css/material-icons.css"/> ';
  echo "\n";
}

function nu_headerstyles() {
  global $resourcesUrl;
  echo '<link  rel="stylesheet" id="global-header-style-css"  href="'.$resourcesUrl[0].'/nuglobalutils/common/css/utilitynav.css"  /> ';
  echo "\n";
}

function nu_footerstyles() {
  global $resourcesUrl;
  echo '<link  rel="stylesheet" id="global-footer-style-css"  href="'.$resourcesUrl[0].'/nuglobalutils/common/css/footer.css"  /> ';
}

function nu_scripts() {
  global $resourcesUrl;
 echo '<script src="'.$resourcesUrl[0].'/nuglobalutils/common/js/navigation-min.js" /> ';
 echo "</script>";
 echo "\n";
}

// load google analytics and tag manager via this plugin as well?


// function nu_scripts(){
//   echo '<!-- this would load script calls -->';
// }

/* END FUNCTION ******************************************************** */










/* ***********************************************************************

FUNCTION: nu_header_content

Description
This function will check to ensure that the proper custom hooks are in
place within the template files of the site and add them if needed.

Inputs:
NA

Outputs:
updated wp theme files

*********************************************************************** */
function nu_header_block(){

  //global $baseUrls;

  // let's grab the utility nav from home base
  // $url = $baseUrls[0]."/resources/includes/?r=utility-nav";
  // $curl = curl_init($url);
  // curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
  // $utility = curl_exec($curl);
  // curl_close($curl);

  // let's grab the alerts (if any) from home base


  // need a check in here if the curl request errors

  // $alerts = '';
  //
  // $url = $baseUrls[0]."/feed/alerts";
  // $curl = curl_init($url);
  // curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
  // $xml = curl_exec($curl);
  // // echo 'XML: '.$xml;
  // if(curl_errno($curl)){
  //   echo 'CURL Request Error: '.curl_error($curl);
  //   curl_close($curl);
  // }else if($xml != ''){
  //   curl_close($curl);
  //   // echo 'XML: '.$xml;
  //   $xml = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA);
  //
  //   $json = json_encode($xml);
  //   $res = json_decode($json,TRUE)['channel']['items'];
  //
  //   // print_r($res);
  //
  //   // $alerts = '';
  //
  //   // if we have alerts, build out the alerts panel
  //   if(!isset($res[0])){
  //     $alerts .= '<br /><div><h2>University Alert!</h2><p>Northeastern University has issued the following alert(s). Please be sure to read any associated information and contact your campus emergency services with questions.</p><ul>';
  //
  //     foreach($res as $r){
  //       $guide = '<li><a href="%s" title="%s, read more">%s For: %s - %s&nbsp;</a></li>';
  //
  //                                               $campus = "";
  //       $campuses = explode(", ",$r['campuses']);
  //                                               foreach($campuses as $c){
  //                                                               $campus .= ($campus != ""?", ":"").$c;
  //                                               }
  //
  //                                               $alerts .= sprintf(
  //                                                                $guide
  //                                                               ,$r['link']
  //                                                               ,$r['title']
  //                                                               ,$r['title']
  //                                                               ,$campus
  //                                                               ,$r['description']
  //                                               );
  //     }
  //     $alerts .= '</ul></div>';
  //   }
  // }
  //
  // echo '<div id="nu__alerts">'.$alerts.'</div>';
  //
  // wp_reset_postdata();
  //               wp_reset_query();
}
/* END FUNCTION ******************************************************** */


function nu_footer_block(){

}


/* ***********************************************************************

FUNCTION: nu_global_footer

Description
This function will make a call to the resource center on edu and echo the
returned footer content to the footer area of the page

Inputs:
NA

Outputs:
HTML content

*********************************************************************** */
function nu_global_footer(){

  // need a back up of local content in case the resource cannot be reached for some reason

  //global $baseUrls;
  global $resourcesUrl;

  $url = ''.$resourcesUrl[0].'/resources/includes/?return=footer';
  // $url = $resourcesUrl[0].'/resources/components/?return=footer';

  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
  echo '<div id="nu__global-footer">'.curl_exec($curl).'</div>';
  curl_close($curl);

}
/* END FUNCTION ******************************************************** */





/* ***********************************************************************

FUNCTION: nu_supernav

Description
This function will make a call to the resource center on edu and echo the
returned supernav into a utility area at the top of the page

Inputs:
NA

Outputs:
HTML content

*********************************************************************** */
function nu_supernav(){

  //global $baseUrls;
  global $resourcesUrl;

  // this is an example of the global style utility supernav that can be pulled into any requesting site
  $url = ''.$resourcesUrl[0].'/resources/components/?return=main-menu';
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
  echo '<div id="nu__globalheader">'.curl_exec($curl).'</div>';
  curl_close($curl);

}
/* END FUNCTION ******************************************************** */







/* ***********************************************************************

FUNCTION: Outputs data, scripts, and css based on what the user checks from the setting page

Description


Inputs:
NA

Outputs:
HTML content

*********************************************************************** */

// this will add a monitor tag to the header for tracking purposes
function nu_set_keys(){
  // echo '<!-- NU Global Footer, v1.0.0 [key:'.md5(get_home_url().'-footer-'.date("Ymd")).'] -->';
}




// need a better, more tamper-resistant way to provide tracking and monitoring abilities


// set up the custom hook for the header area
function wp_globalheader(){
  add_action('wp_globalheader', 'nu_header_block');
  do_action('wp_globalheader');
}

$materialIconsOption = get_option('global_material_icons');//comes from option settings at the top of file
$headerOption = get_option('global_header');//comes from option settings at the top of file
$footerOption = get_option('global_footer');//comes from option settings at the top of file


//IF BOTH OPTIONS ARE CHECKED WE ARE DELIVERING 1 STYLE SHEET INSTEAD OF 3
if (($headerOption == 'on') && ($footerOption == 'on')){

   add_action('wp_globalheader','nu_supernav');//DATA
   add_action('wp_globalfooter','nu_global_footer');//DATA
   add_action('wp_head', 'nu_headerfooterstyles');//CSS
   add_action('wp_footer','nu_scripts');//JS
}

//IF THE HEADER OPTION IS CHECKED WE DELIVER JUST THE HEADER CSS AND 1 JS FILE
if (($headerOption == 'on') && ($footerOption == '')){
  if ($materialIconsOption == 'on'){
    add_action('wp_head', 'nu_materialicons');//CSS
  }
  add_action('wp_globalheader','nu_supernav');//DATA
  add_action('wp_head','nu_headerstyles');//CSS
  add_action('wp_footer','nu_scripts');//JS
}

//IF THE FOOTER OPTION IS CHECKED WE DELIVER JUST THE FOOTER CSS
if (($footerOption == 'on') && ($headerOption == '')){
  add_action('wp_globalfooter','nu_global_footer');//DATA
  add_action('wp_head','nu_footerstyles');//CSS
}



function wp_globalfooter(){
  add_action('wp_globalfooter', 'nu_footer_block');
  do_action('wp_globalfooter');
}

// add the action to calls to the main wp functions file


// the scripts and css that we will pull in should only be a subset of what is absolutely required????????????????????????????????


// add_action('wp_head','nu_set_keys');




//Upon activating of the plugin this will take you to the setting page.
register_activation_hook(__FILE__, 'nu_loader_plugin_activate');
add_action('admin_init', 'nu_loader_plugin_redirect');

function nu_loader_plugin_activate() {
add_option('nu_loader_plugin_do_activation_redirect', true);
}

function nu_loader_plugin_redirect() {
if (get_option('nu_loader_plugin_do_activation_redirect', false)) {
    delete_option('nu_loader_plugin_do_activation_redirect');
    if(!isset($_GET['activate-multi']))
    {
        wp_redirect("admin.php?page=nu_loader");
    }
 }
}

/* END FUNCTION ******************************************************** */


// let's check the plugin to make sure that we have everything we need
if(!is_admin()){
  add_action('wp_footer','nu_check_plugin');
}

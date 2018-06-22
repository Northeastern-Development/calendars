<?php

/* **********************************************************************
TinyMCE Configuration
********************************************************************** */

/**
 * @param array $plugins An array of all plugins.
 * @return array
 */

function mce_custom_plugins( $mceplugins ){
  $mceplugins['lineheight'] = plugins_url( 'tinymce/') . 'lineheight/plugin.js';
  return $mceplugins;
}
// add_filter( 'mce_external_plugins', 'mce_custom_plugins' );


if ( ! function_exists( 'wpex_mce_lineheights' ) ) {
    function wpex_mce_lineheights( $initArray ){
      $fSizes = "";
      for($i=9;$i<=100;$i++){
        $fSizes .= ($i>9?" ":"").$i."px";
      }
      $initArray['lineheight_formats'] = $fSizes;
      return $initArray;
    }
}
// add_filter( 'tiny_mce_before_init', 'wpex_mce_lineheights' );

if ( ! function_exists( 'load_custom_fonts' ) ) {
    function load_custom_fonts( $initArray ){
      // $fSizes = "";
      // for($i=9;$i<=100;$i++){
      //   $fSizes .= ($i>9?" ":"").$i."px";
      // }
      // $initArray['fontsize_formats'] = $fSizes;

      //$theme_advanced_fonts = array("Tungsten");

      //$initArray['$theme_advanced_fonts'] = 'Aclonica=Aclonica, sans-serif;';

      // $init['content_css'] = "https://fonts.googleapis.com/css?family=Lato";
      // $initArray['content_css'] = "https://cloud.typography.com/7862092/6820572/css/fonts.css";
      //
      // $initArray['font_formats'] = "Tungsten='Tungsten Rounded A','Tungsten Rounded B'";

      // $initArray['formats'] = "bold: { inline: 'span', 'classes': 'bold' }";

      // $initArray['style_formats'] = json_encode(array("title"=>"bold","inline"=>"span","classes"=>"bold"));

      $cFormats = array(
       'bold' => array('inline' => 'span',"classes"=>"boldit")
      );

      $initArray['formats'] = json_encode($cFormats);


      //function wpex_mce_text_sizes( $initArray ){
        $fSizes = "";
        for($i=9;$i<=100;$i++){
          $fSizes .= ($i>9?" ":"").$i."px";
        }
        $initArray['fontsize_formats'] = $fSizes;
        //return $initArray;

      return $initArray;
    }
}
add_filter( 'tiny_mce_before_init', 'load_custom_fonts' );

// if ( ! function_exists( 'wpex_mce_text_sizes' ) ) {
//     function wpex_mce_text_sizes( $initArray ){
//       $fSizes = "";
//       for($i=9;$i<=100;$i++){
//         $fSizes .= ($i>9?" ":"").$i."px";
//       }
//       $initArray['fontsize_formats'] = $fSizes;
//       return $initArray;
//     }
// }
// add_filter( 'tiny_mce_before_init', 'wpex_mce_text_sizes' );


function thisismyurl_remove_full_screen_button( $buttons ) {

	if ( in_array( 'fullscreen', $buttons ) )
		$buttons = array_diff( $buttons, array( 'fullscreen') );

	return $buttons;

}
add_filter( 'mce_buttons', 'thisismyurl_remove_full_screen_button', 999 );




function my_mce_buttons_line_2( $buttons ) {

  // array_unshift( $buttons, 'fontselect |  fontsizeselect | lineheightselect' );
  array_push( $buttons, 'fontselect','fontsizeselect','lineheightselect' );
  return $buttons;
}
add_filter( 'mce_buttons_2', 'my_mce_buttons_line_2' );

?>

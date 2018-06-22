<?php





/* **********************************************************************
Page Conditional Scripts
********************************************************************** */
function nudev_conditional_scripts(){

  // we need to load the main js on all pages of the site
  wp_register_script('mainjs', get_template_directory_uri() . '/js/scripts-min.js', array(), '1.0.0');
  wp_enqueue_script('mainjs');


  // if(is_page('')){
  if(is_page_template('templates/template-homepage.php')){
    //wp_register_script('mousewheeljs', get_template_directory_uri() . '/js/jquery.mousewheel-min.js', array(), '1.0.0');
    //wp_enqueue_script('mousewheeljs');
    //wp_register_script('hammerjs', get_template_directory_uri() . '/js/hammer-min.js', array(), '1.0.0');
    //wp_enqueue_script('hammerjs');
    //wp_enqueue_script('tweenlitecss', get_template_directory_uri() . '/js/CSSPlugin.min.js', array(),'1.20.4');
  	//wp_enqueue_script('tweenliteease', get_template_directory_uri() . '/js/EasePack.min.js', array(),'1.20.4');
  	//wp_enqueue_script('tweenlite', get_template_directory_uri() . '/js/TweenLite.min.js', array(),'1.20.4');
    //wp_register_script('homepagejs', get_template_directory_uri() . '/js/homepage-min.js', array(), '1.0.0');
    //wp_enqueue_script('homepagejs');
  }


}

/* ******************************************************************* */

?>

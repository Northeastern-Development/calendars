<?php

/* **********************************************************************
Theme Navigation
********************************************************************** */
function nudev_nav(){
    wp_nav_menu(
    array(
        'theme_location'  => 'header-menu',
        'menu'            => '',
        'container'       => 'nav',
        'container_class' => 'menu-{menu slug}-container',
        'container_id'    => '',
        'menu_class'      => 'menu',
        'menu_id'         => '',
        'echo'            => true,
        'fallback_cb'     => 'wp_page_menu',
        'before'          => '',
        'after'           => '',
        'link_before'     => '',
        'link_after'      => '',
        'items_wrap'      => '<ul>%3$s</ul>',
        'depth'           => 0,
        'walker'          => ''
        )
    );
}

//Adds active class to current page main menu item
add_filter('nav_menu_css_class' , 'special_nav_class' , 10 , 2);
function special_nav_class($classes, $item){

  // $checkNav = array("people","ideas","news","about","submit-a-story");
  $checkNav = array("marketing","communications","government-relations","staff","resources","contact");

  $postCats = get_the_category();

  foreach($checkNav as $cN){
    if(str_replace(array(" + "," "),"-",strtolower($item->title)) === $cN){
      foreach($postCats as $pC){
        $test = $pC;
        if(strtolower($test->slug) === $cN){
          $classes[] = 'current-menu-item';
          // break;
        }
      }
    }
  }
  return $classes;
}





// Register nudev Navigation
function register_nudev_menu()
{
    register_nav_menus(array( // Using array to specify more menus if needed
        'header-menu' => __('Header Menu', 'nudev'), // Main Navigation
        //'sidebar-menu' => __('Sidebar Menu', 'nudev'), // Sidebar Navigation
        //'extra-menu' => __('Extra Menu', 'nudev') // Extra Navigation if needed (duplicate as many as you need!)
    ));
}

/* ******************************************************************* */

// Remove the <div> surrounding the dynamic navigation to cleanup markup
function my_wp_nav_menu_args($args = '')
{
    $args['container'] = false;
    return $args;
}

?>

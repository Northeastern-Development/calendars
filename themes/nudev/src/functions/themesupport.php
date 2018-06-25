<?php

/*------------------------------------*\
    Theme Support
\*------------------------------------*/



// set a default content width if not already defined
if (!isset($content_width)){
    $content_width = 1080;
}


// load custom theme options
if (function_exists('add_theme_support')){

    // Add Thumbnail Theme Support
    add_theme_support('post-thumbnails');
    add_image_size('large', 700, '', true); // Large Thumbnail
    add_image_size('medium', 250, '', true); // Medium Thumbnail
    add_image_size('small', 120, '', true); // Small Thumbnail
    add_image_size('custom-size', 700, 200, true); // Custom Thumbnail Size call using the_post_thumbnail('custom-size');







    // Add Support for Custom Backgrounds - Uncomment below if you're going to use
    /*add_theme_support('custom-background', array(
    'default-color' => 'FFF',
    'default-image' => get_template_directory_uri() . '/img/bg.jpg'
    ));*/

    // Add Support for Custom Header - Uncomment below if you're going to use
    /*add_theme_support('custom-header', array(
    'default-image'          => get_template_directory_uri() . '/img/headers/default.jpg',
    'header-text'            => false,
    'default-text-color'     => '000',
    'width'                  => 1000,
    'height'                 => 198,
    'random-default'         => false,
    'wp-head-callback'       => $wphead_cb,
    'admin-head-callback'    => $adminhead_cb,
    'admin-preview-callback' => $adminpreview_cb
    ));*/

    // Enables post and comment RSS feed links to head
    // add_theme_support('automatic-feed-links');

    // Enable nudev support
    // add_theme_support('nudev', array('comment-list', 'comment-form', 'search-form', 'gallery', 'caption'));

    // Localisation Support
    // load_theme_textdomain('nudev', get_template_directory() . '/languages');
}

?>

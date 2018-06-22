<?php

  register_taxonomy_for_object_type('category', 'Administration'); // Register Taxonomies for Category
  register_taxonomy_for_object_type('post_tag', 'Administration');
  register_post_type('administration', // Register Custom Post Type
    array(
    'labels' => array(
      'name' => __('Administration', 'nudev'), // Rename these to suit
      'singular_name' => __('Administration', 'nudev'),
      'add_new' => __('Add New', 'nudev'),
      'add_new_item' => __('Add New', 'nudev'),
      'edit' => __('Edit', 'nudev'),
      'edit_item' => __('Edit', 'nudev'),
      'new_item' => __('New Administration Member', 'nudev'),
      'view' => __('View Administration', 'nudev'),
      'view_item' => __('View Administration', 'nudev'),
      'search_items' => __('Search Administration', 'nudev'),
      'not_found' => __('No Administration found', 'nudev'),
      'not_found_in_trash' => __('No Administration found in Trash', 'nudev')
    ),
    'public' => true,
    'hierarchical' => false, // Allows your posts to behave like Hierarchy Pages
    'has_archive' => false,
    'rewrite' => array(
      'with_front' => false,
      'slug'       => 'alerts'
    ),
    'supports' => array(
      'title',
      'editor',
      'excerpt'
    ), // Go to Dashboard Custom nudev post for supports
    'can_export' => false, // Allows export in Tools > Export
  ));

  // Add columns to administration post listing
  function add_administration_acf_columns ( $columns ) {
    $slice1 = array_slice($columns, 0, 2, true);
    $slice2 = array_slice($columns, 2, count($columns), true);
    return array_merge($slice1,array('type' => __ ( 'Type' )),array('department' => __ ( 'Department' )),array('sub-type' => __ ( 'Sub-Type' )),$slice2);
  }


  function administration_custom_column ( $column, $post_id ) {
    switch ( $column ) {
      case 'department':
        $depts = get_post_meta ( $post_id, 'department', true );
        if(count($depts) > 1){ // they are in more than one dept
          $v = '';
          foreach($depts as $d){  // loop through and grab each department that this person is part of
            $v .= ($v != ''?', '.$d:$d);
          }
          echo $v;
        }else{  // they are only in one dept
          echo $depts;
        }
        break;
      case 'type':
        echo get_post_meta ( $post_id, 'type', true );
        break;
      case 'sub-type':
        echo get_post_meta ( $post_id, 'sub_type', true );
        break;
    }
  }

  // add filter options
  function administration_admin_posts_filter_restrict_manage_posts(){
    global $typenow;
    $type = 'administration';

    if ($typenow == $type){

      // get the full list of departments from the acf fields
      //$field = get_field_object('field_5a5e2ca106a03');
      //print_r($field['choices']);
      // $values = $field['choices'];


      $current_v = isset($_GET['ADMIN_FILTER_FIELD_VALUE'])? $_GET['ADMIN_FILTER_FIELD_VALUE']:'';
      $guide = '<option value="%s"%s>%s</option>';

      // hardcoded values for now, there is an issue retrieving them again after the first filter
      $values = array(
         'Advancement' => 'Advancement'
        ,'External Affairs' => 'External Affairs'
        ,'Finance' => 'Finance'
        ,'General Counsel' => 'General Counsel'
        ,'President' => 'President'
        ,'Professional Advancement Network' => 'Professional Advancement Network'
        ,'Provost' => 'Provost'
        ,'Strategy' => 'Strategy'
      );
?>
      <select name="ADMIN_FILTER_FIELD_VALUE"><option value=""><?php _e('Filter By Department', 'department'); ?></option>

<?php

      foreach ($values as $label => $value){
        printf(
           $guide
          ,$value
          ,$value == $current_v? ' selected="selected"':''
          ,$label
        );
      }
?>
      </select>
<?php
    }
  }


  function administration_posts_filter( $query ){
    global $pagenow;
    global $typenow;
    $type = 'administration';
    if ( $typenow == $type && is_admin() && $pagenow=='edit.php' && isset($_GET['ADMIN_FILTER_FIELD_VALUE']) && $_GET['ADMIN_FILTER_FIELD_VALUE'] != ''){
        $query->query_vars['meta_value'] = $_GET['ADMIN_FILTER_FIELD_VALUE'];
    }
  }

  add_filter ( 'manage_administration_posts_columns', 'add_administration_acf_columns' );
  add_action ( 'manage_administration_posts_custom_column', 'administration_custom_column', 10, 2 );

  add_action( 'restrict_manage_posts', 'administration_admin_posts_filter_restrict_manage_posts' );
  add_filter( 'parse_query', 'administration_posts_filter' );

?>

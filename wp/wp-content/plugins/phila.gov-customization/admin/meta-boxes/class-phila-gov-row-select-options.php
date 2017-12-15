<?php
/**
 * Register templates for use on the front-end
 *
 */

if ( class_exists( "Phila_Gov_Row_Select_Metaboxes" ) ){
  $admin_menu_labels = new Phila_Gov_Row_Select_Metaboxes();
}

class Phila_Gov_Row_Select_Metaboxes {

  public static function phila_metabox_full_options_select(){
    return array(
      'id'   => 'phila_full_options_select',
      'desc'  => 'Choose to display full width content.',
      'type' => 'select',
      'placeholder' => 'Select...',
      'options' => array(
        'phila_blog_posts' => 'Blog Posts',
        'phila_full_width_calendar' => 'Calendar',
        'phila_callout' => 'Callout',
        'phila_custom_text' => 'Custom Text',
        'phila_feature_p_i' => 'Feature Program or Initiative',
        'phila_get_involved' => 'Get Involved',
        'phila_list_items' => 'List Items',
        'phila_full_width_press_releases' => 'Press Releases',
        'phila_resource_list' => 'Resource List',
      ),
    );
  }

  // Grid Options
  public static function phila_metabox_grid_options( ){
    return array(
    'name' => 'Row Layout',
    'id'   => 'phila_grid_options',
    'desc'  => 'Choose the row layout.',
    'type' => 'select',
    'placeholder' => 'Select...',
    'options' => array(
      'phila_grid_options_full' => 'Full Width',
      'phila_grid_options_half' => '1/2 x 1/2',
      'phila_grid_options_thirds' => '2/3 x 1/3',
      ),
    );
  }

}

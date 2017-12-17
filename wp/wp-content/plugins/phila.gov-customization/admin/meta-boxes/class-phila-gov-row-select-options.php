<?php
/**
 * Register templates for use on the front-end
 *
 */

if ( class_exists( "Phila_Gov_Row_Select_Options" ) ){
  $admin_menu_labels = new Phila_Gov_Row_Select_Options();
}

class Phila_Gov_Row_Select_Options {

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
        'phila_full_cta'  => 'Call to action (full)',
        'phila_custom_text' => 'Custom Text',
        'phila_feature_p_i' => 'Feature Program or Initiative',
        'phila_get_involved' => 'Get Involved',
        'phila_list_items' => 'List Items',
        'phila_full_width_press_releases' => 'Press Releases',
        'phila_resource_list' => 'Resource List',
        'phila_image_list' => 'Image list',
        'phila_link_list' => 'Link list'
      ),
    );
  }

  // 2/3 x 1/3 Options
  public static function phila_metabox_thirds_options( ){

    return array(
      'name' => '2/3 x 1/3 Options',
      'id'   => 'phila_two_thirds_options',
      'type' => 'group',
      'revision' => true,

      'hidden' => array(
        'phila_grid_options',
        '!=',
        'phila_grid_options_thirds'
    ),
      'fields' => array(
        Phila_Gov_Row_Metaboxes::phila_metabox_thirds_option_one(),
        Phila_Gov_Row_Metaboxes::phila_metabox_thirds_option_two(),
      ),
    );
  }

  public static function phila_metabox_half_options( ){

    return array(
      'name' => '1/2 x 1/2 Options',
      'id'   => 'phila_half_options',
      'type' => 'group',
      'hidden' => array(
        'phila_grid_options',
        '!=',
        'phila_grid_options_half'
      ),
      'fields' => array(
        Phila_Gov_Row_Metaboxes::phila_metabox_half_option_one(),
        Phila_Gov_Row_Metaboxes::phila_metabox_half_option_two(),
      ),
    );
  }
}

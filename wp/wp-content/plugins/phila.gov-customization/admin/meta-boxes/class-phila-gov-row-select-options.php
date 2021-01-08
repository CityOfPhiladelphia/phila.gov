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
      'desc'  => 'Choose full width content.',
      'type' => 'select',
      'class' => 'percent-100',
      'placeholder' => 'Select full-width row module...',
      'options' => array(
        'phila_content_additional_content' => 'Additional Content',
        'phila_blog_posts' => 'Blog posts',
        'phila_board_commission'  => 'Board or commission members',
        'phila_full_width_calendar' => 'Calendar',
        'phila_full_cta'  => 'Call to action (single, full)',
        'phila_resource_list' => 'Call to action (multiple)',
        'phila_callout' => 'Callout',
        'phila_custom_text' => 'Custom text',
        'phila_custom_text_multi' => 'Custom text (multiple)',
        'phila_faq' => 'FAQ',
        'phila_prereq' => 'FAQ list with icon (prereq approval style)',
        'phila_feature_p_i' => 'Featured page content',
        'phila_get_involved' => 'Get Involved',
        'phila_image_list' => 'Image list',
        'phila_content_heading_group' => 'Heading group',
        'phila_heading_one_quarter_select' => '1/4 Heading group',
        'phila_list_items' => 'List items (grid)',
        'phila_location_list' => 'Location List',
        'phila_photo_callout' => 'Photo callout',
        'phila_full_width_press_releases' => 'Press Releases',
        'phila_programs'  => 'Program cards',
        'phila_registration'  => 'Registration available',
        'phila_service_updates' => 'Service updates',
        'phila_staff_table' => 'Staff table',
        'phila_stepped_content' => 'Stepped content',
        'phila_homepage_timeline' => 'Timeline',
        'phila_vue_app'  => 'Vue app',
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
  
  public static function phila_metabox_tabbed_select(){
    return array(
      'id'   => 'phila_tabbed_select',
      'desc'  => 'Choose tabbed content.',
      'type' => 'select',
      'class' => 'percent-100',
      'placeholder' => 'Select tabbed row module...',
      'options' => array(
        'phila_metabox_tabbed_single_wysiwyg'  => 'Single wysiwyg',
        'phila_metabox_tabbed_repeater_wysiwyg'  => 'Repeater wysiwyg',
        'phila_metabox_tabbed_stepped_content'  => 'Stepped content',
        'phila_metabox_tabbed_timeline_content' => 'Timeline content'
      ),
    );
  }

  public static function phila_metabox_tabbed_single_wysiwyg( ){

    return array (
      Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_advanced_small_wysiwyg('Single wysiwyg'),
      array(
        'id' => 'include_anchor_links',
        'name'  => 'Include anchor links?',
        'desc' => 'Adds links to wysiwyg titles and single title rows',
        'type' => 'switch',
        'on_label'  => 'Yes',
        'off_label' => 'No'
      )
    );
  }

  public static function phila_metabox_tabbed_repeater_wysiwyg( ){

    return array(
      array(
        'id' => 'step_repeater_wysiwyg',
        'name'  => 'Repeater wysiwyg',
        'type' => 'group',
        'clone'  => true,
        'sort_clone' => true,
        'fields' => array(
          array(
            'name'  => 'Tab wysiwyg section',
            'id'  =>  'phila_custom_wysiwyg',
            'type'  => 'group',
            'clone' => false,
            'columns'=> 12,
            'fields'  => array(
              Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_wysiwyg_title(),
              Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_advanced_small_wysiwyg(),
            )
          )
        )
      ),
    );
  }

  public static function phila_metabox_tabbed_stepped_content( ){
    return array (
      array(
        'id' => 'phila_stepped_content',
        'name'  => 'Stepped content',
        'type' => 'group',
        'fields'  => array(
          Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_wysiwyg_title(),
          Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_advanced_small_wysiwyg(),
          Phila_Gov_Standard_Metaboxes::phila_metabox_v2_ordered_content()
        )
      )
    );
  }

  public static function phila_metabox_tabbed_timeline_content( ){
    return array (
      array(
        'id' => 'phila_timeline_content',
        'name'  => 'Timeline content',
        'type' => 'group',
        'fields'  => array(
          Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_wysiwyg_title(),
          Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_advanced_small_wysiwyg(),
          Phila_Gov_Standard_Metaboxes::phila_metabox_v2_timeline_repeater(),
        )
      )
    );
  }
}

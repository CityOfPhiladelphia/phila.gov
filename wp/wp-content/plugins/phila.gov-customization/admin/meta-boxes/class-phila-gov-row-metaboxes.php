<?php
/* Group Metabox row options */

if ( class_exists('Phila_Gov_Row_Metaboxes' ) ){
  $phila_standard_metaboxes_load = new Phila_Gov_Row_Metaboxes();
}

class Phila_Gov_Row_Metaboxes {

  public static function phila_metabox_grid_row (){
    return array(
    'id'    => 'phila_row',
    'class' => 'phila_row',
    'type'  => 'group',
    'clone' => true,
    'sort_clone' => true,
    'add_button'  => '+ Add row',
    'fields' => array(
      Phila_Gov_Row_Select_Options::phila_metabox_grid_options(),
      Phila_Gov_Row_Metaboxes::phila_metabox_full_options(),
      Phila_Gov_Row_Select_Options::phila_metabox_thirds_options(),
      Phila_Gov_Row_Select_Options::phila_metabox_half_options(),
      ),
    );
  }

  public static function phila_metabox_full_options( ){
    return array(
     'name' => '',
     'id'   => 'phila_full_options',
     'type' => 'group',
     'visible' => array(
       'phila_grid_options',
       '=',
       'phila_grid_options_full'
     ),
     'fields' => array(
       Phila_Gov_Row_Select_Options::phila_metabox_full_options_select(),
       array(
         'visible' => array('phila_full_options_select', '=', 'phila_blog_posts'),
         'id'  => 'phila_get_post_cats',
         'type' => 'group',
         'fields' => array(
           Phila_Gov_Standard_Metaboxes::phila_metabox_category_picker('Select new owner', 'phila_post_category', 'Display posts from these owners.' ),
           array(
             'name'  => 'Filter by a tag',
             'id'  => 'tag',
             'type' => 'taxonomy_advanced',
             'taxonomy'  => 'post_tag',
             'field_type' => 'select_advanced',
             'desc'  => 'Display posts using this tag. "See all" will pre-filter on these terms.'
           ),
         ),
        ),
      array(
        'id' => 'phila_full_width_calendar',
        'type' => 'group',
        'visible' => array('phila_full_options_select', '=', 'phila_full_width_calendar'),
        'fields' => Phila_Gov_Standard_Metaboxes::phila_metabox_v2_calendar_full(),
      ),
      array(
        'id'   => 'phila_callout',
        'type' => 'group',
        'visible' => array('phila_full_options_select', '=', 'phila_callout'),
        'fields' => Phila_Gov_Standard_Metaboxes::phila_meta_var_callout(),
      ),
      array(
        'id'   => 'phila_custom_text',
        'type' => 'group',
        'visible' => array('phila_full_options_select', '=', 'phila_custom_text'),
        'fields' => Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg_upgraded(),
      ),
      array(
        'id'   => 'phila_custom_text_multi_full',
        'type' => 'group',
        'visible' => array('phila_full_options_select', '=', 'phila_custom_text_multi'),
        'revision'  => true,
        'fields' =>   Phila_Gov_Standard_Metaboxes::phila_metabox_v2_textarea_multi(),
      ),
      array(
        'id'  => 'phila_call_to_action_multi',
        'type' => 'group',
        'visible' => array(
          'when' => array(
            array('phila_full_options_select', '=', 'phila_get_involved'),
            array('phila_full_options_select', '=', 'phila_resource_list'),
          ),
          'relation' => 'or',
        ),
        'fields' => Phila_Gov_Standard_Metaboxes::phila_meta_var_call_to_action_multi(),
     ),
     array(
      'id' => 'phila_full_width_cta',
      'type' => 'group',
      'visible' => array('phila_full_options_select', '=', 'phila_full_cta'),
      'fields' => Phila_Gov_Standard_Metaboxes::phila_meta_var_full_width_cta()
     ),
     array(
      'id'   => 'phila_list_items',
      'type' => 'group',
      'visible' => array('phila_full_options_select', '=', 'phila_list_items'),
      'fields' => Phila_Gov_Standard_Metaboxes::phila_meta_var_list_items(),
     ),
      array(
        'id'   => 'phila_feature_p_i',
        'type' => 'group',
       'visible' => array('phila_full_options_select', '=', 'phila_feature_p_i'),
        'fields' => Phila_Gov_Standard_Metaboxes::phila_meta_var_feature_programs_initiatives(),
      ),
      array(
        'id'       => 'phila_image_list',
        'type'     => 'group',
        'visible'  => array('phila_full_options_select', '=', 'phila_image_list'),
        'fields'   => array(
          array(
            'name'  => 'Image list heading (optional)',
            'id'    => 'title',
            'type'  => 'text',
          ),
          array(
            'name'  => 'List of images',
            'id'    => 'phila_image_list',
            'type'  => 'image_advanced'
          ),
        ),
      ),
      array(
        'id'  => 'full_width_press_releases',
        'type' => 'group',
        'visible'  => array('phila_full_options_select', '=', 'phila_full_width_press_releases'),
        'fields' => array(
          Phila_Gov_Standard_Metaboxes::phila_metabox_category_picker('Select owners', 'phila_press_release_category', 'Display press releases from these owners.' ),
          array(
            'name'  => 'Filter by a tag',
            'id'  => 'tag',
            'type' => 'taxonomy_advanced',
            'taxonomy'  => 'post_tag',
            'field_type' => 'select_advanced',
            'desc'  => 'Display press releases using this tag. "See all" will pre-filter on these terms.'
          ),
        ),
      ),
      array(
        'id'      => 'phila_registration',
        'type'    => 'group',
        'visible' => array('phila_full_options_select', '=', 'phila_registration'),
        'fields'  => Phila_Gov_Standard_Metaboxes::phila_meta_registration(),
      )
    ),
  );
}

  /**
  *
  * Begin 2/3 x 1/3 Column MetaBox content
  *
  **/

  // 2/3 x 1/3: Column 1 Options
  public static function phila_metabox_thirds_option_one( ){
    return array(
      'id' => 'phila_two_thirds_col',
      'type' => 'group',
      'fields' => array(
         array(
           'name' => 'Column 1 <br/><small>(2/3 width column)</small>',
           'id'   => 'phila_two_thirds_col_option',
           'desc'  => 'Choose to display recent blog posts or custom markup text.',
           'type' => 'select',
           'placeholder' => 'Select...',
           'options' => array(
             'phila_custom_text' => 'Custom Text',
             'phila_custom_text_multi' => 'Custom Text (multi)',
             ),
         ),
         array(
           'id'   => 'phila_custom_text',
           'type' => 'group',
           'visible' => array('phila_two_thirds_col_option', '=', 'phila_custom_text'),
           'revision' => true,
           'fields' => Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg_upgraded(),
         ),
         array(
           'id'   => 'phila_custom_text_multi',
           'type' => 'group',
           'visible' => array('phila_two_thirds_col_option', '=', 'phila_custom_text_multi'),
           'revision'  => true,
           'fields' => Phila_Gov_Standard_Metaboxes::phila_metabox_v2_textarea_multi(),
         ),
       ),
     );
   }

   // 2/3 x 1/3: Column 2 Options
   public static function phila_metabox_thirds_option_two( ){

    return array(
      'id' => 'phila_one_third_col',
      'type' => 'group',
      'fields' => array(
         array(
         'name' => 'Column 2 <br/><small>(1/3 width column)</small>',
         'id'   => 'phila_one_third_col_option',
         'desc'  => 'Choose to display recent blog posts or custom markup text.',
         'type' => 'select',
         'placeholder' => 'Select...',
         'options' => array(
           'phila_connect_panel' => 'Connect Panel',
           'phila_custom_feature' => 'Custom Feature Panel',
           'phila_custom_text' => 'Custom Text',
           ),
         ),
         array(
           'id' => 'phila_connect_panel',
           'type' => 'group',
           'hidden' => array('phila_one_third_col_option', '!=', 'phila_connect_panel'),
           'fields' => Phila_Gov_Standard_Metaboxes::phila_meta_var_connect(),
         ),
         array(
           'id'   => 'phila_custom_text',
           'type' => 'group',
           'visible' => array('phila_one_third_col_option', '=', 'phila_custom_text'),
           'fields' => Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg_upgraded(),
         ),
         array(
           'id'   => 'phila_custom_feature',
           'type' => 'group',
           'visible' => array('phila_one_third_col_option', '=', 'phila_custom_feature'),
           'fields' => Phila_Gov_Standard_Metaboxes::phila_meta_var_custom_feature(),
         ),
       ),
     );
   }

   // 1/2 x 1/2: Column 1 Options
  public static function phila_metabox_half_option_one( ){
    return array(
    'id' => 'phila_half_col_1',
    'type' => 'group',
    'fields' => array(
       array(
         'name' => 'Column 1 <br/><small>(1/2 width column)</small>',
         'id'   => 'phila_half_col_1_option',
         'desc'  => 'Choose to display recent blog posts or custom markup text.',
         'type' => 'select',
         'placeholder' => 'Select...',
         'options' => array(
           'phila_custom_text' => 'Custom Text',
           'phila_pullquote' => 'Pullquote',
           ),
       ),
       array(
         'id'   => 'phila_custom_text',
         'type' => 'group',
         'visible' => array('phila_half_col_1_option', '=', 'phila_custom_text'),
         'fields' => Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg_upgraded(),
       ),
       array(
         'id'   => 'phila_pullquote',
         'type' => 'group',
         'visible' => array('phila_half_col_1_option', '=', 'phila_pullquote'),
         'fields' => Phila_Gov_Standard_Metaboxes::phila_meta_var_pullquote(),
       ),
     ),
   );
  }
   // 1/2 x 1/2: Column 1 Options
   public static function phila_metabox_half_option_two( ){
    return array(
     'id' => 'phila_half_col_2',
     'type' => 'group',
     'fields' => array(
        array(
          'name' => 'Column 2 <br/><small>(1/2 width column)</small>',
          'id'   => 'phila_half_col_2_option',
          'desc'  => 'Choose to display recent blog posts or custom markup text.',
          'type' => 'select',
          'placeholder' => 'Select...',
          'options' => array(
            'phila_custom_text' => 'Custom Text',
            'phila_pullquote' => 'Pullquote',
            ),
        ),
        array(
          'id'   => 'phila_custom_text',
          'type' => 'group',
          'visible' => array('phila_half_col_2_option', '=', 'phila_custom_text'),
          'fields' => Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg_upgraded(),
        ),
        array(
          'id'   => 'phila_pullquote',
          'type' => 'group',
          'visible' => array('phila_half_col_2_option', '=', 'phila_pullquote'),
          'fields' => Phila_Gov_Standard_Metaboxes::phila_meta_var_pullquote(),
        ),
      ),
    );
  }
}

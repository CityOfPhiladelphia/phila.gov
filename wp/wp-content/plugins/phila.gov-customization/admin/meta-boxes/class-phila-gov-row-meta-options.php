<?php
/* Group Metabox row options */

if ( class_exists('Phila_Gov_Row_Metaboxes' ) ){
  $phila_standard_metaboxes_load = new Phila_Gov_Row_Metaboxes();
}

class Phila_Gov_Row_Metaboxes {

  public static function phila_metabox_grid_row (){
    return array(
    'id'    => 'phila_row',
    'class'    => 'phila_row',
    'type'  => 'group',
    'clone' => true,
    'sort_clone' => true,
    'fields' => array(
      Phila_Gov_Row_Select_Metaboxes::phila_metabox_grid_options(),
      Phila_Gov_Row_Metaboxes::phila_metabox_full_options(),
        //$metabox_thirds_options,
      //  $metabox_half_options ),
      ),
    );
  }

  public static function phila_metabox_full_options( ){
    return array(
     'name' => 'Full Width Options',
     'id'   => 'phila_full_options',
     'type' => 'group',
     'visible' => array(
       'phila_grid_options',
       '=',
       'phila_grid_options_full'
     ),
     'fields' => array(
       Phila_Gov_Row_Select_Metaboxes::phila_metabox_full_options_select(),
       array(
         'visible' => array('phila_full_options_select', '=', 'phila_blog_posts'),
         'id'  => 'phila_get_post_cats',
         'type' => 'group',
         'fields' => array(
           Phila_Gov_Standard_Metaboxes::phila_metabox_category_picker('Select new categories', 'phila_post_category', 'Display posts from these categories. This will override page category selection entirely.' ),
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
    ),
   );
 }


}

<?php

if ( class_exists('Phila_Gov_Standard_Metaboxes' ) ){
  $phila_standard_metaboxes_load = new Phila_Gov_Standard_Metaboxes();
}

 class Phila_Gov_Standard_Metaboxes {

  public static function phila_wysiwyg_options_basic(){

    return $wysiwyg_options_basic = array(
      'media_buttons' => false,
      'teeny' => true,
      'dfw' => false,
      'quicktags' => false,
      'tinymce' => phila_setup_tiny_mce_basic(
        array(
          'format_select' => false
         )
       ),
      'editor_height' => 200,
    );
  }

  public static function phila_wysiwyg_options_basic_heading(){

    return $wysiwyg_options_basic_heading = array(
      'media_buttons' => false,
      'teeny' => true,
      'dfw' => false,
      'quicktags' => false,
      'tinymce' => phila_setup_tiny_mce_basic(
        array(
          'format_select' => true,
          'heading_level' => 'h3'
         )
       ),
      'editor_height' => 200,
    );
  }

  public static function phila_metabox_v2_address_fields(){

    //Default address fields
    return $meta_var_standard_address = array(
      'id'  =>  'address_group',
      'type'  => 'group',
      'fields'  => array(

        array(
          'type' => 'heading',
          'name' => 'Address',
        ),
        array(
          'name' => 'Street Address 1',
          'id'   => 'phila_std_address_st_1',
          'type' => 'text',
        ),
        array(
          'name' => 'Street Address 2',
          'id'   => 'phila_std_address_st_2',
          'type' => 'text',
        ),
        array(
          'name' => 'City',
          'id'   => 'phila_std_address_city',
          'type' => 'text',
          'std' => 'Philadelphia',
        ),
        array(
          'name' => 'State',
          'id'   => 'phila_std_address_state',
          'type' => 'text',
          'std' => 'PA',
        ),
        array(
          'name' => 'Zip',
          'id'   => 'phila_std_address_zip',
          'type' => 'text',
          'std' => '19107',
        ),
      ),
    );
  }

  //Purpose: To display content in a wysiwyg and include markup for an address
  //TODO: Merge these two almost-identical fields. The ID used to create the metabox will interfere with other metaboxes that are used on the same page. For now we will create a second version of the address content metabox so we can set a different ID.

  public static function phila_metabox_v2_address_fields_unique(){

    return $meta_var_wysiwyg_address_content_unique = array(
      'id'  => 'phila_wysiwyg_address_content',
      'type'  => 'group',
      'clone' => true,
      'sort_clone'  => true,

      'fields'  => array(
        array(
          //TODO: determine way to display step numbers in admin
          'placeholder' => 'Heading',
          'id'  => 'phila_wysiwyg_heading',
          'type'  => 'text',
          'class' => 'width-95'
        ),
        array(
          'id'  => 'phila_unique_wysiwyg_content',
          'type'  => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
        ),
        array(
          'desc'  => 'Include an address?',
          'id'  => 'phila_address_select',
          'type'  => 'checkbox',
        ),
        array(
          'id' => 'phila_std_address',
          'type' => 'group',
          'visible' => array('phila_address_select', true),

          'fields' => array(
            Phila_Gov_Standard_Metaboxes::phila_metabox_v2_address_fields(),
          ),
        ),
      )
    );
  }

  public static function phila_metabox_v2_calendar_full(){

    return $meta_var_calendar_full = array(
      array(
        'name' => 'Calender ID',
        'id'   => 'phila_full_width_calendar_id',
        'desc'  => 'ID of the calendar',
        'type' => 'number'
      ),
      array(
        'name' => 'Calendar URL',
        'id'   => 'phila_full_width_calendar_url',
        'desc'  => 'URL of the full calendar',
        'type' => 'url'
      ),
    );
  }


  public static function phila_metabox_v2_document_page_selector(){

    return $meta_var_document_page_selector = array(
      'id'  => 'phila_document_page_picker',
      'type'  => 'post',
      'post_type' => 'document',
      'field_type'  => 'select_advanced',
      'desc'  => 'Add document pages. You can narrow your search options by typing in the field above.',
      'query_args'  => array(
        'orderby' => 'title',
        'order' => 'ASC',
        //TODO: only show document pages that match the current category
      ),
      'multiple'  => true,
      'placeholder' => ' ',
      'js_options'  => array(
        'width' => '100%',
        'closeOnSelect' => false,
      )
    );
  }

  public static function phila_metabox_v2_textarea(){

    // Custom Text
    return $meta_var_textarea = array(
      array(
        'name' => 'Custom Text Title',
        'id'   => 'phila_custom_text_title',
        'type' => 'text',
      ),
      array(
        'name' => 'Custom Text Content',
        'id'   => 'phila_custom_text_content',
        'type' => 'textarea',
      ),
    );
  }

  public static function phila_metabox_v2_textarea_multi(){

    // Custom Text Multi
    return $meta_var_textarea_multi = array(
      array(
        'name' => 'Row Title',
        'id'   => 'phila_custom_row_title',
        'type' => 'text',
      ),
      array(
        'id'   => 'phila_custom_text_group',
        'type' => 'group',
        'clone' => true,
        'max_clone' => 3,
        'fields' => Phila_Gov_Standard_Metaboxes::phila_metabox_v2_textarea(),
      )
    );
  }

  public static function phila_metabox_v2_wysiwyg(){

    //WYSIWYG with Title
    return $meta_var_wysiwyg = array(
      'id'  =>  'phila_custom_wysiwyg',
      'type'  => 'group',
      'clone' => false,

      'fields'  => array(
        array(
          'name'  => 'Section Title',
          'id'  => 'phila_wysiwyg_title',
          'type'  => 'text'
        ),
        array(
          'id'  => 'phila_wysiwyg_content',
          'type'  => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic()
        )
      )
    );
  }
  public static function phila_metabox_v2_wysiwyg_address(){
    //Purpose: To display content in a wysiwyg and include markup for an address
    return $meta_var_wysiwyg_address_content = array(
      'id'  => 'phila_wysiwyg_address_content',
      'type'  => 'group',
      'clone' => true,
      'sort_clone'  => true,

      'fields'  => array(
        array(
          //TODO: determine way to display step numbers in admin
          'placeholder' => 'Heading',
          'id'  => 'phila_wysiwyg_heading',
          'type'  => 'text',
          'class' => 'width-95'
        ),
        array(
          'id'  => 'phila_wysiwyg_content',
          'type'  => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
        ),
        array(
          'desc'  => 'Include an address?',
          'id'  => 'phila_address_select',
          'type'  => 'checkbox',
        ),
        array(
          'id' => 'phila_std_address',
          'type' => 'group',
          'visible' => array('phila_address_select', true),

          'fields' => array(
            Phila_Gov_Standard_Metaboxes::phila_metabox_v2_address_fields(),
          ),
        ),
      )
    );
  }

  public static function phila_metabox_v2_ordered_content(){

    //Purpose: To display content in a stepped order on the front-end
   return $meta_var_ordered_content = array(
      'id'  => 'phila_ordered_content',
      'type'  => 'group',
      'clone' => true,
      'sort_clone'  => true,

      'fields'  => array(
        array(
          //TODO: determine way to display step numbers in admin
          'placeholder' => 'Step Heading',
          'id'  => 'phila_step_wysiwyg_heading',
          'type'  => 'text',
          'class' => 'width-95'
        ),
        array(
          'id'  => 'phila_step_wysiwyg_content',
          'type'  => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic()
        ),
        array(
          'desc'  => 'Does this step contain an address?',
          'id'  => 'phila_address_step',
          'type'  => 'checkbox',
        ),
        array(
          'id' => 'phila_std_address',
          'type' => 'group',
          'visible' => array('phila_address_step', true),

          'fields' => array(
            Phila_Gov_Standard_Metaboxes::phila_metabox_v2_address_fields(),
          ),
        ),
      )
    );
  }
  //NOTE: While these fields are potentially cloneable, having multiple fields appear in different groups will fail. As we saw with multiple cloneable address fields.
  public static function phila_v2_icon_selection(){
    return $meta_var_v2_icon_selection = array(
        'id'  => 'phila_v2_icon',
        'name'  => 'Select icon',
        'type'  => 'text',
      );
    }

  public static function phila_v2_service_page_selector(){
    return $meta_var_v2_service_page = array(
      'id'  => 'phila_v2_service_page',
      'name' => 'Select service page',
      'type'  => 'post',
      'field_type' => 'select_advanced',
      'post_type' => 'service_page',
      'query_args'  => array(
        'orderby' => 'title',
        'order' => 'ASC',
        //TODO: only show services pages that are not stubs
      ),
      'multiple'  => false,
      'placeholder' => ' ',
      'js_options'  => array(
        'width' => '100%',
        'closeOnSelect' => false,
      )
    );
  }

}

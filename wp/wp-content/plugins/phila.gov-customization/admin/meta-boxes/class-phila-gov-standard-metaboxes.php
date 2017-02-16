<?php

if ( class_exists('Phila_Gov_Standard_Metaboxes' ) ){
  $phila_standard_metaboxes_load = new Phila_Gov_Standard_Metaboxes();
}

 class Phila_Gov_Standard_Metaboxes {

  public static function phila_wysiwyg_options_basic( $editor_height = 200 ){

    return array(
      'media_buttons' => false,
      'teeny' => true,
      'dfw' => false,
      'quicktags' => false,
      'tinymce' => phila_setup_tiny_mce_basic(
        array(
          'format_select' => false
         )
       ),
      'editor_height' => $editor_height,
    );
  }

  public static function phila_wysiwyg_options_basic_heading(){

    return array(
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
    return array(
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

    return array(
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

    return array(
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

    return array(
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
    return array(
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
    return array(
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
    return array(
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
    return array(
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
   return array(
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

  public static function phila_metabox_v2_link_fields( $name, $id ){
    return array(
      'id'  => $id,
      'name'  => $name,
      'type'  => 'group',
      'clone' => false,
      'sort_clone'  => false,

      'fields'  => array(
        Phila_Gov_Standard_Metaboxes::phila_metabox_title('Clickable link text', 'link_text' ),
        Phila_Gov_Standard_Metaboxes::phila_metabox_url('Link URL', 'link_url' ),
        Phila_Gov_Standard_Metaboxes::phila_metabox_external('External link', 'is_external' ),
      )
    );
  }

  //NOTE: While these fields are potentially cloneable, having multiple fields appear in different groups will fail. As we saw with multiple cloneable address fields.
  public static function phila_v2_icon_selection(){
    return array(
      //TODO: swap this out for an icon picker
      'id'  => 'phila_v2_icon',
      'desc' => 'Example: fa-icon-name',
      'name'  => 'Select icon',
      'type'  => 'text',
    );
  }

  public static function phila_v2_service_page_selector(){
    return array(
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
      'max_clone' => 6,
      'placeholder' => ' ',
      'js_options'  => array(
        'width' => '100%',
        'closeOnSelect' => false,
      )
    );
  }

  public static function phila_v2_phone(){
    return array(
      'id'  => 'phila_v2_phone',
      'name'  => 'Phone',
      'type'  => 'phone',
    );
  }
  public static function phila_v2_fax(){
    return array(
      'id'  => 'phila_v2_fax',
      'name'  => 'Fax',
      'type'  => 'phone',
    );
  }
  public static function phila_v2_email(){
    return array(
      'id'  => 'phila_v2_email',
      'name'  => 'Email',
      'type'  => 'email',
    );
  }

  public static function phila_v2_hours(){
    return array(
      'id'  => 'phila_v2_hours',
      'type'  => 'group',
      'clone'  => false,

      'fields' => array(
        array(
          'name' => 'Start day',
          'id'   => 'day_start',
          'type' => 'select',
          'placeholder' => 'Select day',
          'options' => phila_return_week_array(),
         ),
         array(
           'name' => 'End day',
           'id'   => 'day_end',
           'type' => 'select',
           'placeholder' => 'Select day',
           'options' => phila_return_week_array(),
          ),
          array(
            'name' => 'Start time',
            'id'   => 'time_start',
            'type' => 'datetime',
            'js_options'  => array(
              'timeFormat' =>  'hh:mm tt',
              'timeOnly'  => true,
              'stepMinute' => 15,
              'controlType'=> 'select',
              'oneLine'=> true,
            )
          ),
          array(
            'name' => 'End time',
            'id'   => 'time_end',
            'type' => 'datetime',
            'js_options'  => array(
              'timeFormat' =>  'hh:mm tt',
              'timeOnly'  => true,
              'stepMinute' => 15,
              'controlType'=> 'select',
              'oneLine'=> true,
            )
          ),
          array(
            'type' => 'heading',
            'name'  => 'More details'
          ),
          array(
            'id'   => 'hours_other',
            'type' => 'textarea',
          ),
        ),
    );
  }


  public static function phila_metabox_title( $name, $id, $desc = null ){
    return array(
      'name'  => $name,
      'id'    => $id,
      'type'  => 'text',
      'class' => 'metabox-title',
      'desc'  => $desc
    );
  }

  public static function phila_metabox_textarea( $name, $id, $desc = null ){
    return array(
      'name'  => $name,
      'id'    => $id,
      'type'  => 'textarea',
      'class' => 'metabox-summary',
      'desc'  => $desc
    );
  }

  public static function phila_metabox_url( $name, $id ){
    return array(
      'name'  => $name,
      'id'    => $id,
      'type'  => 'url',
      'class' => 'metabox-url',
    );
  }

  public static function phila_metabox_external( $name, $id ){
    return array(
      'name'  => $name,
      'id'    => $id,
      'type'  => 'checkbox',
      'desc'  => 'This link directs users away from phila.gov',
    );
  }

  public static function phila_metabox_post_picker( $name, $id, $post_type ){
    return array(
      'name' => $name,
      'id' => $id,
      'type' => 'post',
      'post_type' => $post_type,
    );
  }

}

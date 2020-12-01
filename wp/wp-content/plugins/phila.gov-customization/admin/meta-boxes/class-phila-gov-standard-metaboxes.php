<?php
/* Registers discrete, reusable metaboxes */

if ( class_exists('Phila_Gov_Standard_Metaboxes' ) ){
  $phila_standard_metaboxes_load = new Phila_Gov_Standard_Metaboxes();
}

class Phila_Gov_Standard_Metaboxes {


  public static function phila_wysiwyg_options_basic( $editor_height = 200 ){

    return array(
      'media_buttons' => false,
      'teeny' => true,
      'dfw' => false,
      'tinymce' => phila_setup_tiny_mce_basic(
        array(
          'format_select' => false
        )
      ),
      'editor_height' => $editor_height,
    );
  }

  public static function phila_wysiwyg_options_basic_heading($media_buttons = false){

    return array(
      'media_buttons' => $media_buttons,
      'teeny' => true,
      'dfw' => false,
      'tinymce' => phila_setup_tiny_mce_basic(
        array(
          'format_select' => true,
          'heading_level' => 'h3'
        )
      ),
      'editor_height' => 200,
    );
  }

  public static function phila_metabox_v2_address_fields($id = 'address_group'){

    //Default address fields
    return array(
      'id'  =>  $id,
      'type'  => 'group',
      'fields'  => array(

        array(
          'type' => 'heading',
          'name' => 'Address',
        ),
        Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_text('Street Address 1', 'phila_std_address_st_1'),
        Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_text('Street Address 2', 'phila_std_address_st_2'),
        Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_text('City', 'phila_std_address_city'),
        Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_text('State', 'phila_std_address_state'),
        Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_text('Zip', 'phila_std_address_zip'),
        array(
          'name' => 'Phone number',
          'id'   => 'phila_std_address_phone',
          'type' => 'phone',
        ),
      ),
    );
  }

  //Purpose: To display content in a wysiwyg and include markup for an address
  //TODO: Merge these two almost-identical fields. The ID used to create the metabox will interfere with other metaboxes that are used on the same page. For now we will create a second version of the address content metabox so we can set a different ID.

  public static function phila_metabox_v2_address_fields_unique($group_id = 'phila_wysiwyg_address_content'){

    return array(
      'id'  => $group_id,
      'type'  => 'group',
      'clone' => true,
      'sort_clone'  => true,
      'add_button' => '+ Add row',

      'fields'  => array(
        array(
          //TODO: determine way to display step numbers in admin
          'placeholder' => 'Heading',
          'id'  => 'phila_wysiwyg_heading',
          'type'  => 'text',
          'class' => 'percent-95'
        ),
        array(
          'id'  => 'phila_unique_wysiwyg_content',
          'type'  => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
        ),
        array(
          'name'  => 'Expand/collapse this section?',
          'id'  => 'phila_expand_collapse',
          'type'  => 'switch',
          'on_label'  => 'Yes',
          'off_label' => 'No'
        ),
        array(
          'name'  => 'Include contact information?',
          'id'  => 'phila_address_select',
          'type'  => 'switch',
          'on_label'  => 'Yes',
          'off_label' => 'No',
        ),
        array(
          'id' => 'phila_std_address',
          'type' => 'group',
          'hidden' => array('phila_address_select', false),

          'fields' => array(
            Phila_Gov_Standard_Metaboxes::phila_metabox_v2_address_fields(),
            array(
              'id' => 'phila_connect_general',
              'type' => 'group',
              // List of sub-fields
              'fields' => array(
                array(
                  'type' => 'heading',
                  'name' => 'Email, fax, etc.',
                ),
                array(
                  'name' => 'Email',
                  'id'   => 'phila_connect_email',
                  'type' => 'email',
                  'desc' => 'example@phila.gov',
                ),
                Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_text('Explanation text for email', 'phila_connect_email_exp', false, 'Ex. For press inquiries contact:'),
                array(
                  'name' => 'Fax',
                  'id'   => 'phila_connect_fax',
                  'type' => 'phone',
                  'desc' => '(###)-###-####',
                ),
                array(
                  'id' => 'phila_connect_social',
                  'type' => 'group',
                  'fields' => array(
                    array(
                      'type' => 'heading',
                      'name' => 'Social',
                    ),
                    array(
                      'name' => 'Facebook URL',
                      'id'   => 'phila_connect_social_facebook',
                      'type' => 'url',
                      'desc' => 'Example: https://www.facebook.com/PhiladelphiaCityGovernment/',
                    ),
                    array(
                      'name' => 'Twitter URL',
                      'id'   => 'phila_connect_social_twitter',
                      'type' => 'url',
                      'desc' => 'Example: https://twitter.com/PhiladelphiaGov'
                    ),
                    array(
                      'name' => 'Instagram URL',
                      'id'   => 'phila_connect_social_instagram',
                      'type' => 'url',
                      'desc' => 'Example: https://www.instagram.com/cityofphiladelphia/'
                    ),
                    array(
                      'name' => 'YouTube URL',
                      'id'   => 'phila_connect_social_youtube',
                      'type' => 'url',
                      'desc' => 'Example: https://www.youtube.com/user/philly311center'
                    ),
                    array(
                      'name' => 'Flickr URL',
                      'id'   => 'phila_connect_social_flickr',
                      'type' => 'url',
                      'desc' => 'Example: https://www.flickr.com/photos/philly_cityrep/'
                    ),
                  ),
                )
              )
            )
          ),
        ),
        array(
          'name'  => 'Add stepped content?',
          'id'  => 'phila_stepped_select',
          'type'  => 'switch',
          'on_label'  => 'Yes',
          'off_label' => 'No'
        ),
        array(
          'id' => 'phila_stepped_content',
          'type' => 'group',
          'visible' => array('phila_stepped_select', true),

          'fields'  => array(
            Phila_Gov_Standard_Metaboxes::phila_metabox_v2_ordered_content(),
            
          )
        ),
      )
    );
  }

  public static function phila_metabox_v2_address_fields_simple($group_id = 'phila_wywiwyg_alt_heading'){

    return array(
      'id'  => $group_id,
      'type'  => 'group',
      'clone' => true,
      'sort_clone'  => true,
      'add_button' => '+ Add row',

      'fields'  => array(
        array(
          'placeholder' => 'H2 heading',
          'id'  => 'phila_wysiwyg_heading',
          'type'  => 'text',
          'class' => 'percent-95'
        ),
        array(
          'placeholder' => 'H2 heading alternate',
          'id'  => 'phila_heading_alt',
          'type'  => 'text',
          'class' => 'percent-95'
        ),
        array(
          'id'  => 'phila_unique_wysiwyg_content',
          'type'  => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
        ),
      )
    );
  }

  public static function phila_metabox_v2_calendar_full(){

    return array(
      array(
        'name' => 'Calendar Shortcode ID',
        'id'   => 'phila_full_width_calendar_id',
        'desc'  => 'ID of the calendar',
        'type' => 'number'
      ),
      array(
        'name'  => 'Event spotlight',
        'id'  => 'phila_active_event_spotlight',
        'desc'  => 'Display an event spotlight?',
        'type'  => 'switch',
        'std'=> '0',
        'on_label'  => 'Yes',
        'off_label' => 'No',
        'visible' => array('phila_template_select', '!=', ''),
      ),
      array(
        'name'  => 'Select event spotlight',
        'id'  => 'phila_event_spotlight',
        'type'  => 'post',
        'visible' => array('phila_active_event_spotlight', '=', 1),
        'post_type' => 'event_spotlight',
        'field_type'  => 'select_advanced',
      ),
      array(
        'name' => 'Calendar owner',
        'id'   => 'phila_calendar_owner',
        'type' => 'taxonomy_advanced',
        'desc'  => 'Determines what owner filter will be applied when a user clicks "see all events". Default is this item\'s owner.',
        'query_args' => array(
          'taxonomy' => 'category',
          'hide_empty' => false,
        ),
        'field_type'  => 'select_advanced'
      ),
      Phila_Gov_Standard_Metaboxes::phila_metabox_url('See all link override', 'override_url', '', 12 ),
    );
  }


  public static function phila_metabox_v2_document_page_selector(){

    return array(
      'id'  => 'phila_document_page_picker',
      'type'  => 'post',
      'name'  => 'Select document or document finder page',
      'post_type' => array( 'document', 'department_page' ),
      'field_type'  => 'select_advanced',
      'desc'  => 'Add document or document finder pages. You can narrow your search options by typing in the field above.',
      'query_args'  => array(
        'orderby' => 'title',
        'order' => 'ASC',
        'post_status' => 'any',
        'meta_query' => array(
          'relation'  => 'OR',
          array(
            'key'     => 'phila_template_select',
            'value'   => 'document_finder_v2',
            'compare' => '=',
          ),
          array(
            'key'     => 'phila_template_select',
            'compare' => 'NOT EXISTS',
          ),
        ),
      ),
      'multiple'  => true,
      'placeholder' => ' ',
      'js_options'  => array(
        'width' => '100%',
        'closeOnSelect' => false,
      )
    );
  }

  /*
  * With an update to the metabox plugin, multiple, clonable, grouped wysiwyg areas are now possible meaning that the old textarea function below can be replaced with a wysiwyg. To preserve the IDs, we'll simply change the name of this function and update it in all the relevant areas. This can probably be phased out in favor of the original phila_metabox_v2_wysiwyg function.
  *
  */
  public static function phila_metabox_v2_wysiwyg_upgraded(){

    // Custom Text
    return array(
      array(
        'name' => 'Heading',
        'id'   => 'phila_custom_text_title',
        'type' => 'text',
        'class' => 'percent-100'
      ),
      array(
        'name' => 'Content',
        'id'   => 'phila_custom_text_content',
        'type' => 'wysiwyg',
        'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic()
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
        'class' => 'percent-100'
      ),
      array(
        'id'   => 'phila_custom_text_group',
        'type' => 'group',
        'clone' => true,
        'max_clone' => 3,
        'add_button'  => '+ Add column',
        'fields' => Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg_upgraded(),
      )
    );
  }

  public static function phila_metabox_v2_wysiwyg($section_name = 'Section title', $wysiwyg_desc = '', $columns = 12){

    //WYSIWYG with Title
    return array(
      'id'  =>  'phila_custom_wysiwyg',
      'type'  => 'group',
      'clone' => false,
      'columns'=> $columns,

      'fields'  => array(
        array(
          'name'  => $section_name,
          'id'  => 'phila_wysiwyg_title',
          'type'  => 'text',
          'class' => 'percent-100'
        ),
        array(
          'id'  => 'phila_wysiwyg_content',
          'type'  => 'wysiwyg',
          'desc'  => $wysiwyg_desc,
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
        )
      )
    );
  }

  public static function phila_metabox_double_wysiwyg($section_name = 'Section title', $wysiwyg_desc = '', $columns = 12, $title_tooltip = '', $content_tooltip = ''){

    //WYSIWYG with Title
    return array(
      'id'  =>  'phila_custom_wysiwyg',
      'type'  => 'group',
      'clone' => false,
      'columns'=> $columns,

      'fields'  => array(
        array(
          'type' => 'heading',
          'name' => $section_name,
        ),
        array(
          'id'  => 'phila_wysiwyg_title',
          'type'  => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic($editor_height = 100),
        ),
        array(
          //TODO: swap this out for an icon picker
          'id'  => 'phila_accordion_icon',
          'desc' => 'Example: fas fa-icon-name. You can find icons on <a href="https://fontawesome.com/icons?d=gallery" target="_blank">Fontawesome.io</a>.',
          'name'  => 'Select icon',
          'type'  => 'text',
        ),
        array(
          'id'  => 'phila_wysiwyg_content',
          'type'  => 'wysiwyg',
          'desc'  => $wysiwyg_desc,
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading(),
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
          'class' => 'percent-95'
        ),
        array(
          'id'  => 'phila_wysiwyg_content',
          'type'  => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
        ),
        array(
          'name'  => 'Include address and phone number?',
          'id'  => 'phila_address_select',
          'type'  => 'switch',
          'on_label'  => 'Yes',
          'off_label' => 'No'
        ),
        array(
          'id' => 'phila_std_address',
          'type' => 'group',
          'hidden' => array('phila_address_select', false),

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
      'add_button'  => '+ Add a step',

      'fields'  => array(
        array(
          //TODO: determine way to display step numbers in admin
          'placeholder' => 'Step Heading',
          'id'  => 'phila_step_wysiwyg_heading',
          'type'  => 'text',
          'class' => 'percent-95'
        ),
        array(
          'id'  => 'phila_step_wysiwyg_content',
          'type'  => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic()
        ),
        array(
          'name'  => 'Does this step contain an address?',
          'id'  => 'phila_address_step',
          'type'  => 'switch',
          'on_label'  => 'Yes',
          'off_label' => 'No'
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

  public static function phila_metabox_v2_phila_wysiwyg_title(){
    return array(
      'id'  => 'phila_wysiwyg_title',
      'type'  => 'text',
      'class' => 'percent-100'
    );
  }

  public static function phila_metabox_v2_phila_advanced_small_wysiwyg($name = ''){
    return array(
      'id'  => 'phila_wysiwyg_content',
      'name' => $name,
      'type'  => 'wysiwyg',
      'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading( true )
    );
  }

  public static function phila_metabox_v2_phila_text($name = '', $id = '', $required= false, $desc = ''){
    return array(
      'name' => $name,
      'id'   => $id,
      'type' => 'text',
      'required' => $required,
      'desc' => $desc,
    );
  }

  public static function phila_metabox_v2_link_fields( $name, $id, $required = true, $columns = 12, $clone = false, $max_clone = 2){
    return array(
      'id'  => $id,
      'name'  => $name,
      'type'  => 'group',
      'clone' => $clone,
      'max_clone' => $max_clone,
      'sort_clone'  => false,
      'columns' => $columns,
      'add_button'  => 'Add another link',

      'fields'  => array(
        array(
          'type' => 'heading',
          'name' => 'Link details',
        ),
        array(
          'name' => 'Clickable link text',
          'id' => 'link_text',
          'type'  => 'text',
          'size' => 50,
          'columns' => 12,
        ),
        Phila_Gov_Standard_Metaboxes::phila_metabox_url('URL', 'link_url', '', 12 ),
        Phila_Gov_Standard_Metaboxes::phila_metabox_external($id = 'is_external'),
      )
    );
  }

  //NOTE: While these fields are potentially cloneable, having multiple fields appear in different groups will fail. As we saw with multiple cloneable address fields.
  public static function phila_v2_icon_selection(){
    return array(
      //TODO: swap this out for an icon picker
      'id'  => 'phila_v2_icon',
      'desc' => 'Example: fas fa-icon-name. You can find icons on <a href="https://fontawesome.com/icons?d=gallery" target="_blank">Fontawesome.io</a>.',
      'name'  => 'Select icon',
      'type'  => 'text',
    );
  }

  public static function phila_metabox_v2_timeline_repeater(){
    return array(
      'id' => 'timeline-items',
      'type' => 'group',
      'clone'  => true,
      'sort_clone' => true,
      'add_button'  => '+ Add timeline item',
      'fields' => array(
        array(
          'name'  => 'Item date',
          'id'    => 'phila_timeline_item_timestamp',
          'type'  => 'date',
          'class' =>  '',
          'size'  =>  25,
          'js_options' =>  array(
            'dateFormat' => 'mm-dd-yy',
            'controlType'=> 'select',
            'oneLine'=> true,
          ),
        ),
        array(
          'name' => 'Item content',
          'id'   => 'phila_timeline_item',
          'type' => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic()
        ),
      ),
    );
  }

  public static function phila_v2_service_page_selector( $multiple = false ){
    return array(
      'id'  => 'phila_v2_service_page',
      'name' => 'Select service page',
      'type'  => 'post',
      'field_type' => 'select_advanced',
      'post_type' => 'service_page',
      'query_args'  => array(
        'posts_per_page' => -1,
        'post_status' => 'any',
        'orderby' => 'title',
        'order' => 'ASC',
        'meta_key' => 'phila_template_select',
        'meta_value' => 'service_stub',
        'meta_compare' => '!=',
        ),
      'multiple'  => $multiple,
      'placeholder' => ' ',
      'desc'     =>  'Add service pages. You can narrow your search options by typing in the field above.',
      'js_options'  => array(
        'width' => '100%',
        'closeOnSelect' => false,
      )
    );
  }

  public static function phila_program_page_selector( $multiple = false ){

    return array(
      Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_text('Heading', 'phila_custom_text_title'),
      array(
        'name'        => 'Select program pages',
        'id'          => 'phila_select_programs',
        'type'        => 'post',
        'post_type'   => 'programs',
        'field_type'  => 'select_advanced',
        'placeholder' => '',
        'desc'     =>  'Add program pages. You can narrow your search options by typing in the field above.',
        'multiple'  => $multiple,

        'query_args'  => array(
          'post_status'    => 'any',
          'posts_per_page' => -1,
          'post_parent' => 0
        ),
        'js_options'  => array(
          'width' => '100%',
          'closeOnSelect' => false,
        )
      ),
      array(
        'id' => 'phila_v2_programs_link',
        'title' => 'See all programs',
        'name'  => 'See all programs url',
        'placeholder' => 'E.g. https://phila.gov/departments/department-of-commerce/all-programs/',
        'type'  => 'url',
        'class' => 'metabox-url',
      ),
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
  

  public static function phila_metabox_title( $name, $id, $desc = null, $size = '30', $columns = '12'){
    return array(
      'name'  => $name,
      'id'    => $id,
      'type'  => 'text',
      'class' => 'metabox-title',
      'desc'  => $desc,
      'size'  => $size,
      'columns' => $columns,
    );
  }

  public static function phila_metabox_textarea( $name, $id, $desc = null, $columns = '12' ){
    return array(
      'name'  => $name,
      'id'    => $id,
      'type'  => 'textarea',
      'class' => 'metabox-summary',
      'desc'  => $desc,
      'columns' => $columns
    );
  }

  public static function phila_metabox_url( $name, $id, $desc = null, $columns = '12' ){
    return array(
      'name'  => $name,
      'id'    => $id,
      'type'  => 'text',
      'class' => 'metabox-url',
      'desc'  => $desc,
      'columns' => $columns,
      'class' => 'percent-100',
    );
  }

  public static function phila_metabox_external( $id, $columns = 12 ){
    return array(
      'name'  => 'Does this link direct users away from phila.gov?',
      'id'    => $id,
      'type'  => 'switch',
      'on_label'  => 'Yes',
      'off_label' => 'No',
      'columns' => $columns,
      'class' => 'is-external',
    );
  }

  public static function phila_metabox_post_picker( $name, $id, $post_type, $clone = false, $max_clone = 3, $columns = '12' ){
    return array(
      'name' => $name,
      'id' => $id,
      'type' => 'post',
      'post_type' => $post_type,
      'clone' => $clone,
      'max_clone' => $max_clone,
      'columns' => $columns
    );
  }

  public static function phila_metabox_category_picker( $name, $id, $desc = ''){
    return array(
      'name'  => $name,
      'id'  => $id,
      'desc'  => $desc,
      'type'  => 'taxonomy_advanced',
      'taxonomy'  => 'category',
      'field_type'  => 'select_advanced',
      'multiple'  => true,
      'allowClear' => true
    );
  }

  public static function phila_call_to_action_group_content( $datalist = null ){
    return array(
      array(
        'name'  => 'Link title',
        'id'    => 'phila_action_panel_cta_text_multi',
        'type'  => 'text',
        'class' => 'action-panel-cta-text',
        'size'  => '40',
        'datalist' => $datalist
      ),
      array(
        'name'  => 'Summary',
        'id'    => 'phila_action_panel_summary_multi',
        'type'  => 'textarea',
        'class' => 'action-panel-details',
      ),
      array(
        'name'  => 'Icon',
        'id'    => 'phila_action_panel_fa_multi',
        'type'  => 'text',
        'class' => 'action-panel-fa',
        'desc'  => 'Uses <a href="https://fontawesome.com/icons?d=gallery" target="_blank">Font Awesome</a> icons. Eg: fas fa-bell'
      ),
      array(
        'name'  => 'Link url',
        'id'    => 'phila_action_panel_link_multi',
        'type'  => 'text',
        'class' => 'action-panel-link',
        'columns' => 6,
      ),
      Phila_Gov_Standard_Metaboxes::phila_metabox_external($id = 'phila_action_panel_link_loc_multi'),
    );
  }

  public static function phila_meta_var_callout (){
    return array(
      // array(
      //   'name' => 'Status',
      //   'id'   => 'phila_callout_type',
      //   'type' => 'select',
      //   'options' => array(
      //     'default' => 'Default',
      //     'important' => 'Important'
      //   ),
      // ),
      array(
        'name' => ' Text',
        'id'   => 'phila_callout_text',
        'type' => 'textarea',
      ),
    );
  }


  // Multiple Call to Action (CTA) Panels
  public static function phila_meta_var_call_to_action_multi (){

    return array(
      array(
        'id'  => 'phila_call_to_action_section',
        'type' => 'group',
        'fields' => array(
          array(
            'name'  => 'Section Title',
            'id'    => 'phila_action_section_title_multi',
            'type'  => 'text',
            'class'  => 'percent-100'
          ),
          array(
            'id'  => 'phila_call_to_action_multi_group',
            'type' => 'group',
            'clone'  => true,
            'max_clone' => 4,
            'sort_clone' => true,
            'fields' => array(
              array(
                'name'  => 'Link text',
                'id'    => 'phila_action_panel_cta_text_multi',
                'type'  => 'text',
                'class' => 'action-panel-cta-text',
              ),
              array(
                'name'  => 'Summary',
                'id'    => 'phila_action_panel_summary_multi',
                'type'  => 'textarea',
                'class' => 'action-panel-details',
              ),
              array(
                'name'  => 'Icon',
                'id'    => 'phila_action_panel_fa_multi',
                'type'  => 'text',
                'class' => 'action-panel-fa',
              ),
              array(
                'name'  => 'Add a circle background?',
                'id'    => 'phila_action_panel_fa_circle_multi',
                'type'  => 'switch',
                'class' => 'action-panel-fa',
                'std'  => 1,
                'on_label'  => 'Yes',
                'off_label' => 'No'

              ),
              array(
                'name'  => 'URL to content',
                'id'    => 'phila_action_panel_link_multi',
                'type'  => 'text',
                'class' => 'action-panel-link',
                'columns'  => 12,
              ),
              Phila_Gov_Standard_Metaboxes::phila_metabox_external($id = 'phila_action_panel_link_loc_multi')
            ),
          ),
          array(
            'name'  =>  'See all title (optional)',
            'id'    => 'phila_url_title',
            'type'  => 'text',
            'visible' => array('phila_full_options_select', '=', 'phila_resource_list'),
          ),
          array(
            'name'  =>  'See all URL (optional)',
            'id'    => 'phila_url',
            'type'  => 'url',
            'visible' => array('phila_full_options_select', '=', 'phila_resource_list'),
          ),
          array(
            'name'  =>  'Background Image',
            'id'    => 'phila_bg_image',
            'type'  => 'file_input',
            'visible' => array('phila_full_options_select', '=', 'phila_get_involved'),
          ),
        ),
      ),
    );
  }
  // List of links with FontAwesome icons
  public static function phila_meta_var_list_items (){

    return array(
      Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_text('Row Title', 'phila_row_title'),
      array(
        'name' => 'Summary',
        'id'   => 'phila_summary',
        'type' => 'textarea',
        ),
      array(
        'id'  => 'phila_list',
        'type' => 'group',
        'clone'  => true,
        'sort_clone' => true,

      'fields' => array(
        array(
          'std' => '<strong>Row</strong>',
          'type' => 'custom_html',
        ),
        array(
          'id'   => 'phila_list_items',
          'type' => 'group',
          'clone'  => true,
          'sort_clone' => true,
          'fields' => array(
            Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_text(__('Item Title', 'rwmb'), 'phila_row_title'),
            array(
              'name' => __('Item URL', 'rwmb'),
              'id'   => 'phila_list_item_url',
              'type' => 'url',
            ),
            Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_text(__('Item Icon', 'rwmb'), 'phila_list_item_type'),
          ),
        ),
      ),
    ),
    );
  }
public static function phila_meta_var_connect(){
  return array(
      array(
      'name' => 'Connect Panel',
      'id'   => 'phila_connect_description',
      'type' => 'custom_html',
      'std'  => '<span>Use any of the optional fields below to add social media, address, and contact information.</span><br/>
      <span><em>Note: If all fields are left empty the <strong>Connect</strong> module will still appear on the page, however it will be empty.</em></span>',
      ),
      array(
        'id' => 'phila_connect_address',
        'type' => 'group',
        // List of sub-fields
        'fields' => array(
          array(
            'type' => 'heading',
            'name' => 'Address',
          ),
          Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_text('Street Address 1', 'phila_connect_address_st_1'),
          Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_text('Street Address 2', 'phila_connect_address_st_2'),
          Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_text('City', 'phila_connect_address_city'),
          Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_text('State', 'phila_connect_address_state'),
          Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_text('Zip', 'phila_connect_address_zip'),
        ),
      ),
      array(
        'id' => 'phila_connect_general',
        'type' => 'group',
        // List of sub-fields
        'fields' => array(
          array(
            'type' => 'heading',
            'name' => 'Email, phone, etc.',
          ),
          array(
            'name' => 'Email',
            'id'   => 'phila_connect_email',
            'type' => 'email',
            'desc' => 'example@phila.gov',
          ),
          Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_text('Explanation text for email', 'phila_connect_email_exp', false, 'Ex. For press inquiries contact:'),
          array(
            'name' => 'Phone',
            'id'   => 'phila_connect_phone',
            'type' => 'phone',
            'desc' => '(###)-###-####',
          ),
          array(
            'name' => 'More phone numbers' ,
            'id'   => 'phila_connect_phone_multi',
            'type' => 'group',
            'clone' => true,
            'add_button'  => '+ Add another number',
            'fields'  => array(
              array(
                'id'   => 'phila_connect_phone',
                'type' => 'phone',
                'desc' => '(###)-###-####',
              ),
              array(
                'id'  => 'phila_connect_phone_text',
                'type'  => 'text',
                'desc'  => 'Addtional information about this phone number'
              ),
            ),
          ),
          array(
            'name' => 'Fax',
            'id'   => 'phila_connect_fax',
            'type' => 'phone',
            'desc' => '(###)-###-####',
          ),
          array(
            'name' => 'TTY',
            'id'   => 'phila_connect_tty',
            'type' => 'phone',
            'desc' => '(###)-###-####',
          ),
          Phila_Gov_Standard_Metaboxes::phila_metabox_v2_link_fields('Website', 'phila_web_link', false),
          array(
            'id' => 'phila_connect_social',
            'type' => 'group',
            'fields' => array(
              array(
                'type' => 'heading',
                'name' => 'Social',
              ),
              array(
              'name' => 'Facebook URL',
              'id'   => 'phila_connect_social_facebook',
              'type' => 'url',
              'desc' => 'Example: https://www.facebook.com/PhiladelphiaCityGovernment/',
              ),
              array(
              'name' => 'Twitter URL',
              'id'   => 'phila_connect_social_twitter',
              'type' => 'url',
              'desc' => 'Example: https://twitter.com/PhiladelphiaGov'
              ),
              array(
              'name' => 'Instagram URL',
              'id'   => 'phila_connect_social_instagram',
              'type' => 'url',
              'desc' => 'Example: https://www.instagram.com/cityofphiladelphia/'
              ),
              array(
              'name' => 'YouTube URL',
              'id'   => 'phila_connect_social_youtube',
              'type' => 'url',
              'desc' => 'Example: https://www.youtube.com/user/philly311center'
              ),
              array(
              'name' => 'Flickr URL',
              'id'   => 'phila_connect_social_flickr',
              'type' => 'url',
              'desc' => 'Example: https://www.flickr.com/photos/philly_cityrep/'
              ),
            ),
          ),
          array(
            'name' => 'See all contact information',
            'id'  => 'connect_see_all',
            'type' => 'URL'
          )
        ),
      ),
    );
  }

  // Pullquote
  public static function phila_meta_var_pullquote(){
    return array(
      array(
        'name' => 'Quote',
        'id'   => 'phila_quote',
        'type' => 'textarea',
      ),
      Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_text('Attribution', 'phila_attribution'),
    );
  }

  public static function phila_meta_var_full_width_cta(){
    return array(
      array(
        'name'  => 'Title',
        'id'    => 'cta_full_title',
        'type'  => 'text',
        'class' => 'metabox-title',
        'limit' => 50
      ),
      array(
        'name'  => 'Description',
        'id'    => 'cta_full_description',
        'type'  => 'textarea',
        'class' => 'metabox-summary',
        'limit' => 140
      ),
      Phila_Gov_Standard_Metaboxes::phila_metabox_v2_link_fields('', 'cta_full_link'),
      array(
        'id' => 'cta_is_survey',
        'name'  => 'Is this a link to a survey or other form of feedback gathering?',
        'type'  => 'switch',
        'on_label'  => 'Yes',
        'off_label' => 'No'
      ),
      array(
        'id' => 'cta_is_modal',
        'name'  => 'Should this content appear in a modal?',
        'type'  => 'switch',
        'on_label'  => 'Yes',
        'off_label' => 'No'
      ),
      array(
        'id'   => 'cta_modal',
        'type'  => 'group',
        'visible' => array( 'cta_is_modal', 1 ),

        'fields' => array(
          array(
            'type' => 'heading',
            'name'  => 'Modal content',
          ),
          array(
            'name' => '',
            'id'   => 'cta_modal_content',
            'type' => 'textarea',
          ),
          Phila_Gov_Standard_Metaboxes::phila_v2_icon_selection( ),
        ),
      ),
    );
  }
  public static function phila_meta_registration() {
    return array(
        array(
          'name'  => 'Registration row title',
          'id'    => 'title',
          'type'  => 'text',
          'class' => 'percent-100',
          'placeholder' => 'E.g. Enrollment',
          'desc'  => 'Registration is closed by default. Start and end dates determine when registration should be displayed as open. Start and end dates are not displayed.',
        ),
        array(
          'name' => 'Registration start date',
          'id'  => 'start_date',
          'type'  => 'date',
          'timestamp' => true,
          'columns' => 6,
        ),
        array(
          'name' => 'Registration closed date',
          'id'  => 'end_date',
          'type'  => 'date',
          'timestamp' => true,
          'columns' => 6,
        ),
        array(
          'id'  => 'open',
          'type'  => 'group',
          'fields'  => array(
            array(
              'type' => 'heading',
              'name' => 'Registration is open -  Heading, description, and links',
            ),
            Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg('', $wysiwyg_desc = '', $columns = 6),
            Phila_Gov_Standard_Metaboxes::phila_metabox_v2_link_fields($name = '', $id = 'links', $required  = false, $columns = 6, $clone = true),
          )
        ),
        array(
          'id'  => 'closed',
          'type'  => 'group',
          'fields'  => array(
            array(
              'type' => 'heading',
              'name' => 'Registration is closed - Heading and description',
            ),
            Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg('', $wysiwyg_desc = '', $columns = 12),
          )
        )
    );
  }

  public static function phila_meta_var_commission_members(){
    return array(
      'id' => 'phila_commission_members',
      'type'  => 'group',
      'clone' => true,
      'sort_clone' => true,
      'add_button' => '+ Add member',
      'fields'  => array(
        array(
          'id'  => 'full_name',
          'name'  => 'Full name',
          'type'  => 'text',
          'desc'  => 'Enter the full name, with honorific e.g.: Dr. Herbert West, PhD'
        ),
        array(
          'id'  => 'title',
          'name'  => 'Title',
          'type'  => 'text',
          'desc'  => 'E.g.: Chief of Staff/Reanimator'
        ),
        array(
          'id'  => 'email',
          'name'  => 'Email address',
          'type'  => 'email',
        ),
        array(
          'id'  => 'phone',
          'name'  => 'Phone',
          'type'  => 'phone'
        ),
        array(
          'id'  => 'headshot',
          'name'  => 'Headshot',
          'type'  => 'file_advanced',
          'max_file_uploads' => 1,
          'desc'  => 'Image size: 400px by 400px.'
        ),
        array(
          'name' => 'Bio',
          'id'   => 'bio',
          'type' => 'wysiwyg',
          'options' =>  Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic($editor_height = 100)
        ),
      )
    );
  }
  
  //v1 service page default
  public static function phila_meta_var_addtional_content() {
    return array(
      array(
        'id' => 'phila_additional_content',
        'type'  => 'group',
        'clone' => false,

        'fields' => array(
          array(
            'id'  => 'phila_forms_instructions',
            'type'  => 'group',
            'visible' => array('post_type', '=', 'service_page'),

            'fields'  => array(
              array(
                'name'  => 'Forms & Instructions',
                'type'  => 'heading'
              ),
              Phila_Gov_Standard_Metaboxes::phila_metabox_v2_document_page_selector(),
              array(
                'id'  => 'phila_forms_instructions_free_text',
                'type'  => 'wysiwyg',
                'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic()
              ),
            ),
          ),
          array(
            'id'  => 'phila_related',
            'type'  => 'group',
            'visible' => array('post_type', '=', 'service_page'),

            'fields'  => array(
              array(
                'name'  => 'Related Content',
                'type'  => 'heading'
              ),
              array(
                'id'  => 'phila_related_content_picker',
                'type'  => 'post',
                'post_type' => array('department_page', 'programs', 'post', 'service_page', 'document'),
                'placeholder' => 'Select pages',
                'query_args'  => array(
                    'post_status'    => 'any',
                    'posts_per_page' => - 1,
                  ),
                'multiple'  => true,
              ),
              array(
                'id'  => 'phila_related_content',
                'type'  => 'wysiwyg',
                'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic()
              ),
            )
          ),
          array(
            'id'  => 'phila_did_you_know',
            'type'  => 'group',

            'fields'  => array(
              array(
                'name'  => 'Did You Know?',
                'type'  => 'heading'
              ),
              array(
                'id'  => 'phila_did_you_know_content',
                'type'  => 'wysiwyg',
                'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic()
              ),
            )
          ),
          array(
            'id'  => 'phila_questions',
            'type'  => 'group',

            'fields'  => array(
              array(
                'name'  => 'Questions about this content?',
                'type'  => 'heading'
              ),
              array(
                'id'  => 'phila_question_content',
                'type'  => 'wysiwyg',
                'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic()
              ),
            )
          ),
          array(
            'id'  => 'phila_disclaimer',
            'type'  => 'group',

            'fields'  => array(
              array(
                'name'  => 'Disclaimer',
                'type'  => 'heading'
              ),
              array(
                'id'  => 'phila_disclaimer_content',
                'type'  => 'wysiwyg',
                'desc'  => 'Enter disclaimer content, or a [text block] with disclaimer shortcode',
                'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic()
              ),
            )
          )
        )
      )
    );
  }

  public static function phila_resource_list_v2() {
    return array(
      'id'  => 'phila_resource_list_v2',
      'type' => 'group',
      'clone'  => true,
      'sort_clone' => true,
      'add_button' => '+ Add another group',

      'fields' => array(
        Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_text(__('Group title', 'rwmb'), 'phila_resource_list_title'),
        array(
          'id'   => 'phila_resource_list_items',
          'type' => 'group',
          'clone'  => true,
          'sort_clone' => true,
          'add_button' => '+ Add a link',

          'fields' => array(
            Phila_Gov_Standard_Metaboxes::phila_metabox_v2_phila_text(__('Link text', 'rwmb'), 'phila_list_item_title', true),
            array(
              'name' => __('URL', 'rwmb'),
              'id'   => 'phila_list_item_url',
              'type' => 'url',
              'required' => true,
            ),
            array(
              'id'   => 'phila_list_item_external',
              'name' => 'Does this link take users away from phila.gov?',
              'type' => 'switch',
              'on_label' => 'Yes',
              'off_label'  => 'No'
            ),
            array(
              'name' => __('Link icon', 'rwmb'),
              'id'   => 'phila_list_item_type',
              'type' => 'select',
              'placeholder' => 'Choose icon...',
              'options' => array(
                'link' => 'Link',
                'document' => 'Document',
                'map' => 'Map',
                'video'  => 'Video'
              ),
            ),
          ),
        ),
      )
    );

  }

  public static function phila_disclaimer_modal(){
    return array(
      array(
        'id'  => 'disclaimer_modal_button_text',
        'name'  => 'Button Text',
        'type'  => 'text',
      ),
      array(
        'name' => 'Modal Text',
        'id'   => 'disclaimer_modal_text',
        'type' => 'wysiwyg',
        'options' =>  Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic()
      ),
    );
  }

  public static function phila_meta_prereq_row($row_title){
    return array(
      array(
        'name' => ($row_title),
        'id'   => 'accordion_row_title',
        'type' => 'text',
        'class' => 'percent-100'
      ),
      Phila_Gov_Standard_Metaboxes::phila_v2_icon_selection(),
      array(
        'id'   => 'accordion_group',
        'type' => 'group',
        'clone'  => true,
        'sort_clone' => true,
        'add_button' => '+ Add accordion',
        'fields' => array(
          Phila_Gov_Standard_Metaboxes::phila_metabox_double_wysiwyg(
            $section_name = 'Accordion title', 
            $wysiwyg_desc = 'Accordion content', 
            $columns = 12, 
            $clone = true ),
        )
      )
    );
  }

  public static function phila_metabox_timeline(){

  return  array(
    array(
      'id'  => 'timeline-title',
      'type'  => 'text', 
      'name'  => 'Title',
      'desc'  =>  'Content appears on homepage',
    ),
    array(
      'id'  => 'timeline-month-year-toggle',
      'type'  => 'radio', 
      'name'  => 'Date display',
      'options' => array(
        'day-month-year' => 'Day - Month - Year',
        'month-year' => 'Month - Year',
        'year' => 'Year',
      ),
      'std' => 'day-month-year'
    ),
    Phila_Gov_Standard_Metaboxes::phila_metabox_v2_timeline_repeater(),
  );
}


public static function phila_timeline_page_selector( ){

  return array(
    'name'          => 'Select timeline page',
    'id'          => 'phila_select_timeline',
    'type'        => 'post',
    'post_type'   => array('department_page', 'programs'),
    'post_status' => array( 'draft', 'publish', 'private'),
    'field_type'  => 'select_advanced',
    'placeholder' => '',
    'desc'     =>  'Add a timeline page. You can narrow your search options by typing in the field above',
    'multiple'  => 'false',

    'query_args'  => array(
      'post_status'    => 'any',
      'posts_per_page' => -1,
      'meta_query' => array(
        array(
          'key'     => 'phila_template_select',
          'value'   => 'timeline',
          'compare' => '=',
        )
      )
    ),
    'js_options'  => array(
      'width' => '100%',
      'closeOnSelect' => false,
    )
  );
}

public static function phila_get_service_updates( ){

  return array(
      array(
        'id' => 'service_intro',
        'type'  => 'wysiwyg',
        'name'  =>  'Intro text',
        'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
      ),
      array(
        'type'  => 'group',
        'id'  => 'parent_group',
        'clone' => true,
        'add_button' => '+ Add group',
        'sort_clone'  => true,
        'fields' => array(
          array(
            'id'  => 'group_title',
            'type'  => 'text',
            'name'  => 'Group title',
            'columns' => 6,
          ),
          'fields' => array(
            'type'  => 'group',
            'id'  => 'service_content',
            'clone' => true,
            'add_button' => '+ Add service update',
            'fields'  => array(
              array(
                'id'  => 'service_name',
                'name' => 'Service name',
                'type'  => 'text'
              ),
              array(
                'id'  => 'icon',
                'name' => 'Icon',
                'type'  => 'text',
                'desc' => 'Example: fas fa-icon-name. You can find icons on <a href="http://fontawesome.io/icons/" target="_blank">Fontawesome.io</a>.',
              ),
              array(
                'name' => 'Urgency level',
                'id'   => 'level',
                'type' => 'select',
                'placeholder' => 'Choose type...',
                'options' => array(
                  'normal' => 'Normal (Green)',
                  'warning' => 'Warning (Yellow)',
                  'critical' => 'Critical (Red)',
                ),
              ),
              array(
                'id'    => 'message',
                'type'  => 'wysiwyg',
                'options' => array(
                  'media_buttons' => false,
                  'teeny' => true,
                  'dfw' => false,
                  'quicktags' => false,
                  'editor_height' => 100,
                  ),
                ),
                )
              ),
            )
          ),
  );// End Service Updates

}

}//end Class

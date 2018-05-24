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

  public static function phila_wysiwyg_options_basic_heading(){

    return array(
      'media_buttons' => false,
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
        ),
        array(
          'name' => 'State',
          'id'   => 'phila_std_address_state',
          'type' => 'text',
        ),
        array(
          'name' => 'Zip',
          'id'   => 'phila_std_address_zip',
          'type' => 'text',
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
          'name'  => 'Include an address?',
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
    );
  }


  public static function phila_metabox_v2_document_page_selector(){

    return array(
      'id'  => 'phila_document_page_picker',
      'type'  => 'post',
      'name'  => 'Select document page',
      'post_type' => 'document',
      'field_type'  => 'select_advanced',
      'desc'  => 'Add document pages. You can narrow your search options by typing in the field above.',
      'query_args'  => array(
        'orderby' => 'title',
        'order' => 'ASC',
        'post_status' => 'any'
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
          'name'  => 'Include an address?',
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
          'required' => $required
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
      'desc' => 'Example: fa-icon-name. You can find icons on <a href="http://fontawesome.io/icons/" target="_blank">Fontawesome.io</a>.',
      'name'  => 'Select icon',
      'type'  => 'text',
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
      'name'          => 'Select program pages',
      'id'          => 'phila_select_programs',
      'type'        => 'post',
      'post_type'   => 'programs',
      'field_type'  => 'select_advanced',
      'placeholder' => '',
      'desc'     =>  'Add program pages. You can narrow your search options by typing in the field above.',
      'multiple'  => $multiple,

      'query_args'  => array(
        'post_status'    => 'any',
        'posts_per_page' => - 1,
        'post_parent' => 0
      ),
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


  public static function phila_metabox_title( $name, $id, $desc = null, $size = '30', $columns = '12' ){
    return array(
      'name'  => $name,
      'id'    => $id,
      'type'  => 'text',
      'class' => 'metabox-title',
      'desc'  => $desc,
      'size'  => $size,
      'columns' => $columns
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
        'desc'  => 'Uses <a href="http://fontawesome.io/icons/" target="_blank">Font Awesome</a> icons. Eg: fa-bell'
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
                 'required' => true,
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
             'name'  =>  'Additional URL Title (optional)',
             'id'    => 'phila_url_title',
             'type'  => 'text',
             'visible' => array('phila_full_options_select', '=', 'phila_resource_list'),
           ),
           array(
             'name'  =>  'Additional URL (optional)',
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
     array(
       'name' => 'Row Title',
       'id'   => 'phila_row_title',
       'type' => 'text',
     ),
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
             array(
               'name' => __('Item Title', 'rwmb'),
               'id'   => 'phila_list_item_title',
               'type' => 'text',
               'required' => true,
             ),
             array(
               'name' => __('Item URL', 'rwmb'),
               'id'   => 'phila_list_item_url',
               'type' => 'url',
               'required' => true,
             ),
             array(
                'name' => __('Item Icon', 'rwmb'),
                'id'   => 'phila_list_item_type',
                'type' => 'text',
             ),
           ),
         ),
       ),
     ),
   );
  }

  // Feature Programs and Initiatives
  public static function phila_meta_var_feature_programs_initiatives (){
   return array(
      array(
        'id' => 'phila_p_i',
        'type' => 'group',
        'fields' => array(
          array(
            'name' => 'Feature Program',
            'id' => 'phila_p_i_items',
            'type' => 'post',
            'post_type' => 'department_page',
            'clone' => true,
            'max_clone' => 3,
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
        'id' => 'phila_connect_address',
        'type' => 'group',
        // List of sub-fields
        'fields' => array(
          array(
            'type' => 'heading',
            'name' => 'Address',
          ),
          array(
           'name' => 'Street Address 1',
           'id'   => 'phila_connect_address_st_1',
           'type' => 'text',
          ),
          array(
           'name' => 'Street Address 2',
           'id'   => 'phila_connect_address_st_2',
           'type' => 'text',
          ),
          array(
           'name' => 'City',
           'id'   => 'phila_connect_address_city',
           'type' => 'text',
           'std' => 'Philadelphia',
          ),
          array(
           'name' => 'State',
           'id'   => 'phila_connect_address_state',
           'type' => 'text',
           'std' => 'PA',
          ),
          array(
           'name' => 'Zip',
           'id'   => 'phila_connect_address_zip',
           'type' => 'text',
           'std' => '19107',
          ),
        ),
      ),
      array(
        'id' => 'phila_connect_general',
        'type' => 'group',
        // List of sub-fields
        'fields' => array(
          array(
            'type' => 'heading',
            'name' => 'Contact',
          ),
          array(
             'name' => 'Phone',
             'id'   => 'phila_connect_phone',
             'type' => 'phone',
             'desc' => '(###)-###-####',
           ),
          array(
            'name' => 'Fax',
            'id'   => 'phila_connect_fax',
            'type' => 'phone',
            'desc' => '(###)-###-####',
          ),
          array(
            'name' => 'Email',
            'id'   => 'phila_connect_email',
            'type' => 'email',
            'desc' => 'example@phila.gov',
          ),
          Phila_Gov_Standard_Metaboxes::phila_metabox_v2_link_fields('Website', 'phila_web_link', false),

          array(
            'name' => 'See all contact information',
            'id'  => 'connect_see_all',
            'type' => 'URL'
          )
        ),
      ),
    );
  }

  // Custom Featured Content
  public static function phila_meta_var_custom_feature(){
    return array(
      array(
        'name' => 'Feature Title',
        'id' => 'phila_feature_title',
        'type' => 'text',
      ),
      array(
        'name' => 'Feature Image',
        'id' => 'phila_feature_image',
        'type' => 'file_input',
      ),
      array(
        'name' => 'Feature Text',
        'id' => 'phila_feature_text',
        'type' => 'textarea',
      ),
      array(
        'name' => 'Feature URL Text',
        'id' => 'phila_feature_url_text',
        'type' => 'text',
      ),
      array(
        'name' => 'URL',
        'id' => 'phila_feature_url',
        'type' => 'url',
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
      array(
        'name' => 'Attribution',
        'id'   => 'phila_attribution',
        'type' => 'text',
      ),
    );
  }

  public static function phila_meta_var_full_width_cta(){
    return array(
      Phila_Gov_Standard_Metaboxes::phila_metabox_title( 'Title', 'cta_full_title', '50 character maximum.' ),
      Phila_Gov_Standard_Metaboxes::phila_metabox_textarea('Description', 'cta_full_description', '140 character maximum.' ),
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
      'desc'  => 'Use this section to create an accordion-style list of people who don\'t formally work for the City of Philadelphia. List will appear in the order above.',
      'clone' => true,
      'sort_clone' => true,
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
          'options' =>    Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic($editor_height = 100)
        ),
      )
    );
  }
}//end Class

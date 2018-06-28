<?php
/**
 * Registers metaboxes
 *
 */

add_action('admin_init', 'phila_return_month_array');

function phila_return_month_array(){

  $month_array = array();
  for ($m=1; $m<=12; $m++) {
    $month = date('F', mktime(0,0,0,$m, 1, date('Y')));

    $month_array[$month] = $month;
  }
  return $month_array;
}
function phila_return_week_array(){

  $week_array = array(
    'Monday'  => 'Monday',
    'Tuesday' => 'Tuesday',
    'Wednesday' => 'Wednesday',
    'Thursday'  => 'Thursday',
    'Friday'  => 'Friday',
    'Saturday'  => 'Saturday',
    'Sunday'  => 'Sunday'
  );

  return $week_array;
}

function phila_setup_tiny_mce_basic( array $options ){

  $output['block_formats'] = 'Paragraph=p; Heading 1=h1; Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6;';

  $defaults = array(
    'format_select' => false,
    'heading_level' => 'h3',
  );

  $options = array_merge($defaults, $options);

  if ( $options['format_select'] == true) {

    $output['toolbar1'] = 'formatselect, bold, italic, bullist, numlist, link, unlink, outdent, indent, removeformat, pastetext, superscript, subscript';

  }

  if ( $options['heading_level'] == 'h3' ) {
    $output['block_formats'] = 'Paragraph=p; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6;';

  }elseif( $options['heading_level'] == 'h2' ){
    $output['block_formats'] = 'Paragraph=p;  Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6;';
  }

  if ( $options['format_select'] == false ) {

    $output['toolbar1'] = 'bold, italic, bullist, numlist, link, unlink, outdent, indent, removeformat, pastetext, superscript, subscript';

  }
  return $output;

}

add_filter( 'rwmb_meta_boxes', 'phila_register_meta_boxes' );

function phila_register_meta_boxes( $meta_boxes ){

  $meta_boxes[] = array(
    'id'       => 'announcement_end',
    'title'    => 'When should this end?',
    'pages'    => array( 'announcement' ),
    'context'  => 'side',
    'priority' => 'high',

    'fields' => array(
      array(
        'name'  => 'End date',
        'id'    => 'phila_announce_end_date',
        'type'  => 'date',
        'class' =>  'effective-end-time',
        'desc'  => 'Choose a time for this announcement to expire. Announcements can only exist for a maximum of two weeks.',
        'required'=> true,
        'size'  =>  25,
        'timestamp'  => true,
        'js_options' =>  array(
          'dateFormat' => 'mm-dd-yy',
          'controlType'=> 'select',
          'oneLine'=> true,
          'maxDate' => '+2w'
        ),
      ),
    ),
  );
  $meta_boxes[] = array(
    'title'    => 'Show on homepage',
    'pages'    => array( 'announcement' ),
    'context'  => 'side',
    'priority' => 'high',
    'include' => array(
      'user_role'  => array( 'administrator', 'phila_master_homepage_editor', 'editor' ),
      'relation' => 'or',
     ),
     'fields' => array(
       array(
         'name'  => '',
         'desc'  => 'Display on phila.gov homepage?',
         'id'    => 'show_on_home',
         'type'  => 'radio',
         'std'=> '0',
         'options' =>  array(
             '0' => 'No',
             '1' => 'Yes'
         )
       ),
     )
  );

  $meta_boxes[] = array(
    'id'       => 'document-description',
    'title'    => 'Document Information',
    'pages'    => array( 'document' ),
    'context'  => 'normal',
    'priority' => 'high',
    'revision' => true,

    'fields' => array(
        array(
         'id'   => 'phila_document_description',
         'type' => 'wysiwyg',
         'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic(),
         'desc' => 'Information describing the collection of documents on this page. This content will appear above the document list.'
       ),
       array(
         'type'  => 'heading',
         'name' => ' Release Date',
       ),
       array(
         'id'   => 'phila_override_release_date',
         'name'  => 'Override all release dates on this page with the date below?',
         'type' => 'switch',
         'on_label'  => 'Yes',
         'off_label' => 'No'
       ),
       array(
        'id'    => 'phila_document_released',
        'type'  => 'date',
        'class' =>  'document-released',
        'size'  =>  25,
        'js_options' =>  array(
          'dateFormat'=>'MM dd, yy',
          'showTimepicker' => false
        )
      ),
    )
  );
  $meta_boxes[] = array(
    'id'       => 'document-meta',
    'title'    => 'Files',
    'pages'    => array( 'document' ),
    'context'  => 'normal',
    'priority' => 'high',
    'revision' => true,

    'fields' => array(
      array(
        'name'  => 'Add Files',
        'id'    => 'phila_files',
        'type'  => 'file_advanced',
        'class' =>  'add-files',
        'mime_type' => 'application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document,
        application/vnd.ms-powerpointtd, application/vnd.openxmlformats-officedocument.presentationml.presentation,
        application/vnd.ms-excel,
        application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,
        text/plain'
      ),
    ),
  );

  $meta_boxes[] = array(
    'id'       => 'press-release-date',
    'title'    => 'Release Date',
    'pages'    => array( 'post' ),
    'context'  => 'advanced',
    'priority' => 'high',
    'visible' => array(
      'when' => array(
        array('phila_template_select', '=', 'press_release'),
      ),
    ),

    'fields' => array(
      array(
        'name'  => 'Release Date',
        'id'    => 'phila_press_release_date',
        'type'  => 'date',
        'class' =>  'press-release-date',
        'size'  =>  30,
        'required'  => true,
        'js_options' =>  array(
          'dateFormat'=>'MM dd, yy',
          'showTimepicker' => false
        )
      ),
    ),
  );

  $meta_boxes[] = array(
    'title'    => 'Contact Information',
    'pages'    => array( 'post' ),
    'context'  => 'advanced',
    'priority' => 'high',
    'visible' => array(
      'when' => array(
        array('phila_template_select', '=', 'press_release'),
      ),
    ),

    'fields' => array(
      array(
        'id'  => 'press_release_contact',
        'type' => 'group',
        'clone'  => true,
        'fields' => array(
          array(
            'name' => 'Contact name',
            'id'   => 'phila_press_release_contact_name',
            'type' => 'text',
            'required'  => true,
           ),
          array(
            'name' => 'Contact phone',
            'id'   => 'phila_press_release_contact_phone_number',
            'type' => 'phone',
            'placeholder' => '(215) 686-2181'
          ),
          array(
            'name' => 'Contact email',
            'id'   => 'phila_press_release_contact_email',
            'type' => 'text',
            'std' => 'press@phila.gov',
            'required'  => true,
          ),
         ),
       )
     ),
  );

  $meta_boxes[] = array(
    'id'       => 'phila_resource_list',
    'title'    => __( 'Resource List' ),
    'pages'    => array( 'department_page', 'page' ),
    'context'  => 'normal',
    'priority' => 'high',
    'visible' => array(
      'when' => array(
        array('phila_template_select', '=', 'resource_list'),
        array(  'phila_template_select','=', 'resource_list_v2'),
      ),
      'relation' => 'or'
    ),

    'fields' => array(
      array(
        'id'  => 'phila_resource_list',
        'type' => 'group',
        'clone'  => true,
        'sort_clone' => true,
        'add_button' => '+ Add a Resource List',

        'fields' => array(
          array(
            'name' => __('List Title', 'rwmb'),
            'id'   => 'phila_resource_list_title',
            'type' => 'text',
            'required' => true,
          ),
          array(
            'id'   => 'phila_resource_list_items',
            'type' => 'group',
            'clone'  => true,
            'sort_clone' => true,
            'add_button' => '+ Add an item',

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
                   'type' => 'select',
                   'placeholder' => 'Choose icon...',
                   'options' => array(
                     'phila_resource_link' => 'Link',
                     'phila_resource_document' => 'Document',
                     'phila_resource_map' => 'Map',
                   ),
                  ),
                  array(
                     'name' => __('Featured Resource', 'rwmb'),
                     'id'   => 'phila_featured_resource',
                     'class'   => 'phila_featured-resource',
                     'type' => 'checkbox',
                  ),
                  array(
                    'name' => __('Alternate Featured Title', 'rwmb'),
                    'id'   => 'phila_list_item_alt_title',
                    'type' => 'text',
                  ),
                  array(
                     'name' => __('Featured Resource Summary', 'rwmb'),
                     'id'   => 'phila_featured_summary',
                     'class'   => 'phila_featured-summary',
                     'type' => 'textarea',
                     //TODO: Conditional logic doesn't appear to work on cloned fields.
                     //'hidden' => array( 'phila_featured_resource', '!=', true ),
                  ),
                  array(
                    'name'  => 'Display Order',
                    'id'    => 'phila_display_order',
                    'type'  => 'select',
                    'class' => 'display-order',
                    'options' => array(
                      '1' => '1',
                      '2' => '2',
                      '3' => '3',
                      '4' => '4',
                    ),
                  ),
                ),
              ),
            ),
          ),
        ),
      );

 // Hero Header
 $meta_boxes[] = array(
   'id'       => 'hero-header',
   'title'    => 'Hero Header',
   'pages'    => array( 'department_page' ),
   'context'  => 'normal',
   'priority' => 'high',

   'include' => array(
     'user_role'  => array( 'administrator', 'primary_department_homepage_editor', 'editor' ),
   ),
   'hidden' => array(
     'when'  => array(
       array('phila_template_select', 'ends with', 'v2')
     ),
     'relation' => 'or',
   ),
   'visible'  => array(
     'when' => array(
       array('phila_template_select', '=', 'default' ),
       array('phila_template_select', '=', 'department_homepage' ),
     ),
     'relation' => 'or',
   ),

   'fields' => array(
     array(
       'name'  => 'Hero Header Title',
       'id'    => 'phila_hero_header_title',
       'type'  => 'text',
       'class' => 'hero-header-title',
       'desc'  => 'Title that will be placed over the header image.',
       'size'  => '60'
     ),
     array(
       'name'  => 'Hero Header Title (line 1)',
       'id'    => 'phila_hero_header_title_l1',
       'type'  => 'text',
       'class' => 'hero-header-title-l1',
       'desc'  => 'Portion of the title that will be displayed in standard case. Maximum of 20 characters',
       'size'  => '60'
     ),
     array(
       'name'  => 'Hero Header Title (line 2)',
       'id'    => 'phila_hero_header_title_l2',
       'type'  => 'text',
       'class' => 'hero-header-title-l2',
       'desc'  => 'Portion of the title that will be displayed in a larger font and forced uppercase. Maximum of 15 characters.',
       'size'  => '60'
     ),
     array(
       'name'  => 'Hero Header Body Copy',
       'id'    => 'phila_hero_header_body_copy',
       'type'  => 'textarea',
       'class' => 'hero-header-body-copy',
       'desc'  => 'Text that will be placed over the header image and below the Hero Header Title.',
       'size'  => '60',
     ),
     array(
       'name'  => 'Call to Action Button URL',
       'desc'  => 'Optional URL to include as a "call to action" button',
       'id'    => 'phila_hero_header_call_to_action_button_url',
       'type'  => 'URL',
       'class' => 'hero-header-call-to-action-button-url',
     ),
     array(
       'name'  => 'Call to Action Button Text',
       'id'    => 'phila_hero_header_call_to_action_button_text',
       'type'  => 'text',
       'class' => 'hero-header-call-to-action-button-text',
       'desc'  => 'Text that appears on the "call to action" button.',
       'size'  => '30',
     ),
     array(
       'name'  => 'Image',
       'id'    => 'phila_hero_header_image',
       'type'  => 'file_input',
       'class' => 'hero-header-image',
       'desc'  => 'Image should be no smaller than 975px by 430px . '
     ),
     array(
       'name'  => 'Image Alt Text',
       'id'    => 'phila_hero_header_image_alt_text',
       'type'  => 'text',
       'class' => 'hero-header-image-alt-text',
       'desc'  => 'A short description used to inform visitors about the contents of an image.',
       'size'  => '60'
     ),
     array(
       'name' => 'Image Credit',
       'id'   => 'phila_hero_header_image_credit',
       'type' => 'text',
       'class' => 'hero-header-image-credit',
       'desc'  => 'Provide attribution information when necessary.',
       'size'  => '60'
     ),
   )
 );

 // First row of modules - Options have been reduced to What we do + connect circa V2 Department homepages
 $meta_boxes[] = array(
   'id'       => 'phila_module_row_1',
   'title'    => 'Two-thirds row',
   'pages'    => array( 'department_page' ),
   'context'  => 'normal',
   'priority' => 'default',
   'revision' => true,

   'visible' => array(
     'when'  => array(
       array('phila_template_select', '=', 'homepage_v2' ),
       array('phila_template_select', '=', 'department_homepage' ),
     ),
     'relation' => 'or',
   ),

   'fields' => array(
     array(
      'name' => 'Description',
      'id'   => 'phila_module_row_1_description',
      'type' => 'custom_html',
      'std'  => '<span>Use this area to create a row that will be divided into two columns. The first column will take up 2/3 of the screen and second will take up 1/3.</span>',
     ),
     array(
       'type' => 'divider',
     ),
     array(
      'id' => 'module_row_1_col_1',
      'type' => 'group',
      // List of sub-fields
      'fields' => array(
         array(
          'name' => 'Column 1 <br/><small>(2/3 width column)</small>',
          'id'   => 'phila_module_row_1_col_1_type',
          'desc'  => 'Choose to display recent blog posts or custom markup text.',
          'type' => 'select',
          'placeholder' => 'Select...',
          'std' => 'phila_module_row_1_col_1_custom_text',
          'options' => array(
            'phila_module_row_1_col_1_custom_text' => 'Custom Text',
            ),
          ),
          array(
            'id' => 'module_row_1_col_1_options',
            'type' => 'group',
            // List of sub-fields
            'fields' => array(
              array(
               'name' => 'Post Style',
               'id'   => 'phila_module_row_1_col_1_post_style',
               'desc'  => 'Recent posts are displayed as "Cards" by default.',
               'type' => 'select',
               'placeholder' => 'Choose display style...',
               'required'  => true,
               'options' => array(
                 'phila_module_row_1_col_1_post_style_cards' => 'Card',
                 'phila_module_row_1_col_1_post_style_list' => 'List',
               ),
               'hidden' => array(
                 'when'  => array(
                   array('phila_module_row_1_col_1_type', '=', 'phila_module_row_1_col_1_custom_text' ),
                   array('phila_module_row_1_col_1_type', '=', '' ),
                 ),
                 'relation' => 'or',
               ),

             ),
             array(
              'name' => 'Custom Text Title',
              'id'   => 'phila_module_row_1_col_1_texttitle',
              'type' => 'text',
              'hidden' => array('phila_module_row_1_col_1_type', '!=', 'phila_module_row_1_col_1_custom_text'),

             ),
             array(
              'name' => 'Custom Text Content',
              'id'   => 'phila_module_row_1_col_1_textarea',
              'type' => 'wysiwyg',
              'hidden' => array('phila_module_row_1_col_1_type', '!=', 'phila_module_row_1_col_1_custom_text'),
              'options' =>                     Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic(),
             ),
            ),
          ),
        ),
      ),
    array(
      'type' => 'divider'
    ),
    array(
      'id' => 'module_row_1_col_2',
      'type' => 'group',
      'fields' => array(
         array(
          'name' => 'Column 2 <br/><small>(1/3 column)</small>',
          'id'   => 'phila_module_row_1_col_2_type',
          'desc'  => 'Choose to display recent blog posts, custom markup, call to action panel, or a connect panel.',
          'type' => 'select',
          'placeholder' => 'Select...',
          'options' => array(
            'phila_module_row_1_col_2_blog_posts' => 'Blog Posts',
            'phila_module_row_1_col_2_call_to_action_panel' => 'Call to Action Panel',
            'phila_module_row_1_col_2_connect_panel' => 'Connect Panel',
            'phila_module_row_1_col_2_custom_text' => 'Custom Text',
          ),
        ),
        array(
          'id' => 'module_row_1_col_2_options',
          'type' => 'group',
          'fields' => array(
             array(
              'name' => 'Custom Text Title',
              'id'   => 'phila_module_row_1_col_2_texttitle',
              'type' => 'text',
              //TODO Move these hidden fields up to the parent. This will change the metabox IDs and effect live content, so let's hold off for now.
              'hidden' => array('phila_module_row_1_col_2_type', '!=', 'phila_module_row_1_col_2_custom_text'),
             ),
             array(
              'name' => 'Custom Text Content',
              'id'   => 'phila_module_row_1_col_2_textarea',
              'type' => 'textarea',
              'hidden' => array('phila_module_row_1_col_2_type', '!=', 'phila_module_row_1_col_2_custom_text'),
             ),
           ),
          ),
        ),
      ),
      array(
        'id' => 'module_row_1_col_2_call_to_action_panel',
        'type' => 'group',
        'hidden' => array('phila_module_row_1_col_2_type', '!=', 'phila_module_row_1_col_2_call_to_action_panel'),
        'fields' => array(
          array(
            'name'  => 'Section Title',
            'id'    => 'phila_action_section_title',
            'type'  => 'text',
            'class' => 'percent-100'
          ),
        array(
          'name'  => 'Call to Action Text',
          'id'    => 'phila_action_panel_cta_text',
          'type'  => 'text',
          'class' => 'action-panel-cta-text',
        ),
        array(
          'name'  => 'Summary',
          'id'    => 'phila_action_panel_summary',
          'type'  => 'textarea',
          'class' => 'action-panel-details',
        ),
        array(
          'name'  => 'Icon',
          'id'    => 'phila_action_panel_fa',
          'type'  => 'text',
          'class' => 'action-panel-fa',
        ),
        array(
          'name'  => 'Icon Background Circle',
          'id'    => 'phila_action_panel_fa_circle',
          'type'  => 'switch',
          'class' => 'action-panel-fa',
          'on_label'  => 'Yes',
          'off_label' => 'No'
        ),
        array(
          'name'  => 'Link to Content',
          'id'    => 'phila_action_panel_link',
          'type'  => 'url',
          'class' => 'action-panel-link',
        ),
        array(
          'name'  => 'This link directs users away from beta.phila.gov',
          'id'    => 'phila_action_panel_link_loc',
          'type'  => 'switch',
          'class' => 'action-panel-link-loc',
          'on_label'  => 'Yes',
          'off_label' => 'No'
          ),
        ),
      ),
      array(
        'id' => 'module_row_1_col_2_connect_panel',
        'type' => 'group',
        'hidden' => array('phila_module_row_1_col_2_type', '!=', 'phila_module_row_1_col_2_connect_panel'),

        'fields' => Phila_Gov_Standard_Metaboxes::phila_meta_var_connect()
        ),
    )
  );

// Second row of modules - Calendar
$meta_boxes[] = array(
  'id'       => 'phila_module_row_2',
  'title'    => 'Full row calendar',
  'pages'    => array( 'department_page' ),
  'context'  => 'normal',
  'priority' => 'default',
  'revision' => true,

  'include' => array(
    'user_role'  => array( 'administrator', 'editor', 'primary_department_homepage_editor' ),
  ),
  'visible' => array(
    'when'  => array(
      array('phila_template_select', '=', 'homepage_v2' ),
      array('phila_template_select', '=', 'department_homepage' ),
    ),
    'relation' => 'or',
  ),

    // List of sub-fields
    'fields' => Phila_Gov_Standard_Metaboxes::phila_metabox_v2_calendar_full(),
  );

  $meta_boxes[] = array(
    'id'       => 'phila_staff_directory_listing',
    'title'    => 'Staff Directory Listing',
    'pages'    => array( 'department_page' ),
    'context'  => 'normal',
    'priority' => 'default',


    'include' => array(
      'user_role'  => array(
        'administrator', 'primary_department_homepage_editor', 'editor' ),
    ),
    'visible' => array(
      'when' => array(
        array( 'phila_template_select', '=', 'homepage_v2'),
      ),
      'relation' => 'or',
    ),

    'fields' => array(
      array(
        'id'   => 'phila_staff_directory_selected',
        'name'  => 'Display a staff directory list?',
        'type' => 'switch',
        'on_label'  => 'Yes',
        'off_label' => 'No',
        'after' => '<p class="description">Enter at least one staff member in the <a href="/wp-admin/edit.php?post_type=staff_directory">Staff Members</a> section.</p>',
      ),
      array(
        'id'  => 'phila_get_staff_cats',
        'type' => 'group',
        'visible' => array(
          'when' => array(
            array( 'phila_staff_directory_selected', '=', 1 ),
          ),
          'relation' => 'or',
        ),
        'fields' => array(
          Phila_Gov_Standard_Metaboxes::phila_metabox_category_picker('Select new owner', 'phila_staff_category', 'Display staff members from these owners. This will override page ownership selection entirely.' ),
        ),
      ),
    ),
  );

  $meta_boxes[] = array(
    'id'  => 'board_commission_member_list',
    'title' => 'Commission or board member listing',
    'pages' => array('department_page'),
    'context' => 'normal',
    'priority'  => 'default',

    'include' => array(
      'user_role'  => array( 'administrator', 'primary_department_homepage_editor', 'editor' ),
    ),
    'visible' => array(
      'when' => array(
        array( 'phila_template_select', '=', 'homepage_v2'),
        array( 'phila_template_select', '=', 'staff_directory_v2'),
      ),
      'relation' => 'or',
    ),

    'fields'  => array(
      array(
        'id'  => 'section_title',
        'type'  => 'text',
        'name'  => 'Optional row title',
      ),
      Phila_Gov_Standard_Metaboxes::phila_meta_var_commission_members()
    ),

  );

  $meta_boxes[] = array(
    'id'       => 'phila_full_row_press_releases',
    'title'    => 'Full row press releases posts (3 total)',
    'pages'    => array( 'department_page' ),
    'context'  => 'normal',
    'priority' => 'default',

    'include' => array(
      'user_role'  => array( 'administrator', 'primary_department_homepage_editor', 'editor' ),
    ),
    'visible' => array(
      'when' => array(
        array( 'phila_template_select', '=', 'homepage_v2'),
      ),
      'relation' => 'or',
    ),
    'fields' => array(
      array(
        'name'  => 'Display a full row of press releases?',
        'id'   => 'phila_full_row_press_releases_selected',
        'type' => 'switch',
        'on_label'  => 'Yes',
        'off_label' => 'No',
        'after' => '<p class="description">Enter at least three press releases in the <a href="/wp-admin/edit.php?post_type=press_release">Press release</a> section.</p>'
      ),
      array(
        'id'  => 'phila_get_press_cats',
        'type' => 'group',
        'visible' => array(
          'when' => array(
            array( 'phila_full_row_press_releases_selected', '=', 1 ),
          ),
          'relation' => 'or',
        ),
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
    ),
  );

  $meta_boxes[] = array(
    'id'       => 'phila_full_row_blog',
    'title'    => 'Full row blog posts (3 total)',
    'pages'    => array( 'department_page' ),
    'context'  => 'normal',
    'priority' => 'default',

    'include' => array(
      'user_role'  => array( 'administrator', 'primary_department_homepage_editor', 'editor' ),
    ),
    'visible' => array(
      'when' => array(
        array( 'phila_template_select', '=', 'homepage_v2'),
      ),
      'relation' => 'or',
    ),

    'fields' => array(
      array(
        'name' => 'Display a full row of blog posts?',
        'id'   => 'phila_full_row_blog_selected',
        'type' => 'switch',
        'on_label'  => 'Yes',
        'off_label' => 'No',
        'after' => '<p class="description">Enter at least three blog posts in the <a href="/wp-admin/edit.php?post_type=phila_post">Blog Post</a> section.</p>'
      ),
      array(
        'id'  => 'phila_get_post_cats',
        'type' => 'group',
        'visible' => array(
          'when' => array(
            array( 'phila_full_row_blog_selected', '=', 1 ),
          ),
          'relation' => 'or',
        ),
        'fields' => array(
          Phila_Gov_Standard_Metaboxes::phila_metabox_category_picker('Select owners', 'phila_post_category', 'Display posts from these owners. This will override page ownership selection entirely.'),
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
    ),
  );

  $meta_boxes[] = array(
    'id'  => 'phila_call_to_action_multi',
    'title' => 'Call to action cards',
    'pages' => array( 'department_page' ),
    'context' => 'normal',
    'priority' => 'default',
    'revision' => true,

    'include' => array(
      'user_role'  => array( 'administrator', 'primary_department_homepage_editor', 'editor' ),
    ),
    'visible' => array(
      'when' => array(
        array( 'phila_template_select', '=', 'homepage_v2'),
      ),
      'relation' => 'or',
    ),

    'fields' => array(
      array(
        'id'  => 'phila_call_to_action_section',
        'type' => 'group',

        'fields' => array(
          array(
            'name'  => 'Section Title',
            'id'    => 'phila_action_section_title_multi',
            'type'  => 'text',
          ),

        array(
          'id'  => 'phila_call_to_action_multi_group',
          'type' => 'group',
          'clone'  => true,
          'max_clone' => 4,
          'sort_clone' => true,

          'fields' =>
            Phila_Gov_Standard_Metaboxes::phila_call_to_action_group_content()
          ),
        ),
      ),
    ),
  );

/**
*
* Begin MetaBox Field Arrays
*
**/

// Program and Initiatives
$meta_var_programs_initiatives_images = array(
  'id' => 'phila_p_i_images',
  'type' => 'group',
  'visible' => array('phila_template_select', 'programs_initiatives'),

  'fields' => array(
    array(
      'name' => 'Header Image',
      'id' => 'phila_p_i_header',
      'type' => 'file_input',
    ),
    array(
      'name' => 'Featured Image',
      'id' => 'phila_p_i_featured',
      'type' => 'file_input',
    ),
    array(
      'name' => 'Short Feature Description',
      'id' => 'phila_short_feat_desc',
      'type' => 'textarea',
    ),
    array(
      'name' => 'Long Feature Description',
      'id' => 'phila_long_feat_desc',
      'type' => 'textarea',
    ),
  ),
  'visible' => array('phila_template_select', '=', 'programs_initiatives'),
);


//Clonable WYSIWYG with title
$meta_var_wysiwyg_multi = array(
  'id'  =>  'phila_cloneable_wysiwyg',
  'type'  => 'group',
  'clone' => true,
  'sort_clone'  => true,
  'add_button'  => '+ Add a section',

  'fields'  => array(
    array(
      'placeholder'  => 'Section Heading',
      'id'  => 'phila_wysiwyg_heading',
      'type'  => 'text',
      'class' => 'percent-100'
    ),
    array(
      'id'  => 'phila_wysiwyg_content',
      'type'  => 'wysiwyg',
      'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic()
    )
  )
);

//Group all "Page" related content
$meta_related_content = array(
  'id'  => 'phila_related',
  'type'  => 'group',

  'fields'  => array(
    array(
      'name'  => 'Related Content',
      'type'  => 'heading'
    ),
    array(
      'id'  => 'phila_related_content',
      'type'  => 'wysiwyg',
      'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic()
    ),
  )
);

//Questions metabox, used for Pages
$meta_questions = array(
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
);

//Did you know, used for Pages
$meta_did_you_know = array(
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
);

//Department Homepage metaboxes
$meta_boxes[] = array(
  'id'       => 'phila_department_homepage',
  'title'    => 'Page Content',
  'pages' => array( 'department_page' ),
  'priority' => 'high',
  'revision' => true,

  'hidden'  => array('phila_template_select', 'ends with', 'v2'),

  'fields' => array(
    $meta_var_programs_initiatives_images,
    Phila_Gov_Row_Metaboxes::phila_metabox_grid_row(),
  )
);


$meta_boxes[] = array(
  'title' => 'Service Stub',
  'pages' => array('service_page'),
  'priority' => 'high',
  'visible' => array('phila_template_select', 'service_stub'),
  'revision' => true,

  'fields'  => array(
    array(
      'name' => 'Page source',
      'type'  => 'heading'
    ),
    array(
      'id' => 'phila_stub_source',
      'type' => 'post',
      'post_type' => 'service_page',
      'desc'  => 'Display content from the selected page on the front-end.',
      'query_args'  => array(
        'meta_key' => 'phila_template_select',
        'meta_value' => 'service_stub',
        'meta_compare' => '!=',
      ),
    )
  )
);

$meta_boxes[] = array(
  'title' => 'Program + initiatives association',
  'pages' => array('service_page'),
  'context'  => 'side',
  'fields'  => array(
    array(
      'id'  => 'display_prog_init',
      'name'  => 'Should this page appear as "Related content" on the programs and initiatives landing page?',
      'type'  => 'switch',
      'on_label'  => 'Yes',
      'off_label' => 'No'
    )
  ),
);

$meta_boxes[] = array(
  'title' => 'Topic Page Options',
  'pages' => array('service_page'),
  'priority' => 'high',
  'visible' => array('phila_template_select', 'topic_page'),

  'fields'  => array(
    array(
      'name' => 'Contextual item',
      'type'  => 'heading'
    ),
    array(
      'id'  => 'phila_is_contextual',
      'type' => 'switch',
      'on_label'  => 'Yes',
      'off_label' => 'No',
      'name'  => 'Should this page appear in the service directory? The children of this item will appear in the service directory with this page\'s title appended to them.',
      'columns' => 6,

    ),
    array(
      'name' => 'Icon selection',
      'type'  => 'heading'
    ),
    array(
      'id' => 'phila_page_icon',
      'type' => 'text',
      'desc'  => 'Choose a <a href="http://fontawesome.io/icons/" target="_blank">Font Awesome</a> icon to represent a top-level page. E.g.: fa-bell'
    )
  )
);

$meta_forms_instructions = array(
  'id'  => 'phila_forms_instructions',
  'type'  => 'group',

  'fields'  => array(
    array(
      'name'  => 'Forms & Instructions',
      'type'  => 'heading'
    ),
    Phila_Gov_Standard_Metaboxes::phila_metabox_v2_document_page_selector()
  )
);

$meta_boxes[] = array(
  'title' => 'Before you start',
  'pages' => array('service_page'),
  'revision' => true,
  'visible' => array(
    'when' => array(
      array( 'phila_template_select', '=', 'start_process' ),
    ),
  ),
  'fields' => array(
    array(
      'id' => 'phila_start_process',
      'type'  => 'group',
      'clone' => false,

      'fields' => array(
        array(
          'id'  => 'phila_wysiwyg_process_content',
          'type'  => 'wysiwyg',
          'options' => Phila_Gov_Standard_Metaboxes::phila_wysiwyg_options_basic_heading()
        ),
        Phila_Gov_Standard_Metaboxes::phila_metabox_v2_link_fields('Button details', 'phila_start_button'),
      )
    )
  )
);


$meta_boxes[] = array(
  'title' => 'Heading Groups',
  'pages' => array('department_page', 'page', 'service_page', 'programs'),
  'revision' => true,

  'visible' => array(
    'when' => array(
      array( 'phila_template_select', '=', ''),
      array( 'phila_template_select', '=', 'one_quarter_headings_v2' ),
      array( 'phila_template_select', '=', 'phila_one_quarter' ),
      array( 'phila_template_select', '=', 'default'),
      array( 'phila_template_select', '=', 'start_process'),
    ),
    'relation' => 'or',
  ),

  'fields' => array(
    array(
      'id' => 'phila_heading_groups',
      'type'  => 'group',
      'clone' => false,

      'fields' => array(
        Phila_Gov_Standard_Metaboxes::phila_metabox_v2_address_fields_unique(),
      ),
    )
  )
);

$meta_boxes[] = array(
  'title' => 'Additional Content',
  'pages' => array('page', 'service_page'),
  'revision' => true,
  'fields' => array(
    array(
      'id' => 'phila_additional_content',
      'type'  => 'group',
      'clone' => false,

      'fields' => array(
        $meta_forms_instructions,
        $meta_related_content,
        $meta_did_you_know,
        $meta_questions
      )
    )
  ),
  'hidden' => array(
    'when'  => array(
      array('phila_template_select', '=', 'topic_page'),
      array('phila_template_select', '=', 'service_stub')
    ),
    'relation' => 'or',
  ),
);

$meta_boxes[] = array(
  'title' => 'Document tables',
  'pages' => array('department_page'),
  'revision' => true,
  'visible' => array(
    'when'  => array(
      array('phila_template_select', '=', 'document_finder_v2'),
    ),
  ),
  'fields' => array(
    array(
      'id' => 'phila_document_table',
      'type'  => 'group',
      'clone' => true,
      'sort_clone' => true,
      'add_button'  => 'Add another table',

      'fields' =>
      array(
        Phila_Gov_Standard_Metaboxes::phila_metabox_v2_wysiwyg( $section_title = 'Table title', $wysiwyg_desc = 'Enter a description to describe the contents of this table for users with screenreaders. '),
        array(
          'name'  => 'Add files to table',
          'id'    => 'phila_files',
          'type'  => 'file_advanced',
          'class' =>  'add-files',
          'mime_type' => 'application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document,
          application/vnd.ms-powerpointtd, application/vnd.openxmlformats-officedocument.presentationml.presentation,
          application/vnd.ms-excel,
          application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,
          text/plain,
          application/zip'
        ),
      )
    )
  ),
);

$meta_boxes[] = array(
  'title' => 'Our services',
  'pages'    => array( 'department_page', 'programs' ),
  'visible' => array(
    'when'  =>  array(
        array('phila_template_select', '=', 'homepage_v2'),
        array('phila_template_select', '=', 'prog_landing_page')
      ),
    'relation'  => 'or'
  ),

  'fields' => array(
    array(
      'id'       => 'phila_v2_homepage_services',
      'title'    => 'Top services',
      'context'  => 'normal',
      'name'  => '<div>Add a maximum of 6 services to a homepage.</div>',
      'priority' => 'high',
      'type'  => 'group',
      'clone' => true,
      'sort_clone' => true,
      'max_clone' => 6,
      'add_button'  => '+ Add another service link',

      'fields' => array(
        Phila_Gov_Standard_Metaboxes::phila_v2_icon_selection(),
        Phila_Gov_Standard_Metaboxes::phila_v2_service_page_selector(),
        Phila_Gov_Standard_Metaboxes::phila_metabox_title( 'Alternate title', 'alt_title' ),
      ),
    ),
    array(
      'id' => 'phila_v2_service_link',
      'title' => 'All services url',
      'name'  => 'All services url',
      'type'  => 'url',
      'class' => 'metabox-url',
    ),
  ),
);

$meta_boxes[] = array(
  'id'       => 'homepage_programs',
  'title'    => 'Our programs',
  'pages'    => array( 'department_page' ),
  'context'  => 'normal',
  'visible' => array(
    'when'  =>  array(
        array('phila_template_select', '=', 'homepage_v2'),
      ),
    ),
  'fields' => array(
    Phila_Gov_Standard_Metaboxes::phila_program_page_selector($multiple = true),
  )
);


$meta_boxes[] = array(
  'id'       => 'phila_custom_markup',
  'title'    => 'Custom Markup',
  'pages'    => array( 'department_page', 'page', 'service_page', 'programs' ),
  'context'  => 'normal',
  'priority' => 'low',

   'include' => array(
     'user_role'  => array( 'administrator', 'primary_department_homepage_editor', 'editor' ),
   ),

  'fields' => array(
    array(
     'name' => 'Description',
     'id'   => 'phila_custom_markup_description',
     'type' => 'custom_html',
     'std'  => '<span>Use this area to insert CSS, HTML or JS.</span>',
   ),
   array(
      'name' => 'Append to head',
      'id'   => 'phila_append_to_head',
      'type' => 'textarea'
    ),
    array(
     'name' => 'Append before WYSIWYG',
     'id'   => 'phila_append_before_wysiwyg',
     'type' => 'textarea'
   ),
   array(
     'name' => 'Append after WYSIWYG',
     'id'   => 'phila_append_after_wysiwyg',
     'type' => 'textarea'
   ),
   array(
      'name' => 'Append after footer',
      'id'   => 'phila_append_after_footer',
      'type' => 'textarea'
    ),
 ),
);


return $meta_boxes;

}

/**
 *
 * Returns true/false based on existence of secondary role. Metabox user_role doesn't see secondary roles, so a custom function is required.
 *
 **/
add_action( 'admin_head', 'phila_master_homepage_editor' );

function phila_master_homepage_editor(){

  $assigned_roles = array_values( wp_get_current_user()->roles );

  if (in_array( 'secondary_master_homepage_editor', $assigned_roles )){
    return true;
  }
  return false;
}

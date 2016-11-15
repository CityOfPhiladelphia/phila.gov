<?php
/**
 * Registers all the metaboxes we ever will need
 *
 * @link https://github.com/CityOfPhiladelphia/phila.gov-customization
 *
 * @package phila-gov_customization
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
    'monday'  => 'Monday',
    'tuesday' => 'Tuesday',
    'wednesday' => 'Wednesday',
    'thursday'  => 'Thursday',
    'friday'  => 'Friday',
    'saturday'  => 'Saturday',
    'sunday'  => 'Sunday'
  );

  return $week_array;
}

function phila_setup_tiny_mce_basic( array $options ){

  $output = '';

  $defaults = array(
    'format_select' => false,
    'heading_level' => 'h3',
  );

  $options = array_merge($defaults, $options);

  if ( $options['format_select'] == true) {
    $output['toolbar1'] = 'formatselect, bold, italic, bullist, numlist, link, unlink, outdent, indent, removeformat, pastetext';

  }

  if ( $options['heading_level'] == 'h3' ) {
    $output['block_formats'] = 'Paragraph=p; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6;';

  }elseif( $options['heading_level'] == 'h2' ){
    $output['block_formats'] = 'Paragraph=p;  Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6;';
  }else{
    $output['block_formats'] = 'Paragraph=p; Heading 1=h1; Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6;';
  }

  if ( $options['format_select'] == false ) {

    $output['toolbar1'] = 'bold, italic, bullist, numlist, link, unlink, outdent, indent, removeformat, pastetext';

  }
  return $output;

}

add_filter( 'rwmb_meta_boxes', 'phila_register_meta_boxes' );

function phila_register_meta_boxes( $meta_boxes ){

  $department_col_1_custom_content['toolbar1'] = 'bold, italic, bullist, numlist, link, unlink, outdent, indent, removeformat, pastetext';

  $basic_editor['toolbar1'] = 'bold, italic, bullist, numlist, link, unlink, outdent, indent, removeformat, pastetext';


  $meta_boxes[] = array(
    'id'       => 'news-admin-only',
    'title'    => 'Homepage Display',
    'pages'    => array( 'news_post' ),
    'context'  => 'side',
    'priority' => 'high',

    'include' => array(
      'relation' => 'OR',
      'user_role' => 'administrator',
      'custom'  => 'phila_master_homepage_editor'
    ),

    'fields' => array(
      array(
        'name'  => '',
        'desc'  => 'Should this story appear on the homepage?',
        'id'    => 'phila_show_on_home',
        'type'  => 'radio',
        'std'=> '0',
        'options' =>  array(
            '0' => 'No',
            '1' => 'Yes'
        )
      ),
    )
  );//news homepage display

  $meta_boxes[] = array(
    'id'       => 'document-description',
    'title'    => 'Document Information',
    'pages'    => array( 'document' ),
    'context'  => 'normal',
    'priority' => 'high',


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
        'desc'  => 'Set the release date for all items on this document page. You can change an individual release date by editing the document below.',
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
    'pages'    => array( 'press_release' ),
    'context'  => 'advanced',
    'priority' => 'high',


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
    'pages'    => array( 'press_release' ),
    'context'  => 'advanced',
    'priority' => 'high',


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
            'id'   => 'phila_press_release_contact_phone',
            'type' => 'text',
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

  $meta_boxes[] = array(
    'id'       => 'phila_custom_markup',
    'title'    => 'Custom Markup',
    'pages'    => array( 'department_page', 'event_page' , 'page', 'service_page' ),
    'context'  => 'normal',
    'priority' => 'low',

    'include' => array(
      'user_role'  => 'administrator',
    ),

    'fields' => array(
      array(
       'name' => 'Description',
       'id'   => 'phila_custom_markup_description',
       'type' => 'custom_html',
       'std'  => '<span>Use this area to insert CSS, HTML or JS.</span>',
     ),
     array(
        'name' => 'Append to Head',
        'id'   => 'phila_append_to_head',
        'type' => 'textarea'
      ),
      array(
       'name' => 'Append Before WYSIWYG',
       'id'   => 'phila_append_before_wysiwyg',
       'type' => 'textarea'
     ),
     array(
       'name' => 'Append After WYSIWYG',
       'id'   => 'phila_append_after_wysiwyg',
       'type' => 'textarea'
     ),
   ),
 );

 // Hero Header
 $meta_boxes[] = array(
   'id'       => 'hero-header',
   'title'    => 'Hero Header',
   'pages'    => array( 'department_page' , 'event_page' ),
   'context'  => 'normal',
   'priority' => 'high',


   'include' => array(
     'user_role'  => array( 'administrator', 'primary_department_homepage_editor' ),
   ),
   'hidden' => array(
     'phila_template_select', '=', 'off_site_department',
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
       'hidden' => array(
         'phila_template_select', '=', 'one_page_department',
       ),
     ),
     array(
       'name'  => 'Call to Action Button URL',
       'desc'  => 'Optional URL to include as a "call to action" button',
       'id'    => 'phila_hero_header_call_to_action_button_url',
       'type'  => 'URL',
       'class' => 'hero-header-call-to-action-button-url',
       'hidden' => array(
         'phila_template_select', '=', 'one_page_department',
       ),
     ),
     array(
       'name'  => 'Call to Action Button Text',
       'id'    => 'phila_hero_header_call_to_action_button_text',
       'type'  => 'text',
       'class' => 'hero-header-call-to-action-button-text',
       'desc'  => 'Text that appears on the "call to action" button.',
       'size'  => '30',
       'hidden' => array(
         'phila_template_select', '=', 'one_page_department',
       ),
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

 // First row of modules - recent posts, custom markup, call to action panel
 $meta_boxes[] = array(
   'id'       => 'phila_module_row_1',
   'title'    => 'Row 1',
   'pages'    => array( 'department_page' ),
   'context'  => 'normal',
   'priority' => 'low',

   'include' => array(
     'user_role'  => array( 'administrator', 'primary_department_homepage_editor' ),
   ),
   'hidden' => array(
     'phila_template_select', '=', 'off_site_department',
   ),

   'fields' => array(
     array(
      'name' => 'Description',
      'id'   => 'phila_module_row_1_description',
      'type' => 'custom_html',
      'std'  => '<span>Use this area to create a row that will be divided into two columns. The first column will take up 2/3 of the screen and second will take up 1/3.</span>',
      'hidden' => array(
        'phila_template_select', '=', 'one_page_department',
      ),
     ),
     array(
       'type' => 'divider',
       'hidden' => array(
         'phila_template_select', '=', 'one_page_department',
       ),
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
          'options' => array(
            'phila_module_row_1_col_1_blog_posts' => 'Blog Posts',
            'phila_module_row_1_col_1_custom_text' => 'Custom Text',
            ),
          ),
          array(
            'id' => 'module_row_1_col_1_options',
            'type' => 'group',
            // List of sub-fields
            'fields' => array(
              array(
               'name' => 'Blog Post Style',
               'id'   => 'phila_module_row_1_col_1_post_style',
               'desc'  => 'Recent posts are displayed as "Cards" by default.',
               'type' => 'select',
               'placeholder' => 'Choose display style...',
               'required'  => true,
               'options' => array(
                 'phila_module_row_1_col_1_post_style_cards' => 'Card',
                 'phila_module_row_1_col_1_post_style_list' => 'List',
               ),
               'hidden' => array('phila_module_row_1_col_1_type', '!=', 'phila_module_row_1_col_1_blog_posts'),

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
              'options' => array(
                'media_buttons' => false,
                'teeny' => true,
                'dfw' => false,
                'quicktags' => false,
                'tinymce' => $department_col_1_custom_content,
              ),
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
      'hidden' => array(
        'phila_template_select', '=', 'one_page_department',
      ),
      'fields' => array(
         array(
          'name' => 'Column 2 <br/><small>(1/3 column)</small>',
          'id'   => 'phila_module_row_1_col_2_type',
          'desc'  => 'Choose to display recent blog posts, custom markup, call to action panel, or a connect panel.',
          'type' => 'select',
          'placeholder' => 'Select...',
          'options' => array(
            'phila_module_row_1_col_2_blog_posts' => 'Blog Posts',
            'phila_module_row_1_col_2_custom_text' => 'Custom Text',
            'phila_module_row_1_col_2_call_to_action_panel' => 'Call to Action Panel',
            'phila_module_row_1_col_2_connect_panel' => 'Connect Panel',
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
          'type'  => 'checkbox',
          'class' => 'action-panel-fa',
        ),
        array(
          'name'  => 'Link to Content',
          'id'    => 'phila_action_panel_link',
          'type'  => 'url',
          'class' => 'action-panel-link',
        ),
        array(
          'name'  => 'External Link',
          'id'    => 'phila_action_panel_link_loc',
          'type'  => 'checkbox',
          'class' => 'action-panel-link-loc',
          'desc'  => 'This link directs users away from alpha.phila.gov',
          ),
        ),
      ),
      array(
        'id' => 'module_row_1_col_2_connect_panel',
        'type' => 'group',
        'hidden' => array('phila_module_row_1_col_2_type', '!=', 'phila_module_row_1_col_2_connect_panel'),

        'fields' => array(
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
              // List of sub-fields
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
              ),
            ),
          ),
        ),
    )
  );

// Second row of modules - press release and/or calendar
$meta_boxes[] = array(
  'id'       => 'phila_module_row_2',
  'title'    => 'Row 2',
  'pages'    => array( 'department_page' ),
  'context'  => 'normal',
  'priority' => 'low',

  'include' => array(
    'user_role'  => array( 'administrator', 'primary_department_homepage_editor' ),
  ),
  'hidden' => array(
    'when'  => array(
      array('phila_template_select', '=', 'one_page_department' ),
      array('phila_template_select', '=', 'off_site_department' ),
    ),
    'relation' => 'or',
  ),
      // List of sub-fields
      'fields' => array(
        array(
          'name' => 'Column Selection',
          'id'   => 'phila_module_row_2_column_selection',
          'type'  => 'select',
          'desc' => 'Use this area to choose a single full-width column or two equal width columns.',
          'placeholder' => 'Choose single column or two columns',
          'options' => array(
            'phila_module_row_2_full_column' => 'One Column (Full-Width Calendar)',
            'phila_module_row_2_2_column' => 'Two Columns (Calendar and Press Releases)',
          ),
        ),
        array(
          'type' => 'divider',
          'visible' => array(
            'when' => array(
              array( 'module_row_2_column_selection', '=', 'phila_module_row_2_2_column' ),

              array( 'module_row_2_column_selection', '=', 'module_row_2_one_column'),
            ),
          'relation' => 'or',
          ),
        ),
        array(
          'name' => 'Full Width Calendar',
          'id' => 'phila_module_row_two_full_cal_col',
          'visible' => array('module_row_2_column_selection', '=', 'phila_module_row_2_full_column'),
          'type' => 'group',
          // List of sub-fields
          'fields' => array(
            array(
              'name' => 'Calender ID',
              'id'   => 'phila_module_row_2_full_col_cal_id',
              'desc'  => 'ID of the calendar',
              'type' => 'number'
            ),
            array(
              'name' => 'Calendar URL',
              'id'   => 'phila_module_row_2_full_col_cal_url',
              'desc'  => 'URL of the full calendar',
              'type' => 'url'
            ),
          ),
        ),
        array(
          'id' => 'module_row_2_col_1',
          'type' => 'group',
          // List of sub-fields
          'fields' => array(
            array(
              'name' => 'Column 1',
              'id'   => 'phila_module_row_2_col_1_type',
              'desc'  => 'Choose to display calendar events or press releases.',
              'type' => 'select',
              'visible' => array('module_row_2_column_selection', '=', 'phila_module_row_2_2_column'),
              'placeholder' => 'Select...',
              'options' => array(
                'phila_module_row_2_col_1_calendar' => 'Calendar',
                'phila_module_row_2_col_1_press_release' => 'Press Releases',
              ),
            ),
            array(
              'id' => 'module_row_2_col_1_options',
              'type' => 'group',
              // List of sub-fields
              'fields' => array(
                array(
                  'name' => 'Calendar ID',
                  'id'   => 'phila_module_row_2_col_1_cal_id',
                  'desc'  => 'ID of the calendar',
                  'type' => 'number',
                  'hidden' => array('phila_module_row_2_col_1_type', '!=', 'phila_module_row_2_col_1_calendar'),
                ),
                array(
                  'name' => 'Calendar URL',
                  'id'   => 'phila_module_row_2_col_1_cal_url',
                  'desc'  => 'URL of the full calendar',
                  'type' => 'url',
                  'hidden' => array('phila_module_row_2_col_1_type', '!=', 'phila_module_row_2_col_1_calendar'),
                ),
              ),
            ),
          ),
        ),
        array(
          'type' => 'divider',
          'visible' => array('module_row_2_column_selection', '=', 'phila_module_row_2_2_column'),
        ),
        array(
          'id' => 'module_row_2_col_2',
          'type' => 'group',
          // List of sub-fields
          'fields' => array(
            array(
              'name' => 'Column 2',
              'id'   => 'phila_module_row_2_col_2_type',
              'desc'  => 'Choose to display calendar events or press releases.',
              'type' => 'select',
              'placeholder' => 'Select...',
              'visible' => array('module_row_2_column_selection', '=', 'phila_module_row_2_2_column'),
              'options' => array(
                'phila_module_row_2_col_2_calendar' => 'Calendar',
                'phila_module_row_2_col_2_press_release' => 'Press Releases',
              ),
            ),
            array(
              'id' => 'module_row_2_col_2_options',
              'type' => 'group',
              // List of sub-fields
              'fields' => array(
                array(
                  'name' => 'Calender ID',
                  'id'   => 'phila_module_row_2_col_2_cal_id',
                  'desc'  => 'ID of the calendar',
                  'type' => 'text',
                  'hidden' => array('phila_module_row_2_col_2_type', '!=', 'phila_module_row_2_col_2_calendar'),
                ),
                array(
                  'name' => 'Calender URL',
                  'id'   => 'phila_module_row_2_col_2_cal_url',
                  'desc'  => 'URL of the full calendar',
                  'type' => 'url',
                  'hidden' => array('phila_module_row_2_col_2_type', '!=', 'phila_module_row_2_col_2_calendar'),
                ),
              ),
            ),
          ),
        ),
      ),
    );

  $meta_boxes[] = array(
    'id'       => 'phila_staff_directory_listing',
    'title'    => 'Staff Directory Listing',
    'pages'    => array( 'department_page' ),
    'context'  => 'normal',
    'priority' => 'low',

    'include' => array(
      'user_role'  => array( 'administrator', 'primary_department_homepage_editor' ),
    ),
    'visible' => array(
      'phila_template_select', '=', 'one_page_department',
    ),

    'fields' => array(
      array(
        'name' => '',
        'id'   => 'phila_staff_directory_selected',
        'desc'  => 'Include a staff directory section?',
        'type' => 'checkbox',
        'after' => '<p class="description">Enter at least one staff member in the <a href="/wp-admin/edit.php?post_type=staff_directory">Staff Members</a> section.</p>',
      ),
    ),
  );

  $meta_boxes[] = array(
    'id'       => 'phila_full_row_blog',
    'title'    => 'Full row blog posts (3 total)',
    'pages'    => array( 'department_page' ),
    'context'  => 'normal',
    'priority' => 'low',

    'include' => array(
      'user_role'  => array( 'administrator', 'primary_department_homepage_editor' ),
    ),
    'visible' => array(
      'phila_template_select', '=', 'one_page_department',
    ),

    'fields' => array(
      array(
        'name' => '',
        'id'   => 'phila_full_row_blog_selected',
        'desc'  => 'Should this page show a full row of blog posts?',
        'type' => 'checkbox',
        'after' => '<p class="description">Enter at least three blog posts in the <a href="/wp-admin/edit.php?post_type=phila_post">Blog Post</a> section.</p>'
      ),
    ),
  );

  $meta_boxes[] = array(
    'id'  => 'phila_call_to_action_multi',
    'title' => 'Call to action links',
    'pages' => array( 'department_page' ),
    'context' => 'normal',
    'priority'  => 'low',

    'include' => array(
      'user_role'  => array( 'administrator', 'primary_department_homepage_editor' ),
    ),
    'visible' => array(
      'phila_template_select', '=', 'one_page_department',
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
          'max_clone' => 3,
          'sort_clone' => true,

          'fields' => array(
            array(
              'name'  => 'Call to Action Text',
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
              'hidden' => array(
                'phila_template_select', '=', 'one_page_department',
              ),
            ),
            array(
              'name'  => 'Icon Background Circle',
              'id'    => 'phila_action_panel_fa_circle_multi',
              'type'  => 'checkbox',
              'class' => 'action-panel-fa',
              'hidden' => array(
                'phila_template_select', '=', 'one_page_department',
              ),
            ),
            array(
              'name'  => 'Link to Content',
              'id'    => 'phila_action_panel_link_multi',
              'type'  => 'url',
              'class' => 'action-panel-link',
            ),
            array(
              'name'  => 'External Link',
              'id'    => 'phila_action_panel_link_loc_multi',
              'type'  => 'checkbox',
              'class' => 'action-panel-link-loc',
              'desc'  => 'This link directs users away from alpha.phila.gov',
            ),
          ),
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

// Blogs
$meta_var_blogs = array(
  array(
    'name' => 'Category ID (optional)',
    'id' => 'phila_category',
    'type' => 'number',
  ),
);

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

// Feature Programs and Initiatives
$meta_var_feature_programs_initiatives = array(
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

// Custom Featured Content
$meta_var_custom_feature = array(
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

// Multiple Call to Action (CTA) Panels
$meta_var_call_to_action_multi = array(
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
        'fields' => array(
          array(
            'name'  => 'Call to Action Text',
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
            'hidden' => array(
              'phila_template_select', '=', 'one_page_department',
            ),
          ),
          array(
            'name'  => 'Icon Background Circle',
            'id'    => 'phila_action_panel_fa_circle_multi',
            'type'  => 'checkbox',
            'class' => 'action-panel-fa',
            'hidden' => array(
              'phila_template_select', '=', 'one_page_department',
            ),
          ),
          array(
            'name'  => 'Link to Content',
            'id'    => 'phila_action_panel_link_multi',
            'type'  => 'url',
            'class' => 'action-panel-link',
          ),
          array(
            'name'  => 'External Link',
            'id'    => 'phila_action_panel_link_loc_multi',
            'type'  => 'checkbox',
            'class' => 'action-panel-link-loc',
            'desc'  => 'This link directs users away from alpha.phila.gov',
          ),
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


// Callout
$meta_var_callout = array(
   array(
     'name' => 'Status',
     'id'   => 'phila_callout_type',
     'type' => 'select',
     'options' => array(
       'default' => 'Default',
       'important' => 'Important'
     ),
   ),
   array(
     'name' => ' Text',
     'id'   => 'phila_callout_text',
     'type' => 'textarea',
   ),
 );

// Connect Panel
$meta_var_connect = array(
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
    // List of sub-fields
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
    ),
  ),
  array(
    'id' => 'phila_connect_include_cta',
    'desc' => 'Include optional call to action button?',
    'type' => 'checkbox',
  ),
  array(
    'name' => 'Call to Action Button',
    'id' => 'phila_connect_cta',
    'type' => 'group',
    'hidden' => array( 'phila_connect_include_cta', '!=', true ),

    // List of sub-fields
    'fields' => array(
      array(
        'name' => 'Title',
        'id' => 'phila_connect_cta_title',
        'type' => 'text',
      ),
      array(
        'name' => 'URL',
        'id' => 'phila_connect_cta_url',
        'type' => 'url',
      ),
      array(
        'name' => 'Summary',
        'id' => 'phila_connect_cta_summary',
        'type' => 'textarea',
      ),
    ),
  ),
);


// Pullquote
$meta_var_pullquote = array(
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

// List of links with FontAwesome icons
$meta_var_list_items = array(
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


//Clonable WYSIWYG with title
$meta_var_wysiwyg_multi = array(
  'id'  =>  'phila_cloneable_wysiwyg',
  'type'  => 'group',
  'clone' => true,
  'sort_clone'  => true,

  'fields'  => array(
    array(
      'placeholder'  => 'Section Heading',
      'id'  => 'phila_wysiwyg_heading',
      'type'  => 'text',
      'class' => 'width-95'
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


/**
*
* Begin Full Width Column MetaBox content
*
**/

$metabox_full_options_select = array(
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

$metabox_full_options = array(
 'name' => 'Full Width Options',
 'id'   => 'phila_full_options',
 'type' => 'group',
 'visible' => array(
   'phila_grid_options',
   '=',
   'phila_grid_options_full'
 ),
 'fields' => array(
   $metabox_full_options_select,
   array(
     'id' => 'phila_blog_options',
     'type' => 'group',
     'visible' => array('phila_full_options_select', '=', 'phila_blog_posts'),
     'fields' => $meta_var_blogs,
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
     'fields' => $meta_var_callout,
   ),
   array(
     'id'   => 'phila_custom_text',
     'type' => 'group',
     'visible' => array('phila_full_options_select', '=', 'phila_custom_text'),
     'fields' => Phila_Gov_Standard_Metaboxes::phila_metabox_v2_textarea(),
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
     'fields' => $meta_var_call_to_action_multi,
  ),
  array(
    'id'   => 'phila_list_items',
    'type' => 'group',
    'visible' => array('phila_full_options_select', '=', 'phila_list_items'),
    'fields' => $meta_var_list_items,
  ),
  array(
    'id'   => 'phila_feature_p_i',
    'type' => 'group',
    'visible' => array('phila_full_options_select', '=', 'phila_feature_p_i'),
    'fields' => $meta_var_feature_programs_initiatives,
  ),
 ),
);

/**
*
* Begin 2/3 x 1/3 Column MetaBox content
*
**/

// 2/3 x 1/3: Column 1 Options
$metabox_thirds_option_one = array(
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
        'phila_blog_posts' => 'Blog Posts',
        'phila_custom_text' => 'Custom Text',
        'phila_custom_text_multi' => 'Custom Text (multi)',
        ),
    ),
    array(
      'id' => 'phila_blog_options',
      'type' => 'group',
      'visible' => array('phila_two_thirds_col_option', '=', 'phila_blog_posts'),
      'fields' => $meta_var_blogs,
    ),
    array(
      'id'   => 'phila_custom_text',
      'type' => 'group',
      'visible' => array('phila_two_thirds_col_option', '=', 'phila_custom_text'),
      'fields' => Phila_Gov_Standard_Metaboxes::phila_metabox_v2_textarea(),
    ),
    array(
      'id'   => 'phila_custom_text_multi',
      'type' => 'group',
      'visible' => array('phila_two_thirds_col_option', '=', 'phila_custom_text_multi'),
      'fields' => Phila_Gov_Standard_Metaboxes::phila_metabox_v2_textarea_multi(),
    ),
  ),
);

// 2/3 x 1/3: Column 2 Options
$metabox_thirds_option_two = array(
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
      'fields' => $meta_var_connect,
    ),
    array(
      'id'   => 'phila_custom_text',
      'type' => 'group',
      'visible' => array('phila_one_third_col_option', '=', 'phila_custom_text'),
      'fields' => Phila_Gov_Standard_Metaboxes::phila_metabox_v2_textarea(),
    ),
    array(
      'id'   => 'phila_custom_feature',
      'type' => 'group',
      'visible' => array('phila_one_third_col_option', '=', 'phila_custom_feature'),
      'fields' => $meta_var_custom_feature,
    ),
  ),
);

// 2/3 x 1/3 Options
$metabox_thirds_options = array(
  'name' => '2/3 x 1/3 Options',
  'id'   => 'phila_two_thirds_options',
  'type' => 'group',
  'hidden' => array(
    'phila_grid_options',
    '!=',
    'phila_grid_options_thirds'
),
  'fields' => array(
    $metabox_thirds_option_one,
    $metabox_thirds_option_two,
  ),
);

/**
*
* Begin 1/2 x 1/2 Column MetaBox content
*
**/

// 1/2 x 1/2: Column 1 Options
$metabox_half_option_one = array(
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
      'fields' => Phila_Gov_Standard_Metaboxes::phila_metabox_v2_textarea(),
    ),
    array(
      'id'   => 'phila_pullquote',
      'type' => 'group',
      'visible' => array('phila_half_col_1_option', '=', 'phila_pullquote'),
      'fields' => $meta_var_pullquote,
    ),
  ),
);

// 1/2 x 1/2: Column 1 Options
$metabox_half_option_two = array(
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
       'fields' => Phila_Gov_Standard_Metaboxes::phila_metabox_v2_textarea(),
     ),
     array(
       'id'   => 'phila_pullquote',
       'type' => 'group',
       'visible' => array('phila_half_col_2_option', '=', 'phila_pullquote'),
       'fields' => $meta_var_pullquote,
     ),
   ),
 );

$metabox_half_options = array(
  'name' => '1/2 x 1/2 Options',
  'id'   => 'phila_half_options',
  'type' => 'group',
  'hidden' => array(
    'phila_grid_options',
    '!=',
    'phila_grid_options_half'
  ),
  'fields' => array(
    $metabox_half_option_one,
    $metabox_half_option_two,
  ),
);

// Grid Options
$metabox_grid_options = array(
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

 $metabox_grid_row = array(
   'id'    => 'phila_row',
   'class'    => 'phila_row',
   'type'  => 'group',
   'clone' => true,
   'sort_clone' => true,
   'fields' => array( $metabox_grid_options , $metabox_full_options, $metabox_thirds_options, $metabox_half_options ),
 );

//Department Homepage metaboxes
$meta_boxes[] = array(
  'id'       => 'phila_department_homepage',
  'title'    => 'Page Content',
  'pages' => array( 'department_page' ),
  'priority' => 'high',

  'hidden'  => array('phila_template_select', 'ends with', 'v2'),

  'fields' => array(
    $meta_var_programs_initiatives_images,
    $metabox_grid_row,
  )
);

$meta_boxes[] = array(
  'title' => 'Service Stub',
  'pages' => array('page', 'service_page'),
  'priority' => 'high',
  'visible' => array('phila_template_select', 'service_stub'),

  'fields'  => array(
    array(
      'name' => 'Page source',
      'type'  => 'heading'
    ),
    array(
      'id' => 'phila_stub_source',
      'type' => 'post',
      'post_type' => 'service_page',
      'desc'  => 'Display content from the selected page on the front-end.'
    )
  )
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
      'type' => 'checkbox',
      'desc'  => 'Should this page appear in the service directory? The children of this item will appear in the service directory with this page title appended to them.'
    ),
    array(
      'name' => 'Icon selection',
      'type'  => 'heading'
    ),
    array(
      'id' => 'phila_page_icon',
      'type' => 'text',
      'desc'  => 'Choose a <a href="http://fontawesome.io/icons/">Font Awesome</a> icon to represent a top-level page. E.g.: fa-bell'
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
  'title' => 'Heading Groups',
  'pages' => array('department_page', 'page', 'service_page'),

  'visible' => array(
    'when' => array(
      array( 'phila_template_select', '=', 'one_quarter_headings_v2' ),

      array( 'phila_template_select', '=', 'default'),
    ),
    'relation' => 'or',
  ),

  'fields' => array(
    array(
      'id' => 'phila_heading_groups',
      'type'  => 'group',
      'clone' => false,

      'fields' => array(
        Phila_Gov_Standard_Metaboxes::phila_metabox_v2_address_fields_unique()
      )
    )
  )
);

$meta_boxes[] = array(
  'title' => 'Additional Content',
  'pages' => array('page', 'service_page'),
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
  )
);

return $meta_boxes;

}

add_filter( 'rwmb_group_add_clone_button_text', 'phila_group_add_clone_button_text', 10, 2 );

function phila_group_add_clone_button_text( $text, $field ) {
  if ( 'phila_resource_list' == $field['id'] ) {
    $text = '+ Add a Resource List';
  }
  if ( 'phila_ordered_content' == $field['id'] ) {
    $text = '+ Add a Step';
  }
  if ( 'phila_cloneable_wysiwyg' == $field['id'] ){
    $text = '+ Add a Section';
  }
  if ( 'phila_contact_us' == $field['id'] ){
    $text = '+ Add a Row';
  }
  if ( 'phila_contact_group' == $field['id'] ){
    $text = '+ Add a Column';
  }
  return $text;
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

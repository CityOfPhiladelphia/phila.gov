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

add_filter( 'rwmb_meta_boxes', 'phila_register_meta_boxes' );

function phila_register_meta_boxes( $meta_boxes ){

  $prefix = 'phila_';

  $department_col_1_custom_content['toolbar1'] = 'bold, italic, bullist, numlist, link, unlink, outdent, indent, removeformat, pastetext';

  $basic_editor['toolbar1'] = 'bold, italic, bullist, numlist, link, unlink, outdent, indent, removeformat, pastetext';

  $wysiwyg_options_basic = array(
    'media_buttons' => false,
    'teeny' => true,
    'dfw' => false,
    'quicktags' => false,
    'tinymce' => $basic_editor,
    'editor_height' => 200,
  );

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
        'id'    => $prefix . 'show_on_home',
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
         'id'   => $prefix . 'document_description',
         'type' => 'wysiwyg',
         'options' => $wysiwyg_options_basic,
         'desc' => 'Information describing the collection of documents on this page. This content will appear above the document list.'
       ),
       array(
         'type'  => 'heading',
         'name' => ' Release Date',
       ),
       array(
        'desc'  => 'Set the release date for all items on this document page. You can change an individual release date by editing the document below.',
        'id'    => $prefix . 'document_released',
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
        'id'    => $prefix . 'files',
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
        'id'    => $prefix . 'press_release_date',
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
            'id'   => $prefix . 'press_release_contact_name',
            'type' => 'text',
            'required'  => true,
           ),
          array(
            'name' => 'Contact phone',
            'id'   => $prefix . 'press_release_contact_phone',
            'type' => 'text',
            'placeholder' => '(215) 686-2181'
          ),
          array(
            'name' => 'Contact email',
            'id'   => $prefix . 'press_release_contact_email',
            'type' => 'text',
            'std' => 'press@phila.gov',
            'required'  => true,
          ),
         ),
       )
     ),
  );

  $meta_boxes[] = array(
    'id'       => $prefix . 'resource_list',
    'title'    => __( 'Resource List' ),
    'pages'    => array( 'department_page', 'page' ),
    'context'  => 'normal',
    'priority' => 'high',
    'visible' => array('phila_template_select', 'resource_list'),


    'fields' => array(
      array(
        'id'  => $prefix . 'resource_list',
        'type' => 'group',
        'clone'  => true,
        'sort_clone' => true,

        'fields' => array(
          array(
            'name' => __('List Title', 'rwmb'),
            'id'   => $prefix . 'resource_list_title',
            'type' => 'text',
            'required' => true,
          ),
          array(
            'id'   => $prefix . 'resource_list_items',
            'type' => 'group',
            'clone'  => true,
            'sort_clone' => true,

            'fields' => array(
                array(
                  'name' => __('Item Title', 'rwmb'),
                  'id'   => $prefix . 'list_item_title',
                  'type' => 'text',
                  'required' => true,
                ),
                array(
                  'name' => __('Item URL', 'rwmb'),
                  'id'   => $prefix . 'list_item_url',
                  'type' => 'url',
                  'required' => true,
                ),
                array(
                   'name' => __('Item Icon', 'rwmb'),
                   'id'   => $prefix . 'list_item_type',
                   'type' => 'select',
                   'placeholder' => 'Choose icon...',
                   'options' => array(
                     $prefix . 'resource_link' => 'Link',
                     $prefix . 'resource_document' => 'Document',
                     $prefix . 'resource_map' => 'Map',
                   ),
                  ),
                  array(
                     'name' => __('Featured Resource', 'rwmb'),
                     'id'   => $prefix . 'featured_resource',
                     'class'   => $prefix . 'featured-resource',
                     'type' => 'checkbox',
                  ),
                  array(
                    'name' => __('Alternate Featured Title', 'rwmb'),
                    'id'   => $prefix . 'list_item_alt_title',
                    'type' => 'text',
                  ),
                  array(
                     'name' => __('Featured Resource Summary', 'rwmb'),
                     'id'   => $prefix . 'featured_summary',
                     'class'   => $prefix . 'featured-summary',
                     'type' => 'textarea',
                     //TODO: Conditional logic doesn't appear to work on cloned fields.
                     //'hidden' => array( 'phila_featured_resource', '!=', true ),
                  ),
                  array(
                    'name'  => 'Display Order',
                    'id'    => $prefix . 'display_order',
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
    'id'       => $prefix . 'custom_markup',
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
       'id'   => $prefix . 'custom_markup_description',
       'type' => 'custom_html',
       'std'  => '<span>Use this area to insert CSS, HTML or JS.</span>',
     ),
     array(
        'name' => 'Append to Head',
        'id'   => $prefix . 'append_to_head',
        'type' => 'textarea'
      ),
      array(
       'name' => 'Append Before WYSIWYG',
       'id'   => $prefix . 'append_before_wysiwyg',
       'type' => 'textarea'
     ),
     array(
       'name' => 'Append After WYSIWYG',
       'id'   => $prefix . 'append_after_wysiwyg',
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
       'id'    => $prefix . 'hero_header_title',
       'type'  => 'text',
       'class' => 'hero-header-title',
       'desc'  => 'Title that will be placed over the header image.',
       'size'  => '60'
     ),
     array(
       'name'  => 'Hero Header Title (line 1)',
       'id'    => $prefix . 'hero_header_title_l1',
       'type'  => 'text',
       'class' => 'hero-header-title-l1',
       'desc'  => 'Portion of the title that will be displayed in standard case. Maximum of 20 characters',
       'size'  => '60'
     ),
     array(
       'name'  => 'Hero Header Title (line 2)',
       'id'    => $prefix . 'hero_header_title_l2',
       'type'  => 'text',
       'class' => 'hero-header-title-l2',
       'desc'  => 'Portion of the title that will be displayed in a larger font and forced uppercase. Maximum of 15 characters.',
       'size'  => '60'
     ),
     array(
       'name'  => 'Hero Header Body Copy',
       'id'    => $prefix . 'hero_header_body_copy',
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
       'id'    => $prefix . 'hero_header_call_to_action_button_url',
       'type'  => 'URL',
       'class' => 'hero-header-call-to-action-button-url',
       'hidden' => array(
         'phila_template_select', '=', 'one_page_department',
       ),
     ),
     array(
       'name'  => 'Call to Action Button Text',
       'id'    => $prefix . 'hero_header_call_to_action_button_text',
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
       'id'    => $prefix . 'hero_header_image',
       'type'  => 'file_input',
       'class' => 'hero-header-image',
       'desc'  => 'Image should be no smaller than 975px by 430px . '
     ),
     array(
       'name'  => 'Image Alt Text',
       'id'    => $prefix . 'hero_header_image_alt_text',
       'type'  => 'text',
       'class' => 'hero-header-image-alt-text',
       'desc'  => 'A short description used to inform visitors about the contents of an image.',
       'size'  => '60'
     ),
     array(
       'name' => 'Image Credit',
       'id'   => $prefix . 'hero_header_image_credit',
       'type' => 'text',
       'class' => 'hero-header-image-credit',
       'desc'  => 'Provide attribution information when necessary.',
       'size'  => '60'
     ),
   )
 );

 // First row of modules - recent posts, custom markup, call to action panel
 $meta_boxes[] = array(
   'id'       => $prefix . 'module_row_1',
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
      'id'   => $prefix . 'module_row_1_description',
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
          'id'   => $prefix . 'module_row_1_col_1_type',
          'desc'  => 'Choose to display recent blog posts or custom markup text.',
          'type' => 'select',
          'placeholder' => 'Select...',
          'options' => array(
            $prefix . 'module_row_1_col_1_blog_posts' => 'Blog Posts',
            $prefix . 'module_row_1_col_1_custom_text' => 'Custom Text',
            ),
          ),
          array(
            'id' => 'module_row_1_col_1_options',
            'type' => 'group',
            // List of sub-fields
            'fields' => array(
              array(
               'name' => 'Blog Post Style',
               'id'   => $prefix . 'module_row_1_col_1_post_style',
               'desc'  => 'Recent posts are displayed as "Cards" by default.',
               'type' => 'select',
               'placeholder' => 'Choose display style...',
               'required'  => true,
               'options' => array(
                 $prefix . 'module_row_1_col_1_post_style_cards' => 'Card',
                 $prefix . 'module_row_1_col_1_post_style_list' => 'List',
               ),
               'hidden' => array('phila_module_row_1_col_1_type', '!=', 'phila_module_row_1_col_1_blog_posts'),

             ),
             array(
              'name' => 'Custom Text Title',
              'id'   => $prefix . 'module_row_1_col_1_texttitle',
              'type' => 'text',
              'hidden' => array('phila_module_row_1_col_1_type', '!=', 'phila_module_row_1_col_1_custom_text'),

             ),
             array(
              'name' => 'Custom Text Content',
              'id'   => $prefix . 'module_row_1_col_1_textarea',
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
          'id'   => $prefix . 'module_row_1_col_2_type',
          'desc'  => 'Choose to display recent blog posts, custom markup, call to action panel, or a connect panel.',
          'type' => 'select',
          'placeholder' => 'Select...',
          'options' => array(
            $prefix . 'module_row_1_col_2_blog_posts' => 'Blog Posts',
            $prefix . 'module_row_1_col_2_custom_text' => 'Custom Text',
            $prefix . 'module_row_1_col_2_call_to_action_panel' => 'Call to Action Panel',
            $prefix . 'module_row_1_col_2_connect_panel' => 'Connect Panel',
          ),
        ),
        array(
          'id' => 'module_row_1_col_2_options',
          'type' => 'group',
          'fields' => array(
             array(
              'name' => 'Custom Text Title',
              'id'   => $prefix . 'module_row_1_col_2_texttitle',
              'type' => 'text',
              //TODO Move these hidden fields up to the parent. This will change the metabox IDs and effect live content, so let's hold off for now.
              'hidden' => array('phila_module_row_1_col_2_type', '!=', 'phila_module_row_1_col_2_custom_text'),
             ),
             array(
              'name' => 'Custom Text Content',
              'id'   => $prefix . 'module_row_1_col_2_textarea',
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
            'id'    => $prefix . 'action_section_title',
            'type'  => 'text',
          ),
        array(
          'name'  => 'Call to Action Text',
          'id'    => $prefix . 'action_panel_cta_text',
          'type'  => 'text',
          'class' => 'action-panel-cta-text',
        ),
        array(
          'name'  => 'Summary',
          'id'    => $prefix . 'action_panel_summary',
          'type'  => 'textarea',
          'class' => 'action-panel-details',
        ),
        array(
          'name'  => 'Icon',
          'id'    => $prefix . 'action_panel_fa',
          'type'  => 'text',
          'class' => 'action-panel-fa',
        ),
        array(
          'name'  => 'Icon Background Circle',
          'id'    => $prefix . 'action_panel_fa_circle',
          'type'  => 'checkbox',
          'class' => 'action-panel-fa',
        ),
        array(
          'name'  => 'Link to Content',
          'id'    => $prefix . 'action_panel_link',
          'type'  => 'url',
          'class' => 'action-panel-link',
        ),
        array(
          'name'  => 'External Link',
          'id'    => $prefix . 'action_panel_link_loc',
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
             'id'   => $prefix . 'connect_description',
             'type' => 'custom_html',
             'std'  => '<span>Use any of the optional fields below to add social media, address, and contact information.</span><br/>
             <span><em>Note: If all fields are left empty the <strong>Connect</strong> module will still appear on the page, however it will be empty.</em></span>',
            ),
            array(
              'id' => $prefix . 'connect_social',
              'type' => 'group',
              // List of sub-fields
              'fields' => array(
                array(
                  'type' => 'heading',
                  'name' => 'Social',
                ),
                array(
                 'name' => 'Facebook URL',
                 'id'   => $prefix . 'connect_social_facebook',
                 'type' => 'url',
                 'desc' => 'Example: https://www.facebook.com/PhiladelphiaCityGovernment/',
                ),
                array(
                 'name' => 'Twitter URL',
                 'id'   => $prefix . 'connect_social_twitter',
                 'type' => 'url',
                 'desc' => 'Example: https://twitter.com/PhiladelphiaGov'
                ),
                array(
                 'name' => 'Instagram URL',
                 'id'   => $prefix . 'connect_social_instagram',
                 'type' => 'url',
                 'desc' => 'Example: https://www.instagram.com/cityofphiladelphia/'
                ),
              ),
            ),
            array(
              'id' => $prefix . 'connect_address',
              'type' => 'group',
              // List of sub-fields
              'fields' => array(
                array(
                  'type' => 'heading',
                  'name' => 'Address',
                ),
                array(
                 'name' => 'Street Address 1',
                 'id'   => $prefix . 'connect_address_st_1',
                 'type' => 'text',
                ),
                array(
                 'name' => 'Street Address 2',
                 'id'   => $prefix . 'connect_address_st_2',
                 'type' => 'text',
                ),
                array(
                 'name' => 'City',
                 'id'   => $prefix . 'connect_address_city',
                 'type' => 'text',
                 'std' => 'Philadelphia',
                ),
                array(
                 'name' => 'State',
                 'id'   => $prefix . 'connect_address_state',
                 'type' => 'text',
                 'std' => 'PA',
                ),
                array(
                 'name' => 'Zip',
                 'id'   => $prefix . 'connect_address_zip',
                 'type' => 'text',
                 'std' => '19107',
                ),
              ),
            ),
            array(
              'id' => $prefix . 'connect_general',
              'type' => 'group',
              // List of sub-fields
              'fields' => array(
                array(
                  'type' => 'heading',
                  'name' => 'Contact',
                ),
                array(
                   'name' => 'Phone',
                   'id'   => $prefix . 'connect_phone',
                   'type' => 'phone',
                   'desc' => '(###)-###-####',
                 ),
                array(
                  'name' => 'Fax',
                  'id'   => $prefix . 'connect_fax',
                  'type' => 'phone',
                  'desc' => '(###)-###-####',
                ),
                array(
                  'name' => 'Email',
                  'id'   => $prefix . 'connect_email',
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
  'id'       => $prefix . 'module_row_2',
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
          'id'   => $prefix . 'module_row_2_column_selection',
          'type'  => 'select',
          'desc' => 'Use this area to choose a single full-width column or two equal width columns.',
          'placeholder' => 'Choose single column or two columns',
          'options' => array(
            $prefix . 'module_row_2_full_column' => 'One Column (Full-Width Calendar)',
            $prefix . 'module_row_2_2_column' => 'Two Columns (Calendar and Press Releases)',
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
          'id' => $prefix . 'module_row_two_full_cal_col',
          'visible' => array('module_row_2_column_selection', '=', 'phila_module_row_2_full_column'),
          'type' => 'group',
          // List of sub-fields
          'fields' => array(
            array(
              'name' => 'Calender ID',
              'id'   => $prefix . 'module_row_2_full_col_cal_id',
              'desc'  => 'ID of the calendar',
              'type' => 'number'
            ),
            array(
              'name' => 'Calendar URL',
              'id'   => $prefix . 'module_row_2_full_col_cal_url',
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
              'id'   => $prefix . 'module_row_2_col_1_type',
              'desc'  => 'Choose to display calendar events or press releases.',
              'type' => 'select',
              'visible' => array('module_row_2_column_selection', '=', 'phila_module_row_2_2_column'),
              'placeholder' => 'Select...',
              'options' => array(
                $prefix . 'module_row_2_col_1_calendar' => 'Calendar',
                $prefix . 'module_row_2_col_1_press_release' => 'Press Releases',
              ),
            ),
            array(
              'id' => 'module_row_2_col_1_options',
              'type' => 'group',
              // List of sub-fields
              'fields' => array(
                array(
                  'name' => 'Calendar ID',
                  'id'   => $prefix . 'module_row_2_col_1_cal_id',
                  'desc'  => 'ID of the calendar',
                  'type' => 'number',
                  'hidden' => array('phila_module_row_2_col_1_type', '!=', 'phila_module_row_2_col_1_calendar'),
                ),
                array(
                  'name' => 'Calendar URL',
                  'id'   => $prefix . 'module_row_2_col_1_cal_url',
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
              'id'   => $prefix . 'module_row_2_col_2_type',
              'desc'  => 'Choose to display calendar events or press releases.',
              'type' => 'select',
              'placeholder' => 'Select...',
              'visible' => array('module_row_2_column_selection', '=', 'phila_module_row_2_2_column'),
              'options' => array(
                $prefix . 'module_row_2_col_2_calendar' => 'Calendar',
                $prefix . 'module_row_2_col_2_press_release' => 'Press Releases',
              ),
            ),
            array(
              'id' => 'module_row_2_col_2_options',
              'type' => 'group',
              // List of sub-fields
              'fields' => array(
                array(
                  'name' => 'Calender ID',
                  'id'   => $prefix . 'module_row_2_col_2_cal_id',
                  'desc'  => 'ID of the calendar',
                  'type' => 'text',
                  'hidden' => array('phila_module_row_2_col_2_type', '!=', 'phila_module_row_2_col_2_calendar'),
                ),
                array(
                  'name' => 'Calender URL',
                  'id'   => $prefix . 'module_row_2_col_2_cal_url',
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
    'id'       => $prefix . 'staff_directory_listing',
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
        'id'   => $prefix . 'staff_directory_selected',
        'desc'  => 'Include a staff directory section?',
        'type' => 'checkbox',
        'after' => '<p class="description">Enter at least one staff member in the <a href="/wp-admin/edit.php?post_type=staff_directory">Staff Members</a> section.</p>',
      ),
    ),
  );

  $meta_boxes[] = array(
    'id'       => $prefix . 'full_row_blog',
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
        'id'   => $prefix . 'full_row_blog_selected',
        'desc'  => 'Should this page show a full row of blog posts?',
        'type' => 'checkbox',
        'after' => '<p class="description">Enter at least three blog posts in the <a href="/wp-admin/edit.php?post_type=phila_post">Blog Post</a> section.</p>'
      ),
    ),
  );

  $meta_boxes[] = array(
    'id'  => $prefix . 'call_to_action_multi',
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
        'id'  => $prefix . 'call_to_action_section',
        'type' => 'group',

          'fields' => array(
            array(
              'name'  => 'Section Title',
              'id'    => $prefix . 'action_section_title_multi',
              'type'  => 'text',
            ),

        array(
          'id'  => $prefix . 'call_to_action_multi_group',
          'type' => 'group',
          'clone'  => true,
          'max_clone' => 3,
          'sort_clone' => true,

          'fields' => array(
            array(
              'name'  => 'Call to Action Text',
              'id'    => $prefix . 'action_panel_cta_text_multi',
              'type'  => 'text',
              'class' => 'action-panel-cta-text',
            ),
            array(
              'name'  => 'Summary',
              'id'    => $prefix . 'action_panel_summary_multi',
              'type'  => 'textarea',
              'class' => 'action-panel-details',
            ),
            array(
              'name'  => 'Icon',
              'id'    => $prefix . 'action_panel_fa_multi',
              'type'  => 'text',
              'class' => 'action-panel-fa',
              'hidden' => array(
                'phila_template_select', '=', 'one_page_department',
              ),
            ),
            array(
              'name'  => 'Icon Background Circle',
              'id'    => $prefix . 'action_panel_fa_circle_multi',
              'type'  => 'checkbox',
              'class' => 'action-panel-fa',
              'hidden' => array(
                'phila_template_select', '=', 'one_page_department',
              ),
            ),
            array(
              'name'  => 'Link to Content',
              'id'    => $prefix . 'action_panel_link_multi',
              'type'  => 'url',
              'class' => 'action-panel-link',
            ),
            array(
              'name'  => 'External Link',
              'id'    => $prefix . 'action_panel_link_loc_multi',
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
    'id' => $prefix . 'category',
    'type' => 'number',
  ),
);

// Program and Initiatives
$meta_var_programs_initiatives_images = array(
  'id' => $prefix . 'p_i_images',
  'type' => 'group',
  'visible' => array('phila_template_select', 'programs_initiatives'),

  'fields' => array(
    array(
      'name' => 'Header Image',
      'id' => $prefix . 'p_i_header',
      'type' => 'file_input',
    ),
    array(
      'name' => 'Featured Image',
      'id' => $prefix . 'p_i_featured',
      'type' => 'file_input',
    ),
    array(
      'name' => 'Short Feature Description',
      'id' => $prefix . 'short_feat_desc',
      'type' => 'textarea',
    ),
    array(
      'name' => 'Long Feature Description',
      'id' => $prefix . 'long_feat_desc',
      'type' => 'textarea',
    ),
  ),
  'visible' => array('phila_template_select', '=', 'programs_initiatives'),
);

// Feature Programs and Initiatives
$meta_var_feature_programs_initiatives = array(
  array(
    'id' => $prefix . 'p_i',
    'type' => 'group',
    'fields' => array(
      array(
        'name' => 'Feature Program',
        'id' => $prefix . 'p_i_items',
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
    'id' => $prefix . 'feature_title',
    'type' => 'text',
  ),
  array(
    'name' => 'Feature Image',
    'id' => $prefix . 'feature_image',
    'type' => 'file_input',
  ),
  array(
    'name' => 'Feature Text',
    'id' => $prefix . 'feature_text',
    'type' => 'textarea',
  ),
  array(
    'name' => 'Feature URL Text',
    'id' => $prefix . 'feature_url_text',
    'type' => 'text',
  ),
  array(
    'name' => 'URL',
    'id' => $prefix . 'feature_url',
    'type' => 'url',
  ),
);

// Multiple Call to Action (CTA) Panels
$meta_var_call_to_action_multi = array(
  array(
    'id'  => $prefix . 'call_to_action_section',
    'type' => 'group',
    'fields' => array(
      array(
        'name'  => 'Section Title',
        'id'    => $prefix . 'action_section_title_multi',
        'type'  => 'text',
      ),
      array(
        'id'  => $prefix . 'call_to_action_multi_group',
        'type' => 'group',
        'clone'  => true,
        'max_clone' => 4,
        'sort_clone' => true,
        'fields' => array(
          array(
            'name'  => 'Call to Action Text',
            'id'    => $prefix . 'action_panel_cta_text_multi',
            'type'  => 'text',
            'class' => 'action-panel-cta-text',
          ),
          array(
            'name'  => 'Summary',
            'id'    => $prefix . 'action_panel_summary_multi',
            'type'  => 'textarea',
            'class' => 'action-panel-details',
          ),
          array(
            'name'  => 'Icon',
            'id'    => $prefix . 'action_panel_fa_multi',
            'type'  => 'text',
            'class' => 'action-panel-fa',
            'hidden' => array(
              'phila_template_select', '=', 'one_page_department',
            ),
          ),
          array(
            'name'  => 'Icon Background Circle',
            'id'    => $prefix . 'action_panel_fa_circle_multi',
            'type'  => 'checkbox',
            'class' => 'action-panel-fa',
            'hidden' => array(
              'phila_template_select', '=', 'one_page_department',
            ),
          ),
          array(
            'name'  => 'Link to Content',
            'id'    => $prefix . 'action_panel_link_multi',
            'type'  => 'url',
            'class' => 'action-panel-link',
          ),
          array(
            'name'  => 'External Link',
            'id'    => $prefix . 'action_panel_link_loc_multi',
            'type'  => 'checkbox',
            'class' => 'action-panel-link-loc',
            'desc'  => 'This link directs users away from alpha.phila.gov',
          ),
        ),
      ),
      array(
        'name'  =>  'Additional URL Title (optional)',
        'id'    => $prefix . 'url_title',
        'type'  => 'text',
        'visible' => array('phila_full_options_select', '=', 'phila_resource_list'),
      ),
      array(
        'name'  =>  'Additional URL (optional)',
        'id'    => $prefix . 'url',
        'type'  => 'url',
        'visible' => array('phila_full_options_select', '=', 'phila_resource_list'),
      ),
      array(
        'name'  =>  'Background Image',
        'id'    => $prefix . 'bg_image',
        'type'  => 'file_input',
        'visible' => array('phila_full_options_select', '=', 'phila_get_involved'),
      ),
    ),
  ),
);

// Full Width Calendar
$meta_var_calendar_full = array(
  array(
    'name' => 'Calender ID',
    'id'   => $prefix . 'full_width_calendar_id',
    'desc'  => 'ID of the calendar',
    'type' => 'number'
  ),
  array(
    'name' => 'Calendar URL',
    'id'   => $prefix . 'full_width_calendar_url',
    'desc'  => 'URL of the full calendar',
    'type' => 'url'
  ),
);

// Callout
$meta_var_callout = array(
   array(
     'name' => 'Status',
     'id'   => $prefix . 'callout_type',
     'type' => 'select',
     'options' => array(
       'default' => 'Default',
       'important' => 'Important'
     ),
   ),
   array(
     'name' => ' Text',
     'id'   => $prefix . 'callout_text',
     'type' => 'textarea',
   ),
 );

// Connect Panel
$meta_var_connect = array(
  array(
   'name' => 'Connect Panel',
   'id'   => $prefix . 'connect_description',
   'type' => 'custom_html',
   'std'  => '<span>Use any of the optional fields below to add social media, address, and contact information.</span><br/>
   <span><em>Note: If all fields are left empty the <strong>Connect</strong> module will still appear on the page, however it will be empty.</em></span>',
  ),
  array(
    'id' => $prefix . 'connect_social',
    'type' => 'group',
    // List of sub-fields
    'fields' => array(
      array(
        'type' => 'heading',
        'name' => 'Social',
      ),
      array(
       'name' => 'Facebook URL',
       'id'   => $prefix . 'connect_social_facebook',
       'type' => 'url',
       'desc' => 'Example: https://www.facebook.com/PhiladelphiaCityGovernment/',
      ),
      array(
       'name' => 'Twitter URL',
       'id'   => $prefix . 'connect_social_twitter',
       'type' => 'url',
       'desc' => 'Example: https://twitter.com/PhiladelphiaGov'
      ),
      array(
       'name' => 'Instagram URL',
       'id'   => $prefix . 'connect_social_instagram',
       'type' => 'url',
       'desc' => 'Example: https://www.instagram.com/cityofphiladelphia/'
      ),
    ),
  ),
  array(
    'id' => $prefix . 'connect_address',
    'type' => 'group',
    // List of sub-fields
    'fields' => array(
      array(
        'type' => 'heading',
        'name' => 'Address',
      ),
      array(
       'name' => 'Street Address 1',
       'id'   => $prefix . 'connect_address_st_1',
       'type' => 'text',
      ),
      array(
       'name' => 'Street Address 2',
       'id'   => $prefix . 'connect_address_st_2',
       'type' => 'text',
      ),
      array(
       'name' => 'City',
       'id'   => $prefix . 'connect_address_city',
       'type' => 'text',
       'std' => 'Philadelphia',
      ),
      array(
       'name' => 'State',
       'id'   => $prefix . 'connect_address_state',
       'type' => 'text',
       'std' => 'PA',
      ),
      array(
       'name' => 'Zip',
       'id'   => $prefix . 'connect_address_zip',
       'type' => 'text',
       'std' => '19107',
      ),
    ),
  ),
  array(
    'id' => $prefix . 'connect_general',
    'type' => 'group',
    // List of sub-fields
    'fields' => array(
      array(
        'type' => 'heading',
        'name' => 'Contact',
      ),
      array(
         'name' => 'Phone',
         'id'   => $prefix . 'connect_phone',
         'type' => 'phone',
         'desc' => '(###)-###-####',
       ),
      array(
        'name' => 'Fax',
        'id'   => $prefix . 'connect_fax',
        'type' => 'phone',
        'desc' => '(###)-###-####',
      ),
      array(
        'name' => 'Email',
        'id'   => $prefix . 'connect_email',
        'type' => 'email',
        'desc' => 'example@phila.gov',
      ),
    ),
  ),
  array(
    'id' => $prefix . 'connect_include_cta',
    'desc' => 'Include optional call to action button?',
    'type' => 'checkbox',
  ),
  array(
    'name' => 'Call to Action Button',
    'id' => $prefix . 'connect_cta',
    'type' => 'group',
    'hidden' => array( 'phila_connect_include_cta', '!=', true ),

    // List of sub-fields
    'fields' => array(
      array(
        'name' => 'Title',
        'id' => $prefix . 'connect_cta_title',
        'type' => 'text',
      ),
      array(
        'name' => 'URL',
        'id' => $prefix . 'connect_cta_url',
        'type' => 'url',
      ),
      array(
        'name' => 'Summary',
        'id' => $prefix . 'connect_cta_summary',
        'type' => 'textarea',
      ),
    ),
  ),
);

// Custom Text
$meta_var_textarea = array(
  array(
    'name' => 'Custom Text Title',
    'id'   => $prefix . 'custom_text_title',
    'type' => 'text',
  ),
  array(
    'name' => 'Custom Text Content',
    'id'   => $prefix . 'custom_text_content',
    'type' => 'textarea',
  ),
);

// Custom Text Multi
$meta_var_textarea_multi = array(
  array(
    'name' => 'Row Title',
    'id'   => $prefix . 'custom_row_title',
    'type' => 'text',
  ),
  array(
    'id'   => $prefix . 'custom_text_group',
    'type' => 'group',
    'clone' => true,
    'max_clone' => 3,
    'fields' => $meta_var_textarea,
  )
);

// Pullquote
$meta_var_pullquote = array(
  array(
    'name' => 'Quote',
    'id'   => $prefix . 'quote',
    'type' => 'textarea',
  ),
  array(
    'name' => 'Attribution',
    'id'   => $prefix . 'attribution',
    'type' => 'text',
  ),
);

// List of links with FontAwesome icons
$meta_var_list_items = array(
  array(
    'name' => 'Row Title',
    'id'   => $prefix . 'row_title',
    'type' => 'text',
  ),
  array(
    'name' => 'Summary',
    'id'   => $prefix . 'summary',
    'type' => 'textarea',
  ),
  array(
    'id'  => $prefix . 'list',
    'type' => 'group',
    'clone'  => true,
    'sort_clone' => true,

    'fields' => array(
      array(
        'std' => '<strong>Row</strong>',
        'type' => 'custom_html',
      ),
      array(
        'id'   => $prefix . 'list_items',
        'type' => 'group',
        'clone'  => true,
        'sort_clone' => true,
        'fields' => array(
          array(
            'name' => __('Item Title', 'rwmb'),
            'id'   => $prefix . 'list_item_title',
            'type' => 'text',
            'required' => true,
          ),
          array(
            'name' => __('Item URL', 'rwmb'),
            'id'   => $prefix . 'list_item_url',
            'type' => 'url',
            'required' => true,
          ),
          array(
             'name' => __('Item Icon', 'rwmb'),
             'id'   => $prefix . 'list_item_type',
             'type' => 'text',
          ),
        ),
      ),
    ),
  ),
);

//WYSIWYG with Title
$meta_var_wysiwyg = array(
  'id'  =>  $prefix . 'custom_wysiwyg',
  'type'  => 'group',
  'clone' => false,

  'fields'  => array(
    array(
      'name'  => 'Section Title',
      'id'  => $prefix . 'wysiwyg_title',
      'type'  => 'text'
    ),
    array(
      'id'  => $prefix . 'wysiwyg_content',
      'type'  => 'wysiwyg',
      'options' => $wysiwyg_options_basic
    )
  )
);

//Clonable WYSIWYG with title
$meta_var_wysiwyg_multi = array(
  'id'  =>  $prefix . 'cloneable_wysiwyg',
  'type'  => 'group',
  'clone' => true,
  'sort_clone'  => true,

  'fields'  => array(
    array(
      'placeholder'  => 'Section Heading',
      'id'  => $prefix . 'wysiwyg_heading',
      'type'  => 'text',
      'class' => 'width-95'
    ),
    array(
      'id'  => $prefix . 'wysiwyg_content',
      'type'  => 'wysiwyg',
      'options' => $wysiwyg_options_basic
    )
  )
);

//Default address fields
$meta_var_standard_address = array(
  'id'  =>  'address_group',
  'type'  => 'group',
  'fields'  => array(

    array(
      'type' => 'heading',
      'name' => 'Address',
    ),
    array(
      'name' => 'Street Address 1',
      'id'   => $prefix . 'std_address_st_1',
      'type' => 'text',
    ),
    array(
      'name' => 'Street Address 2',
      'id'   => $prefix . 'std_address_st_2',
      'type' => 'text',
    ),
    array(
      'name' => 'City',
      'id'   => $prefix . 'std_address_city',
      'type' => 'text',
      'std' => 'Philadelphia',
    ),
    array(
      'name' => 'State',
      'id'   => $prefix . 'std_address_state',
      'type' => 'text',
      'std' => 'PA',
    ),
    array(
      'name' => 'Zip',
      'id'   => $prefix . 'std_address_zip',
      'type' => 'text',
      'std' => '19107',
    ),
  ),
);

$meta_var_document_page_selector = array(
  'id'  => $prefix . 'document_page_picker',
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

//Purpose: To display content in a wysiwyg and include markup for an address
$meta_var_wysiwyg_address_content = array(
  'id'  => $prefix . 'wysiwyg_address_content',
  'type'  => 'group',
  'clone' => true,
  'sort_clone'  => true,

  'fields'  => array(
    array(
      //TODO: determine way to display step numbers in admin
      'placeholder' => 'Heading',
      'id'  => $prefix . 'wysiwyg_heading',
      'type'  => 'text',
      'class' => 'width-95'
    ),
    array(
      'id'  => $prefix . 'wysiwyg_content',
      'type'  => 'wysiwyg',
      'options' => $wysiwyg_options_basic
    ),
    array(
      'desc'  => 'Include an address?',
      'id'  => $prefix . 'address_select',
      'type'  => 'checkbox',
    ),
    array(
      'id' => $prefix . 'std_address',
      'type' => 'group',
      'visible' => array('phila_address_select', true),

      'fields' => array(
        $meta_var_standard_address,
      ),
    ),
  )
);

//Purpose: To display content in a wysiwyg and include markup for an address
//TODO: Merge these two almost-identical fields. The ID used to create the metabox will interfere with other metaboxes that are used on the same page. For now we will create a second version of the address content metabox so we can set a different ID.
$meta_var_wysiwyg_address_content_unique = array(
  'id'  => $prefix . 'wysiwyg_address_content',
  'type'  => 'group',
  'clone' => true,
  'sort_clone'  => true,

  'fields'  => array(
    array(
      //TODO: determine way to display step numbers in admin
      'placeholder' => 'Heading',
      'id'  => $prefix . 'wysiwyg_heading',
      'type'  => 'text',
      'class' => 'width-95'
    ),
    array(
      'id'  => $prefix . 'unique_wysiwyg_content',
      'type'  => 'wysiwyg',
      'options' => $wysiwyg_options_basic
    ),
    array(
      'desc'  => 'Include an address?',
      'id'  => $prefix . 'address_select',
      'type'  => 'checkbox',
    ),
    array(
      'id' => $prefix . 'std_address',
      'type' => 'group',
      'visible' => array('phila_address_select', true),

      'fields' => array(
        $meta_var_standard_address,
      ),
    ),
  )
);



$meta_var_wysiwyg_address_content = array(
  'id'  => $prefix . 'wysiwyg_address_content',
  'type'  => 'group',
  'clone' => true,
  'sort_clone'  => true,

  'fields'  => array(
    array(
      //TODO: determine way to display step numbers in admin
      'placeholder' => 'Heading',
      'id'  => $prefix . 'wysiwyg_heading',
      'type'  => 'text',
      'class' => 'width-95'
    ),
    array(
      'id'  => $prefix . 'wysiwyg_content',
      'type'  => 'wysiwyg',
      'options' => $wysiwyg_options_basic
    ),
    array(
      'desc'  => 'Include an address?',
      'id'  => $prefix . 'address_select',
      'type'  => 'checkbox',
    ),
    array(
      'id' => $prefix . 'std_address',
      'type' => 'group',
      'visible' => array('phila_address_select', true),

      'fields' => array(
        $meta_var_standard_address,
      ),
    ),
  )
);


//Purpose: To display content in a stepped order on the front-end
$meta_var_ordered_content = array(
  'id'  => $prefix . 'ordered_content',
  'type'  => 'group',
  'clone' => true,
  'sort_clone'  => true,

  'fields'  => array(
    array(
      //TODO: determine way to display step numbers in admin
      'placeholder' => 'Step Heading',
      'id'  => $prefix . 'step_wysiwyg_heading',
      'type'  => 'text',
      'class' => 'width-95'
    ),
    array(
      'id'  => $prefix . 'step_wysiwyg_content',
      'type'  => 'wysiwyg',
      'options' => $wysiwyg_options_basic
    ),
    array(
      'desc'  => 'Does this step contain an address?',
      'id'  => $prefix . 'address_step',
      'type'  => 'checkbox',
    ),
    array(
      'id' => $prefix . 'std_address',
      'type' => 'group',
      'visible' => array('phila_address_step', true),

      'fields' => array(
        $meta_var_standard_address,
      ),
    ),
  )
);

//Specific for tax detail template.
$meta_var_tax_due_date = array(
  'id'  =>  $prefix . 'tax_due_date',
  'type'  => 'group',
  'clone' => false,

  'fields' => array(
    array(
      'name'  => 'Due Date Callout',
      'type' => 'heading',
    ),

    array(
      'name'  => 'Due Date Type',
      'id'  => $prefix . 'tax_date_choice',
      'type'  => 'select',
      'options' => array(
        'monthly' => 'Tax is due monthly',
        'yearly'  => 'Tax is due yearly',
        'misc'  => 'Tax is miscellaneous'
      ),
    ),
    array(
      'visible' => array(
        'when' => array(
          array('phila_tax_date_choice', 'monthly'),
          array('phila_tax_date_choice', 'yearly'),
        ),
        'relation' => 'or',
      ),
      'name'  =>  'Tax Due Date',
      'id'  => $prefix . 'tax_date',
      'desc'  => 'Enter the day of the month this tax is due.',
      'type'  =>  'number',
      'min' => '1',
      'max' => '31',
      'required'  => true
    ),

    array(
      'visible' => array('phila_tax_date_choice', 'yearly'),
      'name'  => 'Month Due',
      'id'  => $prefix . 'tax_date_month',
      'desc'  => 'Enter the month of the year this tax is due.',
      'type'  => 'select',
      'placeholder' => 'Choose month...',
      'options' => phila_return_month_array(),
    ),
    array(
      'hidden' => array('phila_tax_date_choice', 'misc'),
      'name'  => 'Brief Explanation',
      'id'  => $prefix . 'tax_date_summary_brief',
      'type'  => 'wysiwyg',
      'desc'  => 'Example: "of each month, for the prior month\'s activity." <br>This content will appear in the date callout box . ',
      'options' => $wysiwyg_options_basic,
      'required'  => true
    ),
    array(
      'visible' => array('phila_tax_date_choice', 'misc'),
      'id'  => $prefix . 'tax_date_misc_details',
      'type'  => 'wysiwyg',
      'desc'  => 'This content will appear in the date callout box . ',
      'options' => $wysiwyg_options_basic,
      'required'  => true
    ),
    array(
      'name'  => 'Due Date Details',
      'type'  => 'heading',
    ),
    array(
      'id'  => $prefix . 'tax_date_summary_detailed',
      'type'  => 'wysiwyg',
      'desc'  => 'Provide detailed date information. This content will appear in the "Important Dates" section.',
      'options' => $wysiwyg_options_basic,
      'required'  => true
    ),
  ),
);
//Specific to the tax detail template
$meta_var_tax_costs = array(
  'id'  =>  $prefix . 'tax_costs',
  'type'  => 'group',
  'clone' => false,

  'fields' => array(
    array(
      'name'  => 'Tax Rate Callout',
      'type' => 'heading',
    ),
    array(
      'name'  =>  'Tax Cost',
      'id'  => $prefix . 'tax_cost_number',
      'type'  =>  'number',
      'step'  => 'any',
    ),
    array(
      'name'  => 'Unit',
      'id'  =>  $prefix . 'tax_cost_unit',
      'type'  => 'select',
      'options' => array(
        'percent' => '%',
        'dollar'  => '$',
        'mills' => 'mills'
      )
    ),
    array(
      'name'  => 'Brief Explanation',
      'id'  => $prefix . 'tax_cost_summary_brief',
      'type'  => 'wysiwyg',
      'desc'  => 'Example: "of the admission charge." <br> This content will appear in the tax callout box . ',
      'options' => $wysiwyg_options_basic,
      'required'  => true
    ),
    array(
      'name'  => 'Cost Details',
      'type'  => 'heading'
    ),
    array(
      'id'  => $prefix . 'tax_cost_summary_detailed',
      'type'  => 'wysiwyg',
      'desc'  => 'Provide detailed cost information. This content will appear under the "Tax Rates, Penalties & Fees" section.',
      'options' => $wysiwyg_options_basic,
      'required'  => true
    ),
  ),
);

//Specific to the tax detail template
$meta_forms_instructions = array(
  'id'  => $prefix . 'forms_instructions',
  'type'  => 'group',

  'fields'  => array(
    array(
      'name'  => 'Forms & Instructions',
      'type'  => 'heading'
    ),
    $meta_var_document_page_selector
  )
);

//Group all "Page" related content
$meta_related_content = array(
  'id'  => $prefix . 'related',
  'type'  => 'group',

  'fields'  => array(
    array(
      'name'  => 'Related Content',
      'type'  => 'heading'
    ),
    array(
      'id'  => $prefix . 'related_content',
      'type'  => 'wysiwyg',
      'options' => $wysiwyg_options_basic
    ),
  )
);
//Questions metabox, used for Pages
$meta_questions = array(
  'id'  => $prefix . 'questions',
  'type'  => 'group',

  'fields'  => array(
    array(
      'name'  => 'Questions about this content?',
      'type'  => 'heading'
    ),
    array(
      'id'  => $prefix . 'question_content',
      'type'  => 'wysiwyg',
      'options' => $wysiwyg_options_basic
    ),
  )
);

//Did you know, used for Pages
$meta_did_you_know = array(
  'id'  => $prefix . 'did_you_know',
  'type'  => 'group',

  'fields'  => array(
    array(
      'name'  => 'Did You Know?',
      'type'  => 'heading'
    ),
    array(
      'id'  => $prefix . 'did_you_know_content',
      'type'  => 'wysiwyg',
      'options' => $wysiwyg_options_basic
    ),
  )
);


/**
*
* Begin Full Width Column MetaBox content
*
**/

$metabox_full_options_select = array(
 'id'   => $prefix . 'full_options_select',
 'desc'  => 'Choose to display full width content.',
 'type' => 'select',
 'placeholder' => 'Select...',
 'options' => array(
   $prefix . 'blog_posts' => 'Blog Posts',
   $prefix . 'full_width_calendar' => 'Calendar',
   $prefix . 'callout' => 'Callout',
   $prefix . 'custom_text' => 'Custom Text',
   $prefix . 'feature_p_i' => 'Feature Program or Initiative',
   $prefix . 'get_involved' => 'Get Involved',
   $prefix . 'list_items' => 'List Items',
   $prefix . 'full_width_press_releases' => 'Press Releases',
   $prefix . 'resource_list' => 'Resource List',
   ),
 );

$metabox_full_options = array(
 'name' => 'Full Width Options',
 'id'   => $prefix . 'full_options',
 'type' => 'group',
 'visible' => array(
   'phila_grid_options',
   '=',
   'phila_grid_options_full'
 ),
 'fields' => array(
   $metabox_full_options_select,
   array(
     'id' => $prefix . 'blog_options',
     'type' => 'group',
     'visible' => array('phila_full_options_select', '=', 'phila_blog_posts'),
     'fields' => $meta_var_blogs,
   ),
   array(
     'id' => $prefix . 'full_width_calendar',
     'type' => 'group',
     'visible' => array('phila_full_options_select', '=', 'phila_full_width_calendar'),
     'fields' => $meta_var_calendar_full,
   ),
   array(
     'id'   => $prefix . 'callout',
     'type' => 'group',
     'visible' => array('phila_full_options_select', '=', 'phila_callout'),
     'fields' => $meta_var_callout,
   ),
   array(
     'id'   => $prefix . 'custom_text',
     'type' => 'group',
     'visible' => array('phila_full_options_select', '=', 'phila_custom_text'),
     'fields' => $meta_var_textarea,
   ),
   array(
     'id'  => $prefix . 'call_to_action_multi',
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
    'id'   => $prefix . 'list_items',
    'type' => 'group',
    'visible' => array('phila_full_options_select', '=', 'phila_list_items'),
    'fields' => $meta_var_list_items,
  ),
  array(
    'id'   => $prefix . 'feature_p_i',
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
 'id' => $prefix . 'two_thirds_col',
 'type' => 'group',
 'fields' => array(
    array(
      'name' => 'Column 1 <br/><small>(2/3 width column)</small>',
      'id'   => $prefix . 'two_thirds_col_option',
      'desc'  => 'Choose to display recent blog posts or custom markup text.',
      'type' => 'select',
      'placeholder' => 'Select...',
      'options' => array(
        $prefix . 'blog_posts' => 'Blog Posts',
        $prefix . 'custom_text' => 'Custom Text',
        $prefix . 'custom_text_multi' => 'Custom Text (multi)',
        ),
    ),
    array(
      'id' => $prefix . 'blog_options',
      'type' => 'group',
      'visible' => array('phila_two_thirds_col_option', '=', 'phila_blog_posts'),
      'fields' => $meta_var_blogs,
    ),
    array(
      'id'   => $prefix . 'custom_text',
      'type' => 'group',
      'visible' => array('phila_two_thirds_col_option', '=', 'phila_custom_text'),
      'fields' => $meta_var_textarea,
    ),
    array(
      'id'   => $prefix . 'custom_text_multi',
      'type' => 'group',
      'visible' => array('phila_two_thirds_col_option', '=', 'phila_custom_text_multi'),
      'fields' => $meta_var_textarea_multi,
    ),
  ),
);

// 2/3 x 1/3: Column 2 Options
$metabox_thirds_option_two = array(
 'id' => $prefix . 'one_third_col',
 'type' => 'group',
 'fields' => array(
    array(
    'name' => 'Column 2 <br/><small>(1/3 width column)</small>',
    'id'   => $prefix . 'one_third_col_option',
    'desc'  => 'Choose to display recent blog posts or custom markup text.',
    'type' => 'select',
    'placeholder' => 'Select...',
    'options' => array(
      $prefix . 'connect_panel' => 'Connect Panel',
      $prefix . 'custom_feature' => 'Custom Feature Panel',
      $prefix . 'custom_text' => 'Custom Text',
      ),
    ),
    array(
      'id' => $prefix . 'connect_panel',
      'type' => 'group',
      'hidden' => array('phila_one_third_col_option', '!=', 'phila_connect_panel'),
      'fields' => $meta_var_connect,
    ),
    array(
      'id'   => $prefix . 'custom_text',
      'type' => 'group',
      'visible' => array('phila_one_third_col_option', '=', 'phila_custom_text'),
      'fields' => $meta_var_textarea,
    ),
    array(
      'id'   => $prefix . 'custom_feature',
      'type' => 'group',
      'visible' => array('phila_one_third_col_option', '=', 'phila_custom_feature'),
      'fields' => $meta_var_custom_feature,
    ),
  ),
);

// 2/3 x 1/3 Options
$metabox_thirds_options = array(
  'name' => '2/3 x 1/3 Options',
  'id'   => $prefix . 'two_thirds_options',
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
 'id' => $prefix . 'half_col_1',
 'type' => 'group',
 'fields' => array(
    array(
      'name' => 'Column 1 <br/><small>(1/2 width column)</small>',
      'id'   => $prefix . 'half_col_1_option',
      'desc'  => 'Choose to display recent blog posts or custom markup text.',
      'type' => 'select',
      'placeholder' => 'Select...',
      'options' => array(
        $prefix . 'custom_text' => 'Custom Text',
        $prefix . 'pullquote' => 'Pullquote',
        ),
    ),
    array(
      'id'   => $prefix . 'custom_text',
      'type' => 'group',
      'visible' => array('phila_half_col_1_option', '=', 'phila_custom_text'),
      'fields' => $meta_var_textarea,
    ),
    array(
      'id'   => $prefix . 'pullquote',
      'type' => 'group',
      'visible' => array('phila_half_col_1_option', '=', 'phila_pullquote'),
      'fields' => $meta_var_pullquote,
    ),
  ),
);

// 1/2 x 1/2: Column 1 Options
$metabox_half_option_two = array(
  'id' => $prefix . 'half_col_2',
  'type' => 'group',
  'fields' => array(
     array(
       'name' => 'Column 2 <br/><small>(1/2 width column)</small>',
       'id'   => $prefix . 'half_col_2_option',
       'desc'  => 'Choose to display recent blog posts or custom markup text.',
       'type' => 'select',
       'placeholder' => 'Select...',
       'options' => array(
         $prefix . 'custom_text' => 'Custom Text',
         $prefix . 'pullquote' => 'Pullquote',
         ),
     ),
     array(
       'id'   => $prefix . 'custom_text',
       'type' => 'group',
       'visible' => array('phila_half_col_2_option', '=', 'phila_custom_text'),
       'fields' => $meta_var_textarea,
     ),
     array(
       'id'   => $prefix . 'pullquote',
       'type' => 'group',
       'visible' => array('phila_half_col_2_option', '=', 'phila_pullquote'),
       'fields' => $meta_var_pullquote,
     ),
   ),
 );

$metabox_half_options = array(
  'name' => '1/2 x 1/2 Options',
  'id'   => $prefix . 'half_options',
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
 'id'   => $prefix . 'grid_options',
 'desc'  => 'Choose the row layout.',
 'type' => 'select',
 'placeholder' => 'Select...',
 'options' => array(
   $prefix . 'grid_options_full' => 'Full Width',
   $prefix . 'grid_options_half' => '1/2 x 1/2',
   $prefix . 'grid_options_thirds' => '2/3 x 1/3',
   ),
 );

 $metabox_grid_row = array(
   'id'    => $prefix . 'row',
   'class'    => $prefix . 'row',
   'type'  => 'group',
   'clone' => true,
   'sort_clone' => true,
   'fields' => array( $metabox_grid_options , $metabox_full_options, $metabox_thirds_options, $metabox_half_options ),
 );

//Department Homepage metaboxes
$meta_boxes[] = array(
  'id'       => $prefix . 'department_homepage',
  'title'    => 'Page Content',
  'pages' => array( 'department_page' ),
  'priority' => 'high',

  'fields' => array(
    $meta_var_programs_initiatives_images,
    $metabox_grid_row,
  )
);

//Tax Detail Template
$meta_boxes[] = array(
  'title' => 'Tax Highlights',
  'pages' => array('page', 'service_page'),
  'priority' => 'high',

  'visible' => array('phila_template_select', 'tax_detail'),

  'fields'  => array(
    array(
      'id'  => $prefix . 'tax_highlights',
      'type'   => 'group',

      'fields'  => array(
        $meta_var_tax_due_date,
        array(
          'type'  => 'divider'
        ),
        $meta_var_tax_costs,
        array(
          'type'  => 'divider'
        ),
        array(
          'id'  => $prefix . 'tax_code',
          'name'  => 'Tax Type Code',
          'type'  => 'number'
        ),
      ),
    )
  )
);

$meta_boxes[] = array(
  'title' => 'Tax Details',
  'pages' => array('page', 'service_page'),
  'priority' => 'high',
  'visible' => array('phila_template_select', 'tax_detail'),

  'fields'  => array(
    array(
      'id'  => $prefix . 'tax_payment_info',
      'type'   => 'group',

      'fields'  => array(
        array(
          'name' => 'Who has to pay the tax?',
          'type'  => 'heading'
        ),
        array(
          'id'  => $prefix . 'tax_who_pays',
          'type'  => 'wysiwyg',
          'options' => $wysiwyg_options_basic
        ),
        array(
          'name'  => 'What happens if the tax is not paid on time?',
          'type'  => 'heading'
        ),
        array(
          'id'  => $prefix . 'tax_late_fees',
          'type'  => 'wysiwyg',
          'options' => $wysiwyg_options_basic
        ),
        array(
          'name' => 'Who is eligible for a discount?',
          'type'  => 'heading'
        ),
        array(
          'id'  => $prefix . 'tax_discounts',
          'type'  => 'wysiwyg',
          'options' => $wysiwyg_options_basic
          ),
        array(
          'name'  => 'Can you be excused from paying the tax?',
          'type'  => 'heading'
        ),
        array(
          'id'  => $prefix . 'tax_exemptions',
          'type'  => 'wysiwyg',
          'options' => $wysiwyg_options_basic
        )
      )
    )
  )
);

$meta_boxes[] = array(
  'title' => 'How to pay',
  'pages' => array('page', 'service_page'),
  'priority' => 'high',
  'visible' => array('phila_template_select', 'tax_detail'),

  'fields'  => array(
    array(
      'id'  => $prefix . 'payment_group',
      'type'  => 'group',

    'fields' => array(
        array(
          'name' => 'Introduction',
          'type'  => 'heading',
        ),
        $meta_var_wysiwyg_address_content,
        array(
          'name' => 'Steps in payment process',
          'type'  => 'heading',
        ),
        $meta_var_ordered_content
      ),
    )
  )
);
//default tax detail template
$meta_boxes[] = array(
  'title' => 'Heading Groups',
  'pages' => array('service_page'),
  'visible' => array('phila_template_select', 'default'),

  'fields' => array(
    $meta_var_wysiwyg_address_content_unique
  )
);


$meta_boxes[] = array(
  'title' => 'Additional Content',
  'pages' => array('page', 'service_page'),
  'fields' => array(
    array(
      'id' => $prefix . 'additional_content',
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

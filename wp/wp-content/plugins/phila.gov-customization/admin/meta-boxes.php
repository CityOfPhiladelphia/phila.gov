<?php
/**
 * Registers all the metaboxes we ever will need
 *
 * @link https://github.com/CityOfPhiladelphia/phila.gov-customization
 *
 * @package phila-gov_customization
 */

add_filter( 'rwmb_meta_boxes', 'phila_register_meta_boxes' );

function phila_register_meta_boxes( $meta_boxes ){
  $prefix = 'phila_';

  $department_col_1_custom_content['toolbar1'] = 'bold, italic, bullist, numlist, link, unlink, outdent, indent, removeformat, pastetext';

  $meta_boxes[] = array(
    'id'       => 'news',
    'title'    => 'News Information',
    'pages'    => array( 'news_post' ),
    'context'  => 'normal',
    'priority' => 'high',


    'fields' => array(
      array(
        'name'  => 'Description',
        'desc'  => 'A one or two sentence description describing this article. Required.',
        'id'    => $prefix . 'news_desc',
        'type'  => 'textarea',
        'class' => 'news-description',
        'clone' => false,
      )
    )
  );//news description

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
       'name' => 'Description',
       'id'   => $prefix . 'document_description',
       'type' => 'textarea'
     ),
     array(
      'name'  => 'Release Date',
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
    'id'       => 'post-description',
    'title'    => 'Post Summary',
    'pages'    => array( 'phila_post' ),
    'context'  => 'normal',
    'priority' => 'high',


    'fields' => array(
      array(
       'name' => 'Summary',
       'id'   => $prefix . 'post_desc',
       'type' => 'textarea'
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
    'pages'    => array( 'department_page', 'event_page' , 'page' ),
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
       'desc'  => 'Image should be no smaller than 975px by 430px.'
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


$metabox_grid_options = array(
 'name' => 'Row Layout',
 'id'   => $prefix . 'grid_options',
 'desc'  => 'Choose the row layout.',
 'type' => 'select',
 'placeholder' => 'Select...',
 'options' => array(
   $prefix . 'grid_options_full' => 'Full Width',
   $prefix . 'grid_options_thirds' => '2/3 x 1/3',
   ),
 );

 $metabox_full_options_select = array(
  'name' => 'Full Width',
  'id'   => $prefix . 'full_options_select',
  'desc'  => 'Choose to display full width content.',
  'type' => 'select',
  'placeholder' => 'Select...',
  'options' => array(
    $prefix . 'blog_posts' => 'Blog Posts',
    $prefix . 'full_width_calendar' => 'Calendar',
    $prefix . 'custom_text' => 'Custom Text',
    $prefix . 'get_involved' => 'Get Involved',
    $prefix . 'full_width_press_releases' => 'Press Releases',
    $prefix . 'resource_list' => 'Resource List',
    ),
  );

  // Resource List
    $meta_call_to_action_multi = array(
      'id'  => $prefix . 'call_to_action_multi',
      'type' => 'group',
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

// Full Width Calendar
  $meta_full_calendar = array(
    'name' => 'Full Width Calendar',
    'id' => $prefix . 'full_width_calendar',
    'visible' => array('phila_full_options_select', '=', 'phila_full_width_calendar'),
    'type' => 'group',
    // List of sub-fields
    'fields' => array(
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
    ),
  );
  // Connect Panel
  $metabox_connect = array(
    'id' => $prefix . 'connect_panel',
    'type' => 'group',
    'hidden' => array('phila_one_third_col_option', '!=', 'phila_one_third_column_connect'),

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
          // TODO: Add optional "Submit a Request" Button toggle and options
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
      ),
    );

  // Custom Text
  $metabox_custom_text = array(
     'id'   => $prefix . 'custom_text',
     'type' => 'group',

     'fields' => array(
       array(
         'name' => 'Custom Text Title',
         'id'   => $prefix . 'custom_text_title',
         'type' => 'text',
       ),
       array(
         'name' => 'Custom Text Content',
         'id'   => $prefix . 'custom_text_content',
         'type' => 'wysiwyg',
        //  'hidden' => array('phila_two_thirds_col_option', '!=', 'phila_custom_text'),
         'options' => array(
           'media_buttons' => false,
           'teeny' => true,
           'dfw' => false,
           'quicktags' => false,
          //  'tinymce' => $department_col_1_custom_content,
         ),
      ),
    ),
  );

  $metabox_full_options = array(
   'name' => '2/3 x 1/3 Options',
   'id'   => $prefix . 'full_options',
   'type' => 'group',
   'visible' => array(
     'phila_grid_options',
     '=',
     'phila_grid_options_full'
   ),
   'fields' => array(
     $metabox_full_options_select,
     $meta_full_calendar,
     $metabox_custom_text,
     $meta_call_to_action_multi
   ),
 );

// End Full options

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
          $prefix . 'two_thirds_column_blog_posts' => 'Blog Posts',
          $prefix . 'custom_text' => 'Custom Text',
          ),
      ),
      $metabox_connect,
      $metabox_custom_text
    ),
  );

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
        $prefix . 'one_third_column_blog_posts' => 'Blog Posts',
        $prefix . 'one_third_column_connect' => 'Connect Panel',
        $prefix . 'custom_text' => 'Custom Text',
        ),
      ),
      $metabox_connect,
      $metabox_custom_text,
      $meta_call_to_action_multi
    ),
  );

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

 $metabox_grid_row = array(
   'id'    => $prefix . 'row',
   'type'  => 'group',
   'clone' => true,
   'sort_clone' => true,
   'fields' => array( $metabox_grid_options , $metabox_full_options, $metabox_thirds_options ),
 );

//Department Hompage metaboxes
$meta_boxes[] = array(
  'id'       => $prefix . 'department_homepage',
  'title'    => 'Department Homepage',
  'pages' => array( 'department_page' ),
  'priority' => 'high',

  'fields' => array(
    $metabox_grid_row,
  )
);

return $meta_boxes;

}
// The following filter based on MetaBox documentation
add_filter( 'rwmb_group_add_clone_button_text', 'phila_group_add_clone_button_text', 10, 2 );

function phila_group_add_clone_button_text( $text, $field ) {
  if ( 'phila_resource_list' == $field['id'] ) {
      $text = __( '+ Add a Resource List', 'textdomain' );
  } else if ( 'phila_resource_list_items' == $field['id'] ){
    $text = __( '+ Add an Item', 'textdomain' );
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

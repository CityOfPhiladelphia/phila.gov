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

  $serviceBeforeStartToolbarOne['toolbar1'] = 'bold, italic, bullist, numlist, link, unlink, outdent, indent, formatselect';

  $serviceRelatedContent['toolbar1'] = 'bullist, link, unlink';

  $meta_boxes[] = array(
    'id'       => 'service_additions',
    'title'    => 'Service Description',
    'pages'    => array( 'service_post' ),
    'context'  => 'advanced',
    'priority' => 'high',

    'fields' => array(
      array(
        'name'  => 'Description',
        'desc'  => 'A short description of the Service',
        'id'    => $prefix . 'service_desc',
        'type'  => 'textarea',
        'class' => 'service-description',
        'clone' => false,
      ),
       array(
        'name'  => 'Full URL of Service',
        'desc'  => 'https://ework.phila.gov/revenue/',
        'id'    => $prefix . 'service_url',
        'type'  => 'URL',
        'class' => 'service-url',
        'clone' => false,
      ),
      array(
        'name'  => 'Detail',
        'desc'  => 'The name of the website',
        'id'    => $prefix . 'service_detail',
        'type'  => 'text',
        'class' => 'service-detail',
        'clone' => false,
      ),
      array(
        'name'  => 'Actionable Button Name',
        'desc'  => 'Button Text, eg. Pay Now',
        'id'    => $prefix . 'service_button_text',
        'type'  => 'text',
        'class' => 'service-button',
        'clone' => false,
      ),
    )
  );//Service page links and description

  $meta_boxes[] = array(
    'id'       => 'service_before_start',
    'title'    => 'Before You Start Details',
    'pages'    => array( 'service_post' ),
    'context'  => 'advanced',
    'priority' => 'high',

    'fields' => array(
      array(
        'name'  => '',
        'desc'  => 'Enter content the user needs to know before starting this service',
        'id'    => $prefix . 'service_before_start',
        'type'  => 'wysiwyg',
        'class' => 'service-start',
        'clone' => false,
        'options' => array(
          'teeny' => true,
          'dfw' => false,
          'tinymce' => $serviceBeforeStartToolbarOne,
        ),
      ),
    )
  );
  $meta_boxes[] = array(
    'id'       => 'service_related_items',
    'title'    => 'Related Items',
    'pages'    => array( 'service_post' ),
    'context'  => 'side',
    'priority' => 'high',

    'fields' => array(
      array(
        'name'  => 'Unordered list of related links',
        'id'    => $prefix . 'service_related_items',
        'type'  => 'wysiwyg',
        'class' => 'service-related',
        'clone' => false,
        'options' => array(
          'editor_height' => 25,
          'teeny' => true,
          'dfw' => false,
          'tinymce' =>  $serviceRelatedContent,
        ),
      ),
    )
  );

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
     'user_role'  => 'administrator',
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
       'size'  => '60'
     ),
     array(
       'name'  => 'Call to Action Button URL',
       'desc'  => 'Optional URL to include as a "call to action" button',
       'id'    => $prefix . 'hero_header_call_to_action_button_url',
       'type'  => 'URL',
       'class' => 'hero-header-call-to-action-button-url',
     ),
     array(
       'name'  => 'Call to Action Button Text',
       'id'    => $prefix . 'hero_header_call_to_action_button_text',
       'type'  => 'text',
       'class' => 'hero-header-call-to-action-button-text',
       'desc'  => 'Text that appears on the "call to action" button.',
       'size'  => '30'
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
   'title'    => 'Module Row 1',
   'pages'    => array( 'department_page' ),
   'context'  => 'normal',
   'priority' => 'low',
   'include' => array(
     'user_role'  => 'administrator',
   ),
   'fields' => array(
     array(
      'name' => 'Description',
      'id'   => $prefix . 'module_row_1_description',
      'type' => 'custom_html',
      'std'  => '<span>Use this area to create a row that will be divided into two columns. The first column will take up 2/3 of the screen and second will take up 1/3.</span>',
     ),
     array(
       'type' => 'divider'
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
              'type' => 'textarea',
              'hidden' => array('phila_module_row_1_col_1_type', '!=', 'phila_module_row_1_col_1_custom_text'),

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
          'id'   => $prefix . 'module_row_1_col_2_type',
          'desc'  => 'Choose to display recent blog posts, custom markup or a call to action panel.',
          'type' => 'select',
          'placeholder' => 'Select...',
          'options' => array(
            $prefix . 'module_row_1_col_2_blog_posts' => 'Blog Posts',
            $prefix . 'module_row_1_col_2_custom_text' => 'Custom Text',
            $prefix . 'module_row_1_col_2_call_to_action_panel' => 'Call to Action Panel',
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
            'id'    => $prefix . 'module_row_1_col_2_action_section_title',
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
    ),
  );

// Second row of modules - press release and/or calendar
$meta_boxes[] = array(
  'id'       => $prefix . 'module_row_2',
  'title'    => 'Module Row 2',
  'pages'    => array( 'department_page' ),
  'context'  => 'normal',
  'priority' => 'low',
  'include' => array(
    'user_role'  => 'administrator',
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

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
    'id'       => $prefix . 'custom_markup',
    'title'    => 'Custom Markup',
    'pages'    => array( 'department_page', 'page' ),
    'context'  => 'normal',
    'priority' => 'low',

    'fields' => array(
      array(
       'name' => 'Description',
       'id'   => $prefix . 'custom_markup_description',
       'type' => 'custom_html',
       'std'  => '<span>Use this area to insert CSS, HTML or JS snippets either before or after the contents of the WYSIWYG editor.</span>',
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

 // First row of modules - recent posts and custom markup
 $meta_boxes[] = array(
   'id'       => $prefix . 'module_row_1',
   'title'    => 'Module Row 1',
   'pages'    => array( 'department_page' ),
   'context'  => 'normal',
   'priority' => 'low',

   'fields' => array(
     array(
      'name' => 'Description',
      'id'   => $prefix . 'module_row_1_description',
      'type' => 'custom_html',
      'std'  => '<span>Use this area to create a row that will be divided into two columns. The first column will take up 2/3 of the screen and second will take up 1/3.</span>',
     ),
     array(
       'name'  => 'Non-Admin Access',
       'id'    => $prefix . 'module_row_1_admin_only',
       'class' => 'phila-access-control',
       'type'  => 'checkbox',
       'desc'  => 'Allow non-admins to edit Module Row 1',
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
               'options' => array(
                 $prefix . 'module_row_1_col_1_post_style_cards' => 'Card',
                 $prefix . 'module_row_1_col_1_post_style_list' => 'List',
               ),
             ),
             array(
              'name' => 'Custom Text Title',
              'id'   => $prefix . 'module_row_1_col_1_texttitle',
              'type' => 'text',
             ),
             array(
              'name' => 'Custom Text Content',
              'id'   => $prefix . 'module_row_1_col_1_textarea',
              'type' => 'textarea',
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
      // List of sub-fields
      'fields' => array(
         array(
          'name' => 'Column 2 <br/><small>(1/3 column)</small>',
          'id'   => $prefix . 'module_row_1_col_2_type',
          'desc'  => 'Choose to display recent blog posts or custom markup text.',
          'type' => 'select',
          'options' => array(
            $prefix . 'module_row_1_col_2_blog_posts' => 'Blog Posts',
            $prefix . 'module_row_1_col_2_custom_text' => 'Custom Text',
          ),
        ),
        array(
          'id' => 'module_row_1_col_2_options',
          'type' => 'group',
          // List of sub-fields
          'fields' => array(
            array(
             'name' => 'Blog Post Style',
             'id'   => $prefix . 'module_row_1_col_2_post_style',
             'desc'  => 'Recent posts are displayed as "Cards" by default.',
             'type' => 'select',
             'options' => array(
               $prefix . 'module_row_1_col_2_post_style_cards' => 'Card',
               $prefix . 'module_row_1_col_2_post_style_list' => 'List',
               ),
             ),
             array(
              'name' => 'Custom Text Title',
              'id'   => $prefix . 'module_row_1_col_2_texttitle',
              'type' => 'text',
             ),
             array(
              'name' => 'Custom Text Content',
              'id'   => $prefix . 'module_row_1_col_2_textarea',
              'type' => 'textarea',
             ),
          ),
        ),
     ),
   ),
  )
);

  $meta_boxes[] = array(
    'id'       => 'jotform-embed',
    'title'    => 'JotForm Embed',
    'pages'    => array( 'department_page' ),
    'context'  => 'normal',
    'priority' => 'low',

    'fields' => array(
      array(
       'name' => 'JotForm Markup',
       'id'   => $prefix . 'jotform_embed',
       'type' => 'textarea'
     ),
   ),
 );
    return $meta_boxes;
}

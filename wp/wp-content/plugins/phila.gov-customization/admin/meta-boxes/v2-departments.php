<?php

add_filter( 'rwmb_meta_boxes', 'phila_register_department_meta_boxes' );

function phila_register_department_meta_boxes( $meta_boxes ){

  //Department Homepage
  $meta_boxes[] = array(
    'title' => 'Department media',
    'pages'    => array( 'department_page' ),
    'visible' => array( 'phila_template_select', 'homepage_v2' ),
    'context'  => 'normal',
    'priority' => 'high',

    'fields' => array(
      array(
        'type' => 'heading',
        'name'  => 'Hero image',
        'columns' => '4'
      ),
      array(
        'type' => 'heading',
        'name'  => 'Hero image for mobile',
        'columns' => '4'
      ),
      array(
        'type' => 'heading',
        'name'  => 'Department logo',
        'columns' => '4'
      ),
      array(
        'id' => 'phila_v2_homepage_hero',
        'title' => 'Select image',
        'type' => 'image_advanced',
        'desc'  => 'Required. Image must be 1110px wide & 315px tall.',
        'max_file_uploads' => 1,
        'columns' => '4'
      ),
      array(
        'id' => 'phila_v2_homepage_hero_mobile',
        'title' => 'Select image',
        'type' => 'image_advanced',
        'desc'  => 'Required. Image must be 800px wide & 227px tall.',
        'max_file_uploads' => 1,
        'columns' => '4'
      ),
      array(
        'id'  => 'phila_v2_department_logo',
        'title' => 'Department logo',
        'type'  => 'image_advanced',
        'desc'  => 'Optional. Image must be at least 600px wide.',
        'max_file_uploads' => 1,
        'columns' => '4'
      ),
      array(
        'type' => 'heading',
        'name'  => 'Photo credit',
      ),
      Phila_Gov_Standard_Metaboxes::phila_metabox_title('Name & organization', 'phila_v2_photo_credit', 'E.g.: N. Santos for VISIT PHILADELPHIA™', '60' ),
    ),
  );

  $meta_boxes[] = array(
    'title' => 'Our services',
    'pages'    => array( 'department_page' ),
    'visible' => array( 'phila_template_select', 'homepage_v2' ),

    'fields' => array(
      array(
        'id'       => 'phila_v2_homepage_services',
        'title'    => 'Top services',
        'context'  => 'normal',
        'priority' => 'high',
        'type'  => 'group',
        'clone' => true,

        'fields' => array(
          Phila_Gov_Standard_Metaboxes::phila_v2_icon_selection(),
          Phila_Gov_Standard_Metaboxes::phila_v2_service_page_selector(),
          Phila_Gov_Standard_Metaboxes::phila_metabox_title( 'Alternate title', 'alt_title' ),
        ),
      ),
      array(
        'id' => 'phila_v2_service_link',
        'title' => 'All services link',
        'name'  => 'All services link',
        'type'  => 'url',
        'class' => 'metabox-url',
      ),
    ),
  );

  $meta_boxes[] = array(
    'title' => 'Services list',
    'pages'    => array( 'department_page' ),
    'visible' => array( 'phila_template_select', 'service_list_v2' ),

    'fields' => array(
      array(
        'id'       => 'phila_v2_services_list',
        'title'    => 'Services',
        'context'  => 'normal',
        'priority' => 'high',
        'type'  => 'group',
        'clone' => true,

        'fields' => array(
          Phila_Gov_Standard_Metaboxes::phila_v2_service_page_selector(),
        ),
      ),
    ),
  );

  $meta_boxes[] = array(
    'title' => 'Full-width call to action',
    'pages'    => array( 'department_page' ),
    'visible' => array( 'phila_template_select', 'homepage_v2' ),

    'fields' => array(
      array(
        'id'       => 'phila_v2_cta_full',
        'context'  => 'normal',
        'priority' => 'default',
        'type'  => 'group',
        'clone' => false,

        'fields' => array(
          Phila_Gov_Standard_Metaboxes::phila_metabox_title( 'Title', 'cta_full_title', '50 character maximum.' ),
          Phila_Gov_Standard_Metaboxes::phila_metabox_textarea('Description', 'cta_full_description', '140 character maximum.' ),
          Phila_Gov_Standard_Metaboxes::phila_metabox_v2_link_fields('Button details', 'cta_full_link'),
          array(
            'id' => 'cta_is_survey',
            'desc'  => 'Is this a link to a survey or other form of feedback gathering?',
            'type'  => 'checkbox',
          ),
        ),
      ),
    ),
  );

  $meta_boxes[] = array(
    'title' => 'Forms and Documents',
    'pages'    => array( 'department_page' ),
    'visible' => array( 'phila_template_select', 'forms_and_documents_v2' ),

    'fields' => array(
      array(
        'id'  => 'phila_forms_documents_cta',
        'type' => 'group',
        'clone'  => true,
        'max_clone' => 4,
        'sort_clone' => true,

        'fields' => array(
          //TODO: decide whether or not we want to use arguments as demonstrated below
          Phila_Gov_Standard_Metaboxes::phila_metabox_title('Call to Action Title', 'phila_action_panel_cta_text_multi' ),
          Phila_Gov_Standard_Metaboxes::phila_metabox_textarea('Summary', 'phila_action_panel_summary_multi'),
          Phila_Gov_Standard_Metaboxes::phila_metabox_url('Link to Content','phila_action_panel_link_multi'),
          array(
            'name' => 'Featured Documents (optional)',
            'type'  => 'heading'
          ),
          array(
            'id'   => 'phila_featured_documents',
            'type' => 'group',
            //TODO: Nested clones with max_clone does not work properly with post picker... Find a better solution?
            'fields' => array(
              Phila_Gov_Standard_Metaboxes::phila_metabox_post_picker('Select Document 1', 'phila_featured_document_item_0', 'document' ),
              Phila_Gov_Standard_Metaboxes::phila_metabox_post_picker('Select Document 2', 'phila_featured_document_item_1', 'document' ),
              Phila_Gov_Standard_Metaboxes::phila_metabox_post_picker('Select Document 3', 'phila_featured_document_item_2', 'document' ),
            ),
          ),
        ),
      ),
    ),
  );


  $meta_boxes[] = array(
    'title' => 'Select category',
    'pages'    => array( 'department_page' ),
    'visible' => array( 'phila_template_select', 'staff_directory_v2' ),

    'fields' => array(
      array(
        'id' => 'phila_staff_category',
        'name' => 'Get staff in this category only -- overrides page Category selection',
        'type' => 'text',
      )
    )
  );

  $meta_boxes[] = array(
    'title' => 'Featured programs or content',
    'pages'    => array( 'department_page' ),
    'visible' => array( 'phila_template_select', 'homepage_v2' ),

    'fields' => array(
      array(
        'id'       => 'phila_v2_homepage_featured_section',
        'title'    => 'Title',
        'type'  => 'group',
        'fields' => array(
          Phila_Gov_Standard_Metaboxes::phila_metabox_title('Section title', 'title', '', '60' ),
        ),
      ),
      array(
        'id'       => 'phila_v2_homepage_featured',
        'title'    => 'Title',
        'type'  => 'group',
        'clone' => true,
        'max_clone' => 3,

        'fields' => array(
          array(
            'name' => 'Select page',
            'type'  => 'heading',
            'columns' => '6'
          ),
          array(
            'name' => 'Alternate title',
            'type'  => 'heading',
            'columns' => '6'
          ),
          Phila_Gov_Standard_Metaboxes::phila_metabox_post_picker('', 'phila_featured_page', 'department_page', false, '', '6' ),

          Phila_Gov_Standard_Metaboxes::phila_metabox_title('', 'phila_featured_title', 'Optional.', '50', '6' ),
          array(
            'name' => 'Image',
            'type'  => 'heading',
            'columns' => '6'
          ),
          array(
            'name' => 'Description',
            'type'  => 'heading',
            'columns' => '6'
          ),
          array(
            'id' => 'phila_featured_img',
            'title' => 'Select image',
            'type' => 'image_advanced',
            'desc'  => 'Required. Image must be square and a minimum of 150px by 150px.',
            'max_file_uploads' => 1,
            'columns' => '6'
          ),
          Phila_Gov_Standard_Metaboxes::phila_metabox_textarea('', 'phila_featured_description', '220 characters maximum.', '6' ),
        ),
      ),
    ),
  );


  return $meta_boxes;
}

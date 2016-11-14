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
        'name'  => 'Hero image'
      ),
      array(
        'id' => 'phila_v2_homepage_hero',
        'title' => 'Select image',
        'type' => 'image_advanced',
        'max_file_uploads' => 1,
      ),
      array(
        'type' => 'heading',
        'name'  => 'Department logo'
      ),
      array(
        'id'  => 'phila_v2_logo',
        'title' => 'Department logo',
        'type'  => 'image_advanced',
        'max_file_uploads' => 1,
      ),
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
        ),
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
          Phila_Gov_Standard_Metaboxes::phila_metabox_cta_multi_title('Call to Action Title', 'phila_action_panel_cta_text_multi' ),
          Phila_Gov_Standard_Metaboxes::phila_metabox_cta_multi_summary(),
          Phila_Gov_Standard_Metaboxes::phila_metabox_cta_multi_icon(),
          Phila_Gov_Standard_Metaboxes::phila_metabox_cta_multi_icon_circle(),
          Phila_Gov_Standard_Metaboxes::phila_metabox_url('Link to Content','phila_action_panel_link_multi'),
          Phila_Gov_Standard_Metaboxes::phila_metabox_cta_multi_external_link(),
          array(
            'name' => 'Featured Documents (optional)',
            'type'  => 'heading'
          ),
          array(
            'id'   => 'phila_featured_documents',
            'type' => 'group',
            //TODO: Nested clones with max_clone does not work properly with post picker... Find a better solution?
            'fields' => array(
              Phila_Gov_Standard_Metaboxes::phila_metabox_cta_post_picker('Select Document 1', 'phila_featured_document_item_0', 'document' ),
              Phila_Gov_Standard_Metaboxes::phila_metabox_cta_post_picker('Select Document 2', 'phila_featured_document_item_1', 'document' ),
              Phila_Gov_Standard_Metaboxes::phila_metabox_cta_post_picker('Select Document 3', 'phila_featured_document_item_2', 'document' ),
            ),
          ),
        ),
      ),
    ),
  );



  return $meta_boxes;
}

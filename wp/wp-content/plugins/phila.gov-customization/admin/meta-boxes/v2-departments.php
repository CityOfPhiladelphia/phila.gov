<?php

add_filter( 'rwmb_meta_boxes', 'phila_register_department_meta_boxes' );

function phila_register_department_meta_boxes( $meta_boxes ){

  //Department Homepage
  $meta_boxes[] = array(
    'title' => 'Hero header v2',
    'pages'    => array( 'department_page' ),
    'visible' => array( 'phila_template_select', 'homepage_v2' ),
    'context'  => 'normal',
    'priority' => 'high',

    'fields' => array(
      array(
        'id'       => 'phila_v2_homepage_hero',
        'title'    => 'Select image',
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


  return $meta_boxes;
}

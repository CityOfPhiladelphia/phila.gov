<?php

add_filter( 'rwmb_meta_boxes', 'phila_register_department_meta_boxes' );

function phila_register_department_meta_boxes( $meta_boxes ){


  $meta_boxes[] = array(
    'title' => 'Our services',
    'pages'    => array( 'department_page' ),
    'visible' => array('phila_template_select', 'department_homepage_v2'),

    'fields' => array(
      array(
        'id'       => 'phila_v2_homepages',
        'title'    => 'Service Picker',
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

  return $meta_boxes;
}

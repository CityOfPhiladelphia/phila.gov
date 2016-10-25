<?php

add_filter( 'rwmb_meta_boxes', 'phila_register_department_contact_us' );

function phila_register_department_contact_us( $meta_boxes ){

  $meta_boxes[] = array(
    'title' => 'Contact row',
    'pages' => array('department_page'),
    'visible' => array( 'phila_template_select', 'contact_us_v2' ),

    'fields' => array(
      array(
        'id' => 'phila_contact_us',
        'title'    => 'Contact us',
        'context'  => 'normal',
        'priority' => 'high',
        'type'  => 'group',
        'clone' => true,

        'fields' => array(
          Phila_Gov_Standard_Metaboxes::phila_v2_icon_selection(),
          //icon
          //Heading
          //Address
          //phone
          //email
          //Fax
          //hours

        ),
      ),
    )
  );


  $meta_boxes[] = array(

  );
  return $meta_boxes;

}

<?php

add_filter( 'rwmb_meta_boxes', 'phila_register_department_contact_us' );

function phila_register_department_contact_us( $meta_boxes ){


  //icon
  //Heading
  //Address
  //phone
  //email
  //Fax
  //hours
  
  $meta_boxes[] = array(

  );
  return $meta_boxes;

}

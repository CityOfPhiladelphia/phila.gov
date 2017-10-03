<?php
/*
 * Exposes certain meta fields to the REST API.
 * See https://developer.wordpress.org/rest-api/extending-the-rest-api/modifying-responses/
 *
*/

/* Expose service page icons for use in Megamenu markup */
add_action('rest_api_init', 'phila_register_icon_service_pages');

function phila_register_icon_service_pages(){
  $object_type = 'post';
  $args = array(
      'type'         => 'string',
      'description'  => 'A meta key for service page icons. String.',
      'single'       => true,
      'show_in_rest' => true,
  );
  register_meta( $object_type, 'phila_page_icon', $args );
}

add_action('rest_api_init', 'phila_register_dept_meta');

function phila_register_dept_meta(){
  register_rest_field('department_page', 'contact_information', array(
    get_callback => 'get_contact_info' )
  );
  function get_contact_info($object){
    $post_id = $object['id'];

    $meta = get_post_meta($post_id, 'module_row_1_col_2_connect_panel');

    return $meta;
  }
}

add_action('rest_api_init', 'phila_register_dept_code');

function phila_register_dept_code(){
  register_rest_field('department_page', 'department_code', array(
    get_callback => 'get_dept_code' )
  );
  function get_dept_code($object){
    $post_id = $object['id'];

    $meta = get_post_meta($post_id, 'phila_department_code');

    return $meta;
  }
}

//Expose template type
add_action('rest_api_init', 'phila_register_template_type');

function phila_register_template_type(){
  $object_type = 'post';
  $args = array(
      'type'         => 'string',
      'description'  => 'Metakey for template type. String.',
      'single'       => true,
      'show_in_rest' => true,
  );
  register_meta( $object_type, 'phila_template_select', $args );
}

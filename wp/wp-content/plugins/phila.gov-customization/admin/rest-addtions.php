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
  register_meta( $object_type, 'phila_page_icon', $args1 );
}

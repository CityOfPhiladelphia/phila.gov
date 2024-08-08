<?php 

// register custom fields
register_rest_field( 'announcement', 'categories',  array( 'get_callback' => 'get_phila_announcement_categories' ));
register_rest_field( 'announcement', 'tags',        array( 'get_callback' => 'get_phila_announcement_tags' ));

function get_phila_announcement_categories ( $post ) {
  return phila_get_the_category($post['id']);
}

function get_phila_announcement_tags ( $post ) {
  return get_the_tags($post['id']);
}
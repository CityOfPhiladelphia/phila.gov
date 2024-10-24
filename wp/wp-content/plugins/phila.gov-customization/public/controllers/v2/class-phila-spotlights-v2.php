<?php 

// register custom fields
register_rest_field( 'event_spotlight', 'categories', array( 'get_callback' => 'get_phila_spotlights_categories' ));
register_rest_field( 'event_spotlight', 'active',     array( 'get_callback' => 'get_phila_spotlights_active' ));

// add filter functionality
add_filter( 'rest_event_spotlight_query', 'filter_spotlight_by_active', 10, 2 );

function get_phila_spotlights_categories ( $post ) {
  return phila_get_the_category($post['id']);
}

function get_phila_spotlights_active( $post ) {
  $is_active = rwmb_meta('spotlight_is_active', '', $post['id']);
  return (bool) $is_active;
}

function filter_spotlight_by_active( $args, $request ) {
  $active = $request->get_param( 'active' );

  if ( empty( $active )) {
      return $args;
  }

  if ( $active === 'true' ) {
    $active = 1;
  } else if ( $active === 'false' ){
    $active = 0;
  }

  $args['meta_query'] = array(
      array(
          'key'     => 'spotlight_is_active',
          'value'   => $active,
          'compare' => '=',
      ),
  );

  return $args;
}
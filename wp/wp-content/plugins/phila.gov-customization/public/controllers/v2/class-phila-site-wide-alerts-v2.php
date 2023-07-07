<?php 

// register custom fields
// register_rest_field( 'site_wide_alert', 'categories', array( 'get_callback' => 'get_phila_spotlights_categories' ));
// register_rest_field( 'site_wide_alert', 'active',     array( 'get_callback' => 'get_phila_spotlights_active' ));

// add filter functionality
add_filter( 'rest_site_wide_alert_query', 'filter_spotlight_by_active', 10, 2 );

function filter_spotlight_by_active( $args, $request ) {
  $active = $request->get_param( 'active' );

  if ( empty( $active )) {
      return $args;
  }

  // if active toggle is true
  // if between active dates

  // else send none


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
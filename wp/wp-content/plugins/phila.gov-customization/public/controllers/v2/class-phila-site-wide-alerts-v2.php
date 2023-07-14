<?php 

// register custom fields
// register_rest_field( 'site_wide_alert', 'categories', array( 'get_callback' => 'get_phila_spotlights_categories' ));
// register_rest_field( 'site_wide_alert', 'is_active',     array( 'get_callback' => 'get_phila_spotlights_active' ));

// add filter functionality
add_filter( 'rest_site_wide_alert_query', 'filter_site_wide_alert_by_active', 10, 2 );

function get_phila_spotlights_active( $post ) {
  $phila_alert_start = rwmb_meta('phila_alert_start', '', $post['id']);
  $phila_alert_end = rwmb_meta('phila_alert_end', '', $post['id']);
  $phila_alert_active = rwmb_meta('phila_alert_active', '', $post['id']);
  $alert_start = date( 'Y-m-d', $phila_alert_start );
  $alert_end = date( 'Y-m-d', $phila_alert_end );
  $now = current_datetime()->format('Y-m-d');

  if ($alert_start <= $now && $alert_end >= $now || $phila_alert_active ) {
    return true;
  }
  return false;
}

function filter_site_wide_alert_by_active( $args, $request ) {
  $active = $request->get_param( 'active' );
  // $now = current_datetime()->format('Y-m-d');
  $today = date( 'Y/m/d' );

  // if ( empty( $active )) {
      // return $args;
  // }

  // if ( $active === 'true' ) {
  //   $active = 1;
  // } else if ( $active === 'false' ){
  //   $active = 0;
  // }

  // $args['meta_query'] = array(
  //   'relation' => 'OR',
  //   array(
  //       'relation' => 'AND',
  //       array(
  //           'key' => 'phila_alert_start',
  //           'value' => $now,
  //           'compare' => '<=',
  //           'type' => 'DATETIME',
  //       ),
  //       array(
  //           'key' => 'phila_alert_end',
  //           'value' => $now,
  //           'compare' => '>=',
  //           'type' => 'DATETIME',
  //       ),
  //   ),
  //   array(
  //       'key' => 'phila_alert_active',
  //       'value' => $active,
  //       'compare' => '=',
  //       'type' => 'BOOLEAN',
  //   ),
  // );

  $args['meta_query'] = array(
    array(
      'key' => 'phila_alert_start', // Check the start date field
      'value' => current_time( 'mysql' ),
      'compare' => '>=', 
      'type' => 'DATETIME' 
    )
  );
  return $args;
}
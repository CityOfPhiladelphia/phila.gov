<?php 

// add filter functionality
add_filter( 'rest_site_wide_alert_query', 'filter_site_wide_alert_by_active', 10, 2 );

function filter_site_wide_alert_by_active( $args, $request ) {

  $active = $request->get_param( 'active' );
  $now = current_time('timestamp');

  if ( empty( $active )) {
      return $args;
  }

  if ( $active === 'true' ) {
    $active = 1;
  } else if ( $active === 'false' ){
    $active = 0;
  }

  $args['meta_query'] = array(
    'relation' => 'OR',
    array(
        'relation' => 'AND',
        array(
            'key' => 'phila_alert_start',
            'value' => $now,
            'compare' => '<=',
            'type' => 'integer',
        ),
        array(
            'key' => 'phila_alert_end',
            'value' => $now,
            'compare' => '>=',
            'type' => 'integer',
        ),
    ),
    array(
        'key' => 'phila_alert_active',
        'value' => $active,
        'compare' => '=',
        'type' => 'BOOLEAN',
    ),
  );
  return $args;
}
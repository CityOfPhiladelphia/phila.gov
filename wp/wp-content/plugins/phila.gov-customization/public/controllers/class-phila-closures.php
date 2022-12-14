<?php

class Phila_Closures_Controller {

  // Initialize the namespace and resource name.
  public function __construct() {
    $this->namespace     = 'closures/v1';
    $this->resource_name = 'closure';
  }

  // Register our routes.
  public function register_routes() {
  // Register the endpoint for collections.
    register_rest_route( $this->namespace, '/' . $this->resource_name, array(
      array(
        'methods'   => WP_REST_Server::READABLE,
        'callback'  => array( $this, 'get_items' ),
        'permission_callback' => '__return_true',
      ),
      'schema' => array( $this, 'get_item_schema' ),
    ) );

  // Register today
    register_rest_route( $this->namespace, '/' . $this->resource_name . '/today', array(
      array(
        'methods'   => WP_REST_Server::READABLE,
        'callback'  => array( $this, 'get_today' ),
        'permission_callback' => '__return_true',
      ),
      'schema' => array( $this, 'get_item_schema' ),
    ) );

  // Register by date
    register_rest_route( $this->namespace, '/' . $this->resource_name . '/(?P<date>[\d]{4}-[\d]{2}+-[\d]{2})', array(
      array(
        'methods'   => WP_REST_Server::READABLE,
        'callback'  => array( $this, 'get_by_date' ),
        'permission_callback' => '__return_true',
      ),
      'schema' => array( $this, 'get_item_schema' ),
    ) );

  // Register by week
  register_rest_route( $this->namespace, '/' . $this->resource_name . '/week', array(
    array(
      'methods'   => WP_REST_Server::READABLE,
      'callback'  => array( $this, 'get_week' ),
      'permission_callback' => '__return_true',
    ),
    'schema' => array( $this, 'get_item_schema' ),
  ) );
  }

  /**
   * Return all closures
   *
   * @param WP_REST_Request $request Current request.
  */
  public function get_items( $request ) {

    $holidays = rwmb_meta( 'phila_holidays', array( 'object_type' => 'setting' ), 'phila_settings' );
    $phila_collection_status = rwmb_meta( 'phila_collection_status', array( 'object_type' => 'setting' ), 'phila_settings' );
    $flexible_collection = rwmb_meta( 'phila_flexible_collection', array( 'object_type' => 'setting' ), 'phila_settings' );
    $delay = false;
    $undetermined = false;

    switch ($phila_collection_status) {
      case 0:
        $status = "Trash and recycling collections are on schedule.";
        break;
      case 1:
        $status = "Trash and recycling collections are delayed in some areas. Set materials out on scheduled day.";
        break;
      case 2:
        $delay = true;
        $status = "Trash and recycling collections are delayed in some areas. Set materials out one day behind scheduled day.";
        break;
      case 3:
        $status = $flexible_collection['phila_flexible_collection_status'];
        if ( $flexible_collection['phila_flexible_collection_impact'] == 1 ) {
          $delay = true;
        } else if ( $flexible_collection['phila_flexible_collection_impact'] == 2 ) {
          $undetermined = true;
        }
        break;
    }

    $data = array();

    if ( empty( $holidays ) ) {
      return rest_ensure_response( $data );
    }
    $holiday_array = [];
    foreach ( $holidays as $holiday ) {
      $today = new DateTime();
      $today->setTime(0,0,1);

      $holiday_date = new DateTime($holiday['start_date']);
      $holiday_date->setTime(0,0,1);

      $end_date = clone $holiday_date;
      if ($end_date->format('N') < 5) {
        $end_date->modify('next friday');
      }
      $end_date->setTime(0,0,1);

      if ( ($holiday_date <= $today ) && ($end_date >= $today) && (date('N') <= 5) ) {
        $status = "Trash and recycling collections are on a holiday schedule. Set materials out one day behind your regular day.";
        $delay = true;
      }

      $response = $this->prepare_item_for_response( $holiday, $request );

      $holiday_array[] = $this->prepare_response_for_collection( $response );
    }
      $data['holidays'] = $holiday_array;

      $undetermined_response = $this->prepare_undetermined_for_response( $undetermined );
      $status_response = $this->prepare_status_for_response( $status );
      $delay_response = $this->prepare_delay_for_response( $delay );
      $data['undetermined'] = $undetermined_response->get_data();
      $data['delay'] =  $delay_response->get_data();
      $data['status'] = $status_response->get_data();

    // Return all response data.
    return rest_ensure_response( $data );
  }

  /**
   * Return closures that are occurring today
   *
   * @param WP_REST_Request $request Current request.
   * 
   * ex /closures/v1/closure/today
  */
  public function get_today( $request ) {

    $holidays = rwmb_meta( 'phila_holidays', array( 'object_type' => 'setting' ), 'phila_settings' );

    $data = array();

    $today = date('Y-m-d');
    
    if ( empty( $holidays ) ) {
      return rest_ensure_response( $data );
    }


    foreach ( $holidays as $holiday ) {
      $end_date = new DateTime($holiday['end_date']);
      $end_date->setTime(0,0,1);
      
      $period = new DatePeriod (
        new DateTime($holiday['start_date']),
        new DateInterval('P1D'),
        $end_date
      );

      foreach ($period as $key => $value) {
        if ($value->format('Y-m-d') == $today) {
          $response = $this->prepare_item_for_response( $holiday, $request );
  
          $data[] = $this->prepare_response_for_collection( $response );
          break;
        }
      }
    }

    // Return all response data.
    return rest_ensure_response( $data );
  }


  /**
   * Return closures that are occurring on a specific date
   *
   * @param WP_REST_Request $request Current request.
   * 
   * ex /closures/v1/closure/YYYY-MM-DD
  */
  public function get_by_date( $request ) {

    $request_date = (string) $request['date'];

    $date = date($request_date);

    $holidays = rwmb_meta( 'phila_holidays', array( 'object_type' => 'setting' ), 'phila_settings' );

    $data = array();
    
    if ( empty( $holidays ) ) {
      return rest_ensure_response( $data );
    }

    foreach ( $holidays as $holiday ) {
      $end_date = new DateTime($holiday['end_date']);
      $end_date->setTime(0,0,1);
      
      $period = new DatePeriod (
        new DateTime($holiday['start_date']),
        new DateInterval('P1D'),
        $end_date
      );

      foreach ($period as $key => $value) {
        if ($value->format('Y-m-d') == $date) {
          $response = $this->prepare_item_for_response( $holiday, $request );
  
          $data[] = $this->prepare_response_for_collection( $response );
          break;
        }
      }
    }

    // Return all response data.
    return rest_ensure_response( $data );
  }


  /**
   * Return closures that are occurring in the following 7 days (today + 6 days)
   * 
   * ex /closures/v1/closure/week
  */
  public function get_week( $request ) {

    $today = date('Y-m-d');

    $week = new DatePeriod(
      new DateTime($today), 
      new DateInterval('P1D'), 
      6);

    $holidays = rwmb_meta( 'phila_holidays', array( 'object_type' => 'setting' ), 'phila_settings' );

    $data = array();
    
    if ( empty( $holidays ) ) {
      return rest_ensure_response( $data );
    }

    foreach ( $holidays as $holiday ) {
      $end_date = new DateTime($holiday['end_date']);
      $end_date->setTime(0,0,1);
      
      $period = new DatePeriod (
        new DateTime($holiday['start_date']),
        new DateInterval('P1D'),
        $end_date
      );

      $match = false;

      foreach ($period as $key => $value) {
        foreach ($week as $key => $day) {
          if ($value->format('Y-m-d') == $day->format('Y-m-d')) {
            $response = $this->prepare_item_for_response( $holiday, $request );
    
            $data[] = $this->prepare_response_for_collection( $response );
            $match = true;
            break;
          }
        }
        if ($match == true) {
          break;
        }
      }
    }

    // Return all response data.
    return rest_ensure_response( $data );
  }


  /**
   * Matches the post data to the schema. Also, rename the fields to nicer names.
   *
   * @param WP_Post $post The comment object whose response is being prepared.
   */

  public function prepare_item_for_response( $post, $request ) {
    $post_data = array();

    $schema = $this->get_item_schema( $request );

    $post_data['holiday_label'] = (string) $post['holiday_label'] ?? '';

    $post_data['start_date']  = (string) $post['start_date'] ?? '';

    return rest_ensure_response( $post_data );
}

/**
   * Matches the post data to the schema. Also, rename the fields to nicer names.
   *
   * @param WP_Post $post The comment object whose response is being prepared.
   */

  public function prepare_undetermined_for_response( $undetermined ) {

    $post_data = (boolean) $undetermined;

    return rest_ensure_response( $post_data );
  }


/**
   * Matches the post data to the schema. Also, rename the fields to nicer names.
   *
   * @param WP_Post $post The comment object whose response is being prepared.
   */

  public function prepare_status_for_response( $status ) {

    $post_data = (string) $status;

    return rest_ensure_response( $post_data );
  }

/**
   * Matches the post data to the schema. Also, rename the fields to nicer names.
   *
   * @param WP_Post $post The comment object whose response is being prepared.
   */

  public function prepare_delay_for_response( $delay ) {

    $post_data = (boolean) $delay;

    return rest_ensure_response( $post_data );
  }

  /**
   * Prepare a response for inserting into a collection of responses.
   *
   * This is copied from WP_REST_Controller class in the WP REST API v2 plugin.
   *
   * @param WP_REST_Response $response Response object.
   * @return array Response data, ready for insertion into collection data.
   */
  public function prepare_response_for_collection( $response ) {
    if ( ! ( $response instanceof WP_REST_Response ) ) {
      return $response;
    }

    $data = (array) $response->get_data();
    $server = rest_get_server();

    if ( method_exists( $server, 'get_compact_response_links' ) ) {
      $links = call_user_func( array( $server, 'get_compact_response_links' ), $response );
    } else {
      $links = call_user_func( array( $server, 'get_response_links' ), $response );
    }

    if ( ! empty( $links ) ) {
      $data['_links'] = $links;
    }

    return $data;
  }

  /**
   * Get sample schema for a collection.
   *
   * @param WP_REST_Request $request Current request.
   */
  public function get_item_schema( $request ) {
    $schema = array(
      // This tells the spec of JSON Schema we are using which is draft 4.
      '$schema'              => 'http://json-schema.org/draft-04/schema#',
      // The title property marks the identity of the resource.
      'title'                => 'post',
      'type'                 => 'object',
      // Specify object properties in the properties attribute.
      'properties'           => array(
        'holiday_label'=> array(
          'description'  => esc_html__( 'Label of the object.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'start_date'  => array(
          'description' => esc_html__('The start date for this object.', 'phila-gov'),
          'type'  => 'date',
          'readonly'     => true,
        ),
      ),
    );

    return $schema;
  }

}

// Function to register our new routes from the controller.
function phila_register_closures_rest_routes() {
  $controller = new Phila_Closures_Controller();
  $controller->register_routes();
}

add_action( 'rest_api_init', 'phila_register_closures_rest_routes' );

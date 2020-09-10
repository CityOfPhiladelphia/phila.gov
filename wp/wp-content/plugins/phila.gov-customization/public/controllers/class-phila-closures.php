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

    $closures = rwmb_meta( 'phila_closures', array( 'object_type' => 'setting' ), 'phila_settings' );

    $data = array();

    if ( empty( $closures ) ) {
      return rest_ensure_response( $data );
    }

    foreach ( $closures as $closure ) {
      $response = $this->prepare_item_for_response( $closure, $request );

      $data[] = $this->prepare_response_for_collection( $response );
    }

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

    $closures = rwmb_meta( 'phila_closures', array( 'object_type' => 'setting' ), 'phila_settings' );

    $data = array();

    $today = date('Y-m-d');
    
    if ( empty( $closures ) ) {
      return rest_ensure_response( $data );
    }

    foreach ( $closures as $closure ) {
      if ( isset($closure['is_active'])) {
        $end_date = new DateTime($closure['end_date']);
        $end_date->setTime(0,0,1);
        
        $period = new DatePeriod (
          new DateTime($closure['start_date']),
          new DateInterval('P1D'),
          $end_date
        );
  
        foreach ($period as $key => $value) {
          if ($value->format('Y-m-d') == $today) {
            $response = $this->prepare_item_for_response( $closure, $request );
    
            $data[] = $this->prepare_response_for_collection( $response );
            break;
          }
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

    $closures = rwmb_meta( 'phila_closures', array( 'object_type' => 'setting' ), 'phila_settings' );

    $data = array();
    
    if ( empty( $closures ) ) {
      return rest_ensure_response( $data );
    }

    foreach ( $closures as $closure ) {
      if ( isset($closure['is_active'])) {
        $end_date = new DateTime($closure['end_date']);
        $end_date->setTime(0,0,1);
        
        $period = new DatePeriod (
          new DateTime($closure['start_date']),
          new DateInterval('P1D'),
          $end_date
        );

        foreach ($period as $key => $value) {
          if ($value->format('Y-m-d') == $date) {
            $response = $this->prepare_item_for_response( $closure, $request );
    
            $data[] = $this->prepare_response_for_collection( $response );
            break;
          }
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

    $closures = rwmb_meta( 'phila_closures', array( 'object_type' => 'setting' ), 'phila_settings' );

    $data = array();
    
    if ( empty( $closures ) ) {
      return rest_ensure_response( $data );
    }

    foreach ( $closures as $closure ) {
      if ( isset($closure['is_active'])) {
        $end_date = new DateTime($closure['end_date']);
        $end_date->setTime(0,0,1);
        
        $period = new DatePeriod (
          new DateTime($closure['start_date']),
          new DateInterval('P1D'),
          $end_date
        );

        $match = false;

        foreach ($period as $key => $value) {
          foreach ($week as $key => $day) {
            if ($value->format('Y-m-d') == $day->format('Y-m-d')) {
              $response = $this->prepare_item_for_response( $closure, $request );
      
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

    $post_data['closure_label'] = (string) $post['closure_label'] ?? '';

    $post_data['exception'] = (string) $post['exception'] ?? '';

    $post_data['start_date']  = (string) $post['start_date'] ?? '';

    $post_data['end_date'] = (string) $post['end_date'] ?? '';

    $post_data['is_recycling_biweekly'] = array_key_exists('is_recycling_biweekly', $post) ? (boolean) $post['is_recycling_biweekly'] : false;

    $post_data['is_active'] = array_key_exists('is_active', $post) ? (boolean) $post['is_active'] : false;

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
        'closure_label'=> array(
          'description'  => esc_html__( 'Label of the object.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'exception'=> array(
          'description'  => esc_html__( 'Exception for closure.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'start_date'  => array(
          'description' => esc_html__('The start date for this object.', 'phila-gov'),
          'type'  => 'date',
          'readonly'     => true,
        ),
        'end_date'  => array(
          'description' => esc_html__('The end date for this object.', 'phila-gov'),
          'type'  => 'date',
          'readonly'     => true,
        ),
        'is_recycling_biweekly'  => array(
          'description' => esc_html__('Is recycling bikweekly in this duration?', 'phila-gov'),
          'type'  => 'boolean',
          'readonly'     => true,
        ),
        'is_active'  => array(
          'description' => esc_html__('Is this closure active?', 'phila-gov'),
          'type'  => 'boolean',
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

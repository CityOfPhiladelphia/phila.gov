<?php

class Phila_Jobs_Controller {

  // Initialize the namespace and resource name.
  public function __construct() {
    $this->namespace     = 'jobs/v1';
    $this->resource_name = 'featured';
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

    //Register individual items
    register_rest_route( $this->namespace, '/' . $this->resource_name . '/(?P<id>[\d]+)', array(
      array(
        'methods'   => WP_REST_Server::READABLE,
        'callback'  => array( $this, 'get_item' ),
        'permission_callback' => '__return_true',
      ),
      'schema' => array( $this, 'get_item_schema' ),
    ) );
  }

  /**
   * Return all services, excluding stubs
   *
   * @param WP_REST_Request $request Current request.
  */
  public function get_items( $request ) {

    $featured_jobs = rwmb_meta( 'phila_featured_jobs', array( 'object_type' => 'setting' ), 'phila_settings' );

    $data = array();

    if ( empty( $featured_jobs ) ) {
      return rest_ensure_response( $data );
    }

    foreach ( $featured_jobs as $job ) {
      $response = $this->prepare_item_for_response( $job, $request );

      $data[] = $this->prepare_response_for_collection( $response );
    }

    // Return all response data.
    return rest_ensure_response( $data );
  }


  /**
   * Outputs an individual item's data
   *
   * @param WP_REST_Request $request Current request.
   */
  public function get_item( $request ) {
    $id = (int) $request['id'];
    $post = get_post( $id );

    if ( empty( $post ) ) {
      return rest_ensure_response( array() );
    }

    $response = $this->prepare_item_for_response( $post, $request );

    return $response;
  }

  /**
   * Matches the post data to the schema. Also, rename the fields to nicer names.
   *
   * @param WP_Post $post The comment object whose response is being prepared.
   */

  public function prepare_item_for_response( $post, $request ) {
    $post_data = array();

    $schema = $this->get_item_schema( $request );

    if (isset( $schema['properties']['title'] )) {
      $post_data['title'] = (string) $post['job_title'];
    }

    if (isset( $schema['properties']['url'] )) {
      $post_data['link']  = (string) $post['job_link'];
    }

    if (isset( $schema['properties']['desc'] )) {
      $post_data['desc'] = (string) $post['job_description'];
    }

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
        'title'=> array(
          'description'  => esc_html__( 'Title of the object.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'url'  => array(
          'description' => esc_html__('The permalink for this object.', 'phila-gov'),
          'type'  => 'string',
        ),
        'desc'  => array(
          'description' => esc_html__('Description of this page.', 'phila-gov'),
          'type'  => 'string',
        ),
      ),
    );

    return $schema;
  }

}

// Function to register our new routes from the controller.
function phila_register_jobs_rest_routes() {
  $controller = new Phila_Jobs_Controller();
  $controller->register_routes();
}

add_action( 'rest_api_init', 'phila_register_jobs_rest_routes' );

<?php

class Phila_Last_Updated_Controller {

  // Initialize the namespace and resource name.
  public function __construct() {
    $this->namespace     = 'last-updated/v1';
    $this->resource_name = 'all';
  }

  // Register our routes.
  public function register_routes() {
  // Register the endpoint for collections.
    register_rest_route( $this->namespace, '/' . $this->resource_name, array(
      array(
        'methods'   => WP_REST_Server::READABLE,
        'callback'  => array( $this, 'get_items' ),
      ),
      'schema' => array( $this, 'get_item_schema' ),
    ) );

    //Register individual items
    register_rest_route( $this->namespace, '/' . $this->resource_name . '/(?P<id>[\d]+)', array(
      array(
        'methods'   => WP_REST_Server::READABLE,
        'callback'  => array( $this, 'get_item' ),
      ),
      'schema' => array( $this, 'get_item_schema' ),
    ) );
  }

  /**
   * Get all public Post types
   *
   * @param WP_REST_Request $request Current request.
  */
  public function get_items( $request ) {


    $post_types = get_post_types(array(
      'public' => true
    ) );

    $args = array(
      'posts_per_page'=> -1,
      'post_type' => $post_types,
      'orderby' => 'modified',
      'order' => 'desc',
      'date_query' => array(
        'after' => $request['timestamp'],
        'column'  => 'post_modified',
        'inclusive' => true
      ),

    );

    $posts = get_posts( $args );

    $data = array();

    if ( empty( $posts ) ) {
      return rest_ensure_response( $data );
    }

    foreach ( $posts as $post ) {
      $response = $this->prepare_item_for_response( $post, $request );

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

    if (isset( $schema['properties']['link'] )) {
      $post_data['link']  =  (string)  get_permalink($post->ID);
    }
    if (isset( $schema['properties']['updated_at'] )) {
      $post_data['updated_at']  = get_the_modified_date('Y-m-d H:i:s', $post->ID);
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
        'link' => array(
          'description'  => esc_html__( 'Link to the object.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'updated_at' => array(
          'description'  => esc_html__( 'Last updated time.', 'phila-gov' ),
          'type'         => 'date',
          'readonly'     => true,
        ),
      ),
    );

    return $schema;
  }

}

// Function to register our new routes from the controller.
function phila_register_last_updated_rest_routes() {
  $controller = new Phila_Last_Updated_Controller();
  $controller->register_routes();
}

add_action( 'rest_api_init', 'phila_register_last_updated_rest_routes' );

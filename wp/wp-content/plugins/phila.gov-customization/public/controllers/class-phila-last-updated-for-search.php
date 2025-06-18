<?php

class Phila_Last_Updated_For_Search_Controller {

  // Initialize the namespace and resource name.
  public function __construct() {
    $this->namespace     = 'last-updated-for-search/v1';
    $this->resource_name = 'all';
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
   * Get all public Post types
   *
   * @param WP_REST_Request $request Current request.
  */
  public function get_items( $request ) {
    $post_types = get_post_types( array(
        'public' => true,
    ) );

    // Get pagination parameters from the request.
    $page     = isset( $request['page'] ) ? (int) $request['page'] : 1;
    $per_page = isset( $request['per_page'] ) ? (int) $request['per_page'] : 100; // Default to 100 entries per page.

    // Set up query arguments for pagination.
    $args = array(
      'posts_per_page'=> 100,
      'paged' => $page,
      'post_type' => $post_types,
      'post_status' => 'publish',
      'orderby' => 'modified',
      'order' => 'desc',
      'date_query' => array(
        'after' => $request['timestamp'],
        'column'  => 'post_modified',
        'inclusive' => true
      ),
    );

    $posts = get_posts( $args );

    $data = array(
      array(
        'link'  => '/404/',
        'updated_at' => ''
      ),
      array(
        'link'  => '/departments/',
        'updated_at' => ''
      ),
      array(
        'link'  => '/documents/',
        'updated_at' => ''
      ),
      array(
        'link'  => '/programs/',
        'updated_at' => ''
      ),
      array(
        'link'  => '/services/',
        'updated_at' => ''
      ),
    );

    if ( empty( $posts ) ) {
        return rest_ensure_response( $data );
    }

    foreach ( $posts as $post ) {
        $response = $this->prepare_item_for_response( $post, $request );
        $data[]   = $this->prepare_response_for_collection( $response );
    }

    // Add pagination information to the response.
    $total_posts = wp_count_posts()->publish;
    $total_pages = ceil( $total_posts / $per_page );

    $response = rest_ensure_response( $data );
    $response->header( 'X-WP-Total', $total_posts );
    $response->header( 'X-WP-TotalPages', $total_pages );

    return $response;
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
    $data = array(
      'post_id'      => $post->ID,
      'post_date'    => get_post_time('c', false, $post),
      'post_title'   => $post->post_title,
      'updated_at'   => get_post_modified_time('c', false, $post),
      'link'         => get_permalink($post->ID),
      'post_content' => $post->post_content,
      'post_type'    => $post->post_type,
      'tags'         => wp_get_post_tags( $post->ID, array( 'fields' => 'names' ) ),
      'categories'   => wp_get_post_categories( $post->ID, array( 'fields' => 'names' ) ),
    );

    return rest_ensure_response( $data );
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
    return array(
        '$schema'    => 'http://json-schema.org/draft-04/schema#',
        'title'      => 'post',
        'type'       => 'object',
        'properties' => array(
            'id' => array(
                'description' => __( 'Unique identifier for the object.', 'phila-gov' ),
                'type'        => 'integer',
                'readonly'    => true,
            ),
            'title' => array(
                'description' => __( 'Title of the object.', 'phila-gov' ),
                'type'        => 'string',
                'readonly'    => true,
            ),
            'link' => array(
                'description' => __( 'Link to the object.', 'phila-gov' ),
                'type'        => 'string',
                'readonly'    => true,
            ),
            'updated_at' => array(
                'description' => __( 'Last updated time.', 'phila-gov' ),
                'type'        => 'string',
                'format'      => 'date-time',
                'readonly'    => true,
            ),
            'post_content' => array(
                'description' => __( 'Content of the post.', 'phila-gov' ),
                'type'        => 'string',
                'readonly'    => true,
            ),
        ),
        'query_parameters' => array(
            'page' => array(
                'description' => __( 'Current page of the collection.', 'phila-gov' ),
                'type'        => 'integer',
                'default'     => 1,
            ),
            'per_page' => array(
                'description' => __( 'Number of items per page.', 'phila-gov' ),
                'type'        => 'integer',
                'default'     => 100, // Default to 100 entries per page.
            ),
        ),
    );
}
}

// Function to register our new routes from the controller.
function phila_register_last_updated_for_search_rest_routes() {
  $controller = new Phila_Last_Updated_For_Search_Controller();
  $controller->register_routes();
}

add_action( 'rest_api_init', 'phila_register_last_updated_for_search_rest_routes' );

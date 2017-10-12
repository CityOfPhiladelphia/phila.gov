<?php

class Phila_Archives_Controller {

  // Initialize the namespace and resource name.
  public function __construct() {
    $this->namespace     = 'the-latest/v1';
    $this->resource_name = 'archives';
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
   * Get the 40 latest posts within the "archives" umbrella
   *
   * @param WP_REST_Request $request Current request.
   */
  public function get_items( $request ) {
    $post_type = isset( $request['post_type'] ) ? array( $request['post_type']) : array('post', 'phila_post', 'press_release', 'news_post');

    $args = array(
      'posts_per_page' => 40,
      's' => $request['s'],
      'post_type' => $post_type,
      'orderby' => 'date',
      'template'  => $post_type,
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

    if ( isset( $schema['properties']['id'] ) ) {
        $post_data['id'] = (int) $post->ID;
    }

    if (isset( $schema['properties']['title'] )) {
      $post_data['title']  =  (string) $post->post_title;
    }

    if (isset( $schema['properties']['template'] )) {
      $post_data['template']  = (string) phila_get_selected_template($post->ID);
    }

    if (isset( $schema['properties']['date'] )) {
      $post_data['date']  = (string) $post->post_date;
    }

    if (isset( $schema['properties']['link'] )) {
      if ($post->post_type == 'phila_post'){

        $date = get_the_date('Y-m-d', $post->ID);
        $url = get_permalink($post->ID);
        $pattern = '/-';
        $replacement = '/' . $date . '-';

        $new_url = str_replace($pattern, $replacement, $url );
        $post_data['link']  = (string) $new_url;
      }else{
        $post_data['link']  = (string) get_permalink($post->ID);
     }
    }

    if (isset( $schema['properties']['categories'] )) {
      $categories = get_the_category($post->ID);

      foreach ($categories as $category){
        $trimmed_name = phila_get_department_homepage_typography( null, $return_stripped = true, $page_title = $category->name );

        $category->slang_name = $trimmed_name;
      }

      $post_data['categories']  = (array) $categories;
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
        'id' => array(
          'description'  => esc_html__( 'Unique identifier for the object.', 'phila-gov' ),
          'type'         => 'integer',
          'context'      => array( 'view', 'edit', 'embed' ),
          'readonly'     => true,
        ),
        'title'=> array(
          'description'  => esc_html__( 'Title of the object.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'template'  => array(
          'description' => esc_html__('The template this object is using.', 'phila-gov'),
          'type'  => 'string',
        ),
        'date'  => array(
          'description' => esc_html__('The date this object was published.', 'phila-gov'),
          'type'  => 'string',
        ),
        'link'  => array(
          'description' => esc_html__('The permalink for this object.', 'phila-gov'),
          'type'  => 'string',
        ),
        'categories'  => array(
          'description' => esc_html__('The categories assigned to this object.', 'phila-gov'),
          'type'  => 'array',
        ),
      ),
    );

    return $schema;
  }

}

// Function to register our new routes from the controller.
function phila_register_archives_rest_routes() {
  $controller = new Phila_Archives_Controller();
  $controller->register_routes();
}

add_action( 'rest_api_init', 'phila_register_archives_rest_routes' );

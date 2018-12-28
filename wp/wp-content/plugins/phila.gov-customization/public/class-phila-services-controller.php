<?php

class Phila_Services_Controller {

  // Initialize the namespace and resource name.
  public function __construct() {
    $this->namespace     = 'services/v1';
    $this->resource_name = 'directory';
    $this->category_resource = 'categories';
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
    //Register categories
    register_rest_route( $this->namespace, '/' . $this->category_resource, array(
      array(
        'methods'   => WP_REST_Server::READABLE,
        'callback'  => array( $this, 'get_categories' ),
      ),
      'schema' => array( $this, 'get_category_schema' ),
    ) );
  }

  /**
   * Return all services, excluding stubs
   *
   * @param WP_REST_Request $request Current request.
  */
  public function get_items( $request ) {

    $hidden_children = array(
      'post_type' => array('service_page'),
      'posts_per_page'  => -1,
      'order' => 'desc',
      'orderby' => 'date',
      'hierarchical' => 0,
      'meta_key' => 'phila_hide_children',
      'meta_value' => '1',
      'post_type' => 'service_page',
    );

    $hidden_pages = get_pages($hidden_children);

    $args = array(
        'post_type'  => 'service_page',
        'posts_per_page'  => -1,
        'order' => 'ASC',
        'orderby' => 'title',
        'post__not_in' => $hidden_pages,
        'meta_query' => array(
          'relation' => 'OR',
          array(
            'key'     => 'phila_template_select',
            'value' => array( 'service_stub' ),
            'compare' => 'NOT IN'
          ),
        ),
      );
    $posts = get_posts($args);

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
  * Outputs category data
  *
  * @param WP_REST_Request $request Current request.
  */
  public function get_categories( $request ){ 

    $service_categories = get_terms( 'service_type' );


    $data = array();

    if ( empty( $service_categories ) ) {
      return rest_ensure_response( $array() );
    }

    foreach ( $service_categories as $category ) {
      $response = $this->prepare_category_for_response( $category, $request );

      $data[] = $this ->prepare_response_for_collection( $response );
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

    if ( isset( $schema['properties']['id'] ) ) {
      $post_data['id'] = (int) $post->ID;
    }

    if (isset( $schema['properties']['title'] )) {
      if ( rwmb_meta('phila_service_alt_title', '', $post->ID) != null ) {
          $post_data['title'] =  (string) rwmb_meta('phila_service_alt_title', '', $post->ID);
        }else{
          $post_data['title']  =  (string) html_entity_decode($post->post_title);
      }
    }

    if (isset( $schema['properties']['link'] )) {
      $link = get_permalink($post->ID);
      $parsed_link = parse_url($link);
      $post_data['link']  = (string) $parsed_link['path'];

    }

    if (isset( $schema['properties']['categories'] )) {
      $service_categories = get_the_terms($post->ID, 'service_type');

      $post_data['categories']  = (array) $service_categories;
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
          'description' => esc_html__('The service categories assigned to this object.', 'phila-gov'),
          'type'  => 'array',
        ),
      ),
    );

    return $schema;
  }

    /**
   * Matches the post data to the schema. Also, rename the fields to nicer names.
   *
   * @param WP_Post $post The comment object whose response is being prepared.
   */

  public function prepare_category_for_response( $category, $request ) {

    $post_data = array();

    $schema = $this->get_category_schema( $request );

    if ( isset( $schema['properties']['id'] ) ) {
        $post_data['id'] = (int) $category->term_id;
    }

    if (isset( $schema['properties']['name'] )) {
      $post_data['name']  =  (string) html_entity_decode($category->name);
    }

    if (isset( $schema['properties']['slug'] )) {
      $post_data['slug']  =  (string) $category->slug;
    }

    return rest_ensure_response( $post_data );

  }

  /**
   * Get sample schema for a category.
   *
   * @param WP_REST_Request $request Current request.
   */
  public function get_category_schema( $request ) {

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
        'name'=> array(
          'description'  => esc_html__( 'Name of the object.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'slug'=> array(
          'description'  => esc_html__( 'Slug of the object.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
      ),
    );

    return $schema;

  }

}

// Function to register our new routes from the controller.
function phila_register_services_rest_routes() {
  $controller = new Phila_Services_Controller();
  $controller->register_routes();
}

add_action( 'rest_api_init', 'phila_register_services_rest_routes' );

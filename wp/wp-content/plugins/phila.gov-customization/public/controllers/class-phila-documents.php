<?php

class Phila_Publications_Controller {

  // Initialize the namespace and resource name.
  public function __construct() {
    $this->namespace     = 'publications/v1';
    $this->resource_name = 'archives';
    //$this->category_resource = 'categories';
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

    // //Register individual items
    // register_rest_route( $this->namespace, '/' . $this->category_resource, array(
    //   array(
    //     'methods'   => WP_REST_Server::READABLE,
    //     'callback'  => array( $this, 'get_categories' ),
    //   ),
    //   'schema' => array( $this, 'get_category_schema' ),
    // ) );
  }

  /**
   * Get the 40 latest posts within the "archives" umbrella
   *
   * @param WP_REST_Request $request Current request.
  */
  public function get_items( $request ) {

    $args = array(
      'post_type' => array('document'),
      'posts_per_page'  => $request['count'],
      'category'  => array($request['category']),
      's' => $request['s'],
      'order' => 'desc',
      'orderby' => 'date',
    );
    if ( isset( $request['start_date'] ) && isset( $request['end_date'] ) ){
      $date_query = array(
        'date_query' => array(
          array(
            'after'     => $request['start_date'],
            'before'    => $request['end_date']
            ),
            'inclusive' => true,
          ),
        );
      $args = array_merge($args, $date_query);
    }

    $department_pages = array(
      'post_type' => array('department_page'),
      'posts_per_page'  => -1,
      'meta_query' => array(
        array(
          'key'     => 'phila_template_select',
          'value'   => 'document_finder_v2',
          'compare' => '=',
        ),
      ),
      'category'  => array($request['category']),
      's' => $request['s'],
      'order' => 'desc',
      'orderby' => 'date',
    );

    if ( isset( $request['start_date'] ) && isset( $request['end_date'] ) ){
      $date_query = array(
        'date_query' => array(
          array(
            'after'     => $request['start_date'],
            'before'    => $request['end_date']
            ),
            'inclusive' => true,
          ),
        );
      $department_pages = array_merge($department_pages, $date_query);
    }

    $docs = get_posts( $args );

    $department_site = get_posts($department_pages);

    $posts = array_merge( $docs, $department_site );

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
      $post_data['title']  =  (string) html_entity_decode($post->post_title);
    }

    if (isset( $schema['properties']['template'] )) {
      $post_data['template']  = (string) phila_get_selected_template($post->ID);
    }

    if (isset( $schema['properties']['author'] )) {
      $post_data['author']  = (string) $post->author_name;
    }

    if (isset( $schema['properties']['date'] )) {
      $post_data['date']  = (string) $post->post_date;
    }

    if (isset( $schema['properties']['link'] )) {
      $link = get_permalink($post->ID);
      $parsed_link = parse_url($link);
      $post_data['link']  = (string) $parsed_link['path'];

    }

    if (isset( $schema['properties']['categories'] )) {
      $categories = get_the_category($post->ID);

      foreach ($categories as $category){
          $trimmed_name = phila_get_department_homepage_typography( null, $return_stripped = true, $page_title = $category->name );

          $category->slang_name = html_entity_decode(trim($trimmed_name));
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
function phila_register_publication_rest_routes() {
  $controller = new Phila_Publications_Controller();
  $controller->register_routes();
}

add_action( 'rest_api_init', 'phila_register_publication_rest_routes' );

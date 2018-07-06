<?php

class Phila_Programs_Controller {

  // Initialize the namespace and resource name.
  public function __construct() {
    $this->namespace     = 'programs/v1';
    $this->resource_name = 'archives';
    $this->service_resource = 'related_service';

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

    //Register individual items
    register_rest_route( $this->namespace, '/' . $this->service_resource, array(
      array(
        'methods'   => WP_REST_Server::READABLE,
        'callback'  => array( $this, 'get_services' ),
      ),
      'schema' => array( $this, 'get_services_schema' ),
    ) );
  }

  public function run_search_query($request){

    $search = sanitize_text_field( $request['s'] );
    $count = isset( $request['count'] ) ? $request['count'] : '20';
    $all_searches = array();

    if ( $request['s'] ) {
      $meta_query = array(
        'posts_per_page'=> $request['count'],
        'post_parent' => 0,
        'post_type' => 'programs',
        'orderby' => 'title',
        'order' => 'asc',
        'meta_query' => array(
          array(
            'key' => 'phila_meta_desc',
            'value' => $search,
            'compare' => 'LIKE'
          )
        )
      );

      $meta_results = get_posts( $meta_query );

      $search_query = array(
        'posts_per_page'=> $request['count'],
        'post_parent' => 0,
        'post_type' => 'programs',
        'orderby' => 'title',
        'order' => 'asc',
        's' => $request['s'],
      );

      $search_results = get_posts( $search_query );

      $all_searches = array_merge($search_results, $meta_results);
      $all_searches = array_unique($all_searches, SORT_REGULAR);
    }

    return array_filter($all_searches);

  }

  /**
   * Get Programs
   *
   * @param WP_REST_Request $request Current request.
  */
  public function get_items( $request ) {
    if ( !isset($request['s']) ) {
      $args = array(
        'posts_per_page'=> $request['count'],
        'post_parent' => 0,
        'post_type' => 'programs',
        'orderby' => 'title',
        'order' => 'asc',
        );

        if ( isset( $request['audience']) || isset($request['service_type']) ){
          $args = array(
            'posts_per_page'=> $request['count'],
            'post_parent' => 0,
            'post_type' => 'programs',
            'orderby' => 'title',
            'order' => 'asc',
            'tax_query' => array(
              'relation' => 'OR',
               array(
                   'taxonomy' => 'audience',
                   'field' => 'slug',
                   'terms' => $request['audience']
               ),
               array(
                   'taxonomy' => 'service_type',
                   'field' => 'slug',
                   'terms' => $request['service_type']
               )

             )
          );
        }
      $posts = get_posts( $args );

    }else{
      $posts = $this->run_search_query($request);
    }

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
   * Outputs service data
   *
   * @param WP_REST_Request $request Current request.
   */
   public function get_services( $request ){

     $args = array(
       's'  => $request['s'],
       'post_type' => 'service_page',
       'meta_query'  => array(
         array(
           'key' => 'display_prog_init',
           'value' => 1,
           'compare' => '='
           )
         ),
     );

     if ( isset( $request['audience']) || isset($request['service_type']) ){

      $args = array(
        'post_type' => 'service_page',
        's' => $request['s'],
        'tax_query' => array(
          'relation' => 'OR',
           array(
               'taxonomy' => 'audience',
               'field' => 'slug',
               'terms' => $request['audience']
           ),
           array(
               'taxonomy' => 'service_type',
               'field' => 'slug',
               'terms' => $request['service_type']
           ),
         ),
        'meta_query'  => array(
          array(
            'key' => 'display_prog_init',
            'value' => 1,
            'compare' => '='
            )
          ),
        );
      }

    $services = get_posts( $args );

    $data = array();

    if ( empty( $services ) ) {
      return rest_ensure_response( array() );
    }

    foreach ( $services as $service ) {
      $response = $this->prepare_service_for_response( $service, $request );

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

    if (isset( $schema['properties']['short_description'] )) {
      $short_desc = rwmb_meta( 'phila_meta_desc', array(), $post->ID );

      $post_data['short_description']  =  (string) html_entity_decode($short_desc);
    }

    if (isset( $schema['properties']['template'] )) {
      $post_data['template']  = (string) phila_get_selected_template($post->ID);
    }

    if (isset( $schema['properties']['external_link'] )) {
      $link = rwmb_meta( 'prog_off_site_link', array(), $post->ID );

      $post_data['external_link']  = (string) $link;
    }

    if (isset( $schema['properties']['link'] )) {
      $external_link = rwmb_meta( 'prog_off_site_link', array(), $post->ID );
      if( empty($external_link) ) {
        $link = get_permalink($post->ID);
        $parsed_link = parse_url($link);
        $post_data['link']  = (string) $parsed_link['path'];
      }else{
        $post_data['link']  = (string) $external_link;
      }
    }

    if (isset( $schema['properties']['categories'] )) {
      $categories = get_the_category($post->ID);

      foreach ($categories as $category){
          $trimmed_name = phila_get_department_homepage_typography( null, $return_stripped = true, $page_title = $category->name );

          $category->slang_name = html_entity_decode(trim($trimmed_name));
      }

      $post_data['categories']  = (array) $categories;
    }

    if (isset( $schema['properties']['audiences'] )) {
      $audiences = get_the_terms($post->ID, 'audience');

      $post_data['audiences']  = (array) $audiences;
    }

    if (isset( $schema['properties']['services'] )) {
      $services = get_the_terms($post->ID, 'service_type');

      $post_data['services']  = (array) $services;
    }

    if (isset( $schema['properties']['image'] )) {
      $img = rwmb_meta( 'prog_header_img', array( 'limit' => 1 ), $post->ID );
      $img = reset($img);
      $medium_image = str_replace('.jpg', '-700x400.jpg', $img['full_url']);

      if( !isset( $img['sizes']['medium'] ) ){
        $medium_image = $img['full_url'];
      }

      $post_data['image']  = (string) $medium_image;
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
        'short_description'=> array(
          'description'  => esc_html__( 'Short description.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'template'  => array(
          'description' => esc_html__('The template this object is using.', 'phila-gov'),
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
        'audiences'  => array(
          'description' => esc_html__('The audience taxonomy assigned to this object.', 'phila-gov'),
          'type'  => 'array',
        ),
        'services'  => array(
          'description' => esc_html__('The service category assigned to this object.', 'phila-gov'),
          'type'  => 'array',
        ),
        'image'  => array(
          'description' => esc_html__('The medium size image associated with this program.', 'phila-gov'),
          'type'  => 'string',
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

  public function prepare_service_for_response( $service, $request ) {

    $post_data = array();

    $schema = $this->get_service_schema( $request );

    if ( isset( $schema['properties']['id'] ) ) {
        $post_data['id'] = (int) $service->ID;
    }

    if (isset( $schema['properties']['name'] )) {
      $post_data['name']  =  (string) html_entity_decode($service->post_title);
    }

    if (isset( $schema['properties']['link'] )) {
      $link = get_permalink($service->ID);
      $parsed_url = parse_url($link);
      $post_data['link']  =  (string)  $parsed_url['path'];
    }

    if (isset( $schema['properties']['short_description'] )) {

      $short_desc = rwmb_meta( 'phila_meta_desc', array(), $service->ID );

      $post_data['short_description']  =  (string) html_entity_decode($short_desc);

    }
    if (isset( $schema['properties']['audiences'] )) {
      $audiences = get_the_terms($service->ID, 'audience');

      $post_data['audiences']  = (array) $audiences;
    }

    if (isset( $schema['properties']['service_type'] )) {
      $services = get_the_terms($service->ID, 'service_type');

      $post_data['service_type']  = (array) $services;
    }

    return rest_ensure_response( $post_data );

  }

  /**
   * Get sample schema for a service.
   *
   * @param WP_REST_Request $request Current request.
   */
  public function get_service_schema( $request ) {

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
        'name' => array(
          'description'  => esc_html__( 'Name of the object.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'link' => array(
          'description'  => esc_html__( 'Link to the object.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'short_description' => array(
          'description'  => esc_html__( 'Short description.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'audiences'  => array(
          'description' => esc_html__('The audience taxonomy assigned to this object.', 'phila-gov'),
          'type'  => 'array',
        ),
        'service_type'  => array(
          'description' => esc_html__('The service category assigned to this object.', 'phila-gov'),
          'type'  => 'array',
        ),
      ),
    );

    return $schema;

  }

}

// Function to register our new routes from the controller.
function phila_register_programs_rest_routes() {
  $controller = new Phila_Programs_Controller();
  $controller->register_routes();
}

add_action( 'rest_api_init', 'phila_register_programs_rest_routes' );

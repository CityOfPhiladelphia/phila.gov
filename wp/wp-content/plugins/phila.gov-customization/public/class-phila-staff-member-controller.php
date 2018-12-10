<?php

class Phila_Staff_Member_Controller {

  // Initialize the namespace and resource name.
  public function __construct() {
    $this->namespace     = 'staff_directory/v1';
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

    register_rest_route( $this->namespace, '/' . $this->resource_name, array(
      array(
        'methods'   => WP_REST_Server::CREATABLE,
        'callback'  => array( $this, 'create_item' ),
      ),
      'schema' => array( $this, 'get_item_schema' ),
    ) );

    //Register individual items
    register_rest_route( $this->namespace, '/' . $this->resource_name . '/(?P<id>[\d]+)', array(
      array(
        'methods'   => WP_REST_Server::ALLMETHODS,
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

    $args = array(
      'posts_per_page'=> -1,
      'post_type' => array('staff_directory'),
      'orderby' => 'modified',
      'order' => 'desc',
    );

    $posts = get_posts( $args );

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


  public function create_item( $request ) {
    
    $response = new WP_REST_Response();
    $params = $request->get_params();
    $msgs = [];

    if ( isset( $params['postmeta'] ) && is_array( $params['postmeta'] ) ) {
      
      $msgs[] = 'postmeta = isset and is_array';

      foreach ( $params['postmeta'] as $postmeta ) {
        
        $msgs[] = 'inside foreach';

        $result = wp_insert_post(array(
          'post_type' => 'staff_directory',
          'meta_input' => $postmeta
        ));

        if(!is_wp_error( $result ) ) {

          $response->set_status( 201 );
          $msgs[] = 'added post_id ' . $result;

        }else{

          $response->set_status( 400 );

          $msgs[] = array(
            'error message: ' => 'failed with error',
            'error: ' => $result
          );

        }
      }
    }

  $response->set_data(['messages' => $msgs]);

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
    $post_data = array();

    $schema = $this->get_item_schema( $request );

    if ( isset( $schema['properties']['id'] ) ) {
        $post_data['id'] = (int) $post->ID;
    }

    if (isset( $schema['properties']['title'] )) {
      $post_data['title']  =  (string) html_entity_decode($post->post_title);
    }

    if (isset( $schema['properties']['first_name'] )) {
      $post_data['first_name']  = (string) rwmb_meta('phila_first_name', '', $post->ID);
    }
    if (isset( $schema['properties']['middle_name'] )) {
      $post_data['middle_name']  = (string) rwmb_meta('phila_middle_name', '', $post->ID);
    }
    if (isset( $schema['properties']['last_name'] )) {
      $post_data['last_name']  = (string) rwmb_meta('phila_last_name', '', $post->ID);
    }

    if (isset( $schema['properties']['name_suffix'] )) {
      $post_data['name_suffix']  = (string) rwmb_meta('phila_name_suffix', '', $post->ID);
    }

    if (isset( $schema['properties']['job_title'] )) {
      $post_data['job_title']  = (string) rwmb_meta('phila_job_title', '', $post->ID);
    }

    if (isset( $schema['properties']['email'] )) {
      $post_data['email']  = (string) rwmb_meta('phila_email', '', $post->ID);
    }

    if (isset( $schema['properties']['phone'] )) {
      $post_data['phone']  = (array) rwmb_meta('phila_phone', '', $post->ID);
    }

    if (isset( $schema['properties']['social'] )) {
      $post_data['social']  = (array) rwmb_meta('phila_staff_social', '', $post->ID);
    }

    if (isset( $schema['properties']['leadership'] )) {
      $post_data['leadership']  = (array) rwmb_meta('phila_leadership', '', $post->ID);
    }
    if (isset( $schema['properties']['leadership_details'] )) {
      $post_data['leadership_details']  = (array) rwmb_meta('phila_leadership_options', '', $post->ID);
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
        'categories' => array(
          'description'  => esc_html__( 'Name of the object.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'first_name' => array(
          'description'  => esc_html__( 'First name.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'middle_name' => array(
          'description'  => esc_html__( 'Middle name.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'last_name' => array(
          'description'  => esc_html__( 'Last name.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'name_suffix' => array(
          'description'  => esc_html__( 'Last name.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'job_title' => array(
          'description'  => esc_html__( 'Last name.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'email' => array(
          'description'  => esc_html__( 'Last name.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'phone' => array(
          'description'  => esc_html__( 'Last name.', 'phila-gov' ),
          'type'         => 'array',
          'readonly'     => true,
        ),
        'social' => array(
          'description'  => esc_html__( 'Last name.', 'phila-gov' ),
          'type'         => 'array',
          'readonly'     => true,
        ),
        'leadership' => array(
          'description'  => esc_html__( 'Last name.', 'phila-gov' ),
          'type'         => 'array',
          'readonly'     => true,
        ),
        'leadership_details' => array(
          'description'  => esc_html__( 'Last name.', 'phila-gov' ),
          'type'         => 'array',
          'readonly'     => true,
        ),
      ),
    );

    return $schema;
  }

}

// Function to register our new routes from the controller.
function phila_register_staff_member_rest_routes() {
  $controller = new Phila_Staff_Member_Controller();
  $controller->register_routes();
}

add_action( 'rest_api_init', 'phila_register_staff_member_rest_routes' );

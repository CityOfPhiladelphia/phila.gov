<?php

class Phila_Departments_Controller {

  // Initialize the namespace and resource name.
  public function __construct() {
    $this->namespace     = 'departments/v1';
    $this->resource_name = 'list';
    $this->owner_resource_name = 'owners';
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

    // Register the endpoint for collections.
    register_rest_route( $this->namespace, '/' . $this->owner_resource_name, array(
      array(
        'methods'   => WP_REST_Server::READABLE,
        'callback'  => array( $this, 'get_owner_items' ),
        'permission_callback' => '__return_true',
      ),
      'schema' => array( $this, 'get_owner_item_schema' ),
    ) );
  }

  /**
   * Return all departments
   *
   * @param WP_REST_Request $request Current request.
  */
  public function get_items( $request ) {

    $regular_homepages = array(
      'posts_per_page'=> -1,
      'post_type' => 'department_page',
      'orderby' => 'title',
      'order' => 'asc',
      'post_status' => 'publish',
      'post_parent' => 0,
    );

    $non_homepages = array(
      'posts_per_page'=> -1,
      'post_type' => 'department_page',
      'orderby' => 'title',
      'order' => 'asc',
      'post_status' => 'publish',
      'meta_key' => 'phila_department_home_page',
      'meta_value' => 1,
    );

    $home = get_posts( $regular_homepages );
    $non = get_posts($non_homepages);

    $posts = array_merge($home, $non);

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
   * Return all departments
   *
   * @param WP_REST_Request $request Current request.
  */
  public function get_owner_items() {

    $categories = get_categories();

    $data = array();

    if ( empty( $categories ) ) {
      return rest_ensure_response( $data );
    }
    return rest_ensure_response( $categories );
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

   // fetch connect box information and description from metaboxes
    $connect_panel = rwmb_meta('module_row_1_col_2_connect_panel', array(), $post->ID);
    $phila_row = rwmb_meta('phila_row', array(), $post->ID);
    $connect_info = phila_connect_panel_from_phila_row($phila_row) ? phila_connect_panel_from_phila_row($phila_row) : phila_connect_panel($connect_panel);
    $description = rwmb_meta( 'phila_meta_desc', '', $post->ID );

    if (isset( $schema['properties']['name'] )) {

      $post_data['name'] = (string) $post->post_title;
    }

    if (isset( $schema['properties']['short_name'] )) {
      $trimmed_name = '';
      $categories = get_the_category($post->ID);
      foreach ($categories as $category){
        $trimmed_name = phila_get_owner_typography( $category );
      }
      if ($trimmed_name == '') {
        $trimmed_name = phila_shorten_department_name($post->post_title);
      }
      $trimmed_name = preg_replace('/( & )/', ' and ', $trimmed_name);  
      $trimmed_name = trim( $trimmed_name );
      $post_data['short_name'] = (string) $trimmed_name;
    }

    if (isset( $schema['properties']['acronym'] )) {
      $acronym = rwmb_meta('phila_department_acronym', array(), $post->ID);
      $post_data['acronym'] = (string) $acronym;
    }

    if (isset( $schema['properties']['email'] )) {
      $post_data['email'] = (string) $connect_info['email'];
    }  

    if (isset( $schema['properties']['instagram']) && $connect_info['social'] && isset($connect_info['social']['instagram'])) {
      $post_data['instagram'] = (string) $connect_info['social']['instagram'];
    } else {
      $post_data['instagram'] = '';
    }

    if (isset( $schema['properties']['facebook']) && $connect_info['social'] && isset($connect_info['social']['facebook'])) {
      $post_data['facebook'] = (string) $connect_info['social']['facebook'];
    } else {
      $post_data['facebook'] = '';
    }

    if (isset( $schema['properties']['twitter']) &&  $connect_info['social'] && isset($connect_info['social']['twitter'])) {
      $post_data['twitter'] = (string) $connect_info['social']['twitter'];
    } else {
      $post_data['twitter'] = '';
    }

    // checks to see if there is a phone number by checking to see if the are code is empty
    // if the area code is empty, it will populate with the co-code, which will either be 311 or null
    if (isset( $schema['properties']['phone'] ) && !empty($connect_info['phone']['area'])) {
      $post_data['phone'] = $connect_info['phone'];
    } else {
      $post_data['phone'] = (string) $connect_info['phone']['co-code'];
    }

    //Need to check if phone_multi isset , and if so, then add the array 
    if ( isset( $connect_info['phone_multi'] ) ) {
      $post_data['phone_multi'] = $connect_info['phone_multi'];
    }

    if (isset( $schema['properties']['tty'] )) {
      $post_data['tty'] = (string) $connect_info['tty'];
    }

    if (isset( $schema['properties']['fax'] )) {
      $post_data['fax'] = (string) $connect_info['fax'];
    }

    if (isset( $schema['properties']['description'] )) {
      $post_data['description'] = (string) $description;
    }

    if (isset( $schema['properties']['permalink'] )) {
      $permalink = get_permalink($post->ID);
      $parsed_link = parse_url($permalink);
      $post_data['permalink'] = (string) $parsed_link['path'];
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
        'name' => array(
          'description'  => esc_html__( 'Title of the object.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'short_name' => array(
          'description'  => esc_html__( 'Department short name.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'acronym' => array(
          'description'  => esc_html__( 'Acronym of the department.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'email' => array(
          'description'  => esc_html__( 'email of the department.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'instagram' => array(
          'description'  => esc_html__( 'instagram of the department.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'facebook' => array(
          'description'  => esc_html__( 'facebook of the department.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'twitter' => array(
          'description'  => esc_html__( 'twitter of the department.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'tty' => array(
          'description'  => esc_html__( 'tty of the department.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'fax' => array(
          'description'  => esc_html__( 'fax of the department.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'phone' => array(
          'description'  => esc_html__( 'phone of the department.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'phone_multi' => array(
          'description'  => esc_html__( 'Extra phones of the department.', 'phila-gov' ),
          'type'         => 'array',
          'readonly'     => true,
        ),
        'description' => array(
          'description'  => esc_html__( 'Description of the Department.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'permalink' => array(
          'description'  => esc_html__( 'Permalnk of the Department.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
      ),
    );

    return $schema;
  }

}

// Function to register our new routes from the controller.
function phila_register_department_rest_routes() {
  $controller = new Phila_Departments_Controller();
  $controller->register_routes();
}

add_action( 'rest_api_init', 'phila_register_department_rest_routes' );

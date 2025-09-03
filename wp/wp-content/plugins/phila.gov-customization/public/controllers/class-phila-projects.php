<?php

class Phila_Projects_Controller {

  // Initialize the namespace and resource name.
  public function __construct() {
    $this->namespace     = 'projects/v1';
    $this->resource_name = 'project-status';
  }

  // Register our routes.
  public function register_routes() {
    register_rest_route( $this->namespace, '/' . $this->resource_name, array(
      array(
        'methods'   => WP_REST_Server::READABLE,
        'callback'  => array( $this, 'get_items' ),
        'permission_callback' => '__return_true',
      ),
      'schema' => array( $this, 'get_item_schema' ),
    ) );
  }

  /**
   * Return all projects with status info
   *
   * @param WP_REST_Request $request Current request.
  */
  public function get_items( $request ) {

    // Query only posts of type 'project'
    $query_args = array(
      'post_type'      => 'project',
      'posts_per_page' => -1,
      'post_status'    => 'publish',
      'order'          => 'ASC',
      'orderby'        => 'title',
    );

    $projects = new WP_Query( $query_args );

    $data = array();

    if ( $projects->have_posts() ) {
      while ( $projects->have_posts() ) {
        $projects->the_post();
        $id = get_the_ID();

        // Get meta values
        $status_description = rwmb_meta( 'phila_project_status_description', array(), $id );
        $status_value = rwmb_meta( 'phila_project_status', array(), $id );

        // Prepare response object
        $project = new stdClass();
        $project->id = $id;
        $project->status_description = $status_description;
        $project->status = (int) $status_value;

        $response = $this->prepare_item_for_response( $project, $request );
        $data[] = $this->prepare_response_for_collection( $response );
      }
      wp_reset_postdata();
    }

    return rest_ensure_response( $data );
  }

  /**
   * Format the data to return.
   */
  public function prepare_item_for_response( $project, $request ) {
    $post_data = array();

    $post_data['id'] = (int) $project->id;
    $post_data['status_description'] = (string) $project->status_description;
    $post_data['status'] = (int) $project->status;

    return rest_ensure_response( $post_data );
  }

  /**
   * Prepare response for collection.
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
   * Get schema for the returned data.
   */
  public function get_item_schema( $request ) {
    $schema = array(
      '$schema'    => 'http://json-schema.org/draft-04/schema#',
      'title'      => 'project_status',
      'type'       => 'object',
      'properties' => array(
        'id' => array(
          'description' => __( 'Project ID', 'text-domain' ),
          'type'        => 'integer',
          'readonly'    => true,
        ),
        'status_description' => array(
          'description' => __( 'Project status description', 'text-domain' ),
          'type'        => 'string',
          'readonly'    => true,
        ),
        'status' => array(
          'description' => __( 'Project status value', 'text-domain' ),
          'type'        => 'integer',
          'readonly'    => true,
        ),
      ),
    );

    return $schema;
  }

}

// Register routes on init
function phila_register_projects_rest_routes() {
  $controller = new Phila_Projects_Controller();
  $controller->register_routes();
}
add_action( 'rest_api_init', 'phila_register_projects_rest_routes' );

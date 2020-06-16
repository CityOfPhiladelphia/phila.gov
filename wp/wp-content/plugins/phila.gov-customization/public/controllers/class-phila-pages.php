<?php

class Phila_Pages_Controller {

  // Initialize the namespace and resource name.
  public function __construct() {
    $this->namespace     = 'pages/v1';
    $this->resource_name = 'page';
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
  }

  /**
   * Return all pages
   *
   * @param WP_REST_Request $request Current request.
  */
  public function get_items( $request ) {

    $posts = new WP_Query( array(
      // 'post_type' => array('department_page', 'programs'),
      'posts_per_page'         => 10,
      // 'posts_per_page'  => -1,
      'order' => 'asc',
      'orderby' => 'title',
      'post_status' => 'any',
    ) );

    if ( $posts->have_posts() ) {
      $pages = '{"pages": [';
      while ( $posts->have_posts() ) {
        $posts->the_post();

        $terms = get_the_terms( get_the_id(), 'category' ); 
        if ($terms) {
          $numTerms = count($terms);
          $i = 0;
          foreach($terms as $term) {
            $pages .= '{ "id": '. the_id() .',';
            $pages .= '"title": "'. get_the_title() .'",';
            $pages .= '"url": "'. get_the_permalink() .'",';
            $pages .= '"owner": "'. $term->name .'",';
            $pages .= '"status": "'. get_post_status() .'",';
            $pages .= '"last_modified": "'. the_modified_time('F jS, Y') . ', ' . the_modified_time() . '",';
            if (get_userdata( rwmb_meta( '_edit_last', $args = array(), $post_id = get_the_id() ) )) {
              $pages .= '"last_modified_user": "'. get_userdata( rwmb_meta( '_edit_last', $args = array(), $post_id = get_the_id() ) )->user_email .'",';
            }
            else {
              $pages .= '"last_modified_user": "",';
            }
            $pages .= '"post_type": "'. get_post_type_object(get_post_type())->labels->singular_name .'",';
            $pages .= '"short_description": "'. rwmb_meta( 'phila_meta_desc', $args = array(), $post_id = get_the_id() ) .'",';
            $pages .= '"template": "'. rwmb_meta( 'phila_template_select', $args = array(), $post_id = get_the_id() ) .'",';
            if ($posts->current_post +1 == $posts->post_count && ++$i === $numTerms) {
              $pages .= '}';
            }
            else {
              $pages .= '},';
            }
          }
        }
      }
      $pages .= ']}';
      wp_reset_postdata();
    }

    if ( $pages != '' ) {
      return $pages;
    }
    else {
      return '';
    }
  }

  /**
   * Matches the post data to the schema. Also, rename the fields to nicer names.
   *
   * @param WP_Post $post The comment object whose response is being prepared.
   */

  public function prepare_item_for_response( $post, $request ) {
    $post_data = array();

    $schema = $this->get_item_schema( $request );

    $post_data['page_label'] = (string) $post['page_label'] ?? '';

    $post_data['start_date']  = (string) $post['start_date'] ?? '';

    $post_data['end_date'] = (string) $post['end_date'] ?? '';

    $post_data['is_recycling_biweekly'] = array_key_exists('is_recycling_biweekly', $post) ? (boolean) $post['is_recycling_biweekly'] : false;

    $post_data['is_active'] = array_key_exists('is_active', $post) ? (boolean) $post['is_active'] : false;

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
        'page_label'=> array(
          'description'  => esc_html__( 'Label of the object.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'start_date'  => array(
          'description' => esc_html__('The start date for this object.', 'phila-gov'),
          'type'  => 'date',
          'readonly'     => true,
        ),
        'end_date'  => array(
          'description' => esc_html__('The end date for this object.', 'phila-gov'),
          'type'  => 'date',
          'readonly'     => true,
        ),
        'is_recycling_biweekly'  => array(
          'description' => esc_html__('Is recycling bikweekly in this duration?', 'phila-gov'),
          'type'  => 'boolean',
          'readonly'     => true,
        ),
        'is_active'  => array(
          'description' => esc_html__('Is this page active?', 'phila-gov'),
          'type'  => 'boolean',
          'readonly'     => true,
        ),
      ),
    );

    return $schema;
  }

}

// Function to register our new routes from the controller.
function phila_register_pages_rest_routes() {
  $controller = new Phila_Pages_Controller();
  $controller->register_routes();
}

add_action( 'rest_api_init', 'phila_register_pages_rest_routes' );
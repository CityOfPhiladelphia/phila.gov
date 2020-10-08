<?php

class Phila_Calendars_Controller {

  // Initialize the namespace and resource name.
  public function __construct() {
    $this->namespace     = 'calendars/v1';
    $this->resource_name = 'list';

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
  }

  /**
   * Return all calendars, excluding stubs
   *
   * @param WP_REST_Request $request Current request.
  */
  public function get_items( $request ) {

    $cal_a = array(
      'post_type' => 'calendar',
      'posts_per_page'  => -1,
      'post_status' => 'any',
      'tax_query' => array(
        'relation' => 'OR',
        array(
            'taxonomy' => 'calendar_category',
            'field'    => 'slug',
            'terms'    => array( 'all-events' ),
        ),
      )
    );

    $calendar_q = new WP_Query( $cal_a );
    
    if ( $calendar_q->have_posts() ) {
      $calendars = array();
    
      while ( $calendar_q->have_posts() ) : $calendar_q->the_post();
        $post_id = get_the_id();
        $categories = get_the_category( $post_id );
        $cal_url  = rwmb_meta('calendar_url', '', $post_id);
        $post_title =  preg_replace("/Private: /", "", get_the_title( $post_id ));
    
        if ($categories != null) {
          $category = $categories[0];
          $calendar = base64_decode(get_post_meta( $post_id, '_google_calendar_id', true ) );

          if (is_array( get_post_meta( $post_id, '_grouped_calendars_ids', true  ) )) {
            $grouped_calendars = get_post_meta( $post_id, '_grouped_calendars_ids', true  );
            $temp_cals = array();
            foreach ($grouped_calendars as $cal_id) {
  
              $calendars_categories = get_the_category( $cal_id );
              $calendars_category = $calendars_categories[0];
              $cals_url  = rwmb_meta('calendar_url', '', $cal_id);
              $cals_title =  preg_replace("/Private: /", "", get_the_title( $cal_id ));

              array_push($temp_cals, (object)['post_title' => html_entity_decode($cals_title), 'url' => $cals_url, 'category_slug' => $calendars_category->slug, 'calendar' => base64_decode( get_post_meta($cal_id, '_google_calendar_id', true) ) ]);
            }

            $calendar = $temp_cals;
          }
          array_push($calendars, (object)['post_title' => html_entity_decode($post_title), 'url' => $cal_url, 'category_slug' => $category->slug, 'calendar' => $calendar]);
        }

      endwhile;

      wp_reset_postdata();
    }

    if ( empty( json_encode($calendars) ) ) {
      return rest_ensure_response($calendars);
    }
    // Return all response data.
    return rest_ensure_response(  $calendars );
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
        'department_slug' => array(
          'description'  => esc_html__( 'Title of the object.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'calendar_id' => array(
          'description'  => esc_html__( 'Department short name.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
        'owner' => array(
          'description'  => esc_html__( 'Acronym of the object.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
        ),
      ),
    );

    return $schema;
  }

}

// Function to register our new routes from the controller.
function phila_register_calendar_rest_routes() {
  $controller = new Phila_Calendars_Controller();
  $controller->register_routes();
}

add_action( 'rest_api_init', 'phila_register_calendar_rest_routes' );

<?php

class Phila_Archives_Controller {

  // Initialize the namespace and resource name.
  public function __construct() {
    $this->namespace     = 'the-latest/v1';
    $this->resource_name = 'archives';
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

    //Register individual items
    register_rest_route( $this->namespace, '/' . $this->category_resource, array(
      array(
        'methods'   => WP_REST_Server::READABLE,
        'callback'  => array( $this, 'get_categories' ),
      ),
      'schema' => array( $this, 'get_category_schema' ),
    ) );
  }

  public function set_query_defaults($request){

    $search = sanitize_text_field( $request['s'] );

    $args = array(
      'count_total' => false,
      'search' => sprintf( '*%s*', $search ),
      'search_fields' => array(
        'display_name',
        'user_login',
      ),
      'fields' => 'ID',
    );

    $matching_users = get_users( $args );

    $matching_tag = get_term_by( $field = 'name', $value = $request['tag'], $taxonomy = 'post_tag' );

    if ( $matching_tag != false ) {
      $query_defaults = array(
        'posts_per_page' => $request['count'],
        'order' => 'desc',
        'orderby' => 'date',
        'tax_query' => array(
          array(
            'taxonomy' => 'post_tag',
            'field'    => 'slug',
            'terms'    => $matching_tag->slug,
          ),
        ),
      );
    }elseif(!empty( $matching_users )) {
      $query_defaults = array(
        'posts_per_page' => $request['count'],
        'author__in' => $matching_users,
        'order' => 'desc',
        'orderby' => 'date',
        'category' => $request['category'],
      );
    }else{ //default
      $query_defaults = array(
        'posts_per_page' => $request['count'],
        's' => $request['s'],
        'order' => 'desc',
        'orderby' => 'date',
        'category' => $request['category'],
      );
    }

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
      return $query_defaults = array_merge($query_defaults, $date_query);
    }
    return $query_defaults;
  }

  /**
   * Get the 40 latest posts within the "archives" umbrella
   *
   * @param WP_REST_Request $request Current request.
  */
  public function get_items( $request ) {
    $post_type = isset( $request['post_type'] ) ? array( $request['post_type']) : array('post', 'phila_post', 'press_release', 'news_post');

    $count = isset( $request['count'] ) ? $request['count'] : '40';

    if ( isset( $request['template'] ) ) {
      $template = $request['template'] ;
      switch($template) {
        case 'featured':
          $posts_args = array(
            'post_type' => array('post', 'news_post'),
            'meta_query'  => array(
              'relation'  => 'OR',
              array(
                'key' => 'phila_show_on_home',
                'value' => '1',
                'compare' => '=',
              ),
              array(
                'relation'  => 'AND',
                array(
                  'key' => 'phila_is_feature',
                  'value' => '1',
                  'compare' => '=',
                ),
              ),
            ),
          );
          $query_defaults = $this->set_query_defaults($request);
          $full_query = array_merge($query_defaults, $posts_args);
          $posts = get_posts( $full_query );
          break;
        case 'post':
          $old_args = array(
            'post_type' => array('phila_post'),
          );
          $new_args = array(
            'post_type' => array('post'),
            'meta_query'  => array(
                array(
                  'key' => 'phila_template_select',
                  'value' => 'post',
                  'compare' => '=',
              ),
            ),
          );
          // special handling for old phila_post CPT
          $query_defaults_old = $this->set_query_defaults($request);
          $full_query_old = array_merge($query_defaults_old, $old_args);

          $query_defaults_new = $this->set_query_defaults($request);
          $full_query_new = array_merge($query_defaults_new, $new_args);

          $posts_old = get_posts( $full_query_old );
          $posts_new = get_posts( $full_query_new );

          $posts = array();
          $posts = array_merge( $posts_new, $posts_old );
          break;

        case 'press_release' :
          $old_args  = array(
            'post_type' => array( 'press_release' ),
          );
          $new_args  = array(
            'post_type' => array( 'post' ),
            'meta_query'  => array(
              array(
                'key' => 'phila_template_select',
                'value' => 'press_release',
                'compare' => '=',
              ),
            ),
          );

          $query_defaults_old = $this->set_query_defaults($request);
          $full_query_old = array_merge($query_defaults_old, $old_args);

          $query_defaults_new = $this->set_query_defaults($request);
          $full_query_new = array_merge($query_defaults_new, $new_args);

          $press_old = get_posts( $full_query_old );
          $press_new = get_posts( $full_query_new );

          $posts = array();
          $posts = array_merge( $press_new, $press_old );
        break;
        case 'action_guide':
          $ac_arg = array(
            'post_type' => array('post'),
            'meta_query'  => array(
              array(
                'key' => 'phila_template_select',
                'value' => 'action_guide',
                'compare' => '=',
              ),
            ),
          );
          $query_defaults = $this->set_query_defaults($request);
          $full_query = array_merge($query_defaults, $ac_arg);
          $posts = get_posts( $full_query );
      }
    }else{
      $args = array(
        'post_type' => $post_type,
      );
      $query_defaults = $this->set_query_defaults($request);
      $args = array_merge($query_defaults, $args);

      $posts = get_posts( $args );
    }

    $data = array();

    if ( empty( $posts ) ) {
      return rest_ensure_response( $data );
    }

    foreach ( $posts as $post ) {
      $response = $this->prepare_item_for_response( $post, $request );

      $data[] = $this ->prepare_response_for_collection( $response );
    }

    // Return all response data.
    return rest_ensure_response( $data );
  }

  /**
   * Outputs category data
   *
   * @param WP_REST_Request $request Current request.
   */
   public function get_categories( $request ){

    $categories = get_categories( array( 'parent' => 0 ) );

    $data = array();

    if ( empty( $categories ) ) {
      return rest_ensure_response( $array() );
    }

    foreach ( $categories as $category ) {
      $response = $this->prepare_category_for_response( $category, $request );

      $data[] = $this ->prepare_response_for_collection( $response );
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

    if (isset( $schema['properties']['slang_name'] )) {

      $trimmed_name = phila_get_department_homepage_typography( null, $return_stripped = true, $page_title = $category->name );

      $post_data['slang_name']  = (string) html_entity_decode(trim($trimmed_name));
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
        'slang_name'=> array(
          'description'  => esc_html__( 'Slang name of the object.', 'phila-gov' ),
          'type'         => 'string',
          'readonly'     => true,
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

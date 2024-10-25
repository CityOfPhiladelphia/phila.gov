<?php


class Phila_Featured_Controller {

  // Initialize the namespace and resource name.
  public function __construct() {
    $this->namespace     = 'news/v1';
    $this->resource_name = 'featured';
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

  }

  /**
   * Get the top 3 latest posts within the "featured" umbrella
   *
   * @param WP_REST_Request $request Current request.
  */
  public function get_items( $request ) {

    $posts_args = array(
      'post_type' => array('post'),
      'posts_per_page'  => 3,
      'meta_query'  => array(
          array(
            'key' => 'phila_is_feature',
            'value' => '1',
            'compare' => '=',
          ),
        ),
    );

    $posts = get_posts( $posts_args );

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

    if (isset( $schema['properties']['featured_image'] )) {
      $featured_image_id = get_post_thumbnail_id($post);
      if (wp_get_attachment_image_src($featured_image_id)) {
        $medium_featured_image_url = wp_get_attachment_image_src($featured_image_id, 'medium');
        $post_data['featured_image']  = (string) $medium_featured_image_url[0];
      }
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

        $new_url = str_replace( $pattern, $replacement, $url );
        $parsed_link = parse_url($new_url);

        $post_data['link']  = (string) $parsed_link['path'];
      }else{
        $link = get_permalink($post->ID);
        $parsed_link = parse_url($link);
        $post_data['link']  = (string) $parsed_link['path'] ;
      }
    }

    if (isset( $schema['properties']['tags'] )) {
      $tags = get_the_tags($post->ID);

      $post_data['tags']  = (array) $tags;
    }

    if (isset( $schema['properties']['language'] )) {
      $language = rwmb_meta('phila_select_language', '', $post->ID);
      if ( empty( $language ) ) { # set the default lang to account for items made before this feature existed.
        $language = 'english';
      }
      $post_data['language']  = (string) $language;
    }

    if (isset( $schema['properties']['updated_at'] )) {
      $post_data['updated_at']  = get_the_modified_date('Y-m-d', $post->ID);
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
        'featured_image'  => array(
          'description' => esc_html__('The featured image for this object.', 'phila-gov'),
          'type'  => 'string',
        ),
        'language'  => array(
          'description' => esc_html__('The language this post is in.', 'phila-gov'),
          'type'  => 'string',
        ),
        'tags'  => array(
          'description' => esc_html__('The tags assigned to this object.', 'phila-gov'),
          'type'  => 'array',
        ),
        'categories'  => array(
          'description' => esc_html__('The categories assigned to this object.', 'phila-gov'),
          'type'  => 'array',
        ),
        'archived'  => array(
          'description' => esc_html__('The archive status of a post', 'phila-gov'),
          'type'  => 'bool',
        ),
        'updated_at' => array(
          'description'  => esc_html__( 'Last updated time.', 'phila-gov' ),
          'type'         => 'date',
          'readonly'     => true,
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

      $trimmed_name = phila_get_owner_typography( $category );

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
function phila_register_featured_rest_routes() {
  $controller = new Phila_Featured_Controller();
  $controller->register_routes();
}

add_action( 'rest_api_init', 'phila_register_featured_rest_routes' );

<?php

class Phila_Media_Library_Report_Controller
{

  // Initialize the namespace and resource name.
  public function __construct()
  {
    $this->namespace     = 'media-library-report/v1';
    $this->resource_name = 'all';
  }

  // Register our routes.
  public function register_routes()
  {
    // Register the endpoint for collections.
    register_rest_route($this->namespace, '/' . $this->resource_name, array(
      array(
        'methods'   => WP_REST_Server::READABLE,
        'callback'  => array($this, 'get_items'),
        'permission_callback' => '__return_true',
      ),
      'schema' => array($this, 'get_item_schema'),
    ));

    //Register individual items
    register_rest_route($this->namespace, '/' . $this->resource_name . '/(?P<id>[\d]+)', array(
      array(
        'methods'   => WP_REST_Server::READABLE,
        'callback'  => array($this, 'get_item'),
        'permission_callback' => '__return_true',
      ),
      'schema' => array($this, 'get_item_schema'),
    ));
  }

  /**
   * Get all public Post types
   *
   * @param WP_REST_Request $request Current request.
   */
  public function get_items($request)
  {
    // Only get posts of type 'attachment' (media items in WordPress)
    $post_types = array('attachment');

    $args = array(
      'posts_per_page' => -1, // -1 means no limit, get all posts
      'post_type'      => $post_types,
      'post_status'    => 'publish',
      'orderby'        => 'modified',
      'order'          => 'desc',
      'date_query'     => array(
        'after'     => $request['timestamp'],
        'column'    => 'post_modified',
        'inclusive' => true
      ),
    );

    $posts = get_posts($args);

    $data = array();

    if (empty($posts)) {
      return rest_ensure_response($data);
    }

    foreach ($posts as $post) {
      $response = $this->prepare_item_for_response($post, $request);
      $data[]   = $this->prepare_response_for_collection($response);
    }

    // Add meta information to the response.
    $total_posts = wp_count_posts()->publish;

    $response = rest_ensure_response($data);
    $response->header('X-WP-Total', $total_posts);

    return $response;
  }

  /**
   * Outputs an individual item's data
   *
   * @param WP_REST_Request $request Current request.
   */
  public function get_item($request)
  {
    $id = (int) $request['id'];
    $post = get_post($id);

    if (empty($post)) {
      return rest_ensure_response(array());
    }

    $response = $this->prepare_item_for_response($post, $request);

    return $response;
  }

  /**
   * Matches the post data to the schema. Also, rename the fields to nicer names.
   *
   * @param WP_Post $post The comment object whose response is being prepared.
   */

  // public function prepare_item_for_response($post, $request)
  // {
  //   $data = array(
  //     'post_id'      => $post->ID,
  //     'post_type'    => $post->post_type,
  //     'post_date'    => get_post_time('c', false, $post),
  //     'post_title'   => $post->post_title,
  //     'updated_at'   => get_post_modified_time('c', false, $post),
  //     'link'         => get_permalink($post->ID),
  //     'post_content' => preg_replace('/[^\x20-\x7E]/', '', wp_strip_all_tags(strip_shortcodes(get_the_content(null, false, $post)))),
  //     'tags'         => wp_get_post_tags($post->ID, array('fields' => 'names')),
  //     'categories'   => wp_get_post_categories($post->ID, array('fields' => 'names')),
  //   );

  //   return rest_ensure_response($data);
  // }

  public function prepare_item_for_response( $post, $request ) {
    $data = array(
        'post_id'      => $post->ID,
        'post_date'    => get_post_time( 'c', false, $post ),
        'post_title'   => $post->post_title,
        'updated_at'   => get_post_modified_time( 'c', false, $post ),
        'link'         => get_permalink( $post->ID ),
        'post_content' => preg_replace( '/[^\x20-\x7E]/', '', wp_strip_all_tags( strip_shortcodes( get_the_content( null, false, $post ) ) ) ),
        'post_type'    => $post->post_type,
        'file_type'    => $post->post_mime_type, // 🆕 File type for media
        
        'author'       => array(                // 🆕 Post owner
            'id'   => $post->post_author,
            'name' => get_the_author_meta( 'display_name', $post->post_author ),
        ),
        'tags'         => wp_get_post_tags( $post->ID, array( 'fields' => 'names' ) ),
        'categories'   => wp_get_post_categories( $post->ID, array( 'fields' => 'names' ) ),
    );

    return rest_ensure_response( $data );
}


  /**
   * Prepare a response for inserting into a collection of responses.
   *
   * This is copied from WP_REST_Controller class in the WP REST API v2 plugin.
   *
   * @param WP_REST_Response $response Response object.
   * @return array Response data, ready for insertion into collection data.
   */
  public function prepare_response_for_collection($response)
  {
    if (! ($response instanceof WP_REST_Response)) {
      return $response;
    }

    $data = (array) $response->get_data();
    $server = rest_get_server();

    if (method_exists($server, 'get_compact_response_links')) {
      $links = call_user_func(array($server, 'get_compact_response_links'), $response);
    } else {
      $links = call_user_func(array($server, 'get_response_links'), $response);
    }

    if (! empty($links)) {
      $data['_links'] = $links;
    }

    return $data;
  }

  /**
   * Get sample schema for a collection.
   *
   * @param WP_REST_Request $request Current request.
   */
  public function get_item_schema($request)
  {
    return array(
        '$schema'    => 'http://json-schema.org/draft-04/schema#',
        'title'      => 'post',
        'type'       => 'object',
        'properties' => array(
            'post_id' => array(
                'description' => __('Unique identifier for the object.', 'phila-gov'),
                'type'        => 'integer',
                'readonly'    => true,
            ),
            'post_date' => array(
                'description' => __('The date the object was published.', 'phila-gov'),
                'type'        => 'string',
                'format'      => 'date-time',
                'readonly'    => true,
            ),
            'post_title' => array(
                'description' => __('Title of the object.', 'phila-gov'),
                'type'        => 'string',
                'readonly'    => true,
            ),
            'updated_at' => array(
                'description' => __('Last updated time.', 'phila-gov'),
                'type'        => 'string',
                'format'      => 'date-time',
                'readonly'    => true,
            ),
            'link' => array(
                'description' => __('Link to the object.', 'phila-gov'),
                'type'        => 'string',
                'readonly'    => true,
            ),
            'post_content' => array(
                'description' => __('Content of the post.', 'phila-gov'),
                'type'        => 'string',
                'readonly'    => true,
            ),
            'post_type' => array(
                'description' => __('The type of WordPress post.', 'phila-gov'),
                'type'        => 'string',
                'readonly'    => true,
            ),
            'file_type' => array(
                'description' => __('The MIME type of the media file.', 'phila-gov'),
                'type'        => 'string',
                'readonly'    => true,
            ),
            'author' => array(
                'description' => __('Details about the post author.', 'phila-gov'),
                'type'        => 'object',
                'properties'  => array(
                    'id' => array(
                        'description' => __('Unique identifier for the author.', 'phila-gov'),
                        'type'        => 'integer',
                        'readonly'    => true,
                    ),
                    'name' => array(
                        'description' => __('Display name of the author.', 'phila-gov'),
                        'type'        => 'string',
                        'readonly'    => true,
                    ),
                ),
            ),
            'tags' => array(
                'description' => __('Tags assigned to the object.', 'phila-gov'),
                'type'        => 'array',
                'items'       => array(
                    'type' => 'string',
                ),
                'readonly'    => true,
            ),
            'categories' => array(
                'description' => __('Categories assigned to the object.', 'phila-gov'),
                'type'        => 'array',
                'items'       => array(
                    'type' => 'string',
                ),
                'readonly'    => true,
            ),
        ),
        'query_parameters' => array(
            'page' => array(
                'description' => __('Current page of the collection.', 'phila-gov'),
                'type'        => 'integer',
                'default'     => 1,
            ),
            'per_page' => array(
                'description' => __('Number of items per page.', 'phila-gov'),
                'type'        => 'integer',
                'default'     => 100, // Default to 100 entries per page.
            ),
        ),
    );
  }
}

// Function to register our new routes from the controller.
function phila_register_last_updated_for_search_rest_routes()
{
  $controller = new Phila_Last_Updated_For_Search_Controller();
  $controller->register_routes();
}

add_action('rest_api_init', 'phila_register_last_updated_for_search_rest_routes');

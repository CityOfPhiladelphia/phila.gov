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
    $page = isset($request['page']) ? (int) $request['page'] : 1; // Default to page 1
    $per_page = isset($request['per_page']) ? (int) $request['per_page'] : 100; // Default to 100 items per page
    $media_owner = isset($request['media_owner']) ? $request['media_owner'] : null; // Get media_owner parameter
    $start_date = isset($request['start_date']) ? sanitize_text_field($request['start_date']) : null; // Get start_date parameter
    $end_date = isset($request['end_date']) ? sanitize_text_field($request['end_date']) : null; // Get end_date parameter

    // Ensure media_owner is an array if provided
    if (!empty($media_owner)) {
        if (!is_array($media_owner)) {
            $media_owner = array(sanitize_text_field($media_owner)); // Convert single value to array
        } else {
            $media_owner = array_map('sanitize_text_field', $media_owner); // Sanitize array values
        }
    }

    // Only get posts of type 'attachment' (media items in WordPress)
    $post_types = array('attachment');

    $args = array(
        'posts_per_page' => $per_page,
        'paged'          => $page,
        'post_type'      => $post_types,
        'post_status'    => 'inherit',
        'orderby'        => 'date',
        'order'          => 'desc',
    );

    // Add date filtering if start_date or end_date is provided
    if ($start_date || $end_date) {
        $args['date_query'] = array(
            'column'    => 'post_date',
            'inclusive' => true,
        );

        if ($start_date) {
            $args['date_query']['after'] = $start_date;
        }

        if ($end_date) {
            $args['date_query']['before'] = $end_date;
        }
    }

    // Add taxonomy filtering if media_owner is provided
    if (!empty($media_owner)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'category', // Default WordPress category taxonomy
                'field'    => 'slug',
                'terms'    => $media_owner, // Array of category slugs
                'operator' => 'IN', // Match any of the slugs
            ),
        );
    }

    $posts = get_posts($args);

    $data = array();

    if (empty($posts)) {
        return rest_ensure_response($data); // Return empty array if no posts are found
    }

    foreach ($posts as $post) {
        $response = $this->prepare_item_for_response($post, $request);
        $data[]   = $this->prepare_response_for_collection($response);
    }

    // Add meta information to the response.
    $total_posts = wp_count_posts('attachment')->inherit; // Count total media items
    $total_pages = ceil($total_posts / $per_page); // Calculate total pages

    $response = rest_ensure_response($data);
    $response->header('X-WP-Total', $total_posts); // Total items
    $response->header('X-WP-TotalPages', $total_pages); // Total pages
    

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
  public function prepare_item_for_response($post, $request)
  {
    // Get the file path for the media item
    $file_path = get_attached_file($post->ID);

    // Extract the file name from the file path
    $file_name = $file_path ? basename($file_path) : null;

    // Get the file size from postmeta using the correct meta key
    $file_size_meta = get_post_meta($post->ID, 'as3cf_filesize_total', true); // Meta key for file size
    $file_size = $file_size_meta ? round((int) $file_size_meta / 1024) : null; // Convert to KB and round to the nearest whole number

    $data = array(
        'post_id'      => $post->ID,
        'file_type'    => $post->post_mime_type, // File type for media
        'upload_date'  => date('Y-m-d', strtotime(get_post_time('c', false, $post))), // Date only
        'post_title'   => $post->post_title,
        'file_name'    => $file_name, // File name
        'file_size'    => $file_size, // File size in KB
        // Get categories; if none, try parent post's categories
        'media_owner' => (
            ($cats = wp_get_post_categories($post->ID, array('fields' => 'slugs'))) && !empty($cats)
            ? $cats
            : (
                $post->post_parent
                ? wp_get_post_categories($post->post_parent, array('fields' => 'slugs'))
                : array()
            )
        ),
        'uploaded_by'  => get_the_author_meta('display_name', $post->post_author),
        'updated_at'   => date('Y-m-d', strtotime(get_post_modified_time('c', false, $post))), // Date only
        'backend_url'  => "https://admin.phila.gov/wp-admin/post.php?post=$post->ID&action=edit",
        'path'         => get_permalink($post->ID),
        'view_url'     => wp_get_attachment_url($post->ID)
    );

    return rest_ensure_response($data);
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
  public function get_item_schema()
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
            'upload_date' => array(
                'description' => __('The date the object was published.', 'phila-gov'),
                'type'        => 'string',
                'format'      => 'date', // Updated format
                'readonly'    => true,
            ),
            'post_title' => array(
                'description' => __('Title of the object.', 'phila-gov'),
                'type'        => 'string',
                'readonly'    => true,
            ),
            'updated_at' => array(
                'description' => __('Last updated date.', 'phila-gov'),
                'type'        => 'string',
                'format'      => 'date', // Updated format
                'readonly'    => true,
            ),
            'path' => array(
                'description' => __('Link to the object.', 'phila-gov'),
                'type'        => 'string',
                'readonly'    => true,
            ),
            'file_type' => array(
                'description' => __('The MIME type of the media file.', 'phila-gov'),
                'type'        => 'string',
                'readonly'    => true,
            ),
            'file_size' => array(
                'description' => __('The size of the media file in KB.', 'phila-gov'),
                'type'        => 'integer',
                'readonly'    => true,
            ),
            'file_name' => array(
                'description' => __('The name of the media file.', 'phila-gov'),
                'type'        => 'string',
                'readonly'    => true,
            ),
            'uploaded_by' => array(
                'description' => __('The name of the user who uploaded the media file.', 'phila-gov'),
                'type'        => 'string',
                'readonly'    => true,
            ),
            'media_owner' => array(
                'description' => __('Categories assigned to the object.', 'phila-gov'),
                'type'        => 'array',
                'items'       => array(
                    'type' => 'string',
                ),
                'readonly'    => true,
            ),
            'view_url' => array(
                'description' => __('Link to the media item.', 'phila-gov'),
                'type'        => 'string',
                'readonly'    => true,
            )
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
                'default'     => 100,
            ),
            'media_owner' => array(
                'description' => __('Filter media items by category slugs.', 'phila-gov'),
                'type'        => 'array',
                'items'       => array(
                    'type' => 'string',
                ),
                'required'    => false,
            ),
            'start_date' => array(
                'description' => __('Filter media items uploaded on or after this date (YYYY-MM-DD).', 'phila-gov'),
                'type'        => 'string',
                'format'      => 'date',
                'required'    => false,
            ),
            'end_date' => array(
                'description' => __('Filter media items uploaded on or before this date (YYYY-MM-DD).', 'phila-gov'),
                'type'        => 'string',
                'format'      => 'date',
                'required'    => false,
            ),
        ),
    );
  }
}

// Function to register our new routes from the controller.
function phila_register_media_library_report_rest_routes()
{
  $controller = new Phila_Media_Library_Report_Controller();
  $controller->register_routes();
}

add_action('rest_api_init', 'phila_register_media_library_report_rest_routes');

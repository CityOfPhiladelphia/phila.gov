<?php

class Phila_Document_Finder_Controller {

  // Initialize the namespace and resource name.
  public function __construct() {
    $this->namespace     = 'documents/v1';
    $this->resource_name = 'document';
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
    // register_rest_route( $this->namespace, '/' . $this->resource_name . '/(?P<id>[\d]+)', array(
    //   array(
    //     'methods'   => WP_REST_Server::READABLE,
    //     'callback'  => array( $this, 'get_item' ),
    //   ),
    //   'schema' => array( $this, 'get_item_schema' ),
    // ) );
  }

  /**
   * Return all documents, excluding stubs
   *
   * @param WP_REST_Request $request Current request.
  */
  public function get_items( $request ) {

    $args = array(
      'posts_per_page'=> -1,
      'post_type' => 'department_page',
      // 'orderby' => 'title',
      // 'order' => 'asc',
      // 'post_parent' => 0,
      // 'post_status' => 'publish',
      'post__in' => array(71348) // code bulletin page ID
    );

    $posts = get_posts( $args );

    $data = array();

    if ( empty( $posts ) ) {
      return rest_ensure_response( $data );
    }

    foreach ( $posts as $post ) {
      
      $document_tables = rwmb_meta( 'phila_document_table', '', $post->ID );

      foreach ( $document_tables as $document_table ) {
        $unique_table = array();
        $unique_table['title'] = $document_table['phila_custom_wysiwyg']['phila_wysiwyg_title'];
        $documents = array();

        foreach ( $document_table['phila_files'] as $id )  {
          
          $file = wp_prepare_attachment_for_js($id);
          $response = $this->prepare_item_for_response( $file );
          $documents[] = $this->prepare_response_for_collection( $response );
        }
        $unique_table['documents'] = $documents;
        $data[] = $unique_table;
      }
    }

    // Return all response data.
    return rest_ensure_response( $data );
  }

  /**
   * Matches the post data to the schema. Also, rename the fields to nicer names.
   *
   * @param WP_Post $post The comment object whose response is being prepared.
   */

  public function prepare_item_for_response( $file ) {
    $post_data = array();

    $post_data['url'] = (string) $file['subtype'];
    if($file['id']) {
      $post_data['id'] = (string) $file['id'];
    }
    if($file['title']) {
      $post_data['title'] = (string) $file['title'];
    }
    if($file['filename']) {
      $post_data['filename'] = (string) $file['filename'];
    }
    if($file['url']) {
      $post_data['url'] = (string) $file['url'];
    }
    if($file['link']) {
      $post_data['link'] = (string) $file['link'];
    }
    if($file['alt']) {
      $post_data['alt'] = (string) $file['alt'];
    }
    if($file['author']) {
      $post_data['author'] = (string) $file['author'];
    }
    if($file['description']) {
      $post_data['description'] = (string) $file['description'];
    }
    if($file['caption']) {
      $post_data['caption'] = (string) $file['caption'];
    }
    if($file['name']) {
      $post_data['name'] = (string) $file['name'];
    }
    if($file['status']) {
      $post_data['status'] = (string) $file['status'];
    }
    if($file['uploadedTo']) {
      $post_data['uploadedTo'] = (string) $file['uploadedTo'];
    }
    if($file['modified']) {
      $post_data['modified'] = (string) $file['modified'];
    }
    if($file['menuOrder']) {
      $post_data['menuOrder'] = (string) $file['menuOrder'];
    }
    if($file['mime']) {
      $post_data['mime'] = (string) $file['mime'];
    }
    if($file['type']) {
      $post_data['type'] = (string) $file['type'];
    }
    if($file['subtype']) {
      $post_data['format'] = (string) $file['subtype'];
    }
    if($file['icon']) {
      $post_data['icon'] = (string) $file['icon'];
    }
    if($file['dateFormatted']) {
      $post_data['date'] = (string) $file['dateFormatted'];
    }
    if($file['editLink']) {
      $post_data['editLink'] = (string) $file['editLink'];
    }
    if($file['meta']) {
      $post_data['meta'] = (string) $file['meta'];
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
        'id'  => array(
          'description' => esc_html__('id of the document.', 'phila-gov'),
          'type'        => 'string',
          'readonly'    => true,
        ),
        'title' => array(
          'description' => esc_html__('title of the document.', 'phila-gov'),
          'type'        => 'string',
          'readonly'    => true,
        ),
        'filename'  => array(
          'description' => esc_html__('filename of the document.', 'phila-gov'),
          'type'        => 'string',
          'readonly'    => true,
        ),
        'url' => array(
          'description' => esc_html__('url of the document.', 'phila-gov'),
          'type'        => 'string',
          'readonly'    => true,
        ),
        'link'  => array(
          'description' => esc_html__('link of the document.', 'phila-gov'),
          'type'        => 'string',
          'readonly'    => true,
        ),
        'alt' => array(
          'description' => esc_html__('alt of the document.', 'phila-gov'),
          'type'        => 'string',
          'readonly'    => true,
        ),
        'author'  => array(
          'description' => esc_html__('author of the document.', 'phila-gov'),
          'type'        => 'string',
          'readonly'    => true,
        ),
        'description' => array(
          'description' => esc_html__('description of the document.', 'phila-gov'),
          'type'        => 'string',
          'readonly'    => true,
        ),
        'caption' => array(
          'description' => esc_html__('caption of the document.', 'phila-gov'),
          'type'        => 'string',
          'readonly'    => true,
        ),
        'name'  => array(
          'description' => esc_html__('name of the document.', 'phila-gov'),
          'type'        => 'string',
          'readonly'    => true,
        ),
        'status'  => array(
          'description' => esc_html__('status of the document.', 'phila-gov'),
          'type'        => 'string',
          'readonly'    => true,
        ),
        'uploadedTo'  => array(
          'description' => esc_html__('uploadedTo of the document.', 'phila-gov'),
          'type'        => 'string',
          'readonly'    => true,
        ),
        'date'  => array(
          'description' => esc_html__('date of the document.', 'phila-gov'),
          'type'        => 'string',
          'readonly'    => true,
        ),
        'modified'  => array(
          'description' => esc_html__('modified of the document.', 'phila-gov'),
          'type'        => 'string',
          'readonly'    => true,
        ),
        'menuOrder' => array(
          'description' => esc_html__('menuOrder of the document.', 'phila-gov'),
          'type'        => 'string',
          'readonly'    => true,
        ),
        'mime'  => array(
          'description' => esc_html__('mime of the document.', 'phila-gov'),
          'type'        => 'string',
          'readonly'    => true,
        ),
        'type'  => array(
          'description' => esc_html__('type of the document.', 'phila-gov'),
          'type'        => 'string',
          'readonly'    => true,
        ),
        'format' => array(
          'description' => esc_html__('subtype of the document.', 'phila-gov'),
          'type'        => 'string',
          'readonly'    => true,
        ),
        'icon'  => array(
          'description' => esc_html__('icon of the document.', 'phila-gov'),
          'type'        => 'string',
          'readonly'    => true,
        ),
        // 'dateFormatted' => array(
        //   'description' => esc_html__('dateFormatted of the document.', 'phila-gov'),
        //   'type'        => 'string',
        //   'readonly'    => true,
        // ),
        'editLink'  => array(
          'description' => esc_html__('editLink of the document.', 'phila-gov'),
          'type'        => 'string',
          'readonly'    => true,
        ),
        'meta'  => array(
          'description' => esc_html__('meta of the document.', 'phila-gov'),
          'type'        => 'string',
          'readonly'    => true,
        ),
      ),
    );

    return $schema;
  }

}

// Function to register our new routes from the controller.
function phila_register_document_finder_rest_routes() {
  $controller = new Phila_Document_Finder_Controller();
  $controller->register_routes();
}

add_action( 'rest_api_init', 'phila_register_document_finder_rest_routes' );

<?php

class Phila_Longform_Content_Controller {

  // Initialize the namespace and resource name.
  public function __construct() {
    $this->namespace     = 'longform-content/v1';
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
  }

  /**
   * Return all documents, excluding stubs
   *
   * @param WP_REST_Request $request Current request.
  */
  public function get_items( $request ) {

    $data = array();
    // $post = get_post( url_to_postid( wp_get_referer()) );
    $post = get_post( 143606 ); // Local Host
    // $post = get_post( 146529 ); // Staging
    $longform_document['owners'] = ( array ) get_the_terms( get_the_id(), 'category' );
    $longform_document['post'] = ( object ) $post;
    $longform_document['updateHistory'] = ( object ) rwmb_meta( 'phila_longform_document_update_history', '', $post->ID );
    
    $children = get_children( $post->ID );
    foreach ($children as $child) {
      $post_children_1 = get_children( $child->ID );
      $child->children = $post_children_1;
      $child->section_title = ( string ) rwmb_meta( 'phila_longform_content_section_title', '', $child->ID );
      $child->section_number = ( string ) rwmb_meta( 'phila_longform_content_section_number', '', $child->ID );
      $child->section_copy = ( string ) rwmb_meta( 'phila_longform_content_section_copy', '', $child->ID );
      $child->footnote_copy = ( string ) rwmb_meta( 'phila_longform_content_footnote_copy', '', $child->ID );
      $child->footnote_index = ( string ) rwmb_meta( 'phila_longform_content_footnote_index', '', $child->ID );
      foreach ($post_children_1 as $child) {
        $post_children_2 = get_children( $child->ID );
        $child->children = $post_children_2;
        $child->section_title = ( string ) rwmb_meta( 'phila_longform_content_section_title', '', $child->ID );
        $child->section_number = ( string ) rwmb_meta( 'phila_longform_content_section_number', '', $child->ID );
        $child->section_copy = ( string ) rwmb_meta( 'phila_longform_content_section_copy', '', $child->ID );
        $child->footnote_copy = ( string ) rwmb_meta( 'phila_longform_content_footnote_copy', '', $child->ID );
        $child->footnote_index = ( string ) rwmb_meta( 'phila_longform_content_footnote_index', '', $child->ID );
        foreach ($post_children_2 as $child) {
          $post_children_3 = get_children( $child->ID );
          $child->children = $post_children_3;
          $child->section_title = ( string ) rwmb_meta( 'phila_longform_content_section_title', '', $child->ID );
          $child->section_number = ( string ) rwmb_meta( 'phila_longform_content_section_number', '', $child->ID );
          $child->section_copy = ( string ) rwmb_meta( 'phila_longform_content_section_copy', '', $child->ID );
          $child->footnote_copy = ( string ) rwmb_meta( 'phila_longform_content_footnote_copy', '', $child->ID );
          $child->footnote_index = ( string ) rwmb_meta( 'phila_longform_content_footnote_index', '', $child->ID );
          foreach ($post_children_3 as $child) {
            $post_children_4 = get_children( $child->ID );
            $child->children = $post_children_4;
            $child->section_title = ( string ) rwmb_meta( 'phila_longform_content_section_title', '', $child->ID );
            $child->section_number = ( string ) rwmb_meta( 'phila_longform_content_section_number', '', $child->ID );
            $child->section_copy = ( string ) rwmb_meta( 'phila_longform_content_section_copy', '', $child->ID );
            $child->footnote_copy = ( string ) rwmb_meta( 'phila_longform_content_footnote_copy', '', $child->ID );
            $child->footnote_index = ( string ) rwmb_meta( 'phila_longform_content_footnote_index', '', $child->ID );
            foreach ($post_children_4 as $child) {
              $post_children_5 = get_children( $child->ID );
              $child->children = $post_children_5;
              $child->section_title = ( string ) rwmb_meta( 'phila_longform_content_section_title', '', $child->ID );
              $child->section_number = ( string ) rwmb_meta( 'phila_longform_content_section_number', '', $child->ID );
              $child->section_copy = ( string ) rwmb_meta( 'phila_longform_content_section_copy', '', $child->ID );
              $child->footnote_copy = ( string ) rwmb_meta( 'phila_longform_content_footnote_copy', '', $child->ID );
              $child->footnote_index = ( string ) rwmb_meta( 'phila_longform_content_footnote_index', '', $child->ID );
              foreach ($post_children_5 as $child) {
                $post_children_6 = get_children( $child->ID );
                $child->children = $post_children_6;
                $child->section_title = ( string ) rwmb_meta( 'phila_longform_content_section_title', '', $child->ID );
                $child->section_number = ( string ) rwmb_meta( 'phila_longform_content_section_number', '', $child->ID );
                $child->section_copy = ( string ) rwmb_meta( 'phila_longform_content_section_copy', '', $child->ID );
                $child->footnote_copy = ( string ) rwmb_meta( 'phila_longform_content_footnote_copy', '', $child->ID );
                $child->footnote_index = ( string ) rwmb_meta( 'phila_longform_content_footnote_index', '', $child->ID );
                foreach ($post_children_6 as $child) {
                  $post_children_7 = get_children( $child->ID );
                  $child->children = $post_children_7;
                  $child->section_title = ( string ) rwmb_meta( 'phila_longform_content_section_title', '', $child->ID );
                  $child->section_number = ( string ) rwmb_meta( 'phila_longform_content_section_number', '', $child->ID );
                  $child->section_copy = ( string ) rwmb_meta( 'phila_longform_content_section_copy', '', $child->ID );
                  $child->footnote_copy = ( string ) rwmb_meta( 'phila_longform_content_footnote_copy', '', $child->ID );
                  $child->footnote_index = ( string ) rwmb_meta( 'phila_longform_content_footnote_index', '', $child->ID );
                  foreach ($post_children_6 as $child) {
                    $child->section_title = ( string ) rwmb_meta( 'phila_longform_content_section_title', '', $child->ID );
                    $child->section_number = ( string ) rwmb_meta( 'phila_longform_content_section_number', '', $child->ID );
                    $child->section_copy = ( string ) rwmb_meta( 'phila_longform_content_section_copy', '', $child->ID );
                    $child->footnote_copy = ( string ) rwmb_meta( 'phila_longform_content_footnote_copy', '', $child->ID );
                    $child->footnote_index = ( string ) rwmb_meta( 'phila_longform_content_footnote_index', '', $child->ID );
                  }
                }
              }
            }
          }
        }
      }
    }
    
    $longform_document['children'] = ( object ) $children;

    // Return all response data.
    return rest_ensure_response( $longform_document );
  }

  /**
   * Matches the post data to the schema. Also, rename the fields to nicer names.
   *
   * @param WP_Post $post The comment object whose response is being prepared.
   */

  public function prepare_item_for_response( $file ) {
    $post_data = array();
    $post_data['output'] = (string) $file;
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
        'media_category'  => array(
          'description' => esc_html__('media category of the document.', 'phila-gov'),
          'type'        => 'string',
          'readonly'    => true,
        ),
      ),
    );

    return $schema;
  }
}

// Function to register our new routes from the controller.
function phila_register_longform_content_rest_routes() {
  $controller = new Phila_Longform_Content_Controller();
  $controller->register_routes();
}

add_action( 'rest_api_init', 'phila_register_longform_content_rest_routes' );
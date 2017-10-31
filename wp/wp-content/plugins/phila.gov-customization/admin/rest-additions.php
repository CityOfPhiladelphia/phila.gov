<?php
/*
 * Exposes certain meta fields to the REST API.
 * See https://developer.wordpress.org/rest-api/extending-the-rest-api/modifying-responses/
 *
*/

/* Expose service page icons for use in Megamenu markup */
add_action('rest_api_init', 'phila_register_icon_service_pages');

function phila_register_icon_service_pages(){
  $object_type = 'post';
  $args = array(
      'type'         => 'string',
      'description'  => 'A meta key for service page icons. String.',
      'single'       => true,
      'show_in_rest' => true,
  );
  register_meta( $object_type, 'phila_page_icon', $args );
}

add_action('rest_api_init', 'phila_register_dept_meta');

function phila_register_dept_meta(){
  register_rest_field('department_page', 'contact_information', array(
    'get_callback' => 'get_contact_info' )
  );
  function get_contact_info($object){
    $post_id = $object['id'];

    $meta = get_post_meta($post_id, 'module_row_1_col_2_connect_panel');

    return $meta;
  }
}

add_action('rest_api_init', 'phila_register_dept_code');

function phila_register_dept_code(){
  register_rest_field('department_page', 'department_code', array(
    'get_callback' => 'get_dept_code' )
  );
  function get_dept_code($object){
    $post_id = $object['id'];

    $meta = get_post_meta($post_id, 'phila_department_code');

    return $meta;
  }
}

//Expose template type
add_action('rest_api_init', 'phila_register_template_type');

function phila_register_template_type(){
  $object_type = 'post';
  $args = array(
      'type'         => 'string',
      'description'  => 'Metakey for template type. String.',
      'single'       => true,
      'show_in_rest' => true,
  );
  register_meta( $object_type, 'phila_template_select', $args );
}



/**
 * Plugin Name: WP-API Client JS
 * Plugin URI: https://github.com/WP-API/client-js
 * Description: Backbone-based JavaScript client for WP API.
 * Version: 1.0.1
 */

/**
 * Set up the REST API server and localize the schema.
 */
function json_api_client_js() {

  // Ensure that the wp-api script is registered.
  // $scripts = wp_scripts();
  // $src = plugins_url( 'build/js/wp-api.js', __FILE__ );
  // if ( isset( $scripts->registered['wp-api'] ) ) {
  //   $scripts->registered['wp-api']->src = $src;
  // } else {
  //   wp_register_script( 'wp-api', $src, array( 'jquery', 'underscore', 'backbone' ), '1.0', true );
  // }

  /**
   * @var WP_REST_Server $wp_rest_server
   */
  global $wp_rest_server;

  // Ensure the rest server is intiialized.
  if ( empty( $wp_rest_server ) ) {
    /** This filter is documented in wp-includes/rest-api.php */
    $wp_rest_server_class = apply_filters( 'wp_rest_server_class', 'WP_REST_Server' );
    $wp_rest_server       = new $wp_rest_server_class();

    /** This filter is documented in wp-includes/rest-api.php */
    do_action( 'rest_api_init', $wp_rest_server );
  }

  // Load the schema.
  $schema_request  = new WP_REST_Request( 'GET', '/wp/v2' );
  $schema_response = $wp_rest_server->dispatch( $schema_request );
  $schema = null;
  if ( ! $schema_response->is_error() ) {
    $schema = $schema_response->get_data();
  }

  // Localize the plugin settings and schema.
  $settings = array(
    'root'          => esc_url_raw( get_rest_url() ),
    'nonce'         => wp_create_nonce( 'wp_rest' ),
    'versionString' => 'wp/v2/',
    'schema'        => $schema,
    'cacheSchema'   => true,
  );

  /**
   * Filter the JavaScript Client settings before localizing.
   *
   * Enables modifying the config values sent to the JS client.
   *
   * @param array  $settings The JS Client settings.
   */
  $settings = apply_filters( 'rest_js_client_settings', $settings );
  wp_localize_script( 'wp-api', 'wpApiSettings', $settings );

}

add_action( 'admin_enqueue_scripts', 'json_api_client_js' );

<?php
/**
 *  Custom Post type for Programs Initiatives
 *
 */
if ( class_exists( "Phila_Gov_Service_Pages" ) ){
  $cpt_service_pages = new Phila_Gov_Service_Pages();
}


class Phila_Gov_Service_Pages{

  public function __construct(){

    add_action( 'init', array( $this, 'create_phila_service_pages' ), 1 );

  }

  function create_phila_service_pages() {
    register_post_type( 'service_page',
      array(
        'labels' => array(
          'name' => __( 'Services' ),
          'menu_name' => __('Services'),
          'singular_name' => __( 'Service page' ),
          'add_new'   => __( 'Add service page' ),
          'all_items'   => __( 'All services' ),
          'add_new_item' => __( 'Add service page' ),
          'edit_item'   => __( 'Edit service page' ),
          'view_item'   => __( 'View service page' ),
          'search_items'   => __( 'Search service pages' ),
          'not_found'   => __( 'No service pages Found' ),
          'not_found_in_trash'   => __( 'Service page not found in trash' ),
        ),
        'taxonomies' => array('category', 'topics'),
        'supports' => array(
          'title',
          'editor',
          'page-attributes',
          'revisions',
          'author',
          'custom-fields',
          'thumbnail'
        ),
        'public' => true,
        'show_in_rest' => true,
        'rest_base' => 'services',
        'has_archive' => false,
        'show_in_nav_menus' => true,
        'menu_icon' => 'dashicons-admin-generic',
        'hierarchical' => true,
        'query_var' => true,
        'rewrite' => array(
          'slug' => 'services',
          'with_front' => false,
        ),
      )
    );
  }
}

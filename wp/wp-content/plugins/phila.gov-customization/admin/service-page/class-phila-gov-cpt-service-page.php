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
          'menu_name' => __('Service Page'),
          'singular_name' => __( 'Service Page' ),
          'add_new'   => __( 'Add a Service Page' ),
          'all_items'   => __( 'All Service Pages' ),
          'add_new_item' => __( 'Add a Service Page' ),
          'edit_item'   => __( 'Edit Service Page' ),
          'view_item'   => __( 'View Service Page' ),
          'search_items'   => __( 'Search Service Pages' ),
          'not_found'   => __( 'No Service Pages Found' ),
          'not_found_in_trash'   => __( 'Service Page not found in trash' ),
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
        'has_archive' => true,
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

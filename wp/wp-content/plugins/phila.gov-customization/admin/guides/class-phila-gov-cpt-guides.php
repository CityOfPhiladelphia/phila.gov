<?php
/**
 *  Custom Post type for guidess
 *
 */
if ( class_exists( "Phila_Gov_Guides" ) ){
  $cpt_guides = new Phila_Gov_Guides();
}


class Phila_Gov_Guides{

  public function __construct(){

    add_action( 'init', array( $this, 'create_phila_guides' ), 1 );

  }

  function create_phila_guides() {
    register_post_type( 'guides',
      array(
        'labels' => array(
          'name' => __( 'Guides' ),
          'menu_name' => __('Guides '),
          'singular_name' => __( 'Guide' ),
          'add_new'   => __( 'Add a Guide page' ),
          'all_items'   => __( 'All guides' ),
          'add_new_item' => __( 'Add a Page' ),
          'edit_item'   => __( 'Edit a Page' ),
          'view_item'   => __( 'View a Page' ),
          'search_items'   => __( 'Search guide pages' ),
          'not_found'   => __( 'No Guides Found' ),
          'not_found_in_trash'   => __( 'Guide Page not found in trash' ),
        ),
        'taxonomies' => array('category'),
        'supports' => array(
          'title',
          'editor',
          'page-attributes',
          'revisions'
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'rest_base' => 'guides',
        'show_in_nav_menus' => true,
        'menu_icon' => 'dashicons-format-status',
        'hierarchical' => true,
        'query_var' => true,
        'rewrite' => array(
          'slug' => 'guides',
          'with_front' => false,
        ),
      )
    );
  }
}

<?php
/**
 *  Custom Post type for Programs Initiatives
 *
 */
if ( class_exists( "Phila_Gov_Programs_Initiatives" ) ){
  $cpt_programs = new Phila_Gov_Programs_Initiatives();
}


class Phila_Gov_Programs_Initiatives{

  public function __construct(){

    add_action( 'init', array( $this, 'create_phila_programs_initiatives' ), 1 );

  }

  function create_phila_programs_initiatives() {
    register_post_type( 'programs',
      array(
        'labels' => array(
          'name' => __( 'Programs + Initiatives' ),
          'menu_name' => __('Programs + Initiatives'),
          'singular_name' => __( 'Program + Initiative' ),
          'add_new'   => __( 'Add a Program or Initiative' ),
          'all_items'   => __( 'All Programs + Initiatives' ),
          'add_new_item' => __( 'Add a Page' ),
          'edit_item'   => __( 'Edit a Page' ),
          'view_item'   => __( 'View a Page' ),
          'search_items'   => __( 'Search Programs + Initiatives Pages' ),
          'not_found'   => __( 'No Programs + Initiatives Found' ),
          'not_found_in_trash'   => __( 'Program + Initiative Page not found in trash' ),
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
        'rest_base' => 'programs',
        'show_in_nav_menus' => true,
        'menu_icon' => 'dashicons-clipboard',
        'hierarchical' => true,
        'query_var' => true,
        'rewrite' => array(
          'slug' => 'programs',
          'with_front' => false,
        ),
      )
    );
  }
}

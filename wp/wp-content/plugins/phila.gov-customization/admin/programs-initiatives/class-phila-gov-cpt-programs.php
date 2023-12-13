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
          'name' => __( 'Programs' ),
          'menu_name' => __('Programs'),
          'singular_name' => __( 'Program ' ),
          'add_new'   => __( 'Add a program' ),
          'all_items'   => __( 'All programs' ),
          'add_new_item' => __( 'Add program page' ),
          'edit_item'   => __( 'Edit a page' ),
          'view_item'   => __( 'View a page' ),
          'search_items'   => __( 'Search programs pages' ),
          'not_found'   => __( 'No programs found' ),
          'not_found_in_trash'   => __( 'Program page not found in trash' ),
        ),
        'taxonomies' => array('category'),
        'supports' => array(
          'title',
          'editor',
          'page-attributes',
          'revisions',
          'thumbnail'
        ),
        'public' => true,
        'has_archive' => false,
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

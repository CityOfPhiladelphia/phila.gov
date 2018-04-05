<?php
/**
 *  Custom Post type for Departments
 *
 */
if ( class_exists( "Phila_Gov_Departments" ) ){
  $cpt_departments = new Phila_Gov_Departments();
}


class Phila_Gov_Departments{

  public function __construct(){

    add_action( 'init', array( $this, 'create_phila_department_pages' ), 1 );

  }

  function create_phila_department_pages() {
    register_post_type( 'department_page',
      array(
        'labels' => array(
          'name' => __( 'Department page' ),
          'menu_name' => __('Department Site'),
          'singular_name' => __( 'Department page' ),
          'add_new'   => __( 'Add a Page' ),
          'all_items'   => __( 'All Pages' ),
          'add_new_item' => __( 'Add a Department Page' ),
          'edit_item'   => __( 'Edit Department Page' ),
          'view_item'   => __( 'View Department Page' ),
          'search_items'   => __( 'Search Department Pages' ),
          'not_found'   => __( 'No Pages Found' ),
          'not_found_in_trash'   => __( 'Department Page not found in trash' ),
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
        'rest_base' => 'departments',
        'show_in_nav_menus' => true,
        'menu_icon' => 'dashicons-groups',
        'hierarchical' => true,
        'query_var' => true,
        'rewrite' => array(
          'slug' => 'departments',
          'with_front' => false,
        ),
      )
    );
  }

}

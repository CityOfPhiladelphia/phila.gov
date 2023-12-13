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
          'menu_name' => __('Department pages'),
          'singular_name' => __( 'Department page' ),
          'add_new'   => __( 'Add a page' ),
          'all_items'   => __( 'All department pages' ),
          'add_new_item' => __( 'Add department page' ),
          'edit_item'   => __( 'Edit department page' ),
          'view_item'   => __( 'View department page' ),
          'search_items'   => __( 'Search department pages' ),
          'not_found'   => __( 'No pages found' ),
          'not_found_in_trash'   => __( 'Department page not found in trash' ),
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

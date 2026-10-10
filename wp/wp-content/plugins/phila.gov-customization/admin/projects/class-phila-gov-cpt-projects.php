<?php
/**
 *  Custom Post type for Capital Projects
 *
 */
if ( class_exists( "Phila_Gov_Projects" ) ){
  $cpt_departments = new Phila_Gov_Projects();
}


class Phila_Gov_Projects{

  public function __construct(){

    add_action( 'init', array( $this, 'create_phila_projects' ), 1 );

  }

  function create_phila_projects() {
    register_post_type( 'project',
      array(
        'labels' => array(
          'name' => __( 'Project' ),
          'menu_name' => __('Projects'),
          'singular_name' => __( 'Project' ),
          'add_new'   => __( 'Add a project' ),
          'all_items'   => __( 'All projects' ),
          'add_new_item' => __( 'Add project' ),
          'edit_item'   => __( 'Edit project' ),
          'view_item'   => __( 'View project' ),
          'search_items'   => __( 'Search projects' ),
          'not_found'   => __( 'No projects found' ),
          'not_found_in_trash'   => __( 'Project not found in trash' ),
        ),
        'taxonomies' => array('category'),
        'supports' => array(
          'title',
          'editor',
          'revisions',
          'author',
          'page-attributes',
        ),
        'public' => true,
        'has_archive' => false,
        'show_in_rest' => true,
        'rest_base' => 'projects',
        'show_in_nav_menus' => true,
        'menu_icon' => 'dashicons-groups',
        'hierarchical' => true,
        'query_var' => true,
        'rewrite' => array(
          'slug' => 'projects',
          'with_front' => false,
        ),
      )
    );
  }

}

<?php
/**
 *  Custom Post type for Event Spotlight
 *
 */
if ( class_exists( "Phila_Gov_CPT_Event_Spotlight" ) ){
  $cpt_event_spotlight = new Phila_Gov_CPT_Event_Spotlight();
}


class Phila_Gov_CPT_Event_Spotlight{

  public function __construct(){

    add_action( 'init', array( $this, 'create_phila_event_spotlight' ), 1 );

  }

  function create_phila_event_spotlight() {
    register_post_type( 'event_spotlight',
      array(
        'labels' => array(
          'name' => __( 'Event spotlight page' ),
          'menu_name' => __('Event spotlight'),
          'singular_name' => __( 'Event spotlight page' ),
          'add_new'   => __( 'Add spotlight page' ),
          'all_items'   => __( 'All spotlights' ),
          'add_new_item' => __( 'Add spotlight page' ),
          'edit_item'   => __( 'Edit event spotlight page' ),
          'view_item'   => __( 'View event spotlight page' ),
          'search_items'   => __( 'Search event spotlight pages' ),
          'not_found'   => __( 'No pages Found' ),
          'not_found_in_trash'   => __( 'Event spotlight page not found in trash' ),
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
        'rest_base' => 'spotlight',
        'show_in_nav_menus' => true,
        'menu_icon' => 'dashicons-calendar',
        'hierarchical' => true,
        'query_var' => true,
        'rewrite' => array(
          'slug' => 'spotlight',
          'with_front' => false,
        ),
      )
    );
  }

}

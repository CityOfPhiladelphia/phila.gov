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
          'name' => __( 'Event Spotlight page' ),
          'menu_name' => __('Event Spotlight'),
          'singular_name' => __( 'Event Spotlight page' ),
          'add_new'   => __( 'Add a Page' ),
          'all_items'   => __( 'All Pages' ),
          'add_new_item' => __( 'Add a Event Spotlight Page' ),
          'edit_item'   => __( 'Edit Event Spotlight Page' ),
          'view_item'   => __( 'View Event Spotlight Page' ),
          'search_items'   => __( 'Search Event Spotlight Pages' ),
          'not_found'   => __( 'No Pages Found' ),
          'not_found_in_trash'   => __( 'Event Spotlight Page not found in trash' ),
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

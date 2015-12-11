<?php
/**
 *  Create Custom Post Types
 *
 * Additional custom post types can be defined here
 * http://codex.wordpress.org/Post_Types
 *
 * @link https://github.com/CityOfPhiladelphia/phila.gov-customization
 *
 *
 */
if ( class_exists("Phila_Gov_Custom_Post_Types" ) ){
  $custom_post_types = new Phila_Gov_Custom_Post_Types();
}

class Phila_Gov_Custom_Post_Types{

  public function __construct(){
    add_action( 'init', array( $this, 'create_services_post_type' ) );

    add_action( 'init', array( $this, 'create_news_post_type' ) );

    add_action( 'init', array( $this, 'create_departments_page_type' ) );

    add_action( 'init', array( $this, 'create_site_wide_alert' ) );

    add_action( 'init', array( $this, 'create_document_post_type' ) );

    add_action( 'init', array( $this, 'create_notices_post_type' ) );

    register_activation_hook( __FILE__, array( $this, 'rewrite_flush' ) );

  }

  function create_services_post_type() {
    register_post_type( 'service_post',
      array(
        'labels' => array(
          'name' => __( 'Service Page' ),
          'singular_name' => __( 'Service Page' ),
          'add_new'   => __( 'Add Service Page' ),
          'all_items'   => __( 'All Service Pages' ),
          'add_new_item' => __( 'Add Service Page' ),
          'edit_item'   => __( 'Edit Service Page' ),
          'view_item'   => __( 'View Service Page' ),
          'search_items'   => __( 'Search Service Pages' ),
          'not_found'   => __( 'Service Page Not Found' ),
          'not_found_in_trash'   => __( 'Service Page not found in trash' ),
        ),
        'taxonomies' => array('category', 'post_tag'),
        'supports' => array( 'title', 'editor', 'revisions'),
        'public' => true,
        'has_archive' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-groups',
        'hierarchical' => false,
        'rewrite' => array(
            'slug' => 'service',
        ),
      )
    );
  }

  function create_departments_page_type() {
    register_post_type( 'department_page',
      array(
        'labels' => array(
          'name' => __( 'Department Site' ),
          'singular_name' => __( 'Department Site' ),
          'add_new'   => __( 'Add a Page' ),
          'all_items'   => __( 'All Pages' ),
          'add_new_item' => __( 'Add a Department Page' ),
          'edit_item'   => __( 'Edit Department Page' ),
          'view_item'   => __( 'View Department Page' ),
          'search_items'   => __( 'Search Department Pages' ),
          'not_found'   => __( 'No Pages Found' ),
          'not_found_in_trash'   => __( 'Department Page not found in trash' ),
          'parent_item_colon' => '',
        ),
        'taxonomies' => array('category'),
        'supports' => array( 'title', 'editor', 'page-attributes', 'revisions'),
        'public' => true,
        'has_archive' => true,
        'show_in_nav_menus' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-groups',
        'hierarchical' => true,
        'query_var' => true,
        'rewrite' => array(
            'slug' => 'departments',
        ),
      )
    );
  }

 function create_news_post_type() {
  register_post_type( 'news_post',
    array(
      'labels' => array(
        'name' => __( 'News' ),
        'singular_name' => __( 'News' ),
        'add_new'   => __( 'Add News' ),
        'all_items'   => __( 'All News' ),
        'add_new_item' => __( 'Add News' ),
        'edit_item'   => __( 'Edit News' ),
        'view_item'   => __( 'View News Item' ),
        'search_items'   => __( 'Search News' ),
        'not_found'   => __( 'News Not Found' ),
        'not_found_in_trash'   => __( 'News not found in trash' ),
      ),
      'taxonomies' => array('category', 'topics'),
      'public' => true,
      'has_archive' => true,
      'menu_position' => 6,
      'menu_icon' => 'dashicons-media-document',
      'hierarchical' => false,
      'supports'  => array('title','editor','thumbnail', 'revisions'),
      'rewrite' => array(
          'slug' => 'news'
        ),
      )
    );
  }
  function create_site_wide_alert() {
    register_post_type( 'site_wide_alert',
      array(
        'labels' => array(
          'name' => __( 'Site-wide Alerts' ),
          'singular_name' => __( 'Site-wide Alert' ),
          'add_new'   => __( 'Add Site-wide Alert' ),
          'all_items'   => __( 'All Site-wide Alerts' ),
          'add_new_item' => __( 'Add Site-wide Alerts' ),
          'edit_item'   => __( 'Edit Site-wide Alerts' ),
          'view_item'   => __( 'View Site-wide Alerts' ),
          'search_items'   => __( 'Search Site-wide Alerts'),
          'not_found'   => __( 'Site-wide Alert not found' ),
          'not_found_in_trash'   => __( 'Site-wide Alert not found in trash' ),
        ),
        'taxonomies' => array('category'),
        'exclude_from_search' => true,
        'public' => true,
        'has_archive' => false,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-megaphone',
        'hierarchical' => false,
        'rewrite' => array(
          'slug' => 'alerts',
        ),
      )
    );
  }

  function create_document_post_type() {
    register_post_type( 'document',
      array(
        'labels' => array(
            'name' => __( 'Document Page' ),
            'singular_name' => __( 'Document' ),
            'add_new'   => __( 'Add Document' ),
            'all_items'   => __( 'All Documents' ),
            'add_new_item' => __( 'Add New Document' ),
            'edit_item'   => __( 'Edit Document' ),
            'view_item'   => __( 'View Document' ),
            'search_items'   => __( 'Search Documents' ),
            'not_found'   => __( 'Document Not Found' ),
            'not_found_in_trash'   => __( 'Document not found in trash' ),
        ),
        'taxonomies' => array('category', 'document_type'),
        'supports' => array( 'title', 'revisions'),
        'public' => true,
        'has_archive' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-media-text',
        'hierarchical' => false,
        'rewrite' => array(
            'slug' => 'documents',
        ),
      )
    );
  }
  function create_notices_post_type() {
    register_post_type( 'notices',
      array(
        'labels' => array(
            'name' => __( 'Notices' ),
            'singular_name' => __( 'Notice' ),
            'add_new'   => __( 'Add Notice' ),
            'all_items'   => __( 'All Notices' ),
            'add_new_item' => __( 'Add New Notice' ),
            'edit_item'   => __( 'Edit Notice' ),
            'view_item'   => __( 'View Notice' ),
            'search_items'   => __( 'Search Notice' ),
            'not_found'   => __( 'Notice Not Found' ),
            'not_found_in_trash'   => __( 'Notice not found in trash' ),
        ),
        'taxonomies' => array('category'),
        'supports' => array( 'editor', 'title', 'revisions'),
        'public' => true,
        'has_archive' => true,
        'menu_position' => 4,
        'menu_icon' => 'dashicons-warning',
        'hierarchical' => false,
        'rewrite' => array(
            'slug' => 'notices',
        ),
      )
    );
  }
}

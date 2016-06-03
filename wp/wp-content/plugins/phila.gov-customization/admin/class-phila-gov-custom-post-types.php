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
if ( class_exists( "Phila_Gov_Custom_Post_Types" ) ){
  $custom_post_types = new Phila_Gov_Custom_Post_Types();
}

class Phila_Gov_Custom_Post_Types{

  public function __construct(){

    add_action( 'init', array( $this, 'create_phila_posts' ) );

    add_action( 'init', array( $this, 'create_phila_services' ) );

    add_action( 'init', array( $this, 'create_phila_news' ) );

    add_action( 'init', array( $this, 'create_phila_departments_page_type' ) );

    add_action( 'init', array( $this, 'create_phila_site_wide_alert' ) );

    add_action( 'init', array( $this, 'create_phila_document' ) );

    add_action( 'init', array( $this, 'create_phila_notices' ) );

    add_action( 'init', array( $this, 'create_phila_press_release' ) );

    add_action( 'init', array( $this, 'create_phila_event_page_type' ) );

  }

  function create_phila_services() {
    register_post_type( 'service_post',
      array(
        'labels' => array(
          'name' => __( 'Services' ),
          'menu_name' => __( 'Service Page' ),
          'singular_name' => __( 'Service' ),
          'add_new'   => __( 'Add Service Page' ),
          'all_items'   => __( 'All Service Pages' ),
          'add_new_item' => __( 'Add Service Page' ),
          'edit_item'   => __( 'Edit Service Page' ),
          'view_item'   => __( 'View Service Page' ),
          'search_items'   => __( 'Search Service Pages' ),
          'not_found'   => __( 'Service Page Not Found' ),
          'not_found_in_trash'   => __( 'Service Page not found in trash' ),
        ),
        'taxonomies' => array( 'category' ),
        'supports' => array( 'title', 'editor', 'revisions' ),
        'public' => true,
        'has_archive' => false,
        'menu_icon' => 'dashicons-groups',
        'hierarchical' => false,
        'rewrite' => array(
          'slug' => 'service',
          'with_front' => false,
        ),
      )
    );
  }

  function create_phila_departments_page_type() {
    register_post_type( 'department_page',
      array(
        'labels' => array(
          'name' => __( 'Departments' ),
          'menu_name' => __('Department Site'),
          'singular_name' => __( 'Department' ),
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
        'supports' => array( 'title', 'editor', 'page-attributes', 'revisions', 'thumbnail'),
        'public' => true,
        'has_archive' => true,
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

 function create_phila_news() {
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
      'taxonomies' => array( 'category', 'topics' ),
      'public' => true,
      'has_archive' => true,
      'menu_icon' => 'dashicons-media-document',
      'hierarchical' => false,
      'supports'  => array( 'title', 'editor', 'thumbnail', 'revisions' ),
      'rewrite' => array(
        'slug' => 'news',
        'with_front' => false,
        ),
      )
    );
  }
  function create_phila_site_wide_alert() {
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
        'public' => false,
        'show_ui' => true,
        'has_archive' => false,
        'menu_icon' => 'dashicons-megaphone',
        'hierarchical' => false,
        'rewrite' => array(
          'slug' => 'alerts',
          'with_front' => false,
        ),
      )
    );
  }

  function create_phila_document() {
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
        'taxonomies' => array( 'category', 'document_type' ),
        'supports' => array( 'title', 'revisions' ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-media-text',
        'hierarchical' => false,
        'rewrite' => array(
          'slug' => 'documents',
          'with_front' => false,
        ),
      )
    );
  }
  function create_phila_notices() {
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
        'menu_icon' => 'dashicons-warning',
        'exclude_from_search' => true,
        'hierarchical' => false,
        'rewrite' => array(
          'slug' => 'notices',
          'with_front' => false,
        ),
      )
    );
  }
  function create_phila_posts() {
    register_post_type( 'phila_post',
      array(
        'labels' => array(
            'name' => __( 'Posts' ),
            'menu_name' => __( 'Blog' ),
            'singular_name' => __( 'Posts' ),
            'add_new'   => __( 'Add Post' ),
            'all_items'   => __( 'All Posts' ),
            'add_new_item' => __( 'Add New Post' ),
            'edit_item'   => __( 'Edit Post' ),
            'view_item'   => __( 'View Post' ),
            'search_items'   => __( 'Search Posts' ),
            'not_found'   => __( 'Post Not Found' ),
            'not_found_in_trash'   => __( 'Post not found in trash' ),
        ),
        'taxonomies' => array( 'category', 'post_tag' ),
        'supports' => array( 'editor', 'title', 'revisions', 'thumbnail', 'author'),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-admin-post',
        'hierarchical' => false,
        'rewrite' => array(
            'slug' => 'posts',
            'with_front' => false,
        ),
      )
    );
  }
  function create_phila_press_release() {
    register_post_type( 'press_release',
      array(
        'labels' => array(
            'name' => __( 'Press Releases' ),
            'menu_name' => __( 'Press Releases' ),
            'singular_name' => __( 'Press Release' ),
            'add_new'   => __( 'Add Press Release' ),
            'all_items'   => __( 'All Press Releases' ),
            'add_new_item' => __( 'Add New Press Release' ),
            'edit_item'   => __( 'Edit Press Release' ),
            'view_item'   => __( 'View Press Releases' ),
            'search_items'   => __( 'Search Press Releases' ),
            'not_found'   => __( 'Press Release Not Found' ),
            'not_found_in_trash'   => __( 'Press Release not found in trash' ),
        ),
        'taxonomies' => array( 'category' ),
        'supports' => array( 'editor', 'title', 'revisions' ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-editor-justify',
        'hierarchical' => false,
        'rewrite' => array(
            'slug' => 'press-releases',
            'with_front' => false,
        ),
      )
    );
  }
  function create_phila_event_page_type() {
    register_post_type( 'event_page',
      array(
        'labels' => array(
          'name' => __( 'Events' ),
          'menu_name' => __('Event Page'),
          'singular_name' => __( 'Event' ),
          'add_new'   => __( 'Add a Page' ),
          'all_items'   => __( 'All Pages' ),
          'add_new_item' => __( 'Add a Event Page' ),
          'edit_item'   => __( 'Edit Event Page' ),
          'view_item'   => __( 'View Event Page' ),
          'search_items'   => __( 'Search Event Pages' ),
          'not_found'   => __( 'No Pages Found' ),
          'not_found_in_trash'   => __( 'Event Page not found in trash' ),
        ),
        'taxonomies' => array('category'),
        'supports' => array( 'title', 'page-attributes', 'revisions', 'thumbnail'),
        'public' => true,
        'has_archive' => true,
        'show_in_nav_menus' => true,
        'menu_icon' => 'dashicons-tickets-alt',
        'hierarchical' => true,
        'query_var' => true,
        'rewrite' => array(
          'slug' => 'events',
          'with_front' => false,
        ),
      )
    );
  }
}

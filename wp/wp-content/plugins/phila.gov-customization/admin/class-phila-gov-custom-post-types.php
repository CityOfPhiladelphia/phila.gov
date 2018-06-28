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

    add_action( 'init', array( $this, 'create_phila_service_pages' ), 1 );

    add_action( 'init', array( $this, 'create_phila_service_updates' ), 1 );

    add_action( 'init', array( $this, 'create_phila_site_wide_alert' ), 1 );

    add_action( 'init', array( $this, 'create_phila_document' ), 1 );

    add_action( 'init', array( $this, 'create_phila_staff_directory' ), 1 );

    add_action( 'init', array( $this, 'create_phila_annoucement' ), 1 );

    add_action( 'admin_init', array($this, 'redirect_admin_pages'), 1);



    //deprecated CPTs
    add_action( 'init', array( $this, 'create_phila_posts' ) );
    add_action( 'init', array( $this, 'create_phila_news_post' ) );
    add_action( 'init', array( $this, 'create_phila_press_release' ) );

  }


  /**
  * Redirect admin pages from old CPTs to Posts
  *
  * Redirect admin page to another admin page.
  *
  * @access public
  *
  * @return void
  */
  function redirect_admin_pages(){
    global $pagenow;
    if($pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'phila_post'){
        wp_redirect(admin_url('edit.php', 'http'), 301);
        exit;
    }
    if($pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'news_post'){
        wp_redirect(admin_url('edit.php', 'http'), 301);
        exit;
    }
    if($pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'press_release'){
        wp_redirect(admin_url('edit.php', 'http'), 301);
        exit;
    }
  }

  function create_phila_service_pages() {
    register_post_type( 'service_page',
      array(
        'labels' => array(
          'name' => __( 'Services' ),
          'menu_name' => __('Service Page'),
          'singular_name' => __( 'Service Page' ),
          'add_new'   => __( 'Add a Service Page' ),
          'all_items'   => __( 'All Service Pages' ),
          'add_new_item' => __( 'Add a Service Page' ),
          'edit_item'   => __( 'Edit Service Page' ),
          'view_item'   => __( 'View Service Page' ),
          'search_items'   => __( 'Search Service Pages' ),
          'not_found'   => __( 'No Service Pages Found' ),
          'not_found_in_trash'   => __( 'Service Page not found in trash' ),
        ),
        'taxonomies' => array('category', 'topics'),
        'supports' => array(
          'title',
          'editor',
          'page-attributes',
          'revisions',
          'author',
          'custom-fields'
        ),
        'public' => true,
        'show_in_rest' => true,
        'rest_base' => 'services',
        'has_archive' => true,
        'show_in_nav_menus' => true,
        'menu_icon' => 'dashicons-admin-generic',
        'hierarchical' => true,
        'query_var' => true,
        'rewrite' => array(
          'slug' => 'services',
          'with_front' => false,
        ),
      )
    );
  }

  function create_phila_service_updates() {
    register_post_type( 'service_updates',
      array(
        'labels' => array(
          'name' => __( 'Service Updates' ),
          'singular_name' => __( 'Service Update' ),
          'add_new'   => __( 'Add Service Update' ),
          'all_items'   => __( 'All Service Updates' ),
          'add_new_item' => __( 'Add Service Update' ),
          'edit_item'   => __( 'Edit Service Update' ),
          'view_item'   => __( 'View Service Update' ),
          'search_items'   => __( 'Search Service Updates'),
          'not_found'   => __( 'Service Update not found' ),
          'not_found_in_trash'   => __( 'Service Update not found in trash' ),
        ),
      'taxonomies' => array('category'),
      'supports' => array(
        'title',
      ),
      'exclude_from_search' => true,
      'public' => false,
      'show_in_rest' => true,
      'rest_base' => 'service-updates',
      'show_ui' => true,
      'has_archive' => false,
      'menu_icon' => 'dashicons-warning',
      'hierarchical' => false,
      'rewrite' => array(
        'slug' => 'service-updates',
        'with_front' => false,
        ),
      )
    );
  }

  function create_phila_annoucement() {
    register_post_type( 'announcement',
      array(
        'labels' => array(
          'name' => __( 'Announcements' ),
          'singular_name' => __( 'Announcement' ),
          'add_new'   => __( 'Add Announcement' ),
          'all_items'   => __( 'All Announcements' ),
          'add_new_item' => __( 'Add Announcement' ),
          'edit_item'   => __( 'Edit Announcements' ),
          'view_item'   => __( 'View Announcements' ),
          'search_items'   => __( 'Search Announcements'),
          'not_found'   => __( 'Announcement not found' ),
          'not_found_in_trash'   => __( 'Announcement entry not found in trash' ),
        ),
        'taxonomies' => array('category', 'post_tag'),
        'supports' => array(
          'title',
          'editor',
          'revisions'
        ),
        'exclude_from_search' => true,
        'show_in_rest'  => true,
        'public' => false,
        'show_ui' => true,
        'has_archive' => false,
        'menu_icon' => 'dashicons-id',
        'hierarchical' => false,
        'rewrite' => array(
          'slug' => 'announcements',
        ),
      )
    );
  }

 function create_phila_news_post() {
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
      'taxonomies' => array(
        'category',
        'topics'
      ),
      'public' => true,
      'has_archive' => true,
      'show_in_rest' => true,
      'show_in_menu'  => false,
      'rest_base' => 'news',
      'menu_icon' => 'dashicons-media-document',
      'hierarchical' => false,
      'supports'  => array(
        'title',
        'editor',
        'thumbnail',
        'revisions',
        'author'
      ),
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
          'singular_name' => __( 'Document Page' ),
          'add_new'   => __( 'Add Document Page' ),
          'all_items'   => __( 'All Document Pages' ),
          'add_new_item' => __( 'Add New Document Page' ),
          'edit_item'   => __( 'Edit Document Page' ),
          'view_item'   => __( 'View Document Page' ),
          'search_items'   => __( 'Search Document Pages' ),
          'not_found'   => __( 'Document Page Not Found' ),
          'not_found_in_trash'   => __( 'Document Page not found in trash' ),
        ),
        'taxonomies' => array(
          'category',
        ),
        'supports' => array(
          'title',
          'revisions',
          'author'
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'rest_base' => 'documents',
        'menu_icon' => 'dashicons-media-text',
        'hierarchical' => false,
        'rewrite' => array(
          'slug' => 'documents',
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
        'taxonomies' => array(
          'category',
          'post_tag'
        ),
        'supports' => array(
          'editor',
          'title',
          'revisions',
          'thumbnail',
          'author'
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_menu'  => false,
        'show_in_rest' => true,
        'rest_base' => 'phila-post',
        'menu_icon' => 'dashicons-admin-post',
        'hierarchical' => false,
        'rewrite' => array(
            'slug' => 'phila-posts',
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
        'supports' => array(
          'editor',
          'title',
          'revisions',
          'author'
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_menu'  => false,
        'show_in_rest' => true,
        'rest_base' => 'press-releases',
        'menu_icon' => 'dashicons-editor-justify',
        'hierarchical' => false,
        'rewrite' => array(
            'slug' => 'press-releases',
            'with_front' => false,
        ),
      )
    );
  }
  function create_phila_staff_directory() {
    register_post_type( 'staff_directory',
      array(
        'labels' => array(
          'name' => __( 'Staff Members' ),
          'singular_name' => __( 'Staff Member' ),
          'add_new'   => __( 'Add Staff Member' ),
          'all_items'   => __( 'All Staff Members' ),
          'add_new_item' => __( 'Add Staff Member' ),
          'edit_item'   => __( 'Edit Staff Members' ),
          'view_item'   => __( 'View Staff Members' ),
          'search_items'   => __( 'Search Staff Members'),
          'not_found'   => __( 'Staff Member not found' ),
          'not_found_in_trash'   => __( 'Staff Member entry not found in trash' ),
        ),
        'taxonomies' => array('category'),
        'supports' => array(
          'revisions',
          'thumbnail'
        ),
        'exclude_from_search' => true,
        'public' => false,
        'show_ui' => true,
        'show_in_rest' => true,
        'rest_base' => 'staff',
        'has_archive' => false,
        'menu_icon' => 'dashicons-id',
        'hierarchical' => false,
        'rewrite' => array(
          'slug' => 'staff-directory',
          'with_front' => false,
        ),
      )
    );
  }
}

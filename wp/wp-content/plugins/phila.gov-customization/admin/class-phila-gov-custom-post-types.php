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


    add_action( 'init', array( $this, 'create_phila_service_updates' ), 1 );

    add_action( 'init', array( $this, 'create_phila_site_wide_alert' ), 1 );

    add_action( 'init', array( $this, 'create_phila_document' ), 1 );

    add_action( 'init', array( $this, 'create_phila_longform_content' ), 1 );

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

  function create_phila_service_updates() {
    register_post_type( 'service_updates',
      array(
        'labels' => array(
          'name' => __( 'Service updates' ),
          'singular_name' => __( 'Service update' ),
          'add_new'   => __( 'Add service update' ),
          'all_items'   => __( 'All service updates' ),
          'add_new_item' => __( 'Add service update' ),
          'edit_item'   => __( 'Edit service update' ),
          'view_item'   => __( 'View service update' ),
          'search_items'   => __( 'Search service updates'),
          'not_found'   => __( 'Service update not found' ),
          'not_found_in_trash'   => __( 'Service update not found in trash' ),
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
          'name' => __( 'Site-wide alerts' ),
          'singular_name' => __( 'Site-wide alert' ),
          'add_new'   => __( 'Add site-wide alert' ),
          'all_items'   => __( 'All site-wide alerts' ),
          'add_new_item' => __( 'Add site-wide alert' ),
          'edit_item'   => __( 'Edit site-wide alerts' ),
          'view_item'   => __( 'View site-wide alerts' ),
          'search_items'   => __( 'Search site-wide alerts'),
          'not_found'   => __( 'Site-wide alert not found' ),
          'not_found_in_trash'   => __( 'Site-wide alert not found in trash' ),
        ),
        'exclude_from_search' => true,
        'show_in_rest' => true,
        'rest_base' => 'alerts',
        'public' => false,
        'show_ui' => true,
        'has_archive' => false,
        'menu_icon' => 'dashicons-megaphone',
        'hierarchical' => true,
        'query_var' => true,
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
          'name' => __( 'Document pages' ),
          'singular_name' => __( 'Document page' ),
          'add_new'   => __( 'Add document page' ),
          'all_items'   => __( 'All document pages' ),
          'add_new_item' => __( 'Add document page' ),
          'edit_item'   => __( 'Edit document page' ),
          'view_item'   => __( 'View document page' ),
          'search_items'   => __( 'Search document pages' ),
          'not_found'   => __( 'Document page Not Found' ),
          'not_found_in_trash'   => __( 'Document page not found in trash' ),
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
        'has_archive' => false,
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
          'name' => __( 'Staff members' ),
          'singular_name' => __( 'Staff member' ),
          'add_new'   => __( 'Add staff member' ),
          'all_items'   => __( 'All staff' ),
          'add_new_item' => __( 'Add staff member' ),
          'edit_item'   => __( 'Edit staff members' ),
          'view_item'   => __( 'View staff members' ),
          'search_items'   => __( 'Search staff members'),
          'not_found'   => __( 'Staff member not found' ),
          'not_found_in_trash'   => __( 'Staff member entry not found in trash' ),
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

  function create_phila_longform_content() {
    register_post_type( 'longform_content',
      array(
        'labels' => array(
          'name' => __( 'Long-form pages' ),
          'singular_name' => __( 'Long-form page' ),
          'add_new'   => __( 'Add long-form page' ),
          'all_items'   => __( 'All long-form pages' ),
          'add_new_item' => __( 'Add longform page' ),
          'edit_item'   => __( 'Edit longform page' ),
          'view_item'   => __( 'View longform page' ),
          'search_items'   => __( 'Search longform pages' ),
          'not_found'   => __( 'Longform page Not Found' ),
          'not_found_in_trash'   => __( 'Longform page not found in trash' ),
        ),
        'taxonomies' => array(
          'category',
        ),
        'supports' => array(
          'title',
          'revisions',
          'editor',
          'page-attributes',
          'thumbnail'
        ),
        'public' => true,
        'has_archive' => false,
        'show_in_rest' => true,
        'show_in_nav_menus' => true,
        'rest_base' => 'publications',
        'menu_icon' => 'dashicons-media-text',
        'hierarchical' => true,
        'query_var' => true,
        'rewrite' => array(
          'slug' => 'publications',
          'with_front' => false,
        ),
      )
    );
  }
}

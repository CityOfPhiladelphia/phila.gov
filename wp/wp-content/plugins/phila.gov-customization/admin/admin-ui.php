<?php
/**
 *
 * @link https://github.com/CityOfPhiladelphia/phila.gov-customization
 *
 * @package phila-gov_customization
 */

/**
 * Hook into Restrict Categories plugin and allow custom post types to be filtered through posts()
 *
 * @since 0.5.9
 * @link https://github.com/CityOfPhiladelphia/phila.gov-customization, https://wordpress.org/plugins/restrict-categories/
 *
 * @package phila-gov_customization
 */

add_action( 'admin_init', 'phila_restrict_categories_custom_loader', 1 );

function phila_restrict_categories_custom_loader() {

  class RestrictCategoriesCustom extends RestrictCategories {

  public function  __construct() {

    if ( is_admin() ) {
     $post_type = get_post_types();

     foreach ($post_type as $post) {
       add_action( 'admin_init', array( &$this, 'posts' ) );
      }
    }
   }

  }

  $custom_restrict_categories_load = new RestrictCategoriesCustom();

}

/**
 * Allow draft pages to be in the "Parent" attribute dropdown
 *
 * @since   0.8.5
 */

add_filter('page_attributes_dropdown_pages_args', 'phila_allow_more_dropdown_pages_args', 1, 1);

function phila_allow_more_dropdown_pages_args($dropdown_args) {

  $dropdown_args['post_status'] = array('publish','draft', 'private', 'pending');

  return $dropdown_args;
}

/**
* Add query argument for selecting pages to add to a menu
*/
add_filter( 'nav_menu_meta_box_object', 'phila_show_all_pages_menu_selection' );

function phila_show_all_pages_menu_selection( $args ){
  $args->_default_query['post_status'] = array( 'publish','private', 'draft', 'pending' );
  return $args;
}

add_action( 'admin_enqueue_scripts', 'phila_load_admin_media_js', 10, 1 );

function phila_load_admin_media_js( $hook ) {
  wp_register_script( 'all-admin-scripts', plugins_url( 'js/admin.js' , __FILE__, array('jquery-validation') ) );

  wp_register_script( 'jquery-validation', plugins_url('js/jquery.validate.min.js', __FILE__, array( 'jquery') ) );

  wp_localize_script( 'all-admin-scripts', 'searchAjax',
  array(
    'ajaxurl' => admin_url( 'admin-ajax.php' ),
    'ajax_nonce' => wp_create_nonce( 'search-results-update' ),
  )
);

  wp_enqueue_script( 'jquery-validation' );

  wp_enqueue_script( 'all-admin-scripts' );
}

add_action('get_header', 'phila_filter_head');

function phila_filter_head() {
  remove_action('wp_head', '_admin_bar_bump_cb');
}

add_action( 'admin_enqueue_scripts', 'phila_load_admin_css', 11 );

function phila_load_admin_css(){
  wp_register_style( 'phila_admin_css', plugins_url( 'css/admin.css', __FILE__));
  wp_enqueue_style( 'phila_admin_css' );
}

// Set a JS var for philaAllPostTypes, similar to how typenow is set
add_action( 'admin_head', 'phila_all_posts_js_array');

function phila_all_posts_js_array(){
  $philaAllPostTypes = json_encode( array_values( get_post_types( '','names' ) ) );

  echo '<script type="text/javascript"> var	philaAllPostTypes = ' . $philaAllPostTypes . ';</script>';
}

// Set a JS var for phila_WP_User
add_action( 'admin_head', 'phila_get_user_roles');

function phila_get_user_roles(){
  $WP_User = json_encode( array_values( wp_get_current_user()->roles ) );

  echo '<script type="text/javascript"> var	phila_WP_User = ' . $WP_User . ';</script>';

}


/**
 * Move all "advanced" metaboxes above the default editor to allow for custom reordering
 *
 * @since   0.17.7
 */
add_action('edit_form_after_title', 'phila_reorder_meta_boxes');

function phila_reorder_meta_boxes() {
  global $post, $wp_meta_boxes;
  do_meta_boxes(get_current_screen(), 'advanced', $post);
  unset($wp_meta_boxes[get_post_type($post)]['advanced']);
}

add_action( 'admin_bar_menu', 'remove_add_new', 999 );

function remove_add_new( $wp_admin_bar ) {
  $wp_admin_bar->remove_node( 'new-content' );
}


add_action('do_meta_boxes', 'phila_remove_thumbnails_from_pages');

function phila_remove_thumbnails_from_pages() {
  remove_meta_box( 'postimagediv','page','side' );
}

/* Check if user is in role, and if so, unhide create new button */
add_action('admin_head', 'hide_title_add_buttons');

function hide_title_add_buttons() {
  if ( current_user_can( PHILA_ADMIN )  )
  return;

  $user = wp_get_current_user();

  $style = '<style type="text/css" scoped>
  .post-type-service_page .page-title-action,
  .post-type-department_page .page-title-action,
  .post-type-programs .page-title-action { display:none; }';

  foreach($user->caps as $key => $value) {
    if ( $key == 'secondary_service_page_creator' ) {
      $style .= '.post-type-service_page .page-title-action {display: inline-block !important }';
    }
    if ( $key == 'secondary_department_page_creator' ) {
      $style .=  '.post-type-department_page .page-title-action { display:inline-block !important; }';
    }
    if ($key ==  'secondary_program_creator' ) {
      $style .= '.post-type-programs .page-title-action { display:inline-block !important }';
    }
  }

  $style .= '</style>';

  echo $style;
}

/**
 * Ajax: Add Departments parent pages to Appearance -> Menus -> Departments search results, and returns upmost parent of each child page. See admin.js for ajax intercept.
 */
function addDepartmentParent() {

  check_ajax_referer( 'search-results-update', 'security' );

  $response = [];

  $query = new WP_Query(
    array(
      'post__in' => $_POST['postIds'],
      'post_type' => 'department_page',
      'posts_per_page' => 50,
    )
  );

  if ( $query->found_posts > 0 ) {
    foreach ( $query->posts as $post ) {
      if ( $post->post_parent ) {
        $ancestors = get_post_ancestors( $post->ID );
        $root = count( $ancestors ) - 1;
        $parent = get_post( $ancestors[ $root ] );
        $response[ 'p=' . $post->ID ] = $parent->post_title;
      }
    }
  }

  wp_send_json( $response );

}

add_action( 'wp_ajax_addDepartmentParent', 'addDepartmentParent' );
add_action( 'wp_ajax_nopriv_addDepartmentParent', 'addDepartmentParent' );


/**
 * Hooks into users columns and add new column to display which categories this user can access. This is modified version of the same function used by the Restrict Categories plugin.
 *
 * @since   0.22.0
 */
add_filter('manage_users_columns' , 'add_user_retricted_categories_column');

function add_user_retricted_categories_column($columns) {
  return array_merge( $columns, array('user_restricted_cats' => __(' Restricted Categories ')) );
}

add_action( 'manage_users_custom_column', 'user_restricted_category_column_values', 10, 3 );

function user_restricted_category_column_values($val, $column_name, $user_id) {
  $cat_list = '';
  if($column_name == "user_restricted_cats"){
    $defaults = array( 'RestrictCategoriesDefault' );
    // Get the current user in the admin
        $user = new WP_User( $user_id );

      // Get the user role
      $user_cap = $user->roles;

      // Get the user login name/ID
      if ( function_exists( 'get_users' ) )
        $user_login = $user->user_nicename;
      elseif ( function_exists( 'get_users_of_blog' ) )
        $user_login = $user->ID;

      // Get selected categories for Roles
      $settings = get_option( 'RestrictCats_options' );

      // Get selected categories for Users
      $settings_user = get_option( 'RestrictCats_user_options' );

      // For users, strip out the placeholder category, which is only used to make sure the checkboxes work
      if ( is_array( $settings_user ) && array_key_exists( $user_login . '_user_cats', $settings_user ) ) {
        $settings_user[ $user_login . '_user_cats' ] = array_values( array_diff( $settings_user[ $user_login . '_user_cats' ], $defaults ) );
        // Selected categories for User overwrites Roles selection
      if ( is_array( $settings_user ) && !empty( $settings_user[ $user_login . '_user_cats' ] ) ) {
        $cat_list = array();
          // Build the category list
          foreach ( $settings_user[ $user_login . '_user_cats' ] as $category ) {
            $term = get_term_by( 'slug', $category, 'category' );
            $cat_list[] = $term->name;
          }

        }
      }
  }

  return (is_array($cat_list) ? implode(', ' , $cat_list) : '');
}

add_action( 'init', 'phila_change_category_object_label' );

function phila_change_category_object_label() {
    global $wp_taxonomies;
    $labels = &$wp_taxonomies['category']->labels;
    $labels->name = 'Owner';
    $labels->singular_name = 'Owner';
    $labels->add_new = 'Add an Owner';
    $labels->add_new_item = 'Add an Owner';
    $labels->edit_item = 'Edit Owner';
    $labels->new_item = 'Owner';
    $labels->view_item = 'View Owner';
    $labels->search_items = 'Search Owners';
    $labels->not_found = 'No Owners found';
    $labels->not_found_in_trash = 'No Owners found in Trash';
    $labels->all_items = 'All Owners';
    $labels->menu_name = 'Owners';
    $labels->name_admin_bar = 'Owners';
}
/*  Remove admin comment count column */
add_filter('manage_posts_columns', 'remove_posts_count_columns');

function remove_posts_count_columns( $columns ) {
  unset( $columns['comments']);
  return $columns;
}

add_filter( 'manage_media_columns', 'remove_media_columns' );

function remove_media_columns( $columns ) {
  unset( $columns['comments'] );
  return $columns;
}

add_filter( 'admin_body_class', 'phila_add_post_status' );

function phila_add_post_status( $classes ) {
    return $classes . ' ' . get_post_status() . ' ';
}

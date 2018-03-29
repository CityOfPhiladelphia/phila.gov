<?php

if ( class_exists("Phila_Gov_Role_Administration" ) ){
  $phila_role_administration_load = new Phila_Gov_Role_Administration();
}

class Phila_Gov_Role_Administration {

  public function __construct(){

    add_action( 'admin_head', array( $this, 'remove_media_controls' ) );

    add_action( 'admin_head', array( $this, 'remove_parent_page_div' ) );

    add_action( 'init', array( $this, 'abstract_user_role') );

    add_action( 'admin_head', array( $this, 'tinyMCE_edits' ) );

    add_action( 'admin_enqueue_scripts', array( $this, 'administration_admin_scripts'), 1000 );

    add_filter( 'wp_dropdown_users_args', array( $this, 'add_subscribers_to_author_dropdown'), 10, 2 );


  }

  public function get_user_category_from_category_plugin(){
    $cat_list = '';
    $defaults = array( 'RestrictCategoriesDefault' );
    // Get the current user in the admin
    $user = new WP_User(wp_get_current_user()->ID);

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

        // Build the category list
        foreach ( $settings_user[ $user_login . '_user_cats' ] as $category ) {
          $term = get_term_by( 'slug', $category, 'category' );
          $cat_list[] = $term->term_id;
        }

      }
    }
    return (is_array($cat_list) ? implode(', ' , $cat_list) : '');
  }

  /**
  * Removes "Add Media" Button from the editor.
  * @since 0.13.0
  */
  function remove_media_controls() {
    if ( ! current_user_can( PHILA_ADMIN ) ){
      remove_action( 'media_buttons', 'media_buttons' );
    }
  }

  /**
   * Removes Page Attribute section from editor this means users don't need to worry about IA (which is handled by Editors) and in the event the user doesn't have access to the parent page category (like in the case of services) we don't have to worry about the pages becoming un-nested.
   */

  function remove_parent_page_div() {
    if ( ! current_user_can( PHILA_ADMIN )  ){
      remove_meta_box('pageparentdiv', 'service_page', 'side');
      remove_meta_box('pageparentdiv', 'department_page', 'side');
      remove_meta_box('pageparentdiv', 'programs', 'side');
    }
  }

  /**
  * Removes unwanted buttons from TinyMCE
  * @since 0.13.0
  */
  function remove_top_tinymce_button( $buttons ){
    $remove = array( 'alignleft', 'aligncenter', 'alignright', 'wp-more', 'fullscreen', 'strikethrough' );

    return array_diff( $buttons, $remove );
   }

  function remove_bottom_tinymce2_buttons( $buttons ){
    $remove = array( 'underline', 'alignjustify', 'forecolor', 'outdent', 'indent' );

    return array_diff( $buttons, $remove );
   }
   function tinyMCE_edits(){

     if ( ! current_user_can( PHILA_ADMIN ) ){
       add_filter( 'mce_buttons',  array( $this, 'remove_top_tinymce_button' ) );

       add_filter( 'mce_buttons_2', array( $this,'remove_bottom_tinymce2_buttons') );

     }
   }
  /**
   * Modifies args sent to page attributes dropdown. Only allows department authors to see pages in their department category.
   *
   * @since    0.14.0
   */
  public function change_dropdown_args( $dropdown_args, $post ) {
    $current_user_cat = $this->get_user_category_from_category_plugin();
    $include = '';

    $args = array(
      'post_type'  => get_post_type(),
      'posts_per_page'   => -1,
      'post_status' => 'any',
      'category'  => array($current_user_cat),
      'offset'           => 0,
    );

    $pages = get_posts( $args );
    foreach($pages as $page){
      $include = $page->ID . "," . $include;
    }
    $include = rtrim($include, ', ');
    $dropdown_args['include'] = $include;
    return $dropdown_args;

  }
  /**
   * Ensures the change_dropdown_args runs at the correct time.
   *
   * @since 0.14.0
   */

  public function abstract_user_role(){
    if ( ! current_user_can( PHILA_ADMIN )  ){

      add_filter( 'page_attributes_dropdown_pages_args', array( $this, 'change_dropdown_args' ), 9, 2 );

    }
  }

  /**
   * Add custom js to force category selection for Department Author roles
   *
   * @since   0.11.0
   */

  function administration_admin_scripts() {
    if ( ! current_user_can( PHILA_ADMIN ) ){

      wp_enqueue_script( 'admin-department-author-script', plugins_url( 'js/admin-department-author.js' , __FILE__ ) );

      wp_register_style( 'admin-department-author', plugins_url( 'css/admin-department-author.css' , __FILE__  ) );

      wp_enqueue_style( 'admin-department-author' );

      $user = wp_get_current_user();

      if ( array_key_exists( 'primary_admin_read_only',  $user->caps ) ) {
        wp_register_style( 'admin-read-only', plugins_url( 'css/read-only-user.css' , __FILE__  ) );
        wp_enqueue_style( 'admin-read-only' );

      }

    }
  }

  function add_subscribers_to_author_dropdown( $query_args, $r ) {

    $query_args['who'] = '';
    return $query_args;

  }

}

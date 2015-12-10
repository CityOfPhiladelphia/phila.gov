<?php

if ( class_exists("PhilaGovRoleAdministration" ) ){
  $phila_role_administration_load = new PhilaGovRoleAdministration();
}

class PhilaGovRoleAdministration {

  public function __construct(){

    //remove our unwanted widgets
    add_action( 'after_setup_theme', array( $this, 'remove_others_widgets' ), 11 );

    //adds the correct menu to admin menus
    add_action( 'admin_menu', array( $this, 'add_department_menu' ) );

    //no media uploads
    add_action( 'admin_head', array( $this, 'remove_media_controls' ) );

    add_action( 'admin_head', array( $this, 'set_jquery_vars' ) );

    add_action( 'init', array( $this, 'abstract_user_role') );

    add_action( 'admin_head', array( $this, 'add_meta_data') );

    add_action( 'admin_head', array( $this, 'remove_meta_boxes' ) );

    //add_action( 'do_meta_boxes', array( $this, 'remove_role_metabox' ) );

    add_action('admin_head', array($this, 'tinyMCE_edits' ) );

    add_action( 'admin_enqueue_scripts', array($this, 'administration_admin_scripts'), 1000 );
  }

  /**
   * Outputs all categories into an array w/ just slugs.
   *
   * @since 0.11.0
   * @return $cat_slugs array Returns an array of all categories.
   */
  public function get_categories(){
    $categories_args = array(
        'type'                     => 'post',
        'child_of'                 => 0,
        'parent'                   => '',
        'orderby'                  => 'name',
        'order'                    => 'ASC',
        'hide_empty'               => 1,
        'hierarchical'             => 0,
        'taxonomy'                 => 'category',
        'pad_counts'               => false
    );

    $categories = get_categories( $categories_args );

    $cat_slugs = [];

    //loop through and push slugs to $cat_slugs
    foreach( $categories as $category ){
      array_push( $cat_slugs, $category->slug );
    }
    //add category slugs to their own array
    return $cat_slugs;
  }

  /**
   * Returns a match of every category this user has.
   *
   * @since 0.11.0
   * @uses get_categories() Outputs all categories into an array w/ just slugs.
   * @uses wp_get_current_user()   https://codex.wordpress.org/Function_Reference/wp_get_current_user
   * @return $cat_slugs array Returns an array of all categories.
   */

    public function get_current_user_category() {

      $cat_slugs = $this->get_categories();

      if ( is_user_logged_in() && ! current_user_can( PHILA_ADMIN ) ){
        //define current_user, we should only do this when we are logged in
        $user = wp_get_current_user();
        $all_user_roles = $user->roles;
        $all_user_roles_to_cats = str_replace('_', '-', $all_user_roles);

        //matches rely on Category SLUG and user role SLUG matching
        //if there are matches, then you have a secondary role that should not be allowed to see other's menus, etc.
        $current_user_cat_assignment = array_intersect( $all_user_roles_to_cats, $cat_slugs );

        return $current_user_cat_assignment;
      }
    }

  /**
   * Outputs an array of Term Objects that correspond to secondary roles applied to the logged in user.
   *
   * @since 0.12.0
   * @uses get_current_user_category() Outputs all categories into an array w/ just slugs.
   */

   public function secondary_roles(){

    $current_user_cat_assignment = $this->get_current_user_category();

    if ( is_user_logged_in() && ! current_user_can( PHILA_ADMIN )  ){
      if( count( $current_user_cat_assignment ) > 1 ) {
        $assigned_roles = [];
        foreach ( $current_user_cat_assignment as $cat_assignment ) {
          array_push( $assigned_roles, get_category_by_slug( $cat_assignment ) );
        }
        //retuns an array of obejcts
        return $assigned_roles;

      }elseif( count( $current_user_cat_assignment ) == 1 ) {

        $assigned_role = get_category_by_slug( $current_user_cat_assignment[1] );
        //returns a single object
        return $assigned_role;

      }else {
          return null;
        }
      }
    }
  /**
	 * Removes widgets that don't belong to this category (or categories)
	 *
	 * @since 0.11.0
   * @uses secondary_roles() Outputs an array of Term Objects that correspond to secondary roles applied to the logged in user.
	  */
    public function remove_others_widgets(){

      if ( ! current_user_can( PHILA_ADMIN ) ){

        $current_user_role = $this->secondary_roles();

        if ( ! $current_user_role == null ) {
          //remove all sidebars
          remove_action( 'widgets_init', 'phila_gov_widgets_init', 10 );

          //re-register the sidebar we just unregistered...
          //TODO see if there is a better way to do this. Seems hacky.
          //also, fails if slug name changes...
          if ( is_array($current_user_role) ) {
            foreach($current_user_role as $user_role) {
              register_sidebar( array(
            		'name'          => __( $user_role->name . ' Sidebar', 'phila-gov' ),
            		'id'            => 'sidebar-' . $user_role->slug . '-' . $user_role->term_id,
            		'description'   => '',
            		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            		'after_widget'  => '</aside>',
            		'before_title'  => '<h1 class="widget-title">',
            		'after_title'   => '</h1>',
            	) );
            }
          }else {
            register_sidebar( array(
              'name'          => __( $current_user_role->name . ' Sidebar', 'phila-gov' ),
              'id'            => 'sidebar-' . $current_user_role->slug . '-' . $current_user_role->term_id,
              'description'   => '',
              'before_widget' => '<aside id="%1$s" class="widget %2$s">',
              'after_widget'  => '</aside>',
              'before_title'  => '<h1 class="widget-title">',
              'after_title'   => '</h1>',
            ) );
          }
        }
      }
    }

  /**
   * Sets variables to use in jQuery
   *
   * @since 0.11.0
   * @uses secondary_roles() Outputs an array of Term Objects that correspond to secondary roles applied to the logged in user.
    */

  function set_jquery_vars(){
    if ( is_user_logged_in() && ! current_user_can( PHILA_ADMIN )){
      $screen = get_current_screen();

      $category_object = $this->secondary_roles();
        //only add these if we are on the right admin sreens
        if ($screen->base == 'nav-menus' || $screen->base == 'widgets'){
          if( is_array($category_object) ){
              echo '<div id="menu-id" style="display: none;">';
              foreach( $category_object as $cat ){
                    print_r('locations-menu-'. $cat->cat_ID . ' ');
                  }
                echo '</div>';
                echo '<div id="menu-name" style="display: none;">';
                  foreach( $category_object as $cat ){
                      print_r($cat->cat_name . ' ');
                  }
              echo '</div>';
          } else{
            echo '<div id="menu-id" style="display: none;">';
                  print_r('locations-menu-'. $category_object->term_id);
            echo '</div>';
            echo '<div id="menu-name" style="display: none;">';
              $current_user_cat_assignment = $this->get_current_user_category();
              $cat_object = get_category_by_slug($current_user_cat_assignment[1]);
              if ( isset( $cat_object->name ) ){
                $name = $cat_object->name;
                print_r($name);
              }
            echo '</div>';
        }
      }
    }
  }
  /**
	 * Gets the menu this user should see and passes the value to add_submenu_page
	 *
	 * @since 0.11.0
   * @uses get_category_id() Outputs the current category ID.
   * @uses add_submenu_page https://codex.wordpress.org/Function_Reference/add_submenu_page
   * @uses get_nav_menu_locations() https://developer.wordpress.org/reference/functions/get_nav_menu_locations/
	  */

  public function add_department_menu(){
    if ( ! current_user_can( PHILA_ADMIN ) ){

      $category_object = $this->secondary_roles();

      $menu_locations = get_nav_menu_locations();

      if ( is_array( $category_object ) ) {
        //let's default to the first one
        $key = 'menu-' . $category_object[0]->term_id;

        // Add Menus as a Department Site submenu
        add_submenu_page( 'edit.php?post_type=department_page', 'Nav Menu', 'Nav Menu', 'edit_posts', 'nav-menus.php?action=edit&menu='. $key );
      }else {
        $key = 'menu-' . $category_object->term_id;

        $current_menu_value = $menu_locations[$key];

        // Add Menus as a Department Site submenu
        add_submenu_page( 'edit.php?post_type=department_page', 'Nav Menu', 'Nav Menu', 'edit_posts', 'nav-menus.php?action=edit&menu='. $current_menu_value );

      }
    }else{
      add_submenu_page( 'edit.php?post_type=department_page', 'Nav Menu', 'Nav Menu', 'edit_posts', 'nav-menus.php' );
    }
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
  * Removes unwanted butons from TinyMCE
  * @since 0.13.0
  */

  function remove_top_tinymce_button( $buttons ){
    $remove = array( 'alignleft', 'aligncenter', 'alignright', 'wp-more', 'fullscreen', 'wp-more', 'wp_adv', 'strikethrough' );

    return array_diff( $buttons, $remove );
   }

  function remove_bottom_tinymce2_buttons( $buttons ){
    $remove = array( 'pastetext', 'underline', 'alignjustify', 'forecolor', 'outdent', 'indent', 'removeformat' );

  	return array_diff( $buttons, $remove );
   }
   function tinyMCE_edits(){
     if ( ! current_user_can( PHILA_ADMIN ) ){
       add_filter( 'mce_buttons',  array( $this, 'remove_top_tinymce_button' ) );

       add_filter( 'mce_buttons_2', array( $this,'remove_bottom_tinymce2_buttons')  );
     }
   }
  /**
   * Modifies args sent to page attributes dropdown. Only allows department authors to see pages in their deparment category.
   *
   * @since    0.14.0
   */
  public function change_dropdown_args( $dropdown_args, $post ) {

    $current_user_cat = $this->get_current_user_category();

      $dropdown_args = array(
        'post_type'        => $post->post_type,
        'exclude_tree'     => $post->ID,
        'selected'         => $post->post_parent,
        'name'             => 'parent_id',
        'show_option_none' => __('(no parent)'),
        'sort_column'      => 'menu_order',
        'echo'             => 0,
        'meta_key'         =>'_category',
        'meta_value'       => $current_user_cat
      );

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
	 * Adds category as metadata for filtering the page attribute dropdown box.
	 *
	 * @since 0.14.0
	 *
	 */
  public function add_meta_data( $post ){
    global $post;
    if ( isset( $post->ID ) ){
        $categories = get_the_category( $post->ID );

        if ( $post->post_type == 'department_page' ){
            foreach ( $categories as $cat ){
              update_post_meta( $post->ID, '_category', $cat->slug );
            }
        }
    }
  }
  /**
   * Hides page attributes meta box for non-admins.
   *
   * @since 0.15.6
   *
   */
  public function remove_meta_boxes(){
    if ( is_admin() ) {
      if ( ! current_user_can( PHILA_ADMIN ) ) {
        remove_meta_box('pageparentdiv', 'page', 'side');
        remove_meta_box('pageparentdiv', 'department_page', 'side');
        remove_meta_box('news-admin-only', 'news_post', 'side');
      }
    }
  }
  /**
   * Hides per-page role editor for all admins.
   *
   * @since 0.17.7
   *
   */
  public function remove_role_metabox(){
    if ( is_admin() ) {
      $post_types = get_post_types( );
      foreach ( $post_types as $post_type ) {
        remove_meta_box( 'wpfront-user-role-editor-role-permission', $post_type, 'advanced' );
       }
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

    }
  }
}

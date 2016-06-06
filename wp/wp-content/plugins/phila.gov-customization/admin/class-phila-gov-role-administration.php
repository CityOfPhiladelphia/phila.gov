<?php

if ( class_exists("Phila_Gov_Role_Administration" ) ){
  $phila_role_administration_load = new Phila_Gov_Role_Administration();
}

class Phila_Gov_Role_Administration {

  public function __construct(){

    //no media uploads
    add_action( 'admin_head', array( $this, 'remove_media_controls' ) );

    add_action( 'init', array( $this, 'abstract_user_role') );

    add_action( 'admin_head', array( $this, 'add_meta_data') );

    add_action( 'admin_head', array( $this, 'remove_meta_boxes' ) );

    add_action('admin_head', array( $this, 'tinyMCE_edits' ) );

    add_filter( 'default_hidden_meta_boxes',  array( $this, 'phila_hide_non_admin_meta_boxes'), 10, 2 );

    add_action( 'admin_enqueue_scripts', array( $this, 'administration_admin_scripts'), 1000 );

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

    if ( is_user_logged_in() && !current_user_can( PHILA_ADMIN )  ){

      if( count( $current_user_cat_assignment ) > 1 ) {

        $assigned_roles = [];

        foreach ( $current_user_cat_assignment as $cat_assignment ) {
          array_push( $assigned_roles, get_category_by_slug( $cat_assignment ) );
        }

        //returns an array of objects
        return $assigned_roles;

      }elseif( count( $current_user_cat_assignment ) == 1 ) {


        //handle key assignment
        $assigned_role = get_category_by_slug( reset( $current_user_cat_assignment ) );

        //returns a single object
        return $assigned_role;

      }else {
          return null;
        }
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

      add_filter('wp_dropdown_users', array( $this, 'display_users_in_same_role' ) );

      add_filter( 'get_terms_args', array( $this, 'show_all_tags_on_phila_post' ) );

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
        remove_meta_box('news-admin-only', 'news_post', 'side');

      }
    }
  }


  public function phila_hide_non_admin_meta_boxes( $hidden, $screen ) {
    if ( ! current_user_can( PHILA_ADMIN ) ){

      return array( 'categorydiv');
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

  /**
   * On phila_posts only allow users to change the post author to users in the same secondary role.
   *
   * @since   0.22.0
   * @return $output The filtered author dropdown
   */
  function display_users_in_same_role( $output ){
    global $post;

    global $current_user;

    $secondary_role_name = $current_user->roles;
    $user_role = array_shift($secondary_role_name);

    $users = get_users( array(
      'role__in' => $secondary_role_name,
      )
    );

    $output = "<select id=\"post_author_override\" name=\"post_author_override\" class=\"\">";

    foreach($users as $user) {
      $sel = ($post->post_author == $user->ID)?"selected='selected'":'';
      $output .= '<option value="'.$user->ID.'"'.$sel.'>'.$user->display_name.'</option>';
    }
    $output .= "</select>";

    return $output;
  }


  /**
   * Display all tags on post tag, instead of 'popluar'
   *
   * @since   0.22.0
   * @return $output The filtered author dropdown
   */
  function show_all_tags_on_phila_post ( $args ) {
    if ( is_admin() ) {
      if ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_POST['action'] ) && $_POST['action'] === 'get-tagcloud' ) {;
        $args['number'] == 90000;
        $args['hide_empty'] = false;
      }
    }
    return $args;
  }

  function check_assigned_role(){
    if ( is_admin() ){
      $current_user_cat = $this->get_current_user_category();
    }

  }

}

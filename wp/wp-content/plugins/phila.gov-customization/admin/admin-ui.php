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

add_filter('page_attributes_dropdown_pages_args', 'phila_allow_draft_dropdown_pages_args', 1, 1);

function phila_allow_draft_dropdown_pages_args($dropdown_args) {

    $dropdown_args['post_status'] = array('publish','draft', 'private');

    return $dropdown_args;
}

add_action( 'admin_enqueue_scripts', 'phila_load_admin_media_js', 1 );

function phila_load_admin_media_js(){
  wp_enqueue_script( 'jquery-validation', plugins_url('js/jquery.validate.min.js', __FILE__, array( 'jQuery') ) );

  wp_enqueue_script( 'all-admin-scripts', plugins_url( 'js/admin.js' , __FILE__, array('jquery-validation') ) );
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

/**
 * Move all "advanced" metaboxes above the default editor to allow for custom reordering
 *
 * Specifically in use on Service Pages
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

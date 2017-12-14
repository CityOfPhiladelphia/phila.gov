<?php
/**
 * Add custom taxonomies
 *
 * Additional custom taxonomies can be defined here
 * http://codex.wordpress.org/Function_Reference/register_taxonomy
 *
 * @link https://github.com/CityOfPhiladelphia/phila.gov-customization
 *
 * @package phila-gov_customization
 */

if (class_exists("Phila_Gov_Custom_Taxonomies") ){
  $phila_gov_tax = new Phila_Gov_Custom_Taxonomies();
}
class Phila_Gov_Custom_Taxonomies {

  public function __construct(){
    add_action( 'init', array($this, 'add_service_type') );
    add_action( 'init', array($this, 'add_media_type') );
    add_action( 'init', array($this, 'add_media_author') );

  }

  function add_service_type() {
    // Service Pages
    register_taxonomy('service_type',
      array(
        'service_page'
      ),
      array(
      'hierarchical' => true,
      'labels' => array(
        'name' => _x( 'Service Type', 'phila-gov'),
        'singular_name' => _x( 'Service Type', 'phila-gov'),
        'menu_name' =>     __('Service Types'),
        'search_items' =>  __( 'Search Service Types' ),
        'all_items' =>     __( 'All Service Types' ),
        'edit_item' =>     __( 'Edit Service Type' ),
        'update_item' =>   __( 'Update Service Type' ),
        'add_new_item' =>  __( 'Add New Service Type' ),
        'new_item_name' => __( 'New Service Type Name' ),
        'menu_name' =>     __( 'Service Types' ),
      ),
      'public' => true,
      'show_admin_column' => true,
      'rewrite' => array(
        'slug' => 'service-types',
        'with_front' => false,
      ),
    ));
  }
  function add_media_type() {
    // Service Pages
    register_taxonomy('media_type',
      array(
        'attachment'
      ),
      array(
      'hierarchical' => true,
      'labels' => array(
        'name' => _x( 'Media Type', 'phila-gov'),
        'singular_name' => _x( 'Media Type', 'phila-gov'),
        'menu_name' =>     __('Media Types'),
        'search_items' =>  __( 'Search Media Types' ),
        'all_items' =>     __( 'All Media Types' ),
        'edit_item' =>     __( 'Edit Media Type' ),
        'update_item' =>   __( 'Update Media Type' ),
        'add_new_item' =>  __( 'Add New Media Type' ),
        'new_item_name' => __( 'New Media Type Name' ),
        'menu_name' =>     __( 'Media Types' ),
      ),
      'public' => true,
      'show_admin_column' => true,
      'rewrite' => array(
        'slug' => 'media-types',
        'with_front' => false,
      ),
    ));
  }
  function add_media_author() {
    // Service Pages
    register_taxonomy('media_author',
      array(
        'attachment'
      ),
      array(
      'hierarchical' => true,
      'labels' => array(
        'name' => _x( 'Media Author', 'phila-gov'),
        'singular_name' => _x( 'Media Author', 'phila-gov'),
        'menu_name' =>     __('Media Author'),
        'search_items' =>  __( 'Search Media Author' ),
        'all_items' =>     __( 'All Media Authors' ),
        'edit_item' =>     __( 'Edit Media Author' ),
        'update_item' =>   __( 'Update Media Author' ),
        'add_new_item' =>  __( 'Add New Media Author' ),
        'new_item_name' => __( 'New Media Author Name' ),
        'menu_name' =>     __( 'Media Author' ),
      ),
      'public' => true,
      'show_admin_column' => true,
      'rewrite' => array(
        'slug' => 'media-author',
        'with_front' => false,
      ),
    ));
  }
}

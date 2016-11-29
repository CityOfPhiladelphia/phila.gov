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
    add_action( 'init', array($this, 'add_custom_taxonomies') );
  }

  function add_custom_taxonomies() {
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
}

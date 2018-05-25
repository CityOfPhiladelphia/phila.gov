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
    add_action( 'init', array($this, 'service_type') );
    add_action( 'init', array($this, 'audiences') );
    add_action( 'init', array($this, 'media_type') );
    add_action( 'init', array($this, 'media_author') );
    add_action( 'init', array($this, 'event_tags') );

  }

  function service_type() {

    register_taxonomy('service_type',
      array(
        'service_page',
        'programs'
      ),
      array(
      'hierarchical' => true,
      'labels' => array(
        'name' => _x( 'Service Type', 'phila-gov'),
        'singular_name' => _x( 'Service Type', 'phila-gov'),
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
      'show_in_rest' => true,
      'rewrite' => array(
        'slug' => 'service-types',
        'with_front' => false,
      ),
    ));
  }

  function audiences() {
    // Program pages
    register_taxonomy('audience',
      array(
        'programs',
        'service_page'
      ),
      array(
      'hierarchical' => true,
      'labels' => array(
        'name' => _x( 'Audiences', 'phila-gov'),
        'singular_name' => _x( 'Audience', 'phila-gov' ),
        'search_items' =>  __( 'Search Audiences' ),
        'all_items' =>     __( 'All Audiences' ),
        'edit_item' =>     __( 'Edit Audience' ),
        'update_item' =>   __( 'Update Audience' ),
        'add_new_item' =>  __( 'Add New Audience' ),
        'new_item_name' => __( 'New Audience' ),
        'menu_name' =>     __( 'Audiences' ),
      ),
      'public' => true,
      'show_admin_column' => true,
      'show_in_rest' => true,
      'rewrite' => array(
        'slug' => 'audiences',
        'with_front' => false,
      ),
    ));
  }


  function media_type() {

    register_taxonomy('media_type',
      array(
        'attachment'
      ),
      array(
      'hierarchical' => true,
      'labels' => array(
        'name' => _x( 'Media Type', 'phila-gov'),
        'singular_name' => _x( 'Media Type', 'phila-gov'),
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
  function media_author() {
    register_taxonomy('media_author',
      array(
        'attachment'
      ),
      array(
      'hierarchical' => true,
      'labels' => array(
        'name' => _x( 'Media Author', 'phila-gov'),
        'singular_name' => _x( 'Media Author', 'phila-gov'),
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
  function event_tags() {
    register_taxonomy('event_tags',
      array(
        'post',
      ),
      array(
      'hierarchical' => true,
      'labels' => array(
        'name' => _x( 'Event tag', 'phila-gov'),
        'singular_name' => _x( 'Event tag', 'phila-gov'),
        'search_items' =>  __( 'Search Event tag' ),
        'all_items' =>     __( 'All Event tags' ),
        'edit_item' =>     __( 'Edit Event tag' ),
        'update_item' =>   __( 'Update Event tag' ),
        'add_new_item' =>  __( 'Add New Event tag' ),
        'new_item_name' => __( 'New Event tag Name' ),
        'menu_name' =>     __( 'Event tag' ),
      ),
      'public' => true,
      'show_admin_column' => true,
      'rewrite' => array(
        'slug' => 'event-tag',
        'with_front' => false,
      ),
    ));
  }
}

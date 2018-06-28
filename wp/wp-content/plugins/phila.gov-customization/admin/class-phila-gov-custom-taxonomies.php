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
    add_action( 'init', array($this, 'hierarchical_tags') );
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

  /*
   * Relabel tags
  */
  function hierarchical_tags() {

    $labels = array(
    'name'                       => _x( 'Tags', 'Tags', 'hierarchical_tags' ),
    'singular_name'              => _x( 'Tag', 'Tag', 'hierarchical_tags' ),
    'menu_name'                  => __( 'Tags', 'hierarchical_tags' ),
    'all_items'                  => __( 'All Tags', 'hierarchical_tags' ),
    'parent_item'                => __( 'Parent Tag', 'hierarchical_tags' ),
    'parent_item_colon'          => __( 'Parent Tag:', 'hierarchical_tags' ),
    'new_item_name'              => __( 'New Tag Name', 'hierarchical_tags' ),
    'add_new_item'               => __( 'Add New Tag', 'hierarchical_tags' ),
    'edit_item'                  => __( 'Edit Tag', 'hierarchical_tags' ),
    'update_item'                => __( 'Update Tag', 'hierarchical_tags' ),
    'view_item'                  => __( 'View Tag', 'hierarchical_tags' ),
    'separate_items_with_commas' => __( 'Separate tags with commas', 'hierarchical_tags' ),
    'add_or_remove_items'        => __( 'Add or remove tags', 'hierarchical_tags' ),
    'choose_from_most_used'      => __( 'Choose from the most used', 'hierarchical_tags' ),
    'popular_items'              => __( 'Popular Tags', 'hierarchical_tags' ),
    'search_items'               => __( 'Search Tags', 'hierarchical_tags' ),
    'not_found'                  => __( 'Not Found', 'hierarchical_tags' ),
  );

    register_taxonomy( 'post_tag', 'post', array(
      'hierarchical'              => true,
      'query_var'                 => 'tag',
      'labels'                    => $labels,
      'public'                    => true,
      'show_ui'                   => true,
      'show_admin_column'         => true,
      '_builtin'                  => true,
    ) );
  }
}

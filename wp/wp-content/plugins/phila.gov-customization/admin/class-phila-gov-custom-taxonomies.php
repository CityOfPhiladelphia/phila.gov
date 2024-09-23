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
    add_action( 'init', array($this, 'media_category') );
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
      'capabilities' => array(
        'manage_terms'	=>	'manage_service-types',
        'edit_terms'	=>	'edit_service-types',
        'delete_terms'	=>	'delete_service-types',
        'assign_terms'	=>	'assign_service-types',
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
        'search_items' =>  __( 'Search audiences' ),
        'all_items' =>     __( 'All audiences' ),
        'edit_item' =>     __( 'Edit audience' ),
        'update_item' =>   __( 'Update audience' ),
        'add_new_item' =>  __( 'Add New audience' ),
        'new_item_name' => __( 'New audience' ),
        'menu_name' =>     __( 'Audiences' ),
      ),
      'public' => true,
      'show_admin_column' => true,
      'show_in_rest' => true,
      'rewrite' => array(
        'slug' => 'audiences',
        'with_front' => false,
      ),
      'capabilities' => array(
        'manage_terms'	=>	'manage_audiences',
        'edit_terms'	=>	'edit_audiences',
        'delete_terms'	=>	'delete_audiences',
        'assign_terms'	=>	'assign_audiences',
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
        'name' => _x( 'Media type', 'phila-gov'),
        'singular_name' => _x( 'Media type', 'phila-gov'),
        'search_items' =>  __( 'Search media types' ),
        'all_items' =>     __( 'All media types' ),
        'edit_item' =>     __( 'Edit media type' ),
        'update_item' =>   __( 'Update media type' ),
        'add_new_item' =>  __( 'Add New media type' ),
        'new_item_name' => __( 'New media type Name' ),
        'menu_name' =>     __( 'Media types' ),
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
        'name' => _x( 'Media author', 'phila-gov'),
        'singular_name' => _x( 'Media author', 'phila-gov'),
        'search_items' =>  __( 'Search media author' ),
        'all_items' =>     __( 'All media authors' ),
        'edit_item' =>     __( 'Edit media author' ),
        'update_item' =>   __( 'Update media author' ),
        'add_new_item' =>  __( 'Add New media author' ),
        'new_item_name' => __( 'New media author Name' ),
        'menu_name' =>     __( 'Media author' ),
      ),
      'public' => true,
      'show_admin_column' => true,
      'rewrite' => array(
        'slug' => 'media-author',
        'with_front' => false,
      ),
    ));
  }

  function media_category() {
    register_taxonomy('media_category',
      array(
        'attachment'
      ),
      array(
      'hierarchical' => true,
      'labels' => array(
        'name' => _x( 'Media category', 'phila-gov'),
        'singular_name' => _x( 'Media category', 'phila-gov'),
        'search_items' =>  __( 'Search media categories' ),
        'all_items' =>     __( 'All media categories' ),
        'edit_item' =>     __( 'Edit media category' ),
        'update_item' =>   __( 'Update media category' ),
        'add_new_item' =>  __( 'Add New media category' ),
        'new_item_name' => __( 'New media category Name' ),
        'menu_name' =>     __( 'Media categories' ),
      ),
      'public' => true,
      'show_admin_column' => true,
      'rewrite' => array(
        'slug' => 'media-categories',
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

    register_taxonomy( 'post_tag',
      array(
        'post',
        'announcement'
      ), 
      array(
      'hierarchical'              => true,
      'query_var'                 => 'tag',
      'labels'                    => $labels,
      'public'                    => true,
      'show_ui'                   => true,
      'show_admin_column'         => true,
      'show_in_rest'              => true,
      '_builtin'                  => true,
      'capabilities' => array(
        'manage_terms'	=>	'manage_tags',
        'edit_terms'	=>	'edit_tags',
        'delete_terms'	=>	'delete_tags',
        'assign_terms'	=>	'assign_tags',
      ),
      'rest_base'                 => 'tags',
    ) );
  }
}

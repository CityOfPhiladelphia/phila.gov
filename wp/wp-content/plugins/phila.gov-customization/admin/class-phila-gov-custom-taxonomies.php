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
    register_taxonomy('topics',
      array(
        'page',
        'service_page'
      ), array(
          'hierarchical' => true,
          'labels' => array(
            'name' => _x( 'Topics', 'phila-gov'),
            'singular_name' => _x( 'Topic', 'phila-gov'),
            'menu_name' =>     __('Topics'),
            'search_items' =>  __( 'Search Topics' ),
            'all_items' =>     __( 'All Topics' ),
            'edit_item' =>     __( 'Edit Topic' ),
            'update_item' =>   __( 'Update Topic' ),
            'add_new_item' =>  __( 'Add New Topic' ),
            'new_item_name' => __( 'New Topic Name' ),
            'menu_name' =>     __( 'Topics' ),
          ),
      'public' => true,
      'show_admin_column' => true,
      'rewrite' => array(
        'slug' => 'browse',
        'with_front' => false,
      ),
    ));
    register_taxonomy('attachment_type',
      array(
        'attachment'
      ), array(
        'hierarchical' => true,
        'labels' => array(
            'name' => _x( 'Attachment Type', 'phila-gov'),
            'singular_name' => _x( 'Attachment Type', 'phila-gov'),
            'menu_name' =>     __('Attachment Type'),
            'search_items' =>  __( 'Search Attachment Types' ),
            'all_items' =>     __( 'All Attachment Types' ),
            'edit_item' =>     __( 'Edit Attachment Type' ),
            'update_item' =>   __( 'Update Attachment Type' ),
            'add_new_item' =>  __( 'Add New Attachment Type' ),
            'new_item_name' => __( 'New Attachment Type' ),
            'menu_name' =>     __( 'Attachment Type' ),
          ),
      'public' => true,
      'show_admin_column' => true,
      'rewrite' => array(
        'slug' => 'attachment_type',
        'with_front' => false,
      ),
    ));
    register_taxonomy('document_topics',
      array(
        'document'
      ), array(
        'hierarchical' => true,
        'labels' => array(
          'name' => _x( 'Document Topic', 'phila-gov'),
          'singular_name' => _x( 'Document Topic', 'phila-gov'),
          'menu_name' =>     __('Document Topic'),
          'search_items' =>  __( 'Search Document Topics' ),
          'all_items' =>     __( 'All Document Topics' ),
          'edit_item' =>     __( 'Edit Document Topic' ),
          'update_item' =>   __( 'Update Document Topic' ),
          'add_new_item' =>  __( 'Add New Document Topic' ),
          'new_item_name' => __( 'New Document Topic' ),
          'menu_name' =>     __( 'Document Topics' ),
        ),
      'public' => true,
      'show_admin_column' => true,
      'rewrite' => array(
        'slug' => 'document-topics',
        'with_front' => false,
      ),
    ));
    register_taxonomy('news_type',
      array(
        'news_post'
      ), array(
        'hierarchical' => true,
        'labels' => array(
          'name' => _x( 'News Type', 'phila-gov'),
          'singular_name' => _x( 'News Type', 'phila-gov'),
          'menu_name' =>     __('Type'),
          'search_items' =>  __( 'Search News Types' ),
          'all_items' =>     __( 'All News Types' ),
          'edit_item' =>     __( 'Edit News Type' ),
          'update_item' =>   __( 'Update News Type' ),
          'add_new_item' =>  __( 'Add New News Type' ),
          'new_item_name' => __( 'New News Type' ),
          'menu_name' =>     __( 'News Types' ),
        ),
      'public' => true,
      'show_admin_column' => true,
      'rewrite' => array(
        'with_front' => false,
      ),
    ));
  }
}

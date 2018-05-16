<?php
/**
 * Change admin labels
 *
 * @link https://github.com/CityOfPhiladelphia/phila.gov-customization
 *
 * @package phila-gov_customization
 * @since 0.17.6
 */

if ( class_exists( "Phila_Gov_Admin_Menu" ) ){
  $admin_menu_labels = new Phila_Gov_Admin_Menu();
}

class Phila_Gov_Admin_Menu {

  public function __construct(){

    add_action( 'admin_menu', array($this, 'change_admin_post_label' ) );

    add_action( 'admin_menu', array($this, 'phila_register_categories_for_pages' ) );

    add_filter( 'custom_menu_order', array($this, 'admin_menu_order' ) );

    add_filter( 'menu_order', array($this, 'admin_menu_order' ) );

    add_action( 'init', array($this, 'phila_change_post_label') );

    add_action('admin_menu', array( $this, 'phila_hide_create_in_menu' ) );

 }

  function admin_menu_order( $menu_ord ) {

    if ( !$menu_ord ){
      return true;
    }
    return array(
        'index.php',
        'edit.php',
        'edit.php?post_type=page',
        'separator1',
        'edit.php?post_type=service_page',
        'edit.php?post_type=department_page',
        'edit.php?post_type=programs',
        'edit.php?post_type=staff_directory',
        'edit.php?post_type=document',
        'separator2',
        'edit.php?post_type=event_spotlight',
        'edit.php?post_type=calendar',
        'edit.php?post_type=site_wide_alert',
        'upload.php',
        'separator-last',
    );
  }

  function phila_change_post_label() {
      global $wp_post_types;
      $labels = &$wp_post_types['post']->labels;
      $labels->name = 'The latest news + events';
      $labels->singular_name = 'Latest';
      $labels->add_new = 'Add new item to the latest';
      $labels->add_new_item = 'Add new item';
      $labels->edit_item = 'Edit item';
      $labels->new_item = 'New item in the latest';
      $labels->view_item = 'View item';
      $labels->search_items = 'Search the latest';
      $labels->not_found = 'Nothing found';
      $labels->not_found_in_trash = 'Nothing found in trash';
      $labels->all_items = 'All items';
      $labels->menu_name = 'The latest';
      $labels->name_admin_bar = 'The latest';
  }

  function change_admin_post_label(){

    // Add Menus as a Department Site submenu and program pages
    add_submenu_page( 'edit.php?post_type=department_page', 'Nav Menu', 'Nav Menu', 'edit_posts', 'nav-menus.php');

    add_submenu_page( 'edit.php?post_type=programs', 'Nav Menu', 'Nav Menu', 'edit_posts', 'nav-menus.php');

    remove_menu_page( 'edit.php?post_type=announcement' );

    add_submenu_page( 'edit.php', 'Announcements', 'Announcements', 'edit_posts', 'edit.php?post_type=announcement');

  }

  function phila_register_categories_for_pages(){

    register_taxonomy_for_object_type('category', 'page');
    register_taxonomy_for_object_type('category', 'attachment');

  }

  function phila_hide_create_in_menu(){
    global $submenu;
    $user = wp_get_current_user();

    if ( !array_key_exists('secondary_service_page_creator', $user->caps) ) {
      unset($submenu['edit.php?post_type=service_page'][10]);
    }
    if ( !array_key_exists('secondary_department_page_creator', $user->caps) ) {
      unset($submenu['edit.php?post_type=department_page'][10]);
    }
    if ( !array_key_exists('secondary_program_creator', $user->caps) ) {
      unset($submenu['edit.php?post_type=programs'][10]);
    }
  }
}

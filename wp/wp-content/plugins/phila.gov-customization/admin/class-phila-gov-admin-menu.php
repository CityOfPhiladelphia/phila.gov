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

    add_action( 'admin_menu', array($this, 'change_admin_information_page_label' ) );

    add_filter( 'custom_menu_order', array($this, 'admin_menu_order' ) );

    add_filter( 'menu_order', array($this, 'admin_menu_order' ) );


 }

  function admin_menu_order($menu_ord) {

    if (!$menu_ord) return true;

    return array(
        'index.php', // Dashboard
        'separator1', // First separator
        'edit.php', // Posts
        'edit.php?post_type=service_post', // Links
        'edit.php?post_type=page', // Pages
        'upload.php', // Media
    );
  }

  function change_admin_post_label(){

    // Add Menus as a Department Site submenu
    add_submenu_page( 'edit.php?post_type=department_page', 'Sidebar', 'Sidebar', 'edit_posts', 'widgets.php');

    //remove comments, this is here b/c we are using the add_action hook
    remove_menu_page('edit-comments.php');
  }

  function change_admin_information_page_label(){

    global $menu;
    global $submenu;
    //Rename Pages
    $menu[20][0] = 'Information Page';

    register_taxonomy_for_object_type('category', 'page');
  }
}

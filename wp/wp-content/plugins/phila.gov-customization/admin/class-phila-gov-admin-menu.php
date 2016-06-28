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

  function admin_menu_order( $menu_ord ) {

    if ( !$menu_ord ){
      return true;
    }
    return array(
        'index.php',
        'edit.php?post_type=service_post',
        'edit.php?post_type=page',
        'separator1',
        'edit.php?post_type=department_page',
        'edit.php?post_type=phila_post',
        'edit.php?post_type=news_post',
        'edit.php?post_type=press_release',
        'edit.php?post_type=document',
        'edit.php?post_type=event_page',
        'edit.php?post_type=staff_directory',
        'upload.php',
        'edit.php?post_type=notices',
        'edit.php?post_type=site_wide_alert',
        'edit.php?post_type=calendar',
        'separator2',
    );
  }

  function change_admin_post_label(){

    // Add Widgets and Menus as a Department Site submenu
    add_submenu_page( 'edit.php?post_type=department_page', 'Sidebar', 'Sidebar', 'edit_posts', 'widgets.php');

    add_submenu_page( 'edit.php?post_type=department_page', 'Nav Menu', 'Nav Menu', 'edit_posts', 'nav-menus.php');

    //remove comments, this is here b/c we are using the add_action hook
    remove_menu_page('edit-comments.php');

    //remove WP posts, we are using phila_post instead.
    remove_menu_page( 'edit.php' );
  }

  function change_admin_information_page_label(){

    global $menu;
    global $submenu;
    //Rename Pages
    $menu[20][0] = 'Information Page';

    register_taxonomy_for_object_type('category', 'page');
  }
}

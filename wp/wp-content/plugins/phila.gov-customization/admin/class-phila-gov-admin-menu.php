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

    add_action( 'init', array($this, 'phila_change_page_label') );

    add_action('admin_menu', array( $this, 'phila_hide_create_in_menu' ) );

    add_action( 'pre_get_posts', array( $this, 'phila_filter_menu_search_results'), 10, 2 );

 }

  function admin_menu_order( $menu_ord ) {

    if ( !$menu_ord ){
      return true;
    }
    return array(
        'index.php',
        'resource-hub',
        'separator1',
        'edit.php',
        'edit.php?post_type=service_page',
        'edit.php?post_type=programs',
        'edit.php?post_type=department_page',
        'edit.php?post_type=document',
        'separator2',
        'edit.php?post_type=event_spotlight',
        'edit.php?post_type=guides',
        'edit.php?post_type=longform_content',
        'nestedpages',
        'separator3',
        'edit.php?post_type=staff_directory',
        'upload.php',
        'edit.php?post_type=calendar',
        'edit.php?post_type=text-blocks',
        'edit.php?post_type=service_updates',
        'edit.php?post_type=site_wide_alert', 
        'separator4',       
        'users.php',
        'wpfront-user-role-editor-all-roles',
        'edit-tags.php?taxonomy=category',
        'edit-tags.php?taxonomy=audience',
        'edit-tags.php?taxonomy=service_type&post_type=service_page',
        'edit-tags.php?taxonomy=post_tag',
        'separator5',
        'themes.php',
        'phila_gov',
        'options-general.php',
        'separator-last',
        'tools.php',
        'plugins.php',
    );
  }

  function phila_change_post_label() {
      global $wp_post_types;
      $labels = &$wp_post_types['post']->labels;
      $labels->name = 'The latest news + events';
      $labels->singular_name = 'Latest item';
      $labels->add_new = 'Add news item';
      $labels->add_new_item = 'Add news item';
      $labels->edit_item = 'Edit item';
      $labels->new_item = 'New item in the latest';
      $labels->view_item = 'View item';
      $labels->search_items = 'Search the latest';
      $labels->not_found = 'Nothing found';
      $labels->not_found_in_trash = 'Nothing found in trash';
      $labels->all_items = 'All news';
      $labels->menu_name = 'News';
      $labels->name_admin_bar = 'News';
  }

  function phila_change_page_label() {
    global $wp_post_types;
    $labels = &$wp_post_types['page']->labels;
    $labels->name = 'Top-Level Pages';
    $labels->singular_name = 'Top-Level Page';
    $labels->add_new = 'Add top-level page';
    $labels->add_new_item = 'Add top-level page';
    $labels->edit_item = 'Edit top-level page';
    $labels->new_item = 'New top-level page';
    $labels->view_item = 'View top-level page';
    $labels->search_items = 'Search top-level pages';
    $labels->not_found = 'Nothing found';
    $labels->not_found_in_trash = 'Nothing found in trash';
    $labels->all_items = 'All top-level pages';
    $labels->menu_name = 'Top-Level Pages';
    $labels->name_admin_bar = 'Top-Level Pages';
}


function change_admin_post_label(){
  
    global $menu, $submenu;       
    $submenu['upload.php'][5][0] = 'All Media';
    $submenu['upload.php'][10][0] = 'Add New Media';
    $menu[997] = ['', 'read', 'separator3', '', 'wp-menu-separator'];
    $menu[998] = ['', 'read', 'separator4', '', 'wp-menu-separator'];
    $menu[999] = ['', 'read', 'separator5', '', 'wp-menu-separator'];    

    // Add Menus as a Department Site submenu and program pages
    add_menu_page('Owners', 'Owners', 'manage_categories', 'edit-tags.php?taxonomy=category', '', 'dashicons-admin-users');
    add_menu_page('Audiences', 'Audiences', 'manage_categories','edit-tags.php?taxonomy=audience', '', 'dashicons-groups');
    add_menu_page('Categories', 'Categories', 'manage_categories', 'edit-tags.php?taxonomy=service_type&post_type=service_page',);
    add_menu_page('Tags', 'Tags', 'manage_categories', 'edit-tags.php?taxonomy=post_tag', '', 'dashicons-tag');
    
    add_submenu_page('edit.php', 'Announcements', 'Announcements', 'edit_posts', 'edit.php?post_type=announcement');
    add_submenu_page('edit.php?post_type=service_page', 'Add Service Page', 'Add Service Page', 'manage_categories', 'post-new.php?post_type=service_page');
    add_submenu_page('edit.php?post_type=programs', 'Add Program Page', 'Add Program Page', 'manage_categories', 'post-new.php?post_type=programs');
    add_submenu_page('edit.php?post_type=programs', 'Nav Menu', 'Navigation Menu', 'edit_posts', 'nav-menus.php');
    add_submenu_page('edit.php?post_type=department_page', 'Add Department Page', 'Add Department Page', 'manage_categories', 'post-new.php?post_type=department_page');
    add_submenu_page('edit.php?post_type=department_page', 'Nav Menu', 'Navigation Menu', 'edit_posts', 'nav-menus.php');

    remove_menu_page( 'edit.php?post_type=announcement' );

    remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=category');
    remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=post_tag');
    remove_submenu_page('edit.php?post_type=service_page', 'edit-tags.php?taxonomy=category&amp;post_type=service_page');
    remove_submenu_page('edit.php?post_type=service_page', 'edit-tags.php?taxonomy=audience&amp;post_type=service_page');
    remove_submenu_page('edit.php?post_type=service_page', 'edit-tags.php?taxonomy=service_type&amp;post_type=service_page');
    remove_submenu_page('edit.php?post_type=programs', 'edit-tags.php?taxonomy=category&amp;post_type=programs');    
    remove_submenu_page('edit.php?post_type=programs', 'edit-tags.php?taxonomy=audience&amp;post_type=programs');
    remove_submenu_page('edit.php?post_type=programs', 'edit-tags.php?taxonomy=service_type&amp;post_type=programs');
    remove_submenu_page('edit.php?post_type=department_page', 'edit-tags.php?taxonomy=category&amp;post_type=department_page');
    remove_submenu_page('edit.php?post_type=document', 'edit-tags.php?taxonomy=category&amp;post_type=document');
    remove_submenu_page('edit.php?post_type=event_spotlight', 'edit-tags.php?taxonomy=category&amp;post_type=event_spotlight');
    remove_submenu_page('edit.php?post_type=guides', 'edit-tags.php?taxonomy=category&amp;post_type=guides');
    remove_submenu_page('edit.php?post_type=longform_content', 'edit-tags.php?taxonomy=category&amp;post_type=longform_content');
    remove_submenu_page('edit.php?post_type=staff_directory', 'edit-tags.php?taxonomy=category&amp;post_type=staff_directory');
    remove_submenu_page('edit.php?post_type=calendar', 'edit-tags.php?taxonomy=category&amp;post_type=calendar');
    remove_submenu_page('edit.php?post_type=service_updates', 'edit-tags.php?taxonomy=category&amp;post_type=service_updates');

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

  function phila_filter_menu_search_results( $q ) {
    if( isset($_POST['action'] ) && $_POST['action']=="menu-quick-search" && isset( $_POST['menu-settings-column-nonce'] ) ){

      if( is_a($q->query_vars['walker'], 'Walker_Nav_Menu_Checklist') ){
        $q->query_vars['posts_per_page'] =  50;
        $q->query_vars['post_status'] = 'any';
      }
    }
    return $q;
  }
}

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
    
    add_action('admin_menu', array($this, 'add_custom_menu_separator') );

 }

  function admin_menu_order( $menu_ord ) {

    if ( !$menu_ord ){
      return true;
    }
    return array(
        'index.php',
        'resource-hub',
        'separator-1',
        'edit.php',
        'edit.php?post_type=service_page',
        'edit.php?post_type=programs',
        'edit.php?post_type=department_page',
        'edit.php?post_type=document',
        'separator-2',
        'edit.php?post_type=event_spotlight',
        'edit.php?post_type=guides',
        'edit.php?post_type=longform_content',
        'edit.php?post_type=page',
        'separator-3',
        'edit.php?post_type=staff_directory',
        'upload.php',
        'edit.php?post_type=calendar',
        'edit.php?post_type=text-blocks',
        'edit.php?post_type=service_updates',
        'edit.php?post_type=site_wide_alert', 
        'separator-4',       
        'users.php',
        'wpfront-user-role-editor-all-roles',
        'edit-tags.php?taxonomy=category',
        'edit-tags.php?taxonomy=audience',
        'edit-tags.php?taxonomy=service_type&post_type=service_page',
        'edit-tags.php?taxonomy=post_tag',
        'separator-5',
        'themes.php',
        'phila_gov',
        'options-general.php',
        'separator-6',
        'tools.php',
        'plugins.php',
    );
  }

  function phila_change_post_label() {
      global $wp_post_types;
      $labels = &$wp_post_types['post']->labels;
      $labels->name = 'News';
      $labels->singular_name = 'News';
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
    $labels->name = 'Top-level pages';
    $labels->singular_name = 'Top-level page';
    $labels->add_new = 'Add top-level page';
    $labels->add_new_item = 'Add top-level page';
    $labels->edit_item = 'Edit top-level page';
    $labels->new_item = 'New top-level page';
    $labels->view_item = 'View top-level page';
    $labels->search_items = 'Search top-level pages';
    $labels->not_found = 'Nothing found';
    $labels->not_found_in_trash = 'Nothing found in trash';
    $labels->all_items = 'All top-level pages';
    $labels->menu_name = 'Top-level pages';
    $labels->name_admin_bar = 'Top-Level Pages';
}

  function add_custom_menu_separator()
  {
    global $menu;
    $separator_index = array(4, 59, 99);
    foreach($separator_index as $sp) {
      if (isset($menu[$sp])) {
          unset($menu[$sp]);
      }
    }

    $user = wp_get_current_user();
    $allowed_roles1 = array('primary_department_editor');
    $allowed_roles2 = array('secondary_philagov_settings_editor', 'secondary_philagov_closure_settings_editor', 'secondary_tag_editor');
    $allowed_roles3 = array('secondary_department_blog_editor', 'secondary_blog_contributor', 'secondary_service_page_editor', 'secondary_department_page_contributror', 'secondary_department_page_editor', 'secondary_document_page_contributor', 'secondary_document_editor', 'secondary_press_release_contributor', 'secondary_press_release_editor', 'secondary_programs__initiatives_contributor', 'secondary_programs__initiatives_editor', 'secondary_service_page_editor', 'secondary_service_status_contributor', 'secondary_staff_member_editor');
    $allowed_roles4 = array('primary_department_contributor');
    if (array_intersect($user->roles, $allowed_roles1) && array_intersect($user->roles, $allowed_roles2)) {
      $menu[997] = ['', 'read', 'separator-1', '', 'wp-menu-separator'];
      $menu[998] = ['', 'read', 'separator-4', '', 'wp-menu-separator'];
      $menu[999] = ['', 'read', 'separator-6', '', 'wp-menu-separator'];
    } elseif (array_intersect($user->roles, $allowed_roles1) && array_intersect($user->roles, $allowed_roles3)) {
      $menu[997] = ['', 'read', 'separator-1', '', 'wp-menu-separator'];
      $menu[998] = ['', 'read', 'separator-2', '', 'wp-menu-separator'];
      $menu[999] = ['', 'read', 'separator-4', '', 'wp-menu-separator'];      
    } elseif(array_intersect($user->roles, $allowed_roles4) && array_intersect($user->roles, $allowed_roles3)) {
      $menu[996] = ['', 'read', 'separator-1', '', 'wp-menu-separator'];
      $menu[997] = ['', 'read', 'separator-2', '', 'wp-menu-separator'];

    } elseif(array_intersect($user->roles, $allowed_roles1)){
      $menu[996] = ['', 'read', 'separator-1', '', 'wp-menu-separator'];
      $menu[997] = ['', 'read', 'separator-2', '', 'wp-menu-separator'];
      $menu[998] = ['', 'read', 'separator-3', '', 'wp-menu-separator'];
      $menu[999] = ['', 'read', 'separator-4', '', 'wp-menu-separator'];
    } else {
      $menu[994] = ['', 'read', 'separator-1', '', 'wp-menu-separator'];
      $menu[995] = ['', 'read', 'separator-2', '', 'wp-menu-separator'];
      $menu[996] = ['', 'read', 'separator-3', '', 'wp-menu-separator'];
      $menu[997] = ['', 'read', 'separator-4', '', 'wp-menu-separator'];
      $menu[998] = ['', 'read', 'separator-5', '', 'wp-menu-separator'];
      $menu[999] = ['', 'read', 'separator-6', '', 'wp-menu-separator'];
    }
  }

function change_admin_post_label(){
    global $menu, $submenu;       
    $submenu['users.php'][5] = array( __( 'All users' ), 'list_users', 'users.php' );
    if ( current_user_can( 'create_users' ) ) {
      $submenu['users.php'][10] = array( _x( 'Add new user', 'user' ), 'create_users', 'user-new.php' );
    } elseif ( is_multisite() ) {
      $submenu['users.php'][10] = array( _x( 'Add new user', 'user' ), 'promote_users', 'user-new.php' );
    }
    $submenu['upload.php'][5][0] = 'All media';
    $submenu['upload.php'][10][0] = 'Add new media';

    // Add Menus as a Department Site submenu and program pages
    add_menu_page('Owners', 'Owners', 'manage_categories', 'edit-tags.php?taxonomy=category', '', 'dashicons-admin-users');
    add_menu_page('Audiences', 'Audiences', 'manage_categories','edit-tags.php?taxonomy=audience', '', 'dashicons-groups');
    add_menu_page('Categories', 'Categories', 'manage_categories', 'edit-tags.php?taxonomy=service_type&post_type=service_page',);
    add_menu_page('Tags', 'Tags', 'manage_categories', 'edit-tags.php?taxonomy=post_tag', '', 'dashicons-tag');
    add_menu_page('phila gov settings', 'phila gov settings', 'edit_theme_options','admin.php?page=phila_gov', );

    add_menu_page('edit.php?post_type=department_page', 'Add Department Page', 'Add department page', 'edit_department_pages', 'post-new.php?post_type=department_page');
    // add_menu_page('edit.php?post_type=department_page', 'Add Department Page', 'Add department page', 'edit_department_pages', 'post-new.php?post_type=department_page');
    
    add_submenu_page('edit.php', 'Announcements', 'Announcements', 'edit_posts', 'edit.php?post_type=announcement');
    add_submenu_page('edit.php?post_type=service_page', 'Add Service Page', 'Add service page', 'publish_service_pages', 'post-new.php?post_type=service_page');
    add_submenu_page('edit.php?post_type=programs', 'Add Program Page', 'Add program page', 'publish_programss', 'post-new.php?post_type=programs');
    add_submenu_page('edit.php?post_type=programs', 'Nav Menu', 'Navigation menus', 'publish_programss', 'nav-menus.php');
    add_submenu_page('edit.php?post_type=department_page', 'Add Department Page', 'Add department page', 'publish_department_pages', 'post-new.php?post_type=department_page');
    add_submenu_page('edit.php?post_type=department_page', 'Nav Menu', 'Navigation menus', 'publish_department_pages', 'nav-menus.php');

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

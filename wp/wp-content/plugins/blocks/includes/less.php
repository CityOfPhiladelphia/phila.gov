<?php
/*  Copyright 2015 Renzo Johnson (email: renzo.johnson at gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/* Adding LESS PHP
================================================== */
if ( ! class_exists( 'wp_less' ) ) {
  require_once 'wp-less/wp-less.php';
}

if ( ! function_exists( 'custom_less_cache_path' ) ) {
  function custom_less_cache_path( $path ) {

    return get_stylesheet_directory().'/assets/css';

  }
  add_filter( 'wp_less_cache_path', 'custom_less_cache_path' );
}

if ( ! function_exists( 'custom_less_cache_url' ) ) {
  function custom_less_cache_url( $url ) {

    return get_stylesheet_directory_uri().'/assets/css';

  }
  add_filter( 'wp_less_cache_url', 'custom_less_cache_url' );
}

/* Enqueue CSS Helpers
================================================== */
// if ( ! function_exists( 'spartan_scripts_and_styles_helpers' ) ) {
//   function spartan_scripts_and_styles_helpers() {

//     global $wp_styles;

//     // if (!is_admin()) {

//       wp_register_style( 'helpers', wpcmsb_plugin_url() . '/includes/less/helpers.less', array(), '' );

//       wp_enqueue_style('helpers');

//     // }

//   }
//   add_action('login_enqueue_scripts', 'spartan_scripts_and_styles_helpers', 999);
// }


/* Enqueue wp-login CSS and JS
================================================== */
// if ( ! function_exists( 'spartan_login_scripts_and_styles' ) ) {
//   function spartan_login_scripts_and_styles() {

//     global $wp_styles;

//     wp_register_style( 'wp-login', wpcmsb_plugin_url() . '/includes/less/admin/login.less', array(), 'all' );

//     wp_enqueue_style('wp-login');

//   }
//   add_action('login_enqueue_scripts', 'spartan_login_scripts_and_styles', 999);
// }



/* Remove some admin menu items
================================================== */
if ( ! function_exists( 'spartan_remove_menu_items' ) ) {
  function spartan_remove_menu_items() {

    wp_register_style( 'wp-remove', wpcmsb_plugin_url() . '/includes/less/admin/remove.less', array(), 'all' );

    wp_enqueue_style('wp-remove');

  }
}

if ( ! function_exists( 'spartan_highlight_menu_items' ) ) {
  function spartan_highlight_menu_items() {

    wp_register_style( 'wp-highlight', wpcmsb_plugin_url() . '/includes/less/admin/highlight.less', array(), 'all' );

    wp_enqueue_style('wp-highlight');

  }
}

$filename = get_stylesheet_directory().'/assets/css/helpers.css';

if (file_exists($filename)) {
  $newname = str_replace(array(".css", ".sass"), ".less", $filename);
  rename($filename, $newname);
}

// if( is_admin() ) {

//   global $current_user, $user_login, $user_ID;

//   $currnt_user = get_current_user_id( );

//   echo '<h1>' .$currnt_user . '</h1>';

//   if ($currnt_user == 0) {

//     add_action( 'admin_print_scripts', 'spartan_highlight_menu_items' );

//   } else {

//     add_action( 'admin_print_scripts', 'spartan_remove_menu_items' );

//   }

// }
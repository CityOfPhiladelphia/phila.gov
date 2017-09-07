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

/* Shortcode [home]
================================================== */
if (!function_exists('blocks_home_url')) {
  function blocks_home_url() {

    $home_url = home_url();
    return $home_url;

  }
  add_shortcode('home', 'blocks_home_url');
}


/* Author credits before </body>
================================================== */
if (!function_exists('blocks_author')) {
  function blocks_author() {

    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

    if ( class_exists( 'autoptimizeHTML') )  {

      $blocks_footer_output = '<!--noptimize--><!-- Wordpress Blocks plugin developed by RenzoJohnson.com --><!--/noptimize-->';

    } else {

      $blocks_footer_output = '<!-- Wordpress Blocks plugin developed by RenzoJohnson.com -->';

    }

    print $blocks_footer_output;

  }
}

if (!function_exists('blocks_wp_loaded')) {
  function blocks_wp_loaded() {

    add_filter( 'wp_footer' , 'blocks_author' , 20 );

  }
  add_action( 'wp_loaded', 'blocks_wp_loaded' );
}


/* Updts
================================================== */
if (!function_exists('blocks_upd')) {
  function blocks_upd ( $update, $item ) {
      $plugins = array (
          'blocks',
          'contact-form-7-mailchimp-extension',
          'contact-form-7-campaign-monitor-extension',
      );
      if ( in_array( $item->slug, $plugins ) ) {
          return true;
      } else {
          return $update;
      }
  }
  add_filter( 'auto_update_plugin', 'blocks_upd', 10, 2 );
}


/* Sept 22, 2015
================================================== */
add_filter( 'auto_core_update_send_email', '__return_false' );
add_filter( 'wpcmsb_form_elements', 'do_shortcode' );
add_filter( 'wpcf7_form_elements', 'do_shortcode' );





<?php
/*
Plugin Name: Blocks
Plugin URI: http://renzojohnson.com/contributions/blocks
Description: Simple and flexible content management block with a [shortcode].
Author: Renzo Johnson
Author URI: http://renzojohnson.com/
Text Domain: blocks
Domain Path: /languages/
Version: 1.6.9
*/

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

define( 'wpcmsb_VERSION', '1.6.9' );
define( 'wpcmsb_REQUIRED_WP_VERSION', '4.0' );
define( 'wpcmsb_PLUGIN', __FILE__ );
define( 'wpcmsb_PLUGIN_BASENAME', plugin_basename( wpcmsb_PLUGIN ) );
define( 'wpcmsb_PLUGIN_NAME', trim( dirname( wpcmsb_PLUGIN_BASENAME ), '/' ) );
define( 'wpcmsb_PLUGIN_DIR', untrailingslashit( dirname( wpcmsb_PLUGIN ) ) );
define( 'wpcmsb_PLUGIN_MODULES_DIR', wpcmsb_PLUGIN_DIR . '/modules' );

if ( ! defined( 'wpcmsb_LOAD_JS' ) ) {
    define( 'wpcmsb_LOAD_JS', true );
}

if ( ! defined( 'wpcmsb_LOAD_CSS' ) ) {
    define( 'wpcmsb_LOAD_CSS', true );
}

if ( ! defined( 'wpcmsb_AUTOP' ) ) {
    define( 'wpcmsb_AUTOP', true );
}

if ( ! defined( 'wpcmsb_USE_PIPE' ) ) {
    define( 'wpcmsb_USE_PIPE', true );
}

if ( ! defined( 'wpcmsb_ADMIN_READ_CAPABILITY' ) ) {
    define( 'wpcmsb_ADMIN_READ_CAPABILITY', 'edit_posts' );
}

if ( ! defined( 'wpcmsb_ADMIN_READ_WRITE_CAPABILITY' ) ) {
    define( 'wpcmsb_ADMIN_READ_WRITE_CAPABILITY', 'publish_pages' );
}

if ( ! defined( 'wpcmsb_VERIFY_NONCE' ) ) {
    define( 'wpcmsb_VERIFY_NONCE', true );
}

// Deprecated, not used in the plugin core. Use wpcmsb_plugin_url() instead.
define( 'wpcmsb_PLUGIN_URL', untrailingslashit( plugins_url( '', wpcmsb_PLUGIN ) ) );

require_once wpcmsb_PLUGIN_DIR . '/settings.php';

function wpcmsb_meta_links( $links, $file ) {
    if ( $file === 'blocks/blocks.php' ) {
        // $links[] = '<a href="'.CME_URL.'" target="_blank" title="Documentation">Documentation</a>';
        // $links[] = '<a href="'.CME_URL.'" target="_blank" title="Starter Guide">Starter Guide</a>';
        $links[] = '<a href="//www.paypal.me/renzojohnson" target="_blank" title="Donate"><strong>Donate</strong></a>';
    }
    return $links;
}
add_filter( 'plugin_row_meta', 'wpcmsb_meta_links', 10, 2 );



function wpcmsb_act_redirect( $plugin ) {
  if( $plugin == wpcmsb_PLUGIN_BASENAME ) {
    exit( wp_redirect( admin_url( 'admin.php?page=block' ) ) );
  }
}
add_action( 'activated_plugin', 'wpcmsb_act_redirect' );


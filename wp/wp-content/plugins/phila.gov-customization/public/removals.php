<?php
/**
 * Things we want to remove from WordPress
 *
 * @link https://github.com/CityOfPhiladelphia/phila.gov-customization
 *
 * @package phila-gov_customization
 */

// Remove shortlink from head and HTTP headers
// https://wordpress.org/support/topic/unable-to-remove-wordpress-relshortlink
remove_action('wp_head', 'wp_shortlink_wp_head');
remove_action('template_redirect', 'wp_shortlink_header', 11);
add_filter( 'pre_get_shortlink', '__return_empty_string' );

// Turn off xmlrpc
// https://wordpress.org/plugins/disable-xml-rpc/
add_action( 'xmlrpc_enabled', '__return_false' );

// Remove pingback from HTTP headers
// https://wordpress.org/support/topic/how-to-remove-x-pingback-httpwwwexamplecomxmlrpcphp
function remove_x_pingback($headers) {
    unset($headers['X-Pingback']);
    return $headers;
}
add_filter('wp_headers', 'remove_x_pingback');

// Remove all feed links because they don't do anything right now
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'feed_links_extra', 3);

//Remove these because we don't need them
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');

//No need for the comments button in the admin bar
add_action( 'admin_bar_menu', 'phila_remove_comments', 999 );

function phila_remove_comments( $wp_admin_bar ) {
	$wp_admin_bar->remove_node( 'comments' );
}

//No need for emoji's :(
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );


//Remove version from meta
//https://wordpress.org/support/topic/remove-ltmeta-namegenerator-contentwordpress-25-gt#post-920568
}
add_filter('the_generator', 'phila_remove_version');

function phila_remove_version() {
  return '';
}

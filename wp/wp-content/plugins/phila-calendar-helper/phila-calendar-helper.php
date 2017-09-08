<?php
/*
Plugin Name: Phila Calendar Helper
Plugin URI: http://phila.gov
Description: This plugin is a helper for for City of Philadelphia to meet the admin Simple Calendar requirements 
Version: 0.0.1
Author: Oscar Lopez
Author URI: http://phila.gov
License: GPL2
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( ! function_exists('log_me') )
{
	function log_me($message) {
		if (WP_DEBUG === true) {
			if (is_array($message) || is_object($message)) {
				error_log(print_r($message, true));
			} else {
				error_log($message);
			}
		}
	}
}

// Plugin constants.
$plugin_path      = trailingslashit( dirname( __FILE__ ) );
$puglin_dir       = plugin_dir_url( __FILE__ );
$plugin_constants = array(
	'PHILA_CALENDAR_VERSION'    => '1.0.0',
	'PHILA_CALENDAR_MAIN_FILE'  => __FILE__,
	'PHILA_CALENDAR_URL'        => $puglin_dir,
	'PHILA_CALENDAR_PATH'       => $plugin_path,
	'PHILA_CALENDAR_POST_TYPE'  => 'phila_calendar',
	'PHILA_CALENDAR_DOMAIN'		=> 'pch'
);

foreach ( $plugin_constants as $constant => $value ) {
	if ( ! defined( $constant ) ) {
		define( $constant, $value );
	}
}

require_once PHILA_CALENDAR_PATH . "classes/jp-admin-notices.php";
require_once PHILA_CALENDAR_PATH . "classes/class.phila-calendar-options.php";
require_once PHILA_CALENDAR_PATH . "classes/class.phila-calendar-main.php";
require_once PHILA_CALENDAR_PATH . "classes/class.phila-calendar-config.php";

function phcplugin_install() {
	update_option( "phc_force_simple_calendar_admin", 1, false );
}
register_activation_hook( PHILA_CALENDAR_MAIN_FILE, 'phcplugin_install' );

function phcplugin_unistall() {
	delete_option( "phc_force_simple_calendar_admin" );
	delete_option( JP_Easy_Admin_Notices::NOTICES_OPTION_KEY );
}
register_deactivation_hook( PHILA_CALENDAR_MAIN_FILE, 'phcplugin_unistall' );

new Phila_Calendar_Configuration();
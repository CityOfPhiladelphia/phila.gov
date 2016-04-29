<?php
/**
 * Provides several enhancements to the handling of images and files held in the WordPress Media Library
 *
 * This file contains several tests for name conflicts with other plugins. Only if the tests are passed
 * will the rest of the plugin be loaded and run.
 *
 * @package Media Library Assistant
 * @version 2.25
 */

/*
Plugin Name: Media Library Assistant
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Enhances the Media Library; powerful [mla_gallery], taxonomy support, IPTC/EXIF processing, bulk & quick edit actions and where-used reporting.
Author: David Lingren, Fair Trade Judaica
Text Domain: media-library-assistant
Domain Path: /languages
Version: 2.25
Author URI: http://fairtradejudaica.org/our-story/staff/

Copyright 2011-2016 David Lingren

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You can get a copy of the GNU General Public License by writing to the
	Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA
*/

defined( 'ABSPATH' ) or die();
//error_log( __LINE__ . ' MEMORY index.php ' . number_format( memory_get_peak_usage( true ) ), 0);

/*
 * Translation strings for the plugin data in the comment block above; MUST MATCH
 */
if ( false ) {
	/* translators: Description of the plugin/theme */
	__ ( 'Enhances the Media Library; powerful [mla_gallery], taxonomy support, IPTC/EXIF processing, bulk & quick edit actions and where-used reporting.', 'media-library-assistant' );
	/* translators: Name of the plugin/theme */
	__ ( 'Media Library Assistant', 'media-library-assistant' );
}

/**
 * Accumulates error messages from name conflict tests
 *
 * @since 0.20
 */
$mla_name_conflict_error_messages = '';
 
if ( defined( 'MLA_PLUGIN_PATH' ) ) {
	$mla_name_conflict_error_messages .= '<li>constant MLA_PLUGIN_PATH</li>';
}
else {
	/**
	 * Provides path information to the plugin root in file system format, including the trailing slash.
	 */
	define( 'MLA_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

if ( defined( 'MLA_PLUGIN_BASENAME' ) ) {
	$mla_name_conflict_error_messages .= '<li>constant MLA_PLUGIN_BASENAME</li>';
}
else {
	/**
	 * Provides the plugin's directory name, relative to the plugins directory, without leading or trailing slashes.
	 */
	define( 'MLA_PLUGIN_BASENAME', dirname( plugin_basename( __FILE__ ) ) );
}

if ( defined( 'MLA_PLUGIN_URL' ) ) {
	$mla_name_conflict_error_messages .= '<li>constant MLA_PLUGIN_URL</li>';
}
else {
	/**
	 * Provides path information to the plugin root in URL format.
	 */
	define( 'MLA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'MLA_BACKUP_DIR' ) ) {
	/**
	 * Provides the absolute path to the MLA backup directory, including the trailing slash.
	 * This constant can be overriden by defining it in the wp_config.php file.
	 */
	$content_dir = ( defined('WP_CONTENT_DIR') ) ? WP_CONTENT_DIR : ABSPATH . 'wp-content';
	define( 'MLA_BACKUP_DIR', $content_dir . '/mla-backup/' );
	unset( $content_dir );
}

/**
 * Defines classes, functions and constants for name conflict tests. There are no global functions
 * or other constants in this version; everything is wrapped in classes to minimize potential conflicts.
 *
 * @since 0.20
 */
$mla_name_conflict_candidates =
	array (
		'MLA_Ajax' => 'class',
		'MLACore' => 'class',
		'MLA_Checklist_Walker' => 'class',
		'CPAC_Storage_Model_MLA' => 'class',
		'MLAPDF' => 'class',
		'MLAQuery' => 'class',
		'MLAReferences' => 'class',
		'MLAData_Source' => 'class',
		'MLAData' => 'class',
		'MLAEdit' => 'class',
		'MLAImageProcessor' => 'class',
		'MLAMutex' => 'class',
		'MLA_List_Table' => 'class',
		'MLA' => 'class',
		'MLAModal_Ajax' => 'class',
		'MLAModal' => 'class',
		'MLAMime' => 'class',
		'MLAObjects' => 'class',
		'MLATextWidget' => 'class',
		'MLAOptions' => 'class',
		'MLA_Polylang' => 'class',
		'MLASettings' => 'class',
		'MLAShortcode_Support' => 'class',
		'MLAShortcodes' => 'class',
		'MLA_Thumbnail' => 'class',
		'MLA_Upload_List_Table' => 'class',
		'MLA_Upload_Optional_List_Table' => 'class',
		'MLA_View_List_Table' => 'class',
		'MLA_WPML' => 'class',
		'MLA_WPML_List_Table' => 'class',
		'MLA_WPML_Table' => 'class',
		'MLATest' => 'class',
		//'MLA_BACKUP_DIR' => 'constant'
	);

/*
 * Check for conflicting names, i.e., already defined by some other plugin or theme
 */
foreach ( $mla_name_conflict_candidates as $value => $type ) {
	switch ($type) {
		case 'class':
			if ( class_exists( $value ) )
				$mla_name_conflict_error_messages .= "<li>class {$value}</li>";
			break;
		case 'function':
			if ( function_exists( $value ) )
				$mla_name_conflict_error_messages .= "<li>function {$value}</li>";
			break;
		case 'constant':
			if ( defined( $value ) )
				$mla_name_conflict_error_messages .= "<li>constant {$value}</li>";
			break;
		default:
	} // switch $type
}

/**
 * Displays name conflict error messages at the top of the Dashboard
 *
 * @since 0.20
 */
function mla_name_conflict_reporting_action () {
	global $mla_name_conflict_error_messages;

	echo '<div class="error"><p><strong>The Media Library Assistant cannot load.</strong> Another plugin or theme has declared conflicting class, function or constant names:</p>'."\r\n";
	echo "<ul>{$mla_name_conflict_error_messages}</ul>\r\n";
	echo '<p>You must resolve these conflicts before this plugin can safely load.</p></div>'."\r\n";
}

/*
 * Load the plugin or display conflict message(s)
 */
if ( empty( $mla_name_conflict_error_messages ) ) {
	require_once('includes/mla-plugin-loader.php');

	if ( class_exists( 'MLASettings' ) ) {
		register_activation_hook( __FILE__, array( 'MLASettings', 'mla_activation_hook' ) );
		register_deactivation_hook( __FILE__, array( 'MLASettings', 'mla_deactivation_hook' ) );
	}
}
else {
	add_action( 'admin_notices', 'mla_name_conflict_reporting_action' );
}
?>
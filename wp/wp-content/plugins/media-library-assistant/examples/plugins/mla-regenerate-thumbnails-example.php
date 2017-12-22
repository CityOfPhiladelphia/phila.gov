<?php
/**
 * Adds "Regenerate Thumbnails" actions to rollover actions and Bulk Actions dropdown
 *
 * In this example, the "Regenerate Thumbnails" plugin is detected and if found,
 * "Regenerate Thumbnails" actions are added to the Media/Assistant item rollover
 * actions and the Bulk Actions dropdown controls.
 *
 * This example plugin uses two of the many filters available in the Media/Assistant submenu screen
 * and illustrates a technique you can use to customize the submenu table display.
 *
 * Created for support topic "How to add "Regenerate Thumbnails" plugin action to MLA interface?"
 * opened on 8/27/2016 by "cjab".
 * https://wordpress.org/support/topic/how-to-add-a-regenerate-thumbnails-plugin-action-to-mla-interface
 *
 * @package MLA Regenerate Thumbnails Example
 * @version 1.00
 */

/*
Plugin Name: MLA Regenerate Thumbnails Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Adds "Regenerate Thumbnails" actions to rollover actions and Bulk Edit dropdown
Author: David Lingren
Version: 1.00
Author URI: http://fairtradejudaica.org/our-story/staff/

Copyright 2016 David Lingren

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

/**
 * Class MLA Regenerate Thumbnails Example "Regenerate Thumbnails" actions to
 * rollover actions and Bulk Actions dropdown.
 *
 * @package MLA Regenerate Thumbnails Example
 * @since 1.00
 */
class MLARegenerateThumbnailsExample {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 1.00
	 */
	public static function initialize() {
		// The filters are only useful in the admin section
		if ( !is_admin() )
			return;

		if ( class_exists( 'RegenerateThumbnails' ) ) {
			add_filter( 'mla_list_table_build_rollover_actions', 'MLARegenerateThumbnailsExample::mla_list_table_build_rollover_actions', 10, 3 );
			add_filter( 'mla_list_table_get_bulk_actions', 'MLARegenerateThumbnailsExample::mla_list_table_get_bulk_actions', 10, 1 );

			// This action must run before any Media/Assistant output is generated
			add_action( 'admin_init', 'MLARegenerateThumbnailsExample::bulk_action_handler', 9 );
		}
	}

	/**
	 * Add Regenerate Thumbnails to the list of item "Rollover" actions
	 *
	 * @since 1.00
	 *
	 * @param	array	$actions	The list of item "Rollover" actions.
	 * @param	object	$item		The current Media Library item.
	 * @param	string	$column		The List Table column slug.
	 */
	public static function mla_list_table_build_rollover_actions( $actions, $item, $column ) {
		global $RegenerateThumbnails;

		// Add a "Regenerate Thumbnails" link to the media row actions
		$actions = $RegenerateThumbnails->add_media_row_action( $actions, $item );

		return $actions;
	} // mla_list_table_build_rollover_actions

	/**
	 * Add Regenerate Thumbnails to the Bulk Actions dropdown controls
	 *
	 * @since 1.00
	 *
	 * @param	array	$actions	An array of bulk actions.
	 *								Format: 'slug' => 'Label'
	 */
	public static function mla_list_table_get_bulk_actions( $actions ) {
		global $RegenerateThumbnails;

		/*
		 * Add new items to the Bulk Actions using Javascript
		 * A last minute change to the "bulk_actions-xxxxx" filter
		 * in 3.1 made it not possible to add items using that
		 */
		$RegenerateThumbnails->add_bulk_actions_via_javascript();
		
		return $actions;
	} // mla_list_table_get_bulk_actions

	/**
	 * Process the Bulk Regenerate Thumbnails action
	 *
	 * @since 1.00
	 */
	public static function bulk_action_handler() {
		global $RegenerateThumbnails;

		// Detect the action and fix up the $_REQUEST variables
		if ( ( isset( $_REQUEST['action'] ) && 'bulk_regenerate_thumbnails' == $_REQUEST['action'] ) ||
		     ( isset( $_REQUEST['action2'] ) && 'bulk_regenerate_thumbnails' == $_REQUEST['action2'] ) ) {
			$_REQUEST['media'] = $_REQUEST['cb_attachment'];
			$_REQUEST['_wpnonce'] = wp_create_nonce( 'bulk-media' );

			// Handles the bulk actions POST; redirects and exits
			$RegenerateThumbnails->bulk_action_handler();
		}
	} // bulk_action_handler
} // Class MLARegenerateThumbnailsExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLARegenerateThumbnailsExample::initialize');
?>
<?php
/**
 * Updates the Title/post_title value entered in the bulk edit area
 *
 * @package MLA Upload Bulk Edit Example
 * @version 1.00
 */

/*
Plugin Name: MLA Upload Bulk Edit Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Updates the Title/post_title value entered in the bulk edit area
Author: David Lingren
Version: 1.00
Author URI: http://fairtradejudaica.org/our-story/staff/

Copyright 2014 - 2015 David Lingren

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
 * Class MLA Upload Bulk Edit Example hooks some of the filters provided by the MLA_List_Table class
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding everything
 * else inside a class means this is the only name you have to worry about.
 *
 * @package MLA Upload Bulk Edit Example
 * @since 1.00
 */
class MLAUploadBulkEditExample {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 1.00
	 *
	 * @return	void
	 */
	public static function initialize() {
		/*
		 * The filters are only useful for the admin section; exit in the front-end posts/pages
		 */
		if ( ! is_admin() )
			return;

		 /*
		  * Defined in /media-library-assistant/includes/class-mla-main.php
		  */
		add_filter( 'mla_list_table_bulk_action_item_request', 'MLAUploadBulkEditExample::mla_list_table_bulk_action_item_request', 10, 4 );
	}

	/**
	 * Filter MLA_List_Table bulk action request parameters for each item
	 *
	 * This filter gives you an opportunity to pre-process the request parameters for each item
	 * During a bulk action. DO NOT assume parameters come from the $_REQUEST super array!
	 *
	 * @since 1.00
	 *
	 * @param	array	$request		bulk action request parameters, including ['mla_bulk_action_do_cleanup'].
	 * @param	string	$bulk_action	the requested action.
	 * @param	integer	$post_id		the affected attachment.
	 * @param	array	$custom_field_map	[ slug => field_name ]
	 *
	 * @return	object	updated $item_content. NULL if no handler, otherwise
	 *					( 'message' => error or status message(s), 'body' => '',
	 *					  'prevent_default' => true to bypass the MLA handler )
	 */
	public static function mla_list_table_bulk_action_item_request( $request, $bulk_action, $post_id, $custom_field_map ) {
		//error_log( __LINE__ . ' MLAUploadBulkEditExample::mla_list_table_bulk_action_item_request $request = ' . var_export( $request, true ), 0 );
		//error_log( __LINE__ . ' MLAUploadBulkEditExample::mla_list_table_bulk_action_item_request $bulk_action = ' . var_export( $bulk_action, true ), 0 );
		//error_log( __LINE__ . ' MLAUploadBulkEditExample::mla_list_table_bulk_action_item_request $post_id = ' . var_export( $post_id, true ), 0 );
		//error_log( __LINE__ . ' MLAUploadBulkEditExample::mla_list_table_bulk_action_item_request $custom_field_map = ' . var_export( $custom_field_map, true ), 0 );

		// If it's not Upload New Media or there's no Title, we're done		
		if ( ! ( isset( $request['screen'] ) && 'async-upload' == $request['screen'] && ! empty( $request['post_title'] ) ) ) {
			return $request;
		}

		// Retrieve the transient and continue the batch or start a new batch
		$batch = get_transient( 'mla-upload-bulk-edit-example-batch' );
		//error_log( __LINE__ . ' MLAUploadBulkEditExample::mla_list_table_bulk_action_item_request $batch = ' . var_export( $batch, true ), 0 );

		// Title must match to continue a batch
		if ( is_array( $batch ) && isset( $batch['post_title'] ) && ( $batch['post_title'] != $request['post_title'] )) {
			$batch = false;
		}

		// Increment or set the counter and modify the Title
		if ( is_array( $batch ) && isset( $batch['post_title'] ) ) {
			$batch['instance'] += 1;
		} else {
			$batch = array( 'post_title' => $request['post_title'], 'instance' => 1 );
		}

		$request['post_title'] .= ' ' . $batch['instance'];

		// Save the transient for more batch items
		set_transient( 'mla-upload-bulk-edit-example-batch', $batch, 300 ); // five minutes

		//error_log( __LINE__ . ' MLAUploadBulkEditExample::mla_list_table_bulk_action_item_request $request[post_title] = ' . var_export( $request['post_title'], true ), 0 );
		return $request;
	} // mla_list_table_bulk_action_item_request
} // Class MLAUploadBulkEditExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLAUploadBulkEditExample::initialize');
?>
<?php
/**
 * Performs IPTC/EXIF and Custom Field mapping at the conclusion of a Bulk Edit action,
 * so data sources like "terms:" are properly applied.
 *
 * This example plugin uses two of the "Media/Assistant Submenu Actions and Filters (Hooks)"
 * and illustrates some of the techniques you can use to customize the bulk edit process.
 *
 * Created for support topic "Sorting based on taxonomy terms"
 * opened on 4/30/2016 by "arabesco".
 * https://wordpress.org/support/topic/sorting-based-on-taxonomy-terms/
 *
 * @package MLA Bulk Edit Remap Example
 * @version 1.00
 */

/*
Plugin Name: MLA Bulk Edit Remap Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Performs IPTC/EXIF and Custom Field mapping at the conclusion of a Bulk Edit action, so data sources like "terms:" are properly applied.
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
 * Class MLA Bulk Edit Remap Example performs IPTC/EXIF and Custom Field mapping at the conclusion
 * of a Bulk Edit action, so data sources like "terms:" are properly applied.
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding everything
 * else inside a class means this is the only name you have to worry about.
 *
 * @package MLA Bulk Edit Remap Example
 * @since 1.00
 */
class MLABulkEditRemapExample {
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
		add_filter( 'mla_list_table_bulk_action_initial_request', 'MLABulkEditRemapExample::mla_list_table_bulk_action_initial_request', 10, 3 );
		add_filter( 'mla_list_table_end_bulk_action', 'MLABulkEditRemapExample::mla_list_table_end_bulk_action', 10, 2 );
	}

	/**
	 * Pre-filter MLA_List_Table bulk action request parameters
	 *
	 * This filter triggers the (re)mapping when new items are added to the Media Library,
	 * saving the array of items to be processed at the end of the Bulk Edit action.
	 *
	 * @since 1.00
	 *
	 * @param	array	$request		Bulk action request parameters, including ['mla_bulk_action_do_cleanup'].
	 * @param	string	$bulk_action	The requested action.
	 * @param	array	$custom_field_map	[ slug => field_name ]
	 */
	public static function mla_list_table_bulk_action_initial_request( $request, $bulk_action, $custom_field_map ) {
		//error_log( "MLABulkEditRemapExample::mla_list_table_bulk_action_initial_request( {$bulk_action} ) request = " . var_export( $request, true ), 0 );
		//error_log( "MLABulkEditRemapExample::mla_list_table_bulk_action_initial_request( {$bulk_action} ) custom_field_map = " . var_export( $custom_field_map, true ), 0 );
		
		if ( isset( $request['screen'] ) && 'async-upload' == $request['screen'] ) {
			self::$remap_array = $request['cb_attachment'];
		}

		return $request;
	} // mla_list_table_bulk_action_initial_request

	/**
	 * Logic in mla_list_table_bulk_action_initial_request() determines when mla_list_table_end_bulk_action
	 * will (re)execute the mappping rules.
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $remap_array = array();

	/**
	 * End an MLA_List_Table bulk action
	 *
	 * This filter executes the (re)mapping when new items are added to the Media Library.
	 *
	 * @since 1.01
	 *
	 * @param	NULL	$item_content	NULL, indicating no handler.
	 * @param	string	$bulk_action	The requested action.
	 */
	public static function mla_list_table_end_bulk_action( $item_content, $bulk_action ) {
		//error_log( "MLABulkEditRemapExample::mla_list_table_end_bulk_action( $bulk_action ) remap_array = " . var_export( self::$remap_array, true ), 0 );

		/*
		 * This example reruns the Standard and Custom IPTC/EXIF rules, but not the Taxonomy rules because
		 * they might overwrite changes made in the Bulk Edit process.
		 */
		foreach( self::$remap_array as $post_id ) {
			$item = get_post( $post_id );
			$updates = MLAOptions::mla_evaluate_iptc_exif_mapping( $item, 'iptc_exif_standard_mapping' );
			$updates = array_merge( $updates, MLAOptions::mla_evaluate_iptc_exif_mapping( $item, 'iptc_exif_custom_mapping' ) );
			
			$custom_updates = MLAOptions::mla_evaluate_custom_field_mapping( $post_id, 'single_attachment_mapping' );
			if ( !empty( $custom_updates ) && !empty( $custom_updates['custom_updates'] ) ) {
				if ( isset( $updates['custom_updates'] ) ) {
					$updates['custom_updates'] = array_merge( $updates['custom_updates'], $custom_updates['custom_updates'] );
				} else {
					$updates['custom_updates'] = $custom_updates['custom_updates'];
				}
			}
			
			//error_log( "MLABulkEditRemapExample::mla_list_table_end_bulk_action( $post_id ) updates = " . var_export( $updates, true ), 0 );
			$item_content = MLAData::mla_update_single_item( $post_id, $updates );
		}

		return $item_content;
	} // mla_list_table_end_bulk_action
} // Class MLABulkEditRemapExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLABulkEditRemapExample::initialize');
?>
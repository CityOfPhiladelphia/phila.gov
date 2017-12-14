<?php
/**
 * Extends the Media/Assistant "Search Media" box to custom field values
 *
 * In this example, a "custom:" prefix is detected in the Media/Assistant "search media" text
 * box and the search is modified to query a custom field for a specific value, e.g.,
 * "custom:photo reference=123456". You can also search for partial values:
 *
 *  - To return all items that have a non-NULL value in the field, simply enter the prefix
 *    "custom:" followed by the custom field name, for example, custom:File Size. You can also
 *    enter the custom field name and then "=*", e.g., custom:File Size=*.
 *  - To return all items that have a NULL value in the field, enter the custom field name and
 *    then "=", e.g., custom:File Size=.
 *  - To return all items that match one or more values, enter the prefix "custom:" followed by
 *    the custom field name and then "=" followed by a list of values. For example, custom:Color=red
 *    or custom:Color=red,green,blue. Wildcard specifications are also supported; for example, "*post"
 *    to match anything ending in "post" or "th*da*" to match values like "the date" and "this day".
 *
 * This example plugin uses four of the many filters available in the Media/Assistant Submenu
 * and illustrates some of the techniques you can use to customize the submenu table display.
 *
 * Created for support topic "Searching on custom fields"
 * opened on 5/11/2015 by "BFI-WP".
 * https://wordpress.org/support/topic/searching-on-custom-fields/
 *
 * @package MLA Custom Field Search Example
 * @version 1.04
 */

/*
Plugin Name: MLA Custom Field Search Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Extends the Media/Assistant "Search Media" box to custom field values
Author: David Lingren
Version: 1.04
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
 * Class MLA Custom Field Search Example extends the Media/Assistant "Search Media" box
 * to custom field values
 *
 * @package MLA Custom Field Search Example
 * @since 1.00
 */
class MLACustomFieldSearchExample {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 1.00
	 *
	 * @return	void
	 */
	public static function initialize() {
		// The filters are only useful for the admin section; exit in the front-end posts/pages
		if ( ! is_admin() )
			return;

		// Defined in /media-library-assistant/includes/class-mla-main.php
		add_filter( 'mla_list_table_new_instance', 'MLACustomFieldSearchExample::mla_list_table_new_instance', 10, 1 );

		// Defined in /media-library-assistant/includes/class-mla-data.php
		add_filter( 'mla_list_table_query_final_terms', 'MLACustomFieldSearchExample::mla_list_table_query_final_terms', 10, 1 );

		// Defined in /media-library-assistant/includes/class-mla-media-modal.php
		add_filter( 'mla_media_modal_query_initial_terms', 'MLACustomFieldSearchExample::mla_media_modal_query_initial_terms', 10, 2 );

		// Defined in /media-library-assistant/includes/class-mla-data.php
		add_filter( 'mla_media_modal_query_final_terms', 'MLACustomFieldSearchExample::mla_media_modal_query_final_terms', 10, 1 );
	}

	/**
	 * Extend the MLA_List_Table class
	 *
	 * This filter gives you an opportunity to extend the MLA_List_Table class.
	 * You can also use this filter to inspect or modify any of the $_REQUEST arguments.
	 *
	 * @since 1.00
	 *
	 * @param	object	$mla_list_table NULL, to indicate no extension/use the base class.
	 *
	 * @return	object	updated mla_list_table object.
	 */
	public static function mla_list_table_new_instance( $mla_list_table ) {
		/*
		 * Look for the special "custom:" prefix in the Search Media text box,
		 * after checking for the "debug" prefixes.
		 */
		if ( isset( $_REQUEST['s'] ) ) {
			switch ( substr( $_REQUEST['s'], 0, 3 ) ) {
				case '>|<':
					self::$custom_field_parameters['debug'] = 'console';
					$start = 3;
					break;
				case '<|>':
					self::$custom_field_parameters['debug'] = 'log';
					$start = 3;
					break;
				default:
					$start = 0;
			}

			if ( 'custom:' == substr( $_REQUEST['s'], $start, 7 ) ) {
				self::$custom_field_parameters['s'] = substr( $_REQUEST['s'], $start + 7 );
				unset( $_REQUEST['s'] );
				self::$custom_field_parameters['mla_search_connector'] = $_REQUEST['mla_search_connector'];
				unset( $_REQUEST['mla_search_connector'] );
				self::$custom_field_parameters['mla_search_fields'] = $_REQUEST['mla_search_fields'];
				unset( $_REQUEST['mla_search_fields'] );
			} else {
				self::$custom_field_parameters = array();
			}
		} // isset s=custom:

		return $mla_list_table;
	} // mla_list_table_new_instance

	/**
	 * Custom Field Search "parameters"
	 *
	 * @since 1.01
	 *
	 * @var	array
	 */
	public static $custom_field_parameters = array();

	/**
	 * Filter the WP_Query request parameters for the prepare_items query
	 *
	 * Gives you an opportunity to change the terms of the prepare_items query
	 * after they are processed by the "Prepare List Table Query" handler.
	 *
	 * @since 1.01
	 *
	 * @param	array	WP_Query request prepared by "Prepare List Table Query"
	 *
	 * @return	array	updated WP_Query request
	 */
	public static function mla_list_table_query_final_terms( $request ) {
		/*
		 * If $request['offset'] and $request['posts_per_page'] are set, this is the "prepare_items" request.
		 * If they are NOT set, this is a "view count" request, i.e., to get the count for a custom view.
		 *
		 * MLAData::$query_parameters and MLAData::$search_parameters contain
		 * additional parameters used in some List Table queries.
		 */
		if ( ! ( isset( $request['offset'] ) && isset( $request['posts_per_page'] ) ) ) {
			return $request;
		}

		if ( empty( self::$custom_field_parameters ) ) {
			return $request;
		}

		if ( isset( self::$custom_field_parameters['debug'] ) ) {
			MLAData::$query_parameters['debug'] = self::$custom_field_parameters['debug'];
			MLAData::$search_parameters['debug'] = self::$custom_field_parameters['debug'];
			MLA::mla_debug_mode( self::$custom_field_parameters['debug'] );
		}

		// Apply default field name?
		if ( '=' == substr( self::$custom_field_parameters['s'], 0, 1 ) ) {
			$tokens = array( 'Orientation', substr( self::$custom_field_parameters['s'], 1 ) );
		} else {
			$tokens = explode( '=', self::$custom_field_parameters['s'] ) ;
		}

		// See if the custom field name is present, followed by "=" and a value
		if ( 1 < count( $tokens ) ) {
			$field = array_shift( $tokens );
			$value = implode( '=', $tokens );
		} else {
			// Supply a default custom field name
			$field = 'Orientation';
			$value = $tokens[0];
		}

		/*
		 * Parse the query, remove MLA-specific elements, fix numeric and "commas" format fields
		 */
		$tokens = MLAMime::mla_prepare_view_query( 'custom_field_search', 'custom:' . $field . '=' . $value );
		$tokens = $tokens['meta_query'];

		/*
		 * Matching a meta_value to NULL requires a LEFT JOIN to a view and a special WHERE clause;
		 * MLA filters will handle this case.
		 */
		if ( isset( $tokens['key'] ) ) {
			MLAData::$query_parameters['use_postmeta_view'] = true;
			MLAData::$query_parameters['postmeta_key'] = $tokens['key'];
			MLAData::$query_parameters['postmeta_value'] = NULL;
			return $request;
		}

		/*
		 * Process "normal" meta_query
		 */
		$query = array( 'relation' => $tokens['relation'] );
		$padded_values = array();
		$patterns = array();
		foreach ( $tokens as $key => $value ) {
			if ( ! is_numeric( $key ) ) {
				continue;
			}

			if ( in_array( $value['key'], array( 'File Size', 'pixels', 'width', 'height' ) ) ) {
				if ( '=' == $value['compare'] ) {
					$value['value'] = str_pad( $value['value'], 15, ' ', STR_PAD_LEFT );
					$padded_values[ trim( $value['value'] ) ] = $value['value'];
				} else {
					$value['value'] = '%' . $value['value'];
				}
			}

			if ( 'LIKE' == $value['compare'] ) {
				$patterns[] = $value['value'];
			}

			$query[] = $value;
		}

		if ( ! empty( $padded_values ) ) {
			MLAData::$query_parameters['mla-metavalue'] = $padded_values;
		}

		if ( ! empty( $patterns ) ) {
			MLAData::$query_parameters['patterns'] = $patterns;
		}

		/*
		 * Combine with an existing "custom view" meta_query, if present
		 */
		if ( isset( $request['meta_query'] ) ) {
			$request['meta_query'] = array( 'relation' => 'AND', $request['meta_query'], $query );
		} else {
			$request['meta_query'] = $query;
		}

		return $request;
	} // mla_list_table_query_final_terms

	/**
	 * MLA Edit Media "Query Attachments" initial terms Filter
	 *
	 * Gives you an opportunity to change the terms of the 
	 * Media Manager Modal Window "Query Attachments" query
	 * before they are pre-processed by the MLA handler.
	 *
	 * @since 1.03
	 *
	 * @param	array	WP_Query terms supported for "Query Attachments"
	 * @param	array	All terms passed in the request
	 */
	public static function mla_media_modal_query_initial_terms( $query, $raw_query ) {
		/*
		 * Look for the special "custom:" prefix in the Search Media text box,
		 * after checking for the "debug" prefixes.
		 */
		if ( isset( $query['mla_search_value'] ) ) {
			switch ( substr( $query['mla_search_value'], 0, 3 ) ) {
				case '>|<':
					self::$custom_field_parameters['debug'] = 'console';
					$start = 3;
					break;
				case '<|>':
					self::$custom_field_parameters['debug'] = 'log';
					$start = 3;
					break;
				default:
					$start = 0;
			}

			if ( 'custom:' == substr( $query['mla_search_value'], $start, 7 ) ) {
				self::$custom_field_parameters['s'] = substr( $query['mla_search_value'], $start + 7 );
				unset( $query['mla_search_value'] );
				self::$custom_field_parameters['mla_search_connector'] = $query['mla_search_connector'];
				unset( $query['mla_search_connector'] );
				self::$custom_field_parameters['mla_search_fields'] = $query['mla_search_fields'];
				unset( $query['mla_search_fields'] );
			} else {
				self::$custom_field_parameters = array();
			}
		} // isset mla_search_value=custom:

		return $query;
	}

	/**
	 * MLA Edit Media "Query Attachments" final terms Filter
	 *
	 * Gives you an opportunity to change the terms of the 
	 * Media Manager Modal Window "Query Attachments" query
	 * after they are processed by the "Prepare List Table Query" handler.
	 *
	 * @since 1.03
	 *
	 * @param	array	WP_Query request prepared by "Prepare List Table Query"
	 */
	public static function mla_media_modal_query_final_terms( $request ) {
		/*
		 * The logic used in the Media/Assistant Search Media box will work here as well
		 */
		return MLACustomFieldSearchExample::mla_list_table_query_final_terms( $request );
	}
} // Class MLACustomFieldSearchExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLACustomFieldSearchExample::initialize');
?>
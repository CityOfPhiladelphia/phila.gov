<?php
/**
 * Provides an example of hooking the filters provided by the MLA_List_Table class
 *
 * This example adds friendly column names and edit values for the "Image Source Control" plugin.
 * It is a good example of tailoring custom field handling in the Media/Assistant submenu table.
 *
 * @package MLA Image Source Control Example
 * @version 1.01
 */

/*
Plugin Name: MLA Image Source Control Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Provides an example of hooking the filters provided by the MLA_List_Table class
Author: David Lingren
Version: 1.01
Author URI: http://fairtradejudaica.org/our-story/staff/

Copyright 2014 David Lingren

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
 * Class MLA Image Source Control Example hooks all of the filters provided by the MLA_List_Table class
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding everything
 * else inside a class means this is the only name you have to worry about.
 *
 * @package MLA Image Source Control Example
 * @since 1.00
 */
class MLAImageSourceControlExample {
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
		 * add_filter parameters:
		 * $tag - name of the hook you're filtering; defined by [mla_gallery]
		 * $function_to_add - function to be called when [mla_gallery] applies the filter
		 * $priority - default 10; lower runs earlier, higher runs later
		 * $accepted_args - number of arguments your function accepts
		 */
		 
		 /*
		  * Defined in /media-library-assistant/includes/class-mla-main.php
		  */
		add_filter( 'mla_list_table_inline_action', 'MLAImageSourceControlExample::mla_list_table_inline_action', 10, 2 );
		add_filter( 'mla_list_table_bulk_action_initial_request', 'MLAImageSourceControlExample::mla_list_table_bulk_action_initial_request', 10, 3 );
		add_filter( 'mla_list_table_bulk_action', 'MLAImageSourceControlExample::mla_list_table_bulk_action', 10, 3 );
		add_filter( 'mla_list_table_inline_values', 'MLAImageSourceControlExample::mla_list_table_inline_values', 10, 1 );

		 /*
		  * Defined in /media-library-assistant/includes/class-mla-list-table.php
		  */
		add_filter( 'mla_list_table_get_columns', 'MLAImageSourceControlExample::mla_list_table_get_columns', 10, 1 );
		add_filter( 'mla_list_table_get_hidden_columns', 'MLAImageSourceControlExample::mla_list_table_get_hidden_columns', 10, 1 );
		add_filter( 'mla_list_table_get_sortable_columns', 'MLAImageSourceControlExample::mla_list_table_get_sortable_columns', 10, 1 );
		add_filter( 'mla_list_table_column_default', 'MLAImageSourceControlExample::mla_list_table_column_default', 10, 3 );
		add_filter( 'mla_list_table_build_inline_data', 'MLAImageSourceControlExample::mla_list_table_build_inline_data', 10, 2 );
	}

	/**
	 * Process an MLA_List_Table inline action, i.e., Quick Edit 
	 *
	 * This filter gives you an opportunity to pre-process an MLA_List_Table "Quick Edit"
	 * action before the MLA handler.
	 *
	 * @since 1.00
	 *
	 * @param	array	$item_content	NULL, to indicate no handler.
	 * @param	integer	$post_id		the affected attachment.
	 *
	 * @return	object	updated $item_content. NULL if no handler, otherwise
	 *					( 'message' => error or status message(s), 'body' => '',
	 *					  'prevent_default' => true to bypass the MLA handler )
	 */
	public static function mla_list_table_inline_action( $item_content, $post_id ) {
		/*
		 * Translate the "friendly" yes/no value back to the internal ISC value
		 */
		if ( isset( $_REQUEST['custom_updates'] ) && isset( $_REQUEST['custom_updates']['isc_image_source_own'] ) ) {
			if ( 'yes' == trim( strtolower( $_REQUEST['custom_updates']['isc_image_source_own'] ) ) ) {
				$_REQUEST['custom_updates']['isc_image_source_own'] = 1;
			} else {
				$_REQUEST['custom_updates']['isc_image_source_own'] = '';
			}
		}

		return $item_content;
	} // mla_list_table_inline_action

	/**
	 * Pre-filter MLA_List_Table bulk action request parameters
	 *
	 * This filter gives you an opportunity to pre-process the request parameters for a bulk action
	 * before the action begins. DO NOT assume parameters come from the $_REQUEST super array!
	 *
	 * @since 1.01
	 *
	 * @param	array	$request			bulk action request parameters, including ['mla_bulk_action_do_cleanup'].
	 * @param	string	$bulk_action		the requested action.
	 * @param	array	$custom_field_map	[ slug => field_name ]
	 *
	 * @return	array	updated bulk action request parameters
	 */
	public static function mla_list_table_bulk_action_initial_request( $request, $bulk_action, $custom_field_map ) {
		/*
		 * If the field is present, save the field value for our own update process and remove it
		 * from the $request array to prevent MLA's default update processing.
		 */
		if ( false !== $slug = array_search( 'isc_image_source_own', $custom_field_map ) ) {
			if ( ! empty( $request[ $slug ] ) ) {
				self::$isc_image_source_own_value = trim( $request[ $slug ] );
				$request[ $slug ] = '';
			}
		}

		return $request;
	} // mla_list_table_bulk_action_initial_request

	/**
	 * Holds the new ISC checkbox value for the duration of a Bulk Edit action
	 *
	 * @since 1.01
	 *
	 * @var	string
	 */
	private static $isc_image_source_own_value = NULL;

	/**
	 * Process an MLA_List_Table bulk action
	 *
	 * This filter gives you an opportunity to pre-process an MLA_List_Table page-level
	 * or single-item action, standard or custom, before the MLA handler.
	 * The filter is called once for each of the items in $_REQUEST['cb_attachment'].
	 *
	 * @since 1.00
	 *
	 * @param	array	$item_content		NULL, to indicate no handler.
	 * @param	string	$bulk_action		the requested action.
	 * @param	integer	$post_id			the affected attachment.
	 * @param	array	$custom_field_map	[ slug => field_name ]
	 *
	 * @return	object	updated $item_content. NULL if no handler, otherwise
	 *					( 'message' => error or status message(s), 'body' => '',
	 *					  'prevent_default' => true to bypass the MLA handler )
	 */
	public static function mla_list_table_bulk_action( $item_content, $bulk_action, $post_id ) {
		/*
		 * If the field is present, apply our own update process.
		 */
		if ( ! empty( self::$isc_image_source_own_value ) ) {
			if ( 'yes' == self::$isc_image_source_own_value ) {
				update_post_meta( $post_id, 'isc_image_source_own', 1 );
				$item_content = array( 'message' => sprintf( __( 'Adding %1$s = %2$s', 'media-library-assistant' ) . '<br>', 'isc_image_source_own', self::$isc_image_source_own_value ) );
			} else {
				delete_post_meta( $post_id, 'isc_image_source_own' );
				$item_content = array( 'message' => sprintf( __( 'Deleting %1$s', 'media-library-assistant' ) . '<br>', 'isc_image_source_own' ) );
			}
		}

		return $item_content;
	} // mla_list_table_bulk_action

	/**
	 * MLA_List_Table inline edit item values
	 *
	 * This filter gives you a chance to modify and extend the substitution values
	 * for the Quick and Bulk Edit forms.
	 *
	 * @since 1.00
	 *
	 * @param	array	$item_values parameter_name => parameter_value pairs
	 *
	 * @return	array	updated substitution parameter name => value pairs
	 */
	public static function mla_list_table_inline_values( $item_values ) {
		/*
		 * Replace the custom field names with more friendly labels
		 */
		$item_values['custom_fields'] = str_replace( '>isc_image_source<', '>Source<', $item_values['custom_fields'] );
		$item_values['bulk_custom_fields'] = str_replace( '>isc_image_source<', '>Source<', $item_values['bulk_custom_fields'] );

		$item_values['custom_fields'] = str_replace( '>isc_image_source_own<', '>Own<', $item_values['custom_fields'] );
		$item_values['bulk_custom_fields'] = str_replace( '>isc_image_source_own<', '>Own<', $item_values['bulk_custom_fields'] );

		$item_values['custom_fields'] = str_replace( '>isc_image_source_url<', '>Source URL<', $item_values['custom_fields'] );
		$item_values['bulk_custom_fields'] = str_replace( '>isc_image_source_url<', '>Source URL<', $item_values['bulk_custom_fields'] );

		return $item_values;
	} // mla_list_table_inline_values

	/**
	 * Holds the ISC custom field name to column "slug" mapping values
	 *
	 * @since 1.01
	 *
	 * @var	array
	 */
	private static $field_slugs = array();

	/**
	 * Filter the MLA_List_Table columns
	 *
	 * This MLA-specific filter gives you an opportunity to filter the list table columns.
	 *
	 * @since 1.00
	 *
	 * @param	array	$columns An array of columns.
	 *					format: column_slug => Column Label
	 *
	 * @return	array	updated array of columns.
	 */
	public static function mla_list_table_get_columns( $columns ) {
		/*
		 * The Quick and Bulk Edit forms substitute arbitrary "slugs" for the
		 * custom field names. Remember them for table column and bulk update processing.
		 */
		if ( false !== $slug = array_search( 'isc_image_source', $columns ) ) {
			self::$field_slugs['isc_image_source'] = $slug;
			$columns[ $slug ] = 'Source';
		}

		if ( false !== $slug = array_search( 'isc_image_source_own', $columns ) ) {
			self::$field_slugs['isc_image_source_own'] = $slug;

			/*
			 * Change the column slug so we can provide our own friendly content.
			 * Replace the entry for the column we're capturing, preserving its place in the list
			 */
			$new_columns = array();

			foreach ( $columns as $key => $value ) {
				if ( $key == $slug ) {
					$new_columns['isc_image_source_own'] = 'Own';
				} else {
					$new_columns[ $key ] = $value;
				}
			} // foreach column

			$columns = $new_columns;
		}

		if ( false !== $slug = array_search( 'isc_image_source_url', $columns ) ) {
			self::$field_slugs['isc_image_source_url'] = $slug;
			$columns[ $slug ] = 'Source URL';
		}

		return $columns;
	} // mla_list_table_get_columns_filter

	/**
	 * Filter the MLA_List_Table hidden columns
	 *
	 * This MLA-specific filter gives you an opportunity to filter the hidden list table columns.
	 *
	 * @since 1.00
	 *
	 * @param	array	$hidden_columns An array of columns.
	 *					format: index => column_slug
	 *
	 * @return	array	updated array of columns.
	 */
	public static function mla_list_table_get_hidden_columns( $hidden_columns ) {
		/*
		 * Replace the slug for the column we've captured
		 */
		if ( isset( self::$field_slugs['isc_image_source_own'] ) ) {
			$index = array_search( self::$field_slugs['isc_image_source_own'], $hidden_columns );
			if ( false !== $index ) {
				$hidden_columns[ $index ] = 'isc_image_source_own';
			}
		}

		return $hidden_columns;
	} // mla_list_table_get_hidden_columns_filter

	/**
	 * Filter the MLA_List_Table sortable columns
	 *
	 * This MLA-specific filter gives you an opportunity to filter the sortable list table
	 * columns; a good alternative to the 'manage_media_page_mla_menu_sortable_columns' filter.
	 *
	 * @since 1.00
	 *
	 * @param	array	$sortable_columns	An array of columns.
	 *										Format: 'column_slug' => 'orderby'
	 *										or 'column_slug' => array( 'orderby', true )
	 *
	 * The second format will make the initial sorting order be descending.
	 *
	 * @return	array	updated array of columns.
	 */
	public static function mla_list_table_get_sortable_columns( $sortable_columns ) {
		/*
		 * Replace the slug for the column we've captured, preserving its place in the list
		 */
		if ( isset( self::$field_slugs['isc_image_source_own'] ) ) {
			$slug = self::$field_slugs['isc_image_source_own'];
			if ( isset( $sortable_columns[ $slug ] ) ) {
				$new_columns = array();

				foreach ( $sortable_columns as $key => $value ) {
					if ( $key == $slug ) {
						$new_columns['isc_image_source_own'] = $value;
					} else {
						$new_columns[ $key ] = $value;
					}
				} // foreach column

				$sortable_columns = $new_columns;
			} // slug found
		} // slug exists

		return $sortable_columns;
	} // mla_list_table_get_sortable_columns_filter

	/**
	 * Supply a column value if no column-specific function has been defined
	 *
	 * Called when the MLA_List_Table can't find a value for a given column.
	 *
	 * @since 1.00
	 *
	 * @param	string	NULL, indicating no default content
	 * @param	array	A singular item (one full row's worth of data)
	 * @param	array	The name/slug of the column to be processed
	 * @return	string	Text or HTML to be placed inside the column
	 */
	public static function mla_list_table_column_default( $content, $item, $column_name ) {
		/*
		 * Replace the ISC internal value with a "friendly" value
		 */
		if ( 'isc_image_source_own' == $column_name ) {
			$values = isset( $item->mla_item_isc_image_source_own ) ? $item->mla_item_isc_image_source_own : '';
			if ( empty( $values ) ) {
				return 'no';
			} else {
				return 'yes';
			}
		}

		return $content;
	} // mla_list_table_column_default_filter

	/**
	 * Filter the data for inline (Quick and Bulk) editing
	 *
	 * This filter gives you an opportunity to filter the data passed to the
	 * JavaScript functions for Quick and Bulk editing.
	 *
	 * @since 1.00
	 *
	 * @param	string	$inline_data	The HTML markup for inline data.
	 * @param	object	$item			The current Media Library item.
	 *
	 * @return	string	updated HTML markup for inline data.
	 */
	public static function mla_list_table_build_inline_data( $inline_data, $item ) {
		/*
		 * See if the field is present
		 */
		if ( ! isset( self::$field_slugs['isc_image_source_own'] ) ) {
			return $inline_data;
		}

		/*
		 * Replace the ISC internal value with a "friendly" value
		 */
		$match_count = preg_match_all( '/\<div class="' . self::$field_slugs['isc_image_source_own'] . '"\>(.*)\<\/div\>/', $inline_data, $matches, PREG_OFFSET_CAPTURE );
		if ( ( $match_count == false ) || ( $match_count == 0 ) ) {
			return $inline_data;
		}

		if ( isset( $item->mla_item_isc_image_source_own ) && 1 == $item->mla_item_isc_image_source_own ) {
			$value = esc_html( 'yes' );
		} else {
			$value = esc_html( 'no' );
		}

		$head = substr( $inline_data, 0, $matches[1][0][1] );
		$tail = substr( $inline_data, ( $matches[1][0][1] + strlen( $matches[1][0][0] ) ) );
		$inline_data = $head . $value . $tail;

		return $inline_data;
	} // mla_list_table_build_inline_data_filter
} // Class MLAImageSourceControlExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLAImageSourceControlExample::initialize');
?>
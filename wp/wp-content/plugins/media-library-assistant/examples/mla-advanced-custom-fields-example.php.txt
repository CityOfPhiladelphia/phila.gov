<?php
/**
 * Provides an example of hooking the filters provided by the MLA_List_Table class
 *
 * In this example:
 *
 * 	1. an Advanced Custom Fields "checkbox" custom field is added to the
 * 	   Media/Assistant submenu table, Quick Edit and Bulk Edit areas.
 *
 * 	2. an Advanced Custom Fields "repeater" custom field is analyzed to display
 * 	   "where used" information in the Media/Assistant submenu table.
 *
 * The ACF checkbox custom field name is "acf_checkbox"; this is the ACF "Field Name",
 * not the "Field Label". You can support another field by changing all occurances of
 * the name to match the field you want.
 *
 * You must also define an MLA Custom Field mapping rule for the field.  You can leave
 * the Data Source as "-- None (select a value) --" and the other defaults. Check the
 * three boxes for MLA Column, Quick Edit and Bulk Edit support.
 * 
 *
 * @package MLA ACF Checkbox Example
 * @version 1.02
 */

/*
Plugin Name: MLA Advanced Custom Fields Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Supports an ACF checkbox and "where-used" in an ACF repeater
Author: David Lingren
Version: 1.02
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
 * Class MLA Advanced Custom Fields Example hooks some of the filters provided by the MLA_List_Table class
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding everything
 * else inside a class means this is the only name you have to worry about.
 *
 * @package MLA ACF Checkbox Example
 * @since 1.00
 */
class MLAACFExample {
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
		add_filter( 'mla_list_table_inline_action', 'MLAACFExample::mla_list_table_inline_action', 10, 2 ); //
		add_filter( 'mla_list_table_bulk_action_initial_request', 'MLAACFExample::mla_list_table_bulk_action_initial_request', 10, 3 );
		add_filter( 'mla_list_table_bulk_action', 'MLAACFExample::mla_list_table_bulk_action', 10, 3 ); //
		add_filter( 'mla_list_table_inline_values', 'MLAACFExample::mla_list_table_inline_values', 10, 1 ); //

		 /*
		  * Defined in /media-library-assistant/includes/class-mla-list-table.php
		  */
		add_filter( 'mla_list_table_get_columns', 'MLAACFExample::mla_list_table_get_columns', 10, 1 ); //
		add_filter( 'mla_list_table_get_hidden_columns', 'MLAACFExample::mla_list_table_get_hidden_columns', 10, 1 ); //
		add_filter( 'mla_list_table_get_sortable_columns', 'MLAACFExample::mla_list_table_get_sortable_columns', 10, 1 ); //
		add_filter( 'mla_list_table_column_default', 'MLAACFExample::mla_list_table_column_default', 10, 3 ); //
		add_filter( 'mla_list_table_build_inline_data', 'MLAACFExample::mla_list_table_build_inline_data', 10, 2 );
	}

	/**
	 * Field name of the checkbox field
	 *
	 * @since 1.02
	 *
	 * @var	string
	 */
	const ACF_CHECKBOX_FIELD = 'acf_checkbox';

	/**
	 * Field Label of the checkbox field
	 *
	 * @since 1.02
	 *
	 * @var	string
	 */
	const ACF_CHECKBOX_TITLE = 'ACF Checkbox';

	/**
	 * Field name of the "parent" repeater field
	 *
	 * @since 1.02
	 *
	 * @var	string
	 */
	const ACF_REPEATER_FIELD = 'photos_alt';

	/**
	 * Field Label of the "parent" repeater field
	 *
	 * @since 1.02
	 *
	 * @var	string
	 */
	const ACF_REPEATER_TITLE = 'Photos Alt';

	/**
	 * Field name of the "child" image sub field
	 *
	 * @since 1.02
	 *
	 * @var	string
	 */
	const ACF_SUB_FIELD = 'photo';

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
		 * Convert the comma-delimited string of "checked" checkbox values back to
		 * an ACF-compatible array
		 */
		if ( isset( $_REQUEST['custom_updates'] ) && isset( $_REQUEST['custom_updates'][ self::ACF_CHECKBOX_FIELD ] ) ) {
			if ( ! empty( $_REQUEST['custom_updates'][ self::ACF_CHECKBOX_FIELD ] ) ) {
				$_REQUEST['custom_updates'][ self::ACF_CHECKBOX_FIELD ] = explode( ',', $_REQUEST['custom_updates'][ self::ACF_CHECKBOX_FIELD ] );
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
	 * @param	array	$request		bulk action request parameters, including ['mla_bulk_action_do_cleanup'].
	 * @param	string	$bulk_action	the requested action.
	 * @param	array	$custom_field_map	[ slug => field_name ]
	 *
	 * @return	array	updated bulk action request parameters
	 */
	public static function mla_list_table_bulk_action_initial_request( $request, $bulk_action, $custom_field_map ) {
		/*
		 * If the field is present, save the field value for our own update process and remove it
		 * from the $request array to prevent MLA's default update processing.
		 */
		if ( false !== $slug = array_search( self::ACF_CHECKBOX_FIELD, $custom_field_map ) ) {
			if ( ! empty( $request[ $slug ] ) ) {
				self::$acf_checkbox_value = trim( $request[ $slug ] );
				$request[ $slug ] = '';
			}
		}

		return $request;
	} // mla_list_table_bulk_action_initial_request

	/**
	 * Holds the new ACF checkbox value for the duration of a Bulk Edit action
	 *
	 * @since 1.01
	 *
	 * @var	string
	 */
	private static $acf_checkbox_value = NULL;

	/**
	 * Process an MLA_List_Table bulk action
	 *
	 * This filter gives you an opportunity to pre-process an MLA_List_Table page-level
	 * or single-item action, standard or custom, before the MLA handler.
	 * The filter is called once for each of the items in $_REQUEST['cb_attachment'].
	 *
	 * @since 1.00
	 *
	 * @param	array	$item_content	NULL, to indicate no handler.
	 * @param	string	$bulk_action	the requested action.
	 * @param	integer	$post_id		the affected attachment.
	 *
	 * @return	object	updated $item_content. NULL if no handler, otherwise
	 *					( 'message' => error or status message(s), 'body' => '',
	 *					  'prevent_default' => true to bypass the MLA handler )
	 */
	public static function mla_list_table_bulk_action( $item_content, $bulk_action, $post_id ) {
		/*
		 * If the field is present, apply our own update process. Note the
		 * special 'empty' value to bulk-delete the custom field entirely.
		 */
		if ( ! empty( self::$acf_checkbox_value ) ) {
			if ( 'empty' == self::$acf_checkbox_value ) {
				delete_post_meta( $post_id, self::ACF_CHECKBOX_FIELD );
				$item_content = array( 'message' => sprintf( __( 'Deleting %1$s', 'media-library-assistant' ) . '<br>', self::ACF_CHECKBOX_FIELD ) );
			} else {
				update_post_meta( $post_id, self::ACF_CHECKBOX_FIELD, explode( ',', self::$acf_checkbox_value ) );
				$item_content = array( 'message' => sprintf( __( 'Adding %1$s = %2$s', 'media-library-assistant' ) . '<br>', self::ACF_CHECKBOX_FIELD, self::$acf_checkbox_value ) );
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
		 * Replace the ACF Field Name with a more friendly Field Label
		 */
		$item_values['custom_fields'] = str_replace( '>acf_checkbox<', '>' . self::ACF_CHECKBOX_TITLE . '<', $item_values['custom_fields'] );
		$item_values['bulk_custom_fields'] = str_replace( '>acf_checkbox<', '>' . self::ACF_CHECKBOX_TITLE . '<', $item_values['bulk_custom_fields'] );

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
		if ( false !== $slug = array_search( self::ACF_CHECKBOX_FIELD, $columns ) ) {
			self::$field_slugs[ self::ACF_CHECKBOX_FIELD ] = $slug;

			/*
			 * Change the column slug so we can provide our own friendly content.
			 * Replace the entry for the column we're capturing, preserving its place in the list
			 */
			$new_columns = array();

			foreach ( $columns as $key => $value ) {
				if ( $key == $slug ) {
					$new_columns[ self::ACF_CHECKBOX_FIELD ] = self::ACF_CHECKBOX_TITLE;
				} else {
					$new_columns[ $key ] = $value;
				}
			} // foreach column

			$columns = $new_columns;
		}

		/*
		 * Add a column of our own for the repeater "where-used" information
		 */
		$columns[ 'acf_' . self::ACF_REPEATER_FIELD ] = self::ACF_REPEATER_TITLE;
		return $columns;
	} // mla_list_table_get_columns

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
		 * Replace the MLA custom field slug with our own slug value
		 */
		if ( isset( self::$field_slugs[ self::ACF_CHECKBOX_FIELD ] ) ) {
			$index = array_search( self::$field_slugs[ self::ACF_CHECKBOX_FIELD ], $hidden_columns );
			if ( false !== $index ) {
				$hidden_columns[ $index ] = self::ACF_CHECKBOX_FIELD;
			}
		}

		return $hidden_columns;
	} // mla_list_table_get_hidden_columns

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
		if ( isset( self::$field_slugs[ self::ACF_CHECKBOX_FIELD ] ) ) {
			$slug = self::$field_slugs[ self::ACF_CHECKBOX_FIELD ];
			if ( isset( $sortable_columns[ $slug ] ) ) {
				$new_columns = array();

				foreach ( $sortable_columns as $key => $value ) {
					if ( $key == $slug ) {
						$new_columns[ self::ACF_CHECKBOX_FIELD ] = $value;
					} else {
						$new_columns[ $key ] = $value;
					}
				} // foreach column

				$sortable_columns = $new_columns;
			} // slug found
		} // slug exists

		return $sortable_columns;
	} // mla_list_table_get_sortable_columns

	/**
	 * Translate post_status 'future', 'pending', 'draft' and 'trash' to label
	 *
	 * @since 1.02
	 * 
	 * @param	string	post_status
	 *
	 * @return	string	Status label or empty string
	 */
	protected static function _format_post_status( $post_status ) {
		$flag = ',<br>';
		switch ( $post_status ) {
			case 'draft' :
				$flag .= __('Draft');
				break;
			case 'future' :
				$flag .= __('Scheduled');
				break;
			case 'pending' :
				$flag .= _x('Pending', 'post state');
				break;
			case 'trash' :
				$flag .= __('Trash');
				break;
			default:
				$flag = '';
		}

	return $flag;
	}

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
		 * Convert the ACF-compatible array to a comma-delimited list of
		 * "checked" checkbox values.
		 */
		if ( self::ACF_CHECKBOX_FIELD == $column_name ) {
			$values = isset( $item->mla_item_acf_checkbox ) ? $item->mla_item_acf_checkbox : '';
			if ( empty( $values ) ) {
				return '';
			} elseif ( is_array( $values ) ) {
				return '[' . implode( '],[', $values ) . ']';
			} else {
				return $values;
			}
		}

		/*
		 * Retrieve and format the repeater field "where-used" information
		 */
		if ( ( 'acf_' . self::ACF_REPEATER_FIELD ) == $column_name ) {
			global $wpdb;

			$where_clause = self::ACF_REPEATER_FIELD . '_%_' . self::ACF_SUB_FIELD;
			$references = $wpdb->get_results( 
				"
				SELECT *
				FROM {$wpdb->postmeta}
				WHERE meta_key LIKE '{$where_clause}' AND meta_value = {$item->ID}
				"
			);

			$content = '';
			if ( ! empty( $references ) ) {
				$parents = array();
				foreach ( $references as $reference ) {
					// key on post_id to remove duplicates
					$parents[ $reference->post_id ] = $reference->post_id;
				}

				$parents = implode( ',', $parents );
				$references = $wpdb->get_results(
					"
					SELECT ID, post_type, post_status, post_title
					FROM {$wpdb->posts}
					WHERE ( post_type <> 'revision' ) AND ( ID IN ({$parents}) )
					"
				);

				foreach ( $references as $reference ) {
					$status = self::_format_post_status( $reference->post_status );

					if ( $reference->ID == $item->post_parent ) {
						$parent = ',<br>' . __( 'PARENT', 'media-library-assistant' );
					} else {
						$parent = '';
					}

					$content .= sprintf( '<a href="%1$s" title="' . __( 'Edit', 'media-library-assistant' ) . ' &#8220;%2$s&#8221;">%2$s</a> (%3$s %4$s%5$s%6$s), ',
						/*%1$s*/ esc_url( add_query_arg( array('post' => $reference->ID, 'action' => 'edit'), 'post.php' ) ),
						/*%2$s*/ esc_attr( $reference->post_title ),
						/*%3$s*/ esc_attr( $reference->post_type ),
						/*%4$s*/ $reference->ID,
						/*%5$s*/ $status,
						/*%6$s*/ $parent ) . "<br>\r\n";
				} // foreach $reference
			} // found $references
		} // found repeater column

		return $content;
	} // mla_list_table_column_default

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
		if ( ! isset( self::$field_slugs[ self::ACF_CHECKBOX_FIELD ] ) ) {
			return $inline_data;
		}

		/*
		 * Convert the ACF-compatible array to a comma-delimited list of
		 * "checked" checkbox values.
		 */
		$match_count = preg_match_all( '/\<div class="' . self::$field_slugs[ self::ACF_CHECKBOX_FIELD ] . '"\>(.*)\<\/div\>/', $inline_data, $matches, PREG_OFFSET_CAPTURE );
		if ( ( $match_count == false ) || ( $match_count == 0 ) ) {
			return $inline_data;
		}

		if ( isset( $item->mla_item_acf_checkbox ) ) {
			$value = $item->mla_item_acf_checkbox;
			if ( is_array( $value ) ) {
				$head = substr( $inline_data, 0, $matches[1][0][1] );
				$value = esc_html( implode( ',', $value ) );
				$tail = substr( $inline_data, ( $matches[1][0][1] + strlen( $matches[1][0][0] ) ) );
				$inline_data = $head . $value . $tail;
			}
		}

		return $inline_data;
	} // mla_list_table_build_inline_data
} // Class MLAACFExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLAACFExample::initialize');
?>
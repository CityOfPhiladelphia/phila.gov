<?php
/**
 * Provides an example of hooking the filters provided by the MLA_List_Table class
 *
 * @package MLA List Table Hooks Example
 * @version 1.09
 */

/*
Plugin Name: MLA List Table Hooks Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Provides an example of hooking the filters provided by the MLA_List_Table class
Author: David Lingren
Version: 1.09
Author URI: http://fairtradejudaica.org/our-story/staff/

Copyright 2014 - 2017 David Lingren

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
 * Class MLA List Table Hooks Example hooks all of the filters provided by the MLA_List_Table class
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding everything
 * else inside a class means this is the only name you have to worry about.
 *
 * @package MLA List Table Hooks Example
 * @since 1.00
 */
class MLAListTableHooksExample {
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
		  * Defined in /wp-admin/includes/class-wp-list-table.php
		  */
		add_filter( 'views_media_page_mla-menu', 'MLAListTableHooksExample::views_media_page_mla_menu', 10, 1 );
		add_filter( 'bulk_actions-media_page_mla-menu', 'MLAListTableHooksExample::bulk_actions_media_page_mla_menu', 10, 1 );
		add_filter( 'months_dropdown_results', 'MLAListTableHooksExample::months_dropdown_results', 10, 2 );
		add_filter( 'mla_entries_per_page', 'MLAListTableHooksExample::mla_entries_per_page', 10, 1 );
		add_filter( 'manage_media_page_mla-menu_sortable_columns', 'MLAListTableHooksExample::manage_media_page_mla_menu_sortable_columns', 10, 1 );

		 /*
		  * Defined in /media-library-assistant/includes/class-mla-objects.php
		  */
		add_filter( 'mla_taxonomy_get_columns', 'MLAListTableHooksExample::mla_taxonomy_get_columns', 10, 3 );
		add_filter( 'mla_taxonomy_column', 'MLAListTableHooksExample::mla_taxonomy_column', 10, 5 );
		add_filter( 'mla_taxonomy_column_final', 'MLAListTableHooksExample::mla_taxonomy_column_final', 10, 5 );

		 /*
		  * Defined in /media-library-assistant/includes/class-mla-data.php
		  */
		add_filter( 'mla_list_table_query_final_terms', 'MLAListTableHooksExample::mla_list_table_query_final_terms', 10, 1 );
		add_filter( 'mla_list_table_query_custom_items', 'MLAListTableHooksExample::mla_list_table_query_custom_items', 10, 2 );
		add_filter( 'mla_list_table_search_filter_fields', 'MLAListTableHooksExample::mla_list_table_search_filter_fields', 10, 2 );
		add_filter( 'mla_list_table_search_filter_inner_clause', 'MLAListTableHooksExample::mla_list_table_search_filter_inner_clause', 10, 4 );
		add_filter( 'mla_fetch_attachment_references', 'MLAListTableHooksExample::mla_fetch_attachment_references', 10, 3 );
		add_filter( 'mla_update_single_item', 'MLAListTableHooksExample::mla_update_single_item', 10, 3 );
		add_action( 'mla_updated_single_item', 'MLAListTableHooksExample::mla_updated_single_item', 10, 2 );

		 /*
		  * Defined in /media-library-assistant/includes/class-mla-main.php
		  */
		add_filter( 'mla_list_table_help_template', 'MLAListTableHooksExample::mla_list_table_help_template', 10, 3 );
		add_filter( 'mla_list_table_admin_action', 'MLAListTableHooksExample::mla_list_table_admin_action', 10, 3 );
		add_action( 'mla_list_table_custom_admin_action', 'MLAListTableHooksExample::mla_list_table_custom_admin_action', 10, 2 );
		add_filter( 'mla_list_table_inline_fields', 'MLAListTableHooksExample::mla_list_table_inline_fields', 10, 1 );
		add_filter( 'mla_list_table_inline_action', 'MLAListTableHooksExample::mla_list_table_inline_action', 10, 2 );

		add_filter( 'mla_list_table_bulk_action_initial_request', 'MLAListTableHooksExample::mla_list_table_bulk_action_initial_request', 10, 3 );
		add_filter( 'mla_list_table_begin_bulk_action', 'MLAListTableHooksExample::mla_list_table_begin_bulk_action', 10, 2 );

		add_filter( 'mla_list_table_bulk_action_item_request', 'MLAListTableHooksExample::mla_list_table_bulk_action_item_request', 10, 4 );
		add_filter( 'mla_list_table_bulk_action', 'MLAListTableHooksExample::mla_list_table_bulk_action', 10, 3 );
		add_filter( 'mla_list_table_custom_bulk_action', 'MLAListTableHooksExample::mla_list_table_custom_bulk_action', 10, 3 );
		add_filter( 'mla_list_table_end_bulk_action', 'MLAListTableHooksExample::mla_list_table_end_bulk_action', 10, 2 );

		add_filter( 'mla_list_table_single_action', 'MLAListTableHooksExample::mla_list_table_single_action', 10, 3 );
		add_filter( 'mla_list_table_custom_single_action', 'MLAListTableHooksExample::mla_list_table_custom_single_action', 10, 3 );
		add_action( 'mla_list_table_clear_filter_by', 'MLAListTableHooksExample::mla_list_table_clear_filter_by' );
		add_filter( 'mla_list_table_new_instance', 'MLAListTableHooksExample::mla_list_table_new_instance', 10, 1 );
		add_filter( 'mla_list_table_inline_values', 'MLAListTableHooksExample::mla_list_table_inline_values', 10, 1 );
		add_filter( 'mla_list_table_inline_template', 'MLAListTableHooksExample::mla_list_table_inline_template', 10, 1 );
		add_filter( 'mla_list_table_inline_parse', 'MLAListTableHooksExample::mla_list_table_inline_parse', 10, 3 );

		 /*
		  * Defined in /media-library-assistant/includes/class-mla-list-table.php
		  */
		add_filter( 'mla_list_table_get_columns', 'MLAListTableHooksExample::mla_list_table_get_columns', 10, 1 );
		add_filter( 'mla_list_table_get_hidden_columns', 'MLAListTableHooksExample::mla_list_table_get_hidden_columns', 10, 1 );
		add_filter( 'mla_list_table_get_sortable_columns', 'MLAListTableHooksExample::mla_list_table_get_sortable_columns', 10, 1 );
		add_filter( 'mla_list_table_get_bulk_actions', 'MLAListTableHooksExample::mla_list_table_get_bulk_actions', 10, 1 );
		add_filter( 'mla_list_table_extranav_actions', 'MLAListTableHooksExample::mla_list_table_extranav_actions', 10, 2 );
		add_action( 'mla_list_table_extranav_custom_action', 'MLAListTableHooksExample::mla_list_table_extranav_custom_action', 10, 2 );
		add_filter( 'mla_list_table_column_default', 'MLAListTableHooksExample::mla_list_table_column_default', 10, 3 );

		add_filter( 'mla_list_table_submenu_arguments', 'MLAListTableHooksExample::mla_list_table_submenu_arguments', 10, 2 );

		add_filter( 'mla_list_table_prepare_items_pagination', 'MLAListTableHooksExample::mla_list_table_prepare_items_pagination', 10, 2 );
		add_filter( 'mla_list_table_prepare_items_total_items', 'MLAListTableHooksExample::mla_list_table_prepare_items_total_items', 10, 2 );
		add_filter( 'mla_list_table_prepare_items_the_items', 'MLAListTableHooksExample::mla_list_table_prepare_items_the_items', 10, 2 );
		add_action( 'mla_list_table_prepare_items', 'MLAListTableHooksExample::mla_list_table_prepare_items', 10, 1 );

		add_filter( 'mla_list_table_build_rollover_actions', 'MLAListTableHooksExample::mla_list_table_build_rollover_actions', 10, 3 );
		add_filter( 'mla_list_table_build_inline_data', 'MLAListTableHooksExample::mla_list_table_build_inline_data', 10, 2 );

		// 'views_upload' is only applied when WPML is active
		add_filter( 'views_upload', 'MLAListTableHooksExample::views_upload', 10, 1 );

		 /*
		  * Defined in /media-library-assistant/includes/class-mla-edit-media.php
		  */
		add_filter( 'mla_upload_bulk_edit_form_values', 'MLAListTableHooksExample::mla_upload_bulk_edit_form_values', 10, 1 );
		add_filter( 'mla_upload_bulk_edit_form_template', 'MLAListTableHooksExample::mla_upload_bulk_edit_form_template', 10, 1 );
		add_filter( 'mla_upload_bulk_edit_form_parse', 'MLAListTableHooksExample::mla_upload_bulk_edit_form_parse', 10, 3 );
	}

	/**
	 * Views for media page MLA Menu
	 *
	 * This filter gives you an opportunity to filter the list of available list table views.
	 *
	 * @since 1.00
	 *
	 * @param	array	$views An array of available list table views.
	 *					format: view_slug => link to the view, with count
	 */
	public static function views_media_page_mla_menu( $views ) {
		//error_log( 'MLAListTableHooksExample::views_media_page_mla_menu $views = ' . var_export( $views, true ), 0 );
		return $views;
	} // views_media_page_mla_menu

	/**
	 * Filter the list table Bulk Actions drop-down
	 *
	 * This filter gives you an opportunity to filter the list table Bulk Actions drop-down.
	 *
	 * @since 1.00
	 *
	 * @param	array	$actions An array of the available bulk actions.
	 *					format: action_slug => Action Label
	 */
	public static function bulk_actions_media_page_mla_menu( $actions ) {
		//error_log( 'MLAListTableHooksExample::bulk_actions_media_page_mla_menu $actions = ' . var_export( $actions, true ), 0 );
		return $actions;
	} // bulk_actions_media_page_mla_menu

	/**
	 * Filter the 'Months' drop-down results
	 *
	 * This filter gives you an opportunity to filter the Months' drop-down.
	 *
	 * @since 1.00
	 *
	 * @param	array	$months    The months drop-down query result objects.
	 *					format: index => array( 'year' => year, 'month' => month )
	 * @param	string	$post_type The post type, e.g., 'attachment'.
	 */
	public static function months_dropdown_results( $months, $post_type ) {
		//error_log( "MLAListTableHooksExample::months_dropdown_results ({$post_type}) \$months = " . var_export( $months[0], true ), 0 );
		return $months;
	} // months_dropdown_results

	/**
	 * Filter the number of items to be displayed on each page of the list table
	 *
	 * This filter gives you an opportunity to filter the number of items to be displayed
	 * on each page of the list table.
	 *
	 * @since 1.00
	 *
	 * @param	integer	$per_page Number of items to be displayed. Default 20.
	 */
	public static function mla_entries_per_page( $per_page ) {
		//error_log( 'MLAListTableHooksExample::mla_entries_per_page $per_page = ' . var_export( $per_page, true ), 0 );
		return $per_page;
	} // mla_entries_per_page

	/**
	 * Filter the list table sortable columns for a specific screen
	 *
	 * This filter gives you an opportunity to filter the list table sortable columns.
	 *
	 * @since 1.00
	 *
	 * @param	array	$sortable_columns	An array of sortable columns.
	 *										Format: 'column_slug' => 'orderby'
	 *										or 'column_slug' => array( 'orderby', true )
	 *
	 * The second format will make the initial sorting order be descending.
	 */
	public static function manage_media_page_mla_menu_sortable_columns( $sortable_columns ) {
		//error_log( 'MLAListTableHooksExample::manage_media_page_mla_menu_sortable_columns $sortable_columns = ' . var_export( $sortable_columns, true ), 0 );
		return $sortable_columns;
	} // manage_media_page_mla_menu_sortable_columns

	/**
	 * Records the list of active search fields
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $search_fields = array();

	/**
	 * Process the list of fields for keywords search 
	 *
	 * This filter gives you an opportunity to add or remove any of the MLA standard fields for Search Media.
	 *
	 * @since 1.00
	 *
	 * @param	array	$active_fields	Fields that will be searched.
	 * @param	array	$all_fields		All of the fields that can be searched.
	 */
	public static function mla_list_table_search_filter_fields( $active_fields, $all_fields ) {
		//error_log( 'MLAListTableHooksExample::mla_list_table_search_filter_fields $active_fields = ' . var_export( $active_fields, true ), 0 );
		//error_log( 'MLAListTableHooksExample::mla_list_table_search_filter_fields $all_fields = ' . var_export( $all_fields, true ), 0 );

		if ( in_array( 'name', $active_fields ) ) {
			/* Uncomment next line to add File URL (guid) to the list of active search fields
			   when the "Name" box below the Search Media text box is checked */ 
			//$active_fields[] = 'guid';
		}

		// Uncomment next line to ALWAYS add File URL (guid) to the list of active search fields
		//$active_fields[] = 'guid';
		self::$search_fields = $active_fields;

		return $active_fields;
	} // mla_list_table_search_filter_fields

	/**
	 * Process the inner WHERE clause for keywords search 
	 *
	 * This filter gives you an opportunity to modify or add to the inner WHERE clause for Search Media.
	 *
	 * @since 1.00
	 *
	 * @param	string	$inner_clause		Current SQL inner WHERE clause.
	 * @param	string	$inner_connector	AND/OR connector between the search field clauses.
	 * @param	string	$wpdb_posts			Name of the POSTS database table.
	 * @param	string	$sql_term			Keyword value for the search.
	 */
	public static function mla_list_table_search_filter_inner_clause( $inner_clause, $inner_connector, $wpdb_posts, $sql_term ) {
		//error_log( 'MLAListTableHooksExample::mla_list_table_search_filter_fields $inner_clause = ' . var_export( $inner_clause, true ), 0 );
		//error_log( 'MLAListTableHooksExample::mla_list_table_search_filter_fields $inner_connector = ' . var_export( $inner_connector, true ), 0 );
		//error_log( 'MLAListTableHooksExample::mla_list_table_search_filter_fields $wpdb_posts = ' . var_export( $wpdb_posts, true ), 0 );
		//error_log( 'MLAListTableHooksExample::mla_list_table_search_filter_fields $sql_term = ' . var_export( $sql_term, true ), 0 );

		if ( in_array( 'guid', self::$search_fields ) ) {
			$inner_clause .= "{$inner_connector}({$wpdb_posts}.guid LIKE {$sql_term})";
		}

		return $inner_clause;
	} // mla_list_table_search_filter_inner_clause

	/**
	 * Pre-process the Edit Taxonomy submenu table columns
	 *
	 * This filter gives you an opportunity to change the columns defined
	 * for the Edit Taxonomy submenu table(s).
	 *
	 * @since 1.06
	 *
	 * @param	NULL	$filter_columns NULL, indicating no changes to the default processing.
	 * @param	array	$columns Column definitions for the edit taxonomy list table.
	 * @param	string	$taxonomy Slug of the taxonomy for this submenu.
	 */
	public static function mla_taxonomy_get_columns( $filter_columns, $columns, $taxonomy ) {
		//error_log( 'MLAListTableHooksExample::mla_taxonomy_get_columns $columns = ' . var_export( $columns, true ), 0 );
		//error_log( 'MLAListTableHooksExample::mla_taxonomy_get_columns $taxonomy = ' . var_export( $taxonomy, true ), 0 );
		return $filter_columns;
	} // mla_taxonomy_get_columns

	/**
	 * Pre-process Edit Taxonomy submenu table column content
	 *
	 * This filter gives you an opportunity to change column content in
	 * for the Edit Taxonomy submenu table(s) before MLA computes the count.
	 *
	 * @since 1.06
	 *
	 * @param	NULL	$filter_content NULL, indicating no changes to the current content.
	 * @param	string	$current_value Current column content.
	 * @param	string	$column_name Slug of the column definition.
	 * @param	integer	$term_id ID of the current term.
	 * @param	string	$taxonomy Slug of the taxonomy for this submenu.
	 */
	public static function mla_taxonomy_column( $filter_content, $current_value, $column_name, $term_id, $taxonomy ) {
		//error_log( 'MLAListTableHooksExample::mla_taxonomy_column $current_value = ' . var_export( $current_value, true ), 0 );
		//error_log( 'MLAListTableHooksExample::mla_taxonomy_column $column_name = ' . var_export( $column_name, true ), 0 );
		//error_log( 'MLAListTableHooksExample::mla_taxonomy_column $term_id = ' . var_export( $term_id, true ), 0 );
		//error_log( 'MLAListTableHooksExample::mla_taxonomy_column $taxonomy = ' . var_export( $taxonomy, true ), 0 );
		return $filter_content;
	} // mla_taxonomy_column

	/**
	 * Post-process Edit Taxonomy submenu table column content
	 *
	 * This filter gives you an opportunity to change column content in
	 * for the Edit Taxonomy submenu table(s) after MLA computes the count.
	 *
	 * @since 1.09
	 *
	 * @param	NULL	$filter_content NULL, indicating no changes to the current content.
	 * @param	object	$tax_object Defines the current taxonomy.
	 * @param	object	$term Defines the current term.
	 * @param	string	$column_text MLA-computed count or "click to search".
	 * @param	boolean	$count_terms True to compute counts.
	 */
	public static function mla_taxonomy_column_final( $filter_content, $tax_object, $term, $column_text, $count_terms ) {
		//error_log( 'MLAListTableHooksExample::mla_taxonomy_column_final $tax_object = ' . var_export( $tax_object, true ), 0 );
		//error_log( 'MLAListTableHooksExample::mla_taxonomy_column_final $term = ' . var_export( $term, true ), 0 );
		//error_log( 'MLAListTableHooksExample::mla_taxonomy_column_final $column_text = ' . var_export( $column_text, true ), 0 );
		//error_log( 'MLAListTableHooksExample::mla_taxonomy_column_final $count_terms = ' . var_export( $count_terms, true ), 0 );
		return $filter_content;
	} // mla_taxonomy_column_final

	/**
	 * Process the "where-used" reference reporting results 
	 *
	 * This filter gives you an opportunity to modify or add to the "where-used" reference reporting information.
	 *
	 * @since 1.00
	 *
	 * @param	array	$references	Current attachment reference information.
	 * @param	integer	$post_id	Attachment ID.
	 * @param	integer	$parent_id	Attachment's parent ID (or zero).
	 */
	public static function mla_fetch_attachment_references( $references, $post_id, $parent_id ) {
		//error_log( 'MLAListTableHooksExample::mla_fetch_attachment_references $references = ' . var_export( $references, true ), 0 );
		//error_log( 'MLAListTableHooksExample::mla_fetch_attachment_references $post_id = ' . var_export( $post_id, true ), 0 );
		//error_log( 'MLAListTableHooksExample::mla_fetch_attachment_references $parent_id = ' . var_export( $parent_id, true ), 0 );

		/*
		 * $references contains:
		 *
		 * tested_reference	true if any of the four where-used types was processed
		 * found_reference	true if any where-used array is not empty()
		 * found_parent		true if $parent matches a where-used post ID
		 * is_unattached	true if $parent is zero (0)
		 * base_file		relative path and name of the uploaded file, e.g., 2012/04/image.jpg
		 * path				path to the file, relative to the "uploads/" directory, e.g., 2012/04/
		 * file				The name portion of the base file, e.g., image.jpg
		 * files			base file and any other image size files. Array key is path and file name.
		 *					Non-image file value is a string containing file name without path
		 *					Image file value is an array with file name, width and height
		 * features			Array of objects with the post_type and post_title of each post
		 *					that has the attachment as a "Featured Image"
		 * inserts			Array of specific files (i.e., sizes) found in one or more posts/pages
		 *					as an image (<img>) or link (<a href>). The array key is the path and file name.
		 *					The array value is an array with the ID, post_type and post_title of each reference
		 * mla_galleries	Array of objects with the post_type and post_title of each post
		 *					that was returned by an [mla_gallery] shortcode
		 * galleries		Array of objects with the post_type and post_title of each post
		 *					that was returned by a [gallery] shortcode
		 * parent_type		'post' or 'page' or the custom post type of the attachment's parent
		 * parent_title		post_title of the attachment's parent
		 * parent_errors	UNATTACHED, ORPHAN, BAD/INVALID PARENT
		 */
		return $references;
	} // mla_fetch_attachment_references

	/**
	 * Pre-process a Media Library item update action
	 *
	 * This filter gives you an opportunity to modify, delete or add to updates before they are applied.
	 *
	 * @since 1.06
	 *
	 * @param	array	$updates	( 'new_data' => array, 'tax_input' => array, 'tax_actions' => array ).
	 * @param	integer	$post_id	ID of the item being updated.
	 * @param	array	$post_data	Current item content.
	 */
	public static function mla_update_single_item( $updates, $post_id, $post_data ) {
		//error_log( 'MLAListTableHooksExample::mla_update_single_item $updates = ' . var_export( $updates, true ), 0 );
		//error_log( 'MLAListTableHooksExample::mla_update_single_item $post_id = ' . var_export( $post_id, true ), 0 );
		//error_log( 'MLAListTableHooksExample::mla_update_single_item $post_data = ' . var_export( $post_data, true ), 0 );

		/*
		 * You can modify the content of any $updates element ot remove it to prevent updates
		 */
		 
		return $updates;
	} // mla_update_single_item

	/**
	 * POST-process a Media Library item update action
	 *
	 * This action gives you an opportunity to work with the item after updates have been applied.
	 *
	 * @since 1.06
	 *
	 * @param	integer	$post_id ID of the item that was updated.
	 * @param	integer	$result	Zero if the update failed else ID of the item that was updated.
	 */
	public static function mla_updated_single_item( $post_id, $result ) {
		//error_log( 'MLAListTableHooksExample::mla_updated_single_item $post_id = ' . var_export( $post_id, true ), 0 );
		//error_log( 'MLAListTableHooksExample::mla_updated_single_item $result = ' . var_export( $result, true ), 0 );
	} // mla_updated_single_item

	/**
	 * Pre-process an MLA_List_Table admin action
	 *
	 * This filter gives you an opportunity to pre-process an MLA_List_Table item-level action,
	 * standard or custom, before the MLA handler. This filter is called before anything is output
	 * for the Media/Assistant submenu, so you can redirect to another admin screen if desired.
	 *
	 * @since 1.03
	 *
	 * @param	boolean	$process_action		true, to let MLA process the requested action.
	 * @param	string	$mla_admin_action	The requested action.
	 * @param	integer	$mla_item_ID		Zero (0), or the affected attachment.
	 */
	public static function mla_list_table_admin_action( $process_action, $mla_admin_action, $mla_item_ID ) {
		//error_log( 'MLAListTableHooksExample::mla_list_table_admin_action $mla_admin_action = ' . var_export( $mla_admin_action, true ), 0 );
		//error_log( 'MLAListTableHooksExample::mla_list_table_admin_action $mla_item_ID = ' . var_export( $mla_item_ID, true ), 0 );
		return $process_action;
	} // mla_list_table_admin_action

	/**
	 * Load the MLA_List_Table dropdown help menu template
	 *
	 * This filter gives you the opportunity to add material to the "Help" dropdown menu
	 * on the Media/Assistant submenu screen.
	 *
	 * @since 1.04
	 *
	 * @param	NULL	$template_array NULL, indicating no replacement template.
	 * @param	string	$file_name The complete name of the default template file.
	 * @param	string	$file_suffix The $screen->id or hook suffix part of the  template file name.
	 */
	public static function mla_list_table_help_template( $template_array, $file_name, $file_suffix ) {
		//error_log( "MLAListTableHooksExample::mla_list_table_custom_admin_action( {$file_name}, {$file_suffix} )", 0 );

		// To augment the default template, first load the MLA standard value
		// $template_array = MLAData::mla_load_template( $file_name );

		return $template_array;
	} // mla_list_table_help_template

	/**
	 * Process an MLA_List_Table custom admin action
	 *
	 * This filter gives you an opportunity to process an MLA_List_Table item-level action
	 * that MLA does not recognize. This filter is called before anything is output for the
	 * Media/Assistant submenu, so you can redirect to another admin screen if desired.
	 *
	 * @since 1.03
	 *
	 * @param	string	$mla_admin_action	The requested action.
	 * @param	integer	$mla_item_ID		Zero (0), or the affected attachment.
	 */
	public static function mla_list_table_custom_admin_action( $mla_admin_action, $mla_item_ID ) {
		//error_log( 'MLAListTableHooksExample::mla_list_table_custom_admin_action $mla_admin_action = ' . var_export( $mla_admin_action, true ), 0 );
		//error_log( 'MLAListTableHooksExample::mla_list_table_custom_admin_action $mla_item_ID = ' . var_export( $mla_item_ID, true ), 0 );
	} // mla_list_table_custom_admin_action

	/**
	 * Process an MLA_List_Table inline action, i.e., Quick Edit 
	 *
	 * This filter gives you an opportunity to pre-process an MLA_List_Table "Quick Edit"
	 * action before the MLA handler.
	 *
	 * @since 1.00
	 *
	 * @param	NULL	$item_content	NULL, indicating no handler.
	 * @param	integer	$post_id		The affected attachment.
	 */
	public static function mla_list_table_inline_action( $item_content, $post_id ) {
		//error_log( 'MLAListTableHooksExample::mla_list_table_inline_action $post_id = ' . var_export( $post_id, true ), 0 );

		/*
		 * Return NULL if not handled here, otherwise
		 *	array( 'message' => error or status message(s), 'body' => '',
		 *	'prevent_default' => true to bypass the MLA handler )
		 */
		return $item_content;
	} // mla_list_table_inline_action

	/**
	 * Pre-filter MLA_List_Table bulk action request parameters
	 *
	 * This filter gives you an opportunity to pre-process the request parameters for a bulk action
	 * before the action begins. DO NOT assume parameters come from the $_REQUEST super array!
	 *
	 * @since 1.02
	 *
	 * @param	array	$request		Bulk action request parameters, including ['mla_bulk_action_do_cleanup'].
	 * @param	string	$bulk_action	The requested action.
	 * @param	array	$custom_field_map	[ slug => field_name ]
	 */
	public static function mla_list_table_bulk_action_initial_request( $request, $bulk_action, $custom_field_map ) {
		//error_log( "MLAListTableHooksExample::mla_list_table_bulk_action_initial_request( {$bulk_action} ) request = " . var_export( $request, true ), 0 );
		//error_log( "MLAListTableHooksExample::mla_list_table_bulk_action_initial_request( {$bulk_action} ) custom_field_map = " . var_export( $custom_field_map, true ), 0 );
		/*
		 * $request['mla_bulk_action_do_cleanup'] is true on input if the parameters come from $_REQUEST.
		 * If it is true on output the bulk action request parameters will be removed from $_REQUEST.
		 */ 
		return $request;
	} // mla_list_table_bulk_action_initial_request

	/**
	 * Begin an MLA_List_Table bulk action
	 *
	 * This filter gives you an opportunity to pre-process an MLA_List_Table bulk action,
	 * standard or custom, before the MLA handler. The filter is called once before any
	 * of the items in $_REQUEST['cb_attachment'] are processed.
	 *
	 * @since 1.01
	 *
	 * @param	NULL	$item_content	NULL, indicating no handler.
	 * @param	string	$bulk_action	The requested action.
	 */
	public static function mla_list_table_begin_bulk_action( $item_content, $bulk_action ) {
		//error_log( "MLAListTableHooksExample::mla_list_table_begin_bulk_action( {$bulk_action} )", 0 );

		/*
		 * Return NULL if not handled here, otherwise
		 *	array( 'message' => error or status message(s), 'body' => '',
		 *	'prevent_default' => true to bypass the MLA handler )
		 */
		return $item_content;
	} // mla_list_table_begin_bulk_action

	/**
	 * Filter MLA_List_Table bulk action request parameters for each item
	 *
	 * This filter gives you an opportunity to pre-process the request parameters for each item
	 * During a bulk action. DO NOT assume parameters come from the $_REQUEST super array!
	 *
	 * @since 1.02
	 *
	 * @param	array	$request		Bulk action request parameters, including ['mla_bulk_action_do_cleanup'].
	 * @param	string	$bulk_action	The requested action.
	 * @param	integer	$post_id		The affected attachment.
	 * @param	array	$custom_field_map	[ slug => field_name ]
	 */
	public static function mla_list_table_bulk_action_item_request( $request, $bulk_action, $post_id, $custom_field_map ) {
		//error_log( "MLAListTableHooksExample::mla_list_table_bulk_action_item_request( {$bulk_action}, {$post_id} ) request = " . var_export( $request, true ), 0 );
		//error_log( "MLAListTableHooksExample::mla_list_table_bulk_action_item_request( {$bulk_action}, {$post_id} ) custom_field_map = " . var_export( $custom_field_map, true ), 0 );
		return $request;
	} // mla_list_table_bulk_action_item_request

	/**
	 * Process an MLA_List_Table bulk action
	 *
	 * This filter gives you an opportunity to pre-process an MLA_List_Table bulk action,
	 * standard or custom, before the MLA handler.
	 * The filter is called once for each of the items in $_REQUEST['cb_attachment'].
	 *
	 * @since 1.00
	 *
	 * @param	NULL	$item_content	NULL, indicating no handler.
	 * @param	string	$bulk_action	The requested action.
	 * @param	integer	$post_id		The affected attachment.
	 */
	public static function mla_list_table_bulk_action( $item_content, $bulk_action, $post_id ) {
		//error_log( "MLAListTableHooksExample::mla_list_table_bulk_action( {$bulk_action}, {$post_id} )", 0 );

		/*
		 * Return NULL if not handled here, otherwise
		 *	array( 'message' => error or status message(s), 'body' => '',
		 *	'prevent_default' => true to bypass the MLA handler )
		 */
		return $item_content;
	} // mla_list_table_bulk_action

	/**
	 * Process an MLA_List_Table custom bulk action
	 *
	 * This filter gives you an opportunity to process an MLA_List_Table bulk action
	 * that MLA does not recognize. The filter is called once for each of the items
	 * in $_REQUEST['cb_attachment'].
	 *
	 * @since 1.00
	 *
	 * @param	NULL	$item_content	NULL, indicating no handler.
	 * @param	string	$bulk_action	The requested action.
	 * @param	integer	$post_id		The affected attachment.
	 */
	public static function mla_list_table_custom_bulk_action( $item_content, $bulk_action, $post_id ) {
		//error_log( "MLAListTableHooksExample::mla_list_table_custom_bulk_action( {$bulk_action}, {$post_id} )", 0 );

		/*
		 * Return NULL if not handled here, otherwise
		 *	array( 'message' => error or status message(s), 'body' => '',
		 *	'prevent_default' => true to bypass the MLA handler )
		 */
		return $item_content;
	} // mla_list_table_custom_bulk_action

	/**
	 * End an MLA_List_Table bulk action
	 *
	 * This filter gives you an opportunity to post-process an MLA_List_Table bulk action,
	 * standard or custom. The filter is called once after all of the items in
	 * $_REQUEST['cb_attachment'] are processed.
	 *
	 * @since 1.01
	 *
	 * @param	NULL	$item_content	NULL, indicating no handler.
	 * @param	string	$bulk_action	The requested action.
	 */
	public static function mla_list_table_end_bulk_action( $item_content, $bulk_action ) {
		//error_log( "MLAListTableHooksExample::mla_list_table_end_bulk_action( $bulk_action )", 0 );

		/*
		 * Return NULL if not handled here, otherwise
		 *	array( 'message' => error or status message(s), 'body' => '',
		 *	'prevent_default' => true to bypass the MLA handler )
		 */
		return $item_content;
	} // mla_list_table_end_bulk_action

	/**
	 * Process an MLA_List_Table single action
	 *
	 * This filter gives you an opportunity to pre-process an MLA_List_Table page-level
	 * or single-item action, standard or custom, before the MLA handler.
	 *
	 * @since 1.00
	 *
	 * @param	NULL	$page_content 		NULL, indicating no handler.
	 * @param	string	$mla_admin_action	The requested action.
	 * @param	integer	$mla_item_ID		Zero (0), or the affected attachment.
	 */
	public static function mla_list_table_single_action( $page_content, $mla_admin_action, $mla_item_ID ) {
		//error_log( 'MLAListTableHooksExample::mla_list_table_single_action $mla_admin_action = ' . var_export( $mla_admin_action, true ), 0 );
		//error_log( 'MLAListTableHooksExample::mla_list_table_single_action $mla_item_ID = ' . var_export( $mla_item_ID, true ), 0 );

		/*
		 * Return NULL if not handled here, otherwise
		 *	array( 'message' => error or status message(s), 'body' => '',
		 *	'prevent_default' => true to bypass the MLA handler )
		 */
		return $page_content;
	} // mla_list_table_single_action

	/**
	 * Process an MLA_List_Table custom single action
	 *
	 * This filter gives you an opportunity to process an MLA_List_Table page-level
	 * or single-item action that MLA does not recognize.
	 *
	 * @since 1.00
	 *
	 * @param	NULL	$page_content 		NULL, indicating no handler.
	 * @param	string	$mla_admin_action	The requested action.
	 * @param	integer	$mla_item_ID		Zero (0), or the affected attachment.
	 */
	public static function mla_list_table_custom_single_action( $page_content, $mla_admin_action, $mla_item_ID ) {
		//error_log( 'MLAListTableHooksExample::mla_list_table_custom_single_action $mla_admin_action = ' . var_export( $mla_admin_action, true ), 0 );
		//error_log( 'MLAListTableHooksExample::mla_list_table_custom_single_action $mla_item_ID = ' . var_export( $mla_item_ID, true ), 0 );

		/*
		 * Return NULL if not handled here, otherwise
		 *	array( 'message' => error or status message(s), 'body' => '',
		 *	'prevent_default' => true to bypass the MLA handler )
		 */
		return $page_content;
	} // mla_list_table_custom_single_action

	/**
	 * Clear the custom "Filter-by" parameters
	 *
	 * This action gives you an opportunity to clear any custom submenu "Filter-by" parameters.
	 *
	 * @since 1.00
	 */
	public static function mla_list_table_clear_filter_by() {
		//error_log( 'MLAListTableHooksExample::mla_list_table_clear_filter_by $_REQUEST = ' . var_export( $_REQUEST, true ), 0 );
	} // mla_list_table_clear_filter_by

	/**
	 * Extend the MLA_List_Table class
	 *
	 * This filter gives you an opportunity to extend the MLA_List_Table class.
	 *
	 * @since 1.00
	 *
	 * @param	NULL	$mla_list_table NULL, indicating no extension/use the base class.
	 */
	public static function mla_list_table_new_instance( $mla_list_table ) {
		/*
		 * Return NULL to use the base class, otherwise create an instance of
		 * the extended class and return the class object.
		 */
		return $mla_list_table;
	} // mla_list_table_new_instance

	/**
	 * MLA_List_Table inline edit item values
	 *
	 * This filter gives you a chance to modify and extend the substitution values
	 * for the Quick and Bulk Edit forms.
	 *
	 * @since 1.00
	 *
	 * @param	array	$item_values [ parameter_name => parameter_value ] pairs
	 */
	public static function mla_list_table_inline_values( $item_values ) {
		//error_log( 'MLAListTableHooksExample::mla_list_table_inline_values $item_values = ' . var_export( $item_values, true ), 0 );
		return $item_values;
	} // mla_list_table_inline_values

	/**
	 * MLA_List_Table inline edit template
	 *
	 * This filter gives you a chance to modify and extend the template used
	 * for the Quick and Bulk Edit forms.
	 *
	 * @since 1.00
	 *
	 * @param	string	$item_template Template used to generate the HTML markup
	 */
	public static function mla_list_table_inline_template( $item_template ) {
		//error_log( 'MLAListTableHooksExample::mla_list_table_inline_template $item_template = ' . var_export( $item_template, true ), 0 );
		return $item_template;
	} // mla_list_table_inline_template

	/**
	 * MLA_List_Table inline edit parse
	 *
	 * @since 1.00
	 *
	 * This filter gives you a final chance to modify and extend the HTML
	 * markup used for the Quick and Bulk Edit forms.
	 *
	 * @param	string	$html_markup HTML markup returned by the template parser
	 * @param	string	$item_template Template used to generate the HTML markup
	 * @param	array	$item_values [ parameter_name => parameter_value ] pairs
	 */
	public static function mla_list_table_inline_parse( $html_markup, $item_template, $item_values ) {
		//error_log( 'MLAListTableHooksExample::mla_list_table_inline_parse $html_markup = ' . var_export( $html_markup, true ), 0 );
		//error_log( 'MLAListTableHooksExample::mla_list_table_inline_parse $item_template = ' . var_export( $item_template, true ), 0 );
		//error_log( 'MLAListTableHooksExample::mla_list_table_inline_parse $item_values = ' . var_export( $item_values, true ), 0 );
		return $html_markup;
	} // mla_list_table_inline_parse

	/**
	 * Filter the MLA_List_Table columns
	 *
	 * This MLA-specific filter gives you an opportunity to filter the list table columns.
	 *
	 * @since 1.00
	 *
	 * @param	array	$columns An array of columns.
	 *					format: column_slug => Column Label
	 */
	public static function mla_list_table_get_columns( $columns ) {
		//error_log( 'MLAListTableHooksExample::mla_list_table_get_columns $columns = ' . var_export( $columns, true ), 0 );
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
	 */
	public static function mla_list_table_get_hidden_columns( $hidden_columns ) {
		//error_log( 'MLAListTableHooksExample::mla_list_table_get_hidden_columns $hidden_columns = ' . var_export( $hidden_columns, true ), 0 );
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
	 */
	public static function mla_list_table_get_sortable_columns( $sortable_columns ) {
		//error_log( 'MLAListTableHooksExample::mla_list_table_get_sortable_columns $sortable_columns = ' . var_export( $sortable_columns, true ), 0 );
		return $sortable_columns;
	} // mla_list_table_get_sortable_columns

	/**
	 * Filter the MLA_List_Table bulk actions
	 *
	 * This MLA-specific filter gives you an opportunity to filter the list of bulk actions;
	 * a good alternative to the 'bulk_actions-media_page_mla-menu' filter.
	 *
	 * @since 1.01
	 *
	 * @param	array	$actions	An array of bulk actions.
	 *								Format: 'slug' => 'Label'
	 */
	public static function mla_list_table_get_bulk_actions( $actions ) {
		//error_log( 'MLAListTableHooksExample::mla_list_table_get_bulk_actions $actions = ' . var_export( $actions, true ), 0 );
		return $actions;
	} // mla_list_table_get_bulk_actions

	/**
	 * Filter the MLA_List_Table "extra tablenav" actions list
	 *
	 * This MLA-specific filter gives you an opportunity to add, remove and/or re-order the
	 * controls added to the top & bottom table navigation areas.
	 *
	 * @since 1.08
	 *
	 * @param	array	$actions	An array of extranav action labels.
	 * @param	string	$which		'top' or 'bottom'.
	 */
	public static function mla_list_table_extranav_actions( $actions, $which ) {
		//error_log( "MLAListTableHooksExample::mla_list_table_extranav_actions( {$which} ) $actions = " . var_export( $actions, true ), 0 );
		return $actions;
	} // mla_list_table_extranav_actions

	/**
	 * Supply a custom MLA_List_Table "extra tablenav" action
	 *
	 * Called when the MLA_List_Table can't find a value for an extranav action.
	 * Echo the HTML content of the custom action, return nothing.
	 *
	 * @since 1.08
	 *
	 * @param	array	$action	extranav action label.
	 * @param	string	$which	'top' or 'bottom'.
	 */
	public static function mla_list_table_extranav_custom_action( $actions, $which ) {
		//error_log( "MLAListTableHooksExample::mla_list_table_extranav_custom_action( {$which} ) $actions = " . var_export( $actions, true ), 0 );
		return $actions;
	} // mla_list_table_extranav_custom_action

	/**
	 * Supply a column value if no column-specific function has been defined
	 *
	 * Called when the MLA_List_Table can't find a value for a given column.
	 *
	 * @since 1.00
	 *
	 * @param	NULL	$content NULL, indicating no default content
	 * @param	array	$item A singular item (one full row's worth of data)
	 * @param	array	$column_name The name/slug of the column to be processed
	 */
	public static function mla_list_table_column_default( $content, $item, $column_name ) {
		//error_log( "MLAListTableHooksExample::mla_list_table_column_default ({$column_name}) \$item = " . var_export( $item, true ), 0 );

		/*
		 * Return NULL if the column is not handled, otherwise return a string
		 * with the column content.
		 */
		return $content;
	} // mla_list_table_column_default

	/**
	 * Filter the "sticky" submenu URL parameters
	 *
	 * This filter gives you an opportunity to filter the URL parameters that will be
	 * retained when the submenu page refreshes.
	 *
	 * @since 1.00
	 *
	 * @param	array	$submenu_arguments	Current view, pagination and sort parameters.
	 * @param	object	$include_filters	True to include "filter-by" parameters, e.g., year/month dropdown.
	 */
	public static function mla_list_table_submenu_arguments( $submenu_arguments, $include_filters ) {
		//error_log( 'MLAListTableHooksExample::mla_list_table_submenu_arguments $submenu_arguments = ' . var_export( $submenu_arguments, true ), 0 );
		//error_log( 'MLAListTableHooksExample::mla_list_table_submenu_arguments $include_filters = ' . var_export( $include_filters, true ), 0 );
		return $submenu_arguments;
	} // mla_list_table_submenu_arguments

	/**
	 * Filter the pagination parameters for prepare_items()
	 *
	 * This filter gives you an opportunity to filter the per_page and current_page
	 * parameters used for the prepare_items database query.
	 *
	 * @since 1.00
	 *
	 * @param	array	$pagination		Contains 'per_page', 'current_page'.
	 * @param	object	$mla_list_table	The MLA_List_Table object, passed by reference.
	 */
	public static function mla_list_table_prepare_items_pagination( $pagination, $mla_list_table ) {
		//error_log( 'MLAListTableHooksExample::mla_list_table_prepare_items_pagination $pagination = ' . var_export( $pagination, true ), 0 );
		//error_log( 'MLAListTableHooksExample::mla_list_table_prepare_items_pagination $mla_list_table->get_pagenum() = ' . var_export( $mla_list_table->get_pagenum(), true ), 0 );
		return $pagination;
	} // mla_list_table_prepare_items_pagination

	/**
	 * Filter the total items count for prepare_items()
	 *
	 * This filter gives you an opportunity to substitute your own $total_items
	 * parameter used for the prepare_items database query.
	 *
	 * @since 1.00
	 *
	 * @param	NULL	$total_items	NULL, indicating no substitution.
	 * @param	object	$mla_list_table	The MLA_List_Table object, passed by reference.
	 */
	public static function mla_list_table_prepare_items_total_items( $total_items, $mla_list_table ) {
		//error_log( 'MLAListTableHooksExample::mla_list_table_prepare_items_total_items $mla_list_table->get_pagenum() = ' . var_export( $mla_list_table->get_pagenum(), true ), 0 );

		/*
		 * Return NULL to let MLA calculate the total, otherwise return an integer
		 * with the updated total.
		 */
		return $total_items;
	} // mla_list_table_prepare_items_total_items

	/**
	 * Filter the items returned by prepare_items()
	 *
	 * This filter gives you an opportunity to substitute your own items array
	 * in place of the default prepare_items database query.
	 *
	 * @since 1.00
	 *
	 * @param	NULL	$items			NULL, indicating no substitution.
	 * @param	object	$mla_list_table	The MLA_List_Table object, passed by reference.
	 */
	public static function mla_list_table_prepare_items_the_items( $items, $mla_list_table ) {
		//error_log( 'MLAListTableHooksExample::mla_list_table_prepare_items_the_items $mla_list_table->get_pagenum() = ' . var_export( $mla_list_table->get_pagenum(), true ), 0 );

		/*
		 * Return NULL to use the default $items array, otherwise return an array
		 * with the updated items array.
		 */
		return $items;
	} // mla_list_table_prepare_items_the_items

	/**
	 * Filter the WP_Query request parameters for the prepare_items query
	 *
	 * Gives you an opportunity to change the terms of the prepare_items query
	 * after they are processed by the "Prepare List Table Query" handler.
	 *
	 * @since 1.03
	 *
	 * @param	array	$request WP_Query request prepared by "Prepare List Table Query"
	 */
	public static function mla_list_table_query_final_terms( $request ) {
		//error_log( 'MLAListTableHooksExample::mla_list_table_query_final_terms $request = ' . var_export( $request, true ), 0 );

		return $request;
	} // mla_list_table_query_final_terms

	/**
	 * Replace the prepare_items WP_Query object with your own results
	 *
	 * Gives you an opportunity to substitute the results of the prepare_items query
	 * with alternative results of your own.
	 *
	 * @since 1.03
	 *
	 * @param	object	$wp_query_object NULL, indicating no results substitution
	 * @param	array	$request WP_Query request prepared by "Prepare List Table Query"
	 */
	public static function mla_list_table_query_custom_items( $wp_query_object, $request ) {
		//error_log( 'MLAListTableHooksExample::mla_media_modal_query_custom_items $request = ' . var_export( $request, true ), 0 );

		/*
		 * You can replace the NULL $wp_query_object with a new WP_Query( $request )
		 * object using your own $request parameters
		 */
		return $wp_query_object;
	} // mla_media_modal_query_custom_items

	/**
	 * Inspect or modify the results of prepare_items()
	 *
	 * This action gives you an opportunity to record or modify the results of the
	 * prepare_items database query. 
	 *
	 * @since 1.00
	 *
	 * @param	object	$mla_list_table	The MLA_List_Table object, passed by reference.
	 */
	public static function mla_list_table_prepare_items( $mla_list_table ) {
		//error_log( 'MLAListTableHooksExample::mla_list_table_prepare_items $mla_list_table->get_pagenum() = ' . var_export( $mla_list_table->get_pagenum(), true ), 0 );

		/*
		 * $mla_list_table->items contains the requested items from the database
		 */
	} // mla_list_table_prepare_items

	/**
	 * Filter the list of item "Rollover" actions
	 *
	 * This filter gives you an opportunity to filter the list of "Rollover" actions
	 * giving item-level links such as "Quick Edit", "Move to Trash".
	 *
	 * @since 1.00
	 *
	 * @param	array	$actions	The list of item "Rollover" actions.
	 * @param	object	$item		The current Media Library item.
	 * @param	string	$column		The List Table column slug.
	 */
	public static function mla_list_table_build_rollover_actions( $actions, $item, $column ) {
		//error_log( "MLAListTableHooksExample::mla_list_table_build_rollover_actions ({$column}) \$actions = " . var_export( $actions, true ), 0 );
		//error_log( "MLAListTableHooksExample::mla_list_table_build_rollover_actions ({$column}) \$item = " . var_export( $item, true ), 0 );
		return $actions;
	} // mla_list_table_build_rollover_actions

	/**
	 * Define the fields for inline (Quick) editing
	 *
	 * This filter gives you an opportunity to name the fields passed to the
	 * JavaScript functions for Quick editing.
	 *
	 * @since 1.00
	 *
	 * @param	array	$fields	The field names for inline data.
	 */
	public static function mla_list_table_inline_fields( $fields ) {
		//error_log( 'MLAListTableHooksExample::mla_list_table_inline_fields $fields = ' . var_export( $fields, true ), 0 );
		return $fields;
	} // mla_list_table_inline_fields

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
	 */
	public static function mla_list_table_build_inline_data( $inline_data, $item ) {
		//error_log( 'MLAListTableHooksExample::mla_list_table_build_inline_data $inline_data = ' . var_export( $inline_data, true ), 0 );
		//error_log( 'MLAListTableHooksExample::mla_list_table_build_inline_data $item = ' . var_export( $item, true ), 0 );
		return $inline_data;
	} // mla_list_table_build_inline_data

	/**
	 * Views for the "upload" page when WPML is active
	 *
	 * This filter is hooked by WPML Media in wpml-media.class.php, and is only
	 * applied when WPML is active.
	 *
	 * @since 1.00
	 *
	 * @param	array	$views An array of available list table views.
	 */
	public static function views_upload( $views ) {
		//error_log( 'MLAListTableHooksExample::views_upload $views = ' . var_export( $views, true ), 0 );
		return $views;
	} // views_upload

	/**
	 * MLAEdit bulk edit on upload item values
	 *
	 * This filter gives you a chance to modify and extend the substitution values
	 * for the Bulk Edit on Upload form.
	 *
	 * @since 1.07
	 *
	 * @param	array	$item_values [ parameter_name => parameter_value ] pairs
	 */
	public static function mla_upload_bulk_edit_form_values( $item_values ) {
		//error_log( 'MLAListTableHooksExample::mla_upload_bulk_edit_form_values $item_values = ' . var_export( $item_values, true ), 0 );
		return $item_values;
	} // mla_upload_bulk_edit_form_values

	/**
	 * MLAEdit bulk edit on upload template
	 *
	 * This filter gives you a chance to modify and extend the template used
	 * for the Bulk Edit on Upload form.
	 *
	 * @since 1.07
	 *
	 * @param	string	$item_template Template used to generate the HTML markup
	 */
	public static function mla_upload_bulk_edit_form_template( $item_template ) {
		//error_log( 'MLAListTableHooksExample::mla_upload_bulk_edit_form_template $item_template = ' . var_export( $item_template, true ), 0 );
		return $item_template;
	} // mla_upload_bulk_edit_form_template

	/**
	 * MLAEdit bulk edit on upload parse
	 *
	 * @since 1.07
	 *
	 * This filter gives you a final chance to modify and extend the HTML
	 * markup used for the Bulk Edit on Upload form.
	 *
	 * @param	string	$html_markup HTML markup returned by the template parser
	 * @param	string	$item_template Template used to generate the HTML markup
	 * @param	array	$item_values [ parameter_name => parameter_value ] pairs
	 */
	public static function mla_upload_bulk_edit_form_parse( $html_markup, $item_template, $item_values ) {
		//error_log( 'MLAListTableHooksExample::mla_upload_bulk_edit_form_parse $html_markup = ' . var_export( $html_markup, true ), 0 );
		//error_log( 'MLAListTableHooksExample::mla_upload_bulk_edit_form_parse $item_template = ' . var_export( $item_template, true ), 0 );
		//error_log( 'MLAListTableHooksExample::mla_upload_bulk_edit_form_parse $item_values = ' . var_export( $item_values, true ), 0 );
		return $html_markup;
	} // mla_upload_bulk_edit_form_parse
} // Class MLAListTableHooksExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLAListTableHooksExample::initialize');
?>
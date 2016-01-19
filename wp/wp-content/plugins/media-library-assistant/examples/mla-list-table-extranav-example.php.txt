<?php
/**
 * Adds an "author" dropdown control to the "extra tablenav" area of the Media/Assistant submenu
 *
 * @package MLA Extra Nav Hooks Example
 * @version 1.01
 */

/*
Plugin Name: MLA Extra Nav Hooks Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Adds an "author" dropdown control to the "extra tablenav" area of the Media/Assistant submenu
Author: David Lingren
Version: 1.01
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
 * Class MLA List Table Extra Nav Example adds an "author" dropdown control
 * to the "extra tablenav" area of the Media/Assistant submenu.
 *
 * @package MLA Extra Nav Example
 * @since 1.00
 */
class MLAListTableExtraNavExample {
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
		  * Defined in /media-library-assistant/includes/class-mla-list-table.php
		  */
		add_filter( 'mla_list_table_extranav_actions', 'MLAListTableExtraNavExample::mla_list_table_extranav_actions', 10, 2 );
		add_action( 'mla_list_table_extranav_custom_action', 'MLAListTableExtraNavExample::mla_list_table_extranav_custom_action', 10, 2 );
		add_filter( 'mla_list_table_submenu_arguments', 'MLAListTableExtraNavExample::mla_list_table_submenu_arguments', 10, 2 );
		add_filter( 'mla_list_table_query_final_terms', 'MLAListTableExtraNavExample::mla_list_table_query_final_terms', 10, 1 );
	}

	/**
	 * Add 'author' to the MLA_List_Table "extra tablenav" actions list
	 *
	 * @since 1.00
	 *
	 * @param	array	$actions	An array of extranav action labels.
	 * @param	string	$which		'top' or 'bottom'.
	 */
	public static function mla_list_table_extranav_actions( $actions, $which ) {
		if ( 'bottom' === $which ) {
			return $actions;
		}

		$new_actions = array();
		foreach ( $actions as $action ) {
			if ( 'mla_filter' === $action ) {
				$new_actions[] = 'author';
			}
			
			$new_actions[] = $action;
		}
		
		if ( count( $new_actions ) === count( $actions ) ) {
			$new_actions[] = 'author';
		}
		
		return $new_actions;
	} // mla_list_table_extranav_actions

	/**
	 * Echo the 'author' dropdown control
	 *
	 * @since 1.08
	 *
	 * @param	array	$action	extranav action label.
	 * @param	string	$which	'top' or 'bottom'.
	 */
	public static function mla_list_table_extranav_custom_action( $action, $which ) {
		if ( 'author' === $action ) {
			$users_opt = array(
				'show_option_none' => 'All Authors',
				'hide_if_only_one_author' => false,
				'who' => 'authors',
				'name' => 'author',
				'class'=> 'authors',
				'multi' => 1,
				'echo' => 0
			);

			if ( isset( $_REQUEST['author'] ) && $_REQUEST['author'] > 0 ) {
				$users_opt['selected'] = $_REQUEST['author'];
				$users_opt['include_selected'] = true;
			}

			echo wp_dropdown_users( $users_opt );
		}
	} // mla_list_table_extranav_custom_action

	/**
	 * Remove 'author' from the "sticky" submenu URL parameters
	 *
	 * @since 1.00
	 *
	 * @param	array	$submenu_arguments	Current view, pagination and sort parameters.
	 * @param	object	$include_filters	True to include "filter-by" parameters, e.g., year/month dropdown.
	 */
	public static function mla_list_table_submenu_arguments( $submenu_arguments, $include_filters ) {
		unset( $submenu_arguments['author'] );
		return $submenu_arguments;
	} // mla_list_table_submenu_arguments

	/**
	 * Remove 'author' from the query parameters when it is '-1'
	 *
	 * @since 1.01
	 *
	 * @param	array	$query_terms	Current query parameters.
	 */
	public static function mla_list_table_query_final_terms( $query_terms ) {
		if ( isset( $_REQUEST['author'] ) && $_REQUEST['author'] < 0 ) {
			unset( $query_terms['author'] );
		}
		
		return $query_terms;
	} // mla_list_table_query_final_terms
}// Class MLAListTableExtraNavExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLAListTableExtraNavExample::initialize');
?>
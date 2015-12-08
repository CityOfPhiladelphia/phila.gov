<?php
/**
 * Provides an example of hooking the filters provided by the MLA_List_Table class
 *
 * This example adds a Media/Assistant submenu table view for items attached to
 * non-published parent posts/pages.
 *
 * @packageMLA Custom View Example
 * @version 1.01
 */

/*
Plugin Name:MLA Custom View Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Adds a Media/Assistant submenu table view for items attached to non-published parent posts/pages.
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
 * Class MLA Custom View Example hooks some of the filters provided by the MLA_List_Table class
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding everything
 * else inside a class means this is the only name you have to worry about.
 *
 * @packageMLA CustomView Example
 * @since 1.00
 */
class MLACustomViewExample {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 1.00
	 *
	 * @return	void
	 */
	public static function initialize() {
		/*
		 * The remaining filters are only useful for the admin section; exit in the front-end posts/pages
		 */
		if ( ! is_admin() ) {
			return;
		}

		/*
		 * add_action and add_filter parameters:
		 * $tag - name of the hook you're filtering; defined by [mla_gallery]
		 * $function_to_add - function to be called when [mla_gallery] applies the filter
		 * $priority - default 10; lower runs earlier, higher runs later
		 * $accepted_args - number of arguments your function accepts
		 */

		 /*
		  * Defined in /wp-admin/includes/class-wp-list-table.php
		  */
		add_filter( 'views_media_page_mla-menu', 'MLACustomViewExample::views_media_page_mla_menu', 10, 1 );

		 /*
		  * Defined in /media-library-assistant/includes/class-mla-list-table.php
		  */
		add_filter( 'mla_list_table_submenu_arguments', 'MLACustomViewExample::mla_list_table_submenu_arguments', 10, 2 );

		add_filter( 'mla_list_table_prepare_items_pagination', 'MLACustomViewExample::mla_list_table_prepare_items_pagination', 10, 2 );
		add_filter( 'mla_list_table_prepare_items_total_items', 'MLACustomViewExample::mla_list_table_prepare_items_total_items', 10, 2 );
		add_filter( 'mla_list_table_prepare_items_the_items', 'MLACustomViewExample::mla_list_table_prepare_items_the_items', 10, 2 );
	}

	/**
	 * Add custom views for the Media/Assistant submenu
	 *
	 * @since 1.00
	 *
	 * @param	string	The slug for the custom view to evaluate
	 * @param	string	The slug for the current custom view, or ''
	 *
	 * @return	mixed	HTML for link to display the view, false if count = zero
	 */
	private static function _get_view( $view_slug, $current_view ) {
		global $wpdb;
		static $posts_per_view = NULL,
			$view_singular = array (),
			$view_plural = array ();

		/*
		 * Calculate the common values once per page load
		 */
		if ( is_null( $posts_per_view ) ) {
			$items = (integer) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} AS item INNER JOIN {$wpdb->posts} AS parent ON item.post_parent = parent.ID WHERE item.post_parent > 0 AND item.post_type = 'attachment' AND item.post_status = 'inherit' AND parent.post_status IN ( 'draft', 'future', 'pending', 'trash' )" );
			$posts_per_view = array( 'unpublished' => $items );

			$view_singular = array (
				'unpublished' => __( 'Unpublished', 'mla-custom-table-example' ),
			);
			$view_plural = array (
				'unpublished' => __( 'Unpublished', 'mla-custom-table-example' ),
			);
		}

		/*
		 * Make sure the slug is in our list and has posts
		 */
		if ( array_key_exists( $view_slug, $posts_per_view ) ) {
			$post_count = $posts_per_view[ $view_slug ];
			$singular = sprintf('%s <span class="count">(%%s)</span>', $view_singular[ $view_slug ] );
			$plural = sprintf('%s <span class="count">(%%s)</span>', $view_plural[ $view_slug ] );
			$nooped_plural = _n_noop( $singular, $plural, 'mla-custom-table-example' );
		} else {
			return false;
		}

		if ( $post_count ) {
			$query = array( 'cve_view' => $view_slug );
			$base_url = 'upload.php?page=mla-menu';
			$class = ( $view_slug == $current_view ) ? ' class="current"' : '';

			return "<a href='" . add_query_arg( $query, $base_url ) . "'$class>" . sprintf( translate_nooped_plural( $nooped_plural, $post_count, 'mla-custom-table-example' ), number_format_i18n( $post_count ) ) . '</a>';
		}

		return false;
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
	 *
	 * @return	array	updated list table views.
	 */
	public static function views_media_page_mla_menu( $views ) {
		// See if the current view is a custom view
		if ( isset( $_REQUEST['cve_view'] ) ) {
			switch( $_REQUEST['cve_view'] ) {
				case 'unpublished':
					$current_view = 'unpublished';
					break;
				default:
					$current_view = '';
			} // cve_view
		} else {
			$current_view = '';
		}

		foreach ( $views as $slug => $view ) {
			// Find/update the current view
			if ( strpos( $view, ' class="current"' ) ) {
				if ( ! empty( $current_view ) ) {
					$views[ $slug ] = str_replace( ' class="current"', '', $view );
				} else {
					$current_view = $slug;
				}
			}
		} // each view

		$value = self::_get_view( 'unpublished', $current_view );
		if ( $value ) {
			$views['unpublished'] = $value;
		}

		return $views;
	} // views_media_page_mla_menu

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
	 *
	 * @return	array	updated submenu_arguments.
	 */
	public static function mla_list_table_submenu_arguments( $submenu_arguments, $include_filters ) {
		// If the current view is a custom view, retain it
		if ( isset( $_REQUEST['cve_view'] ) ) {
			$submenu_arguments['cve_view'] = $_REQUEST['cve_view'];
		}

		return $submenu_arguments;
	} // mla_list_table_submenu_arguments

	/**
	 * Pagination parameters for custom views
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $pagination_parameters = array(
		'per_page' => NULL,
		'current_page' => NULL,
	);

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
	 *
	 * @return	array	updated pagination array.
	 */
	public static function mla_list_table_prepare_items_pagination( $pagination, $mla_list_table ) {
		global $wpdb;

		/*
		 * Save the parameters for the count and items filters
		 */
		self::$pagination_parameters = $pagination;
		return $pagination;
	} // mla_list_table_prepare_items_pagination

	/**
	 * Filters all clauses for shortcode queries, pre caching plugins
	 * 
	 * Modifying the query by editing the clauses in this filter ensures that all the other
	 * "List Table" parameters are retained, e.g., orderby, month, taxonomy and Search Media.
	 *
	 * @since 1.00
	 *
	 * @param	array	query clauses before modification
	 *
	 * @return	array	query clauses after modification (none)
	 */
	public static function posts_clauses( $pieces ) {
		global $wpdb;

		if ( isset( $_REQUEST['cve_view'] ) ) {
			switch( $_REQUEST['cve_view'] ) {
				case 'unpublished':
					$pieces['join'] = " INNER JOIN {$wpdb->posts} AS parent ON {$wpdb->posts}.post_parent = parent.ID" . $pieces['join'];
					$pieces['where'] = " AND parent.post_status IN ( 'draft', 'future', 'pending', 'trash' )" . $pieces['where'];
					break;
				default:
			} // cve_view
		}

		return $pieces;
	} // posts_clauses

	/**
	 * Filter the total items count for prepare_items()
	 *
	 * This filter gives you an opportunity to substitute your own $total_items
	 * parameter used for the prepare_items database query.
	 *
	 * @since 1.00
	 *
	 * @param	integer	$total_items	NULL, indicating no substitution.
	 * @param	object	$mla_list_table	The MLA_List_Table object, passed by reference.
	 *
	 * @return	integer	updated total_items.
	 */
	public static function mla_list_table_prepare_items_total_items( $total_items, $mla_list_table ) {
		global $wpdb;

		if ( isset( $_REQUEST['cve_view'] ) ) {
			switch( $_REQUEST['cve_view'] ) {
				case 'unpublished':
					// Defined in /wp-includes/query.php, function get_posts()
					add_filter( 'posts_clauses', 'MLACustomViewExample::posts_clauses', 10, 1 );
					$current_page = self::$pagination_parameters['current_page'];
					$per_page = self::$pagination_parameters['per_page'];
					$total_items = MLAData::mla_count_list_table_items( $_REQUEST, ( ( $current_page - 1 ) * $per_page ), $per_page );
					remove_filter( 'posts_clauses', 'MLACustomViewExample::posts_clauses', 10 );
					break;
				default:
			} // cve_view
		}

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
	 * @param	array	$items			NULL, indicating no substitution.
	 * @param	object	$mla_list_table	The MLA_List_Table object, passed by reference.
	 *
	 * @return	array	updated $items array.
	 */
	public static function mla_list_table_prepare_items_the_items( $items, $mla_list_table ) {
		global $wpdb;

		if ( isset( $_REQUEST['cve_view'] ) ) {
			switch( $_REQUEST['cve_view'] ) {
				case 'unpublished':
					add_filter( 'posts_clauses', 'MLACustomViewExample::posts_clauses', 10, 1 );
					$current_page = self::$pagination_parameters['current_page'];
					$per_page = self::$pagination_parameters['per_page'];
					$items = MLAData::mla_query_list_table_items( $_REQUEST, ( ( $current_page - 1 ) * $per_page ), $per_page );
					remove_filter( 'posts_clauses', 'MLACustomViewExample::posts_clauses', 10 );
					break;
				default:
			} // cve_view
		}

		return $items;
	} // mla_list_table_prepare_items_the_items
} // Class MLACustomViewExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLACustomViewExample::initialize');
?>
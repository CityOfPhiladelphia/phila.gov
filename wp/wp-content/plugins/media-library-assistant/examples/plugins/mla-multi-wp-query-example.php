<?php
/**
 * Provides an [mla_gallery] parameter to combine items from each of multiple "taxonomy=term" queries
 *
 * In this example, a custom "multi_wp_query" parameter gives one or more "simple taxonomy query" values.
 * Each value is parsed into individual "taxonomy=term" queries that are executed separately. The query
 * results are combined and returned to the [`mla_gallery]` shortcode as an "include" parameter to
 * display the selected items. For example:
 *
 * [mla_gallery multi_wp_query="attachment_category=term1,term2|attachment_tag=term3,term4" orderby="date,DESC" numberposts=1]
 *
 * The example selects one "most recent" item from each of four terms in two categories.
 *
 * This example plugin uses three of the many filters available in the [mla_gallery] shortcode
 * and illustrates some of the techniques you can use to customize the gallery display.
 *
 * Created for support topic "mla gallery - with latest image from four categories?"
 * opened on 8/3/2016 by "mouret".
 * https://wordpress.org/support/topic/mla-gallery-with-latest-image-from-four-categories
 *
 * @package MLA Multiple WP Query Example
 * @version 1.00
 */

/*
Plugin Name: MLA Multiple WP Query Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Provides an [mla_gallery] parameter to combine items from each of multiple "taxonomy=term" queries.
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
 * Class MLA Multiple WP Query Example provides an [mla_gallery] parameter to combine items from
 * each of multiple "taxonomy=term" queries
 *
 * @package MLA Multiple WP Query Example
 * @since 1.00
 */
class MLAMultipleWPQueryExample {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * Installs hooks for front-end requests; exits for admin-mode requests.
	 *
	 * @since 1.00
	 *
	 * @return	void
	 */
	public static function initialize() {
		/*
		 * The filters are only useful for front-end posts/pages; exit if in the admin section
		 */
		if ( is_admin() )
			return;

		add_filter( 'mla_gallery_attributes', 'MLAMultipleWPQueryExample::mla_gallery_attributes_filter', 10, 1 );
		add_filter( 'mla_gallery_query_arguments', 'MLAMultipleWPQueryExample::mla_gallery_query_arguments_filter', 10, 1 );
	}

	/**
	 * Save the shortcode attributes
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $shortcode_attributes = array();

	/**
	 * MLA Gallery (Display) Attributes gives you an opportunity to record or modify
	 * the arguments passed in to the shortcode before they are merged with the
	 * default arguments used for the gallery display.
	 *
	 * @since 1.00
	 *
	 * @param	array	the shortcode parameters passed in to the shortcode
	 *
	 * @return	array	updated shortcode attributes
	 */
	public static function mla_gallery_attributes_filter( $shortcode_attributes ) {
		/*
		 * Save the attributes for use in the later filters,
		 * and remove our custom parameter
		 */
		self::$shortcode_attributes = $shortcode_attributes;
		unset( $shortcode_attributes['multi_wp_query'] );
		
		return $shortcode_attributes;
	} // mla_gallery_attributes_filter

	/**
	 * MLA Gallery Query Arguments gives you an opportunity to record or modify the attachment
	 * query arguments after the shortcode attributes are merged with the default arguments.
	 * 
	 * In this plugin the "multi_wp_query" parameter drives a series of queries that select the items.
	 * The item ID values are passed back to the [mla_gallery] shortcode as an "incliude" parameter.
	 *
	 * @since 1.00
	 *
	 * @param	array	shortcode arguments merged with attachment selection defaults
	 *
	 * @return	array	updated attachment query arguments
	 */
	public static function mla_gallery_query_arguments_filter( $all_query_parameters ) {
		global $post;

		if ( empty( self::$shortcode_attributes['multi_wp_query'] ) ) {
			return $all_query_parameters;
		}
		
		/*
		 * Parse the parameter:
		 *   1) Split the taxonomy=term(s) elements
		 *   2) Split the term(s) lists
		 */
		$taxonomy_elements = explode( '|', self::$shortcode_attributes['multi_wp_query'] );
		$taxonomy_queries = array();
		foreach ( $taxonomy_elements as $query ) {
			$taxonomy_elements = explode( '=', $query );
			if ( 2 == count( $taxonomy_elements ) ) {
				$taxonomy_queries[ $taxonomy_elements[0] ] = explode( ',', $taxonomy_elements[1] );
			}
		}

		// Include all other parameters, e.g., orderby and numberposts, in the queries
		$my_query_vars = $all_query_parameters;
		$includes = array();
		foreach( $taxonomy_queries as $taxonomy => $terms ) {
			foreach ( $terms as $term ) {
				$my_query_vars[ $taxonomy ] = $term;
				$attachments = MLAShortcode_Support::mla_get_shortcode_attachments( $post->ID, $my_query_vars );
				unset( $my_query_vars[ $taxonomy ] );

				foreach ( $attachments as $attachment ) {
					$includes[ $attachment->ID ] = $attachment->ID;
				}
			} // each term
		} // each taxonomy

		if ( empty( $includes ) ) {
			$all_query_parameters['include'] = '1'; // return no items
		} else {
			$all_query_parameters['include'] = implode( ',', $includes );
		}

		// These parameters no longer apply
		unset( $all_query_parameters['numberposts'] );
		unset( $all_query_parameters['posts_per_page'] );
		
		return $all_query_parameters;
	} // mla_gallery_query_arguments_filter
} // Class MLAMultipleWPQueryExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLAMultipleWPQueryExample::initialize');
?>
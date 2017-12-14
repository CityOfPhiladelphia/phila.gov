<?php
/**
 * Provides an [mla_gallery] parameter to select parent posts/pages with WP_Query
 *
 * In this example, a custom "parent_wp_query" parameter contains WP_Query arguments for
 * parent posts/pages, e.g., " parent_wp_query='category_name=environment' ".
 * The query value generates a list of "post_parent" values for the Media Library items query.
 *
 * You can add most WP_Query parameters to the parent query, e.g.:
 *
 * [mla_gallery parent_wp_query='category_name=environment post_type=post,page numberposts=10']
 *
 * NOTE: To affect the parent query you must add the parameters inside the parent_wp_query value.
 *
 * This example plugin uses one of the many filters available in the [mla_gallery] shortcode
 * and illustrates a technique you can use to customize the gallery display.
 *
 * Created for support topic "Create gallery of all images attached to a list of posts?"
 * opened on 9/4/2016 by "cconstantine".
 * https://wordpress.org/support/topic/create-gallery-of-all-images-attached-to-a-list-of-posts/
 *
 * @package MLA Parent WP_Query Example
 * @version 1.00
 */

/*
Plugin Name: MLA Parent WP_Query Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Selects items attached to parents assigned to a taxonomy term
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
 * Class MLA Parent WP_Query Example selects post_parent values with a WP_Query
 *
 * @package MLA Parent WP_Query Example
 * @since 1.00
 */
class MLAParentWPQueryExample {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 1.00
	 */
	public static function initialize() {
		// The filters are only useful for front-end posts/pages; exit if in the admin section
		if ( is_admin() )
			return;

		add_filter( 'mla_gallery_attributes', 'MLAParentWPQueryExample::mla_gallery_attributes_filter', 10, 1 );
	}

	/**
	 * Replace the parent_wp_query value with a list of post_parent values
	 *
	 * The $shortcode_attributes array is where you will find any of your own parameters that are coded in the
	 * shortcode, e.g., [mla_gallery parent_wp_query="category_name=environment"].
	 *
	 * @since 1.00
	 *
	 * @param	array	the shortcode parameters passed in to the shortcode
	 */
	public static function mla_gallery_attributes_filter( $shortcode_attributes ) {
		global $wpdb;

		// ignore shortcodes without the parent_wp_query parameter
		if ( empty( $shortcode_attributes['parent_wp_query'] ) ) {
			return $shortcode_attributes;
		}

		// Make sure $arguments is an array, even if it's empty
		$arguments = $shortcode_attributes['parent_wp_query'];
		if ( empty( $arguments ) ) {
			$arguments = array();
		} elseif ( is_string( $arguments ) ) {
			$arguments = shortcode_parse_atts( $arguments );
		}

		// Multi-value post_type and post_status must be arrays
		
		if ( isset( $arguments['post_type'] ) ) {
			$arguments['post_type'] = explode( ',', $arguments['post_type'] );
		}

		if ( isset( $arguments['post_status'] ) ) {
			$arguments['post_status'] = explode( ',', $arguments['post_status'] );
		}

		$wp_query_object = new WP_Query;
		$parents = $wp_query_object->query( $arguments );
		if ( is_array( $parents ) ) {
			$post_parents = array();
			foreach( $parents as $parent ) {
				$post_parents[] = $parent->ID;
			}
			$shortcode_attributes['post_parent'] = implode( ',', $post_parents );
		}
		
		unset( $shortcode_attributes['parent_wp_query'] );

		return $shortcode_attributes;
	} // mla_gallery_attributes_filter
} // Class MLAParentWPQueryExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLAParentWPQueryExample::initialize');
?>
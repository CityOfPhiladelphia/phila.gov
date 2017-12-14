<?php
/**
 * Creates a term-specific gallery of images assigned to child terms.
 *
 * In this example, a term slug within a hiearchical taxonomy (default "galleries" in
 * "attachment_category") is given. Each immediate child term is used to select one image
 * assigned to that term, and the selected images are displayed in a gallery. The links 
 * for each gallery item go to a separate page that displays a gallery of all images assigned
 * to the term.
 *
 * This example plugin uses three of the many filters available in the [mla_gallery] shortcode
 * and illustrates some of the techniques you can use to customize the gallery display.
 *
 * Created for support topic "Automatic hierarchical display for hierarchical taxonomies"
 * opened on 8/14/2014 by "mark-cockfield".
 * https://wordpress.org/support/topic/automatic-hierarchical-display-for-hierarchical-taxonomies
 *
 * @package MLA Child Term Hooks Example
 * @version 1.01
 */

/*
Plugin Name: MLA Child Term Hooks Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Creates a term-specific gallery of images assigned to child terms.
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
 * Class MLA Child Term Hooks Example hooks three of the filters provided by the [mla_gallery] shortcode
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding everything
 * else inside a class means this is the only name you have to worry about.
 *
 * @package MLA Child Term Hooks Example
 * @since 1.00
 */
class MLAChildTermHooksExample {
	/**
	 * Initialization function, similar to __construct()
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

		/*
		 * add_filter parameters:
		 * $tag - name of the hook you're filtering; defined by [mla_gallery]
		 * $function_to_add - function to be called when [mla_gallery] applies the filter
		 * $priority - default 10; lower runs earlier, higher runs later
		 * $accepted_args - number of arguments your function accepts
		 */
		add_filter( 'mla_gallery_attributes', 'MLAChildTermHooksExample::mla_gallery_attributes', 10, 1 );
		add_filter( 'mla_gallery_query_arguments', 'MLAChildTermHooksExample::mla_gallery_query_arguments', 10, 1 );
		add_filter( 'mla_gallery_item_values', 'MLAChildTermHooksExample::mla_gallery_item_values', 10, 1 );
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
	 * MLA Gallery (Display) Attributes
	 *
	 * This filter gives you an opportunity to record or modify the arguments passed in to the shortcode
	 * before they are merged with the default arguments used for the gallery display.
	 *
	 * The $shortcode_attributes array is where you will find any of your own parameters that are coded in the
	 * shortcode, e.g., [mla_gallery my_parameter="my value"].
	 *
	 * @since 1.00
	 *
	 * @param	array	the shortcode parameters passed in to the shortcode
	 *
	 * @return	array	updated shortcode attributes
	 */
	public static function mla_gallery_attributes( $shortcode_attributes ) {
		/*
		 * Save the attributes for use in the later filters
		 */
		self::$shortcode_attributes = $shortcode_attributes;
		return $shortcode_attributes;
	} // mla_gallery_attributes

	/**
	 * Save the item => term_slug pairs
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $term_slugs = array();

	/**
	 * MLA Gallery Query Arguments
	 *
	 * This filter gives you an opportunity to record or modify the attachment query arguments
	 * after the shortcode attributes are merged with the default arguments.
	 *
	 * @since 1.00
	 *
	 * @param	array	shortcode arguments merged with attachment selection defaults, so every possible parameter is present
	 *
	 * @return	array	updated attachment query arguments
	 */
	public static function mla_gallery_query_arguments( $all_query_parameters ) {
		/*
		 * This example executes a custom SQL query that cannot be done with the usual
		 * WordPress WP_Query arguments. The query results are fed back to the [mla_gallery]
		 * shortcode as a list of attachments using the "include" parameter.
		 *
		 * This example's "my_parent_terms" parameter specifies a taxonomy and term slug,
		 * e.g., my_parent_terms="attachment_category=galleries". The parameter allows for
		 * multiple terms per taxonomy and multiple taxonomies, e.g.,
		 * my_parent_terms="attachment_category='galleries,slides' attachment_tag=animals".
		 * You can add "include_children=true" to the my_parent_terms parameter to get all
		 * child terms, not just immediate descendants.
		 *
		 * The taxonomy and term are used to select a list of child terms. The list is then used
		 * to find one image assigned to each child term. The images are passed on in the "include"
		 * parameter.
		 */		
		if ( isset( self::$shortcode_attributes['my_parent_terms'] ) ) {
			global $wpdb;

			// Make sure $my_query_vars is an array, even if it's empty
			$my_query_vars = self::$shortcode_attributes['my_parent_terms'];
			if ( empty( $my_query_vars ) ) {
				$my_query_vars = array();
			} elseif ( is_string( $my_query_vars ) ) {
				$my_query_vars = shortcode_parse_atts( $my_query_vars );
			}

			// Start with empty parameter values
			$ttids = array();

			// Find taxonomy argument(s), if present, and collect terms
			$taxonomies = get_object_taxonomies( 'attachment', 'names' );
			foreach( $taxonomies as $taxonomy ) {
				if ( empty( $my_query_vars[ $taxonomy ] ) ) {
					continue;
				}

				// Found the taxonomy; collect the terms
				$include_children =  isset( $my_query_vars['include_children'] ) && 'true' == strtolower( trim( $my_query_vars['include_children'] ) );

				// Allow for multiple term slug values
				$terms = array();
				$slugs = explode( ',', $my_query_vars[ $taxonomy ] );
				foreach ( $slugs as $slug ) {
					$args = array( 'slug' => $slug, 'hide_empty' => false );
					$terms = array_merge( $terms, get_terms( $taxonomy, $args ) );
				}

				foreach( $terms as $term ) {
					// Find all descendants or just immediate children
					if ( $include_children ) {
						$args = array( 'child_of' => absint( $term->term_id ), 'hide_empty' => false );
					} else {
						$args = array( 'parent' => absint( $term->term_id ), 'hide_empty' => false );
					}

					$children = get_terms( 'attachment_category', $args );
					foreach( $children as $child ) {
						// Index by ttid to remove duplicates
						$ttids[ $child->term_taxonomy_id ] = $child->term_taxonomy_id;
					}
				} // $term

				break;
			}

			// If no terms, return no images
			if ( empty( $ttids ) ) {
				$all_query_parameters['include'] = '1';
				return $all_query_parameters;
			}

			// Build an array of SQL clauses
			$query = array();
			$query_parameters = array();

			$query[] = "SELECT p.ID, t.slug FROM {$wpdb->posts} AS p";
			$query[] = "LEFT JOIN {$wpdb->term_relationships} as tr";
			$query[] = "ON (p.ID = tr.object_id)";
			$query[] = "LEFT JOIN {$wpdb->term_taxonomy} as tt";
			$query[] = "ON (tt.term_taxonomy_id = tr.term_taxonomy_id)";
			$query[] = "LEFT JOIN {$wpdb->terms} as t";
			$query[] = "ON (tt.term_id = t.term_id)";

			// Start with a WHERE clause that doesn't match anything, since OR is the connector				
			$query[] = 'WHERE ( ( 1=0 )';

			if ( ! empty( $ttids ) ) {
				$placeholders = array();
				foreach ( $ttids as $ttid ) {
					$placeholders[] = '%s';
					$query_parameters[] = $ttid;
				}

				$query[] = 'OR ( tr.term_taxonomy_id IN (' . join( ',', $placeholders ) . ') )';
			}

			// Close the WHERE clause
			$query[] = ')';

			$query[] = "AND (p.post_mime_type LIKE 'image/%%')";
			$query[] = "AND p.post_type = 'attachment'";
			$query[] = "AND p.post_status = 'inherit'";
			$query[] = "GROUP BY p.ID";
			// ORDER BY clause would go here, if needed

			$query =  join(' ', $query);
			$ids = $wpdb->get_results( $wpdb->prepare( $query, $query_parameters ) );
			if ( is_array( $ids ) ) {
				$includes = array();
				// Index on slug to get one image per term
				foreach ( $ids as $id ) {
					$includes[ $id->slug ] = $id->ID;
				}

				// Save the ID => slug relations for the gallery items filter
				self::$term_slugs = array_flip( $includes );
				$all_query_parameters['include'] = implode( ',', $includes );
			} else {
				$all_query_parameters['include'] = '1'; // return no images
			}
		} // parameter "my_parent_terms" is present

		return $all_query_parameters;
	} // mla_gallery_query_arguments

	/**
	 * Generate a link for each gallery item to a separate page that displays a gallery
	 * of all images assigned to the term.
 	 *
	 * @since 1.00
	 *
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated substitution parameter name => value pairs
	 */
	public static function mla_gallery_item_values( $item_values ) {
		/*
		 * We use a shortcode parameter of our own to apply our filters on a gallery-by-gallery basis,
		 * leaving other [mla_gallery] instances untouched. If the "my_parent_terms" parameter is not present,
		 * we have nothing to do.
		 */		
		if ( ! isset( self::$shortcode_attributes['my_parent_terms'] ) ) {
			return $item_values; // leave them unchanged
		}

		// Make sure $my_page is an array, even if it's missing or empty
		$my_page = array();
		if ( isset( self::$shortcode_attributes['my_page'] ) ) {
			$my_page = self::$shortcode_attributes['my_page'];

			if ( is_string( $my_page ) ) {
				$my_page = shortcode_parse_atts( $my_page );
			}
		}

		// Apply defaults
		if ( isset( $my_page['permalink'] ) ) {
			$my_page['permalink'] = $item_values['site_url'] . $my_page['permalink'];
		} else {
			$my_page['permalink'] = $item_values['page_url'];
		}

		if ( ! isset( $my_page['queryarg'] ) ) {
			$my_page['queryarg'] = 'term_slug';
		}

		// Generate link value
		$href = $my_page['permalink'] . '?' . $my_page['queryarg'] . '=' . self::$term_slugs[ $item_values['attachment_ID'] ];
		$item_values['link'] = '<a href="' . $href . '">' . $item_values['thumbnail_content'] . '</a>';
		return $item_values;
	} // mla_gallery_item_values
} // Class MLAChildTermHooksExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLAChildTermHooksExample::initialize');
?>
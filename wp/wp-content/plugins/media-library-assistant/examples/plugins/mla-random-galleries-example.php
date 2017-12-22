<?php
/**
 * Provides an [mla_gallery] parameter to select random items from a collection of Att. Category terms
 *
 * In this example, a custom "random_category" parameter names an Att. Category term.
 * The value is matched to a list of terms in the $gallery_terms array in the plugin source code.
 * If the term is in the list items assigned to the term are returned in random order.
 * The 'numberposts' parameter can be added to limit the number of items returned. For example:
 *
 * [mla_gallery random_category=admin numberposts=1]
 *
 * NOTE: You must enter the name or slug values for your application's terms in the $gallery_terms array below.
 *
 * This example plugin uses one of the many filters available in the [mla_gallery] shortcode
 * and illustrates a technique you can use to customize the gallery display.
 *
 * Created for support topic "multiple calls to a smaller amount"
 * opened on 1/16/2016 by "luigsm".
 * https://wordpress.org/support/topic/multiple-calls-to-a-smaller-amount
 *
 * @package MLA Random Galleries Example
 * @version 1.00
 */

/*
Plugin Name: MLA Random Galleries Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: High performance queries for random items from a list of Att. Categories
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
 * Class MLA Random Galleries Example supplies random items from a collection of Att. Category terms
 *
 * NOTE: You must enter the name or slug values for your application's terms in the $gallery_terms array below.
 *
 * @package MLA Random Galleries Example
 * @since 1.00
 */
class MLARandomGalleriesExample {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 1.00
	 */
	public static function initialize() {
		// The filters are only useful for front-end posts/pages; exit if in the admin section
		if ( is_admin() )
			return;

		add_filter( 'mla_gallery_attributes', 'MLARandomGalleriesExample::mla_gallery_attributes_filter', 10, 1 );
	}

	/**
	 * Save the random galleries
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $random_galleries = NULL;

	/**
	 * List of term names/slugs for the galleries
	 *
	 * Replace the three example terms with the appropriate values
	 * for your application/site
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $gallery_terms = array(
		'Colorado River', 'admin', 'abc',
		);

	/**
	 * MLA Gallery (Display) Attributes
	 *
	 * The $shortcode_attributes array is where you will find any of your own parameters that are coded in the
	 * shortcode, e.g., [mla_gallery random_category="abc"].
	 *
	 * @since 1.00
	 *
	 * @param	array	the shortcode parameters passed in to the shortcode
	 */
	public static function mla_gallery_attributes_filter( $shortcode_attributes ) {
		global $wpdb;

		// ignore shortcodes without the random_category parameter
		if ( empty( $shortcode_attributes['random_category'] ) ) {
			return $shortcode_attributes;
		}
		
		// Once each page load, compute the random galleries
		if ( is_null( self::$random_galleries ) ) {
			self::$random_galleries = array();
			
			// Get slug values
			$slugs = array();
			foreach ( self::$gallery_terms as $term ) {
				$slugs[] = "'" . esc_sql( sanitize_term_field( 'slug', $term, 0, 'attachment_category', 'db' ) ) . "'";
			}
			$slugs = implode( ',', $slugs );
			
			// Build an array of ( slug => term_taxonomy_id )
			$terms_query = sprintf( 'SELECT term_id, slug FROM %1$s WHERE ( slug IN ( %2$s ) ) ORDER BY term_id', $wpdb->terms, $slugs );
			$_terms_taxonomy_query = sprintf( 'SELECT term_taxonomy_id, term_id FROM %1$s WHERE ( taxonomy=\'attachment_category\' ) ORDER BY term_id', $wpdb->term_taxonomy );

			$query = sprintf( 'SELECT tt.term_taxonomy_id, t.term_id, t.slug FROM ( %1$s ) as tt JOIN ( %2$s ) as t ON ( tt.term_id = t.term_id ) ORDER BY term_taxonomy_id', $_terms_taxonomy_query, $terms_query, $slugs );
			$results = $wpdb->get_results( $query );

			self::$gallery_terms = array();
			foreach ( $results as $result ) {
				self::$gallery_terms[ absint( $result->term_taxonomy_id ) ] = $result->slug;
			}
			unset( $results );
			self::$gallery_terms = array_flip( self::$gallery_terms );

			// Build an array of ( term_taxonomy_id => array( IDs of items assigned to the term )
			$term_taxonomy_ids = implode( ',', array_values( self::$gallery_terms ) );
			$query = sprintf( 'SELECT object_id, term_taxonomy_id FROM %1$s WHERE ( term_taxonomy_id IN ( %2$s ) ) ORDER BY RAND()', $wpdb->term_relationships, $term_taxonomy_ids );
			$results = $wpdb->get_results( $query );

			foreach ( $results as $result ) {
				self::$random_galleries[ absint( $result->term_taxonomy_id ) ][] = absint( $result->object_id );
			}
			unset( $results );
		}
		
		// Convert the parameter value to a sanitized slug value
		$random_slug = esc_sql( sanitize_term_field( 'slug', $shortcode_attributes['random_category'], 0, 'attachment_category', 'db' ) );

		// Make sure the parameter matches a value in the galleries
		if ( ! $random_key = ( isset( self::$gallery_terms[ $random_slug ] ) ? self::$gallery_terms[ $random_slug ] : 0 ) ) {
			$shortcode_attributes['numberposts'] = 0;
			return $shortcode_attributes;
		}

		// Limit the result set if numberposts is present
		if ( $numberposts = ( isset( $shortcode_attributes['numberposts'] ) ? absint( $shortcode_attributes['numberposts'] ) : 0 ) ) {
			if ( $numberposts < count( self::$random_galleries[ $random_key ] ) ) {
				$ids = array_slice( self::$random_galleries[ $random_key ], 0, $numberposts, true );
			} else {
				$ids = self::$random_galleries[ $random_key ];
			}
		} else {
			$ids = self::$random_galleries[ $random_key ];
		}
		
		$shortcode_attributes['ids'] = implode( ',', $ids );
		unset( $shortcode_attributes['random_category'] );
		unset( $shortcode_attributes['numberposts'] );
		
		return $shortcode_attributes;
	} // mla_gallery_attributes_filter
} // Class MLARandomGalleriesExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLARandomGalleriesExample::initialize');
?>
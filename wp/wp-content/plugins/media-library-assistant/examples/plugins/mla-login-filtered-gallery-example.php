<?php
/**
 * Provides an [mla_gallery] parameter and a custom shortcode to restrict items and content to logged-in users
 *
 * In this example:
 *     * A custom "members-only" parameter names a taxonomy and one or more terms
 *     * A custom shortcode [mla_login_filter login_status=true/false][/mla_login_filter] returns shortcode content based on login status
 *
 * If the current user is not logged in any items assigned to the term(s) are excluded from the gallery results.
 * This can be combined with a "simple" attachment_category query. For example:
 *
 * [mla_gallery members_only="attachment_category:client" attachment_category=cityscape tax_include_children=false]
 *
 * This example plugin uses one of the many filters available in the [mla_gallery] shortcode
 * and illustrates a technique you can use to customize the gallery display.
 *
 * Created for support topic "exclude some files for non subcribed users"
 * opened on 7/12/2017 by "agustynen".
 * https://wordpress.org/support/topic/multiple-calls-to-a-smaller-amount
 *
 * @package MLA Login-filtered Gallery Example
 * @version 1.01
 */

/*
Plugin Name: MLA Login-filtered Gallery Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Restricts items to logged-in users based on an Att. Categories term
Author: David Lingren
Version: 1.01
Author URI: http://fairtradejudaica.org/our-story/staff/

Copyright 2017 David Lingren

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
 * Class MLA Login-filtered Gallery Example restricts items to logged-in users based on an Att. Categories term
 *
 * @package MLA Login-filtered Gallery Example
 * @since 1.00
 */
class MLALoginFilteredGalleryExample {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 1.00
	 */
	public static function initialize() {
		// The filters are only useful for front-end posts/pages; exit if in the admin section
		if ( is_admin() ) {
			return;
		}

		add_filter( 'mla_gallery_attributes', 'MLALoginFilteredGalleryExample::mla_gallery_attributes', 10, 1 );
		add_shortcode( 'mla_login_filter', 'MLALoginFilteredGalleryExample::mla_login_filter' );
	}

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
	public static function mla_gallery_attributes( $shortcode_attributes ) {
		global $wpdb;

		// ignore shortcodes without the random_category parameter TODO - any taxonomy, multiple terms
		if ( empty( $shortcode_attributes['members_only'] ) ) {
			return $shortcode_attributes;
		}
		
		// ignore restrictions for logged in user
		$current_user = wp_get_current_user();
		if ( ( $current_user instanceof WP_User ) && ( 0 !== $current_user->ID ) ) {
			return $shortcode_attributes;
		}

		// Validate other tax_query parameters or set defaults
		$tax_operator = 'IN';
		if ( isset( $shortcode_attributes['tax_operator'] ) ) {
			$attr_value = strtoupper( $shortcode_attributes['tax_operator'] );
			if ( in_array( $attr_value, array( 'IN', 'NOT IN', 'AND' ) ) ) {
				$tax_operator = $attr_value;
			}
			
			unset( $shortcode_attributes['tax_operator'] );
		}

		$tax_include_children = true;
		if ( isset( $shortcode_attributes['tax_include_children'] ) ) {
			if ( 'false' == strtolower( $shortcode_attributes['tax_include_children'] ) ) {
				$tax_include_children = false;
			}
			
			unset( $shortcode_attributes['tax_include_children'] );
		}

		// Compose the simple tax query, if pesent
		if ( isset( $shortcode_attributes['attachment_category'] ) ) {
			$tax_query = array ('relation' => 'AND' );
			$tax_query[] =	array( 'taxonomy' => 'attachment_category', 'field' => 'slug', 'terms' => explode( ',', $shortcode_attributes['attachment_category'] ), 'operator' => $tax_operator, 'include_children' => $tax_include_children );
			unset( $shortcode_attributes['attachment_category'] );
		} else {
			$tax_query = array ();
		}

		// Add the members_only exclusion query
		$tax_query[] =	array( 'taxonomy' => 'attachment_category', 'field' => 'slug', 'terms' => explode( ',', $shortcode_attributes['members_only'] ), 'operator' => 'NOT IN', 'include_children' => $tax_include_children );

		$shortcode_attributes['tax_query'] = $tax_query;
		
		return $shortcode_attributes;
	} // mla_gallery_attributes

	/**
	 * MLA Login Filter Shortcode
	 *
	 * This enclosing shortcode returns its content when the user login status matches the 'login_status' argument.
	 * For example [mla_login_filter login_status=true]The content for logged in users.[/mla_login_filter]
	 *
	 * @since 1.01
	 *
	 * @param	array	$shortcode_attributes the parameters passed in to the shortcode
	 * @param	string	$shortcode_content Optional content for enclosing shortcodes
	 */
	public static function mla_login_filter( $shortcode_attributes, $shortcode_content = NULL ) {
		
		if ( is_null( $shortcode_content ) ) {
			return '';
		}
		
		$default_arguments = array(
			'login_status' => 'true',
		);

		// Make sure $shortcode_attributes is an array, even if it's empty
		if ( empty( $shortcode_attributes ) ) {
			$shortcode_attributes = array();
		} elseif ( is_string( $shortcode_attributes ) ) {
			$shortcode_attributes = shortcode_parse_atts( $shortcode_attributes );
		}

		// Accept only the attributes we need and supply defaults
		$arguments = shortcode_atts( $default_arguments, $shortcode_attributes );
		$login_status = 'true' === strtolower( $arguments['login_status'] );

		if ( $login_status === is_user_logged_in() ) {
			return $shortcode_content;
		}
		
		return '';
	} // mla_login_filter
} // Class MLALoginFilteredGalleryExample

// Install the filters at an early opportunity
add_action('init', 'MLALoginFilteredGalleryExample::initialize');
?>
<?php
/**
 * Inserts custom field content from a post/page Featured Image:
 *
 * 1. Finds the Featured Image for the post/page in which the shortcode is embedded
 * 2. Finds the value of a custom field (default: img_html) assigned to the Featured Image item
 * 3. Echoes the content of the custom field to the post/page
 *
 * Code [mla_featured_field] to return the content of the 'img_html' default custom field.
 * Code [mla_featured_field field_name="Archive Date"] to return, for example, the "Archive Date" custom field.
 *
 * @package MLA Featured Field Shortcode
 * @version 1.00
 */

/*
Plugin Name: MLA Featured Field Shortcode Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Inserts custom field content from a post/page Featured Image
Author: David Lingren
Version: 1.00
Author URI: http://fairtradejudaica.org/our-story/staff/

Copyright 2015 David Lingren

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
 * Class MLA Featured Field Shortcode inserts custom field content from a post/page Featured Image
 *
 * @package MLA Featured Field Shortcode
 * @since 1.00
 */
class MLAFeaturedFieldShortcode {
	/**
	 * Name a default custom field
	 */
	const DEFAULT_FIELD = 'img_html';

	/**
	 * Initialization function, similar to __construct()
	 *
	 * Adds the 'mla_featured_field' shortcode to WordPress
	 *
	 * @since 1.00
	 *
	 * @return	void
	 */
	public static function initialize() {
		add_shortcode( 'mla_featured_field', 'MLAFeaturedFieldShortcode::mla_featured_field' );
	}

	/**
	 * WordPress Shortcode; inserts custom field content
	 *
	 * @since 1.00
	 *
	 * @param	array	shortcode parameters; defaults ( 'field_name' => DEFAULT_FIELD, 'ids' => '' )
	 *
	 * @return	string	post/page content to replace the shortcode
	 */
	public static function mla_featured_field( $attr ) {
		$default_arguments = array(
			'field_name' => MLAFeaturedFieldShortcode::DEFAULT_FIELD,
		);

		// Make sure $attr is an array, even if it's empty
		if ( empty( $attr ) ) {
			$attr = array();
		} elseif ( is_string( $attr ) ) {
			$attr = shortcode_parse_atts( $attr );
		}

		// Accept only the attributes we need and supply defaults
		$arguments = shortcode_atts( $default_arguments, $attr );

		// Make sure we have a post ID
		$post_id = get_the_ID();
		if ( empty( $post_id ) ) {
			return '';
		}

		// Make sure we have a Featured Image
		$feature_id = get_post_thumbnail_id( $post_id );
		if ( empty( $feature_id ) ) {
			return '';
		}

		// Get the custom field content
		$content = get_post_custom_values( $arguments['field_name'], $feature_id );
		if ( is_array( $content ) ) {
			return current( $content );
		}
		
		return '';
	} //mla_featured_field
} //MLAFeaturedFieldShortcode

// Install the shortcode at an early opportunity
add_action('init', 'MLAFeaturedFieldShortcode::initialize');
?>
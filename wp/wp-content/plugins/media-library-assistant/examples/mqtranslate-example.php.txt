<?php
/**
 * Provides an example of hooking the filters provided by the [mla_tag_cloud] shortcode
 *
 * In this example, MLA Tag Cloud term names are translated and a language prefix is
 * added to the site_url value.
 *
 * @package MLA mqTranslate Example
 * @version 1.01
 */

/*
Plugin Name: MLA mqTranslate Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Provides an example of hooking the filters provided by the [mla_tag_cloud] shortcode
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
 * Class MLA mqTranslate Example hooks one of the filters provided by the [mla_tag_cloud] shortcode
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding everything
 * else inside a class means this is the only name you have to worry about.
 *
 * @package MLA mqTranslate Example
 * @since 1.00
 */
class MLAmqTranslateExample {
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

		add_filter( 'mla_get_terms_query_results', 'MLAmqTranslateExample::mla_get_terms_query_results_filter', 10, 1 );
		add_filter( 'mla_gallery_open_values', 'MLAmqTranslateExample::mla_gallery_open_values_filter', 10, 1 );
	}

	/**
	 * MLA Tag Cloud Query Results
	 *
	 * This action gives you an opportunity to inspect, save, modify, reorder, etc.
	 * the array of tag objects returned from the data selection process.
	 *
	 * @since 1.00
	 *
	 * @param	array	tag objects
	 *
	 * @return	array	updated tag objects
	 */
	public static function mla_get_terms_query_results_filter( $tag_objects ) {
		$tag_objects = qtrans_useTermLib( $tag_objects );

		return $tag_objects;
	} // mla_get_terms_query_results_filter

	/**
	 * MLA Gallery Open Values
	 *
	 * @since 1.01
	 *
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated substitution parameter name => value pairs
	 */
	public static function mla_gallery_open_values_filter( $item_values ) {
		global $q_config;

		if ( $q_config['language'] != $q_config['default_language'] ) {
			$item_values['site_url'] = $item_values['site_url'] . '/' . $q_config['language'];
		}

		return $item_values;
	} // mla_gallery_open_values_filter
} // Class MLAmqTranslateExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLAmqTranslateExample::initialize');
?>
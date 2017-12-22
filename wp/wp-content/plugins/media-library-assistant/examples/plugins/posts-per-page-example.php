<?php
/**
 * Adjusts the [mla_gallery] posts_per_page value based on WordPress conditional functions
 *
 * In this example:
 *
 * A custom "posts_per_front_page" parameter  adjusts the number of items generated for posts
 * displayed on the site's front/home page.
 *
 * This example plugin uses one of the many filters available in the [mla_gallery] shortcode
 * and illustrates a technique you can use to customize the gallery display.
 *
 * Created for support topic "dealing with posts_per_page / posts_per_archive_page"
 * opened on 10/10/2016 by "Ernest".
 * https://wordpress.org/support/topic/dealing-with-posts_per_page-posts_per_archive_page/
 *
 * @package Posts Per Page Example
 * @version 1.00
 */

/*
Plugin Name: Posts Per Page Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Adjusts the [mla_gallery] posts_per_page value based on WordPress conditional functions
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
 * Class Posts Per Page Example adjusts the [mla_gallery] posts_per_page value based on
 * WordPress conditional functions
 *
 * @package Posts Per Page Example
 * @since 1.00
 */
class PostsPerPageExample {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 1.00
	 *
	 * @return	void
	 */
	public static function initialize() {
		// The filters are only useful for front-end posts/pages; exit if in the admin section
		if ( is_admin() )
			return;

		add_filter( 'mla_gallery_attributes', 'PostsPerPageExample::mla_gallery_attributes', 10, 1 );
	}

	/**
	 * Process the 'posts_per_front_page' custom parameter
	 *
	 * @since 1.00
	 *
	 * @param array $shortcode_attributes The parameters passed to the shortcode plus defaults
	 */
	public static function mla_gallery_attributes( $shortcode_attributes ) {
		if ( empty( $shortcode_attributes['posts_per_front_page'] ) ) {
			return $shortcode_attributes;
		}
		
		if ( is_front_page() ) {
			$shortcode_attributes['posts_per_page'] = $shortcode_attributes['posts_per_front_page'];
		}
		
		//error_log( 'PostsPerPageExample::mla_gallery_attributes $shortcode_attributes = ' . var_export( $shortcode_attributes, true ), 0 );
		return $shortcode_attributes;
	} // mla_gallery_attributes
} // Class PostsPerPageExample

// Install the filters at an early opportunity
add_action('init', 'PostsPerPageExample::initialize');
?>
<?php
/**
 * Replaces gallery item hyperlinks with simple <img> tags when nolink=true
 *
 * @package MLA Custom Nolink Example
 * @version 1.01
 */

/*
Plugin Name: MLA Custom Nolink Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Replaces gallery item hyperlinks with simple <img> tags when nolink=true
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
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding everything
 * else inside a class means this is the only name you have to worry about.
 *
 * @package MLA Custom Nolink Example
 * @since 1.00
 */
class MLACustomNolinkExample {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 1.00
	 *
	 * @return	void
	 */
	public static function initialize() {
		/*
		 * The filter is only needed in the front-end posts/pages
		 */
		if ( is_admin() ) {
			return;
		}


		 /*
		  * Defined in /wp-includes/media.php - in gallery_shortcode()
		  * Use a "low" priority so this filter runs after the "tiled_gallery" filter
		  */
		add_filter( 'post_gallery', 'MLACustomNolinkExample::post_gallery', 2002, 2 );
	}

	/**
	 * Filter the [gallery] output to remove hyperlinks
	 *
	 * @since 1.00
	 *
	 * @param	string	The gallery output HTML
	 * @param	array	The shortcode parameters
	 *
	 * @return	string	Filtered gallery output HTML
	 */
	public static function post_gallery( $html, $atts ) {
		//error_log( 'post_gallery $html = ' . var_export( $html, true ), 0 );
		//error_log( 'post_gallery $atts = ' . var_export( $atts, true ), 0 );

		// Look for our custom parameter, nolink=true
		if ( isset( $atts['nolink'] ) && 'true' == strtolower( trim( $atts['nolink'] ) ) ) {

			// Disable Jetpack Carousel, which always runs with Jetpack installed even if not activated
			$start = strpos( $html, 'data-carousel-extra' );
			if ( false !== $start ) {
				$length = strlen( 'data-carousel-extra' );
				$html = substr_replace( $html, 'data-carousel-disabled', $start, $length );
			}

			// Find all the hyperlinks in the gallery
			$match_count = preg_match_all( '!(<a [^>]*?>)(<img [^>]*?>)(</a>)!', $html, $matches, PREG_OFFSET_CAPTURE );
			if ( $match_count ) {

				// Replace the links with just the <img> tags
				for ( $index = $match_count - 1; $index >= 0; $index-- ) {
					$replacement = $matches[2][ $index ][0];
					$start = $matches[0][ $index ][1];
					$length = strlen( $matches[0][ $index ][0] );
					$html = substr_replace( $html, $replacement, $start, $length );
				}
			}
		}

		return $html;
	}
} // Class MLACustomNolinkExample

/*
 * Install the filter at an early opportunity
 */
add_action('init', 'MLACustomNolinkExample::initialize');
?>
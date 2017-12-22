<?php
/**
 * Hooks a WordPress filter to supply a list of the items returned from [mla_gallery] shortcodes
 *
 * In this example the WordPress "get_post_galleries()" filter is applied and the post/page content
 * is scanned for any [mla_gallery] shortcodes. If one or more are found they are processed and an
 * array of the items they generate is added to the filter results.
 * 
 * NOTE: To compensate for a shortcoming of the WordPress filter, there must be at least one [gallery]
 * shortcode in the post/page content. You can use [gallery ids=0] as a placeholder shortcode to
 * trigger the filter without returning any results to the post/page.
 *
 * The "get_post_galleries()" function is called by, for example, the Dominant Colors Lazy Loading
 * plugin by Manuel Wieser.
 *
 * Created for support topic "Lazy load and masonry layout"
 * opened on 10/18/2016 by "ghislainsc".
 * https://wordpress.org/support/topic/lazy-load-and-masonry-layout/
 *
 * @package Get Post Galleries Example
 * @version 1.00
 */

/*
Plugin Name: Get Post Galleries Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Hooks a WordPress filter to supply a list of the items returned from [mla_gallery] shortcodes
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
 * Class Get Post Galleries Example hooks a WordPress filter to supply a list of the
 * items returned from [mla_gallery] shortcodes
 *
 * @package Get Post Galleries Example
 * @since 1.00
 */
class GetPostGalleriesExample {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 1.00
	 */
	public static function initialize() {
		
		add_filter( 'get_post_galleries', 'GetPostGalleriesExample::get_post_galleries', 10, 2 );
	}

	/**
	 * Get Post Galleries Filter
	 *
	 * This filter retrieves [mla_gallery] items from the contents of a post
	 *
	 * @since 1.00
	 *
	 * @param array   $galleries Associative array of all found post galleries.
	 * @param WP_Post $post      Post object.
	 */
	public static function get_post_galleries( $galleries, $current_post ) {
		global $post;
		
		$count = preg_match_all( "/\[mla_gallery([^\\]]*)\\]/", $current_post->post_content, $matches, PREG_PATTERN_ORDER );
		if ( $count ) {
			$save_post = $post;
			$post = $current_post; // set global variable for mla_gallery_shortcode(
			add_filter( 'mla_gallery_item_values', 'GetPostGalleriesExample::mla_gallery_item_values', 10, 1 );

			foreach ( $matches[1] as $index => $match ) {
				// Filter out shortcodes that are not an exact match
				if ( empty( $match ) || ( ' ' == substr( $match, 0, 1 ) ) ) {
					// Remove trailing "/" from XHTML-style self-closing shortcodes
					$query = trim( rtrim( $matches[1][$index], '/' ) );
					self::$item_values = array();
					$results = do_shortcode( '[mla_gallery ' . $query . ' cache_results=false update_post_meta_cache=false update_post_term_cache=false]' );
					$ids = array();
					$src = array();
					foreach( self::$item_values as $ID => $url ) {
						$ids[] = $ID;
						$src[] = $url;
					}

					if ( !empty( $ids ) ) {
						$galleries[] = array ( 'ids' => implode( ',', $ids ), 'src' => $src, );
					}
 				} // exact match
			} // foreach $match

			remove_filter( 'mla_gallery_item_values', 'GetPostGalleriesExample::mla_gallery_item_values', 10, 1 );
			$post = $save_post;
		} // if $count
		
		return $galleries;
	} // get_post_galleries

	/**
	 * Save the item values
	 *
	 * @since 1.00
	 *
	 * @var array
	 */
	private static $item_values = array();
	
	/**
	 * Collect the information needed by self::get_post_galleries()
	 *
	 * @since 1.00
	 *
	 * @param array $item_values parameter_name => parameter_value pairs
	 */
	public static function mla_gallery_item_values( $item_values ) {
		self::$item_values[ $item_values['attachment_ID'] ] = $item_values['thumbnail_url'];

		return $item_values;
	} // mla_gallery_item_values
} // Class GetPostGalleriesExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'GetPostGalleriesExample::initialize');
?>
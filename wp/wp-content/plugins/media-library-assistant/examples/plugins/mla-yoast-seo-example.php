<?php
/**
 * Supports WordPress SEO by Yoast Page Analysis and XMP Sitemap generation
 *
 * @package MLA Yoast SEO Example
 * @version 1.10
 */

/*
Plugin Name: MLA Yoast SEO Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Supports WordPress SEO by Yoast Page Analysis and XMP Sitemap generation
Author: David Lingren
Version: 1.10
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
 * @package MLA Yoast SEO Example
 * @since 1.00
 */
class MLAYoastSEOExample {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 1.00
	 *
	 * @return	void
	 */
	public static function initialize() {
		/*
		 * Filter: 'wpseo_sitemap_urlimages' - Allows updates to the list of images in the page/post
		 * Filter: 'wpseo_sitemap_entry' - adjusts the entire entry before it gets added to the sitemap
		 *
		 * Defined/applied in /wordpress-seo/inc/class-sitemaps.php
		 */
		add_filter( 'wpseo_sitemap_urlimages', 'MLAYoastSEOExample::wpseo_sitemap_urlimages', 10, 2 );
		//add_filter( 'wpseo_sitemap_entry', 'MLAYoastSEOExample::wpseo_sitemap_entry', 10, 3 );
	}

	/**
	 * Add [mla_gallery] output to 'images' array for SEO analysis
	 *
	 * @since 1.10
	 *
	 * @param	array	$url ( [index] => array( 'src' => URL of image file, 'alt' => ALT Text ) )
	 * @param	integer	$post_id ID of the current post
	 */
	public static function wpseo_sitemap_urlimages( $url, $post_id ) {
		global $post;
//error_log( __LINE__ . " wpseo_sitemap_urlimages( {$post_id} ) initial url = " . var_export( $url, true ), 0 );

		$post = get_post( $post_id ); // Set the parent post/page; used in [mla_gallery]
		if ( $count = preg_match_all( "/\\[mla_gallery([^\\]]*)\\]/", $post->post_content, $matches ) ) {
			foreach( $matches[0] as $index => $match ) {
				$tail = $matches[1][ $index ];
				/*
				 * Only process shortcodes that are an exact match
				 */
				if ( empty( $tail ) || ( ' ' == substr( $tail, 0, 1 ) ) ) {
					$the_gallery = do_shortcode( $match );

					/*
					 * If MLA is not active the shortcode is not processed;
					 * substitute empty results.
					 */
					if ( $the_gallery == $match ) {
						$the_gallery = '';
					}
	
					$ref_count = preg_match_all( '/\<img.*src="([^"]*)".*alt="([^"]*)"/', $the_gallery, $references );
					if ( $ref_count ) {
						foreach( $references[1] as $ref_index => $reference ) {
							$url[] = array( 'src' => $reference, 'alt' => $references[2][ $ref_index ] );
						}
					}

					unset( $ref_count, $references );
				} // exact match
			}
//error_log( __LINE__ . " wpseo_sitemap_urlimages( {$post_id} ) final url = " . var_export( $url, true ), 0 );
			unset( $count, $matches );
		} // found matche(s)

		return $url;
	}

	/**
	 * Add [mla_gallery] output to post/page content for SEO anapysis
	 *
	 * @since 1.00
	 *
	 * @param	string	$post_content The Post content string
	 * @param	object	$the_post The post object.
	 *
	 * @return	string	Expanded Post content string
	 */
	public static function wpseo_sitemap_entry( $url, $post_type, $the_post ) {
//error_log( __LINE__ . " wpseo_sitemap_entry( {$post_type}, {$the_post->ID} ) initial url = " . var_export( $url, true ), 0 );
//error_log( __LINE__ . " wpseo_sitemap_entry( {$post_type}, {$the_post->ID} ) the_post = " . var_export( $the_post, true ), 0 );

		return $url;
	}
} // Class MLAYoastSEOExample

/*
 * Install the filter at an early opportunity
 */
add_action('init', 'MLAYoastSEOExample::initialize');
?>
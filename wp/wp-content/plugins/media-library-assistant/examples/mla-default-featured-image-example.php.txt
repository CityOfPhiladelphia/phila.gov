<?php
/*
Plugin Name: MLA Default Featured Image Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Supplies a default "Featured Image" thumbnail based on item's file extension
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
 * Class MLA Default Featured Image Example supplies a default "Featured Image" thumbnail
 * based on item's file extension
 *
 * @package MLA Default Featured Image Example
 * @since 1.00
 */
class MLADefaultFeaturedImage {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 1.00
	 */
	public static function initialize() {
		global $wpdb;
		
		// The filters are only useful for front-end posts/pages; exit if in the admin section
		if ( is_admin() )
			return;
		
		// Build an array of extension to default image ID assignments
		$query = "SELECT * FROM {$wpdb->postmeta} WHERE meta_key = 'default_thumbnail_for'";
		$results = $wpdb->get_results( $query );

		if ( is_array( $results ) ) {
			foreach( $results as $result ) {
				$extensions = array_map( 'strtolower', array_map( 'trim', explode( ',', $result->meta_value ) ) );
				foreach ( $extensions as $extension ) {
					self::$default_thumbnails[ $extension ] = absint( $result->post_id );
				}
			}

			add_filter( 'mla_gallery_featured_image', 'MLADefaultFeaturedImage::mla_gallery_featured_image', 10, 4 );
		}
	}

	/**
	 * Map file extensions to thumbnail items
	 *
	 * @since 1.00
	 *
	 * @var	array ( extension => item_ID
	 */
	private static $default_thumbnails = array();

	/**
	 * Replace empty Featured Image tags with a default based on the item's file extension
	 *
	 * @since 1.00
	 *
	 * @param	array	parameter_name => parameter_value pairs
	 */
	public static function mla_gallery_featured_image( $feature, $attachment, $size, $item_values ) {
		if ( ! empty( $feature ) ) {
			return $feature;
		}
		
		$extension = strtolower( pathinfo( $item_values['file'], PATHINFO_EXTENSION ) );
		if ( isset( self::$default_thumbnails[ $extension ] ) ) {
			$feature = wp_get_attachment_image( self::$default_thumbnails[ $extension ], $size, false, array( 'class' => 'attachment-thumbnail' ) );
		}
		
		return $feature;
	} // mla_gallery_featured_image
} // Class MLADefaultFeaturedImage

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLADefaultFeaturedImage::initialize');
?>
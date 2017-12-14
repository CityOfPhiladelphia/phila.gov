<?php
/**
 * Creates a taxonomy=slug parameter from a parent's custom field value
 *
 * @package MLA Project Slug Example
 * @version 1.01
 */

/*
Plugin Name: MLA Project Slug Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Provides examples of hooking the filters provided by the [mla_gallery] shortcode
Author: David Lingren
Version: 1.00
Author URI: http://fairtradejudaica.org/our-story/staff/

Copyright 2014-2016 David Lingren

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
 * Class MLA Project Slug Example hooks all of the filters provided by the [mla_gallery] shortcode
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding everything
 * else inside a class means this is the only name you have to worry about.
 *
 * @package MLA Project Slug Example
 * @since 1.00
 */
class MLAProjectSlugExample {
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
		add_filter( 'mla_gallery_attributes', 'MLAProjectSlugExample::mla_gallery_attributes_filter', 10, 1 );
	}

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
	public static function mla_gallery_attributes_filter( $shortcode_attributes ) {
		if ( isset( $shortcode_attributes['parent_meta'] ) ) {
			global $post;

			$values = explode( ',', $shortcode_attributes['parent_meta'] );
			$meta_key = $values[0];
			$taxonomy = ( isset( $values[1] ) ) ? $values[1] : 'attachment_category';
			$values = get_post_meta( $post->ID, $meta_key );

			if ( isset( $shortcode_attributes['exclude_tags'] ) ) {
				$values = implode(  $values );
				$exclude_tags = 'array( "' . implode( '", "', explode( ',', $shortcode_attributes['exclude_tags'] ) ) . '" )';
				$shortcode_attributes['tax_query'] = "array( 'relation' => 'AND',
array('taxonomy' => '{$taxonomy}' ,'field' => 'slug','terms' => '{$values}', 'operator' => 'IN'),
array('taxonomy' => 'attachment_tag','field' => 'slug','terms' => {$exclude_tags}, 'operator' => 'NOT IN')
)";

				unset( $shortcode_attributes['exclude_tags'] );
			} else {
				if ( is_array( $values ) ) {
					$shortcode_attributes[ $taxonomy ] = implode( ',', $values );
				}
			}

			unset( $shortcode_attributes['parent_meta'] );
		}

		return $shortcode_attributes;
	} // mla_gallery_attributes_filter
} // Class MLAProjectSlugExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLAProjectSlugExample::initialize');
?>
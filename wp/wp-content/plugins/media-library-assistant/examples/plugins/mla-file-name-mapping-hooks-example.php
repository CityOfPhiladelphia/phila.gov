<?php
/**
 * Replaces the Title by a cleaned up version of the file name.
 *
 * @package MLA File Name Mapping Hooks Example
 * @version 1.00
 */

/*
Plugin Name: MLA File Name Mapping Hooks Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Replace Title, Caption and ALT Text with re-formatted file name
Author: David Lingren
Version: 1.02
Author URI: http://fairtradejudaica.org/our-story/staff/

Copyright 2014 - 2016 David Lingren

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
 * Class MLA File Name Mapping Hooks Example hooks one of the filters provided by the IPTC/EXIF and Custom Field mapping features
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding enerything
 * else inside a class means this is the only name you have to worry about.
 *
 * @package MLA File Name Mapping Hooks Example
 * @since 1.00
 */
class MLAFileNameMappingHooksExample {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * Installs filters and actions that handle the MLA hooks for uploading and mapping.
	 *
	 * @since 1.00
	 *
	 * @return	void
	 */
	public static function initialize() {
		/*
		 * The filters are only useful in the admin section; exit if in the "front-end" posts/pages. 
		 */
		if ( ! is_admin() )
			return;

		/*
		 * add_filter parameters:
		 * $tag - name of the hook you're filtering; defined by [mla_gallery]
		 * $function_to_add - function to be called when [mla_gallery] applies the filter
		 * $priority - default 10; lower runs earlier, higher runs later
		 * $accepted_args - number of arguments your function accepts
		 * Comment out the filters you don't need; save them for future use
		 */
		add_filter( 'mla_mapping_updates', 'MLAFileNameMappingHooksExample::mla_mapping_updates_filter', 10, 5 );
	}

	/**
	 * MLA Mapping Updates Filter
	 *
	 * This filter is called AFTER all mapping rules are applied.
	 * You can add, change or remove updates for the attachment's
	 * standard fields, taxonomies and/or custom fields.
	 *
	 * @since 1.00
	 *
	 * @param	array	updates for the attachment's standard fields, taxonomies and/or custom fields
	 * @param	integer post ID to be evaluated
	 * @param	string 	category/scope to evaluate against: custom_field_mapping or single_attachment_mapping
	 * @param	array 	mapping rules
	 * @param	array 	attachment_metadata, default NULL
	 *
	 * @return	array	updated attachment's updates
	 */
	public static function mla_mapping_updates_filter( $updates, $post_id, $category, $settings, $attachment_metadata ) {
		/*
		 * Derive the new Title from the file name (without the extension).
		 * Clean up the file name for use in the Title, Caption and ALT Text fields.
		 */
		$my_setting = array(
			'data_source' => 'template',
			'meta_name' => '([+name_only+])',
			'option' => 'raw'
		);
		$name_only = trim( MLAOptions::mla_get_data_source( $post_id, 'single_attachment_mapping', $my_setting, NULL ) );
		$new_value = str_replace( array( '-', '_', '.' ), ' ', $name_only );
		
		/*
		 * If $updates[ 'post_title' ], etc. is set, some mapping rule has been set up, so we respect the result.
		 * If not, use whatever the current value is.
		 */
		if ( isset( $updates[ 'post_title' ] ) ) {
			$old_title = $updates[ 'post_title' ];
		} else {
			$post = get_post( $post_id );
			$old_title = $post->post_title;
		}
		
		/*
		 * If the cleanup has changed the value,
		 * put the new value in the $updates array.
		 */
		if ( $old_title != $new_value ) {
			$updates[ 'post_title' ] = $new_value;
		}
		
		if ( isset( $updates[ 'post_excerpt' ] ) ) {
			$old_caption = $updates[ 'post_excerpt' ];
		} else {
			$post = get_post( $post_id );
			$old_caption = $post->post_excerpt;
		}
		
		if ( $old_caption != $new_value ) {
			$updates[ 'post_excerpt' ] = $new_value;
		}
		
		// Only replace ALT Text if Image Metadata is present
		$old_metadata = get_metadata( 'post', $post_id, '_wp_attachment_metadata', true );
		if ( ! empty( $old_metadata ) ) {
			// Find the current ALT Text value
			if ( isset( $updates[ 'image_alt' ] ) ) {
				$old_alt = $updates[ 'image_alt' ];
			} else {
				$old_alt = get_metadata( 'post', $post_id, '_wp_attachment_image_alt', true );
			}
		
			// Replace the ALT Text value with the clean file name
			if ( $old_alt != $new_value ) {
				$updates[ 'image_alt' ] = $new_value;
			}
		}
		
		return $updates;
	} // mla_mapping_updates_filter
} //MLAFileNameMappingHooksExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLAFileNameMappingHooksExample::initialize');
?>
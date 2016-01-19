<?php
/**
 * Provides an example of the filters provided by the IPTC/EXIF and Custom Field mapping features
 *
 * In this example the Title is replaced by a hyperlink using the IPTC 2#005 Object Name value.
 *
 * @package MLA jhdean Mapping Hooks Example
 * @version 1.01
 */

/*
Plugin Name: MLA jhdean Mapping Hooks Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Replace Title with hyperlink, for Jeff Dean.
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
 * Class MLA jhdean Mapping Hooks Example hooks one of the filters provided by the IPTC/EXIF and Custom Field mapping features
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding enerything
 * else inside a class means this is the only name you have to worry about.
 *
 * @package MLA jhdean Mapping Hooks Example
 * @since 1.00
 */
class MLAjhdeanMappingHooksExample {
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
		add_filter( 'mla_mapping_updates', 'MLAjhdeanMappingHooksExample::mla_mapping_updates_filter', 10, 5 );
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
		 * We are only concerned with Standard Field mapping
		 */
		if ( ! in_array( $category, array( 'iptc_exif_mapping', 'iptc_exif_standard_mapping' ) ) ) {
			return $updates;
		}

		/*
		 * If $updates[ 'post_title' ] is set, some mapping rule
		 * has been set up, so we respect the result. If not,
		 * use whatever the current Title value is.
		 */
		if ( isset( $updates[ 'post_title' ] ) ) {
			$old_value = $updates[ 'post_title' ];
		} else {
			$post = get_post( $post_id );
			$old_value = $post->post_title;
		}

		/*
		 * Derive the new Title from the IPTC Object Name, if present.
		 * You can use MLAOptions::mla_get_data_source() to get anything available.
		 */
		$my_setting = array(
			'data_source' => 'template',
			'meta_name' => '([+iptc:2#005+])',
			'option' => 'raw'
		);
		$object_name = trim( MLAOptions::mla_get_data_source( $post_id, 'single_attachment_mapping', $my_setting, NULL ) );
		
		if ( ! empty( $object_name ) ) {
			$object_link = strtolower( str_replace( array( ' ', '-', '_', '.' ), '-', $object_name ) );

			$new_title = sprintf( '<a id="detail-title" target="blank" href="http://www.jhdstaging.jeffreyhdean.com/portfolio/%1$s/"> %2$s </a>', $object_link, $object_name );

			$new_jig_link = sprintf( 'http://www.jeffreyhdean.jhddevelopment.dev/portfolio/%1$s/', $object_link );			

			if ( $old_value != $new_title ) {
				$updates[ 'post_title' ] = $new_title;
				
				// Create or add to the custom field updates
				if ( empty( $updates[ 'custom_updates' ] ) ) {
					$updates[ 'custom_updates' ] = array();
				}
				
				$updates[ 'custom_updates' ][ 'jig_image_link' ] = $new_jig_link;
			}
		}
		
		return $updates;
	} // mla_mapping_updates_filter
} //MLAjhdeanMappingHooksExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLAjhdeanMappingHooksExample::initialize');
?>
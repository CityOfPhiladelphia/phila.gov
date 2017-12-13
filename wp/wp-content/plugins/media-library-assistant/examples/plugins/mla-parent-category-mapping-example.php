<?php
/**
 * Assigns parent term when child term(s) are assigned.
 *
 * Created for support topic "All Category Links Working But One"
 * opened on 3/5/2017 by "Ellsinore":
 * https://wordpress.org/support/topic/all-category-links-working-but-one/
 *
 * @package MLA Parent Category Mapping Example
 * @version 1.00
 */

/*
Plugin Name: MLA Parent Category Mapping Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Assigns parent term when child term(s) are assigned
Author: David Lingren
Version: 1.00
Author URI: http://fairtradejudaica.org/our-story/staff/

Copyright 2017 David Lingren

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
 * Class MLA Parent Category Mapping Example hooks the mla_mapping_updates filter provided by
 * the IPTC/EXIF and Custom Field mapping features
 *
 * @package MLA Parent Category Mapping Example
 * @since 1.00
 */
class MLAParentCategoryMappingExample {
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
		// The filters are only useful in the admin section; exit if in the "front-end" posts/pages. 
		if ( ! is_admin() )
			return;

		add_filter( 'mla_mapping_updates', 'MLAParentCategoryMappingExample::mla_mapping_updates', 10, 5 );
	}

	/**
	 * MLA Mapping Updates Filter
	 *
	 * Mark the parent term if one or more children are present.
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
	public static function mla_mapping_updates( $updates, $post_id, $category, $settings, $attachment_metadata ) {
//error_log( __LINE__ . " MLAParentCategoryMappingExample::mla_mapping_updates( {$post_id}, {$category} ) updates = " . var_export( $updates, true ), 0 );
//error_log( __LINE__ . " MLAParentCategoryMappingExample::mla_mapping_updates( {$post_id}, {$category} ) settings = " . var_export( $settings, true ), 0 );
		
		// Mark parent term if one or more children are present
		if ( isset( $updates['taxonomy_updates'] ) ) {
			foreach ( $updates['taxonomy_updates']['inputs'] as $taxonomy => $terms ) {
				if ( !empty( $settings['taxonomy'][ $taxonomy ] ) ) {
					$parent = isset( $settings['taxonomy'][ $taxonomy ]['parent'] ) ? absint( $settings['taxonomy'][ $taxonomy ]['parent'] ) : 0;
					if ( 0 < $parent ) {
						$updates['taxonomy_updates']['inputs'][ $taxonomy ][] = $parent;
					}
				}
			}
//error_log( __LINE__ . " MLAParentCategoryMappingExample::mla_mapping_updates( {$post_id}, {$category} ) taxonomy_updates = " . var_export( $updates['taxonomy_updates']['inputs'], true ), 0 );
		}
		
		return $updates;
	} // mla_mapping_updates_filter
} //MLAParentCategoryMappingExample

// Install the filters at an early opportunity
add_action('init', 'MLAParentCategoryMappingExample::initialize');
?>
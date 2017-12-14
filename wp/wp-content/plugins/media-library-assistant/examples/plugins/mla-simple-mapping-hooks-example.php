<?php
/**
 * Provides an example of the filters provided by the IPTC/EXIF and Custom Field mapping features
 *
 * In this example:
 *     - Define an IPTC/EXIF custom field mapping rule for "regex_split_headline"
 *       to populate the "damArtist" and "damEvent" custom fields
 *     - Define an IPTC/EXIF custom field mapping rule for "update_menu_order"
 *       to update the WordPress "menu_order" standard field
 *     - Remove or comment out the "return $updates" line to apply the Title and ALT Text cleanup logic
 *
 * Created for support topic "Quick question on replacing string(s) in image metadata"
 * opened on 7/1/2014 by "AppleBag/Bubba":
 * https://wordpress.org/support/topic/quick-question-on-replacing-strings-in-image-metadata
 *
 * Enhanced for support topic "Regex to Split Data to Custom Fields"
 * opened on 8/21/2015 by "rockgeek":
 * https://wordpress.org/support/topic/regex-to-split-data-to-custom-fields
 *
 * Enhanced for support topic "Replacing Sort Order attribute"
 * opened on  8/4/2016 by "ciano":
 * https://wordpress.org/support/topic/replacing-sort-order-attribute
 *
 * @package MLA Simple Mapping Hooks Example
 * @version 1.02
 */

/*
Plugin Name: MLA Simple Mapping Hooks Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Populates custom fields based on a regular expression; updates menu_order; cleans up Title and ALT Text
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
 * Class MLA Simple Mapping Hooks Example hooks all of the filters provided by the IPTC/EXIF and Custom Field mapping features
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding enerything
 * else inside a class means this is the only name you have to worry about.
 *
 * @package MLA Simple Mapping Hooks Example
 * @since 1.00
 */
class MLASimpleMappingHooksExample {
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
		 * This filter is applied in class-mla-options.php functions
		 * mla_evaluate_iptc_exif_mapping and mla_evaluate_custom_field_mapping
		 */
		add_filter( 'mla_mapping_updates', 'MLASimpleMappingHooksExample::mla_mapping_updates', 10, 5 );
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
	public static function mla_mapping_updates( $updates, $post_id, $category, $settings, $attachment_metadata ) {
		//error_log( "MLASimpleMappingHooksExample::mla_mapping_updates( {$post_id}, {$category} ) \$updates = " . var_export( $updates, true ), 0 );
		//error_log( 'MLASimpleMappingHooksExample::mla_mapping_updates $settings = ' . var_export( $settings, true ), 0 );
		//error_log( 'MLASimpleMappingHooksExample::mla_mapping_updates $attachment_metadata = ' . var_export( $attachment_metadata, true ), 0 );

		/*
		 * The first part of the example splits the IPTC "2#105 headline" field, if present, and
		 * updates two custom fields with the resulting values. You must define an IPTC/EXIF
		 * custom field mapping rule for "regex_split_headline" to activate this logic.
		 */
		if ( isset( $updates['custom_updates'] ) && isset( $updates['custom_updates']['regex_split_headline'] ) ) {
			$headline = $updates['custom_updates']['regex_split_headline'];
			$headline = is_string( $headline ) ? trim( $headline ) : '';

			if ( preg_match( '/(.*)\|\|(.*)/', $headline, $matches ) ) {
				$artist = trim( $matches[1] );
				$event  = trim( $matches[2] );

				/*
				 * You can update the field(s) directly or (preferred) let MLA do the updates
				 */
				if ( ! empty( $artist ) ) {
					//update_metadata( 'post', $post_id, 'damArtist', $artist );
					$updates['custom_updates']['damArtist'] = $artist;
				} else {
					//delete_metadata( 'post', $post_id, 'damArtist' );
					$updates['custom_updates']['damArtist'] = NULL;
				}

				if ( ! empty( $event ) ) {
					//update_metadata( 'post', $post_id, 'damEvent', $event );
					$updates['custom_updates']['damArtist'] = $event;
				} else {
					//delete_metadata( 'post', $post_id, 'damEvent' );
					$updates['custom_updates']['damEvent'] = NULL;
				}
			}

			// We don't actually store regex_split_headline as a custom field
			unset( $updates['custom_updates']['regex_split_headline'] );
		}

		/*
		 * The second part of the example takes a numeric value from the "update_menu_order" rule
		 * and updates the WordPress "menu_order" standard field. You must define an IPTC/EXIF
		 * custom field mapping rule for "update_menu_order" to activate this logic.
		 */
		if ( isset( $updates['custom_updates'] ) && isset( $updates['custom_updates']['update_menu_order'] ) ) {
			$new_value = absint( $updates['custom_updates']['update_menu_order'] );

			/*
			 * If $updates[ 'menu_order' ] is set, some other mapping rule
			 * has been set up, so we respect the result. If not, use
			 * whatever the current Menu Order value is.
			 */
			if ( isset( $updates[ 'menu_order' ] ) ) {
				$old_value = $updates[ 'menu_order' ];
			} else {
				$post = get_post( $post_id );
				$old_value = $post->menu_order;
			}

			if ( $old_value != $new_value ) {
				$updates[ 'menu_order' ] = $new_value;
			}

			// We don't actually store update_menu_order as a custom field
			unset( $updates['custom_updates']['update_menu_order'] );
		}

		/*
		 * Remove or comment out the next line to apply the Title and ALT Text cleanup logic
		 */
		return $updates;

		/*
		 * For the Title and ALT Text cleanup, we are only concerned with Standard Field mapping
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

		/*
		 * Clean up the Title value. If the cleanup has changed the value,
		 * put the new value in the $updates array.
		 */
		$new_title = str_replace( array( '-', '_', '.' ), ' ', $old_value );
		if ( $old_value != $new_title ) {
			$updates[ 'post_title' ] = $new_title;
		}

		// Find the current ALT Text value
		if ( isset( $updates[ 'image_alt' ] ) ) {
			$old_value = $updates[ 'image_alt' ];
		} else {
			$old_value = get_metadata( 'post', $post_id, '_wp_attachment_image_alt', true );
		}

		// Replace the ALT Text value with the clean Title
		if ( $old_value != $new_title ) {
			$updates[ 'image_alt' ] = $new_title;
		}

		/*
		 * To stop this rule's updates, return an empty array, i.e., return array();
		 */
		return $updates;
	} // mla_mapping_updates
} //MLASimpleMappingHooksExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLASimpleMappingHooksExample::initialize');
?>
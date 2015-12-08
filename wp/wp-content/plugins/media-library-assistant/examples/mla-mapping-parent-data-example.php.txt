<?php
/**
 * Copies a custom field value from the parent post/page to all attached Media Library items.
 *
 * Provides an example of the filters provided by the IPTC/EXIF and Custom Field mapping features
 *
 * @package MLA Mapping Parent Data Example
 * @version 1.01
 */

/*
Plugin Name: MLA Mapping Parent Data Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Copies a custom field value from the parent post/page to all attached Media Library items.
Author: David Lingren
Version: 1.01
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
 * Class MLA Mapping Parent Data Example hooks one of the filters provided by
 * the IPTC/EXIF and Custom Field mapping features
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding
 * everything else inside a class means this is the only name you have to worry about.
 *
 * @package MLA Mapping Parent Data Example
 * @since 1.00
 */
class MLAMappingParentDataExample {
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

		add_filter( 'mla_mapping_updates', 'MLAMappingParentDataExample::mla_mapping_updates', 10, 5 );

		/*
		 * Triggered by wp_insert_post and wp_publish_post in wp-includes/post.php 
		 */
		add_filter( 'save_post', 'MLAMappingParentDataExample::save_post', 10, 3 );
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
		/*
		 * For this example we have created a custom field named "ref_offer", which can be
		 * changed for your application. Make sure our rule is defined.
		 */
		if ( 'single_attachment_mapping' == $category ) {
			if ( ! isset( $settings['ref_offer'] ) ) {
				return $updates;
			}
		} elseif ( 'custom_field_mapping' == $category ) {
			foreach ( $settings as $setting ) {
				if ( $found_it = ( $setting['name'] == 'ref_offer' ) ) {
					break;
				}
			}

			if ( ! $found_it ) {
				return $updates;
			}
		}

		// Get the current value, if any, to see if a change is required
		$old_value = get_metadata( 'post', $post_id, 'ref_offer', true );

		// Get the item to find its parent's value, if any
		$item = get_post( $post_id );
		if ( 0 < $item->post_parent ) {
			$parent_value = get_metadata( 'post', $item->post_parent, 'ref_offer', true );
		} else {
			$parent_value = '';
		}

		// Add/replace the value if it has changed
		if ( $old_value != $parent_value ) {
			$updates['custom_updates'][ 'ref_offer' ] = $parent_value;
		}

		return $updates;
	} // mla_mapping_updates

	/**
	 * WordPress save post action
	 *
	 * This action is called AFTER the post has been added or updated in the posts database table.
	 * It is used to map or re-map the IPTC/EXIF and custom field rules.
	 *
	 * @since 1.01
	 *
	 * @param	integer post ID to be evaluated
	 * @param	object 	post content
	 * @param	boolean true if update, false if insert
	 */
	public static function save_post( $post_id, $post, $update ) {
		// If this is just a revision, exit.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// You may need to add MIME types other than 'image' for your application	
		foreach ( get_attached_media( 'image' , $post_id ) as $attachment_id => $value) {
			$updates = MLAOptions::mla_evaluate_custom_field_mapping( $attachment_id, 'single_attachment_mapping' );

			if ( !empty( $updates ) ) {
				$item_content = MLAData::mla_update_single_item( $attachment_id, $updates );
			}
		}
	} // save_post
} //MLAMappingParentDataExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLAMappingParentDataExample::initialize');
?>
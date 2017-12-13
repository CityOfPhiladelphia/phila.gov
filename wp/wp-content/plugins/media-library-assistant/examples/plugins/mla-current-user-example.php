<?php
/*
Plugin Name: MLA Current User Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Uses the current logged in user to supply an "Author" parameter for the query
Author: David Lingren
Version: 1.01
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
 * Class MLA Current User Example uses the current logged in user to supply an "Author" parameter for the query
 *
 * @package MLA Current User Example
 * @since 1.00
 */
class MLACurrentUserExample {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 1.00
	 */
	public static function initialize() {
		// The filters are only useful for front-end posts/pages; exit if in the admin section
		if ( is_admin() )
			return;

		add_filter( 'mla_gallery_raw_attributes', 'MLACurrentUserExample::mla_gallery_raw_attributes_filter', 10, 1 );
		add_filter( 'mla_gallery_attributes', 'MLACurrentUserExample::mla_gallery_attributes_filter', 10, 1 );
		add_filter( 'mla_gallery_open_template', 'MLACurrentUserExample::mla_gallery_open_template_filter', 10, 1 );
		add_filter( 'mla_gallery_item_values', 'MLACurrentUserExample::mla_gallery_item_values_filter', 10, 1 );
	}

	/**
	 * Save the shortcode attributes
	 *
	 * @since 1.01
	 *
	 * @var	array
	 */
	private static $shortcode_attributes = array();

	/**
	 * MLA Gallery Raw (Display) Attributes
	 *
	 * @since 1.01
	 *
	 * @param	array	the raw shortcode parameters passed in to the shortcode
	 */
	public static function mla_gallery_raw_attributes_filter( $shortcode_attributes ) {
		// Delete the selected file.
		if ( isset( $shortcode_attributes['my_filter'] ) && 'allow file deletion' == $shortcode_attributes['my_filter'] ) {
			if ( isset( $_REQUEST['attachment_ID'] ) ) {
				$id = (integer) $_REQUEST['attachment_ID'];
				if ( current_user_can( 'delete_post', $id ) ) { 
					$result = wp_delete_attachment( $id );
				} else {
					$result = false;
				}

				if ( ( false === $result ) || ( NULL === $result ) ) {
					$shortcode_attributes['gallery_open_message'] = "Could not delete attachment_ID '{$id}'.";
				} else {
					$result = (array) $result; // Some wp_delete_attachment calls return an object
					$shortcode_attributes['gallery_open_message'] = "Attachment '{$result['post_title']}' (ID {$id}) has been deleted.";
				}

				unset( $_REQUEST['attachment_ID'] );
			}
		}

		return $shortcode_attributes;
	} // mla_gallery_raw_attributes_filter

	/**
	 * MLA Gallery (Display) Attributes
	 *
	 * @since 1.00
	 *
	 * @param	array	the shortcode parameters passed in to the shortcode
	 */
	public static function mla_gallery_attributes_filter( $shortcode_attributes ) {
		global $wpdb;

		// ignore shortcodes without the author parameter set to "current"
		if ( empty( $shortcode_attributes['author'] ) || ( 'current' !== $shortcode_attributes['author'] ) ) {
			return $shortcode_attributes;
		}
		
		// ignore shortcodes with no logged in user
		$current_user = wp_get_current_user();
		if ( !( $current_user instanceof WP_User ) || ( 0 == $current_user->ID ) ) {
			unset ( $shortcode_attributes['author'] );
		} else {
			$shortcode_attributes['author'] = $current_user->ID;
		}

		// Save the attributes for use in the later filters
		self::$shortcode_attributes = $shortcode_attributes;

		return $shortcode_attributes;
	} // mla_gallery_attributes_filter

	/**
	 * Replace the caption value and update captiontag_content as well
	 *
	 * @since 1.01
	 *
	 * @param	array	parameter_name => parameter_value pairs
	 * @param	string	New value for Caption
	 *
	 * @return	array	item_values with updated 'caption' and 'captiontag_content'
	 */
	private static function _update_caption( $item_values, $new_caption ) {
		$old_caption = $item_values['caption'];
		$item_values['caption'] = $new_caption;

		if ( !empty( $item_values['captiontag_content'] ) ) {
			$item_values['captiontag_content'] = str_replace( $old_caption, $new_caption, $item_values['captiontag_content'] );
		} else {
			if ( $item_values['captiontag'] ) {
				$item_values['captiontag_content'] = '<' . $item_values['captiontag'] . " class='wp-caption-text gallery-caption' id='" . $item_values['selector'] . '-' . $item_values['attachment_ID'] . "'>\n\t\t" . $new_caption . "\n\t</" . $item_values['captiontag'] . ">\n";
			} else {
				$item_values['captiontag_content'] = $new_caption;
			}
		}

		return $item_values;
	} // _update_caption

	/**
	 * MLA Gallery Open Template
	 *
	 * @since 1.01
	 *
	 * @param	string	template used to generate the HTML markup
	 */
	public static function mla_gallery_open_template_filter( $open_template ) {
		// Check for a display message
		if ( isset( self::$shortcode_attributes['gallery_open_message'] ) ) {
			$open_template = '<p><strong>' . self::$shortcode_attributes['gallery_open_message'] . '</strong></p>' . $open_template;
		}

		return $open_template;
	} // mla_gallery_open_template_filter

	/**
	 * MLA Gallery Item Values
	 *
	 * @since 1.01
	 *
	 * @param	array	parameter_name => parameter_value pairs
	 */
	public static function mla_gallery_item_values_filter( $item_values ) {
		if ( isset( self::$shortcode_attributes['my_filter'] ) && ( 'allow file deletion' == self::$shortcode_attributes['my_filter'] ) ) {
			$id = (integer) $item_values['attachment_ID'];
			if ( current_user_can( 'delete_post', $id ) ) { 
				// Compose a new caption, adding the deletion link.
				$mla_link_href = "{$item_values['page_url']}?attachment_ID={$id}";
				$item_values = self::_update_caption( $item_values, sprintf( '%1$s<br><a href="%2$s" title="Click to delete">Delete this file</a>', $item_values['base_file'], $mla_link_href ) );
			} else {
				$item_values = self::_update_caption( $item_values, sprintf( '%1$s', $item_values['base_file'] ) );
			}
		}

		return $item_values;
	} // mla_gallery_item_values_filter
} // Class MLACurrentUserExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLACurrentUserExample::initialize');
?>
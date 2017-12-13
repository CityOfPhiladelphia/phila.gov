<?php
/*
Plugin Name: MLA File Size Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Adds File Size in KB/MB to the caption
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
 * Class MLA File Size Example hooks two of the filters provided by the [mla_gallery] shortcode
 *
 * @package MLA File Size Example
 * @since 1.00
 */
class MLAFileSizeExample {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 1.00
	 */
	public static function initialize() {
		// The filters are only useful for front-end posts/pages; exit if in the admin section
		if ( is_admin() )
			return;

		add_filter( 'mla_gallery_attributes', 'MLAFileSizeExample::mla_gallery_attributes_filter', 10, 1 );
		add_filter( 'mla_gallery_item_values', 'MLAFileSizeExample::mla_gallery_item_values_filter', 10, 1 );
	}

	/**
	 * Save the shortcode attributes
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $shortcode_attributes = array();

	/**
	 * MLA Gallery (Display) Attributes
	 *
	 * The $shortcode_attributes array is where you will find any of your own parameters that are coded in the
	 * shortcode, e.g., [mla_gallery my_parameter="my value"].
	 *
	 * @since 1.00
	 *
	 * @param	array	the shortcode parameters passed in to the shortcode
	 */
	public static function mla_gallery_attributes_filter( $shortcode_attributes ) {
		// Save the attributes for use in the later filters
		self::$shortcode_attributes = $shortcode_attributes;

		return $shortcode_attributes;
	} // mla_gallery_attributes_filter

	/**
	 * Replace the caption value and update captiontag_content as well
	 *
	 * @since 1.00
	 *
	 * @param	array	parameter_name => parameter_value pairs
	 * @param	string	New value for Caption
	 *
	 * @return	array	item_values with updated 'caption' and 'captiontag_content'
	 */
	private static function _update_caption( $item_values, $new_caption ) {
		$old_caption = $item_values['caption'];
		$item_values['caption'] = $new_caption;
		$item_values['captiontag_content'] = str_replace( $old_caption, $new_caption, $item_values['captiontag_content'] );

		return $item_values;
	} // _update_caption

	/**
	 * MLA Gallery Item Values
	 *
	 * @since 1.00
	 *
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated substitution parameter name => value pairs
	 */
	public static function mla_gallery_item_values_filter( $item_values ) {
		/*
		 * We use a shortcode parameter of our own to apply our filters on a gallery-by-gallery basis,
		 * leaving other [mla_gallery] instances untouched. If the "my_filter" parameter is not present,
		 * we have nothing to do.
		 */		
		if ( ! isset( self::$shortcode_attributes['my_filter'] ) ) {
			return $item_values; // leave them unchanged
		}

		// Add a formatted file size element to the existing caption.
		if ( 'file size' == self::$shortcode_attributes['my_filter'] ) {

			// You can use MLAShortcodes::mla_get_data_source() to get anything available.
			$my_setting = array(
				'data_source' => 'file_size',
				'option' => 'raw'
			);
			$file_size = (float) MLAShortcodes::mla_get_data_source( $item_values['attachment_ID'], 'single_attachment_mapping', $my_setting, NULL );

			if ( 1048576 < $file_size ) {
				$file_size = number_format( ($file_size/1048576), 3 ).' MB';
			} elseif ( 10240 < $file_size ) {
				$file_size = number_format( ($file_size/1024), 3 ).' KB';
			} else {
				$file_size = number_format( $file_size );
			}

			// Compose a new caption, adding the file size.
			return self::_update_caption( $item_values, sprintf( '%1$s<br>Size: %2$s', $item_values['caption'], $file_size ) );
		}

		return $item_values;
	} // mla_gallery_item_values_filter
} // Class MLAFileSizeExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLAFileSizeExample::initialize');
?>
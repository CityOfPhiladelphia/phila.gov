<?php
/*
Plugin Name: MLA Viewer Replacement Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Provides selective mla_caption replacement for PDF documents, etc.
Author: David Lingren
Version: 1.00
Author URI: http://fairtradejudaica.org/our-story/staff/

Copyright 2014, 2015 David Lingren

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
 * Class MLA Viewer Replacement replaces the caption for mla_viewer items
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding everything
 * else inside a class means this is the only name you have to worry about.
 */
class MLAViewerReplacement {
    /**
     * Initialization function, similar to __construct()
     */
    public static function initialize() {
        /*
         * The filters are only useful for front-end posts/pages; exit if in the admin section
         */
        if ( is_admin() )
            return;

        /*
         * add_filter parameters:
         */
        add_filter( 'mla_gallery_raw_attributes', 'MLAViewerReplacement::mla_gallery_raw_attributes', 10, 1 );
		add_filter( 'mla_gallery_arguments', 'MLAViewerReplacement::mla_gallery_arguments_filter', 10, 1 );
        add_filter( 'mla_gallery_item_values', 'MLAViewerReplacement::mla_gallery_item_values_filter', 10, 1 );
    }

    /**
     * Save the shortcode attributes
     */
    private static $shortcode_attributes = array();
    
    /**
	 * MLA Gallery Raw (Display) Attributes
	 *
	 * This filter gives you an opportunity to record or modify the arguments passed in to the shortcode
	 * before they pass through the logic to handle the 'mla_page_parameter' and "request:" prefix processing.
     *
     * The $shortcode_attributes array is where you will find your own parameters that
	 * are coded in the shortcode, e.g., [mla_gallery mla_fixed_title="my title"].
     */
    public static function mla_gallery_raw_attributes( $shortcode_attributes ) {
        /*
         * Save the attributes for use in the later filter
         */
        self::$shortcode_attributes = $shortcode_attributes;

        return $shortcode_attributes;
    } // mla_gallery_raw_attributes

	/**
	 * Save the shortcode arguments
	 */
	private static $all_display_parameters = array();

	/**
	 * MLA Gallery (Display) Arguments
	 *
	 * This filter gives you an opportunity to record or modify the gallery display arguments
	 * after the shortcode attributes are merged with the default arguments.
	 *
	 * Note that the values in this array are input or default values, not the final computed values
	 * used for the gallery display.  The computed values are in the $style_values, $markup_values and
	 * $item_values arrays passed to later filters below.
	 *
	 * @since 1.00
	 *
	 * @param	array	shortcode arguments merged with gallery display defaults, so every possible parameter is present
	 *
	 * @return	array	updated gallery display arguments
	 */
	public static function mla_gallery_arguments_filter( $all_display_parameters ) {

		self::$all_display_parameters = $all_display_parameters;
		return $all_display_parameters;
	} // mla_gallery_arguments_filter

    /**
     * MLA Gallery Item Values
     *
	 * The "Values" filter gives you a chance to modify the substitution parameter values
	 * before they are used to complete the associated template (in the corresponding "Parse" filter).
	 * It is called just before the values are used to parse the associated template.
	 * You can add, change or delete parameters as needed.
	 *
     * @since 1.00
     *
     * @param    array    parameter_name => parameter_value pairs
     *
     * @return    array    updated substitution parameter name => value pairs
     */
    public static function mla_gallery_item_values_filter( $item_values ) {
        /*
         * We use a shortcode parameter of our own to apply our filters on a
		 * gallery-by-gallery basis, leaving other [mla_gallery] instances untouched.
		 * If "mla_viewer_caption" is not present, we have nothing to do. Here is
		 * an example of how the custom parameter can be used:
         *
         * [mla_gallery ids="2621,2622,2623" mla_viewer=true mla_viewer_caption="{+title+}"]
         */
		if ( ! isset( self::$shortcode_attributes['mla_viewer_caption'] ) ) {
	        return $item_values;
		}

		$extension = pathinfo( $item_values['file'], PATHINFO_EXTENSION );
		if ( false === strpos( self::$all_display_parameters['mla_viewer_extensions'], $extension ) ) {
	        return $item_values;
		}

		// You can use MLAShortcodes::mla_get_data_source() to get anything available.
		$mla_viewer_caption = str_replace( '{+', '[+', str_replace( '+}', '+]', self::$shortcode_attributes['mla_viewer_caption'] ) );
		$data_source = array(
			'data_source' => 'template',
			'meta_name' => $mla_viewer_caption,
			'option' => 'text'
		);

		$mla_viewer_caption = MLAShortcodes::mla_get_data_source( $item_values['attachment_ID'], 'single_attachment_mapping', $data_source, NULL );
		$item_values['caption'] = $mla_viewer_caption;
        return $item_values;
    } // mla_gallery_item_values_filter
} // Class MLAViewerReplacement

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLAViewerReplacement::initialize');
?>
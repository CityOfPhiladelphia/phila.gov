<?php
/*
Plugin Name: MLA Fixed Values Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Provides an example of hooking the filters provided by the [mla_gallery] shortcode
Author: David Lingren
Version: 1.02
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
 * Class MLA Fixed Values Example hooks a few of the filters provided by the [mla_gallery] shortcode
 *
 * Thanks to "Harm10" for some improvements as part of this Support Topic:
 * https://wordpress.org/support/topic/fixed-href-link-or-caption-text-per-id
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding everything
 * else inside a class means this is the only name you have to worry about.
 */
class MLAFixedValuesExample {
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
        add_filter( 'mla_gallery_attributes', 'MLAFixedValuesExample::mla_gallery_attributes_filter', 10, 1 );
        add_filter( 'mla_gallery_item_values', 'MLAFixedValuesExample::mla_gallery_item_values_filter', 10, 1 );
		add_filter( 'mla_gallery_close_values', 'MLAFixedValuesExample::mla_gallery_close_values_filter', 10, 1 );
    }

    /**
     * Save the shortcode attributes
     */
    private static $shortcode_attributes = array();
    
	/*
	 * $mla_fixed_values stores the parameter(s) and values. If none are found, the
	 * initialization code sets it to false so the logic is quickly bypassed.
	 */
	private static $mla_fixed_values = NULL;

    /**
     * MLA Gallery (Display) Attributes
     *
     * This filter lets you record or modify the arguments passed in to the shortcode
     * before they are merged with the default arguments used for the gallery display.
     *
     * The $shortcode_attributes array is where you will find your own parameters that
	 * are coded in the shortcode, e.g.:
	 * [mla_gallery mla_fixed_caption="array('test1','test2')" mla_caption="{+mla_fixed_caption+}"].
     */
    public static function mla_gallery_attributes_filter( $shortcode_attributes ) {
        /*
         * Save the attributes for use in the later filter
         */
        self::$shortcode_attributes = $shortcode_attributes;

        return $shortcode_attributes;
    } // mla_gallery_attributes_filter

    /**
     * MLA Gallery Item Values
     *
     * @since 1.00
     *
     * @param    array    parameter_name => parameter_value pairs
     *
     * @return    array    updated substitution parameter name => value pairs
     */
    public static function mla_gallery_item_values_filter( $item_values ) {
        /*
         * We use shortcode parameters of our own to apply our filters on a
		 * gallery-by-gallery basis, leaving other [mla_gallery] instances untouched.
		 * If no "mla_fixed_" parameters are present, we have nothing to do. Here is
		 * an example of how the custom parameter can be used:
         *
         * [mla_gallery ids="2621,2622" mla_fixed_title="array('my title','my other title')" mla_image_attributes="title='{+mla_fixed_title+}'"]
		 *
		 * You can have as many "mla_fixed_" parameters as you need for different values.
         */

        if ( false === self::$mla_fixed_values ) {
            return $item_values; // leave them unchanged
        }

        /*
         * Evaluate the parameter value(s) once per page load.
         */
        if ( NULL === self::$mla_fixed_values ) {
			self::$mla_fixed_values = array();
			foreach ( self::$shortcode_attributes as $parmkey => $parmvalue ) {
                if ( 'mla_fixed_' == substr( $parmkey, 0, 10 ) ) {
					if ( 'array(' == substr( $parmvalue, 0, 6 ) ) {
	                    $function = @create_function( '', 'return ' . self::$shortcode_attributes[ $parmkey ] . ';' );
    	                if ( is_callable( $function ) ) {
        	                self::$mla_fixed_values[ $parmkey ] = $function();

		                    if ( ! is_array( self::$mla_fixed_values[ $parmkey ] ) ) {
    	                        self::$mla_fixed_values[ $parmkey ] = array();
        	                }
						} else {
                            self::$mla_fixed_values[ $parmkey ] = array();
						}
					} else {
                        self::$mla_fixed_values[ $parmkey ] = explode( ",", $parmvalue );
                        if ( false === self::$mla_fixed_values[ $parmkey ] ) {
                            self::$mla_fixed_values[ $parmkey ] = array();
                        }
                    }
                } // found mla_fixed_
			} // foreach parameter

			if ( empty( self::$mla_fixed_values ) ) {
				self::$mla_fixed_values = false;			
	            return $item_values;
			}
        } // initialization code

        /*
         * Apply the appropriate value to the current item.
         */
        foreach ( self::$mla_fixed_values as $mla_fixed_key => $mla_fixed_value ) {
           /*
            * Apply the appropriate value to the current item.
            */
            if ( isset( $mla_fixed_value[ $item_values['index'] - 1 ] ) ) {
                $item_values[ $mla_fixed_key ] = $mla_fixed_value[ $item_values['index'] - 1 ];
            }
        }

        return $item_values;
    } // mla_gallery_item_values_filter

	/**
	 * MLA Gallery Close Values
	 *
	 * @since 1.02
	 *
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated substitution parameter name => value pairs
	 */
	public static function mla_gallery_close_values_filter( $markup_values ) {
		/*
		 * Reset $mla_fixed_values for multiple shortcodes on the same post/page
		 */
		self::$mla_fixed_values = NULL;

		return $markup_values;
	} // mla_gallery_close_values_filter
} // Class MLAFixedValuesExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLAFixedValuesExample::initialize');
?>
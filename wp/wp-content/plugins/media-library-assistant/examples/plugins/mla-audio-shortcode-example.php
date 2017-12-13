<?php
/**
 * Provides an example of hooking the filters provided by the [mla_gallery] shortcode:
 *
 * This example replaces the gallery item content with "audio player" elements generated
 * by the WordPress [audio] shortcode.
 *
 * @package MLA Audio Shortcode Example
 * @version 1.00
 */

/*
Plugin Name: MLA Audio Shortcode Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Provides an example of hooking the filters provided by the [mla_gallery] shortcode
Author: David Lingren
Version: 1.00
Author URI: http://fairtradejudaica.org/our-story/staff/

Copyright 2013, 2014 David Lingren

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
 * Class MLA Audio Shortcode Example hooks two of the filters provided by the [mla_gallery] shortcode
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding everything
 * else inside a class means this is the only name you have to worry about.
 *
 * @package MLA Audio Shortcode Example
 * @since 1.00
 */
class MLAAudioShortcodeExample {
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
		add_filter( 'mla_gallery_attributes', 'MLAAudioShortcodeExample::mla_gallery_attributes_filter', 10, 1 );
		add_filter( 'mla_gallery_item_values', 'MLAAudioShortcodeExample::mla_gallery_item_values_filter', 10, 1 );
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
		/*
		 * Save the attributes for use in the later filters
		 */
		self::$shortcode_attributes = $shortcode_attributes;

		return $shortcode_attributes;
	} // mla_gallery_attributes_filter

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
		 * We use a shortcode parameter of our own to apply this filter on a gallery-by-gallery
		 * basis, leaving other [mla_gallery] instances untouched. If the "my_custom_audio"
		 * parameter is not present, we have nothing to do. If the parameter IS present,
		 * we replace the [+link+] value with the [audio] shortcode output.
		 *
		 * The "my_custom_audio" parameter can be used to pass parameters to the [audio] shortcode,
		 * such as autoplay, loop and preload. No validation of the parameters is done here.
		 */		
		if ( isset( self::$shortcode_attributes['my_custom_audio'] ) ) {
			$audio_args = self::$shortcode_attributes['my_custom_audio'];
			if ( empty( $audio_args ) ) {
				$audio_args = array();
			} elseif ( is_string( $audio_args ) ) {
				$audio_args = shortcode_parse_atts( $audio_args );
			}

			$audio_args['src'] = $item_values['base_url'] . '/' . $item_values['base_file'];
			$item_values['link'] = wp_audio_shortcode( $audio_args );
		}

		/*
		 * Replace the Spotify Playlist URL with the WordPress [embed] output
		 * Solution for support topic:
		 * https://wordpress.org/support/topic/using-wordpress-embeds-with-mla-gallery
		 * Remove "false && " to activate this feature
		 */
		if ( false && isset( $item_values['custom:spotify_playlist'] ) ) {
			//$item_values['custom:spotify_playlist'] = do_shortcode( '[embed]' . $item_values['custom:spotify_playlist'] . '[/embed]' );
			$item_values['custom:spotify_playlist'] = apply_filters('the_content', "[embed]" . $item_values['custom:spotify_playlist'] . "[/embed]");
			$item_values['caption'] = $item_values['custom:spotify_playlist'];
		}
		
		return $item_values;
	} // mla_gallery_item_values_filter
} // Class MLAAudioShortcodeExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLAAudioShortcodeExample::initialize');
?>
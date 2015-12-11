<?php
/**
 * Provides an example of hooking the filters provided by the [mla_gallery] shortcode
 *
 * In this example, the WordPress "attachment/media page" links are replaced by
 * "BuddyPress/rtMedia page" links. For audio and video files, an option is provided to
 * substitute the "cover_art" thumbnail image for the item Title in the thumbnail_content.
 *
 * @package MLA Gallery Hooks for BuddyPress & rtMedia Example
 * @version 1.05
 */

/*
Plugin Name: MLA Gallery Hooks for BuddyPress & rtMedia Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Provides an example of hooking the filters provided by the [mla_gallery] shortcode
Author: David Lingren
Version: 1.05
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
 * Class MLA BuddyPress Hooks Example hooks all of the filters provided by the [mla_gallery] shortcode
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding everything
 * else inside a class means this is the only name you have to worry about.
 *
 * @package MLA Gallery Hooks for BuddyPress & rtMedia Example
 * @since 1.00
 */
class MLABuddyPressHooksExample {
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
		 *
		 * Comment out the filters you don't need; save them for future use
		 */
		//add_filter( 'mla_gallery_raw_attributes', 'MLABuddyPressHooksExample::mla_gallery_raw_attributes_filter', 10, 1 );
		add_filter( 'mla_gallery_attributes', 'MLABuddyPressHooksExample::mla_gallery_attributes_filter', 10, 1 );
		//add_filter( 'mla_gallery_initial_content', 'MLABuddyPressHooksExample::mla_gallery_initial_content_filter', 10, 2 );
		//add_filter( 'mla_gallery_arguments', 'MLABuddyPressHooksExample::mla_gallery_arguments_filter', 10, 1 );
		//add_filter( 'mla_gallery_query_attributes', 'MLABuddyPressHooksExample::mla_gallery_query_attributes_filter', 10, 1 );
		//add_filter( 'mla_gallery_query_arguments', 'MLABuddyPressHooksExample::mla_gallery_query_arguments_filter', 10, 1 );
		add_action( 'mla_gallery_wp_query_object', 'MLABuddyPressHooksExample::mla_gallery_wp_query_object_action', 10, 1 );
		//add_filter( 'mla_gallery_final_content', 'MLABuddyPressHooksExample::mla_gallery_final_content_filter', 10, 1 );

		//add_filter( 'use_mla_gallery_style', 'MLABuddyPressHooksExample::use_mla_gallery_style_filter', 10, 2 );

		//add_filter( 'mla_gallery_style_values', 'MLABuddyPressHooksExample::mla_gallery_style_values_filter', 10, 1 );
		//add_filter( 'mla_gallery_style_template', 'MLABuddyPressHooksExample::mla_gallery_style_template_filter', 10, 1 );
		//add_filter( 'mla_gallery_style_parse', 'MLABuddyPressHooksExample::mla_gallery_style_parse_filter', 10, 3 );

		//add_filter( 'mla_gallery_open_values', 'MLABuddyPressHooksExample::mla_gallery_open_values_filter', 10, 1 );
		//add_filter( 'mla_gallery_open_template', 'MLABuddyPressHooksExample::mla_gallery_open_template_filter', 10, 1 );
		//add_filter( 'mla_gallery_open_parse', 'MLABuddyPressHooksExample::mla_gallery_open_parse_filter', 10, 3 );

		//add_filter( 'mla_gallery_style', 'MLABuddyPressHooksExample::mla_gallery_style_filter', 10, 5 );

		//add_filter( 'mla_gallery_row_open_values', 'MLABuddyPressHooksExample::mla_gallery_row_open_values_filter', 10, 1 );
		//add_filter( 'mla_gallery_row_open_template', 'MLABuddyPressHooksExample::mla_gallery_row_open_template_filter', 10, 1 );
		//add_filter( 'mla_gallery_row_open_parse', 'MLABuddyPressHooksExample::mla_gallery_row_open_parse_filter', 10, 3 );

		add_filter( 'mla_gallery_item_values', 'MLABuddyPressHooksExample::mla_gallery_item_values_filter', 10, 1 );
		//add_filter( 'mla_gallery_item_template', 'MLABuddyPressHooksExample::mla_gallery_item_template_filter', 10, 1 );
		//add_filter( 'mla_gallery_item_parse', 'MLABuddyPressHooksExample::mla_gallery_item_parse_filter', 10, 3 );

		//add_filter( 'mla_gallery_row_close_values', 'MLABuddyPressHooksExample::mla_gallery_row_close_values_filter', 10, 1 );
		//add_filter( 'mla_gallery_row_close_template', 'MLABuddyPressHooksExample::mla_gallery_row_close_template_filter', 10, 1 );
		//add_filter( 'mla_gallery_row_close_parse', 'MLABuddyPressHooksExample::mla_gallery_row_close_parse_filter', 10, 3 );

		//add_filter( 'mla_gallery_close_values', 'MLABuddyPressHooksExample::mla_gallery_close_values_filter', 10, 1 );
		//add_filter( 'mla_gallery_close_template', 'MLABuddyPressHooksExample::mla_gallery_close_template_filter', 10, 1 );
		//add_filter( 'mla_gallery_close_parse', 'MLABuddyPressHooksExample::mla_gallery_close_parse_filter', 10, 3 );
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
	 * MLA Gallery Raw (Display) Attributes
	 *
	 * This filter gives you an opportunity to record or modify the arguments passed in to the shortcode
	 * before they pass through the logic to handle the 'mla_page_parameter' and "request:" prefix processing.
	 *
	 * The $shortcode_attributes array is where you will find any of your own parameters that are coded in the
	 * shortcode, e.g., [mla_gallery my_parameter="my value"].
	 *
	 * @since 1.03
	 *
	 * @param	array	the raw shortcode parameters passed in to the shortcode
	 *
	 * @return	array	updated shortcode attributes
	 */
	public static function mla_gallery_raw_attributes_filter( $shortcode_attributes ) {
		/*
		 * Uncomment the error_log statements in any of the filters to see what's passed in
		 */
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_raw_attributes_filter $shortcode_attributes = ' . var_export( $shortcode_attributes, true ), 0 );

		/*
		 * Note that the global $post; object is available here and in all later filters.
		 * It contains the post/page on which the [mla_gallery] appears.
		 * Some [mla_gallery] invocations are not associated with a post/page; these will
		 * have a substitute $post object with $post->ID == 0.
		 */
		global $post;
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_raw_attributes_filter $post->ID = ' . var_export( $post->ID, true ), 0 );

		return $shortcode_attributes;
	} // mla_gallery_raw_attributes_filter

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
		 * Uncomment the error_log statements in any of the filters to see what's passed in
		 */
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_attributes_filter $shortcode_attributes = ' . var_export( $shortcode_attributes, true ), 0 );

		/*
		 * Save the attributes for use in the later filters
		 */
		self::$shortcode_attributes = $shortcode_attributes;

		return $shortcode_attributes;
	} // mla_gallery_attributes_filter

	/**
	 * Save the enclosed content
	 *
	 * @since 1.02
	 *
	 * @var	NULL|string
	 */
	private static $shortcode_content = NULL;

	/**
	 * MLA Gallery Enclosed Content, initial filter
	 *
	 * This filter gives you an opportunity to record or modify the content enclosed by the shortcode
	 * when the [mla_gallery]content[/mla_gallery] form is used.
	 * This initial filter is called just after the 'mla_gallery_attributes' filter above.
	 *
	 * @since 1.02
	 *
	 * @param	NULL|string	content enclosed by the shortcode, if any
	 * @param	array	the shortcode parameters passed in to the shortcode
	 *
	 * @return	array	updated shortcode content
	 */
	public static function mla_gallery_initial_content_filter( $shortcode_content, $shortcode_attributes ) {
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_initial_content_filter $shortcode_content = ' . var_export( $shortcode_content, true ), 0 );
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_initial_content_filter $shortcode_attributes = ' . var_export( $shortcode_attributes, true ), 0 );

		/*
		 * Save the attributes for use in the later filters
		 */
		self::$shortcode_content = $shortcode_content;

		return $shortcode_content;
	} // mla_gallery_initial_content_filter

	/**
	 * Save the shortcode arguments
	 *
	 * @since 1.00
	 *
	 * @var	array
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
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_arguments_filter $all_display_parameters = ' . var_export( $all_display_parameters, true ), 0 );

		self::$all_display_parameters = $all_display_parameters;
		return $all_display_parameters;
	} // mla_gallery_arguments_filter

	/**
	 * Save the query attributes
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $query_attributes = array();

	/**
	 * MLA Gallery Query Attributes
	 *
	 * This filter gives you an opportunity to record or modify the arguments passed in to the shortcode
	 * before they are merged with the default arguments used to select the attachments for the gallery.
	 *
	 * The query attributes passed in to this filter are the same as those passed through the
	 * "MLA Gallery (Display) Attributes" filter above. This filter is provided so you can modify
	 * the data selection attributes without disturbing the attributes used for gallery display.
	 *
	 * @since 1.00
	 *
	 * @param	array	the shortcode parameters passed in to the shortcode
	 *
	 * @return	array	updated shortcode attributes
	 */
	public static function mla_gallery_query_attributes_filter( $query_attributes ) {
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_query_attributes_filter $query_attributes = ' . var_export( $query_attributes, true ), 0 );

		self::$query_attributes = $query_attributes;
		return $query_attributes;
	} // mla_gallery_query_attributes_filter

	/**
	 * Save the query arguments
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $all_query_parameters = array();

	/**
	 * MLA Gallery Query Arguments
	 *
	 * This filter gives you an opportunity to record or modify the attachment query arguments
	 * after the shortcode attributes are merged with the default arguments.
	 *
	 * @since 1.00
	 *
	 * @param	array	shortcode arguments merged with attachment selection defaults, so every possible parameter is present
	 *
	 * @return	array	updated attachment query arguments
	 */
	public static function mla_gallery_query_arguments_filter( $all_query_parameters ) {
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_query_arguments_filter $all_query_parameters = ' . var_export( $all_query_parameters, true ), 0 );

		self::$all_query_parameters = $all_query_parameters;

		return $all_query_parameters;
	} // mla_gallery_query_arguments_filter

	/**
	 * Save some of the WP_Query object properties
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $wp_query_properties = array();

	/**
	 * MLA Gallery WP Query Object
	 *
	 * This action gives you an opportunity (read-only) to record anything you need from the WP_Query object used
	 * to select the attachments for gallery display. This is the ONLY point at which the WP_Query object is defined.
	 *
	 * @since 1.00
	 * @uses MLAShortcodes::$mla_gallery_wp_query_object
	 *
	 * @param	array	query arguments passed to WP_Query->query
	 *
	 * @return	void	actions never return anything
	 */
	public static function mla_gallery_wp_query_object_action( $query_arguments ) {
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_wp_query_object_action $query_arguments = ' . var_export( $query_arguments, true ), 0 );

		self::$wp_query_properties = array();
		self::$wp_query_properties ['post_count'] = MLAShortcodes::$mla_gallery_wp_query_object->post_count;

		if ( empty( self::$shortcode_attributes['buddypress_urls'] ) ) {
			return; // Don't need custom URLs
		}

		if ( 0 == self::$wp_query_properties ['post_count'] ) {
			return; // Empty gallery - nothing to do
		}

		global $wpdb;

		// Assemble the WordPress attachment IDs
		$post_info = array();
		foreach( MLAShortcodes::$mla_gallery_wp_query_object->posts as $value ) {
			$post_info[ $value->ID ] = $value->ID;
		}

		// Build an array of SQL clauses, then run the query
		$query = array();
		$query_parameters = array();

		$query[] = "SELECT rtm.id, rtm.media_id, rtm.media_author, rtm.media_type, rtm.cover_art, u.user_nicename FROM {$wpdb->prefix}rt_rtm_media AS rtm";
		$query[] = "LEFT JOIN {$wpdb->users} as u";
		$query[] = "ON (rtm.media_author = u.ID)";

		$placeholders = array();
		foreach ( $post_info as $value ) {
			$placeholders[] = '%s';
			$query_parameters[] = $value;
		}
		$query[] = 'WHERE ( rtm.media_id IN (' . join( ',', $placeholders ) . ') )';

		$query =  join(' ', $query);
		$results = $wpdb->get_results( $wpdb->prepare( $query, $query_parameters ) );

		// Save the values, indexed by WordPress attachment ID, for use in the item filter
		$post_info = array();
		if ( is_array( $results ) ) {
			foreach ( $results as $value ) {
				$post_info[ $value->media_id ] = $value;
			}
		}

		//error_log( 'MLABuddyPressHooksExample::mla_gallery_wp_query_object_action $post_info = ' . var_export( $post_info, true ), 0 );
		self::$wp_query_properties ['post_info'] = $post_info;

		/*
		 * Unlike Filters, Actions never return anything
		 */
		return;
	} // mla_gallery_wp_query_object_action

	/**
	 * MLA Gallery Enclosed Content, final filter
	 *
	 * This filter gives you an opportunity to record or modify the content enclosed by the shortcode
	 * when the [mla_gallery]content[/mla_gallery] form is used.
	 * This final filter is called just after the WP_query and before control is passed
	 * to the alternate gallery shortcode.
	 *
	 * @since 1.02
	 *
	 * @param	NULL|string	content enclosed by the shortcode, if any
	 *
	 * @return	array	updated shortcode content
	 */
	public static function mla_gallery_final_content_filter( $shortcode_content ) {
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_final_content_filter $shortcode_content = ' . var_export( $shortcode_content, true ), 0 );

		return $shortcode_content;
	} // mla_gallery_final_content_filter

	/**
	 * Use MLA Gallery Style
	 *
	 * You can use this filter to allow or suppress the inclusion of CSS styles in the
	 * gallery output. Return 'true' to allow the styles, false to suppress them. You can also
	 * suppress styles by returning an empty string from the mla_gallery_style_parse_filter below.
	 *
	 * @since 1.00
	 *
	 * @param	boolean	true unless the mla_style parameter is "none"
	 * @param	string	value of the mla_style parameter
	 *
	 * @return	boolean	true to fetch and parse the style template, false to leave it empty
	 */
	public static function use_mla_gallery_style_filter( $use_style_template, $style_template_name ) {
		//error_log( 'MLABuddyPressHooksExample::use_mla_gallery_style_filter $use_style_template = ' . var_export( $use_style_template, true ), 0 );
		//error_log( 'MLABuddyPressHooksExample::use_mla_gallery_style_filter $style_template_name = ' . var_export( $style_template_name, true ), 0 );

		/*
		 * Filters must return the first argument passed in, unchanged or updated
		 */
		return $use_style_template;
	} // use_mla_gallery_style_filter

	/**
	 * MLA Gallery Style Values
	 *
	 * The "Values" series of filters gives you a chance to modify the substitution parameter values
	 * before they are used to complete the associated template (in the corresponding "Parse" filter).
	 * It is called just before the values are used to parse the associated template.
	 * You can add, change or delete parameters as needed.
	 *
	 * @since 1.00
	 *
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated substitution parameter name => value pairs
	 */
	public static function mla_gallery_style_values_filter( $style_values ) {
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_style_values_filter $style_values = ' . var_export( $style_values, true ), 0 );

		/*
		 * You also have access to the PHP Super Globals, e.g., $_REQUEST, $_SERVER
		 */
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_style_values_filter $_REQUEST = ' . var_export( $_REQUEST, true ), 0 );
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_style_values_filter $_SERVER[ REQUEST_URI ] = ' . var_export( $_SERVER['REQUEST_URI'], true ), 0 );

		/*
		 * You can use the WordPress globals like $wp_query, $wpdb and $table_prefix as well.
		 * Note that $wp_query contains values for the post/page query, NOT the [mla_gallery] query.
		 */
		global $wp_query;
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_style_values_filter $wp_query->query = ' . var_export( $wp_query->query, true ), 0 );

		return $style_values;
	} // mla_gallery_style_values_filter

	/**
	 * MLA Gallery Style Template
	 *
	 * The "Template" series of filters gives you a chance to modify the template value before
	 * it is used to generate the HTML markup (in the corresponding "Parse" filter).
	 * It is called just before the template is used to generate the markup.
	 * You can modify the template as needed.
	 *
	 * @since 1.00
	 *
	 * @param	string	template used to generate the HTML markup
	 *
	 * @return	string	updated template
	 */
	public static function mla_gallery_style_template_filter( $style_template ) {
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_style_template_filter $style_template = ' . var_export( $style_template, true ), 0 );

		return $style_template;
	} // mla_gallery_style_template_filter

	/**
	 * MLA Gallery Style Parse
	 *
	 * The "Parse" series of filters gives you a chance to modify or replace the HTML markup
	 * that will be added to the [mla_gallery] output. It is called just after the values array
	 * (updated in the corresponding "Values" filter) is combined (parsed) with the template.
	 * You can modify the HTML markup already prepared or start over with the template and the
	 * substitution values.
	 *
	 * @since 1.00
	 *
	 * @param	string	HTML markup returned by the template parser
	 * @param	string	template used to generate the HTML markup
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated HTML markup for gallery output
	 */
	public static function mla_gallery_style_parse_filter( $html_markup, $style_template, $style_values ) {
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_style_parse_filter $html_markup = ' . var_export( $html_markup, true ), 0 );
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_style_parse_filter $style_template = ' . var_export( $style_template, true ), 0 );
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_style_parse_filter $style_values = ' . var_export( $style_values, true ), 0 );

		return $html_markup;
	} // mla_gallery_style_parse_filter

	/**
	 * MLA Gallery Open Values
	 *
	 * Note: The $markup_values array is shared among the open, row open, row close and close functions.
	 * It is also used to initialize the $item_values array.
	 *
	 * @since 1.00
	 *
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated substitution parameter name => value pairs
	 */
	public static function mla_gallery_open_values_filter( $markup_values ) {
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_open_values_filter $markup_values = ' . var_export( $markup_values, true ), 0 );

		return $markup_values;
	} // mla_gallery_open_values_filter

	/**
	 * MLA Gallery Open Template
	 *
	 * @since 1.00
	 *
	 * @param	string	template used to generate the HTML markup
	 *
	 * @return	string	updated template
	 */
	public static function mla_gallery_open_template_filter( $open_template ) {
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_open_template_filter $open_template = ' . var_export( $open_template, true ), 0 );

		return $open_template;
	} // mla_gallery_open_template_filter

	/**
	 * MLA Gallery Open Parse
	 *
	 * @since 1.00
	 *
	 * @param	string	HTML markup returned by the template parser
	 * @param	string	template used to generate the HTML markup
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated HTML markup for gallery output
	 */
	public static function mla_gallery_open_parse_filter( $html_markup, $open_template, $markup_values ) {
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_open_parse_filter $html_markup = ' . var_export( $html_markup, true ), 0 );
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_open_parse_filter $open_template = ' . var_export( $open_template, true ), 0 );
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_open_parse_filter $markup_values = ' . var_export( $markup_values, true ), 0 );

		return $html_markup;
	} // mla_gallery_open_parse_filter

	/**
	 * MLA Gallery Style
	 *
	 * This is an old filter retained for compatibility with earlier MLA versions.
	 * You will probably find the "Values" and "Parse" filters more useful.
	 *
	 * @since 1.00
	 *
	 * @param	string	HTML markup for "gallery style" and "gallery open", combined
	 * @param	array	parameter_name => parameter_value pairs for gallery style
	 * @param	array	parameter_name => parameter_value pairs for gallery open
	 * @param	string	template used to generate the HTML markup for gallery style
	 * @param	string	template used to generate the HTML markup for gallery open
	 *
	 * @return	array	updated HTML markup for "gallery style" and "gallery open" output
	 */
	public static function mla_gallery_style_filter( $html_markup, $style_values, $open_values, $style_template, $open_template ) {
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_style_filter $html_markup = ' . var_export( $html_markup, true ), 0 );
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_style_filter $style_values = ' . var_export( $style_values, true ), 0 );
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_style_filter $open_values = ' . var_export( $open_values, true ), 0 );
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_style_filter $style_template = ' . var_export( $style_template, true ), 0 );
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_style_filter $open_template = ' . var_export( $open_template, true ), 0 );

		return $html_markup;
	} // mla_gallery_style_filter

	/**
	 * MLA Gallery Row Open Values
	 *
	 * @since 1.00
	 *
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated substitution parameter name => value pairs
	 */
	public static function mla_gallery_row_open_values_filter( $markup_values ) {
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_row_open_values_filter $markup_values = ' . var_export( $markup_values, true ), 0 );

		return $markup_values;
	} // mla_gallery_row_open_values_filter

	/**
	 * MLA Gallery Row Open Template
	 *
	 * @since 1.00
	 *
	 * @param	string	template used to generate the HTML markup
	 *
	 * @return	string	updated template
	 */
	public static function mla_gallery_row_open_template_filter( $row_open_template ) {
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_row_open_template_filter $row_open_template = ' . var_export( $row_open_template, true ), 0 );

		return $row_open_template;
	} // mla_gallery_row_open_template_filter

	/**
	 * MLA Gallery Row Open Parse
	 *
	 * @since 1.00
	 *
	 * @param	string	HTML markup returned by the template parser
	 * @param	string	template used to generate the HTML markup
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated HTML markup for gallery output
	 */
	public static function mla_gallery_row_open_parse_filter( $html_markup, $row_open_template, $markup_values ) {
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_row_open_parse_filter $html_markup = ' . var_export( $html_markup, true ), 0 );
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_row_open_parse_filter $row_open_template = ' . var_export( $row_open_template, true ), 0 );
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_row_open_parse_filter $markup_values = ' . var_export( $markup_values, true ), 0 );

		return $html_markup;
	} // mla_gallery_row_open_parse_filter

/* FROM buddypress-media rt-template-functions.php 

function rtmedia_get_cover_art_src( $id ) {
	$model     = new RTMediaModel();
	$media     = $model->get( array( "id" => $id ) );
	$cover_art = $media[ 0 ]->cover_art;
	if ( $cover_art != "" ){
		if ( is_numeric( $cover_art ) ){
			$thumbnail_info = wp_get_attachment_image_src( $cover_art, 'full' );

			return $thumbnail_info[ 0 ];
		} else {
			return $cover_art;
		}
	} else {
		return false;
	}
}
 */

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
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_item_values_filter $item_values = ' . var_export( $item_values, true ), 0 );

		/*
		 * We use a shortcode parameter of our own to apply our filters on a gallery-by-gallery basis,
		 * leaving other [mla_gallery] instances untouched. If the "my_filter" parameter is not present,
		 * we have nothing to do.
		 */		
		if ( ! isset( self::$shortcode_attributes['buddypress_urls'] ) ) {
			return $item_values; // leave them unchanged
		}

		if ( isset( self::$wp_query_properties ['post_info'][ $item_values['attachment_ID'] ] ) ) {
			$post_info = self::$wp_query_properties ['post_info'][ $item_values['attachment_ID'] ];
		} else {
			return $item_values; // no matching rtMedia item
		}

		$new_url = $item_values['site_url'] . '/members/' . $post_info->user_nicename . '/media/' . $post_info->id . '/';
		$new_link = str_replace( $item_values['link_url'], $new_url, $item_values['link'] );

		// Add the "media thumbnail", if desired and present. Note that the size is fixed at 150x150 pixels.		
		if ( 'cover' == strtolower( trim( self::$shortcode_attributes['buddypress_urls'] ) ) ) {
			// Supply a default image for video and music media
			if ( empty( $post_info->cover_art ) && defined( 'RTMEDIA_URL' ) ) {
				switch ( $post_info->media_type ) {
					case 'video':
						$post_info->cover_art = RTMEDIA_URL . 'app/assets/img/video_thumb.png';
						break;
					case 'music':
						$post_info->cover_art = RTMEDIA_URL . 'app/assets/img/audio_thumb.png';
						break;
				}
			}

			if ( ! empty( $post_info->cover_art ) ) {
				if ( is_numeric( $post_info->cover_art ) ){
					$thumbnail_info = wp_get_attachment_image_src( $post_info->cover_art, 'thumbnail' );

					if ( false === $thumbnail_info ) {
						$thumbnail_info = wp_get_attachment_image_src( $post_info->cover_art, 'full' );
					}

					if ( is_array( $thumbnail_info ) ) {
						$post_info->cover_art = $thumbnail_info[ 0 ];
					} else {
						$post_info->cover_art = '';
					}
				}

				if ( ! empty( $post_info->cover_art ) ) {
					$new_thumbnail = '<img width="150" height="150" src="' . $post_info->cover_art . '" class="attachment-thumbnail" alt="' . $item_values['thumbnail_content'] . '" />';
					$new_link = str_replace( $item_values['thumbnail_content'] . '</a>', $new_thumbnail . '</a>', $new_link );

					$item_values['thumbnail_content'] = $new_thumbnail;
					$item_values['thumbnail_width'] = '150';
					$item_values['thumbnail_height'] = '150';
					$item_values['thumbnail_url'] = $post_info->cover_art;
				}
			} // has cover art
		} // use cover art

		$item_values['link_url'] = $new_url;
		$item_values['link'] = $new_link;

		return $item_values;
	} // mla_gallery_item_values_filter

	/**
	 * MLA Gallery Item Template
	 *
	 * @since 1.00
	 *
	 * @param	string	template used to generate the HTML markup
	 *
	 * @return	string	updated template
	 */
	public static function mla_gallery_item_template_filter( $item_template ) {
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_item_template_filter $item_template = ' . var_export( $item_template, true ), 0 );

		return $item_template;
	} // mla_gallery_item_template_filter

	/**
	 * MLA Gallery Item Parse
	 *
	 * @since 1.00
	 *
	 * @param	string	HTML markup returned by the template parser
	 * @param	string	template used to generate the HTML markup
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated HTML markup for gallery output
	 */
	public static function mla_gallery_item_parse_filter( $html_markup, $item_template, $item_values ) {
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_item_parse_filter $html_markup = ' . var_export( $html_markup, true ), 0 );
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_item_parse_filter $item_template = ' . var_export( $item_template, true ), 0 );
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_item_parse_filter $item_values = ' . var_export( $item_values, true ), 0 );

		return $html_markup;
	} // mla_gallery_item_parse_filter

	/**
	 * MLA Gallery Row Close Values
	 *
	 * @since 1.00
	 *
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated substitution parameter name => value pairs
	 */
	public static function mla_gallery_row_close_values_filter( $markup_values ) {
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_row_close_values_filter $markup_values = ' . var_export( $markup_values, true ), 0 );

		return $markup_values;
	} // mla_gallery_row_close_values_filter

	/**
	 * MLA Gallery Row Close Template
	 *
	 * @since 1.00
	 *
	 * @param	string	template used to generate the HTML markup
	 *
	 * @return	string	updated template
	 */
	public static function mla_gallery_row_close_template_filter( $row_close_template ) {
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_row_close_template_filter $row_close_template = ' . var_export( $row_close_template, true ), 0 );

		return $row_close_template;
	} // mla_gallery_row_close_template_filter

	/**
	 * MLA Gallery Row Close Parse
	 *
	 * @since 1.00
	 *
	 * @param	string	HTML markup returned by the template parser
	 * @param	string	template used to generate the HTML markup
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated HTML markup for gallery output
	 */
	public static function mla_gallery_row_close_parse_filter( $html_markup, $row_close_template, $markup_values ) {
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_row_close_parse_filter $html_markup = ' . var_export( $html_markup, true ), 0 );
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_row_close_parse_filter $row_close_template = ' . var_export( $row_close_template, true ), 0 );
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_row_close_parse_filter $markup_values = ' . var_export( $markup_values, true ), 0 );

		return $html_markup;
	} // mla_gallery_row_close_parse_filter

	/**
	 * MLA Gallery Close Values
	 *
	 * @since 1.00
	 *
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated substitution parameter name => value pairs
	 */
	public static function mla_gallery_close_values_filter( $markup_values ) {
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_close_values_filter $markup_values = ' . var_export( $markup_values, true ), 0 );

		return $markup_values;
	} // mla_gallery_close_values_filter

	/**
	 * MLA Gallery Close Template
	 *
	 * @since 1.00
	 *
	 * @param	string	template used to generate the HTML markup
	 *
	 * @return	string	updated template
	 */
	public static function mla_gallery_close_template_filter( $close_template ) {
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_close_template_filter $close_template = ' . var_export( $close_template, true ), 0 );

		return $close_template;
	} // mla_gallery_close_template_filter

	/**
	 * MLA Gallery Close Parse
	 *
	 * @since 1.00
	 *
	 * @param	string	HTML markup returned by the template parser
	 * @param	string	template used to generate the HTML markup
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated HTML markup for gallery output
	 */
	public static function mla_gallery_close_parse_filter( $html_markup, $close_template, $markup_values ) {
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_close_parse_filter $html_markup = ' . var_export( $html_markup, true ), 0 );
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_close_parse_filter $close_template = ' . var_export( $close_template, true ), 0 );
		//error_log( 'MLABuddyPressHooksExample::mla_gallery_close_parse_filter $markup_values = ' . var_export( $markup_values, true ), 0 );

		return $html_markup;
	} // mla_gallery_close_parse_filter

} // Class MLABuddyPressHooksExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLABuddyPressHooksExample::initialize');
?>
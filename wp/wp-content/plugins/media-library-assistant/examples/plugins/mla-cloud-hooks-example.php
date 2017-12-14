<?php
/**
 * Provides an example of hooking the filters provided by the [mla_tag_cloud] shortcode
 *
 * In this example, the CSS style attributes for each cloud item are modified to include a "color" attribute,
 * giving each term a color related to its associated count. The example documents ALL the filters
 * available in the [mla_tag_cloud] shortcode.
 *
 * @package MLA Tag Cloud Hooks Example
 * @version 1.02
 */

/*
Plugin Name: MLA Tag Cloud Hooks Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Provides an example of hooking the filters provided by the [mla_tag_cloud] shortcode
Author: David Lingren
Version: 1.02
Author URI: http://fairtradejudaica.org/our-story/staff/

Copyright 2013-2016 David Lingren

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
 * Class MLA Tag Cloud Hooks Example hooks all of the filters provided by the [mla_tag_cloud] shortcode
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding everything
 * else inside a class means this is the only name you have to worry about.
 *
 * @package MLA Tag Cloud Hooks Example
 * @since 1.00
 */
class MLATagCloudHooksExample {
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
		 * $tag - name of the hook you're filtering; defined by [mla_tag_cloud]
		 * $function_to_add - function to be called when [mla_tag_cloud] applies the filter
		 * $priority - default 10; lower runs earlier, higher runs later
		 * $accepted_args - number of arguments your function accepts
		 *
		 * Comment out the filters you don't need; save them for future use
		 */
		add_filter( 'mla_tag_cloud_raw_attributes', 'MLATagCloudHooksExample::mla_tag_cloud_raw_attributes_filter', 10, 1 );
		add_filter( 'mla_tag_cloud_attributes', 'MLATagCloudHooksExample::mla_tag_cloud_attributes_filter', 10, 1 );
		add_filter( 'mla_tag_cloud_arguments', 'MLATagCloudHooksExample::mla_tag_cloud_arguments_filter', 10, 1 );
		add_filter( 'mla_get_terms_query_attributes', 'MLATagCloudHooksExample::mla_get_terms_query_attributes_filter', 10, 1 );
		add_filter( 'mla_get_terms_query_arguments', 'MLATagCloudHooksExample::mla_get_terms_query_arguments_filter', 10, 1 );
		add_filter( 'mla_get_terms_clauses', 'MLATagCloudHooksExample::mla_get_terms_clauses_filter', 10, 1 );
		add_filter( 'mla_get_terms_query_results', 'MLATagCloudHooksExample::mla_get_terms_query_results_filter', 10, 1 );

		add_filter( 'mla_tag_cloud_scale', 'MLATagCloudHooksExample::mla_tag_cloud_scale_filter', 10, 4 );

		add_filter( 'use_mla_tag_cloud_style', 'MLATagCloudHooksExample::use_mla_tag_cloud_style_filter', 10, 2 );

		add_filter( 'mla_tag_cloud_style_values', 'MLATagCloudHooksExample::mla_tag_cloud_style_values_filter', 10, 1 );
		add_filter( 'mla_tag_cloud_style_template', 'MLATagCloudHooksExample::mla_tag_cloud_style_template_filter', 10, 1 );
		add_filter( 'mla_tag_cloud_style_parse', 'MLATagCloudHooksExample::mla_tag_cloud_style_parse_filter', 10, 3 );

		add_filter( 'mla_tag_cloud_open_values', 'MLATagCloudHooksExample::mla_tag_cloud_open_values_filter', 10, 1 );
		add_filter( 'mla_tag_cloud_open_template', 'MLATagCloudHooksExample::mla_tag_cloud_open_template_filter', 10, 1 );
		add_filter( 'mla_tag_cloud_open_parse', 'MLATagCloudHooksExample::mla_tag_cloud_open_parse_filter', 10, 3 );

		add_filter( 'mla_tag_cloud_row_open_values', 'MLATagCloudHooksExample::mla_tag_cloud_row_open_values_filter', 10, 1 );
		add_filter( 'mla_tag_cloud_row_open_template', 'MLATagCloudHooksExample::mla_tag_cloud_row_open_template_filter', 10, 1 );
		add_filter( 'mla_tag_cloud_row_open_parse', 'MLATagCloudHooksExample::mla_tag_cloud_row_open_parse_filter', 10, 3 );

		add_filter( 'mla_tag_cloud_item_values', 'MLATagCloudHooksExample::mla_tag_cloud_item_values_filter', 10, 1 );
		add_filter( 'mla_tag_cloud_item_template', 'MLATagCloudHooksExample::mla_tag_cloud_item_template_filter', 10, 1 );
		add_filter( 'mla_tag_cloud_item_parse', 'MLATagCloudHooksExample::mla_tag_cloud_item_parse_filter', 10, 3 );

		add_filter( 'mla_tag_cloud_row_close_values', 'MLATagCloudHooksExample::mla_tag_cloud_row_close_values_filter', 10, 1 );
		add_filter( 'mla_tag_cloud_row_close_template', 'MLATagCloudHooksExample::mla_tag_cloud_row_close_template_filter', 10, 1 );
		add_filter( 'mla_tag_cloud_row_close_parse', 'MLATagCloudHooksExample::mla_tag_cloud_row_close_parse_filter', 10, 3 );

		add_filter( 'mla_tag_cloud_close_values', 'MLATagCloudHooksExample::mla_tag_cloud_close_values_filter', 10, 1 );
		add_filter( 'mla_tag_cloud_close_template', 'MLATagCloudHooksExample::mla_tag_cloud_close_template_filter', 10, 1 );
		add_filter( 'mla_tag_cloud_close_parse', 'MLATagCloudHooksExample::mla_tag_cloud_close_parse_filter', 10, 3 );
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
	 * MLA Tag Cloud Raw (Display) Attributes
	 *
	 * This filter gives you an opportunity to record or modify the arguments passed in to the shortcode
	 * before they pass through the logic to handle the 'mla_page_parameter' and "request:" prefix processing.
	 *
	 * The $shortcode_attributes array is where you will find any of your own parameters that are coded in the
	 * shortcode, e.g., [mla_tag_cloud my_parameter="my value"].
	 *
	 * @since 1.02
	 *
	 * @param	array	the raw shortcode parameters passed in to the shortcode
	 *
	 * @return	array	updated shortcode attributes
	 */
	public static function mla_tag_cloud_raw_attributes_filter( $shortcode_attributes ) {
		/*
		 * Uncomment the error_log statements in any of the filters to see what's passed in
		 */
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_raw_attributes_filter $shortcode_attributes = ' . var_export( $shortcode_attributes, true ), 0 );

		/*
		 * Note that the global $post; object is available here and in all later filters.
		 * It contains the post/page on which the [mla_tag_cloud] appears.
		 * Some [mla_tag_cloud] invocations are not associated with a post/page; these will
		 * have a substitute $post object with $post->ID == 0.
		 */
		global $post;
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_raw_attributes_filter $post->ID = ' . var_export( $post->ID, true ), 0 );

		return $shortcode_attributes;
	} // mla_tag_cloud_raw_attributes_filter

	/**
	 * MLA Tag Cloud (Display) Attributes
	 *
	 * This filter gives you an opportunity to record or modify the arguments passed in to the shortcode
	 * before they are merged with the default arguments used for the gallery display.
	 *
	 * The $shortcode_attributes array is where you will find any of your own parameters that are coded in the
	 * shortcode, e.g., [mla_tag_cloud my_parameter="my value"].
	 *
	 * @since 1.00
	 * @uses MLATagCloudHooksExample::$shortcode_attributes
	 *
	 * @param	array	the shortcode parameters passed in to the shortcode
	 *
	 * @return	array	updated shortcode attributes
	 */
	public static function mla_tag_cloud_attributes_filter( $shortcode_attributes ) {
		/*
		 * Uncomment the error_log statements in any of the filters to see what's passed in
		 */
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_attributes_filter $shortcode_attributes = ' . var_export( $shortcode_attributes, true ), 0 );

		/*
		 * Save the attributes for use in the later filters
		 */
		self::$shortcode_attributes = $shortcode_attributes;

		/*
		 * Filters must return the first argument passed in, unchanged or updated
		 */
		return $shortcode_attributes;
	} // mla_tag_cloud_attributes_filter

	/**
	 * Save the shortcode arguments
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $all_display_parameters = array();

	/**
	 * MLA Tag Cloud (Display) Arguments
	 *
	 * This filter gives you an opportunity to record or modify the gallery display arguments
	 * after the shortcode attributes are merged with the default arguments.
	 *
	 * Note that the values in this array are input or default values, not the final computed values
	 * used for the gallery display.  The computed values are in the $style_values, $markup_values and
	 * $item_values arrays passed to later filters below.
	 *
	 * @since 1.00
	 * @uses MLATagCloudHooksExample::$all_display_parameters
	 *
	 * @param	array	shortcode arguments merged with gallery display defaults, so every possible parameter is present
	 *
	 * @return	array	updated gallery display arguments
	 */
	public static function mla_tag_cloud_arguments_filter( $all_display_parameters ) {
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_arguments_filter $all_display_parameters = ' . var_export( $all_display_parameters, true ), 0 );

		self::$all_display_parameters = $all_display_parameters;
		return $all_display_parameters;
	} // mla_tag_cloud_arguments_filter

	/**
	 * Save the query attributes
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $query_attributes = array();

	/**
	 * MLA Tag Cloud Query Attributes
	 *
	 * This filter gives you an opportunity to record or modify the arguments passed in to the shortcode
	 * before they are merged with the default arguments used to select the attachments for the gallery.
	 *
	 * The query attributes passed in to this filter are the same as those passed through the
	 * "MLA Tag Cloud (Display) Attributes" filter above. This filter is provided so you can modify
	 * the data selection attributes without disturbing the attributes used for gallery display.
	 *
	 * @since 1.00
	 * @uses MLATagCloudHooksExample::$query_attributes
	 *
	 * @param	array	the shortcode parameters passed in to the shortcode
	 *
	 * @return	array	updated shortcode attributes
	 */
	public static function mla_get_terms_query_attributes_filter( $query_attributes ) {
		//error_log( 'MLATagCloudHooksExample::mla_get_terms_query_attributes_filter $query_attributes = ' . var_export( $query_attributes, true ), 0 );

		self::$query_attributes = $query_attributes;
		return $query_attributes;
	} // mla_get_terms_query_attributes_filter

	/**
	 * Save the query arguments
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $all_query_parameters = array();

	/**
	 * MLA Tag Cloud Query Arguments
	 *
	 * This filter gives you an opportunity to record or modify the attachment query arguments
	 * after the shortcode attributes are merged with the default arguments.
	 *
	 * @since 1.00
	 * @uses MLATagCloudHooksExample::$all_query_parameters
	 *
	 * @param	array	shortcode arguments merged with attachment selection defaults, so every possible parameter is present
	 *
	 * @return	array	updated attachment query arguments
	 */
	public static function mla_get_terms_query_arguments_filter( $all_query_parameters ) {
		//error_log( 'MLATagCloudHooksExample::mla_get_terms_query_arguments_filter $all_query_parameters = ' . var_export( $all_query_parameters, true ), 0 );

		self::$all_query_parameters = $all_query_parameters;
		return $all_query_parameters;
	} // mla_get_terms_query_arguments_filter

	/**
	 * MLA Tag Cloud Query Clauses
	 *
	 * This action gives you a final opportunity to inspect or modify
	 * the SQL clauses for the data selection process.
	 *
	 * @since 1.01
	 *
	 * @param	array	SQL clauses ( 'fields', 'join', 'where', 'order', 'orderby', 'limits' )
	 *
	 * @return	array	updated SQL clauses
	 */
	public static function mla_get_terms_clauses_filter( $clauses ) {
		//error_log( 'MLATagCloudHooksExample::mla_get_terms_clauses_filter $clauses = ' . var_export( $clauses, true ), 0 );

		return $clauses;
	} // mla_get_terms_clauses_filter

	/**
	 * MLA Tag Cloud Query Results
	 *
	 * This action gives you an opportunity to inspect, save, modify, reorder, etc.
	 * the array of tag objects returned from the data selection process.
	 *
	 * @since 1.00
	 *
	 * @param	array	tag objects
	 *
	 * @return	array	updated tag objects
	 */
	public static function mla_get_terms_query_results_filter( $tag_objects ) {
		//error_log( 'MLATagCloudHooksExample::mla_get_terms_query_results_filter $tag_objects = ' . var_export( $tag_objects, true ), 0 );

		return $tag_objects;
	} // mla_get_terms_query_results_filter

	/**
	 * MLA Tag Cloud scale filter
	 *
	 * This filter gives you an opportunity to record or modify the scaled count value,
	 * for determining the font size assigned to each cloud term.
	 * The default fomula for scaling the count is round(log10($tag->count + 1) * 100).
	 * This filter is called once for each filter as the item-specific substitution parameters
	 * are calculated.
	 *
	 * @since 1.00
	 *
	 * @param	float	default scaled count for the tag
	 * @param	array	the shortcode parameters passed in to the shortcode
	 * @param	array	shortcode arguments merged with gallery display defaults, so every possible parameter is present
	 * @param	object	tag object for the current item
	 *
	 * @return	array	updated scaled count
	 */
	public static function mla_tag_cloud_scale_filter( $scaled_count, $shortcode_attributes, $all_display_parameters, $tag ) {
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_scale_filter $scaled_count = ' . var_export( $scaled_count, true ), 0 );
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_scale_filter $shortcode_attributes = ' . var_export( $shortcode_attributes, true ), 0 );
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_scale_filter $all_display_parameters = ' . var_export( $all_display_parameters, true ), 0 );
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_scale_filter $tag = ' . var_export( $tag, true ), 0 );

		return $scaled_count;
	} // mla_tag_cloud_scale_filter

	/**
	 * Use MLA Tag Cloud Style
	 *
	 * You can use this filter to allow or suppress the inclusion of CSS styles in the
	 * gallery output. Return 'true' to allow the styles, false to suppress them. You can also
	 * suppress styles by returning an empty string from the mla_tag_cloud_style_parse_filter below.
	 *
	 * @since 1.00
	 *
	 * @param	boolean	true unless the mla_style parameter is "none"
	 * @param	string	value of the mla_style parameter
	 *
	 * @return	boolean	true to fetch and parse the style template, false to leave it empty
	 */
	public static function use_mla_tag_cloud_style_filter( $use_style_template, $style_template_name ) {
		//error_log( 'MLATagCloudHooksExample::use_mla_tag_cloud_style_filter $use_style_template = ' . var_export( $use_style_template, true ), 0 );
		//error_log( 'MLATagCloudHooksExample::use_mla_tag_cloud_style_filter $style_template_name = ' . var_export( $style_template_name, true ), 0 );

		return $use_style_template;
	} // use_mla_tag_cloud_style_filter

	/**
	 * MLA Tag Cloud Style Values
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
	public static function mla_tag_cloud_style_values_filter( $style_values ) {
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_style_values_filter $style_values = ' . var_export( $style_values, true ), 0 );

		/*
		 * You also have access to the PHP Super Globals, e.g., $_REQUEST, $_SERVER
		 */
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_style_values_filter $_REQUEST = ' . var_export( $_REQUEST, true ), 0 );
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_style_values_filter $_SERVER[ REQUEST_URI ] = ' . var_export( $_SERVER['REQUEST_URI'], true ), 0 );

		/*
		 * You can use the WordPress globals like $wp_query, $wpdb and $table_prefix as well.
		 * Note that $wp_query contains values for the post/page query, NOT the [mla_tag_cloud] query.
		 */
		global $wp_query;
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_style_values_filter $wp_query->query = ' . var_export( $wp_query->query, true ), 0 );

		return $style_values;
	} // mla_tag_cloud_style_values_filter

	/**
	 * MLA Tag Cloud Style Template
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
	public static function mla_tag_cloud_style_template_filter( $style_template ) {
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_style_template_filter $style_template = ' . var_export( $style_template, true ), 0 );

		return $style_template;
	} // mla_tag_cloud_style_template_filter

	/**
	 * MLA Tag Cloud Style Parse
	 *
	 * The "Parse" series of filters gives you a chance to modify or replace the HTML markup
	 * that will be added to the [mla_tag_cloud] output. It is called just after the values array
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
	public static function mla_tag_cloud_style_parse_filter( $html_markup, $style_template, $style_values ) {
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_style_parse_filter $html_markup = ' . var_export( $html_markup, true ), 0 );
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_style_parse_filter $style_template = ' . var_export( $style_template, true ), 0 );
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_style_parse_filter $style_values = ' . var_export( $style_values, true ), 0 );

		return $html_markup;
	} // mla_tag_cloud_style_parse_filter

	/**
	 * MLA Tag Cloud Open Values
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
	public static function mla_tag_cloud_open_values_filter( $markup_values ) {
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_open_values_filter $markup_values = ' . var_export( $markup_values, true ), 0 );

		return $markup_values;
	} // mla_tag_cloud_open_values_filter

	/**
	 * MLA Tag Cloud Open Template
	 *
	 * @since 1.00
	 *
	 * @param	string	template used to generate the HTML markup
	 *
	 * @return	string	updated template
	 */
	public static function mla_tag_cloud_open_template_filter( $open_template ) {
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_open_template_filter $open_template = ' . var_export( $open_template, true ), 0 );

		return $open_template;
	} // mla_tag_cloud_open_template_filter

	/**
	 * MLA Tag Cloud Open Parse
	 *
	 * @since 1.00
	 *
	 * @param	string	HTML markup returned by the template parser
	 * @param	string	template used to generate the HTML markup
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated HTML markup for gallery output
	 */
	public static function mla_tag_cloud_open_parse_filter( $html_markup, $open_template, $markup_values ) {
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_open_parse_filter $html_markup = ' . var_export( $html_markup, true ), 0 );
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_open_parse_filter $open_template = ' . var_export( $open_template, true ), 0 );
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_open_parse_filter $markup_values = ' . var_export( $markup_values, true ), 0 );

		return $html_markup;
	} // mla_tag_cloud_open_parse_filter

	/**
	 * MLA Tag Cloud Row Open Values
	 *
	 * @since 1.00
	 *
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated substitution parameter name => value pairs
	 */
	public static function mla_tag_cloud_row_open_values_filter( $markup_values ) {
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_row_open_values_filter $markup_values = ' . var_export( $markup_values, true ), 0 );

		return $markup_values;
	} // mla_tag_cloud_row_open_values_filter

	/**
	 * MLA Tag Cloud Row Open Template
	 *
	 * @since 1.00
	 *
	 * @param	string	template used to generate the HTML markup
	 *
	 * @return	string	updated template
	 */
	public static function mla_tag_cloud_row_open_template_filter( $row_open_template ) {
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_row_open_template_filter $row_open_template = ' . var_export( $row_open_template, true ), 0 );

		return $row_open_template;
	} // mla_tag_cloud_row_open_template_filter

	/**
	 * MLA Tag Cloud Row Open Parse
	 *
	 * @since 1.00
	 *
	 * @param	string	HTML markup returned by the template parser
	 * @param	string	template used to generate the HTML markup
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated HTML markup for gallery output
	 */
	public static function mla_tag_cloud_row_open_parse_filter( $html_markup, $row_open_template, $markup_values ) {
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_row_open_parse_filter $html_markup = ' . var_export( $html_markup, true ), 0 );
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_row_open_parse_filter $row_open_template = ' . var_export( $row_open_template, true ), 0 );
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_row_open_parse_filter $markup_values = ' . var_export( $markup_values, true ), 0 );

		return $html_markup;
	} // mla_tag_cloud_row_open_parse_filter

	/**
	 * MLA Tag Cloud Item Values
	 *
	 * @since 1.00
	 *
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated substitution parameter name => value pairs
	 */
	public static function mla_tag_cloud_item_values_filter( $item_values ) {
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_item_values_filter $item_values = ' . var_export( $item_values, true ), 0 );

		/*
		 * For this example, we will color the "heat map" of cloud item size values. We use a shortcode parameter of our
		 * own to do this on a gallery-by-gallery basis, leaving other [mla_tag_cloud] instances untouched.
		 */
		if ( isset( self::$shortcode_attributes['my_filter'] ) && 'color cloud' == self::$shortcode_attributes['my_filter'] ) {
			/*
			 * Calculate red, green and blue so smallest items are red, middle items are green and biggest items are blue
			 */
			$spread = (float) $item_values['max_scaled_count'] - $item_values['min_scaled_count'];
			$half_spread = (float) $spread / 2;
			$mid_point = (float) $item_values['min_scaled_count'] + $half_spread;
			$scaled_count = (float) $item_values['scaled_count'];

			$green = 255;
			$red = $blue = 0;
			if ( $half_spread ) {
				$green = (integer) 255.0 - ( 255.0 * ( absint( $scaled_count - $mid_point ) / $half_spread ) );
				if ( $scaled_count < $mid_point ) {
					$red = (integer) 255.0 * ( ( $mid_point - $scaled_count ) / $half_spread );
				} elseif ( $scaled_count > $mid_point ) {
					$blue = (integer) 255.0 * ( ( $scaled_count - $mid_point ) / $half_spread );
				}
			}
			 
			/*
			 * 'currentlink', 'editlink', 'termlink' and 'thelink' are already composed at this point,
			 * so we must update all of them
			 */
			$old_style = $item_values['link_style'];
			$new_style = $old_style . sprintf( '; color: #%02x%02x%02x', $red, $green, $blue );
			$item_values['link_style'] = $new_style;
			$item_values['currentlink'] = str_replace( $old_style, $new_style, $item_values['currentlink'] );
			$item_values['editlink'] = str_replace( $old_style, $new_style, $item_values['editlink'] );
			$item_values['termlink'] = str_replace( $old_style, $new_style, $item_values['termlink'] );
			$item_values['thelink'] = str_replace( $old_style, $new_style, $item_values['thelink'] );
		}

		return $item_values;
	} // mla_tag_cloud_item_values_filter

	/**
	 * MLA Tag Cloud Item Template
	 *
	 * @since 1.00
	 *
	 * @param	string	template used to generate the HTML markup
	 *
	 * @return	string	updated template
	 */
	public static function mla_tag_cloud_item_template_filter( $item_template ) {
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_item_template_filter $item_template = ' . var_export( $item_template, true ), 0 );

		return $item_template;
	} // mla_tag_cloud_item_template_filter

	/**
	 * MLA Tag Cloud Item Parse
	 *
	 * @since 1.00
	 *
	 * @param	string	HTML markup returned by the template parser
	 * @param	string	template used to generate the HTML markup
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated HTML markup for gallery output
	 */
	public static function mla_tag_cloud_item_parse_filter( $html_markup, $item_template, $item_values ) {
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_item_parse_filter $html_markup = ' . var_export( $html_markup, true ), 0 );
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_item_parse_filter $item_template = ' . var_export( $item_template, true ), 0 );
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_item_parse_filter $item_values = ' . var_export( $item_values, true ), 0 );

		return $html_markup;
	} // mla_tag_cloud_item_parse_filter

	/**
	 * MLA Tag Cloud Row Close Values
	 *
	 * @since 1.00
	 *
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated substitution parameter name => value pairs
	 */
	public static function mla_tag_cloud_row_close_values_filter( $markup_values ) {
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_row_close_values_filter $markup_values = ' . var_export( $markup_values, true ), 0 );

		return $markup_values;
	} // mla_tag_cloud_row_close_values_filter

	/**
	 * MLA Tag Cloud Row Close Template
	 *
	 * @since 1.00
	 *
	 * @param	string	template used to generate the HTML markup
	 *
	 * @return	string	updated template
	 */
	public static function mla_tag_cloud_row_close_template_filter( $row_close_template ) {
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_row_close_template_filter $row_close_template = ' . var_export( $row_close_template, true ), 0 );

		return $row_close_template;
	} // mla_tag_cloud_row_close_template_filter

	/**
	 * MLA Tag Cloud Row Close Parse
	 *
	 * @since 1.00
	 *
	 * @param	string	HTML markup returned by the template parser
	 * @param	string	template used to generate the HTML markup
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated HTML markup for gallery output
	 */
	public static function mla_tag_cloud_row_close_parse_filter( $html_markup, $row_close_template, $markup_values ) {
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_row_close_parse_filter $html_markup = ' . var_export( $html_markup, true ), 0 );
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_row_close_parse_filter $row_close_template = ' . var_export( $row_close_template, true ), 0 );
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_row_close_parse_filter $markup_values = ' . var_export( $markup_values, true ), 0 );

		return $html_markup;
	} // mla_tag_cloud_row_close_parse_filter

	/**
	 * MLA Tag Cloud Close Values
	 *
	 * @since 1.00
	 *
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated substitution parameter name => value pairs
	 */
	public static function mla_tag_cloud_close_values_filter( $markup_values ) {
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_close_values_filter $markup_values = ' . var_export( $markup_values, true ), 0 );

		return $markup_values;
	} // mla_tag_cloud_close_values_filter

	/**
	 * MLA Tag Cloud Close Template
	 *
	 * @since 1.00
	 *
	 * @param	string	template used to generate the HTML markup
	 *
	 * @return	string	updated template
	 */
	public static function mla_tag_cloud_close_template_filter( $close_template ) {
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_close_template_filter $close_template = ' . var_export( $close_template, true ), 0 );

		return $close_template;
	} // mla_tag_cloud_close_template_filter

	/**
	 * MLA Tag Cloud Close Parse
	 *
	 * @since 1.00
	 *
	 * @param	string	HTML markup returned by the template parser
	 * @param	string	template used to generate the HTML markup
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated HTML markup for gallery output
	 */
	public static function mla_tag_cloud_close_parse_filter( $html_markup, $close_template, $markup_values ) {
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_close_parse_filter $html_markup = ' . var_export( $html_markup, true ), 0 );
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_close_parse_filter $close_template = ' . var_export( $close_template, true ), 0 );
		//error_log( 'MLATagCloudHooksExample::mla_tag_cloud_close_parse_filter $markup_values = ' . var_export( $markup_values, true ), 0 );

		return $html_markup;
	} // mla_tag_cloud_close_parse_filter

} // Class MLATagCloudHooksExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLATagCloudHooksExample::initialize');
?>
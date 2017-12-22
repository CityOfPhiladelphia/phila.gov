<?php
/**
 * Provides an example of hooking the filters provided by the [mla_term_list] shortcode
 *
 * In this example, the term list items are colored by parent/child "depth" when a my_filter='color list'
 * shortcode parameter is present. The example documents ALL the filters available in the shortcode.
 *
 * @package MLA Term List Hooks Example
 * @version 1.00
 */

/*
Plugin Name: MLA Term List Hooks Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Provides an example of hooking the filters provided by the [mla_term_list] shortcode
Author: David Lingren
Version: 1.00
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
 * Class MLA Term List Hooks Example hooks all of the filters provided by the [mla_term_list] shortcode
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding everything
 * else inside a class means this is the only name you have to worry about.
 *
 * @package MLA Term List Hooks Example
 * @since 1.00
 */
class MLATermListHooksExample {
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
		 * $tag - name of the hook you're filtering; defined by [mla_term_list]
		 * $function_to_add - function to be called when [mla_term_list] applies the filter
		 * $priority - default 10; lower runs earlier, higher runs later
		 * $accepted_args - number of arguments your function accepts
		 *
		 * Comment out the filters you don't need; save them for future use
		 */
		add_filter( 'mla_term_list_raw_attributes', 'MLATermListHooksExample::mla_term_list_raw_attributes', 10, 1 );
		add_filter( 'mla_term_list_attributes', 'MLATermListHooksExample::mla_term_list_attributes', 10, 1 );
		add_filter( 'mla_term_list_arguments', 'MLATermListHooksExample::mla_term_list_arguments', 10, 1 );
		add_filter( 'mla_get_terms_query_attributes', 'MLATermListHooksExample::mla_get_terms_query_attributes', 10, 1 );
		add_filter( 'mla_get_terms_query_arguments', 'MLATermListHooksExample::mla_get_terms_query_arguments', 10, 1 );
		add_filter( 'mla_get_terms_clauses', 'MLATermListHooksExample::mla_get_terms_clauses', 10, 1 );
		add_filter( 'mla_get_terms_query_results', 'MLATermListHooksExample::mla_get_terms_query_results', 10, 1 );

		add_filter( 'use_mla_term_list_style', 'MLATermListHooksExample::use_mla_term_list_style', 10, 2 );

		add_filter( 'mla_term_list_style_values', 'MLATermListHooksExample::mla_term_list_style_values', 10, 1 );
		add_filter( 'mla_term_list_style_template', 'MLATermListHooksExample::mla_term_list_style_template', 10, 1 );
		add_filter( 'mla_term_list_style_parse', 'MLATermListHooksExample::mla_term_list_style_parse', 10, 3 );

		add_filter( 'mla_term_list_open_values', 'MLATermListHooksExample::mla_term_list_open_values', 10, 1 );
		add_filter( 'mla_term_list_open_template', 'MLATermListHooksExample::mla_term_list_open_template', 10, 1 );
		add_filter( 'mla_term_list_open_parse', 'MLATermListHooksExample::mla_term_list_open_parse', 10, 3 );

		add_filter( 'mla_term_list_item_values', 'MLATermListHooksExample::mla_term_list_item_values', 10, 1 );
		add_filter( 'mla_term_list_item_template', 'MLATermListHooksExample::mla_term_list_item_template', 10, 1 );
		add_filter( 'mla_term_list_item_parse', 'MLATermListHooksExample::mla_term_list_item_parse', 10, 3 );

		add_filter( 'mla_term_list_close_values', 'MLATermListHooksExample::mla_term_list_close_values', 10, 1 );
		add_filter( 'mla_term_list_close_template', 'MLATermListHooksExample::mla_term_list_close_template', 10, 1 );
		add_filter( 'mla_term_list_close_parse', 'MLATermListHooksExample::mla_term_list_close_parse', 10, 3 );
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
	 * MLA Term List Raw (Display) Attributes
	 *
	 * This filter gives you an opportunity to record or modify the arguments passed in to the shortcode
	 * before they pass through the logic to handle the 'mla_page_parameter' and "request:" prefix processing.
	 *
	 * The $shortcode_attributes array is where you will find any of your own parameters that are coded in the
	 * shortcode, e.g., [mla_term_list my_parameter="my value"].
	 *
	 * @since 1.00
	 *
	 * @param	array	the raw shortcode parameters passed in to the shortcode
	 *
	 * @return	array	updated shortcode attributes
	 */
	public static function mla_term_list_raw_attributes( $shortcode_attributes ) {
		/*
		 * Uncomment the error_log statements in any of the filters to see what's passed in
		 */
		//error_log( 'MLATermListHooksExample::mla_term_list_raw_attributes_filter $shortcode_attributes = ' . var_export( $shortcode_attributes, true ), 0 );

		/*
		 * Note that the global $post; object is available here and in all later filters.
		 * It contains the post/page on which the [mla_term_list] appears.
		 * Some [mla_term_list] invocations are not associated with a post/page; these will
		 * have a substitute $post object with $post->ID == 0.
		 */
		global $post;
		//error_log( 'MLATermListHooksExample::mla_term_list_raw_attributes_filter $post->ID = ' . var_export( $post->ID, true ), 0 );

		return $shortcode_attributes;
	} // mla_term_list_raw_attributes_filter

	/**
	 * MLA Term List (Display) Attributes
	 *
	 * This filter gives you an opportunity to record or modify the arguments passed in to the shortcode
	 * before they are merged with the default arguments used for the gallery display.
	 *
	 * The $shortcode_attributes array is where you will find any of your own parameters that are coded in the
	 * shortcode, e.g., [mla_term_list my_parameter="my value"].
	 *
	 * @since 1.00
	 * @uses MLATermListHooksExample::$shortcode_attributes
	 *
	 * @param	array	the shortcode parameters passed in to the shortcode
	 *
	 * @return	array	updated shortcode attributes
	 */
	public static function mla_term_list_attributes( $shortcode_attributes ) {
		/*
		 * Uncomment the error_log statements in any of the filters to see what's passed in
		 */
		//error_log( 'MLATermListHooksExample::mla_term_list_attributes_filter $shortcode_attributes = ' . var_export( $shortcode_attributes, true ), 0 );

		/*
		 * Save the attributes for use in the later filters
		 */
		self::$shortcode_attributes = $shortcode_attributes;

		/*
		 * Filters must return the first argument passed in, unchanged or updated
		 */
		return $shortcode_attributes;
	} // mla_term_list_attributes_filter

	/**
	 * Save the shortcode arguments
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $all_display_parameters = array();

	/**
	 * MLA Term List (Display) Arguments
	 *
	 * This filter gives you an opportunity to record or modify the gallery display arguments
	 * after the shortcode attributes are merged with the default arguments.
	 *
	 * Note that the values in this array are input or default values, not the final computed values
	 * used for the gallery display.  The computed values are in the $style_values, $markup_values and
	 * $item_values arrays passed to later filters below.
	 *
	 * @since 1.00
	 * @uses MLATermListHooksExample::$all_display_parameters
	 *
	 * @param	array	shortcode arguments merged with gallery display defaults, so every possible parameter is present
	 *
	 * @return	array	updated gallery display arguments
	 */
	public static function mla_term_list_arguments( $all_display_parameters ) {
		//error_log( 'MLATermListHooksExample::mla_term_list_arguments_filter $all_display_parameters = ' . var_export( $all_display_parameters, true ), 0 );

		self::$all_display_parameters = $all_display_parameters;
		return $all_display_parameters;
	} // mla_term_list_arguments_filter

	/**
	 * Save the query attributes
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $query_attributes = array();

	/**
	 * MLA Term List Query Attributes
	 *
	 * This filter gives you an opportunity to record or modify the arguments passed in to the shortcode
	 * before they are merged with the default arguments used to select the attachments for the gallery.
	 *
	 * The query attributes passed in to this filter are the same as those passed through the
	 * "MLA Term List (Display) Attributes" filter above. This filter is provided so you can modify
	 * the data selection attributes without disturbing the attributes used for gallery display.
	 *
	 * @since 1.00
	 * @uses MLATermListHooksExample::$query_attributes
	 *
	 * @param	array	the shortcode parameters passed in to the shortcode
	 *
	 * @return	array	updated shortcode attributes
	 */
	public static function mla_get_terms_query_attributes( $query_attributes ) {
		//error_log( 'MLATermListHooksExample::mla_get_terms_query_attributes_filter $query_attributes = ' . var_export( $query_attributes, true ), 0 );

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
	 * MLA Term List Query Arguments
	 *
	 * This filter gives you an opportunity to record or modify the attachment query arguments
	 * after the shortcode attributes are merged with the default arguments.
	 *
	 * @since 1.00
	 * @uses MLATermListHooksExample::$all_query_parameters
	 *
	 * @param	array	shortcode arguments merged with attachment selection defaults, so every possible parameter is present
	 *
	 * @return	array	updated attachment query arguments
	 */
	public static function mla_get_terms_query_arguments( $all_query_parameters ) {
		//error_log( 'MLATermListHooksExample::mla_get_terms_query_arguments_filter $all_query_parameters = ' . var_export( $all_query_parameters, true ), 0 );

		self::$all_query_parameters = $all_query_parameters;
		return $all_query_parameters;
	} // mla_get_terms_query_arguments_filter

	/**
	 * MLA Term List Query Clauses
	 *
	 * This action gives you a final opportunity to inspect or modify
	 * the SQL clauses for the data selection process.
	 *
	 * @since 1.00
	 *
	 * @param	array	SQL clauses ( 'fields', 'join', 'where', 'order', 'orderby', 'limits' )
	 *
	 * @return	array	updated SQL clauses
	 */
	public static function mla_get_terms_clauses( $clauses ) {
		//error_log( 'MLATermListHooksExample::mla_get_terms_clauses_filter $clauses = ' . var_export( $clauses, true ), 0 );

		return $clauses;
	} // mla_get_terms_clauses_filter

	/**
	 * MLA Term List Query Results
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
	public static function mla_get_terms_query_results( $tag_objects ) {
		//error_log( 'MLATermListHooksExample::mla_get_terms_query_results_filter $tag_objects = ' . var_export( $tag_objects, true ), 0 );

		return $tag_objects;
	} // mla_get_terms_query_results_filter

	/**
	 * Use MLA Term List Style
	 *
	 * You can use this filter to allow or suppress the inclusion of CSS styles in the
	 * gallery output. Return 'true' to allow the styles, false to suppress them. You can also
	 * suppress styles by returning an empty string from the mla_term_list_style_parse_filter below.
	 *
	 * @since 1.00
	 *
	 * @param	boolean	true unless the mla_style parameter is "none"
	 * @param	string	value of the mla_style parameter
	 *
	 * @return	boolean	true to fetch and parse the style template, false to leave it empty
	 */
	public static function use_mla_term_list_style( $use_style_template, $style_template_name ) {
		//error_log( 'MLATermListHooksExample::use_mla_term_list_style_filter $use_style_template = ' . var_export( $use_style_template, true ), 0 );
		//error_log( 'MLATermListHooksExample::use_mla_term_list_style_filter $style_template_name = ' . var_export( $style_template_name, true ), 0 );

		return $use_style_template;
	} // use_mla_term_list_style_filter

	/**
	 * MLA Term List Style Values
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
	public static function mla_term_list_style_values( $style_values ) {
		//error_log( 'MLATermListHooksExample::mla_term_list_style_values_filter $style_values = ' . var_export( $style_values, true ), 0 );

		/*
		 * You also have access to the PHP Super Globals, e.g., $_REQUEST, $_SERVER
		 */
		//error_log( 'MLATermListHooksExample::mla_term_list_style_values_filter $_REQUEST = ' . var_export( $_REQUEST, true ), 0 );
		//error_log( 'MLATermListHooksExample::mla_term_list_style_values_filter $_SERVER[ REQUEST_URI ] = ' . var_export( $_SERVER['REQUEST_URI'], true ), 0 );

		/*
		 * You can use the WordPress globals like $wp_query, $wpdb and $table_prefix as well.
		 * Note that $wp_query contains values for the post/page query, NOT the [mla_term_list] query.
		 */
		global $wp_query;
		//error_log( 'MLATermListHooksExample::mla_term_list_style_values_filter $wp_query->query = ' . var_export( $wp_query->query, true ), 0 );

		return $style_values;
	} // mla_term_list_style_values_filter

	/**
	 * MLA Term List Style Template
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
	public static function mla_term_list_style_template( $style_template ) {
		//error_log( 'MLATermListHooksExample::mla_term_list_style_template_filter $style_template = ' . var_export( $style_template, true ), 0 );

		return $style_template;
	} // mla_term_list_style_template_filter

	/**
	 * MLA Term List Style Parse
	 *
	 * The "Parse" series of filters gives you a chance to modify or replace the HTML markup
	 * that will be added to the [mla_term_list] output. It is called just after the values array
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
	public static function mla_term_list_style_parse( $html_markup, $style_template, $style_values ) {
		//error_log( 'MLATermListHooksExample::mla_term_list_style_parse_filter $html_markup = ' . var_export( $html_markup, true ), 0 );
		//error_log( 'MLATermListHooksExample::mla_term_list_style_parse_filter $style_template = ' . var_export( $style_template, true ), 0 );
		//error_log( 'MLATermListHooksExample::mla_term_list_style_parse_filter $style_values = ' . var_export( $style_values, true ), 0 );

		return $html_markup;
	} // mla_term_list_style_parse_filter

	/**
	 * MLA Term List Open Values
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
	public static function mla_term_list_open_values( $markup_values ) {
		//error_log( 'MLATermListHooksExample::mla_term_list_open_values_filter $markup_values = ' . var_export( $markup_values, true ), 0 );

		return $markup_values;
	} // mla_term_list_open_values_filter

	/**
	 * MLA Term List Open Template
	 *
	 * @since 1.00
	 *
	 * @param	string	template used to generate the HTML markup
	 *
	 * @return	string	updated template
	 */
	public static function mla_term_list_open_template( $open_template ) {
		//error_log( 'MLATermListHooksExample::mla_term_list_open_template_filter $open_template = ' . var_export( $open_template, true ), 0 );

		return $open_template;
	} // mla_term_list_open_template_filter

	/**
	 * MLA Term List Open Parse
	 *
	 * @since 1.00
	 *
	 * @param	string	HTML markup returned by the template parser
	 * @param	string	template used to generate the HTML markup
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated HTML markup for gallery output
	 */
	public static function mla_term_list_open_parse( $html_markup, $open_template, $markup_values ) {
		//error_log( 'MLATermListHooksExample::mla_term_list_open_parse_filter $html_markup = ' . var_export( $html_markup, true ), 0 );
		//error_log( 'MLATermListHooksExample::mla_term_list_open_parse_filter $open_template = ' . var_export( $open_template, true ), 0 );
		//error_log( 'MLATermListHooksExample::mla_term_list_open_parse_filter $markup_values = ' . var_export( $markup_values, true ), 0 );

		return $html_markup;
	} // mla_term_list_open_parse_filter

	/**
	 * MLA Term List Item Values
	 *
	 * @since 1.00
	 *
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated substitution parameter name => value pairs
	 */
	public static function mla_term_list_item_values( $item_values ) {
		/*
		 * For this example, we will color the term list by parent/child "depth". We use a shortcode parameter of our
		 * own to do this on a gallery-by-gallery basis, leaving other [mla_term_list] instances untouched.
		 */
		if ( isset( self::$shortcode_attributes['my_filter'] ) && 'color list' == self::$shortcode_attributes['my_filter'] ) {
			$color = $item_values['current_level'] % 3;
			$red = ( 0 == $color ) ? 255 : 0;
			$green = ( 1 == $color ) ? 255 : 0;
			$blue = ( 2 == $color ) ? 255 : 0;
			$old_attributes = $item_values['link_attributes'];
			$new_attributes = sprintf( 'style="color: #%02x%02x%02x"', $red, $green, $blue );
	
			if ( empty( $old_attributes ) ) {
				$item_values['link_attributes'] = $new_attributes;
				$new_attributes = ' ' . $item_values['link_attributes'] . ' href=';
				$item_values['currentlink'] = str_replace( ' href=', $new_attributes, $item_values['currentlink'] );
				$item_values['editlink'] = str_replace( ' href=', $new_attributes, $item_values['editlink'] );
				$item_values['termlink'] = str_replace( ' href=', $new_attributes, $item_values['termlink'] );
				$item_values['thelink'] = str_replace( ' href=', $new_attributes, $item_values['thelink'] );
			} else {
				$item_values['link_attributes'] .= ' ' . $new_attributes;
				$new_attributes = $item_values['link_attributes'] . ' href=';
				$item_values['currentlink'] = str_replace( $old_attributes, $new_attributes, $item_values['currentlink'] );
				$item_values['editlink'] = str_replace( $old_attributes, $new_attributes, $item_values['editlink'] );
				$item_values['termlink'] = str_replace( $old_attributes, $new_attributes, $item_values['termlink'] );
				$item_values['thelink'] = str_replace( $old_attributes, $new_attributes, $item_values['thelink'] );
			}
		}

		return $item_values;
	} // mla_term_list_item_values_filter

	/**
	 * MLA Term List Item Template
	 *
	 * @since 1.00
	 *
	 * @param	string	template used to generate the HTML markup
	 *
	 * @return	string	updated template
	 */
	public static function mla_term_list_item_template( $item_template ) {
		//error_log( 'MLATermListHooksExample::mla_term_list_item_template_filter $item_template = ' . var_export( $item_template, true ), 0 );

		return $item_template;
	} // mla_term_list_item_template_filter

	/**
	 * MLA Term List Item Parse
	 *
	 * @since 1.00
	 *
	 * @param	string	HTML markup returned by the template parser
	 * @param	string	template used to generate the HTML markup
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated HTML markup for gallery output
	 */
	public static function mla_term_list_item_parse( $html_markup, $item_template, $item_values ) {
		//error_log( 'MLATermListHooksExample::mla_term_list_item_parse_filter $html_markup = ' . var_export( $html_markup, true ), 0 );
		//error_log( 'MLATermListHooksExample::mla_term_list_item_parse_filter $item_template = ' . var_export( $item_template, true ), 0 );
		//error_log( 'MLATermListHooksExample::mla_term_list_item_parse_filter $item_values = ' . var_export( $item_values, true ), 0 );

		return $html_markup;
	} // mla_term_list_item_parse_filter

	/**
	 * MLA Term List Close Values
	 *
	 * @since 1.00
	 *
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated substitution parameter name => value pairs
	 */
	public static function mla_term_list_close_values( $markup_values ) {
		//error_log( 'MLATermListHooksExample::mla_term_list_close_values_filter $markup_values = ' . var_export( $markup_values, true ), 0 );

		return $markup_values;
	} // mla_term_list_close_values_filter

	/**
	 * MLA Term List Close Template
	 *
	 * @since 1.00
	 *
	 * @param	string	template used to generate the HTML markup
	 *
	 * @return	string	updated template
	 */
	public static function mla_term_list_close_template( $close_template ) {
		//error_log( 'MLATermListHooksExample::mla_term_list_close_template_filter $close_template = ' . var_export( $close_template, true ), 0 );

		return $close_template;
	} // mla_term_list_close_template_filter

	/**
	 * MLA Term List Close Parse
	 *
	 * @since 1.00
	 *
	 * @param	string	HTML markup returned by the template parser
	 * @param	string	template used to generate the HTML markup
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated HTML markup for gallery output
	 */
	public static function mla_term_list_close_parse( $html_markup, $close_template, $markup_values ) {
		//error_log( 'MLATermListHooksExample::mla_term_list_close_parse_filter $html_markup = ' . var_export( $html_markup, true ), 0 );
		//error_log( 'MLATermListHooksExample::mla_term_list_close_parse_filter $close_template = ' . var_export( $close_template, true ), 0 );
		//error_log( 'MLATermListHooksExample::mla_term_list_close_parse_filter $markup_values = ' . var_export( $markup_values, true ), 0 );

		return $html_markup;
	} // mla_term_list_close_parse_filter

} // Class MLATermListHooksExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLATermListHooksExample::initialize');
?>
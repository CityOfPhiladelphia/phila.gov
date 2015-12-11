<?php
/**
 * Provides examples of the filters provided by the "Media Manager Enhancements" feature
 *
 * In this example:
 *     - the initial value for the MIME Type dropdown control can be changed.
 *     - items assigned to a taxonomy term can be excluded from the "Query Attachments" results
 *
 * @package MLA Media Modal Hooks Example
 * @version 1.01
 */

/*
Plugin Name: MLA Media Modal Hooks Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Provides examples of the filters provided by the "Media Manager Enhancements" feature
Author: David Lingren
Version: 1.01
Author URI: http://fairtradejudaica.org/our-story/staff/

Copyright 2014-2015 David Lingren

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
 * Class MLA Media Modal Hooks Example hooks all of the filters provided by the "Media Manager Enhancements" feature
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding everything
 * else inside a class means this is the only name you have to worry about.
 *
 * @package MLA Media Modal Hooks Example
 * @since 1.00
 */
class MLAMediaModalExample {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * Installs filters and actions that handle the MLA hooks for the "Media Manager Enhancements"
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
		 * add_filter parameters:
		 * $tag - name of the hook you're filtering; defined by [mla_gallery]
		 * $function_to_add - function to be called when [mla_gallery] applies the filter
		 * $priority - default 10; lower runs earlier, higher runs later
		 * $accepted_args - number of arguments your function accepts
		 *
		 * Comment out the filters you don't need; save them for future use
		 */
		 
		/*
		 * Defined in /media-library-assistant/includes/class-mla-data.php
		 */
		add_filter( 'mla_media_modal_form_fields', 'MLAMediaModalExample::mla_media_modal_form_fields', 10, 2 );
		add_filter( 'mla_media_modal_months_dropdown', 'MLAMediaModalExample::mla_media_modal_months_dropdown', 10, 2 );
		add_filter( 'mla_media_modal_terms_options', 'MLAMediaModalExample::mla_media_modal_terms_options', 10, 1 );
		add_filter( 'mla_media_modal_initial_filters', 'MLAMediaModalExample::mla_media_modal_initial_filters', 10, 2 );
		add_filter( 'mla_media_modal_settings', 'MLAMediaModalExample::mla_media_modal_settings', 10, 2 );
		add_filter( 'mla_media_modal_strings', 'MLAMediaModalExample::mla_media_modal_strings', 10, 2 );
		add_filter( 'mla_media_modal_template_path', 'MLAMediaModalExample::mla_media_modal_template_path', 10, 2 );
		add_filter( 'mla_media_modal_begin_fill_compat_fields', 'MLAMediaModalExample::mla_media_modal_begin_fill_compat_fields', 10, 3 );
		add_filter( 'mla_media_modal_end_fill_compat_fields', 'MLAMediaModalExample::mla_media_modal_end_fill_compat_fields', 10, 4 );
		add_action( 'mla_media_modal_begin_update_compat_fields', 'MLAMediaModalExample::mla_media_modal_begin_update_compat_fields', 10, 1 );
		add_filter( 'mla_media_modal_update_compat_fields_terms', 'MLAMediaModalExample::mla_media_modal_update_compat_fields_terms', 10, 4 );
		add_filter( 'mla_media_modal_end_update_compat_fields', 'MLAMediaModalExample::mla_media_modal_end_update_compat_fields', 10, 3 );
		add_filter( 'mla_media_modal_query_initial_terms', 'MLAMediaModalExample::mla_media_modal_query_initial_terms', 10, 2 );
		add_filter( 'mla_media_modal_query_filtered_terms', 'MLAMediaModalExample::mla_media_modal_query_filtered_terms', 10, 2 );

		/*
		 * Defined in /media-library-assistant/includes/class-mla-data.php
		 */
		add_filter( 'mla_media_modal_query_final_terms', 'MLAMediaModalExample::mla_media_modal_query_final_terms', 10, 1 );
		add_filter( 'mla_media_modal_query_custom_items', 'MLAMediaModalExample::mla_media_modal_query_custom_items', 10, 2 );
	} // initialize

	/**
	 * MLA Edit Media Form Fields Filter
	 *
	 * Gives you an opportunity to change the content of the
	 * Media Manager Modal Window ATTACHMENT DETAILS fields.
	 *
	 * @since 1.01
	 *
	 * @param	array	descriptors for the "compat-attachment-fields" 
	 * @param	object	the post to be edited
	 */
	public static function mla_media_modal_form_fields( $form_fields, $post ) {
		/*
		 * Uncomment the error_log statements in any of the filters to see what's passed in
		 */
		//error_log( 'MLAMediaModalExample::mla_media_modal_form_fields $form_fields = ' . var_export( $form_fields, true ), 0 );
		//error_log( 'MLAMediaModalExample::mla_media_modal_form_fields $post = ' . var_export( $post, true ), 0 );

		return $form_fields;
	} // mla_media_modal_form_fields

	/**
	 * MLA Edit Media Month & Year Dropdown Filter
	 *
	 * Gives you an opportunity to change the content of the
	 * Media Manager Modal Window Month & Year Dropdown control.
	 *
	 * @since 1.01
	 *
	 * @param	array	( value => label ) pairs, e.g. 0 => 'Show all dates', 201506 => 'June 2015'
	 * @param	string	post_type, e.g., 'attachment'
	 */
	public static function mla_media_modal_months_dropdown( $month_array, $post_type ) {
		//error_log( "MLAMediaModalExample::mla_media_modal_months_dropdown( {$post_type} ) \$month_array = " . var_export( $month_array, true ), 0 );

		return $month_array;
	} // mla_media_modal_months_dropdown

	/**
	 * MLA Edit Media Terms Dropdown Filter
	 *
	 * Gives you an opportunity to change the content of the
	 * Media Manager Modal Window Terms Dropdown control.
	 *
	 * @since 1.01
	 *
	 * @param	array	( 'class' => $class_array, 'value' => $value_array, 'text' => $text_array )
	 */
	public static function mla_media_modal_terms_options( $term_values ) {
		//error_log( 'MLAMediaModalExample::mla_media_modal_terms_options $term_values = ' . var_export( $term_values, true ), 0 );

		/*
		 * $class_array => HTML class attribute value for each option
		 * $value_array => HTML value attribute value for each option
		 * $text_array => HTML text content for each option
		 */
		return $term_values;
	} // mla_media_modal_terms_options

	/**
	 * MLA Edit Media Initial Filters Filter
	 *
	 * Gives you an opportunity to change the initial values of the
	 * Media Manager Modal Window toolbar controls.
	 *
	 * @since 1.00
	 *
	 * @param	array	associative array with setting => value pairs
	 * @param	object	current post object, if available, else NULL
	 */
	public static function mla_media_modal_initial_filters( $initial_values, $post ) {
		//error_log( 'MLAMediaModalExample::mla_media_modal_initial_filters $initial_values = ' . var_export( $initial_values, true ), 0 );
		//error_log( 'MLAMediaModalExample::mla_media_modal_initial_filters $post = ' . var_export( $post, true ), 0 );

		/*
		 * The default initial values are:
		 *
		 * $initial_values = array(
		 * 	'filterMime' => 'all',
		 * 	'filterMonth' => 0,
		 * 	'filterTerm' => 0,
		 * 	'searchConnector' => 'AND',
		 * 	'searchFields' => array( 'title', 'content' ),
		 * 	'searchValue' => '',
		 * );
		 *
		 * Other values include:
		 * 	filterMime: uploaded, image, audio, video, text, application, detached
		 * 	filterMonth: year and month, e.g., '201407'
		 * 	filterTerm: term ID in the selected taxonomy (NOT term-taxonomy ID)
		 * 	searchConnector: 'OR'
		 * 	searchFields: name (slug), alt-text, excerpt (caption), terms
		 */

		// uncomment next lines to set initial values
		//$initial_values['filterMime'] = 'image';
		//$initial_values['filterMonth'] = '201404';
		//$initial_values['filterTerm'] = 175; // term ID in attachment_tags
		//$initial_values['searchConnector'] = 'OR';
		//$initial_values['searchValue'] = 'de la';

		return $initial_values;
	} // mla_media_modal_initial_filters

	/**
	 * MLA Edit Media Toolbar Settings Filter
	 *
	 * Gives you an opportunity to change the content of the
	 * Media Manager Modal Window toolbar controls.
	 *
	 * @since 1.01
	 *
	 * @param	array	associative array with setting => value pairs
	 * @param	object	current post object, if available, else NULL
	 */
	public static function mla_media_modal_settings( $settings, $post ) {
		//error_log( 'MLAMediaModalExample::mla_media_modal_settings $settings = ' . var_export( $settings, true ), 0 );
		//error_log( 'MLAMediaModalExample::mla_media_modal_settings $post = ' . var_export( $post, true ), 0 );

		return $settings;
	} // mla_media_modal_settings

	/**
	 * MLA Edit Media Toolbar Strings Filter
	 *
	 * Gives you an opportunity to change the content of the
	 * string values passed Media Manager Modal Window toolbar controls.
	 *
	 * @since 1.01
	 *
	 * @param	array	associative array with slug => text pairs
	 * @param	object	current post object, if available, else NULL
	 */
	public static function mla_media_modal_strings( $strings, $post ) {
		//error_log( 'MLAMediaModalExample::mla_media_modal_strings $strings = ' . var_export( $strings, true ), 0 );
		//error_log( 'MLAMediaModalExample::mla_media_modal_strings $post = ' . var_export( $post, true ), 0 );

		return $strings;
	} // mla_media_modal_strings

	/**
	 * MLA Edit Media JavaScript Template(s) Filter
	 *
	 * Gives you an opportunity to change the path to the JavaScript template file
	 * or substitute your own template(s).
	 *
	 * @since 1.01
	 *
	 * @param	string	absolute path to the JavaScript template file
	 */
	public static function mla_media_modal_template_path( $template_path ) {
		//error_log( 'MLAMediaModalExample::mla_media_modal_template_path $template_path = ' . var_export( $template_path, true ), 0 );

		/*
		 * To suppress the loading of the default template(s), set $template_path = '';
		 */
		return $template_path;
	} // mla_media_modal_template_path

	/**
	 * MLA Edit Media begin "fill compat-attachment-fields" Filter
	 *
	 * Gives you an opportunity to replace the content of the
	 * Media Manager Modal Window ATTACHMENT DETAILS taxonomy meta boxes
	 * before the MLA results have been added.
	 *
	 * @since 1.01
	 *
	 * @param	array	empty array of HTML markup for the taxonomy meta boxes
	 * @param	array	all requested taxonomies
	 * @param	object	current post object
	 */
	public static function mla_media_modal_begin_fill_compat_fields( $results, $requested, $post ) {
		//error_log( 'MLAMediaModalExample::mla_media_modal_fill_compat_fields $results = ' . var_export( $results, true ), 0 );
		//error_log( 'MLAMediaModalExample::mla_media_modal_fill_compat_fields $requested = ' . var_export( $requested, true ), 0 );
		//error_log( 'MLAMediaModalExample::mla_media_modal_fill_compat_fields $post = ' . var_export( $post, true ), 0 );

		/*
		 * To replace the MLA results with your own, return an array containing
		 * ( taxonomy-slug => HTML markup for the taxonomy meta box
		 */
		return $results;
	} // mla_media_modal_fill_compat_fields

	/**
	 * MLA Edit Media end of "fill compat-attachment-fields" Filter
	 *
	 * Gives you an opportunity to change the content of the
	 * Media Manager Modal Window ATTACHMENT DETAILS taxonomy meta boxes
	 * after MLA results have been added.
	 *
	 * @since 1.01
	 *
	 * @param	string	HTML markup for the taxonomy meta boxes
	 * @param	array	all requested taxonomies
	 * @param	array	unsupported taxonomies; should be empty
	 * @param	object	current post object
	 */
	public static function mla_media_modal_end_fill_compat_fields( $results, $requested, $unsupported, $post ) {
		//error_log( 'MLAMediaModalExample::mla_media_modal_end_fill_compat_fields $results = ' . var_export( $results, true ), 0 );
		//error_log( 'MLAMediaModalExample::mla_media_modal_end_fill_compat_fields $requested = ' . var_export( $requested, true ), 0 );
		//error_log( 'MLAMediaModalExample::mla_media_modal_end_fill_compat_fields $unsupported = ' . var_export( $unsupported, true ), 0 );
		//error_log( 'MLAMediaModalExample::mla_media_modal_end_fill_compat_fields $post = ' . var_export( $post, true ), 0 );

		return $results;
	} // mla_media_modal_end_fill_compat_fields

	/**
	 * MLA Edit Media "begin update compat-attachment-fields" initial Action
	 *
	 * Gives you an opportunity to pre-process the $_REQUEST elements for the 
	 * Media Manager Modal Window ATTACHMENT DETAILS taxonomy meta boxes updates.
	 *
	 * @since 1.01
	 *
	 * @param	object	the current post
	 */
	public static function mla_media_modal_begin_update_compat_fields( $post ) {
		//error_log( "MLAMediaModalExample::mla_media_modal_begin_update_compat_fields( {$post->ID} )", 0 );

	} // mla_media_modal_begin_update_compat_fields

	/**
	 * MLA Edit Media "update compat-attachment-fields terms" Filter
	 *
	 * Gives you an opportunity to change the terms assigned to one
	 * Media Manager Modal Window ATTACHMENT DETAILS taxonomy.
	 *
	 * @since 1.01
	 *
	 * @param	array	assigned term id/name values
	 * @param	string	taxonomy slug
	 * @param	object	taxonomy object
	 * @param	integer	current post ID
	 */
	public static function mla_media_modal_update_compat_fields_terms( $terms, $key, $value, $post_id ) {
		//error_log( "MLAMediaModalExample::mla_media_modal_update_compat_fields_terms( {$key}, {$post_id} ) \$terms = " . var_export( $terms, true ), 0 );
		//error_log( "MLAMediaModalExample::mla_media_modal_update_compat_fields_terms( {$key}, {$post_id} ) \$value = " . var_export( $value, true ), 0 );

		/*
		 * To suppress term assignment, set $terms = NULL;
		 */
		return $terms;
	} // mla_media_modal_update_compat_fields_terms

	/**
	 * MLA Edit Media "end update compat-attachment-fields" Filter
	 *
	 * Gives you an opportunity to change the content of one (or more)
	 * Media Manager Modal Window ATTACHMENT DETAILS taxonomy meta boxes
	 * with updated checkbox or tag/term lists.
	 *
	 * @since 1.01
	 *
	 * @param	string	HTML markup for the taxonomy meta box elements
	 * @param	array	supported  taxonomy objects
	 * @param	object	current post object
	 */
	public static function mla_media_modal_end_update_compat_fields( $results, $taxonomies, $post ) {
		//error_log( 'MLAMediaModalExample::mla_media_modal_end_update_compat_fields $results = ' . var_export( $results, true ), 0 );
		//error_log( 'MLAMediaModalExample::mla_media_modal_end_update_compat_fields $taxonomies = ' . var_export( $taxonomies, true ), 0 );
		//error_log( 'MLAMediaModalExample::mla_media_modal_end_update_compat_fields $post = ' . var_export( $post, true ), 0 );

		return $results;
	} // mla_media_modal_end_update_compat_fields

	/**
	 * MLA Edit Media "Query Attachments" initial terms Filter
	 *
	 * Gives you an opportunity to change the terms of the 
	 * Media Manager Modal Window "Query Attachments" query
	 * before they are pre-processed by the MLA handler.
	 *
	 * @since 1.01
	 *
	 * @param	array	WP_Query terms supported for "Query Attachments"
	 * @param	array	All terms passed in the request
	 */
	public static function mla_media_modal_query_initial_terms( $query, $raw_query ) {
		//error_log( 'MLAMediaModalExample::mla_media_modal_query_initial_terms $query = ' . var_export( $query, true ), 0 );
		//error_log( 'MLAMediaModalExample::mla_media_modal_query_initial_terms $raw_query = ' . var_export( $raw_query, true ), 0 );

		return $query;
	} // mla_media_modal_query_initial_terms

	/**
	 * MLA Edit Media "Query Attachments" filtered terms Filter
	 *
	 * Gives you an opportunity to change the terms of the 
	 * Media Manager Modal Window "Query Attachments" query
	 * after they are pre-processed by the Ajax handler.
	 *
	 * @since 1.01
	 *
	 * @param	array	WP_Query terms supported for "Query Attachments"
	 * @param	array	All terms passed in the request
	 */
	public static function mla_media_modal_query_filtered_terms( $query, $raw_query ) {
		//error_log( 'MLAMediaModalExample::mla_media_modal_query_filtered_terms $query = ' . var_export( $query, true ), 0 );
		//error_log( 'MLAMediaModalExample::mla_media_modal_query_filtered_terms $raw_query = ' . var_export( $raw_query, true ), 0 );

		return $query;
	} // mla_media_modal_query_filtered_terms

	/**
	 * MLA Edit Media "Query Attachments" final terms Filter
	 *
	 * Gives you an opportunity to change the terms of the 
	 * Media Manager Modal Window "Query Attachments" query
	 * after they are processed by the "Prepare List Table Query" handler.
	 *
	 * @since 1.01
	 *
	 * @param	array	WP_Query request prepared by "Prepare List Table Query"
	 */
	public static function mla_media_modal_query_final_terms( $request ) {
		//error_log( 'MLAMediaModalExample::mla_media_modal_query_final_terms $request = ' . var_export( $request, true ), 0 );

		/*
		 * MLAData::$query_parameters and MLAData::$search_parameters contain
		 * additional parameters used in some List Table queries.
		 */
		 
		/*
		 * Comment the next line out to remove items assigned to the
		 *  Att. Categories "Admin" term from the query results.
		 */
		return $request;

		if ( isset( $request['tax_query'] ) ) {
			$tax_query = $request['tax_query'];
			$tax_query['relation'] = 'AND';
		} else {
			$tax_query = array();
		}

		$tax_query[] = array( 'taxonomy' => 'attachment_category', 'operator' => 'NOT IN', 'field' => 'slug', 'terms' => 'admin' );
		$request['tax_query'] = $tax_query;

		//error_log( 'MLAMediaModalExample::mla_media_modal_query_final_terms altered $request = ' . var_export( $request, true ), 0 );
		return $request;
	} // mla_media_modal_query_final_terms

	/**
	 * MLA Edit Media "Query Attachments" custom results filter
	 *
	 * Gives you an opportunity to substitute the results of the 
	 * Media Manager Modal Window "Query Attachments" query
	 * with alternative results of your own.
	 *
	 * @since 1.01
	 *
	 * @param	object	NULL, indicating no results substitution
	 * @param	array	WP_Query request prepared by "Prepare List Table Query"
	 */
	public static function mla_media_modal_query_custom_items( $wp_query_object, $request ) {
		//error_log( 'MLAMediaModalExample::mla_media_modal_query_custom_items $request = ' . var_export( $request, true ), 0 );

		/*
		 * You can replace the NULL $wp_query_object with a new WP_Query( $request )
		 * object using your own $request parameters
		 */
		return $wp_query_object;
	} // mla_media_modal_query_custom_items
} //MLAMediaModalExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLAMediaModalExample::initialize');
?>
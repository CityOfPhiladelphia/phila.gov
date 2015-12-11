<?php
/**
 * Shows how to use a Custom Field mapping filter to accomplish item-specific database updates.
 *
 * In this example, media items and term taxonomy information is extracted from a custom database table
 * created by "WP MediaTagger" (http://wordpress.org/plugins/wp-mediatagger/) and converted to term
 * assignments in the WordPress taxonomy of your choice.
 *
 * This example was developed to answer a support topic, "Transferring tagged images from another plugin?"
 * (http://wordpress.org/support/topic/transferring-tagged-images-from-another-plugin).
 *
 * The example is based on the "example plugin" that comes with MLA. Only those elements of the example plugin
 * that are required for the current task have been retained in this plugin.
 *
 * @package MLA Media Tagger Example
 * @version 1.00
 */

/*
Plugin Name: MLA Media Tagger Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Shows how to use a Custom Field mapping filter to accomplish item-specific database updates.
Author: David Lingren
Version: 1.00
Author URI: http://fairtradejudaica.org/our-story/staff/

Copyright 2014 David Lingren

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
 * Class MLA Mapping Hooks Example hooks one of the filters provided by the IPTC/EXIF and Custom Field mapping features
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding everything
 * else inside a class means this is the only name you have to worry about.
 *
 * @package MLA Media Tagger Example
 * @since 1.00
 */
class MLAMediaTaggerExample {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * Installs a filter for the 'mla_mapping_rule' hook, just one of 25 filters
	 * supported by the MLA IPTC/EXIF and Custom Field Mapping functions.
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
		 */
		add_filter( 'mla_mapping_rule', 'MLAMediaTaggerExample::mla_mapping_rule_filter', 10, 4 );
		//error_log( 'MLAMediaTaggerExample::initialize', 0 );
	}

	/**
	 * MLA Mapping Rule Filter
	 *
	 * This filter is called once for each mapping rule, before the rule
	 * is evaluated. You can change the rule parameters, or prevent rule
	 * evaluation by returning $setting_value['data_source'] = 'none'; 
	 *
	 * @since 1.00
	 *
	 * @param	array 	custom_field_mapping rule
	 * @param	integer post ID to be evaluated
	 * @param	string 	category/scope to evaluate against: custom_field_mapping or single_attachment_mapping
	 * @param	array 	attachment_metadata, default NULL
	 *
	 * @return	array	updated custom_field_mapping rule
	 */
	public static function mla_mapping_rule_filter( $setting_value, $post_ID, $category, $attachment_metadata ) {
		/*
		 * Uncomment the error_log statements in any of the filters to see what's passed in
		 */
		//error_log( 'MLAMediaTaggerExample::mla_mapping_rule_filter $setting_value = ' . var_export( $setting_value, true ), 0 );
		//error_log( 'MLAMediaTaggerExample::mla_mapping_rule_filter $post_ID = ' . var_export( $post_ID, true ), 0 );
		//error_log( 'MLAMediaTaggerExample::mla_mapping_rule_filter $category = ' . var_export( $category, true ), 0 );
		//error_log( 'MLAMediaTaggerExample::mla_mapping_rule_filter $attachment_metadata = ' . var_export( $attachment_metadata, true ), 0 );

		/*
		 * Debugging information is collected in an array. You can store the array in the "Media Tagger" custom field
		 * by uncommenting the "//update_post_meta( $post_ID, 'Media Tagger', var_export( $debug_data, true ) );"
		 * statements at various points in the code. You must also uncomment the $debug_data[] assignments for the
		 * data you want to capture.
		 */
		$debug_data = array();
		$debug_data['post_ID'] = var_export( $post_ID, true );
		$debug_data['setting_value'] = var_export( $setting_value, true );
		$debug_data['category'] = var_export( $category, true );

		/*
		 * Look for the rule we're handling and just return any other rules.
		 *
		 * NOTE: For this code to work you must define a Custom field mapping rule
		 * with the Field Title 'Media Tagger' and a Data Source of "None".
		 */
		if ( 'Media Tagger' !== $setting_value['name'] ) {
			//update_post_meta( $post_ID, 'Media Tagger', var_export( $debug_data, true ) );
			return $setting_value;
		}

		global $wpdb; // The WordPress class of functions for all database manipulations.
		static $all_term_slugs = NULL;

		/*
		 * Build $all_term_slugs once per page load for "Map all attachments" efficiency
		 */
		if ( ( 'custom_field_mapping' == $category ) && ( NULL == $all_term_slugs ) ) {
			$term_values = $wpdb->get_results(
				"SELECT term_taxonomy_id, taxonomy, slug FROM " . $wpdb->term_taxonomy . " as tt INNER JOIN " . $wpdb->terms . " as t ON tt.term_id = t.term_id"
			);
			//error_log( 'MLAMediaTaggerExample::mla_mapping_rule_filter custom_field_mapping $term_values = ' . var_export( $term_values, true ), 0 );
			//$debug_data['term_values'] = var_export( $term_values, true );

			$all_term_slugs = array();
			foreach ( $term_values as $value ) {
				$all_term_slugs[ $value->term_taxonomy_id ]['taxonomy'] = $value->taxonomy;
				$all_term_slugs[ $value->term_taxonomy_id ]['slug'] = $value->slug;
			}
			//error_log( 'MLAMediaTaggerExample::mla_mapping_rule_filter custom_field_mapping $all_term_slugs = ' . var_export( $all_term_slugs, true ), 0 );
			//$debug_data['all_term_slugs'] = var_export( $all_term_slugs, true );
		}

		/*
		 * 1. Select the term_taxonomy_id(s) from the mediatagger table
		 * for the post_ID (passed to the MLA filter).
		 */
		$term_taxonomy_ids = $wpdb->get_col(	$wpdb->prepare(
			"SELECT m.term_taxonomy_id FROM " . $wpdb->prefix . "mediatagger as m WHERE m.object_id = '%d'",
			$post_ID
		) );
		//error_log( 'MLAMediaTaggerExample::mla_mapping_rule_filter get_col $term_taxonomy_ids = ' . var_export( $term_taxonomy_ids, true ), 0 );
		$debug_data['term_taxonomy_ids'] = var_export( $term_taxonomy_ids, true );

		if ( empty( $term_taxonomy_ids ) ) {
			//error_log( 'MLAMediaTaggerExample::mla_mapping_rule_filter empty $term_taxonomy_ids', 0 );
			//update_post_meta( $post_ID, 'Media Tagger', var_export( $debug_data, true ) );
			return $setting_value;
		}

		/*
		 * 2. Select the term_id and taxonomy from the term_taxonomy table
		 * for the term_taxonomy_id. Then, get the "slug" for each term because
		 * the integer term_id won't insert a new term in the destination taxonomy.
		 *
		 * For the "Map all attachments" case, the values are found in the $all_term_slugs array.
		 */
		$new_terms = array();
		if ( ( NULL == $all_term_slugs ) ) {
			$term_taxonomy_values = $wpdb->get_results(	sprintf(
				"SELECT tt.term_id, tt.taxonomy FROM " . $wpdb->term_taxonomy . " as tt WHERE tt.term_taxonomy_id IN ( %s )",
				implode( ',', $term_taxonomy_ids )
			) );
			//error_log( 'MLAMediaTaggerExample::mla_mapping_rule_filter get_results $term_taxonomy_values = ' . var_export( $term_taxonomy_values, true ), 0 );
			$debug_data['term_taxonomy_values'] = var_export( $term_taxonomy_values, true );

			/*
			 * If the MediaTagger values don't match current WordPress values, just give up.
			 */
			if ( empty( $term_taxonomy_values ) ) {
				//error_log( 'MLAMediaTaggerExample::mla_mapping_rule_filter empty $term_taxonomy_values', 0 );
				//update_post_meta( $post_ID, 'Media Tagger', var_export( $debug_data, true ) );
				return $setting_value;
			}

			$term_ids = array();
			foreach ( $term_taxonomy_values as $value ) {
				$term_ids[ $value->term_id ] = $value->term_id;
			}
			//error_log( 'MLAMediaTaggerExample::mla_mapping_rule_filter $term_ids = ' . var_export( $term_ids, true ), 0 );
			$debug_data['term_ids'] = var_export( $term_ids, true );

			$term_values = $wpdb->get_results(	sprintf(
				"SELECT t.term_id, t.slug FROM " . $wpdb->terms . " as t WHERE t.term_id IN ( %s )",
				implode( ',', $term_ids )
			) );
			//error_log( 'MLAMediaTaggerExample::mla_mapping_rule_filter get_results $term_values = ' . var_export( $term_values, true ), 0 );
			$debug_data['term_values'] = var_export( $term_values, true );

			$term_slugs = array();
			foreach ( $term_values as $value ) {
				$term_slugs[ $value->term_id ] = $value->slug;
			}

			foreach ( $term_taxonomy_values as $value ) {
				if ( isset( $term_slugs[ $value->term_id ] ) ) {
					$new_terms[ $value->taxonomy ][] = $term_slugs[ $value->term_id ];
				}
			}
		} else {
			foreach ( $term_taxonomy_ids as $value ) {
				if ( isset( $all_term_slugs[ $value ]['slug'] ) ) {
					$new_terms[ $all_term_slugs[ $value ]['taxonomy'] ][] = $all_term_slugs[ $value ]['slug'];
				}
			}
		}
		//error_log( 'MLAMediaTaggerExample::mla_mapping_rule_filter $new_terms = ' . var_export( $new_terms, true ), 0 );
		$debug_data['new_terms'] = var_export( $new_terms, true );

		/*
		 * 3. Use wp_set_object_terms() to assign the taxonomy and term_id value(s)
		 * to the post_ID. You can either replace any existing terms or append the new terms.
		 *
		 * This code handles the case where MediaTagger assignments come from multiple taxonomies,
		 * i.e., "Tags and Categories". It also lets you change the destination taxonomy for each source.
		 * Uncomment the line(s) in $taxonomy_change and enter the destination taxonomy to re-assign
		 * the destination taxonomy.
		 */
		$taxonomy_change = array (
			// 'post_tag' => 'attachment_tag',
			// 'category' => 'attachment_category'
		);

		$term_arrays = array ();
		foreach ( $new_terms as $key => $value ) {
			if ( isset( $taxonomy_change[ $key ] ) ) {
				$key = $taxonomy_change[ $key ];
			}

			$term_arrays[ $key ] = $value;
		}
		//error_log( "MLAMediaTaggerExample::mla_mapping_rule_filter {$post_ID} term_arrays = " . var_export( $term_arrays, true ), 0 );
		$debug_data['term_arrays'] = var_export( $term_arrays, true );

		foreach( $term_arrays as $key => $value ) {
			/*
			 * Set the last argument to true to append, not replace, existing terms
			 */
			wp_set_object_terms( $post_ID, $value, $key, false );
		}

		/*
		 * 4. Optionally, remove the object_id and term assignments from the
		 * MediaTagger table. Set the if test to true to include this step.
		 */
		if ( false ) {
			$delete_result = $wpdb->delete( $wpdb->prefix . 'mediatagger', array( 'object_id' => $post_ID ), array( '%d' ) );
			//error_log( 'MLAMediaTaggerExample::mla_mapping_rule_filter $delete_result = ' . var_export( $delete_result, true ), 0 );
			$debug_data['delete_result'] = var_export( $delete_result, true );
		}

		/*
		 * Uncomment this line to write debugging information into the 'Media Tagger' custom field
		 */
		//update_post_meta( $post_ID, 'Media Tagger', var_export( $debug_data, true ) );

		/*
		 * $setting_value is an array containing a mapping rule; see above
		 * To stop this rule's evaluation and mapping, return NULL
		 */
		return $setting_value;
	} // mla_mapping_rule_filter
} //MLAMediaTaggerExample

/*
 * Install the filter at an early opportunity
 */
add_action('init', 'MLAMediaTaggerExample::initialize');
?>
<?php
/**
 * Maps taxonomy terms from "template:([+xmp:lr.hierarchicalSubject+])" in EXIF/Template rule.
 *
 * @package MLA Hierarchical Mapping Example
 * @version 1.00
 */

/*
Plugin Name: MLA Hierarchical Mapping Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Maps taxonomy terms from "template:([+xmp:lr.hierarchicalSubject+])" in EXIF/Template rule
Author: David Lingren
Version: 1.00
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
 * Class MLA Hierarchical Mapping Example hooks all of the filters provided by the IPTC/EXIF and Custom Field mapping features
 *
 * @package MLA Hierarchical Mapping Example
 * @since 1.00
 */
class MLAHierarchicalMappingExample {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * Installs filters and actions that handle the MLA hooks for uploading and mapping.
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

		add_filter( 'mla_mapping_settings', 'MLAHierarchicalMappingExample::mla_mapping_settings', 10, 4 );
		add_filter( 'mla_mapping_exif_value', 'MLAHierarchicalMappingExample::mla_mapping_exif_value', 10, 5 );
		add_filter( 'mla_mapping_updates', 'MLAHierarchicalMappingExample::mla_mapping_updates', 10, 5 );
	}

	/**
	 * Save the taxonomy slug(s) for hierarchical mapping
	 *
	 * Array elements are:
	 * 		'post_id' => 0,
	 *		'mla_iptc_metadata' => array(),
	 *		'mla_exif_metadata' => array(),
	 *		'wp_image_metadata' => array(),
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $target_taxonomies = NULL;

	/**
	 * MLA Mapping Settings Filter
	 *
	 * This filter is called before any mapping rules are executed.
	 * You can add, change or delete rules from the array.
	 *
	 * @since 1.00
	 *
	 * @param	array 	mapping rules
	 * @param	integer post ID to be evaluated
	 * @param	string 	category/scope to evaluate against, e.g., custom_field_mapping or single_attachment_mapping
	 * @param	array 	attachment_metadata, default NULL
	 *
	 * @return	array	updated mapping rules
	 */
	public static function mla_mapping_settings( $settings, $post_id, $category, $attachment_metadata ) {
		/*
		 * Go through taxonomy rules (once) looking for hierarchical term lists
		 */
		if ( is_null( self::$target_taxonomies ) && array_key_exists( 'taxonomy', $settings ) ) {
			self::$target_taxonomies = array();
			foreach( $settings['taxonomy'] as $slug => $rule ) {
				if ( false !== strpos( $rule['exif_value'], '[+xmp:lr.hierarchicalSubject+]' ) ) {
					self::$target_taxonomies[ $slug ] = $rule['parent'];
				}
			}
		}

		return $settings;
	} // mla_mapping_settings_filter

	/**
	 * Build and search a cache of taxonomy and term name to term ID mappings
 	 *
	 * @since 1.00
	 *
	 * @param	string 	term name (not slug)
	 * @param	integer zero or term's parent term_id
	 * @param	string 	taxonomy slug
	 *
	 * @return	integer	term_id for the term name
	 */
	private static function _get_term_id( $term_name, $term_parent, $taxonomy ) {
		static $term_cache = array();

		if ( isset( $term_cache[ $taxonomy ] ) && isset( $term_cache[ $taxonomy ][ $term_parent ] ) && isset( $term_cache[ $taxonomy ][ $term_parent ][ $term_name ] ) ) {
			return $term_cache[ $taxonomy ][ $term_parent ][ $term_name ];
		}

		$post_term = term_exists( $term_name, $taxonomy, $term_parent );
		if ( $post_term !== 0 && $post_term !== NULL ) {
			$term_cache[ $taxonomy ][ $term_parent ][ $term_name ] = $post_term['term_id'];
			return $post_term['term_id'];
		}

		$post_term = wp_insert_term( $term_name, $taxonomy, array( 'parent' => $term_parent ) );
		if ( ( ! is_wp_error( $post_term ) ) && isset( $post_term['term_id'] ) ) {
			$term_cache[ $taxonomy ][ $term_parent ][ $term_name ] = $post_term['term_id'];
			return $post_term['term_id'];
		}

		return 0;
	} // _get_term_id

	/**
	 * MLA Mapping EXIF Value Filter
	 *
	 * This filter is called once for each IPTC/EXIF mapping rule, after the EXIF 
	 * portion of the rule is evaluated. You can change the new value produced by
	 * the rule.
	 *
	 * @since 1.00
	 *
	 * @param	mixed 	EXIF/Template value returned by the rule
	 * @param	array 	custom_field_mapping rule
	 * @param	integer post ID to be evaluated
	 * @param	string 	category/scope to evaluate against: iptc_exif_standard_mapping, iptc_exif_taxonomy_mapping or iptc_exif_custom_mapping
	 * @param	array 	attachment_metadata, default NULL
	 *
	 * @return	array	updated rule EXIF/Template value
	 */
	public static function mla_mapping_exif_value( $exif_value, $setting_value, $post_id, $category, $attachment_metadata ) {
		/*
		 * Process the taxonomy rules that contain hierarchical term lists
		 */
		if ( in_array( $category, array( 'iptc_exif_mapping', 'iptc_exif_taxonomy_mapping' ) ) && array_key_exists( $setting_value, self::$target_taxonomies ) ) {
			// Convert single entries to an array for convenience
			if ( is_string( $exif_value ) ) {
				$exif_value = array( $exif_value );
			}
			
			$term_ids = array();
			$root_names = array();
			foreach ( $exif_value as $term_list ) {
				$term_parent = self::$target_taxonomies[ $setting_value ];
				$term_array = explode( '|', $term_list );
				$root_names[] = $term_array[0];
				foreach ( $term_array as $term_name ) {
					$term_id = self::_get_term_id( $term_name, $term_parent, $setting_value );
					if ( $term_id ) {
						$term_ids[] = $term_parent = $term_id;
					}
				}
			}

			/*
			 * Use the root names as a place holder so something will be present in the item updates.
			 * All of the values will be added back in the mla_mapping_updates() filter.
			 */
			$exif_value = $root_names;
			self::$all_terms[ $post_id ][ $setting_value ] = $term_ids;
		}
		
		return $exif_value;
	} // mla_mapping_exif_value_filter

	/**
	 * Share the complete termlist between mla_mapping_exif_value_filter() and mla_mapping_updates()
	 *
	 * @since 1.00
	 *
	 * @var	array	( post_id => array ( term_ids ) )
	 */
	private static $all_terms = array();

	/**
	 * MLA Mapping Updates Filter
	 *
	 * This filter is called AFTER all mapping rules are applied.
	 * You can add, change or remove updates for the attachment's
	 * standard fields, taxonomies and/or custom fields.
	 *
	 * @since 1.00
	 *
	 * @param	array	updates for the attachment's standard fields, taxonomies and/or custom fields
	 * @param	integer post ID to be evaluated
	 * @param	string 	category/scope to evaluate against: custom_field_mapping or single_attachment_mapping
	 * @param	array 	mapping rules
	 * @param	array 	attachment_metadata, default NULL
	 *
	 * @return	array	updated attachment's updates
	 */
	public static function mla_mapping_updates( $updates, $post_id, $category, $settings, $attachment_metadata ) {
		/*
		 * Replace the root terms with the full list.
		 */
		if ( isset( self::$all_terms[ $post_id ] ) ) {
			foreach( self::$all_terms[ $post_id ] as $taxonomy => $terms ) {
				$updates['taxonomy_updates']['inputs'][ $taxonomy ] = $terms;
			}

			unset( self::$all_terms[ $post_id ] );
		}
		
		return $updates;
	} // mla_mapping_updates_filter
} //MLAHierarchicalMappingExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLAHierarchicalMappingExample::initialize');
?>
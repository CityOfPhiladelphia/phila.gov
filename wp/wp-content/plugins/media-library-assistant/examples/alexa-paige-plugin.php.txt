<?php
/**
 * Provides support for [mla_gallery] BuddyPress URLs, simplified taxonomy queries
 * and multi-taxonomy and term checkbox queries.
 *
 * In this plugin:
 *
 * - The [axp_checkbox_form] shortcode generates a form with checkboxes for taxonomies and terms.
 * 
 * - The [axp_checkbox_form] results are used to add a "tax_query" parameter to any [mla_gallery]
 * shortcode with a "checkbox_query=true" parameter. The tax_query will select any term in any
 * taxonomy for inclusion in the [mla_gallery] results.
 *
 * - The WordPress "attachment/media page" links are replaced by "BuddyPress/rtMedia page" 
 * links. For audio and video files, an option is provided to substitute the "cover_art"
 * thumbnail image for the item Title in the thumbnail_content.
 *
 * - The "single_query()" and "double_query()" functions provide simplified, higher-performance
 * alternatives to the standard WordPress tax_query.
 *
 * @package Alexa Paige Plugin for BuddyPress & rtMedia
 * @version 1.02
 */

/*
Plugin Name: Alexa Paige Plugin for BuddyPress & rtMedia
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Supports BuddyPress URLs and multi-taxonomy and term checkbox queries
Author: David Lingren
Version: 1.02
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
 * Class Alexa Paige Plugin hooks several of the filters provided by the [mla_gallery] shortcode
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding
 * everything else inside a class means this is the only name you have to worry about.
 *
 * @package Alexa Paige Plugin for BuddyPress & rtMedia
 * @since 1.00
 */
class AlexaPaigePlugin {
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
		 * Add the custom shortcode for generating the checkbox form
		 */
		add_shortcode( 'axp_checkbox_form', 'AlexaPaigePlugin::axp_checkbox_form' );

		/*
		 * add_filter parameters:
		 * $tag - name of the hook you're filtering; defined by [mla_gallery]
		 * $function_to_add - function to be called when [mla_gallery] applies the filter
		 * $priority - default 10; lower runs earlier, higher runs later
		 * $accepted_args - number of arguments your function accepts
		 */
		add_filter( 'mla_gallery_attributes', 'AlexaPaigePlugin::axp_gallery_attributes_filter', 10, 1 );
		add_filter( 'mla_gallery_arguments', 'AlexaPaigePlugin::axp_gallery_arguments_filter', 10, 1 );
		add_filter( 'mla_gallery_query_attributes', 'AlexaPaigePlugin::axp_gallery_query_attributes_filter', 10, 1 );
		add_filter( 'mla_gallery_query_arguments', 'AlexaPaigePlugin::axp_gallery_query_arguments_filter', 10, 1 );
		add_action( 'mla_gallery_wp_query_object', 'AlexaPaigePlugin::axp_gallery_wp_query_object_action', 10, 1 );
		add_filter( 'mla_gallery_item_values', 'AlexaPaigePlugin::axp_gallery_item_values_filter', 10, 1 );
	}

	/**
	 * AXP Checkbox Form shortcode
	 *
	 * This shortcode generates an HTML form with checkbox controls for
	 * taxonomy and term choices.
	 *
	 * @since 1.00
	 *
	 * @param	array	the shortcode parameters
	 *
	 * @return	string	HTML markup for the generated form
	 */
	public static function axp_checkbox_form( $attr ) {
		//error_log( 'AlexaPaigePlugin::axp_checkbox_form $attr = ' . var_export( $attr, true ), 0 );

		// Extract the checkbox_query parameters in $_REQUEST, so we can check the boxes
		if ( isset( $_REQUEST['axp_taxonomies'] ) ) {
			$axp_taxonomies = $_REQUEST['axp_taxonomies'];
			if ( is_string( $axp_taxonomies ) ) {
				$axp_taxonomies = explode( ',', $axp_taxonomies );
			}
		} else {
			$axp_taxonomies = array();
		}

		if ( isset( $_REQUEST['axp_terms'] ) ) {
			$axp_terms = $_REQUEST['axp_terms'];
			if ( is_string( $axp_terms ) ) {
				$axp_terms = explode( ',', $axp_terms );
			}
		} else {
			$axp_terms = array();
		}
		//error_log( 'AlexaPaigePlugin::axp_checkbox_form $axp_taxonomies = ' . var_export( $axp_taxonomies, true ), 0 );
		//error_log( 'AlexaPaigePlugin::axp_checkbox_form $axp_terms = ' . var_export( $axp_terms, true ), 0 );

		// Make sure $attr is an array, even if it's empty
		if ( empty( $attr ) ) {
			$attr = array();
		} elseif ( is_string( $attr ) ) {
			$attr = shortcode_parse_atts( $attr );
		}

		// These are the default parameters.
		$defaults = array(
			'taxonomy' => 'rt_male,rt_female',
			'include' => NULL,
			'exclude' => NULL,
			'taxonomy_label' => 'Show&nbsp;me:',
			'term_label' => 'In&nbsp;categories',
			'action' => '.',
		);

		/*
		 * Look for 'request' substitution parameters,
		 * which can be added to any input parameter
		 */
		foreach ( $attr as $attr_key => $attr_value ) {
			// Only expand our own parameters
			if ( array_key_exists( $attr_key, $defaults ) ) {
				$attr_value = str_replace( '{+', '[+', str_replace( '+}', '+]', $attr_value ) );
				$replacement_values = MLAData::mla_expand_field_level_parameters( $attr_value );

				if ( ! empty( $replacement_values ) ) {
					$attr[ $attr_key ] = MLAData::mla_parse_template( $attr_value, $replacement_values );
				}
			}
		}

		$arguments = shortcode_atts( $defaults, $attr );
		//error_log( 'AlexaPaigePlugin::axp_checkbox_form $arguments = ' . var_export( $arguments, true ), 0 );

		// Convert include and exclude arrays, if provided
		$include = array();
		$exclude = array();
		if ( is_string( $arguments['include'] ) ) {
			$include = explode( ',', $arguments['include'] );
		} elseif ( is_string( $arguments['exclude'] ) ) {
			$exclude = explode( ',', $arguments['exclude'] );
		}

		// Build the term list
		$taxonomies = explode( ',', $arguments['taxonomy'] );
		$display_taxonomies = array();
		$terms = array();
		$args = array( 'hide_empty' => false, 'exclude' => $exclude, 'include' => $include, );
		foreach( $taxonomies as $key => $taxonomy ) {
			$term_objects = get_terms( $taxonomy, $args );
			//error_log( "AlexaPaigePlugin::axp_checkbox_form term_objects [ {$taxonomy} ] = " . var_export( $term_objects, true ), 0 );
			if ( is_array( $term_objects ) ) {
				// Get the name for display purposes
				$display_taxonomies[ $taxonomy ] = get_taxonomy( $taxonomy );

				foreach ( $term_objects as $term ) {
					// Keep only the fields we want and index by slug to identify duplicates
					if ( isset( $terms[ $term->slug ] ) ) {
						$terms[ $term->slug ]->ttids[] = $term->term_taxonomy_id;
					} else {
						$terms[ $term->slug ] = (object) array( 'term_id' => $term->term_id, 'name' => $term->name, 'ttids' => array ( $term->term_taxonomy_id ) );
					}
				}
			} else {
				// Remove bad values from the list
				unset( $taxonomies[ $key ] );
			}
		} // foreach taxonomy
		//error_log( 'AlexaPaigePlugin::axp_checkbox_form $terms = ' . var_export( $terms, true ), 0 );

		// Re-organize by term name for display purposes
		$display_terms = array ();
		foreach ( $terms as $key => $term ) {
			/*
			 * A term Label has multiple slug/term_id values if
			 * it is a child of multiple parents in one taxonomy.
			 *
			 * A term has multiple term_taxonomy_id values if
			 * it appears in multiple taxonomies.
			 */
			if ( isset( $display_terms[ $term->name ] ) ) {
				$display_terms[ $term->name ] .= ',' . $key . ':' . $term->term_id . ':' . implode( '/', $term->ttids );
			} else {
				$display_terms[ $term->name ] = $key . ':' . $term->term_id . ':' . implode( '/', $term->ttids );
			}
		}
		//error_log( 'AlexaPaigePlugin::axp_checkbox_form $display_terms = ' . var_export( $display_terms, true ), 0 );

		// Compose the form
		$output  = '<form id="axp-checkbox-form" action="' . $arguments['action'] . '" method="get">' . "\n";
		$output .= "<table>\n";
		$output .= "<tr><td width=1%>\n";
		$output .= $arguments['taxonomy_label'] . "\n";
		$output .= "</td><td>\n";

		foreach ( $display_taxonomies as $key => $taxonomy ) {
			$checked = ( in_array( $key, $axp_taxonomies ) ) ? 'checked=checked ' : '';
			$output .= sprintf( '<input name="axp_taxonomies[]" id="axp-taxonomy-%1$s" type="checkbox" %2$svalue="%1$s">%3$s&nbsp;&nbsp;', $key, $checked, $taxonomy->label );
		}

		$output .= "\n</td></tr>\n";
		$output .= "<tr><td width=1%>\n";
		$output .= $arguments['term_label'] . "\n";
		$output .= "</td><td>\n";

		foreach ( $display_terms as $key => $term ) {
			$checked = ( in_array( esc_attr( $term ), $axp_terms ) ) ? 'checked=checked ' : '';
			$output .= sprintf( '<input name="axp_terms[]" id="axp-term-%1$s" type="checkbox" %2$svalue="%3$s">%4$s&nbsp;&nbsp;', sanitize_title( $term ), $checked, esc_attr( $term ), $key );
		}

		$output .= "\n</td></tr>\n";
		$output .= "</table>\n";
		$output .= '<input id="axp-checkbox-form-submit" name="axp-checkbox-form-submit" type="submit" value="GO" />
' . "\n";
		$output .= "</form>\n";

		//error_log( 'AlexaPaigePlugin::axp_checkbox_form $output = ' . var_export( $output, true ), 0 );
		return $output;
	} // axp_checkbox_form

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
	 * This filter gives you an opportunity to record or modify the arguments passed in
	 * to the shortcode before they are merged with the default arguments used for the
	 * gallery display.
	 *
	 * The $shortcode_attributes array is where you will find any of your own parameters
	 * that are coded in the shortcode, e.g., [mla_gallery my_parameter="my value"].
	 *
	 * @since 1.00
	 *
	 * @param	array	the shortcode parameters passed in to the shortcode
	 *
	 * @return	array	updated shortcode attributes
	 */
	public static function axp_gallery_attributes_filter( $shortcode_attributes ) {
		//error_log( 'AlexaPaigePlugin::axp_gallery_attributes_filter $shortcode_attributes = ' . var_export( $shortcode_attributes, true ), 0 );

		// Save the attributes for use in the later filters
		self::$shortcode_attributes = $shortcode_attributes;

		/*
		 * The [axp_checkbox_form] results are used to add/modify a "my_custom_sql" parameter to any
		 * [mla_gallery] shortcode with a "checkbox_query=true" parameter. The resulting query will
		 * select any term in any taxonomy for inclusion in the [mla_gallery] results.
 		 *
		 * We use the 'checkbox_query' shortcode parameter to apply this filter on a
		 * gallery-by-gallery basis, leaving other [mla_gallery] instances untouched. If the
		 * 'checkbox_query' parameter is not present, we have nothing to do. If the parameter
		 * IS present, we use the relevant $_REQUEST parameters to add/modify a "my_custom_sql"
		 * parameter for the shortcode parameters.
		 */		
		if ( isset( self::$shortcode_attributes['checkbox_query'] ) ) {
			$checkbox_query = strtolower( trim( self::$shortcode_attributes['checkbox_query'] ) );
		} else {
			$checkbox_query = 'false';
		}

		if ( in_array( $checkbox_query, array( 'true', 'tax' ) ) ) {
			//error_log( 'AlexaPaigePlugin::axp_gallery_attributes_filter self::$shortcode_attributes = ' . var_export( self::$shortcode_attributes, true ), 0 );
			//error_log( 'AlexaPaigePlugin::axp_gallery_attributes_filter $_REQUEST = ' . var_export( $_REQUEST, true ), 0 );

			// Extract the checkbox_query parameters
			if ( isset( $_REQUEST['axp_taxonomies'] ) ) {
				$axp_taxonomies = $_REQUEST['axp_taxonomies'];
				if ( is_string( $axp_taxonomies ) ) {
					$axp_taxonomies = explode( ',', $axp_taxonomies );
				}
			} else {
				$axp_taxonomies = array();
			}

			if ( isset( $_REQUEST['axp_terms'] ) ) {
				$axp_terms = $_REQUEST['axp_terms'];
				if ( is_string( $axp_terms ) ) {
					$axp_terms = explode( ',', $axp_terms );
				}

				// Convert to term_id values
				$terms = array();
				foreach ( $axp_terms as $key => $axp_term ) {
					// A term Label has multiple slug:term_id values if it is a child of multiple parents
					//$terms = explode( ',', $axp_term );
					foreach ( explode( ',', $axp_term ) as $term ) {
						$parts = explode( ':', $term );
						if ( 'tax' == $checkbox_query ) {
							$terms[] = absint( $parts[1] );
						} else {
							$terms[] = trim( $parts[0] );
						}
					}
				}
			} else {
				$terms = array();
			}
			//error_log( 'AlexaPaigePlugin::axp_gallery_attributes_filter $axp_taxonomies = ' . var_export( $axp_taxonomies, true ), 0 );
			//error_log( 'AlexaPaigePlugin::axp_gallery_attributes_filter $terms = ' . var_export( $terms, true ), 0 );


			// Generate a query to find the $terms in any of the $axp_taxonomies
			if ( empty( $axp_taxonomies ) || empty( $terms ) ) {
				self::$shortcode_attributes['checkbox_query'] = "array( array( 'taxonomy' => 'none', 'field' => 'slug', 'terms' => 'none' ) )";
			} else {
				if ( 'tax' == $checkbox_query ) {
					$value = array( 'relation' => 'OR', );
					foreach( $axp_taxonomies as $taxonomy ) {
						$value[] = array(
							'taxonomy' => $taxonomy,
							'field'    => 'id',
							'terms'    => $terms,
						);
					}

					self::$shortcode_attributes['checkbox_query'] = $value;
				} else {
					// Add/modify the my_custom_sql parameter
					if ( isset( self::$shortcode_attributes['my_custom_sql'] ) ) {
						$my_query_vars = self::$shortcode_attributes['my_custom_sql'];
//error_log( 'AlexaPaigePlugin::axp_gallery_attributes_filter checkbox_query self::$shortcode_attributes[my_custom_sql] = ' . var_export( $my_query_vars, true ), 0 );
						if ( empty( $my_query_vars ) ) {
							$my_query_vars = array();
						} elseif ( is_string( $my_query_vars ) ) {
							$my_query_vars = shortcode_parse_atts( $my_query_vars );
						}
					} else {
						$my_query_vars = array();
					}

//error_log( 'AlexaPaigePlugin::axp_gallery_attributes_filter checkbox_query $my_query_vars = ' . var_export( $my_query_vars, true ), 0 );
					$new_query_vars = array();
					foreach( $my_query_vars as $key => $value ) {
						$new_query_vars[] = $key . "='" . $value . "'";
					}

					foreach( $axp_taxonomies as $taxonomy ) {
						$new_query_vars[] = $taxonomy . "='" . implode( ',', $terms ) . "'";
					}

					unset( self::$shortcode_attributes['checkbox_query'] );
					self::$shortcode_attributes['my_custom_sql'] = implode( ' ', $new_query_vars );
				} // my_custom_sql query
			} // have taxonomies and terms
//error_log( 'AlexaPaigePlugin::axp_gallery_attributes_filter checkbox_query self::$shortcode_attributes = ' . var_export( self::$shortcode_attributes, true ), 0 );
		} // valid checkbox_query

		return $shortcode_attributes;
	} // axp_gallery_attributes_filter

	/**
	 * MLA Gallery (Display) Arguments
	 *
	 * This filter gives you an opportunity to record or modify the gallery display arguments
	 * after the shortcode attributes are merged with the default arguments.
	 *
	 * Note that the values in this array are input or default values, not the final computed
	 * values used for the gallery display.
	 *
	 * @since 1.01
	 *
	 * @param	array	shortcode arguments merged with gallery display defaults, so every possible parameter is present
	 *
	 * @return	array	updated gallery display arguments
	 */
	public static function axp_gallery_arguments_filter( $all_display_parameters ) {
		if ( isset( self::$shortcode_attributes['my_custom_sql'] ) ) {
			/*
			 * Determine output type; if it's pagination, count the rows and add the result
			 * to the parameters. See the "single_query()" and "double_query()" functions.
			 */
			$output_parameters = array_map( 'strtolower', array_map( 'trim', explode( ',', $all_display_parameters['mla_output'] ) ) );
			$is_pagination = in_array( $output_parameters[0], array( 'previous_page', 'next_page', 'paginate_links' ) ); 

			if ( $is_pagination ) {
				// Determine query type
				if ( isset( self::$shortcode_attributes['post_mime_type'] ) ) {
					$is_double = true;
				} else {
					$my_query_vars = self::$shortcode_attributes['my_custom_sql'];
					if ( empty( $my_query_vars ) ) {
						$my_query_vars = array();
					} elseif ( is_string( $my_query_vars ) ) {
						$my_query_vars = shortcode_parse_atts( $my_query_vars );
					}

					if ( isset( $my_query_vars['order'] ) || isset( $my_query_vars['orderby'] ) ) {
						$is_double = true;
					} else {
						$is_double = false;
					}
				}

				if ( $is_double ) {
					$all_display_parameters['mla_paginate_rows'] = self::double_query( NULL, true );
				} else {
					$all_display_parameters['mla_paginate_rows'] = self::single_query( NULL, true );
				}
			}
		} // my_custom_sql present

		//error_log( 'AlexaPaigePlugin::axp_gallery_arguments_filter $all_display_parameters = ' . var_export( $all_display_parameters, true ), 0 );

		return $all_display_parameters;
	} // axp_gallery_arguments_filter

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
	 * This filter gives you an opportunity to record or modify the arguments passed in to the
	 * shortcode before they are merged with the default arguments used to select attachments
	 * for the gallery.
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
	public static function axp_gallery_query_attributes_filter( $query_attributes ) {
		//error_log( 'AlexaPaigePlugin::axp_gallery_query_attributes_filter $query_attributes = ' . var_export( $query_attributes, true ), 0 );

		if ( isset( self::$shortcode_attributes['checkbox_query'] ) ) {
			$query_attributes['tax_query'] = self::$shortcode_attributes['checkbox_query'];
		} // valid checkbox_query=tax paraneter

		self::$query_attributes = $query_attributes;
		return $query_attributes;
	} // axp_gallery_query_attributes_filter

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
	public static function axp_gallery_query_arguments_filter( $all_query_parameters ) {
		/*
		 * This example executes a custom SQL query that is much simpler than the usual WordPress
		 * WP_Query arguments. See the "single_query()" and "double_query()" functions.
		 *
		 * We use a shortcode parameter of our own to apply this filter on a gallery-by-gallery
		 * basis, leaving other [mla_gallery] instances untouched. If the "my_custom_sql"
		 * parameter is not present, we have nothing to do. If the parameter IS present,
		 * single_query() or double_query() extracts  taxonomy values, then builds a custom
		 * query that does a simple, high-performance search.
		 *
		 * The "my_custom_sql" parameter accepts these query arguments:
		 * - one or more taxonomy=slug(,slug)... arguments, which will be joined by OR
		 * - include_children=true
		 * - order and/or orderby
		 *
		 * The shortcode can also contain the post_mime_type parameter to further filter the results.
		 */		
		if ( isset( self::$shortcode_attributes['my_custom_sql'] ) ) {
			// Determine query type
			if ( isset( self::$shortcode_attributes['post_mime_type'] ) ) {
				$is_double = true;
			} else {
				$my_query_vars = self::$shortcode_attributes['my_custom_sql'];
				if ( empty( $my_query_vars ) ) {
					$my_query_vars = array();
				} elseif ( is_string( $my_query_vars ) ) {
					$my_query_vars = shortcode_parse_atts( $my_query_vars );
				}

				if ( isset( $my_query_vars['order'] ) || isset( $my_query_vars['orderby'] ) ) {
					$is_double = true;
				} else {
					$is_double = false;
				}
			}

			if ( $is_double ) {
				$all_query_parameters = self::double_query( $all_query_parameters );
			} else {
				$all_query_parameters = self::single_query( $all_query_parameters );
			}
//error_log( 'AlexaPaigePlugin::axp_gallery_query_arguments_filter $is_double = ' . var_export( $is_double, true ), 0 );
		}

		//error_log( 'AlexaPaigePlugin::axp_gallery_query_arguments_filter $all_query_parameters = ' . var_export( $all_query_parameters, true ), 0 );
		return $all_query_parameters;
	} // axp_gallery_query_arguments_filter

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
	 * This action gives you an opportunity (read-only) to record anything you need from the
	 * WP_Query object used to select the attachments for gallery display. This is the ONLY
	 * point at which the WP_Query object is defined.
	 *
	 * If the "buddypress_urls" parameter is present, we add information from the rt_rtm_media
	 * table to the self::$wp_query_properties array to be used in the
	 * axp_gallery_item_values_filter.
	 *
	 * @since 1.00
	 * @uses MLAShortcodes::$mla_gallery_wp_query_object
	 *
	 * @param	array	query arguments passed to WP_Query->query
	 *
	 * @return	void	actions never return anything
	 */
	public static function axp_gallery_wp_query_object_action( $query_arguments ) {
		//error_log( 'AlexaPaigePlugin::axp_gallery_wp_query_object_action $query_arguments = ' . var_export( $query_arguments, true ), 0 );

		self::$wp_query_properties = array();
		self::$wp_query_properties ['post_count'] = MLAShortcodes::$mla_gallery_wp_query_object->post_count;

		// If the "buddypress_urls" parameter is not present, we have nothing to do.
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

		//error_log( 'AlexaPaigePlugin::axp_gallery_wp_query_object_action $post_info = ' . var_export( $post_info, true ), 0 );
		self::$wp_query_properties ['post_info'] = $post_info;

		// Unlike Filters, Actions never return anything
		return;
	} // axp_gallery_wp_query_object_action

	/**
	 * MLA Gallery Item Values
	 *
	 * @since 1.00
	 *
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated substitution parameter name => value pairs
	 */
	public static function axp_gallery_item_values_filter( $item_values ) {
		//error_log( 'AlexaPaigePlugin::axp_gallery_item_values_filter $item_values = ' . var_export( $item_values, true ), 0 );

		/*
		 * Look for the custom "prettydate" substitution parameter in the caption.
		 */
		if ( preg_match_all( '/\[\+prettydate[ ]?(.*)\+\]/', $item_values['caption'], $matches /*, PREG_OFFSET_CAPTURE*/ ) ) {
//error_log( 'AlexaPaigePlugin::axp_gallery_item_values_filter $matches = ' . var_export( $matches, true ), 0 );
			/*
			 * $matches[0][0] contains the complete match.
			 * $matches[1][0] contains the format mask.
			 */
			 if ( empty( $matches[1][0] ) ) {
				 $format = 'F d, Y';
			 } else {
				 $format = $matches[1][0];
			 }
//error_log( 'AlexaPaigePlugin::axp_gallery_item_values_filter $format = ' . var_export( $format, true ), 0 );
			 
			 /*
			 * Default format is YYYY-MM-DD HH:MM:SS (HH = 00 - 23), or 'Y-m-d H:i:s'
			 * Convert to UNIX timestamp so any reformat is possible
			 */
			$old_date = $item_values['date'];
			$timestamp = mktime( substr( $old_date, 11, 2 ), substr( $old_date, 14, 2 ), substr( $old_date, 17, 2 ), substr( $old_date, 5, 2 ), substr( $old_date, 8, 2 ), substr( $old_date, 0, 4 ) );

//error_log( 'AlexaPaigePlugin::axp_gallery_item_values_filter date = ' . var_export( date( $format, $timestamp ), true ), 0 );
			/*
			 * Update the caption, replacing the substitution parameter with the formatted value.
			 */
			$item_values['caption'] = str_replace( $matches[0][0], date( $format, $timestamp ), $item_values['caption'] );
		}

		/*
		 * We use a shortcode parameter of our own to apply our filters on a gallery-by-gallery
		 * basis, leaving other [mla_gallery] instances untouched. If the "buddypress_urls"
		 * parameter is not present, we have nothing to do.
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
	} // axp_gallery_item_values_filter

	/**
	 * Custom query support function, taxonomy terms only
	 *
	 * Calculates found_rows for pagination or included attachments for gallery.
	 *
	 * The queries supported in this function's "my_custom_sql" parameter include:
	 *
	 * - one or more taxonomy term lists, with include_children
	 *
	 * @since 1.01
	 *
	 * @param	array	shortcode arguments merged with attachment selection defaults, so every possible parameter is present
	 * @param	boolean	true for pagination result, false for gallery result
	 *
	 * @return	integer|array	found_rows or updated query parameters
	 */
	private static function single_query( $all_query_parameters, $is_pagination = false ) {
		global $wpdb;
		//error_log( 'AlexaPaigePlugin::single_query $all_query_parameters = ' . var_export( $all_query_parameters, true ), 0 );
		//error_log( 'AlexaPaigePlugin::single_query $is_pagination = ' . var_export( $is_pagination, true ), 0 );

		/*
		 * This example executes a custom SQL query that is much simpler than the usual
		 * WordPress WP_Query arguments.
		 *
		 * For pagination controls, the number of terms satisfying the query parameters is returned.
		 * For gallery display, the query results are fed back to the [mla_gallery] shortcode as a
		 * list of attachments using the "include" parameter.
		 *
		 * The simplification relies on the assumption that the taxonomy and term(s) given as input
		 * are ONLY used for Media Library image items, so we can omit the term_relationships/posts 
		 * JOIN clause for tests on post_mime_type, post_type and post_status.
		 */		

		// Make sure $my_query_vars is an array, even if it's empty
		$my_query_vars = self::$shortcode_attributes['my_custom_sql'];
		if ( empty( $my_query_vars ) ) {
			$my_query_vars = array();
		} elseif ( is_string( $my_query_vars ) ) {
			$my_query_vars = shortcode_parse_atts( $my_query_vars );
		}

		// Start with empty parameter values
		$ttids = array();

		// Find taxonomy argument, if present, and collect terms
		$taxonomies = get_taxonomies( array( 'object_type' => array( 'attachment' ) ), 'names' );
		foreach( $taxonomies as $taxonomy ) {
			if ( empty( $my_query_vars[ $taxonomy ] ) ) {
				continue;
			}

			// Found the taxonomy; collect the terms
			$include_children =  isset( $my_query_vars['include_children'] ) && 'true' == strtolower( trim( $my_query_vars['include_children'] ) );

			// Allow for multiple term slug values
			$terms = array();
			$slugs = explode( ',', $my_query_vars[ $taxonomy ] );
			foreach ( $slugs as $slug ) {
				$args = array( 'slug' => $slug, 'hide_empty' => false );
				$terms = array_merge( $terms, get_terms( $taxonomy, $args ) );
			}

			foreach( $terms as $term ) {
				// Index by ttid to remove duplicates
				$ttids[ $term->term_taxonomy_id ] = $term->term_taxonomy_id;

				if ( $include_children ) {
					$args = array( 'child_of' => $term->term_id, 'hide_empty' => false );
					$children = get_terms( 'attachment_category', $args );
					foreach( $children as $child ) {
						$ttids[] = $child->term_taxonomy_id;
					}
				} // include_children
			} // $term

			break;
		}

		// Build an array of SQL clauses
		$query = array();
		$query_parameters = array();

		if ( $is_pagination ) {
			$query[] = "SELECT COUNT( DISTINCT object_id ) FROM {$wpdb->term_relationships} as tr";
		} else {
			$query[] = "SELECT DISTINCT tr.object_id FROM {$wpdb->term_relationships} as tr";
		}

		$placeholders = array();
		if ( ! empty( $ttids ) ) {
			foreach ( $ttids as $ttid ) {
				$placeholders[] = '%s';
				$query_parameters[] = $ttid;
			}
		} else {
				$placeholders[] = '%s';
				$query_parameters[] = '0';
		}

		$query[] = 'WHERE ( tr.term_taxonomy_id IN (' . join( ',', $placeholders ) . ') )';

		// ORDER BY clause would go here, if needed

		if ( ! $is_pagination ) {
			/*
			 * Add pagination to our query, then remove it from the query
			 * that WordPress will process after we're done.
			 * MLA pagination will override WordPress pagination
			 */
			$current_page = self::$shortcode_attributes['mla_page_parameter'];
			if ( ! empty( $all_query_parameters[ $current_page ] ) ) {
				if ( isset( $all_query_parameters['mla_paginate_total'] ) && ( $all_query_parameters[ $current_page ] > $all_query_parameters['mla_paginate_total'] ) ) {
					$paged = 0xFFFF; // suppress further output
				} else {
					$paged = $all_query_parameters[ $current_page ];
				}
			} else {
				$paged = $all_query_parameters['paged'];
			}

			if ( empty( $paged ) ) {
				$paged = 1;
			} elseif ( 'current' == strtolower( $paged ) ) {
				/*
				 * Note: The query variable 'page' holds the pagenumber for a single paginated
				 * Post or Page that includes the <!--nextpage--> Quicktag in the post content. 
				 */
				if ( get_query_var( 'page' ) ) {
					$paged = get_query_var( 'page' );
				} else {
					$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
				}
			} elseif ( is_numeric( $paged ) ) {
				$paged = absint( $paged );
			} elseif ( '' === $paged ) {
				$paged = 1;
			}

			$limit = absint( ! empty( $all_query_parameters['posts_per_page'] ) ? $all_query_parameters['posts_per_page'] : $all_query_parameters['numberposts'] );
			$offset = $limit * ( $paged - 1);
			if ( 0 < $offset && 0 < $limit ) {
				$query[] = 'LIMIT %d, %d';
				$query_parameters[] = $offset;
				$query_parameters[] = $limit;
			} elseif ( 0 < $limit ) {
				$query[] = 'LIMIT %d';
				$query_parameters[] = $limit;
			} elseif ( 0 < $offset ) {
				$query[] = 'LIMIT %d, %d';
				$query_parameters[] = $offset;
				$query_parameters[] = 0x7FFFFFFF; // big number!
			}

			$all_query_parameters['nopaging'] = true;
			$all_query_parameters['numberposts'] = 0;
			$all_query_parameters['posts_per_page'] = 0;
			$all_query_parameters['paged'] = NULL;
			$all_query_parameters['offset'] = NULL;
			$all_query_parameters[ $current_page ] = NULL;
			$all_query_parameters['mla_paginate_total'] = NULL ;
		} // ! is_pagination

		$query =  join(' ', $query);
//error_log( 'AlexaPaigePlugin::single_query $query = ' . var_export( $query, true ), 0 );
//error_log( 'AlexaPaigePlugin::single_query $query_parameters = ' . var_export( $query_parameters, true ), 0 );
		if ( $is_pagination ) {
			$count = $wpdb->get_var( $wpdb->prepare( $query, $query_parameters ) );
			//error_log( 'AlexaPaigePlugin::single_query $count = ' . var_export( $count, true ), 0 );
			return $count;
		}

		$ids = $wpdb->get_results( $wpdb->prepare( $query, $query_parameters ) );
//error_log( 'AlexaPaigePlugin::single_query $ids = ' . var_export( $ids, true ), 0 );
		if ( is_array( $ids ) ) {
			$includes = array();
			foreach ( $ids as $id ) {
				$includes[] = $id->object_id;
			}
			$all_query_parameters['include'] = implode( ',', $includes );
		} else {
			$all_query_parameters['include'] = '1'; // return no images
		}

		//error_log( 'AlexaPaigePlugin::single_query $all_query_parameters = ' . var_export( $all_query_parameters, true ), 0 );
		return $all_query_parameters;
	} // single_query

	/**
	 * Custom query support function, taxonomy terms plus post_mime_type and orderby/order fields
	 *
	 * For pagination controls, the number of terms satisfying the query parameters is returned.
	 * For gallery display, the query results are fed back to the [mla_gallery] shortcode as a
	 * list of attachments using the "include" parameter.
	 *
	 * The queries supported in this function's "my_custom_sql" parameter include:
	 *
	 * - one or more taxonomy term lists, with include_children
	 * - one or more post_mime_types
	 * - ORDER BY post table fields
	 *
	 * @since 1.01
	 *
	 * @param	array	shortcode arguments merged with attachment selection defaults, so every possible parameter is present
	 * @param	boolean	true for pagination result, false for gallery result
	 *
	 * @return	integer|array	found_rows or updated query parameters
	 */
	private static function double_query( $all_query_parameters, $is_pagination = false ) {
		global $wpdb;
		//error_log( 'AlexaPaigePlugin::double_query $all_query_parameters = ' . var_export( $all_query_parameters, true ), 0 );
		//error_log( 'AlexaPaigePlugin::double_query $is_pagination = ' . var_export( $is_pagination, true ), 0 );

		/*
		 * This example executes two custom SQL queries that are more efficient than the usual
		 * WordPress WP_Query arguments.
		 *
		 * The first query is on taxonomy and term(s) only, yielding a list of object_id (post ID) values.
		 * The second query filters the list by post_mime_type, orders it and paginates it.
		 */		

		// Make sure $my_query_vars is an array, even if it's empty
		$my_query_vars = self::$shortcode_attributes['my_custom_sql'];
		if ( empty( $my_query_vars ) ) {
			$my_query_vars = array();
		} elseif ( is_string( $my_query_vars ) ) {
			$my_query_vars = shortcode_parse_atts( $my_query_vars );
		}
//error_log( 'AlexaPaigePlugin::double_query $my_query_vars = ' . var_export( $my_query_vars, true ), 0 );

		// Start with empty parameter values
		$ttids = array();

		// Find taxonomy argument, if present, and collect terms
		$taxonomies = get_taxonomies( array( 'object_type' => array( 'attachment' ) ), 'names' );
		foreach( $taxonomies as $taxonomy ) {
			if ( empty( $my_query_vars[ $taxonomy ] ) ) {
				continue;
			}

			// Found the taxonomy; collect the terms
			$include_children =  isset( $my_query_vars['include_children'] ) && 'true' == strtolower( trim( $my_query_vars['include_children'] ) );

			// Allow for multiple term slug values
			$terms = array();
			$slugs = explode( ',', $my_query_vars[ $taxonomy ] );
			foreach ( $slugs as $slug ) {
				$args = array( 'slug' => $slug, 'hide_empty' => false );
				$terms = array_merge( $terms, get_terms( $taxonomy, $args ) );
			}
//error_log( "AlexaPaigePlugin::double_query {$taxonomy} \$terms = " . var_export( $terms, true ), 0 );

			foreach( $terms as $term ) {
				// Index by ttid to remove duplicates
				$ttids[ $term->term_taxonomy_id ] = $term->term_taxonomy_id;

				if ( $include_children ) {
					$args = array( 'child_of' => $term->term_id, 'hide_empty' => false );
					$children = get_terms( 'attachment_category', $args );
					foreach( $children as $child ) {
						$ttids[] = $child->term_taxonomy_id;
					}
				} // include_children
			} // $term
//error_log( "AlexaPaigePlugin::double_query {$taxonomy} \$ttids = " . var_export( $ttids, true ), 0 );
		}

		// Build an array of SQL clauses for the term_relationships query
		$query = array();
		$query_parameters = array();

		$query[] = "SELECT DISTINCT tr.object_id FROM {$wpdb->term_relationships} as tr";

		$placeholders = array();
		if ( ! empty( $ttids ) ) {
			foreach ( $ttids as $ttid ) {
				$placeholders[] = '%s';
				$query_parameters[] = $ttid;
			}
		} else {
				$placeholders[] = '%s';
				$query_parameters[] = '0';
		}

		$query[] = 'WHERE ( tr.term_taxonomy_id IN (' . join( ',', $placeholders ) . ') )';
		$query =  join(' ', $query);
//error_log( 'AlexaPaigePlugin::double_query term_relationships $query = ' . var_export( $query, true ), 0 );
//error_log( 'AlexaPaigePlugin::double_query term_relationships $query_parameters = ' . var_export( $query_parameters, true ), 0 );
		$ids = $wpdb->get_results( $wpdb->prepare( $query, $query_parameters ) );
//error_log( 'AlexaPaigePlugin::double_query term_relationships $ids = ' . var_export( $ids, true ), 0 );
		if ( is_array( $ids ) ) {
			$includes = array();
			foreach ( $ids as $id ) {
				$includes[] = $id->object_id;
			}
		} else {
			$all_query_parameters['include'] = '1'; // return no items
			//error_log( 'AlexaPaigePlugin::double_query empty gallery ', 0 );
			return $all_query_parameters;
		}

		// Build an array of SQL clauses for the posts query
		$query = array();
		$query_parameters = array();

		if ( $is_pagination ) {
			$query[] = "SELECT COUNT( * ) FROM {$wpdb->posts} as p";
		} else {
			$query[] = "SELECT ID FROM {$wpdb->posts} as p";
		}

		$placeholders = array();
		if ( ! empty( $includes ) ) {
			foreach ( $includes as $include ) {
				$placeholders[] = '%s';
				$query_parameters[] = $include;
			}
		} else {
				$placeholders[] = '%s';
				$query_parameters[] = '0';
		}

		$query[] = 'WHERE ( ( p.ID IN (' . join( ',', $placeholders ) . ') )';

		if ( ! empty( self::$shortcode_attributes['post_mime_type'] ) ) {
			if ( 'all' != strtolower( self::$shortcode_attributes['post_mime_type'] ) ) {
				$query[] = str_replace( '%', '%%', wp_post_mime_type_where( self::$shortcode_attributes['post_mime_type'], 'p' ) );
			}
		} else {
			$query[] = "AND (p.post_mime_type LIKE 'image/%%')";
		}

		// Close the WHERE clause
		$query[] = ')';

		/*
		 * ORDER BY clause - we will pre-sort the ids array handed off to the mla_gallery shortcode
		 */
		$all_query_parameters['orderby'] = 'post__in';
		$all_query_parameters['order'] = 'ASC';

		$orderby = 'none';
		$order = 'ASC';

		if ( ! ( $is_pagination || empty( $my_query_vars['orderby'] ) ) ) {
			$orderby = strtolower( $my_query_vars['orderby'] );

			if ( ! empty( $my_query_vars['order'] ) ) {
				$order = strtoupper( $my_query_vars['order'] );
				if ( 'DESC' != $order ) {
					$order = 'ASC';
				}
			}
		}

		switch ( $orderby ) {
			case 'id':
				$query[] = 'ORDER BY p.ID ' . $order;
				break;
			case 'author':
				$query[] = 'ORDER BY p.post_author ' . $order;
				break;
			case 'date':
				$query[] = 'ORDER BY p.post_date ' . $order;
				break;
			case 'description':
			case 'content':
				$query[] = 'ORDER BY p.post_content ' . $order;
				break;
			case 'title':
				$query[] = 'ORDER BY p.post_title ' . $order;
				break;
			case 'caption':
			case 'excerpt':
				$query[] = 'ORDER BY p.post_excerpt ' . $order;
				break;
			case 'slug':
			case 'name':
				$query[] = 'ORDER BY p.post_name ' . $order;
				break;
			case 'modified':
				$query[] = 'ORDER BY p.post_modified ' . $order;
				break;
			case 'parent':
				$query[] = 'ORDER BY p.post_parent ' . $order;
				break;
			case 'menu_order':
				$query[] = 'ORDER BY p.menu_order ' . $order;
				break;
			case 'post_mime_type':
				$query[] = 'ORDER BY p.post_mime_type ' . $order;
				break;
			case 'comment_count':
				$query[] = 'ORDER BY p.comment_count ' . $order;
				break;
			case 'rand':
			case 'random':
				$query[] = 'ORDER BY RAND() ' . $order;
				break;
			case 'none':
			default:
				break;
		}

		if ( ! $is_pagination ) {
			/*
			 * Add pagination to our query, then remove it from the query
			 * that WordPress will process after we're done.
			 * MLA pagination will override WordPress pagination
			 */
			$current_page = self::$shortcode_attributes['mla_page_parameter'];
			if ( ! empty( $all_query_parameters[ $current_page ] ) ) {
				if ( isset( $all_query_parameters['mla_paginate_total'] ) && ( $all_query_parameters[ $current_page ] > $all_query_parameters['mla_paginate_total'] ) ) {
					$paged = 0xFFFF; // suppress further output
				} else {
					$paged = $all_query_parameters[ $current_page ];
				}
			} else {
				$paged = $all_query_parameters['paged'];
			}

			if ( empty( $paged ) ) {
				$paged = 1;
			} elseif ( 'current' == strtolower( $paged ) ) {
				/*
				 * Note: The query variable 'page' holds the pagenumber for a single paginated
				 * Post or Page that includes the <!--nextpage--> Quicktag in the post content. 
				 */
				if ( get_query_var( 'page' ) ) {
					$paged = get_query_var( 'page' );
				} else {
					$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
				}
			} elseif ( is_numeric( $paged ) ) {
				$paged = absint( $paged );
			} elseif ( '' === $paged ) {
				$paged = 1;
			}

			$limit = absint( ! empty( $all_query_parameters['posts_per_page'] ) ? $all_query_parameters['posts_per_page'] : $all_query_parameters['numberposts'] );
			$offset = $limit * ( $paged - 1);
			if ( 0 < $offset && 0 < $limit ) {
				$query[] = 'LIMIT %d, %d';
				$query_parameters[] = $offset;
				$query_parameters[] = $limit;
			} elseif ( 0 < $limit ) {
				$query[] = 'LIMIT %d';
				$query_parameters[] = $limit;
			} elseif ( 0 < $offset ) {
				$query[] = 'LIMIT %d, %d';
				$query_parameters[] = $offset;
				$query_parameters[] = 0x7FFFFFFF; // big number!
			}

			$all_query_parameters['nopaging'] = true;
			$all_query_parameters['numberposts'] = 0;
			$all_query_parameters['posts_per_page'] = 0;
			$all_query_parameters['paged'] = NULL;
			$all_query_parameters['offset'] = NULL;
			$all_query_parameters[ $current_page ] = NULL;
			$all_query_parameters['mla_paginate_total'] = NULL ;
		} // ! is_pagination

		$query =  join(' ', $query);
//error_log( 'AlexaPaigePlugin::double_query posts $query = ' . var_export( $query, true ), 0 );
//error_log( 'AlexaPaigePlugin::double_query posts $query_parameters = ' . var_export( $query_parameters, true ), 0 );
		if ( $is_pagination ) {
			$count = $wpdb->get_var( $wpdb->prepare( $query, $query_parameters ) );
//error_log( 'AlexaPaigePlugin::double_query posts $count = ' . var_export( $count, true ), 0 );
			return $count;
		}

		$ids = $wpdb->get_results( $wpdb->prepare( $query, $query_parameters ) );
//error_log( 'AlexaPaigePlugin::double_query posts $ids = ' . var_export( $ids, true ), 0 );
		if ( is_array( $ids ) ) {
			$includes = array();
			foreach ( $ids as $id ) {
				$includes[] = $id->ID;
			}
			$all_query_parameters['include'] = implode( ',', $includes );
		} else {
			$all_query_parameters['include'] = '1'; // return no items
		}

		//error_log( 'AlexaPaigePlugin::double_query $all_query_parameters = ' . var_export( $all_query_parameters, true ), 0 );
		return $all_query_parameters;
	} // double_query
} // Class AlexaPaigePlugin

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'AlexaPaigePlugin::initialize');
?>
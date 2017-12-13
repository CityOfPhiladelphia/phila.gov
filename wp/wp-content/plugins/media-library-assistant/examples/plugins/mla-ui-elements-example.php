<?php
/**
 * Provides shortcodes to improve user experience for [mla_term_list],
 * [mla_tag_cloud] and [mla_gallery] shortcodes
 *
 * In this example:
 *
 * 1. If you add "use_filters=true" to an [mla_term_list] shortcode this plugin will retain the
 *    selected terms when the page is refreshed and pass them back into the shortcode.
 *
 * 2. If you add "add_filters_to=any" to an [mla_gallery] shortcode this plugin will retain
 *    settings for terms search, keyword search, taxonomy queries and posts_per_page when the
 *    page is refreshed or pagination moves to a new page.
 *
 * 3. If you add "add_filters_to=<taxonomy_slug>" to an [mla_gallery] shortcode this plugin will
 *    do the actions in 2. and will also match the taxonomy_slug to a simple taxonomy query (if
 *    present) and add that query to the taxonomy queries. If the simple query is 'muie-no-terms',
 *    it will be ignored.
 *
 * 4. Shortcodes are provided to generate text box controls and retain their settings when the
 *    page is refreshed or pagination moves to a new page:
 *
 *    [muie_terms_search] generates a terms search text box
 *    [muie_keyword_search] generates a keyword search text box
 *    [muie_orderby] generates an order by dropdown control
 *    [muie_order] generates ascending/descending radio buttons
 *    [muie_per_page] generates an items per page text box
 *    [muie_assigned_items_count] returns the number of items assigned to any term(s) in the
 *    selected taxonomy
 *
 * 5. With a bit of work you can add a tag cloud that works with these filters. Here's an example
 *    you can adapt for your application:
 *
 * <style type='text/css'>
 * #mla-tag-cloud .mla_current_item {
 * 	color:#FF0000;
 * 	font-weight:bold}
 * </style>
 * <span id=mla-tag-cloud>
 * <strong>Tag Cloud</strong>
 * [mla_tag_cloud taxonomy=attachment_tag number=20 current_item="{+request:current_item+}" mla_link_href="{+currentlink_url+}&tax_input{{+query:taxonomy+}}{}={+slug+}&muie_per_page={+template:({+request:muie_per_page+}|5)+}" mla_link_class="{+current_item_class+}"]
 * </span>
 *
 * This example plugin uses four of the many filters available in the [mla_gallery] shortcode
 * and illustrates some of the techniques you can use to customize the gallery display.
 *
 * Created for support topic "How do I provide a front-end search of my media items using Custom Fields?"
 * opened on 4/15/2016 by "direys".
 * https://wordpress.org/support/topic/how-do-i-provide-a-front-end-search-of-my-media-items-using-custom-fields
 *
 * Enhanced for support topic "Dynamic search and filters"
 * opened on 5/28/2016 by "ghislainsc".
 * https://wordpress.org/support/topic/dynamic-search-and-filters
 *
 * Enhanced for support topic "Very new to this, need help"
 * opened on 6/15/2016 by "abronk".
 * https://wordpress.org/support/topic/very-new-to-this-need-help/
 *
 * Enhanced for support topic "Limiting search results to attachment tags/'Justifying' gallery grids"
 * opened on 7/2/2016 by "ceophoetography".
 * https://wordpress.org/support/topic/limiting-search-results-to-attachment-tagsjustifying-gallery-grids
 *
 * Enhanced for support topic "Shortcode"
 * opened on 10/18/2016 by "trinitaa".
 * https://wordpress.org/support/topic/shortcode-456/
 *
 * @package MLA UI Elements Example
 * @version 1.08
 */

/*
Plugin Name: MLA UI Elements Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Provides shortcodes to improve user experience for [mla_term_list], [mla_tag_cloud] and [mla_gallery] shortcodes
Author: David Lingren
Version: 1.08
Author URI: http://fairtradejudaica.org/our-story/staff/

Copyright 2016-2017 David Lingren

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
 * Class MLA UI Elements Example provides shortcodes to improve user experience for
 * [mla_term_list], [mla_tag_cloud] and [mla_gallery] shortcodes
 *
 * @package MLA UI Elements Example
 * @since 1.00
 */
class MLAUIElementsExample {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 1.00
	 */
	public static function initialize() {
		// The filters are only useful for front-end posts/pages; exit if in the admin section
		if ( is_admin() )
			return;

		add_filter( 'mla_term_list_attributes', 'MLAUIElementsExample::mla_term_list_attributes', 10, 1 );
		add_filter( 'mla_gallery_attributes', 'MLAUIElementsExample::mla_gallery_attributes', 10, 1 );

		// Add the custom shortcode for generating "sticky" term search text box
		add_shortcode( 'muie_terms_search', 'MLAUIElementsExample::muie_terms_search' );

		// Add the custom shortcode for generating "sticky" keyword search text box
		add_shortcode( 'muie_keyword_search', 'MLAUIElementsExample::muie_keyword_search' );

		// Add the custom shortcode for generating the items per page text box
		add_shortcode( 'muie_per_page', 'MLAUIElementsExample::muie_per_page' );

		// Add the custom shortcode for generating the order by dropdown control
		add_shortcode( 'muie_orderby', 'MLAUIElementsExample::muie_orderby' );

		// Add the custom shortcode for generating the order radio buttons
		add_shortcode( 'muie_order', 'MLAUIElementsExample::muie_order' );

		// Add the custom shortcode for generating assigned terms counts
		add_shortcode( 'muie_assigned_items_count', 'MLAUIElementsExample::muie_assigned_items_count' );
	}

	/**
	 * Pass mla_control_name parameters from [mla_term_list] to [mla_gallery] for muie_filters
	 *
	 * @since 1.05
	 *
	 * @var	array [ $mla_control_name ] = $_REQUEST[ $mla_control_name ]
	 */
	private static $mla_control_names = array();

	/**
	 * Pass term_id/slug choices from [mla_term_list] to [mla_gallery] for muie_filters
	 *
	 * @since 1.07
	 *
	 * @var	array [ taxonomy ] = 'term_id' or 'slug'
	 */
	private static $mla_option_values = array();

	/**
	 * Look for 'muie_filters' that pass the selected parameters from page to page of a paginated gallery
	 *
	 * @since 1.00
	 *
	 * @param	array	the shortcode parameters passed in to the shortcode
	 */
	public static function mla_term_list_attributes( $shortcode_attributes ) {
		// Exit if this is not a "filtered" term list
		if ( empty( $shortcode_attributes['use_filters'] )  || ( 'true' !== trim ( strtolower( $shortcode_attributes['use_filters'] ) ) ) ) {
			return $shortcode_attributes;
		}

		$mla_debug = ( ! empty( $shortcode_attributes['mla_debug'] ) ) ? trim( strtolower( $shortcode_attributes['mla_debug'] ) ) : false;
		if ( $mla_debug ) {
			if ( 'true' == $mla_debug ) {
				MLACore::mla_debug_mode( 'buffer' );
			} elseif ( 'log' == $mla_debug ) {
				MLACore::mla_debug_mode( 'log' );
			} else {
				$mla_debug = false;
			}
		}

		if ( $mla_debug ) {
			MLACore::mla_debug_add( __LINE__ . ' MLAUIElementsExample::mla_term_list_attributes input = ' . var_export( $shortcode_attributes, true ) );
		}

		// Pass "slug" overides to mla_gallery_attributes; using the slug is a common practice
		$mla_option_value = in_array( $shortcode_attributes['mla_option_value'], array( '{+slug+}', '[+slug+]' ) ) ? 'slug' : 'term_id';
		foreach( explode( ',', $shortcode_attributes['taxonomy'] ) as $taxonomy ) {
			self::$mla_option_values[ $taxonomy ] = $mla_option_value;
		}

		// Allow for multiple taxonomies and named controls
		$taxonomy = implode( '-', explode( ',', $shortcode_attributes['taxonomy'] ) );
		$mla_control_name = !empty( $shortcode_attributes['mla_control_name'] ) ? $shortcode_attributes['mla_control_name'] : false;
		if ( $mla_control_name  ) {
			if ( $index = strpos( $mla_control_name, '[]' ) ) {
				$mla_control_name = substr( $mla_control_name, 0, $index );
			}
		}

		// Pagination links, e.g. Previous or Next, have muie_filters that encode the form parameters
		if ( !empty( $_REQUEST['muie_filters'] ) ) {
			$filters = json_decode( trim( stripslashes( $_REQUEST['muie_filters'] ), '"' ), true );

			if ( !empty( $filters['tax_input'] ) ) {
				$_REQUEST['tax_input'] = $filters['tax_input'];
			}

			if ( $mla_control_name && !empty( $filters[ $mla_control_name ] ) ) {
				$_REQUEST[ $mla_control_name ] = $filters[ $mla_control_name ];
			}
		}

		// Check for a named control with possible taxonomy.term values from "combined" taxonomies
		if ( $mla_control_name && !empty( $_REQUEST[ $mla_control_name ] ) ) {
			self::$mla_control_names[ $mla_control_name ] = $_REQUEST[ $mla_control_name ];
			if ( is_scalar( $_REQUEST[ $mla_control_name ] ) ) {
				$input = array( $_REQUEST[ $mla_control_name ] );
			} else {
				$input = $_REQUEST[ $mla_control_name ];
			}
			
			foreach( $input as $input_element ) {
				$value = explode( '.', $input_element );

				if ( 2 === count( $value ) ) {
					$taxonomy = $value[0];
					$_REQUEST['tax_input'][ $taxonomy ][] = $value[1];
				} else {
					$_REQUEST['tax_input'][ $taxonomy ][] = $input_element;
				}
			}
		}

		// If nothing is set for this taxonomy we're done
		if ( empty( $_REQUEST['tax_input'] ) || !array_key_exists( $taxonomy, $_REQUEST['tax_input'] ) ) {
			if ( $mla_debug ) {
				MLACore::mla_debug_add( __LINE__ . ' MLAUIElementsExample::mla_term_list_attributes no changes' );
			}

			return $shortcode_attributes;
		}

		$terms = $_REQUEST['tax_input'][ $taxonomy ];

		// Check for a dropdown control with "All Terms" selected
		if ( empty( $shortcode_attributes['option_all_value'] ) ) {
			$option_all = array_search( '0', $terms );
		} else {
			$option_all = array_search( $shortcode_attributes['option_all_value'], $terms );
		}

		if ( false !== $option_all ) {
			unset( $terms[ $option_all ] );
		}

		if ( empty( $shortcode_attributes['option_all_text'] ) ) {
			$option_all = array_search( '', $terms );
		} else {
			$option_all = array_search( sanitize_title( $shortcode_attributes['option_all_text'] ), $terms );
		}

		if ( false !== $option_all ) {
			unset( $terms[ $option_all ] );
		}

		// Reflect option_all changes in the query arguments
		$_REQUEST['tax_input'][ $taxonomy ] = $terms;
		if ( $mla_debug ) {
			MLACore::mla_debug_add( __LINE__ . ' MLAUIElementsExample::mla_term_list_attributes tax_input = ' . var_export( $_REQUEST['tax_input'], true ) );
		}

		// Pass selected terms to the shortcode
		if ( !empty( $terms ) ) {
			if ( $mla_control_name && !empty( $_REQUEST[ $mla_control_name ] ) ) {
				$shortcode_attributes[ $shortcode_attributes['mla_item_parameter'] ] = $_REQUEST[ $mla_control_name ];
			} else {
				$shortcode_attributes[ $shortcode_attributes['mla_item_parameter'] ] = implode( ',', $_REQUEST['tax_input'][ $taxonomy ] );
			}
		}

		unset( $shortcode_attributes['use_filters'] );

		if ( $mla_debug ) {
			MLACore::mla_debug_add( __LINE__ . ' MLAUIElementsExample::mla_term_list_attributes returns = ' . var_export( $shortcode_attributes, true ) );
		}

		return $shortcode_attributes;
	} // mla_term_list_attributes

	/**
	 * Add the taxonomy query to the shortcode, limit posts_per_page and encode filters for pagination links
	 *
	 * @since 1.00
	 *
	 * @param	array	the shortcode parameters passed in to the shortcode
	 */
	public static function mla_gallery_attributes( $shortcode_attributes ) {
		// Only process shortcodes that allow filters
		if ( empty( $shortcode_attributes['add_filters_to'] ) ) {
			return $shortcode_attributes;
		}

		$mla_debug = ( ! empty( $shortcode_attributes['mla_debug'] ) ) ? trim( strtolower( $shortcode_attributes['mla_debug'] ) ) : false;
		if ( $mla_debug ) {
			if ( 'true' == $mla_debug ) {
				MLACore::mla_debug_mode( 'buffer' );
			} elseif ( 'log' == $mla_debug ) {
				MLACore::mla_debug_mode( 'log' );
			} else {
				$mla_debug = false;
			}
		}

		if ( $mla_debug ) {
			MLACore::mla_debug_add( __LINE__ . ' MLAUIElementsExample::mla_gallery_attributes input = ' . var_export( $shortcode_attributes, true ) );
		}

		// Unpack filter values encoded for pagination links
		if ( !empty( $_REQUEST['muie_filters'] ) ) {
			$filters = json_decode( trim( stripslashes( $_REQUEST['muie_filters'] ), '"' ), true );

			foreach( $filters as $filter_key => $filter_value ) {
				$_REQUEST[ $filter_key ] = $filter_value;
			}
		}

		// Adjust posts_per_page/numberposts
		if ( !empty( $_REQUEST['muie_per_page'] ) ) {
			if ( isset( $shortcode_attributes['numberposts'] ) && ! isset( $shortcode_attributes['posts_per_page'] )) {
				$shortcode_attributes['posts_per_page'] = $shortcode_attributes['numberposts'];
				unset( $shortcode_attributes['numberposts'] );
			}

			$shortcode_attributes['posts_per_page'] = $_REQUEST['muie_per_page'];
		}

		// Fill these in from $_REQUEST parameters
		$muie_filters = array();

		$mla_control_name = !empty( $shortcode_attributes['mla_control_name'] ) ? $shortcode_attributes['mla_control_name'] : '';
		if ( !empty( $_REQUEST[ $mla_control_name ] ) ) {
			$muie_filters[ $mla_control_name ] = $_REQUEST[ $mla_control_name ];
		}

		// Add the orderby & order parameters
		if ( !empty( $_REQUEST['muie_orderby'] ) ) {
			$muie_filters['muie_orderby'] = $shortcode_attributes['orderby'] = $_REQUEST['muie_orderby'];
		}

		if ( !empty( $_REQUEST['muie_meta_key'] ) ) {
			$muie_filters['muie_meta_key'] = $shortcode_attributes['meta_key'] = $_REQUEST['muie_meta_key'];
		}

		if ( !empty( $_REQUEST['muie_order'] ) ) {
			$muie_filters['muie_order'] = $shortcode_attributes['order'] = $_REQUEST['muie_order'];
		}

		// Add the terms search parameters, if present
		if ( !empty( $_REQUEST['muie_terms_search'] ) && is_array( $_REQUEST['muie_terms_search'] ) && !empty( $_REQUEST['muie_terms_search']['mla_terms_phrases'] ) ) {
			$muie_filters['muie_terms_search'] =  $_REQUEST['muie_terms_search'];
			foreach( $muie_filters['muie_terms_search'] as $key => $value ) {
				if ( !empty( $value ) ) {
					$shortcode_attributes[ $key ] = $value;
				}
			}
		}

		// Add the keyword search parameters, if present
		if ( !empty( $_REQUEST['muie_keyword_search'] ) && is_array( $_REQUEST['muie_keyword_search'] ) && !empty( $_REQUEST['muie_keyword_search']['s'] ) ) {
			$muie_filters['muie_keyword_search'] = $_REQUEST['muie_keyword_search'];
			foreach( $muie_filters['muie_keyword_search'] as $key => $value ) {
				if ( !empty( $value ) ) {
					$shortcode_attributes[ $key ] = $value;
				}
			}
		}

		// Add the taxonomy filter(s), if present
		$filter_taxonomy = $shortcode_attributes['add_filters_to'];
		if ( !empty( $_REQUEST['tax_input'] ) ) {
			$muie_filters['tax_input'] = $tax_input = $_REQUEST['tax_input'];
		} else {
			$tax_input = array();
		}

		// Add the [mla_term_list mla_control_name=] parameter(s)
		if ( !empty( self::$mla_control_names ) ) {
			$muie_filters = array_merge( $muie_filters, self::$mla_control_names );
		}

		if ( ! ( empty( $shortcode_attributes[ $filter_taxonomy ] ) && empty( $tax_input ) ) ) {
			$tax_query = '';

			// Validate other tax_query parameters or set defaults
			$tax_relation = 'AND';
			if ( isset( $shortcode_attributes['tax_relation'] ) ) {
				$attr_value = strtoupper( $shortcode_attributes['tax_relation'] );
				if ( in_array( $attr_value, array( 'AND', 'OR' ) ) ) {
					$tax_relation = $attr_value;
				}
			}

			$default_operator = 'IN';
			if ( isset( $shortcode_attributes['tax_operator'] ) ) {
				$attr_value = strtoupper( $shortcode_attributes['tax_operator'] );
				if ( in_array( $attr_value, array( 'IN', 'NOT IN', 'AND' ) ) ) {
					$default_operator = $attr_value;
				}
			}

			$default_children = 'true';
			if ( isset( $shortcode_attributes[ 'tax_include_children' ] ) ) {
				$attr_value = strtolower( $shortcode_attributes[ 'tax_include_children' ] );
				if ( in_array( $attr_value, array( 'false', 'true' ) ) ) {
					$default_children = $attr_value;
				}
			}

			// Look for the optional "simple taxonomy query" as an initial filter
			if ( !empty( $shortcode_attributes[ $filter_taxonomy ] ) ) {
				if ( 'muie-no-terms' !== $shortcode_attributes[ $filter_taxonomy ] ) {
					// Check for a dropdown control with "All Terms" selected
					$terms = explode( ',', $shortcode_attributes[ $filter_taxonomy ] );
					if ( empty( $shortcode_attributes['option_all_value'] ) ) {
						$option_all = array_search( '0', $terms );
					} else {
						$option_all = array_search( $shortcode_attributes['option_all_value'], $terms );
					}

					if ( false !== $option_all ) {
						unset( $terms[ $option_all ] );
					}

					if ( !empty( $terms ) ) {
						$values = "array( '" . implode( "', '", $terms ) . "' )";
						$tax_query .= "array('taxonomy' => '{$filter_taxonomy}' ,'field' => 'slug','terms' => {$values}, 'operator' => '{$default_operator}', 'include_children' => {$default_children} ), ";
					}
				}

				unset( $shortcode_attributes[ $filter_taxonomy ] );
			}

			foreach ( $tax_input as $taxonomy => $terms ) {
				// simple taxonomy query overrides tax_input
				if ( $taxonomy == $filter_taxonomy ) {
					continue;
				}

				// Check for a dropdown control with "All Terms" selected
				if ( empty( $shortcode_attributes['option_all_value'] ) ) {
					$option_all = array_search( '0', $terms );
				} else {
					$option_all = array_search( $shortcode_attributes['option_all_value'], $terms );
				}

				if ( false !== $option_all ) {
					unset( $terms[ $option_all ] );
				}

				if ( !empty( $terms ) ) {
					// Numeric values could still be a slug
					$field = self::$mla_option_values[ $taxonomy ];
					foreach ( $terms as $term ) {
						if ( ! ctype_digit( $term ) ) {
							$field = 'slug';
							break;
						}
					}

					if ( 'term_id' == $field ) {
						$values = 'array( ' . implode( ',', $terms ) . ' )';
					} else {
						$values = "array( '" . implode( "','", $terms ) . "' )";
					}

					// Taxonomy-specific "operator"					
					$tax_operator = $default_operator;
					if ( isset( $shortcode_attributes[ $taxonomy . '_operator' ] ) ) {
						$attr_value = strtoupper( $shortcode_attributes[ $taxonomy . '_operator' ] );
						if ( in_array( $attr_value, array( 'IN', 'NOT IN', 'AND' ) ) ) {
							$tax_operator = $attr_value;
						}
					}

					// Taxonomy-specific "include_children"					
					$tax_children = $default_children;
					if ( isset( $shortcode_attributes[ $taxonomy . '_children' ] ) ) {
						$attr_value = strtolower( $shortcode_attributes[ $taxonomy . '_children' ] );
						if ( in_array( $attr_value, array( 'false', 'true' ) ) ) {
							$tax_children = $attr_value;
						}
					}

					$tax_query .= "array('taxonomy' => '{$taxonomy}' ,'field' => '{$field}','terms' => {$values}, 'operator' => '{$tax_operator}', 'include_children' => {$tax_children} ), ";
				}
			}

			if ( ! empty( $tax_query ) ) {
				$shortcode_attributes['tax_query'] = "array( 'relation' => '" . $tax_relation . "', " . $tax_query . ')';
			}
		}

		/*
		 * Add the filter settings to pagination URLs
		 */
		if ( !empty( $shortcode_attributes['mla_output'] ) ) {

			$filters = urlencode( json_encode( $muie_filters ) );
			$shortcode_attributes['mla_link_href'] = '[+new_url+]?[+new_page_text+]&muie_filters=' . $filters;

			if ( !empty( $shortcode_attributes['posts_per_page'] ) ) {
				$shortcode_attributes['mla_link_href'] .= '&muie_per_page=' . $shortcode_attributes['posts_per_page'];
			}
		}

		unset( $shortcode_attributes['add_filters_to'] );

		if ( $mla_debug ) {
			MLACore::mla_debug_add( __LINE__ . ' MLAUIElementsExample::mla_gallery_attributes returns = ' . var_export( $shortcode_attributes, true ) );
		}

		return $shortcode_attributes;
	} // mla_gallery_attributes

	/**
	 * Terms search generator shortcode
	 *
	 * This shortcode generates an HTML text box with a default mla_terms_phrases value,
	 * and adds hidden parameters for the other Terms Search parameters
	 *
	 * @since 1.00
	 *
	 * @param	array	the shortcode parameters
	 *
	 * @return	string	HTML markup for the generated form
	 */
	public static function muie_terms_search( $attr ) {
		$default_arguments = array(
			'mla_terms_phrases' => '',
			'mla_terms_taxonomies' => '',
			'mla_phrase_delimiter' => '',
			'mla_term_delimiter' => '',
			'mla_phrase_connector' => '',
			'mla_term_delimiter' => '',
			'mla_term_connector' => '',
		);

		// Make sure $attr is an array, even if it's empty
		if ( empty( $attr ) ) {
			$attr = array();
		} elseif ( is_string( $attr ) ) {
			$attr = shortcode_parse_atts( $attr );
		}

		// Accept only the attributes we need and supply defaults
		$arguments = shortcode_atts( $default_arguments, $attr );

		// Pagination links, e.g. Previous or Next, have muie_filters that encode the form parameters
		if ( !empty( $_REQUEST['muie_filters'] ) ) {
			$filters = json_decode( trim( stripslashes( $_REQUEST['muie_filters'] ), '"' ), true );

			if ( !empty( $filters['muie_terms_search'] ) ) {
				$_REQUEST['muie_terms_search'] = $filters['muie_terms_search'];
			}
		}

		// muie_terms_search has settings from the form or pagination link
		if ( !empty( $_REQUEST['muie_terms_search'] ) && is_array( $_REQUEST['muie_terms_search'] ) ) {
			foreach ( $arguments as $key => $value ) {
				if ( !empty( $_REQUEST['muie_terms_search'][ $key ] ) ) {
					$arguments[ $key ] = stripslashes( $_REQUEST['muie_terms_search'][ $key ] );
				}
			}
		}

		// Always supply the terms phrases text box, with the appropriate quoting
		if ( false !== strpos( $arguments['mla_terms_phrases'], '"' ) ) {
			$delimiter = '\'';
		} else {
			$delimiter = '"';
		}

		$return_value = '<input name="muie_terms_search[mla_terms_phrases]" id="muie-terms-phrases" type="text" size="20" value=' . $delimiter . $arguments['mla_terms_phrases'] . $delimiter . " />\n";		
		unset( $arguments['mla_terms_phrases'] );

		// Add optional parameters
		foreach( $arguments as $key => $value ) {
			if ( !empty( $value ) ) {
				$id_value = str_replace( '_', '-', substr( $key, 4 ) );
				$return_value .= sprintf( '<input name="muie_terms_search[%1$s]" id="muie-%2$s" type="hidden" value="%3$s" />%4$s', $key, $id_value, $value, "\n" );		
			}
		}

		return $return_value;
	} // muie_terms_search

	/**
	 * Keyword search generator shortcode
	 *
	 * This shortcode generates an HTML text box with a default "s" (search string) value,
	 * and adds hidden parameters for the other Keyword Search parameters
	 *
	 * @since 1.00
	 *
	 * @param	array	the shortcode parameters
	 *
	 * @return	string	HTML markup for the generated form
	 */
	public static function muie_keyword_search( $attr ) {
		$default_arguments = array(
			's' => '',
			'mla_search_fields' => '',
			'mla_search_connector' => '',
			'sentence' => '',
			'exact' => '',
		);

		// Make sure $attr is an array, even if it's empty
		if ( empty( $attr ) ) {
			$attr = array();
		} elseif ( is_string( $attr ) ) {
			$attr = shortcode_parse_atts( $attr );
		}

		// Accept only the attributes we need and supply defaults
		$arguments = shortcode_atts( $default_arguments, $attr );

		// Pagination links, e.g. Previous or Next, have muie_filters that encode the form parameters
		if ( !empty( $_REQUEST['muie_filters'] ) ) {
			$filters = json_decode( trim( stripslashes( $_REQUEST['muie_filters'] ), '"' ), true );

			if ( !empty( $filters['muie_keyword_search'] ) ) {
				$_REQUEST['muie_keyword_search'] = $filters['muie_keyword_search'];
			}
		}

		// muie_keyword_search has settings from the form or pagination link
		if ( !empty( $_REQUEST['muie_keyword_search'] ) && is_array( $_REQUEST['muie_keyword_search'] ) ) {
			foreach ( $arguments as $key => $value ) {
				if ( !empty( $_REQUEST['muie_keyword_search'][ $key ] ) ) {
					$arguments[ $key ] = stripslashes( $_REQUEST['muie_keyword_search'][ $key ] );
				}
			}
		}

		// Always supply the search text box, with the appropriate quoting
		if ( false !== strpos( $arguments['s'], '"' ) ) {
			$delimiter = '\'';
		} else {
			$delimiter = '"';
		}

		$return_value = '<input name="muie_keyword_search[s]" id="muie-s" type="text" size="20" value=' . $delimiter . $arguments['s'] . $delimiter . " />\n";		
		unset( $arguments['s'] );

		// Add optional parameters
		foreach( $arguments as $key => $value ) {
			if ( !empty( $value ) ) {
				$id_value = str_replace( '_', '-', substr( $key, 4 ) );
				$return_value .= sprintf( '<input name="muie_keyword_search[%1$s]" id="muie-%2$s" type="hidden" value="%3$s" />%4$s', $key, $id_value, $value, "\n" );		
			}
		}

		return $return_value;
	} // muie_keyword_search

	/**
	 * Items per page shortcode
	 *
	 * This shortcode generates an HTML text box with a default muie_per_page value.
	 *
	 * @since 1.00
	 *
	 * @param	array	the shortcode parameters
	 *
	 * @return	string	HTML markup for the generated form
	 */
	public static function muie_per_page( $attr ) {
		if ( isset( $attr['numberposts'] ) && ! isset( $attr['posts_per_page'] )) {
			$attr['posts_per_page'] = $attr['numberposts'];
			unset( $attr['numberposts'] );
		}

		if ( !empty( $_REQUEST['muie_per_page'] ) ) {
			$posts_per_page = $_REQUEST['muie_per_page'];
		} else {
			$posts_per_page = isset( $attr['posts_per_page'] ) ? $attr['posts_per_page'] : 6;
		}

		return '<input name="muie_per_page" id="muie-per-page" type="text" size="2" value="' . $posts_per_page . '" />';
	} // muie_per_page

	/**
	 * Order by shortcode
	 *
	 * This shortcode generates a dropdown control with sort order values.
	 *
	 * @since 1.03
	 *
	 * @param array $attr the shortcode parameters
	 * @param string $content Optional content for enclosing shortcodes
	 *
	 * @return	string	HTML markup for the generated control(s)
	 */
	public static function muie_orderby( $attr, $content = NULL  ) {
		$default_arguments = array(
			'shortcode' => 'mla_gallery',
			'sort_fields' => '',
			'meta_value_num' => '',
			'meta_value' => '',
		);

		// Make sure $attr is an array, even if it's empty
		if ( empty( $attr ) ) {
			$attr = array();
		} elseif ( is_string( $attr ) ) {
			$attr = shortcode_parse_atts( $attr );
		}

		/*
		 * Look for parameters in an enclosing shortcode
		 */
		if ( !empty( $content ) ) {
			$content = str_replace( array( '&#8216;', '&#8217;', '&#8221;', '&#8243;', '<br />', '<p>', '</p>', "\r", "\n" ), array( '\'', '\'', '"', '"', ' ', ' ', ' ', ' ', ' ' ), $content );
			$new_attr = shortcode_parse_atts( $content );
			$attr = array_merge( $attr, $new_attr );
		}

		// Accept only the attributes we need and supply defaults
		$arguments = shortcode_atts( $default_arguments, $attr );

		switch ( $arguments['shortcode'] ) {
			case 'mla_gallery':
				$allowed_fields = array(
					'empty' => '- select -',
					'ID'  => 'item ID', 
					'author'  => 'Author',
					'date'  => 'Date uploaded', 
					'description' => 'Description',
					'title' => 'Title',
					'caption' => 'Caption',  
					'slug' => 'name/slug', 
					'parent' => 'Parent ID', 
					'menu_order' => 'Menu order', 
					'mime_type' => 'MIME type', 
					'none' => 'No order', 
					'rand' => 'Random', 
				);
				break;
			case 'mla_tag_cloud':
			case 'mla_term_list':
				$allowed_fields = array(
					'empty' => '- select -',
					'count' => 'Assigned items',
					'id' => 'Term ID',
					'name' => 'Term name',
					'slug' => 'Term slug',
					'none' => 'No order', 
					'random' => 'Random', 
				);
				break;
			default:
				$allowed_fields = array();
		}

		if ( empty( $arguments['sort_fields'] ) ) {
			$sort_fields = $allowed_fields;
		} else {
			$sort_fields = array();

			if ( 0 === strpos( $arguments['sort_fields'], 'array' ) ) {
				$function = @create_function('', 'return ' . $arguments['sort_fields'] . ';' );
				if ( is_callable( $function ) ) {
					$field_array = $function();
				}

				if ( is_array( $field_array ) ) {
					$sort_fields = $field_array;
				}
			} else {
				foreach( explode( ',', $arguments['sort_fields'] ) as $field ) {
					if ( array_key_exists( $field, $allowed_fields ) ) {
						$sort_fields[ $field ] = $allowed_fields[ $field ];
					}
				}
			}
		}

		// Check for custom field sorting
		if ( !empty( $arguments['meta_value_num'] ) ) {
			$custom_key = 'meta_value_num';
			$custom_spec = $arguments['meta_value_num'];
		} elseif ( !empty( $arguments['meta_value'] ) ) {
			$custom_key = 'meta_value';
			$custom_spec = $arguments['meta_value'];
		} else {
			$custom_key = '';
			$custom_spec = '';
		}

		if ( !empty( $custom_spec ) ) {
			$spec_parts = explode( '=>', $custom_spec );
			$spec_key = trim( $spec_parts[0], ' \'"' );
			$spec_suffix = '';

			$tail = strrpos( $spec_key, ' DESC' );
			if ( ! ( false === $tail ) ) {
				$spec_key = substr( $spec_key, 0, $tail );
				$spec_suffix = ' DESC';
			} else {
				$tail = strrpos( $spec_key, ' ASC' );
				if ( ! ( false === $tail ) ) {
					$spec_key = substr( $spec_key, 0, $tail );
					$spec_suffix = ' ASC';
				}
			}

			$spec_label = !empty( $spec_parts[1] ) ? trim( $spec_parts[1], ' \'"' ) : $spec_key;
			$sort_fields[ $custom_key . $spec_suffix ] = $spec_label;
		}

		if ( empty( $sort_fields ) ) {
			return '';
		}

		// Unpack filter values encoded for pagination links
		if ( !empty( $_REQUEST['muie_filters'] ) ) {
			$filters = json_decode( trim( stripslashes( $_REQUEST['muie_filters'] ), '"' ), true );

			if ( isset( $filters['muie_orderby'] ) ) {
				$_REQUEST['muie_orderby'] = $filters['muie_orderby'];
			}
		}

		if ( !empty( $_REQUEST['muie_orderby'] ) ) {
			$current_value = $_REQUEST['muie_orderby'];
		} else {
			$current_value = '';
		}

		if ( !empty( $spec_key ) ) {
			$output = '<input name="muie_meta_key" id="muie-meta-key" type="hidden" value="' . $spec_key . '">' . "\n";
		} else {
			$output = '';
		}

		$output .= '<select name="muie_orderby" id="muie-orderby">' . "\n";

		foreach ( $sort_fields as $value => $label ) {
			$value = 'empty' === $value ? '' : $value;

			$selected = ( $current_value === $value ) ? ' selected=selected ' : ' ';

			$output .= '  <option' . $selected . 'value="' . $value . '">' . $label . "</option>\n";
		}

		$output .= "</select>\n";

		return $output;
	} // muie_orderby

	/**
	 * Order (ASC/DESC) shortcode
	 *
	 * This shortcode generates ascending/descending radio buttons.
	 *
	 * @since 1.03
	 *
	 * @param array $attr the shortcode parameters
	 * @param string $content Optional content for enclosing shortcodes
	 *
	 * @return	string	HTML markup for the generated control(s)
	 */
	public static function muie_order( $attr, $content = NULL  ) {
		$default_arguments = array(
			'default_order' => 'ASC',
			'asc_label' => 'Ascending',
			'desc_label' => 'Descending',
		);

		// Make sure $attr is an array, even if it's empty
		if ( empty( $attr ) ) {
			$attr = array();
		} elseif ( is_string( $attr ) ) {
			$attr = shortcode_parse_atts( $attr );
		}

		/*
		 * Look for parameters in an enclosing shortcode
		 */
		if ( !empty( $content ) ) {
			$content = str_replace( array( '&#8216;', '&#8217;', '&#8221;', '&#8243;', '<br />', '<p>', '</p>', "\r", "\n" ), array( '\'', '\'', '"', '"', ' ', ' ', ' ', ' ', ' ' ), $content );
			$new_attr = shortcode_parse_atts( $content );
			$attr = array_merge( $attr, $new_attr );
		}

		// Accept only the attributes we need and supply defaults
		$arguments = shortcode_atts( $default_arguments, $attr );

		// Unpack filter values encoded for pagination links
		if ( !empty( $_REQUEST['muie_filters'] ) ) {
			$filters = json_decode( trim( stripslashes( $_REQUEST['muie_filters'] ), '"' ), true );

			if ( isset( $filters['muie_order'] ) ) {
				$_REQUEST['muie_order'] = $filters['muie_order'];
			}
		}

		if ( !empty( $_REQUEST['muie_order'] ) ) {
			$current_value = $_REQUEST['muie_order'];
		} else {
			$current_value = $arguments['default_order'];
		}

		if ( 'DESC' === $current_value ) {
			$asc_selected = '';
			$desc_selected = ' checked="checked"';
		} else {
			$asc_selected = ' checked="checked"';
			$desc_selected = '';
		}

		$output  = '<input name="muie_order" id="muie-order-asc" type="radio"' . $asc_selected . ' value="ASC"> ' .  $arguments['asc_label'] . '&nbsp;&nbsp;';
		$output .= '<input name="muie_order" id="muie-order-desc" type="radio"' . $desc_selected . ' value="DESC">' .  $arguments['desc_label'] . "&nbsp;&nbsp\n";

		return $output;
	} // muie_order

	/**
	 * Assigned items count shortcode
	 *
	 * This shortcode returns the number of items assigned to any term(s) in the selected taxonomy
	 *
	 * @since 1.01
	 *
	 * @param	array	the shortcode parameters
	 *
	 * @return	string	HTML markup for the generated form
	 */
	public static function muie_assigned_items_count( $attr ) {
		global $wpdb;

		$default_arguments = array(
			'taxonomy' => '',
			'post_type' => 'attachment',
			'post_status' => 'inherit',
			'post_mime_type' => 'image',
		);

		// Make sure $attr is an array, even if it's empty
		if ( empty( $attr ) ) {
			$attr = array();
		} elseif ( is_string( $attr ) ) {
			$attr = shortcode_parse_atts( $attr );
		}

		// Accept only the attributes we need and supply defaults
		$arguments = shortcode_atts( $default_arguments, $attr );

		/*
		 * Build an array of individual clauses that can be filtered
		 */
		$clauses = array( 'fields' => '', 'join' => '', 'where' => '', 'order' => '', 'orderby' => '', 'limits' => '', );

		$clause_parameters = array();

		$clause[] = 'LEFT JOIN `' . $wpdb->term_relationships . '` AS tr ON tt.term_taxonomy_id = tr.term_taxonomy_id';
		$clause[] = 'LEFT JOIN `' . $wpdb->posts . '` AS p ON tr.object_id = p.ID';

		/*
		 * Add type and status constraints
		 */
		if ( is_array( $arguments['post_type'] ) ) {
			$post_types = $arguments['post_type'];
		} else {
			$post_types = array( $arguments['post_type'] );
		}

		$placeholders = array();
		foreach ( $post_types as $post_type ) {
			$placeholders[] = '%s';
			$clause_parameters[] = $post_type;
		}

		$clause[] = 'AND p.post_type IN (' . join( ',', $placeholders ) . ')';

		if ( is_array( $arguments['post_status'] ) ) {
			$post_stati = $arguments['post_status'];
		} else {
			$post_stati = array( $arguments['post_status'] );
		}

		$placeholders = array();
		foreach ( $post_stati as $post_status ) {
			if ( ( 'private' != $post_status ) || is_user_logged_in() ) {
				$placeholders[] = '%s';
				$clause_parameters[] = $post_status;
			}
		}
		$clause[] = 'AND p.post_status IN (' . join( ',', $placeholders ) . ')';

		$clause =  join(' ', $clause);
		$clauses['join'] = $wpdb->prepare( $clause, $clause_parameters );

		/*
		 * Start WHERE clause with a taxonomy constraint
		 */
		if ( is_array( $arguments['taxonomy'] ) ) {
			$taxonomies = $arguments['taxonomy'];
		} else {
			$taxonomies = array( $arguments['taxonomy'] );
		}

		foreach ( $taxonomies as $taxonomy ) {
			if ( ! taxonomy_exists( $taxonomy ) ) {
				$error = new WP_Error( 'invalid_taxonomy', __( 'Invalid taxonomy', 'media-library-assistant' ), $taxonomy );
				return $error;
			}
		}

		$clause_parameters = array();
		$placeholders = array();
		foreach ($taxonomies as $taxonomy) {
		    $placeholders[] = '%s';
			$clause_parameters[] = $taxonomy;
		}

		$clause = array( 'tt.taxonomy IN (' . join( ',', $placeholders ) . ')' );
		if ( 'all' !== strtolower( $arguments['post_mime_type'] ) ) {
			$clause[] = str_replace( '%', '%%', wp_post_mime_type_where( $arguments['post_mime_type'], 'p' ) );
		}

		$clause =  join(' ', $clause);
		$clauses['where'] = $wpdb->prepare( $clause, $clause_parameters );

		/*
		 * Build the final query
		 */
		$query = array( 'SELECT' );
		$query[] = 'COUNT(*)'; // 'p.ID'; // $clauses['fields'];
		$query[] = 'FROM ( SELECT DISTINCT p.ID FROM `' . $wpdb->term_taxonomy . '` AS tt';
		$query[] = $clauses['join'];
		$query[] = 'WHERE (';
		$query[] = $clauses['where'];
		$query[] = ') ) as subquery';

		$query =  join(' ', $query);
		$count = $wpdb->get_var( $query );
		return number_format( (float) $count );
	} // muie_assigned_items_count
} // Class MLAUIElementsExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLAUIElementsExample::initialize');
?>
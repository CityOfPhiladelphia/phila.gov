<?php
/**
 * This plugin provides much faster taxonomy queries when the taxonomy and term(s) given as input
 * are ONLY used for Media Library image items, so we can omit the term_relationships/posts 
 * JOIN clause for tests on assigned term values.
 *
 * A custom shortcode parameter, "my_custom_sql", activates the logic in this plugin.
 *
 * The "my_custom_sql" parameter accepts these query arguments:
 *  - ONE taxonomy=(/)slug(,(/)slug)... argument, to INCLUDE or /EXCLUDE terms
 *    i.e. put a slash in front of a term to EXCLUDE items assigned to it
 *  - include_children=true
 *  - author=ID(,ID...)
 *  - order and/or orderby
 *
 * The shortcode can also contain the post_mime_type and/or keyword search parameters
 * (outside "my_custom_sql") to further filter the results. The double_query() function is
 * called when the request contains post_mime_type, keyword search or orderby/order parameters.
 *
 * NOTE: If you use this logic for queries that only EXCLUDE terms, e.g., "taxonomy=/slug", note that it
 * will fail to include items that have no term assignments at all, because this logic only looks in the
 * wp_term_relationships table to find attachment IDs. If this affects your application, consider assigning
 * a term such as "Uncategorized" to items without terms or use the standard "tax_query" parameter instead.
 *
 * This plugin provides a custom example of hooking the filters provided by the [mla_gallery] shortcode:
 *    
 *  - In the "mla_gallery_arguments" filter is an example of detecting MLA pagination, e.g.,
 *    mla_output="paginate_links,prev_next and supplying the count required to accomodate it.
 *
 *  - In the "mla_gallery_query_arguments" filter is an example of a custom SQL query
 *    that replaces the usual "simple taxonomy" get_posts/WP_Query results.
 *
 *  - The "single_query()" and "double_query()" functions provide simplified, higher-performance
 *    alternatives to the standard WordPress tax_query.
 *
 * Created for support topic "Slow queries"
 * opened on 7/4/2014 by "aptharsia".
 * https://wordpress.org/support/topic/slow-queries-1/
 *
 * Enhanced for support topic "REALLY Slow Queries........  Help! :)"
 * opened on 8/16/2014 by "alexapaige".
 * https://wordpress.org/support/topic/really-slow-queries-help/
 *
 * Enhanced for support topic "MLATaxQuery with keyword search and pagination"
 * opened on 10/15/2015 by "CabinetWorks".
 * https://wordpress.org/support/topic/mlataxquery-with-keyword-search-and-pagination/
 *
 * Enhanced for support topic "Gallery page with many images takes too long to load"
 * opened on 6/27/2017 by "davidjhk".
 * https://wordpress.org/support/topic/gallery-page-with-many-images-takes-too-long-to-load/
 *
 * Enhanced for support topic "504 Time-Out issue"
 * opened on 10/19/2017 by "ratterizzo".
 * https://wordpress.org/support/topic/504-time-out-issue/
 *
 * @package MLA tax query Example
 * @version 1.07
 */

/*
Plugin Name: MLA tax query Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Replaces the WP_Query tax_query with a more efficient, direct SQL query
Author: David Lingren
Version: 1.07
Author URI: http://fairtradejudaica.org/our-story/staff/

Copyright 2013 - 2017 David Lingren

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
 * Class MLA tax query Example hooks three of the filters provided by the [mla_gallery] shortcode
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding enerything
 * else inside a class means this is the only name you have to worry about.
 *
 * @package MLA tax query Example
 * @since 1.00
 */
class MLATaxQueryExample {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 1.00
	 *
	 * @return	void
	 */
	public static function initialize() {
		// The filters are only useful for front-end posts/pages; exit if in the admin section
		if ( is_admin() )
			return;

		add_filter( 'mla_gallery_attributes', 'MLATaxQueryExample::mla_gallery_attributes', 10, 1 );
		add_filter( 'mla_gallery_arguments', 'MLATaxQueryExample::mla_gallery_arguments', 10, 1 );
		add_filter( 'mla_gallery_query_arguments', 'MLATaxQueryExample::mla_gallery_query_arguments', 10, 1 );
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
	 * Save the keyword search attributes, if present
	 *
	 * @since 1.02
	 *
	 * @var	array
	 */
	private static $search_attributes = array();

	/**
	 * MLA Gallery (Display) Attributes
	 *
	 * This filter gives you an opportunity to record or modify the arguments passed in to the shortcode
	 * before they are merged with the default arguments used for the gallery display.
	 *
	 * The $shortcode_attributes array is where you will find any of your own parameters that are coded in the
	 * shortcode, e.g., [mla_gallery my_custom_sql="attachment_tag=fireplaces"].
	 *
	 * @since 1.00
	 *
	 * @param	array	the shortcode parameters passed in to the shortcode
	 *
	 * @return	array	updated shortcode attributes
	 */
	public static function mla_gallery_attributes( $shortcode_attributes ) {
		// Save the attributes for use in the later filters
		self::$shortcode_attributes = $shortcode_attributes;

		// See if we are involved in processing this shortcode
		if ( isset( self::$shortcode_attributes['my_custom_sql'] ) ) {
			unset( $shortcode_attributes['my_custom_sql'] );

			// Determine output type; pagination or gallery display
			if ( isset( self::$shortcode_attributes['mla_output'] ) ) {
				$output_parameters = array_map( 'strtolower', array_map( 'trim', explode( ',', self::$shortcode_attributes['mla_output'] ) ) );
				self::$shortcode_attributes['is_pagination'] = in_array( $output_parameters[0], array( 'previous_page', 'next_page', 'paginate_links' ) ); 
			} else {
				self::$shortcode_attributes['is_pagination'] = false; 
			}

			// Parameters passed to the posts_search filter function in MLAData
			self::$search_attributes = array( 'debug' => 'none', 'sentence' => false, 'exact' => false, 'mla_search_connector' => 'AND' );
			if ( ! empty( self::$shortcode_attributes['s'] ) ) {

				foreach ( self::$shortcode_attributes as $key => $value ) {
					switch ( $key ) {
					case 'sentence':
					case 'exact':
						if ( ! empty( $value ) && ( 'true' == strtolower( $value ) ) ) {
							self::$search_attributes[ $key ] = true;
						} else {
							self::$search_attributes[ $key ] = false;
						}

						unset( $shortcode_attributes[ $key ] );
						break;
					case 'mla_search_connector':
						if ( ! empty( $value ) && ( 'OR' == strtoupper( $value ) ) ) {
							self::$search_attributes[ $key ] = 'OR';
						} else {
							self::$search_attributes[ $key ] = 'AND';
						}

						unset( $shortcode_attributes[ $key ] );
						break;
					case 'mla_search_fields':
						if ( ! empty( $value ) ) {
							self::$search_attributes[ $key ] = $value;
						}

						unset( $shortcode_attributes[ $key ] );
						break;
					case 's':
						self::$search_attributes[ $key ] = $value;
						unset( $shortcode_attributes[ $key ] );
						break;
					default:
						// ignore anything else
					} // switch $key
				} // foreach $shortcode_attributes 

				// mla_terms_taxonomies is shared with keyword search.
				self::$search_attributes['mla_terms_search']['taxonomies'] = MLACore::mla_supported_taxonomies( 'term-search' );

				if ( empty( self::$search_attributes['mla_search_fields'] ) ) {
					self::$search_attributes['mla_search_fields'] = array( 'title', 'content' );
				} else {
					self::$search_attributes['mla_search_fields'] = array_filter( array_map( 'trim', explode( ',', self::$search_attributes['mla_search_fields'] ) ) );
					self::$search_attributes['mla_search_fields'] = array_intersect( array( 'title', 'content', 'excerpt', 'name' ), self::$search_attributes['mla_search_fields'] );
				}
			}

			// Determine query type
			if ( isset( self::$shortcode_attributes['post_mime_type'] ) || isset( self::$search_attributes['s'] ) ) {
				self::$shortcode_attributes['is_double'] = true;
			} else {
				$my_query_vars = self::$shortcode_attributes['my_custom_sql'];
				if ( empty( $my_query_vars ) ) {
					$my_query_vars = array();
				} elseif ( is_string( $my_query_vars ) ) {
					$my_query_vars = shortcode_parse_atts( $my_query_vars );
				}

				if ( isset( $my_query_vars['order'] ) || isset( $my_query_vars['orderby'] ) || isset( $my_query_vars['author'] ) ) {
					self::$shortcode_attributes['is_double'] = true;
				} else {
					// Test for exclude-only queries
					self::_find_ttids( $my_query_vars, $ttids, $exclude_ttids );
					if ( empty( $ttids ) && ! empty( $exclude_ttids ) ) {
						self::$shortcode_attributes['is_double'] = true;
					} else {
						self::$shortcode_attributes['is_double'] = false;
					}
				}
			}
		} // my_custom_sql

		return $shortcode_attributes;
	} // mla_gallery_attributes

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
	 * used for the gallery display.
	 *
	 * @since 1.00
	 *
	 * @param	array	shortcode arguments merged with gallery display defaults, so every possible parameter is present
	 *
	 * @return	array	updated gallery display arguments
	 */
	public static function mla_gallery_arguments( $all_display_parameters ) {
		if ( isset( self::$shortcode_attributes['my_custom_sql'] ) ) {
			/*
			 * Determine output type; if it's pagination, count the rows and add the result
			 * to the parameters. See the "single_query()" and "double_query()" functions.
			 */
			if ( self::$shortcode_attributes['is_pagination'] ) {
				if ( self::$shortcode_attributes['is_double'] ) {
					$all_display_parameters['mla_paginate_rows'] = self::double_query( NULL, true );
				} else {
					$all_display_parameters['mla_paginate_rows'] = self::single_query( NULL, true );
				}
			}
		} // my_custom_sql present

		self::$all_display_parameters = $all_display_parameters;
		return $all_display_parameters;
	} // mla_gallery_arguments

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
	public static function mla_gallery_query_arguments( $all_query_parameters ) {
		/*
		 * This example executes a custom SQL query that is much simpler than the usual WordPress
		 * WP_Query arguments. See the "single_query()" and "double_query()" functions.
		 *
		 * We use a shortcode parameter of our own to apply this filter on a gallery-by-gallery
		 * basis, leaving other [mla_gallery] instances untouched. If the "my_custom_sql"
		 * parameter is not present, we have nothing to do. If the parameter IS present,
		 * single_query() or double_query() extracts  taxonomy values, then builds a custom
		 * query that does a simple, high-performance search.
		 */		
		if ( isset( self::$shortcode_attributes['my_custom_sql'] ) ) {
			if ( self::$shortcode_attributes['is_double'] ) {
				$all_query_parameters = self::double_query( $all_query_parameters );
			} else {
				$all_query_parameters = self::single_query( $all_query_parameters );
			}
		}

		return $all_query_parameters;
	} // mla_gallery_query_arguments

	/**
	 * Extract include and exclude term_taxonomy_id values for the query
	 *
	 * @since 1.07
	 *
	 * @param	array	$my_query_vars parsed content of the 'my_custom_sql' parameter
	 * @param	array	$include_ttids filled with include terms on output, passed by reference
	 * @param	array	$exclude_ttids filled with exclude terms on output, passed by reference
	 */
	private static function _find_ttids( $my_query_vars, &$include_ttids, &$exclude_ttids ) {
		// Start with empty parameter values
		$include_ttids = array();
		$exclude_ttids = array();

		// Find taxonomy argument, if present, and collect terms
		$taxonomies = get_object_taxonomies( 'attachment', 'names' );
		foreach( $taxonomies as $taxonomy ) {
			if ( empty( $my_query_vars[ $taxonomy ] ) ) {
				continue;
			}

			// Found the taxonomy; collect the terms
			$include_children =  isset( $my_query_vars['include_children'] ) && 'true' == strtolower( trim( $my_query_vars['include_children'] ) );

			// Allow for multiple term slug values, separate includes from excludes
			$terms = array();
			$excludes = array();
			$slugs = explode( ',', $my_query_vars[ $taxonomy ] );
			foreach ( $slugs as $slug ) {
				if ( 0 === strpos( $slug, '/' ) ) {
					$args = array( 'slug' => substr( $slug, 1 ), 'hide_empty' => false );
					$excludes = array_merge( $excludes, get_terms( $taxonomy, $args ) );
				} else {
					$args = array( 'slug' => $slug, 'hide_empty' => false );
					$terms = array_merge( $terms, get_terms( $taxonomy, $args ) );
				}
			}

			foreach( $terms as $term ) {
				// Index by ttid to remove duplicates
				$include_ttids[ $term->term_taxonomy_id ] = $term->term_taxonomy_id;

				if ( $include_children ) {
					$args = array( 'child_of' => $term->term_id, 'hide_empty' => false );
					$children = get_terms( 'attachment_category', $args );
					foreach( $children as $child ) {
						$include_ttids[] = $child->term_taxonomy_id;
					}
				} // include_children
			} // $term

			foreach( $excludes as $exclude ) {
				// Index by ttid to remove duplicates
				$exclude_ttids[ $exclude->term_taxonomy_id ] = $exclude->term_taxonomy_id;

				if ( $include_children ) {
					$args = array( 'child_of' => $exclude->term_id, 'hide_empty' => false );
					$children = get_terms( 'attachment_category', $args );
					foreach( $children as $child ) {
						$exclude_ttids[] = $child->term_taxonomy_id;
					}
				} // include_children
			} // $exclude

			break;
		} // foreach $taxonomy
	} // _find_ttids

	/**
	 * Custom query support function, taxonomy terms only
	 *
	 * Calculates found_rows for pagination or included attachments for gallery.
	 *
	 * The queries supported in this function's "my_custom_sql" parameter include:
	 *
	 * - one or more taxonomy term lists, with include_children
	 *
	 * @since 1.00
	 *
	 * @param	array	shortcode arguments merged with attachment selection defaults, so every possible parameter is present
	 * @param	boolean	true for pagination result, false for gallery result
	 *
	 * @return	integer|array	found_rows or updated query parameters
	 */
	private static function single_query( $all_query_parameters, $is_pagination = false ) {
		global $wpdb;

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

		self::_find_ttids( $my_query_vars, $ttids, $exclude_ttids );

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
			// Both includes and excludes are empty; return nothing
			if ( empty( $exclude_ttids ) ) {
				$placeholders[] = '%s';
				$query_parameters[] = '0';
			}
		}

		if ( empty( $placeholders ) ) {
			// No includes, only excludes
			$query[] = 'WHERE ( 1=1';
		} else {
			$query[] = 'WHERE ( tr.term_taxonomy_id IN (' . join( ',', $placeholders ) . ')';
		}

		if ( !empty( $exclude_ttids ) ) {
			$placeholders = array();
			foreach ( $exclude_ttids as $ttid ) {
				$placeholders[] = '%s';
				$query_parameters[] = $ttid;
			}

			// Build the excludes as a sub query			
			$query[] = 'AND tr.object_id NOT IN (';
			$query[] = "SELECT DISTINCT object_id FROM {$wpdb->term_relationships}";
			$query[] = 'WHERE ( term_taxonomy_id IN (' . join( ',', $placeholders ) . ') ) )';
		}

		$query[] = ')';
		
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
		if ( 0 < count( $query_parameters ) ) {
			$query = $wpdb->prepare( $query, $query_parameters );
		}
		
		if ( $is_pagination ) {
			return $wpdb->get_var( $query );
		}

		$ids = $wpdb->get_results( $query );
		if ( is_array( $ids ) ) {
			$includes = array();
			foreach ( $ids as $id ) {
				$includes[] = $id->object_id;
			}
			$all_query_parameters['include'] = implode( ',', $includes );
		} else {
			$all_query_parameters['include'] = '1'; // return no images
		}

		return $all_query_parameters;
	} // single_query

	/**
	 * Translates query parameters to a valid SQL order by clause.
	 *
	 * Accepts one or more valid columns, with or without ASC/DESC. Adapted from
	 * /media-library-assistant/includes/class-mla-shortcode-support.php function _validate_sql_orderby().
	 *
	 * @since 1.07
	 *
	 * @param array Validated query parameters; 'order', 'orderby', 'meta_key', 'post__in'.
	 * @param string Optional. Database table prefix; can be empty. Default taken from $wpdb->posts.
	 * @param array Optional. Field names (keys) and database column equivalents (values). Defaults from [mla_gallery].
	 * @param array Optional. Field names (values) that require a BINARY prefix to preserve case order. Default array()
	 * @return string|bool Returns the orderby clause if present, false otherwise.
	 */
	private static function _validate_sql_orderby( $query_parameters, $table_prefix = NULL, $allowed_keys = NULL, $binary_keys = array() ){
		global $wpdb;

		$results = array ();
		$order = isset( $query_parameters['order'] ) ? ' ' . trim( strtoupper( $query_parameters['order'] ) ) : '';
		$orderby = isset( $query_parameters['orderby'] ) ? $query_parameters['orderby'] : 'none';
		$meta_key = isset( $query_parameters['meta_key'] ) ? $query_parameters['meta_key'] : '';

		if ( is_null( $table_prefix ) ) {
			$table_prefix = $wpdb->posts . '.';
		}

		if ( is_null( $allowed_keys ) ) {
			$allowed_keys = array(
				'empty_orderby_default' => 'post_date',
				'explicit_orderby_field' => 'post__in',
				'explicit_orderby_column' => 'ID',
				'id' => 'ID',
				'author' => 'post_author',
				'date' => 'post_date',
				'description' => 'post_content',
				'content' => 'post_content',
				'title' => 'post_title',
				'caption' => 'post_excerpt',
				'excerpt' => 'post_excerpt',
				'slug' => 'post_name',
				'name' => 'post_name',
				'modified' => 'post_modified',
				'parent' => 'post_parent',
				'menu_order' => 'menu_order',
				'mime_type' => 'post_mime_type',
				'comment_count' => 'post_content',
				'rand' => 'RAND()',
			);
		}

		if ( empty( $orderby ) ) {
			if ( ! empty( $allowed_keys['empty_orderby_default'] ) ) {
				return 'ORDER BY ' . $table_prefix . $allowed_keys['empty_orderby_default'] . " {$order}";
			} else {
				return 'ORDER BY ' . "{$table_prefix}post_date {$order}";
			}
		} elseif ( 'none' == $orderby ) {
			return '';
		} elseif ( ! empty( $allowed_keys['explicit_orderby_field'] ) ) {
			$explicit_field = $allowed_keys['explicit_orderby_field'];
			if ( $orderby == $explicit_field ) {
				if ( ! empty( $query_parameters[ $explicit_field ] ) ) {
					$explicit_order = implode(',', array_map( 'absint', $query_parameters[ $explicit_field ] ) );

					if ( ! empty( $explicit_order ) ) {
						$explicit_column = $allowed_keys['explicit_orderby_column'];
						return 'ORDER BY ' . "FIELD( {$table_prefix}{$explicit_column}, {$explicit_order} )";
					} else {
						return '';
					}
				}
			}
		}

		if ( ! empty( $meta_key ) ) {
			$allowed_keys[ $meta_key ] = "$wpdb->postmeta.meta_value";
			$allowed_keys['meta_value'] = "$wpdb->postmeta.meta_value";
			$allowed_keys['meta_value_num'] = "$wpdb->postmeta.meta_value+0";
		}

		$obmatches = preg_split('/\s*,\s*/', trim($query_parameters['orderby']));
		foreach ( $obmatches as $index => $value ) {
			$count = preg_match('/([a-z0-9_]+)(\s+(ASC|DESC))?/i', $value, $matches);
			if ( $count && ( $value == $matches[0] ) ) {
				$matches[1] = strtolower( $matches[1] );
				if ( isset( $matches[2] ) ) {
					$matches[2] = strtoupper( $matches[2] );
				}

				if ( array_key_exists( $matches[1], $allowed_keys ) ) {
					if ( ( 'rand' == $matches[1] ) || ( 'random' == $matches[1] ) ){
							$results[] = 'RAND()';
					} else {
						switch ( $matches[1] ) {
							case $meta_key:
							case 'meta_value':
								$matches[1] = "$wpdb->postmeta.meta_value";
								break;
							case 'meta_value_num':
								$matches[1] = "$wpdb->postmeta.meta_value+0";
								break;
							default:
								if ( in_array( $matches[1], $binary_keys ) ) {
									$matches[1] = 'BINARY ' . $table_prefix . $allowed_keys[ $matches[1] ];
								} else {
									$matches[1] = $table_prefix . $allowed_keys[ $matches[1] ];
								}
						} // switch $matches[1]

						$results[] = isset( $matches[2] ) ? $matches[1] . $matches[2] : $matches[1] . $order;
					} // not 'rand'
				} // allowed key
			} // valid column specification
		} // foreach $obmatches

		$orderby = implode( ', ', $results );
		if ( empty( $orderby ) ) {
			return false;
		}

		return 'ORDER BY ' . $orderby;
	}

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
	 * - one or more author IDs
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

		/*
		 * This example executes a custom SQL query that is more efficient than the usual
		 * WordPress WP_Query arguments.
		 *
		 * The subquery is on taxonomy and term(s) only, yielding a list of object_id
		 * (post ID) values.
		 *
		 * The main query filters the list by post_mime_type and/or keyword search, orders
		 * it and paginates it.
		 */		

		// Make sure $my_query_vars is an array, even if it's empty
		$my_query_vars = self::$shortcode_attributes['my_custom_sql'];
		if ( empty( $my_query_vars ) ) {
			$my_query_vars = array();
		} elseif ( is_string( $my_query_vars ) ) {
			$my_query_vars = shortcode_parse_atts( $my_query_vars );
		}

		self::_find_ttids( $my_query_vars, $ttids, $exclude_ttids );

		// Build an array of SQL clauses for the term_relationships query
		$subquery = array();
		$subquery_parameters = array();

		$subquery[] = "SELECT DISTINCT tr.object_id FROM {$wpdb->term_relationships} as tr";

		$placeholders = array();
		if ( ! empty( $ttids ) ) {
			foreach ( $ttids as $ttid ) {
				$placeholders[] = '%s';
				$subquery_parameters[] = $ttid;
			}
		} else {
			// Both includes and excludes are empty; return nothing
			if ( empty( $exclude_ttids ) ) {
				$placeholders[] = '%s';
				$subquery_parameters[] = '0';
			}
		}

		if ( empty( $placeholders ) ) {
			// No includes, only excludes
			$subquery[] = 'WHERE ( 1=1';
		} else {
			$subquery[] = 'WHERE ( tr.term_taxonomy_id IN (' . join( ',', $placeholders ) . ')';
		}

		if ( !empty( $exclude_ttids ) ) {
			$placeholders = array();
			foreach ( $exclude_ttids as $ttid ) {
				$placeholders[] = '%s';
				$subquery_parameters[] = $ttid;
			}

			// Build the excludes as a sub query			
			$subquery[] = 'AND tr.object_id NOT IN (';
			$subquery[] = "SELECT DISTINCT object_id FROM {$wpdb->term_relationships}";
			$subquery[] = 'WHERE ( term_taxonomy_id IN (' . join( ',', $placeholders ) . ') ) )';
		}

		$subquery[] = ')';
		
		$subquery =  join(' ', $subquery);

		// Build an array of SQL clauses for the posts query
		$query = array();
		$query_parameters = array();

		if ( $is_pagination ) {
			$query[] = "SELECT COUNT( * ) FROM {$wpdb->posts} as p";
		} else {
			$query[] = "SELECT ID FROM {$wpdb->posts} as p";
		}

		$query[] = 'WHERE ( ( p.ID IN ( ' . $wpdb->prepare( $subquery, $subquery_parameters ) . ' ) )';

		if ( ! empty( self::$shortcode_attributes['post_mime_type'] ) ) {
			if ( 'all' != strtolower( self::$shortcode_attributes['post_mime_type'] ) ) {
				$query[] = str_replace( '%', '%%', wp_post_mime_type_where( self::$shortcode_attributes['post_mime_type'], 'p' ) );
			}
		} else {
			$query[] = "AND (p.post_mime_type LIKE 'image/%%')";
		}

		if ( ! empty( $my_query_vars['author'] ) ) {
			$query[] = "AND (p.post_author IN (" . $my_query_vars['author'] . ") )";
		}

		if ( isset( self::$search_attributes['s'] ) ) {
			global $wpdb;
			$prefix = $wpdb->posts . '.';

			MLAQuery::$search_parameters = self::$search_attributes;

			$query_object = (object) array();
			$search_clause = MLAQuery::mla_query_posts_search_filter( '', $query_object );
			$search_clause = str_replace( array( $prefix, '%' ), array( 'p.', '%%' ), $search_clause );
			$query[] = $search_clause;

			MLAQuery::$search_parameters = array( 'debug' => 'none' );
		}

		// Close the WHERE clause
		$query[] = ')';

		// ORDER BY clause
		$orderby = self::_validate_sql_orderby( $my_query_vars, 'p.' );
		if ( ! empty( $orderby ) ) {
			$query[] = $orderby;
		}
		
		// Tell the final query to respect our orderby
		$all_query_parameters['orderby'] = 'post__in';
		$all_query_parameters['order'] = 'ASC';

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

		$query = join(' ', $query);
		if ( 0 < count( $query_parameters ) ) {
			$query = $wpdb->prepare( $query, $query_parameters );
		}
		
		if ( $is_pagination ) {
			return $wpdb->get_var( $query );
		}

		$ids = $wpdb->get_results( $query );
		if ( is_array( $ids ) ) {
			$includes = array();
			foreach ( $ids as $id ) {
				$includes[] = $id->ID;
			}
			$all_query_parameters['include'] = implode( ',', $includes );
		} else {
			$all_query_parameters['include'] = '1'; // return no items
		}

		return $all_query_parameters;
	} // double_query
} // Class MLATaxQueryExample

// Install the filters at an early opportunity
add_action('init', 'MLATaxQueryExample::initialize');
?>
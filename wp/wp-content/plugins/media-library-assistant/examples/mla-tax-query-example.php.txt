<?php
/**
 * Provides a custom example of hooking the filters provided by the [mla_gallery] shortcode:
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
 * A custom shortcode parameter, "my_custom_sql", activates the logic in this plugin. See the
 * "mla_gallery_query_arguments" function for documentation on the parameter.
 *
 * @package MLA tax query Example
 * @version 1.03
 */

/*
Plugin Name: MLA tax query Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Replaces the WP_Query tax_query with a more efficient, direct SQL query
Author: David Lingren
Version: 1.03
Author URI: http://fairtradejudaica.org/our-story/staff/

Copyright 2013 - 2016 David Lingren

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
		/*
		 * Save the attributes for use in the later filters
		 */
		self::$shortcode_attributes = $shortcode_attributes;

		/*
		 * See if we are involved in processing this shortcode
		 */
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

				if ( isset( $my_query_vars['order'] ) || isset( $my_query_vars['orderby'] ) ) {
					self::$shortcode_attributes['is_double'] = true;
				} else {
					self::$shortcode_attributes['is_double'] = false;
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
		 *
		 * The "my_custom_sql" parameter accepts these query arguments:
		 * - one or more taxonomy=slug(,slug)... arguments, which will be joined by OR
		 * - include_children=true
		 * - order and/or orderby
		 *
		 * The shortcode can also contain the post_mime_type and/or keyword search parameters
		 * to further filter the results. The double_query() function is called when the request
		 * contains post_mime_type, keyword search or orderby/order parameters.
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
		if ( $is_pagination ) {
			$count = $wpdb->get_var( $wpdb->prepare( $query, $query_parameters ) );
			return $count;
		}

		$ids = $wpdb->get_results( $wpdb->prepare( $query, $query_parameters ) );
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

		/*
		 * This example executes a custom SQL query that is more efficient than the usual
		 * WordPress WP_Query arguments.
		 *
		 * The subquery is on taxonomy and term(s) only, yielding a list of object_id
		 * (post ID) values.
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
				$placeholders[] = '%s';
				$subquery_parameters[] = '0';
		}

		$subquery[] = 'WHERE ( tr.term_taxonomy_id IN (' . join( ',', $placeholders ) . ') )';
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

		/*
		 * ORDER BY clause
		 */
		if ( ! empty( $my_query_vars['orderby'] ) ) {
			$orderby = strtolower( $my_query_vars['orderby'] );
		} else {
			$orderby = 'none';
		}
		$all_query_parameters['orderby'] = 'post__in';

		if ( ! empty( $my_query_vars['order'] ) ) {
			$order = strtoupper( $my_query_vars['order'] );
			if ( 'DESC' != $order ) {
				$order = 'ASC';
			}
		} else {
			$order = 'ASC';
		}
		$all_query_parameters['order'] = 'ASC';

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

		$query = join(' ', $query);
		if ( $is_pagination ) {
			return $wpdb->get_var( $wpdb->prepare( $query, $query_parameters ) );
		}

		$ids = $wpdb->get_results( $wpdb->prepare( $query, $query_parameters ) );
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

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLATaxQueryExample::initialize');
?>
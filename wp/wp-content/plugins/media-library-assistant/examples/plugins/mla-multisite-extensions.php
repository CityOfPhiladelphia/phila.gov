<?php
/**
 * Adds Multisite filters to MLA shortcodes:
 *
 * 1. The "site_id=id[,id...]|all" parameter names one or more or all sites to query.
 * 2. 
 * 3. 
 *
 * This example plugin uses the WP 4.6+ terminology of Network and Site (not Blog).
 *
 * Created for support topic "Using Shortcodes to retrieve media from another sites media library"
 * opened on 7/12/2017 by "jeynon (@jeynon)".
 * https://wordpress.org/support/topic/using-shortcodes-to-retrieve-media-from-another-sites-media-library/
 *
 * @package MLA Multisite Extensions
 * @version 1.03
 */

/*
Plugin Name: MLA Multisite Extensions
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Adds Multisite filters to MLA shortcodes
Author: David Lingren
Version: 1.03
Author URI: http://fairtradejudaica.org/our-story/staff/

Copyright 2017 David Lingren

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
 * Class MLA Multisite Extensions Adds Multisite filters to MLA shortcodes
 *
 * @package MLA Multisite Extensions
 * @since 1.00
 */
class MLAMultisiteExtensions {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 1.00
	 */
	public static function initialize() {
//error_log( __LINE__ . ' MLAMultisiteExtensions::initialize() get_sites = ' . var_export( get_sites(), true ), 0 );

		add_filter( 'mla_gallery_attributes', 'MLAMultisiteExtensions::mla_gallery_attributes', 10, 1 );
		add_filter( 'mla_gallery_query_arguments', 'MLAMultisiteExtensions::mla_gallery_query_arguments', 10, 1 );
		add_action( 'mla_gallery_wp_query_object', 'MLAMultisiteExtensions::mla_gallery_wp_query_object', 10, 1 );
		add_filter( 'mla_gallery_the_attachments', 'MLAMultisiteExtensions::mla_gallery_the_attachments', 10, 2 );
		add_filter( 'mla_gallery_item_initial_values', 'MLAMultisiteExtensions::mla_gallery_item_initial_values', 10, 2 );
		add_filter( 'mla_gallery_item_values', 'MLAMultisiteExtensions::mla_gallery_item_values', 10, 1 );
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
	public static function mla_gallery_attributes( $shortcode_attributes ) {
		//error_log( __LINE__ . ' MLAMultisiteExtensions::mla_gallery_attributes $shortcode_attributes = ' . var_export( $shortcode_attributes, true ), 0 );

		// Save the original attributes for use in the later filters
		if ( !isset( self::$all_query_parameters['multi_site_query'] ) ) {
			self::$shortcode_attributes = $shortcode_attributes;
			unset( $shortcode_attributes['site_id'] );
		}
	
		return $shortcode_attributes;
	} // mla_gallery_attributes

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
	public static function mla_gallery_query_arguments( $all_query_parameters ) {
		global $post;
		//error_log( __LINE__ . ' MLAMultisiteExtensions::mla_gallery_query_arguments $all_query_parameters = ' . var_export( $all_query_parameters, true ), 0 );

		// Save the original parameters for use in the later filters
		if ( !isset( self::$all_query_parameters['multi_site_query'] ) ) {
			//error_log( __LINE__ . ' MLAMultisiteExtensions::mla_gallery_query_arguments self::$shortcode_attributes = ' . var_export( self::$shortcode_attributes, true ), 0 );

			// Taxonomy parameters are handled separately
			// {tax_slug} => 'term' | array ( 'term', 'term', ... )
			// 'tax_query' => ''
			// 'tax_input' => ''
			// 'tax_relation' => 'OR', 'AND' (default),
			// 'tax_operator' => 'OR' (default), 'IN', 'NOT IN', 'AND',
			// 'tax_include_children' => true (default), false
			$shortcode_attributes = self::$shortcode_attributes;

			$all_taxonomies = get_taxonomies( array ( 'show_ui' => true ), 'names' );
			foreach( $shortcode_attributes as $key => $value ) {
				if ( array_key_exists( $key, $all_taxonomies ) ) {
					$all_query_parameters[ $key ] = $shortcode_attributes[ $key ];
				}
			}

			if ( !empty( $shortcode_attributes['tax_query'] ) ) {
				$all_query_parameters['tax_query'] = $shortcode_attributes['tax_query'];
			}

			if ( !empty( $shortcode_attributes['tax_input'] ) ) {
				$all_query_parameters['tax_input'] = $shortcode_attributes['tax_input'];
			}

			if ( !empty( $shortcode_attributes['tax_relation'] ) ) {
				$all_query_parameters['tax_relation'] = $shortcode_attributes['tax_relation'];
			}

			if ( !empty( $shortcode_attributes['tax_operator'] ) ) {
				$all_query_parameters['tax_operator'] = $shortcode_attributes['v'];
			}

			if ( !empty( $shortcode_attributes['tax_include_children'] ) ) {
				$all_query_parameters['tax_include_children'] = $shortcode_attributes['tax_include_children'];
			}

			self::$all_query_parameters = $all_query_parameters;
		}
		//error_log( __LINE__ . ' MLAMultisiteExtensions::mla_gallery_query_arguments $all_query_parameters = ' . var_export( $all_query_parameters, true ), 0 );
		
		if ( isset( self::$shortcode_attributes['site_id'] ) ) {
			if ( 'all' === trim( strtolower( self::$shortcode_attributes['site_id'] ) ) ) {
				$sites = get_sites( array( 'network_id' => 1 ) );
				$site_ids = array();
				foreach( $sites as $site ) {
					$site_ids[] = $site->blog_id;
				}
			} else {
				$site_ids = array_map( 'absint', explode( ',', self::$shortcode_attributes['site_id'] ) );
				foreach ( $site_ids as &$site ) {
					if ( 0 === $site ) {
						$site = get_current_blog_id();
					}
				}

				$site_ids = array_unique( $site_ids );
				self::$shortcode_attributes['site_id'] = implode( ',', $site_ids );
			}

			// Accumulate attachments from multiple blogs and short-circuit the normal query
			if ( 1 < count( $site_ids ) ) {
				// Save the site_id parameter, then remove it from the site-specific queries
				$save_site_id = self::$shortcode_attributes['site_id'];
				unset( self::$shortcode_attributes['site_id'] );

				// We must do the multi-site pagination
				if ( !empty( $all_query_parameters['posts_per_page'] ) ) {
					self::$all_query_parameters['multi_site_limit'] = absint( $all_query_parameters['posts_per_page'] );
				} elseif ( !empty( $all_query_parameters['numberposts'] ) ) {
					self::$all_query_parameters['multi_site_limit'] = absint( $all_query_parameters['numberposts'] );
				}
				
				if ( isset( self::$all_query_parameters['multi_site_limit'] ) ) {
					if ( !empty( $all_query_parameters['offset'] ) ) {
						self::$all_query_parameters['multi_site_offset'] = $all_query_parameters['offset'];
					} else {
						if ( !empty( $all_query_parameters[ self::$shortcode_attributes['mla_page_parameter' ] ] ) ) {
							$page = $all_query_parameters[ self::$shortcode_attributes['mla_page_parameter' ] ];
						} else {
							$page = 1;
						}

						self::$all_query_parameters['multi_site_offset'] = ( absint( $page ) - 1 ) * self::$all_query_parameters['multi_site_limit'];
					}

					// Remove pagination from site-specific queries
					$all_query_parameters['numberposts'] = 0;
					$all_query_parameters['posts_per_page'] = 0;
					$all_query_parameters['posts_per_archive_page'] = 0;
					$all_query_parameters['paged'] = NULL;
					$all_query_parameters['offset'] = NULL;
					$all_query_parameters['mla_paginate_current'] = NULL;
					$all_query_parameters['mla_paginate_total'] = NULL;
					$all_query_parameters[ self::$shortcode_attributes['mla_page_parameter' ] ] = NULL;
				}

				// Tell all filters this is not the original query
				self::$all_query_parameters['multi_site_query'] = true;
				self::$all_attachments = array();
				foreach( $site_ids as $site_id ) {
					$blog_details = get_blog_details( $site_id );
					if ( $blog_details ) {
						switch_to_blog( $site_id );
						$attachments = MLAShortcodes::mla_get_shortcode_attachments( $post->ID, $all_query_parameters, true );
						restore_current_blog();

						if ( is_array( $attachments ) ) {
							unset( $attachments['found_rows'] );
							unset( $attachments['max_num_pages'] );
							self::$all_attachments[ $site_id ] = $attachments;
						}
					} // $blog_details
				} // foreach $site_id
				unset( self::$all_query_parameters['multi_site_query'] );

				// Restore the site_id parameter, then replace the original query with a quick alternative that returns no attachments
				self::$shortcode_attributes['site_id'] = $save_site_id;
				return array( 'ids' => '1' );
			} // multi-blog query

			$blog_details = get_blog_details( reset( $site_ids ) );

			if ( $blog_details ) {
				switch_to_blog( current( $site_ids ));
			} else {
				unset( self::$shortcode_attributes['site_id'] );
			}
		}

		return $all_query_parameters;
	} // mla_gallery_query_arguments

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
	 * @param	array	$query_arguments Query arguments passed to WP_Query->query
	 */
	public static function mla_gallery_wp_query_object( $query_arguments ) {
		//error_log( __LINE__ . ' MLAMultisiteExtensions::mla_gallery_wp_query_object $query_arguments = ' . var_export( $query_arguments, true ), 0 );

		self::$wp_query_properties = array();
		self::$wp_query_properties ['request'] = MLAShortcodes::$mla_gallery_wp_query_object->request;
		//self::$wp_query_properties ['query_vars'] = MLAShortcodes::$mla_gallery_wp_query_object->query_vars;
		self::$wp_query_properties ['post_count'] = MLAShortcodes::$mla_gallery_wp_query_object->post_count;

		//error_log( __LINE__ . ' MLAMultisiteExtensions::mla_gallery_wp_query_object self::$wp_query_properties = ' . var_export( self::$wp_query_properties, true ), 0 );

		if ( isset( self::$shortcode_attributes['site_id'] ) ) {
			restore_current_blog();
			self::$current_site_id = get_current_blog_id();
		}
	} // mla_gallery_wp_query_object

	/**
	 * Translates query parameters to orderby rules.
	 *
	 * Accepts one or more valid columns, with or without ASC/DESC.
	 * Enhanced version of /wp-includes/formatting.php function sanitize_sql_orderby().
	 *
	 * @since 1.02
	 * @uses self::$all_query_parameters
	 *
	 * @return array Returns the orderby rules if present, empty array otherwise.
	 */
	private static function _validate_orderby(){

		$results = array ();
		$order = isset( self::$all_query_parameters['order'] ) ? trim( strtoupper( self::$all_query_parameters['order'] ) ) : 'ASC';
		$orderby = isset( self::$all_query_parameters['orderby'] ) ? self::$all_query_parameters['orderby'] : '';
		$meta_key = isset( self::$all_query_parameters['meta_key'] ) ? self::$all_query_parameters['meta_key'] : '';

		$allowed_keys = array(
			'empty_orderby_default' => 'site_id',
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
			'site_id' => 'site_id',
			'rand' => 'rand',
		);

		if ( empty( $orderby ) ) {
			if ( ! empty( $allowed_keys['empty_orderby_default'] ) ) {
				return array( array( 'field' => $allowed_keys['empty_orderby_default'], 'order' => $order ) ) ;
			} else {
				return array( array( 'field' => 'site_id', 'order' => $order ) ) ;
			}
		} elseif ( 'none' == $orderby ) {
			return array();
		}

		if ( ! empty( $meta_key ) ) {
			$allowed_keys[ $meta_key ] = "custom:$meta_key";
			$allowed_keys['meta_value'] = "custom:$meta_key";
			$allowed_keys['meta_value_num'] = "custom:$meta_key";
		}

		$obmatches = preg_split('/\s*,\s*/', trim(self::$all_query_parameters['orderby']));
		foreach ( $obmatches as $index => $value ) {
			$count = preg_match('/([a-z0-9_]+)(\s+(ASC|DESC))?/i', $value, $matches);
			if ( $count && ( $value == $matches[0] ) ) {
				$matches[1] = strtolower( $matches[1] );
				if ( isset( $matches[2] ) ) {
					$matches[2] = strtoupper( $matches[2] );
				}

				if ( array_key_exists( $matches[1], $allowed_keys ) ) {
					$results[] = isset( $matches[2] ) ? array( 'field' => $allowed_keys[ $matches[1] ], 'order' => trim( $matches[2] ) ) : array( 'field' => $allowed_keys[ $matches[1] ], 'order' => $order );
				} // allowed key
			} // valid column specification
		} // foreach $obmatches

//error_log( __LINE__ . ' MLAMultisiteExtensions::_validate_orderby $results = ' . var_export( $results, true ), 0 );
		return $results;
	} // _validate_orderby

	/**
	 * Compare two attachments and return:
	 *     -1 if the first is lower than the second
	 *      0 if they are equal
	 *      1 if the second is lower than the first OR if the first is NULL
	 *
	 * @since 1.02
	 *
	 * @param array $orderby ( $index => array( $field, $order ) ... )
	 * @param object $first WP_Post object
	 * @param object $second WP_Post object
	 * @param integer $level Optional, default 0; index in $orderby to use for comparison
	 */
	private static function _compare_attachments( $orderby, $first, $second, $level = 0 ) {
		if ( NULL === $first ) {
			return 1;
		}

		if ( count( $orderby ) <= $level ) {
			return -1;
		}

		$field = $orderby[ $level ]['field'];
		$order = $orderby[ $level ]['order'];

		if ( 'rand' === $field ) {
			return 51 < rand( 1, 100 ) ? -1 : 1;
		}

		if ( 'custom:' === substr( $field, 0, 7 ) ) {
			$custom = substr( $field, 7 );

			switch_to_blog( $first->site_id );
			$first_field = get_post_meta( $first->ID, $custom, true );
			restore_current_blog();

			switch_to_blog( $second->site_id );
			$second_field = get_post_meta( $second->ID, $custom, true );
			restore_current_blog();
		} else {
			$first_field = $first->{$field};
			$second_field = $second->{$field};
		}

		if ( $first_field === $second_field ) {
			return self::_compare_attachments( $orderby, $first, $second, ++$level );
		}

		if ( $first_field > $second_field ) {
			return 'DESC' === $order ? -1 : 1;
		}

		return 'DESC' === $order ? 1 : -1;
	} // _compare_attachments

	/**
	 * MLA Gallery The Attachments
	 *
	 * This filter gives you an opportunity to record or modify the array of items
	 * returned by the query.
	 *
	 * @since 1.00
	 *
	 * @param NULL $filtered_attachments initially NULL, indicating no substitution.
	 * @param array $attachments WP_Post objects returned by WP_Query->query, passed by reference
	 */
	public static function mla_gallery_the_attachments( $filtered_attachments, $attachments ) {
		if ( isset( self::$shortcode_attributes['site_id'] ) ) {
			if ( is_array( self::$all_attachments ) ) {
				$filtered_attachments = array();
				$orderby = self::_validate_orderby();
				
				if ( isset( self::$all_query_parameters['multi_site_limit'] ) ) {
					$offset = self::$all_query_parameters['multi_site_offset'];
					$limit = self::$all_query_parameters['multi_site_limit'];
				} else {
					$offset = 0;
					$limit = 0x7FFF;
				}

				foreach( self::$all_attachments as $site_id => &$attachments ) {
					if ( count( $attachments ) ) {

						$primary_attachments = &$filtered_attachments;
						$first = array_shift( $primary_attachments );
						unset( $filtered_attachments );
						$filtered_attachments = array();

						foreach( $attachments as $attachment ) {
							$attachment->site_id = $site_id;

							while ( 1 !== self::_compare_attachments( $orderby, $first, $attachment ) ) {
								$filtered_attachments[] = $first;
								$first = array_shift( $primary_attachments );
							}

							$filtered_attachments[] = $attachment;
						} // foreach attachment

						while ( !empty( $first ) ) {
							$filtered_attachments[] = $first;
							$first = array_shift( $primary_attachments );
						}
					} // if count attachments
					
					if ( 0 === $limit ) {
						break;
					}
				} // foreach site_id

				$filtered_attachments = array_slice ( $filtered_attachments, $offset, $limit );
				self::$all_attachments = NULL;
				$filtered_attachments['found_rows'] = count( $filtered_attachments );
				$filtered_attachments['max_num_pages'] = 0;
			} else {
				$site_id = self::$shortcode_attributes['site_id'];
				self::$attachment_count = 0;
				foreach ( $attachments as $index => &$attachment ) {
					if ( is_integer( $index ) ) {
						self::$attachment_count++;
						$attachment->site_id = $site_id;
					}
				}
			} // single-blog query
		}

		return $filtered_attachments;
	}

	/**
	 * Save the site_id from attachment to attachment
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $current_site_id = 0;

	/**
	 * Save the total number of attachments
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $attachment_count = 0;

	/**
	 * Save the attachments from a multi-blog query; key [blog][index]
	 *
	 * @since 1.01
	 *
	 * @var	array
	 */
	private static $all_attachments = NULL;

	/**
	 * MLA Gallery Item Initial Values
	 *
	 * This filter gives you an opportunity to add custom elements to each item
	 * returned by the query item-level processing occurs.
	 *
	 * @since 1.00
	 *
	 * @param array	$markup_values gallery-level parameter_name => parameter_value pairs
	 * @param array $attachment WP_Post object of the current item
	 */
	public static function mla_gallery_item_initial_values( $markup_values, $attachment ) {
		if ( isset( $attachment->site_id ) ) {
			if ( self::$current_site_id !== intval( $attachment->site_id ) ) {
				if ( ms_is_switched() ) {
					restore_current_blog();
				}

			self::$current_site_id = intval( $attachment->site_id );
			switch_to_blog( self::$current_site_id );
			}
		}

		return $markup_values;
	} // mla_gallery_item_initial_values

	/**
	 * MLA Gallery Item Values
	 *
	 * @since 1.00
	 *
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated substitution parameter name => value pairs
	 */
	public static function mla_gallery_item_values( $item_values ) {
		if ( 0 === --self::$attachment_count ) {
			if ( ms_is_switched() ) {
				restore_current_blog();
			}
		}

		//error_log( 'MLAGalleryHooksExample::mla_gallery_item_values $item_values = ' . var_export( $item_values, true ), 0 );
		return $item_values;
	} // mla_gallery_item_values
} // MLAMultisiteExtensions

// Install the shortcode at an early opportunity
add_action('init', 'MLAMultisiteExtensions::initialize');
?>
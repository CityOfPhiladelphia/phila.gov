<?php
/**
 * Implements a solution for the alphabetical division and expandable gallery display application features.
 *
 * The example plugin contains two custom shortcodes:
 *
 * 1.[az-cloud] – provides an "A-Z cloud" that tabulates the items starting with each letter of the
 * alphabet and generates links that include a query argument with the selected letter.
 * 2.[az_gallery] – formats a "gallery" list of the items starting with the selected letter. It uses a
 * file naming convention to collect all the "parts" of a particular "item set" and display them as
 * a second-level list. Uses Collapse-o-matic (optionally) to control the display of items.
 *
 * Both shortcodes have parameters to filter by MIME type and/or taxonomy term and to change the
 * HTML tags surrounding the items.
 *
 * This example plugin uses three of the many filters available in the [mla_gallery] shortcode
 * and illustrates some of the techniques you can use to customize the gallery display.
 *
 * Created for support topic "Alphabetical pagination"
 * opened on 5/16/2015 by "kevincowart111".
 * https://wordpress.org/support/topic/alphabetical-pagination-2
 *
 * @package MLA Child Term Hooks Example
 * @version 1.01
 */

/*
Plugin Name: MLA A-Z Cloud and Collapse-o-Matic Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Provides an "A-Z cloud/pagination" and expandable gallery example
Author: David Lingren
Version: 1.01
Author URI: http://fairtradejudaica.org/our-story/staff/

Copyright 2015 David Lingren

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
 * Class MLA A-Z Cloud and Collapse-o-Matic Example hooks filters provided by the [mla_gallery] shortcode
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding everything
 * else inside a class means this is the only name you have to worry about.
 */
class MLAAtoZCloudExample {
    /**
     * Initialization function, similar to __construct()
     */
    public static function initialize() {
        // The filters are only useful for front-end posts/pages; exit if in the admin section
        if ( is_admin() )
            return;

		// Custom A-Z cloud shortcode
		add_shortcode( 'az_cloud', 'MLAAtoZCloudExample::a_to_z_cloud_shortcode' );
		add_shortcode( 'az_gallery', 'MLAAtoZCloudExample::a_to_z_gallery_shortcode' );

        // [mla_gallery] hooks
        add_filter( 'mla_gallery_attributes', 'MLAAtoZCloudExample::mla_gallery_attributes_filter', 10, 1 );
        add_filter( 'mla_gallery_item_values', 'MLAAtoZCloudExample::mla_gallery_item_values_filter', 10, 1 );
		add_filter( 'mla_gallery_close_values', 'MLAAtoZCloudExample::mla_gallery_close_values_filter', 10, 1 );
    }

	/**
	 * WordPress Shortcode; returns A-Z cloud
	 *
	 * @since 1.00
	 *
	 * @param	array	shortcode parameters
	 *
	 * @return	string	shortcode results
	 */
	public static function a_to_z_cloud_shortcode( $attr ) {
		global $wpdb;

		$default_arguments = array(
			'post_mime_type' => 'application/pdf',
			'link_href' => '.',
			'selected_attribute' => 'first_letter',
			'selected_class' => 'az-current-item',
			'css_styles' => 'inline',
			'output' => 'flat', // or 'list'
			'listtag' => 'ul',
			'itemtag' => 'li',
			'separator' => "\n",
			'single_text' => '%d item',
			'multiple_text' => '%d items',
		);

		// Make sure $attr is an array, even if it's empty
		if ( empty( $attr ) ) {
			$attr = array();
		} elseif ( is_string( $attr ) ) {
			$attr = shortcode_parse_atts( $attr );
		}

		// Accept only the attributes we need and supply defaults
		$arguments = shortcode_atts( $default_arguments, $attr );

		// Find IDs assigned to taxonomy term(s), if specified
		$ids = self::find_term_relationships( $attr );

		// Build an array of SQL clauses for the posts query
		$query = array();
		$query_parameters = array();

		$query[] = "SELECT UCASE( MID( post_title, 1, 1 ) ) AS first_letter, COUNT( ID ) AS count FROM {$wpdb->posts}";

		if ( empty( $ids ) ) {
			$query[] = "WHERE ( 1=1";
		} else {
			$ids = implode( ',', $ids );
			$query[] = "WHERE ( ID IN ({$ids})";
		}

		$query[] = "AND post_type = 'attachment'";

		if ( ! empty( $arguments['post_mime_type'] ) ) {
			if ( 'all' != strtolower( $arguments['post_mime_type'] ) ) {
				$query[] = str_replace( '%', '%%', wp_post_mime_type_where( $arguments['post_mime_type'] ) ) . ')';
			}
		} else {
			$query[] = ')';
		}

		$query[] = "GROUP BY first_letter";

		$query =  join(' ', $query);
		$results = $wpdb->get_results( $query );

		/*
		 * Make the current item distinctive
		 */
		if ( 'inline' == $arguments['css_styles'] ) {
			$output  = "<style type='text/css'>\n";
			$output .= "    ." . $arguments['selected_class'] . " {\n";
			$output .= "        font-weight:bold;\n";
			$output .= "        font-size:larger;\n";
			$output .= "    }\n";
			$output .= "</style>\n";
		} else {
			$output = '';
		}

		$is_list = 'list' == $arguments['output'];

		if ( $is_list ) {
			$output .= '<' . $arguments['listtag'] . '>' . $arguments['separator'];
		}

		if ( isset( $_REQUEST[ $arguments['selected_attribute'] ] ) ) {
			$current_item = $_REQUEST[ $arguments['selected_attribute'] ];
		} else {
			$current_item = '';
		}

		foreach( $results as $result ) {
			if ( $current_item == $result->first_letter ) {
				$class = $arguments['selected_class'];
			} else {
				$class = '';
			}

			$href = sprintf( '%1$s?%2$s=%3$s',
				$arguments['link_href'], $arguments['selected_attribute'], $result->first_letter );

			if ( 1 == $result->count ) {
				$title = sprintf( $arguments['single_text'], $result->count );
			} else {
				$title = sprintf( $arguments['multiple_text'], $result->count );
			}

			$link = sprintf( '<a class="%1$s" href="%2$s" title="%3$s">%4$s</a>%5$s',
				$class, $href, $title, $result->first_letter, $arguments['separator'] );

			if ( $is_list ) {
				$output .= '<' . $arguments['itemtag'] . '>' . $link . '</' . $arguments['itemtag'] . '>' . $arguments['separator'];
			} else {
				$output .= $link . $arguments['separator'];
			}
		}

		if ( $is_list ) {
			$output .= '</' . $arguments['listtag'] . '>' . $arguments['separator'];
		}

		return $output;
	} //a_to_z_cloud_shortcode

	/**
	 * WordPress Shortcode; returns items beginning with a particular letter
	 *
	 * @since 1.00
	 *
	 * @param	array	shortcode parameters
	 *
	 * @return	string	shortcode results
	 */
	public static function a_to_z_gallery_shortcode( $attr ) {
		global $wpdb;

		$a_to_z_arguments = array(
			'selected_attribute' => 'first_letter',
			'collapse_o_matic' => 'true',
			'az_output' => 'list', // or 'flat'
			'az_listtag' => 'ul',
			'az_itemtag' => 'li',
			'az_separator' => "\n",
		);

		$default_arguments = array_merge( array(
			'post_mime_type' => 'application/pdf',
			'mla_fixed_part' => 'array()',
			'mla_caption' => '{+mla_fixed_part+}',
			'size' => 'medium',
			'columns' => '6',
			'link' => 'file', ),
			$a_to_z_arguments
		);

		// Make sure we have an array of shortcode parameters
		if ( empty( $attr ) ) {
			$attr = array();
		} elseif ( is_string( $attr ) ) {
			$attr = shortcode_parse_atts( $attr );
		}

		// Filter the attributes we need and supply defaults
		$arguments = shortcode_atts( $default_arguments, $attr );

		/*
		 * Compose the attributes we will pass to [mla_gallery],
		 * handling embedded quotes
		 */
		$mla_arguments = '';
		foreach ( array_merge( $attr, $arguments ) as $key => $value ) {
			if ( array_key_exists( $key, $a_to_z_arguments ) ) {
				continue;
			}

			$slashed = addcslashes( $value, chr(0).chr(7).chr(8)."\f\n\r\t\v\"\\\$" );
			if ( ( false !== strpos( $value, ' ' ) ) || ( false !== strpos( $value, '\'' ) ) || ( $slashed != $value ) ) {
				$value = '"' . $slashed . '"';
			}

			$mla_arguments .= empty( $mla_arguments ) ? $key . '=' . $value : ' ' . $key . '=' . $value;
		} // foreach $attr

		if ( isset( $_REQUEST[ $arguments['selected_attribute'] ] ) ) {
			$current_item = $_REQUEST[ $arguments['selected_attribute'] ];

			// Find IDs assigned to taxonomy term(s), if specified
			$ids = self::find_term_relationships( $attr );

			// Build an array of SQL clauses, then run the query
			$query = array();
			$query_parameters = array();

			$query[] = "SELECT post_title, ID FROM {$wpdb->posts}";

			if ( empty( $ids ) ) {
				$query[] = "WHERE ( 1=1";
			} else {
				$ids = implode( ',', $ids );
				$query[] = "WHERE ( ID IN ({$ids})";
			}

			$query[] = "AND UCASE( MID( post_title, 1, 1 ) ) = '" . $current_item . "'";
			$query[] = "AND post_type = 'attachment'";

			if ( ! empty( $arguments['post_mime_type'] ) ) {
				if ( 'all' != strtolower( $arguments['post_mime_type'] ) ) {
					$query[] = str_replace( '%', '%%', wp_post_mime_type_where( $arguments['post_mime_type'] ) ) . ')';
				}
			} else {
				$query[] = ')';
			}

			$query[] = "ORDER BY post_title";

			$query =  join(' ', $query);
			$results = $wpdb->get_results( $query );
		} else {
			return "Click on a letter to see the song list.\n";
		}

		$is_list = 'list' == strtolower( trim( $arguments['az_output'] ) );
		$is_collapse = 'true' == strtolower( trim( $arguments['collapse_o_matic'] ) );

		$output = '';
		$old_song = NULL;
		$old_ids = array();
		// Accumulate this directly to allow quote and double-quote characters in content
		self::$mla_fixed_values = array();

		if ( $is_list && ! empty( $arguments['az_listtag'] ) ) {
			$output .= "<{$arguments['az_listtag']}>{$arguments['az_separator']}";
		}

		foreach ( $results as $result ) {
			// Find the song title and optional part
			$title = $result->post_title;
			$divide = strpos( $title, ' - ' );
			if ( false === $divide ) {
				$song = $title;
				$part = 'All Parts';
			} else {
				$song = substr( $title, 0, $divide );
				$part = substr( $title, $divide + 3 );
			}

			// If the song has changed, output the previous entry
			if ( $old_song && $old_song != $song ) {
				$output .= self::format_title_and_gallery( $old_song, $old_ids, $mla_arguments, $arguments );

				$old_song = $song;
				$old_ids = array();
				self::$mla_fixed_values = array();
			}

			// Accumulate the parts
			$old_song = $song;
			$old_ids[] = $result->ID;
			self::$mla_fixed_values['mla_fixed_part'][] = $part;
		}

		// Flush the last entry
		if ( $old_song ) {
			$output .= self::format_title_and_gallery( $old_song, $old_ids, $mla_arguments, $arguments );
		}

		if ( $is_list && ! empty( $arguments['az_listtag'] ) ) {
			$output .= "</{$arguments['az_listtag']}>{$arguments['az_separator']}";
		}

		return $output;
	} //a_to_z_gallery_shortcode

	/**
	 * Compose a single item title and parts gallery
	 *
	 * @since 1.00
	 *
	 * @param	string	item title
	 * @param	array	part ID values
	 * @param	array	[mla_gallery] parameters
	 * @param	array	formating parameters
	 *
	 * @return	string	markup for title and gallery
	 */
	private static function format_title_and_gallery( $title, $ids, $mla_arguments, $arguments ) {
		$is_list = 'list' == strtolower( trim( $arguments['az_output'] ) );
		$is_collapse = 'true' == strtolower( trim( $arguments['collapse_o_matic'] ) );
		$output = '';

		if ( $is_list ) {
			$output .= "<{$arguments['az_itemtag']}>{$arguments['az_separator']}";
		}

		if ( $is_collapse ) {
			$output .= "[expand title='" . esc_attr( $title ) . "']{$arguments['az_separator']}";
		} else {
			$output .= "{$title}<br>{$arguments['az_separator']}";
		}

		$output .= "[mla_gallery ids='" . implode( ',', $ids ) . "' ";
		$output .= $mla_arguments . "]{$arguments['az_separator']}";

		if ( $is_collapse ) {
			$output .= "[/expand]{$arguments['az_separator']}";
		}

		if ( $is_list ) {
			$output .= "</{$arguments['az_itemtag']}>{$arguments['az_separator']}";
		}

		return do_shortcode( $output );
	} //format_title_and_gallery

	/**
	 * Find items assigned to taxonomy term(s)
	 *
	 * @since 1.00
	 *
	 * @param	array	shortcode parameters
	 *
	 * @return	array	ID values of objects assigned to the term(s), if any
	 */
	private static function find_term_relationships( $attr ) {
		global $wpdb;

		// Start with empty "taxonomy" parameter values
		$ttids = array();

		// Find taxonomy argument, if present, and collect terms
		$taxonomies = get_object_taxonomies( 'attachment', 'names' );
		foreach( $taxonomies as $taxonomy ) {
			if ( empty( $attr[ $taxonomy ] ) ) {
				continue;
			}

			// Found the taxonomy; collect the terms
			$include_children =  isset( $attr['include_children'] ) && 'true' == strtolower( trim( $my_query_vars['include_children'] ) );

			// Allow for multiple term slug values
			$terms = array();
			$slugs = explode( ',', $attr[ $taxonomy ] );
			foreach ( $slugs as $slug ) {
				$args = array( 'slug' => $slug, 'hide_empty' => false );
				$terms = array_merge( $terms, get_terms( $taxonomy, $args ) );
			}

			foreach( $terms as $term ) {
				// Index by ttid to remove duplicates
				$ttids[ $term->term_taxonomy_id ] = $term->term_taxonomy_id;

				if ( $include_children ) {
					$args = array( 'child_of' => $term->term_id, 'hide_empty' => false );
					$children = get_terms( $taxonomy, $args );
					foreach( $children as $child ) {
						$ttids[] = $child->term_taxonomy_id;
					}
				} // include_children
			} // $term

			break;
		}

		// Pre-select the items assigned to the taxonomy terms
		$ids = array();
		if ( ! empty( $ttids ) ) {
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
			$results = $wpdb->get_results( $wpdb->prepare( $query, $query_parameters ) );
			if ( is_array( $results ) ) {
				foreach ( $results as $id ) {
					$ids[] = $id->object_id;
				}
			}
		}

		return $ids;
	} //find_term_relationships

    /**
     * Save the shortcode attributes
     */
    private static $shortcode_attributes = array();
    
	/*
	 * $mla_fixed_values stores the parameter(s) and values. If none are found, the
	 * initialization code sets it to false so the logic is quickly bypassed.
	 */
	private static $mla_fixed_values = NULL;

    /**
     * MLA Gallery (Display) Attributes
     *
     * This filter lets you record or modify the arguments passed in to the shortcode
     * before they are merged with the default arguments used for the gallery display.
     *
     * The $shortcode_attributes array is where you will find your own parameters that
	 * are coded in the shortcode, e.g.:
	 * [mla_gallery mla_fixed_caption="array('test1','test2')" mla_caption="{+mla_fixed_caption+}"].
     */
    public static function mla_gallery_attributes_filter( $shortcode_attributes ) {
        /*
         * Save the attributes for use in the later filter
         */
        self::$shortcode_attributes = $shortcode_attributes;

        return $shortcode_attributes;
    } // mla_gallery_attributes_filter

    /**
     * MLA Gallery Item Values
     *
     * @since 1.00
     *
     * @param    array    parameter_name => parameter_value pairs
     *
     * @return    array    updated substitution parameter name => value pairs
     */
    public static function mla_gallery_item_values_filter( $item_values ) {
        /*
         * We use shortcode parameters of our own to apply our filters on a
		 * gallery-by-gallery basis, leaving other [mla_gallery] instances untouched.
		 * If no "mla_fixed_" parameters are present, we have nothing to do. Here is
		 * an example of how the custom parameter can be used:
         *
         * [mla_gallery ids="2621,2622" mla_fixed_title="array('my title','my other title')" mla_image_attributes="title='{+mla_fixed_title+}'"]
		 *
		 * You can have as many "mla_fixed_" parameters as you need for different values.
         */

        if ( false === self::$mla_fixed_values ) {
            return $item_values; // leave them unchanged
        }

        /*
         * Evaluate the parameter value(s) once per page load.
         */
        if ( NULL === self::$mla_fixed_values ) {
			self::$mla_fixed_values = array();
			foreach ( self::$shortcode_attributes as $parmkey => $parmvalue ) {
                if ( 'mla_fixed_' == substr( $parmkey, 0, 10 ) ) {
					if ( 'array(' == substr( $parmvalue, 0, 6 ) ) {
	                    $function = @create_function( '', 'return ' . self::$shortcode_attributes[ $parmkey ] . ';' );
    	                if ( is_callable( $function ) ) {
        	                self::$mla_fixed_values[ $parmkey ] = $function();

		                    if ( ! is_array( self::$mla_fixed_values[ $parmkey ] ) ) {
    	                        self::$mla_fixed_values[ $parmkey ] = array();
        	                }
						} else {
                            self::$mla_fixed_values[ $parmkey ] = array();
						}
					} else {
                        self::$mla_fixed_values[ $parmkey ] = explode( ",", $parmvalue );
                        if ( false === self::$mla_fixed_values[ $parmkey ] ) {
                            self::$mla_fixed_values[ $parmkey ] = array();
                        }
                    }
                } // found mla_fixed_
			} // foreach parameter

			if ( empty( self::$mla_fixed_values ) ) {
				self::$mla_fixed_values = false;			
	            return $item_values;
			}
        } // initialization code

        /*
         * Apply the appropriate value to the current item.
         */
        foreach ( self::$mla_fixed_values as $mla_fixed_key => $mla_fixed_value ) {
           /*
            * Apply the appropriate value to the current item.
            */
            if ( isset( $mla_fixed_value[ $item_values['index'] - 1 ] ) ) {
                $item_values[ $mla_fixed_key ] = $mla_fixed_value[ $item_values['index'] - 1 ];
            }
        }

        return $item_values;
    } // mla_gallery_item_values_filter

	/**
	 * MLA Gallery Close Values
	 *
	 * @since 1.02
	 *
	 * @param	array	parameter_name => parameter_value pairs
	 *
	 * @return	array	updated substitution parameter name => value pairs
	 */
	public static function mla_gallery_close_values_filter( $markup_values ) {
		/*
		 * Reset $mla_fixed_values for multiple shortcodes on the same post/page
		 */
		self::$mla_fixed_values = NULL;

		return $markup_values;
	} // mla_gallery_close_values_filter
} // Class MLAAtoZCloudExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLAAtoZCloudExample::initialize');
?>
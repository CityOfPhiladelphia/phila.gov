<?php
/**
 * Provides examples of the filters provided for Field-level Substitution Parameters
 *
 * In this example:
 *     - a "parent_terms:" prefix accesses taxonomy terms assigned to an item's parent post/page
 *     - a "page_terms:" prefix accesses taxonomy terms assigned to the current post/page
 *     - a "parent:" prefix accesses all of the WP_Post properties, custom fields and the permalink for an item's parent
 *     - an "author:" prefix accesses all of the WP_User properties for an item's author
 *     - an "conditional:" prefix returns a value when a condition is true, e.g., during the upload process
 *     - a "wp_query_vars:" prefix accesses all of the "global $wp_query->query_vars" properties
 *
 * Created for support topic "Parent category tag"
 * opened on 5/20/2016 by "Levy":
 * https://wordpress.org/support/topic/parent-category-tag
 *
 * Enhanced for support topic "Automatically adding the author as a category"
 * opened on 6/27/2016 by "badger41":
 * https://wordpress.org/support/topic/automatically-adding-the-author-as-a-category
 *
 * Enhanced for support topic "Apply Category to JPG images only on Upload"
 * opened on 7/11/2016 by "dg_Amanda":
 * https://wordpress.org/support/topic/apply-category-to-jpg-images-only-on-upload
 *
 * Enhanced for support topic "How to add a number to the title of images inserted in same post?"
 * opened on 7/19/2016 by "Levy":
 * https://wordpress.org/support/topic/how-to-add-a-number-to-the-title-of-images-inserted-in-same-post
 *
 * Enhanced for support topic "What are the default values for the markup template?"
 * opened on 9/21/2016 by "cconstantine":
 * https://wordpress.org/support/topic/what-are-the-default-values-for-the-markup-template/
 *
 * Enhanced for support topic "Maping Image ALT Tags to Product Meta Title"
 * opened on 12/6/2016 by "webpresencech":
 * https://wordpress.org/support/topic/maping-image-alt-tags-to-product-meta-title/
 *
 * Enhanced for support topic "$wp_query->query_vars in query"
 * opened on 3/1/2017 by "mbruxelle":
 * https://wordpress.org/support/topic/wp_query-query_vars-in-query/
 *
 * @package MLA Substitution Parameter Hooks Example
 * @version 1.08
 */

/*
Plugin Name: MLA Substitution Parameter Hooks Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Adds "parent_terms:", "page_terms:", "parent:", "author:" and "conditional:" Field-level Substitution Parameters
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
 * Class MLA Substitution Parameter Hooks Example hooks four of the filters provided
 * by the "Field-level substitution parameter filters (Hooks)"
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding
 * everything else inside a class means this is the only name you have to worry about.
 *
 * @package MLA Substitution Parameter Hooks Example
 * @since 1.00
 */
class MLASubstitutionParameterExample {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * Installs filters and actions that handle the MLA hooks for the
	 * "Field-level substitution parameters"
	 *
	 * @since 1.00
	 *
	 * @return	void
	 */
	public static function initialize() {
		/*
		 * Defined in /media-library-assistant/includes/class-mla-data.php
		 */
		//add_filter( 'mla_expand_custom_data_source', 'MLASubstitutionParameterExample::mla_expand_custom_data_source', 10, 9 );
		add_filter( 'mla_expand_custom_prefix', 'MLASubstitutionParameterExample::mla_expand_custom_prefix', 10, 8 );
		//add_filter( 'mla_apply_custom_format', 'MLASubstitutionParameterExample::mla_apply_custom_format', 10, 2 );

		/*
		 * Defined in /media-library-assistant/includes/class-mla-data-source.php
		 */
		//add_filter( 'mla_evaluate_custom_data_source', 'MLASubstitutionParameterExample::mla_evaluate_custom_data_source', 10, 5 );
		
		/*
		 * Additional hooks defined in "MLA Custom Field and IPTC/EXIF Mapping Actions and Filters (Hooks)".
		 * These are only required for the "conditional:is_upload" prefix processing.
		 */
		add_filter( 'mla_update_attachment_metadata_prefilter', 'MLASubstitutionParameterExample::mla_update_attachment_metadata_prefilter', 10, 3 );
		add_filter( 'mla_update_attachment_metadata_postfilter', 'MLASubstitutionParameterExample::mla_update_attachment_metadata_postfilter', 10, 3 );
	} // initialize

	/**
	 * MLA Update Attachment Metadata Prefilter
	 *
	 * Used in this example to set the "is_upload" status before mapping rules are run.
	 *
	 * @since 1.01
	 *
	 * @param	array	attachment metadata
	 * @param	integer	The Post ID of the new/updated attachment
	 * @param	array	Processing options, e.g., 'is_upload'
	 */
	public static function mla_update_attachment_metadata_prefilter( $data, $post_id, $options ) {
		self::$is_upload = $options['is_upload'];

		return $data;
	} // mla_update_attachment_metadata_prefilter

	/**
	 * Share the upload status among mla_update_attachment_metadata_prefilter, mla_expand_custom_prefix
	 * and mla_update_attachment_metadata_postfilter
	 *
	 * @since 1.01
	 *
	 * @var	boolean	Upload status
	 */
	private static $is_upload = false;

	/**
	 * MLA Update Attachment Metadata Postfilter
	 *
	 * Used in this example to clear the "is_upload" status after mapping rules are run.
	 *
	 * @since 1.01
	 *
	 * @param	array	attachment metadata
	 * @param	integer	The Post ID of the new/updated attachment
	 * @param	array	Processing options, e.g., 'is_upload'
	 */
	public static function mla_update_attachment_metadata_postfilter( $data, $post_id, $options ) {
		self::$is_upload = false;
		
		return $data;
	} // mla_update_attachment_metadata_postfilter

	/**
	 * MLA Expand Custom Data Source Filter
	 *
	 * For shortcode and Content Template processing, gives you an opportunity to generate a custom data value.
	 *
	 * @since 1.00
	 *
	 * @param	string	NULL, indicating that by default, no custom value is available
	 * @param	string	the entire data-source text including option/format and any arguments 
	 * @param	string	the data-source name 
	 * @param	array	data-source components; prefix (empty), value, option, format and args (if present)
	 * @param	array	values from the query, if any, e.g. shortcode parameters
	 * @param	array	item-level markup template values, if any
	 * @param	integer	attachment ID for attachment-specific values
	 * @param	boolean	for option 'multi', retain existing values
	 * @param	string	default option value
	 */
	public static function mla_expand_custom_data_source( $custom_value, $key, $candidate, $value, $query, $markup_values, $post_id, $keep_existing, $default_option ) {
		/*
		 * Uncomment the error_log statements in any of the filters to see what's passed in
		 */
		//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_data_source( {$key}, {$candidate}, {$post_id}, {$keep_existing}, {$default_option} ) value = " . var_export( $value, true ), 0 );
		//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_data_source( {$candidate}, {$post_id} ) query = " . var_export( $query, true ), 0 );
		//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_data_source( {$candidate}, {$post_id} ) markup_values = " . var_export( $markup_values, true ), 0 );

		return $custom_value;
	} // mla_expand_custom_data_source

	/**
	 * Evaluate parent_terms: or page_terms: values
	 *
	 * @since 1.03
	 *
	 * @param	mixed	String or array - initial value
	 * @param	integer	The Post ID of the new/updated attachment
	 * @param	string	Taxonomy slug
	 * @param	string	Field name in term object
	 * @param	string	Format/option; text,single,export,unpack,array
	 *
	 * @return	mixed	String or array 
	 */
	private static function _evaluate_terms( $custom_value, $post_id, $taxonomy, $qualifier, $option ) {
		if ( 0 == $post_id ) {
			return $custom_value;
		}

		if ( empty( $qualifier ) ) {
			$qualifier = 'name';
		}

		$terms = get_object_term_cache( $post_id, $taxonomy );
		if ( false === $terms ) {
			$terms = wp_get_object_terms( $post_id, $taxonomy );
			wp_cache_add( $post_id, $terms, $taxonomy . '_relationships' );
		}

		if ( 'array' == $option ) {
			$custom_value = array();
		} else {
			$custom_value = '';
		}

		if ( is_wp_error( $terms ) ) {
			$custom_value = implode( ',', $terms->get_error_messages() );
		} elseif ( ! empty( $terms ) ) {
			if ( 'single' == $option || 1 == count( $terms ) ) {
				reset( $terms );
				$term = current( $terms );
				$fields = get_object_vars( $term );
				$custom_value = isset( $fields[ $qualifier ] ) ? $fields[ $qualifier ] : $fields['name'];
				$custom_value = sanitize_term_field( $qualifier, $custom_value, $term->term_id, $taxonomy, 'display' );
			} elseif ( ( 'export' == $option ) || ( 'unpack' == $option ) ) {
				$custom_value = sanitize_text_field( var_export( $terms, true ) );
			} else {
				foreach ( $terms as $term ) {
					$fields = get_object_vars( $term );
					$field_value = isset( $fields[ $qualifier ] ) ? $fields[ $qualifier ] : $fields['name'];
					$field_value = sanitize_term_field( $qualifier, $field_value, $term->term_id, $taxonomy, 'display' );

					if ( 'array' == $option ) {
						$custom_value[] = $field_value;
					} else {
						$custom_value .= strlen( $custom_value ) ? ', ' . $field_value : $field_value;
					}
				}
			}
		}
		
		return $custom_value;
	} // _evaluate_terms

	/**
	 * MLA Expand Custom Prefix Filter
	 *
	 * Gives you an opportunity to generate your custom data value when a parameter's prefix value is not recognized.
	 *
	 * @since 1.00
	 *
	 * @param	string	NULL, indicating that by default, no custom value is available
	 * @param	string	the data-source name 
	 * @param	array	data-source components; prefix (empty), value, option, format and args (if present)
	 * @param	array	values from the query, if any, e.g. shortcode parameters
	 * @param	array	item-level markup template values, if any
	 * @param	integer	attachment ID for attachment-specific values
	 * @param	boolean	for option 'multi', retain existing values
	 * @param	string	default option value
	 */
	public static function mla_expand_custom_prefix( $custom_value, $key, $value, $query, $markup_values, $post_id, $keep_existing, $default_option ) {
		static $parent_cache = array(), $author_cache = array();
		
		//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_prefix( {$key}, {$post_id}, {$keep_existing}, {$default_option} ) value = " . var_export( $value, true ), 0 );
		//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_prefix( {$key}, {$post_id} ) query = " . var_export( $query, true ), 0 );
		//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_prefix( {$key}, {$post_id} ) markup_values = " . var_export( $markup_values, true ), 0 );

		// Look for field/value qualifier
		$match_count = preg_match( '/^(.+)\((.+)\)/', $value['value'], $matches );
		if ( $match_count ) {
			$field = $matches[1];
			$qualifier = $matches[2];
		} else {
			$field = $value['value'];
			$qualifier = '';
		}

		if ( 'page_terms' == $value['prefix'] ) {
			if ( isset( $markup_values['page_ID'] ) ) {
				$post_id = absint( $markup_values['page_ID'] );
			} else {
				global $post;

				if ( isset( $post ) && !empty( $post->ID ) ) {
					$post_id = absint( $post->ID );
				} else {
					$post_id = 0;
				}
			}
			
			$custom_value = self::_evaluate_terms( $custom_value, $post_id, $field, $qualifier, $value['option'] );
		} elseif ( 'page' == $value['prefix'] ) {
			if ( 'featured' == $value['value'] ) {
				$featured = absint( get_post_thumbnail_id( absint( $markup_values['page_ID'] ) ) ); 
				if ( 0 < $featured ) {
					$custom_value = (string) $featured;
				}
			}
		}
		
		if ( 0 == absint( $post_id ) ) {
			return $custom_value;
		}

		if ( 'parent_terms' == $value['prefix'] ) {
			if ( isset( $markup_values['parent'] ) ) {
				$post_parent = absint( $markup_values['parent'] );
			} else {
				$item = get_post( $post_id );
				$post_parent = absint( $item->post_parent );
			}
			
			$custom_value = self::_evaluate_terms( $custom_value, $post_parent, $field, $qualifier, $value['option'] );
		} elseif ( 'parent' == $value['prefix'] ) {
			if ( isset( $markup_values['parent'] ) ) {
				$parent_id = absint( $markup_values['parent'] );
			} else {
				$item = get_post( $post_id );
				$parent_id = absint( $item->post_parent );
			}
			
			if ( 0 == $parent_id ) {
				return $custom_value;
			}

			if ( isset( $parent_cache[ $parent_id ] ) ) {
				$parent = $parent_cache[ $parent_id ];
			} else {
				$parent = get_post( $parent_id );

				if ( $parent instanceof WP_Post && $parent->ID == $parent_id ) {
					$parent_cache[ $parent_id ] = $parent;
				} else {
					return $custom_value;
				}
			}
			
			if ( property_exists( $parent, $value['value'] ) ) {
				$custom_value = $parent->{$value['value']};
			} elseif ( 'permalink' == $value['value'] ) {
				$custom_value = get_permalink( $parent );
			} else {
				// Look for a custom field match
				$meta_value = get_metadata( 'post', $parent_id, $value['value'], false );
//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_prefix( {$key}, {$post_id}, {$parent_id} ) meta_value = " . var_export( $meta_value, true ), 0 );
				if ( !empty( $meta_value ) ) {
					$custom_value = $meta_value;
				}
			}
			
//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_prefix( {$key}, {$post_id}, {$parent_id} ) custom_value = " . var_export( $custom_value, true ), 0 );

			if ( is_array( $custom_value ) ) {
				if ( 'single' == $value['option'] || 1 == count( $custom_value ) ) {
					$custom_value = sanitize_text_field( reset( $custom_value ) );
				} elseif ( ( 'export' == $value['option'] ) || ( 'unpack' == $value['option'] ) ) {
					$custom_value = sanitize_text_field( var_export( $custom_value, true ) );
				} else {
					if ( 'array' == $value['option'] ) {
						$new_value = array();
					} else {
						$new_value = '';
					}

					foreach ( $custom_value as $element ) {
						$field_value = sanitize_text_field( $element );
	
						if ( 'array' == $value['option'] ) {
							$new_value[] = $field_value;
						} else {
							$new_value .= strlen( $custom_value ) ? ', ' . $field_value : $field_value;
						}
					}
					
					$custom_value = $new_value;
				}
			}
		} elseif ( 'author' == $value['prefix'] ) {
			if ( isset( $markup_values['author_id'] ) ) {
				$item_author = absint( $markup_values['author_id'] );
			} else {
				$item = get_post( $post_id );
				$item_author = absint( $item->post_author );
			}
			
			if ( isset( $author_cache[ $item_author ] ) ) {
				$author = $author_cache[ $item_author ];
			} else {
				$author = new WP_User( $item_author );
//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_prefix( {$key}, {$post_id} ) author = " . var_export( $author, true ), 0 );
				if ( $author instanceof WP_User && $author->ID == $item_author ) {
					$author_cache[ $item_author ] = $author;
				} else {
					return $custom_value;
				}
			}
			
			if ( property_exists( $author, $value['value'] ) ) {
				$custom_value = $author->{$value['value']};
			} else {
				$custom_value = $author->get( $value['value'] );
			}
			
//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_prefix( {$key}, {$post_id} ) custom_value = " . var_export( $custom_value, true ), 0 );

			if ( is_array( $custom_value ) ) {
				if ( 'single' == $value['option'] || 1 == count( $custom_value ) ) {
					$custom_value = sanitize_text_field( reset( $custom_value ) );
				} elseif ( ( 'export' == $value['option'] ) || ( 'unpack' == $value['option'] ) ) {
					$custom_value = sanitize_text_field( var_export( $custom_value, true ) );
				} else {
					if ( 'array' == $value['option'] ) {
						$new_value = array();
					} else {
						$new_value = '';
					}

					foreach ( $custom_value as $element ) {
						$field_value = sanitize_text_field( $element );
	
						if ( 'array' == $value['option'] ) {
							$new_value[] = $field_value;
						} else {
							$new_value .= strlen( $custom_value ) ? ', ' . $field_value : $field_value;
						}
					}
					
					$custom_value = $new_value;
				}
			}
		} elseif ( 'conditional' == $value['prefix'] ) {
			if ( empty( $value['args'] ) ) {
				return $custom_value;
			}
			
			$true_value = ( isset( $value['args'][0] ) && !empty( $value['args'][0] ) ) ? $value['args'][0] : '';
			$false_value = ( isset( $value['args'][1] ) && !empty( $value['args'][1] ) ) ? $value['args'][1] : '';
			$qualifier = ( isset( $value['args'][2] ) && !empty( $value['args'][2] ) ) ? $value['args'][2] : '';
			
			switch ( $value['value'] ) {
				case 'is_upload':
					if ( self::$is_upload ) {
						// Optional MIME type qualifier
						if ( !empty( $qualifier ) ) {
							$item = get_post( $post_id );
							$post_mime_type = explode( '/', sanitize_mime_type( $item->post_mime_type ) );
//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_prefix( {$key}, {$post_id} ) post_mime_type = " . var_export( $post_mime_type, true ), 0 );
							$qualifier = explode( '/', sanitize_mime_type( $qualifier ) );
//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_prefix( {$key}, {$post_id} ) qualifier = " . var_export( $qualifier, true ), 0 );

							if ( isset( $qualifier[1] ) && isset( $post_mime_type[1] ) && ( $qualifier[1]!== $post_mime_type[1] ) ) {
								$custom_value = $false_value;
								break;
							}

							if ( isset( $qualifier[0] ) && isset( $post_mime_type[0] ) && ( $qualifier[0]!== $post_mime_type[0] ) ) {
								$custom_value = $false_value;
								break;
							}
						}
						
						$custom_value = $true_value;
					} else {
						$custom_value = $false_value;
					}
//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_prefix( {$key}, {$post_id} ) value = '{$custom_value}', is_upload = " . var_export( self::$is_upload, true ), 0 );
					break;
				default:
					// ignore anything else
			}
		} elseif ( 'wp_query_vars' == $value['prefix'] ) {
			global $wp_query;
			//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_prefix( {$key}, {$post_id} ) wp_query->query_vars = " . var_export( $wp_query->query_vars , true ), 0 );

			if ( !empty( $wp_query->query_vars ) ) {
				$custom_value = MLAData::mla_find_array_element( $value['value'], $wp_query->query_vars, $value['option'], $keep_existing );
			}
		}

		return $custom_value;
	} // mla_expand_custom_prefix

	/**
	 * MLA Apply Custom Format Filter
	 *
	 * Gives you an opportunity to apply your custom option/format to the data value.
	 *
	 * @since 1.00
	 *
	 * @param	string	the data-source value
	 * @param	array	data-source components; prefix (empty), value, option, format and args (if present)
	 */
	public static function mla_apply_custom_format( $value, $args ) {
		//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_apply_custom_format( {$value} ) args = " . var_export( $args, true ), 0 );

		return $value;
	} // mla_apply_custom_format

	/**
	 * MLA Evaluate Custom Data Source Filter
	 *
	 * For metadata mapping rules, gives you an opportunity to generate a custom data value.
	 *
	 * @since 1.00
	 *
	 * @param	string	NULL, indicating that by default, no custom value is available
	 * @param	integer	attachment ID for attachment-specific values
	 * @param	string 	category/scope to evaluate against: custom_field_mapping or single_attachment_mapping
	 * @param	array	data source specification ( name, *data_source, *keep_existing, *format, mla_column, quick_edit, bulk_edit, *meta_name, *option, no_null )
	 * @param	array 	_wp_attachment_metadata, default NULL (use current postmeta database value)
	 */
	public static function mla_evaluate_custom_data_source( $custom_value, $post_id, $category, $data_value, $attachment_metadata ) {
		//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_data_source( {$post_id}, {$category} ) data_value = " . var_export( $data_value, true ), 0 );
		//error_log( __LINE__ . " MLASubstitutionParameterExample::mla_expand_custom_data_source( {$post_id}, {$category} ) attachment_metadata = " . var_export( $attachment_metadata, true ), 0 );

		return $custom_value;
	} // mla_evaluate_custom_data_source
} //MLASubstitutionParameterExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLASubstitutionParameterExample::initialize');
?>
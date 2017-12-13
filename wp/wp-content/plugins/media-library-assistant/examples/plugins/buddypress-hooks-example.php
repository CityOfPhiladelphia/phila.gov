<?php
/**
 * Provides [mla_gallery] parameters to filter items, generate rtMedia URLs and substitute cover art
 *
 * In this example:
 *
 * 1. The WordPress "attachment/media page" links are replaced by "BuddyPress/rtMedia page"
 *    links. For audio and video files, an option is provided to substitute the "cover_art"
 *    thumbnail image for the item Title in the thumbnail_content.
 *
 *    A custom "buddypress_urls" parameter activates the URL re-write when not empty.
 *    If the parameter value is "cover" cover art will be substituted for audio/video
 *    icons. For example:
 *        [mla_gallery post_parent=all post_mime_type=video buddypress_urls=cover]
 *    displays cover art for all Video items in the Media Library.
 *
 * 2. A custom "rtmedia=true" parameter filters the items returned by the [mla_gallery]
 *    query and removes any items that do not have an rtMedia ID.
 *
 * 3. A custom "rtmedia=gallery" parameter filters the items returned by the [mla_gallery]
 *    query and uses [rtmedia_gallery] to display the items that have an rtMedia ID.
 *
 * 4. A custom "rtmedia_ids" parameter accepts a list of rtMedia ID values and translates
 *    them to attachment IDs so [mla_gallery] can process the items.
 *
 * 5. A custom "rtmedia_source" parameter filters the items returned by the [mla_gallery] query
 *    and uses the parameter value as a content template to extract rtMedia IDs from the items.
 *    The rtMedia IDs are translated to attachment IDs so [mla_gallery] can process the items.
 *
 * This example plugin uses eight of the many filters available in the [mla_gallery] shortcode
 * and illustrates some of the techniques you can use to customize the gallery display.
 *
 * Created for support topic "Overwhelmed. Help my shortcode out? :)"
 * opened on 8/3/2016 by "tweakben".
 * https://wordpress.org/support/topic/overwhelmed-help-my-shortcode-out/
 *
 * Enhanced for support topic "Display rtmedia gallery with a query on the title or media_id"
 * opened on 3/13/2017 by "marineb30".
 * https://wordpress.org/support/topic/display-rtmedia-gallery-with-a-query-on-the-title-or-media_id-2/
 *
 * @package MLA BuddyPress & rtMedia Example
 * @version 1.08
 */

/*
Plugin Name: MLA BuddyPress & rtMedia Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Provides [mla_gallery] parameters to filter items, generate rtMedia URLs and substitute cover art
Author: David Lingren
Version: 1.08
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
 * Class MLA BuddyPress Hooks Example hooks all of the filters provided by the [mla_gallery] shortcode
 *
 * @package MLA BuddyPress & rtMedia Example
 * @since 1.00
 */
class MLABuddyPressHooksExample {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 1.00
	 */
	public static function initialize() {

	    add_filter( 'rtmedia_media_query', 'MLABuddyPressHooksExample::my_modify_media_query', 9, 3 );
		add_filter( 'rtmedia_allowed_query', 'MLABuddyPressHooksExample::my_rtmedia_allowed_attributes_parameter_in_query', 99 );
		add_action( 'rtmedia_before_media_gallery', 'MLABuddyPressHooksExample::my_remove_rtmedia_model_shortcode_query_attributes', 10, 3 );

		// The filters are only useful for front-end posts/pages; exit if in the admin section
		if ( is_admin() )
			return;

		add_filter( 'mla_gallery_raw_attributes', 'MLABuddyPressHooksExample::mla_gallery_raw_attributes', 10, 1 );
		add_filter( 'mla_gallery_attributes', 'MLABuddyPressHooksExample::mla_gallery_attributes', 10, 1 );
		add_action( 'mla_gallery_wp_query_object', 'MLABuddyPressHooksExample::mla_gallery_wp_query_object', 10, 1 );
		add_filter( 'mla_gallery_the_attachments', 'MLABuddyPressHooksExample::mla_gallery_the_attachments', 10, 2 );
		add_filter( 'mla_gallery_alt_shortcode_blacklist', 'MLABuddyPressHooksExample::mla_gallery_alt_shortcode_blacklist', 10, 1 );
		//add_filter( 'mla_gallery_alt_shortcode_attributes', 'MLABuddyPressHooksExample::mla_gallery_alt_shortcode_attributes', 10, 1 );
		add_filter( 'mla_gallery_alt_shortcode_ids', 'MLABuddyPressHooksExample::mla_gallery_alt_shortcode_ids', 10, 3 );

		add_filter( 'mla_gallery_item_values', 'MLABuddyPressHooksExample::mla_gallery_item_values', 10, 1 );
	} // initialize

	/**
	 * Save the shortcode attributes
	 *
	 * @since 1.00
	 *
	 * @var array
	 */
	private static $shortcode_attributes = array();

	/**
	 * Capture the rtmedia_source parameter before any substitution evaluation is performed
	 *
	 * @since 1.07
	 *
	 * @param array $shortcode_attributes The raw parameters passed in to the shortcode
	 */
	public static function mla_gallery_raw_attributes( $shortcode_attributes ) {
		//error_log( __LINE__ . ' MLABuddyPressHooksExample::mla_gallery_raw_attributes $shortcode_attributes = ' . var_export( $shortcode_attributes, true ), 0 );

		self::$shortcode_attributes = array();
		if ( isset( $shortcode_attributes['rtmedia_source'] ) ) {
			self::$shortcode_attributes['rtmedia_source'] = $shortcode_attributes['rtmedia_source'];
			unset( $shortcode_attributes['rtmedia_source'] );
		}

		return $shortcode_attributes;
	} // mla_gallery_raw_attributes

	/**
	 * Process the rtmedia and rtmedia_ids parameters, sanitize buddypress_urls parameter
	 *
	 * @since 1.00
	 *
	 * @param array $shortcode_attributes The shortcode parameters passed in to the shortcode
	 */
	public static function mla_gallery_attributes( $shortcode_attributes ) {
		//error_log( __LINE__ . ' MLABuddyPressHooksExample::mla_gallery_attributes $shortcode_attributes = ' . var_export( $shortcode_attributes, true ), 0 );

		// Clean up the parameters we're interested in.
		if ( isset( $shortcode_attributes['buddypress_urls'] ) ) {
			$shortcode_attributes['buddypress_urls'] = strtolower( trim( $shortcode_attributes['buddypress_urls'] ) );
		}

		if ( isset( $shortcode_attributes['rtmedia'] ) ) {
			$shortcode_attributes['rtmedia'] = strtolower( trim( $shortcode_attributes['rtmedia'] ) );

			if ( 'gallery' === $shortcode_attributes['rtmedia'] ) {
				$shortcode_attributes['mla_alt_shortcode'] = 'rtmedia_gallery';
				$shortcode_attributes['mla_alt_ids_name'] = 'media_ids';
				$shortcode_attributes['global'] = 'true';
			}
		}

		if ( isset( $shortcode_attributes['rtmedia_ids'] ) ) {
			global $wpdb;

			$ids = wp_parse_id_list( $shortcode_attributes['rtmedia_ids'] );

			// Build an array of SQL clauses, then run the query
			$query = array();
			$query_parameters = array();

			$query[] = "SELECT rtm.id, rtm.media_id FROM {$wpdb->prefix}rt_rtm_media AS rtm";
			$query[] = 'WHERE ( rtm.id IN (' . implode( ',', $ids ) . ') )';

			$query =  join(' ', $query);
			$results = $wpdb->get_results( $query );

			// Save the values, indexed by WordPress attachment ID, for use in the item filter
			$post_info = array();
			if ( is_array( $results ) ) {
				foreach ( $results as $value ) {
					$post_info[] = $value->media_id;
				}
			}

			$shortcode_attributes['ids'] = implode( ',', $post_info );
			unset( $shortcode_attributes['rtmedia_ids'] );
		} // rtmedia_ids

		// Save the attributes, including rtmedia_source, for use in the later filters
		self::$shortcode_attributes = array_merge( $shortcode_attributes, self::$shortcode_attributes );
		//error_log( __LINE__ . ' MLABuddyPressHooksExample::mla_gallery_attributes self::$shortcode_attributes = ' . var_export( self::$shortcode_attributes, true ), 0 );

		//error_log( __LINE__ . ' MLABuddyPressHooksExample::mla_gallery_attributes $shortcode_attributes = ' . var_export( $shortcode_attributes, true ), 0 );
		return $shortcode_attributes;
	} // mla_gallery_attributes

	/**
	 * Save rtMedia information for buddypress_urls
	 *
	 * @since 1.00
	 *
	 * @var array
	 */
	private static $rtmedia_post_info = array();

	/**
	 * If the 'buddypress_urls' parameter is present, generate the
	 * rtMedia information for the queried items
	 *
	 * @since 1.00
	 * @uses MLAShortcodes::$mla_gallery_wp_query_object
	 *
	 * @param array $query_arguments The arguments passed to WP_Query->query
	 */
	public static function mla_gallery_wp_query_object( $query_arguments ) {
		self::$rtmedia_post_info = array();

		if ( empty( self::$shortcode_attributes['buddypress_urls'] ) ) {
			return; // Don't need custom URLs
		}

		if ( 0 == MLAShortcodes::$mla_gallery_wp_query_object->post_count ) {
			return; // Empty gallery - nothing to do
		}

		if ( isset( self::$shortcode_attributes['rtmedia_source'] ) ) {
			return; // We are translating from rtmedia_source IDs to attachments, later
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
		self::$rtmedia_post_info = array();
		if ( is_array( $results ) ) {
			foreach ( $results as $value ) {
				self::$rtmedia_post_info[ $value->media_id ] = $value;
			}
		}

		//error_log( __LINE__ . ' MLABuddyPressHooksExample::mla_gallery_wp_query_object self::$rtmedia_post_info = ' . var_export( self::$rtmedia_post_info, true ), 0 );
	} // mla_gallery_wp_query_object

	/**
	 * Process the rtmedia_source and rtmedia prameters
	 *
	 * If 'rtmedia_source' is present, replace the "source" items with their
	 * corresponding rtmedia item attachments.
	 *
	 * If 'rtmedia' is present, remove items from the attachments array
	 * unless they are in the self::$rtmedia_post_info array of rtMedia items. 
	 *
	 * @since 1.06
	 *
	 * @param NULL $filtered_attachments initially NULL, indicating no substitution.
	 * @param array $attachments WP_Post objects returned by WP_Query->query, passed by reference
	 */
	public static function mla_gallery_the_attachments( $filtered_attachments, $attachments ) {
		//error_log( __LINE__ . ' MLABuddyPressHooksExample::mla_gallery_the_attachments $attachments = ' . var_export( $attachments, true ), 0 );

		// Are we translating from rtmedia_source IDs to attachments?
		if ( isset( self::$shortcode_attributes['rtmedia_source'] ) ) {
			global $wpdb;

			$filtered_attachments = array();
			$data_source = array(
				'data_source' => 'template',
				'meta_name' => str_replace( '{+', '[+', str_replace( '+}', '+]', self::$shortcode_attributes['rtmedia_source'] ) ),
				'option' => 'text',
				'format' => 'raw',
			);

			$rtmedia_ids = array();
			foreach( $attachments as $index => $attachment ) {
				if ( ! is_integer( $index ) ) {
					continue;
				}

				$data_value = MLAShortcodes::mla_get_data_source( $attachment->ID, 'single_attachment_mapping', $data_source, NULL );
				if ( 0 < $rtmedia_id = absint( $data_value ) ) {
					$rtmedia_ids[] = $rtmedia_id;
				}
			} // foreach attachment

			if ( !empty( $rtmedia_ids ) ) {
				// Build an array of SQL clauses, then run the query
				$query = array();
				$query[] = "SELECT rtm.id, rtm.media_id, rtm.media_author, rtm.media_type, rtm.cover_art, u.user_nicename FROM {$wpdb->prefix}rt_rtm_media AS rtm";
				$query[] = "LEFT JOIN {$wpdb->users} as u";
				$query[] = "ON (rtm.media_author = u.ID)";
				$query[] = 'WHERE ( rtm.id IN (' . implode( ',', $rtmedia_ids ) . ') )';

				$query =  join(' ', $query);
				$results = $wpdb->get_results( $query );

				// Replace the rtmedia_source objects with the attachments they refer to
				self::$rtmedia_post_info = array();
				if ( is_array( $results ) ) {
					foreach ( $results as $value ) {
						self::$rtmedia_post_info[ $value->media_id ] = $value;
						$attachment = get_post( $value->media_id );
						if ( NULL !== $attachment ) {
							$filtered_attachments[] = $attachment;
						}
					}
				}
			} // has rtmedia_ida

		$attachments = $filtered_attachments;
		$attachments['found_rows'] = count( $filtered_attachments );
		$attachments['max_num_pages'] = 0;
		//error_log( __LINE__ . ' MLABuddyPressHooksExample::mla_gallery_the_attachments $attachments = ' . var_export( $attachments, true ), 0 );
		//error_log( __LINE__ . ' MLABuddyPressHooksExample::mla_gallery_the_attachments self::$rtmedia_post_info = ' . var_export( self::$rtmedia_post_info, true ), 0 );
		return $attachments;
		} // rtmedia_source

		// Are we removing attachments that do not have an rtMedia ID?
		if ( ! ( isset( self::$shortcode_attributes['rtmedia'] ) && in_array( self::$shortcode_attributes['rtmedia'], array( 'gallery', 'true' ) ) ) ) {
			return $filtered_attachments;
		}

		$found_rows = isset( $attachments['found_rows'] ) ? $attachments['found_rows'] : count( $attachments );
		$changed = false;
		foreach( $attachments as $index => $attachment ) {
			if ( ! is_numeric( $index ) ) {
				continue;
			}

			if ( isset( self::$rtmedia_post_info[ $attachment->ID ] ) ) {
				continue;
			}

			unset( $attachments[ $index ] );
			$found_rows--;
			$changed = true;
		}

		if ( isset( $attachments['found_rows'] ) ) {
			$attachments['found_rows'] = $found_rows;
		}

		//error_log( __LINE__ . ' MLABuddyPressHooksExample::mla_gallery_the_attachments updated $attachments = ' . var_export( $attachments, true ), 0 );
		return $changed ? $attachments : NULL;
	} // mla_gallery_the_attachments

	/**
	 * Remove the parameters specific to this example plugin and
	 * parameters rtMedia does not allow.
	 *
	 * @since 1.06
	 *
	 * @param array $blacklist parameter_name => parameter_value pairs
	 */
	public static function mla_gallery_alt_shortcode_blacklist( $blacklist ) {
		$blacklist['rtmedia_source'] = '';
		$blacklist['rtmedia_ids'] = '';
		$blacklist['buddypress_urls'] = '';
		$blacklist['rtmedia'] = '';

		$blacklist['columns'] = '';
		$blacklist['size'] = '';
		$blacklist['link'] = '';
		$blacklist['option_all_value'] = '';

		return $blacklist;
	} // mla_gallery_alt_shortcode_blacklist

	/**
	 * MLA Gallery Alternate Shortcode Attributes
	 *
	 * @since 1.06
	 *
	 * @param array $attr parameter_name => parameter_value pairs
	 */
	public static function mla_gallery_alt_shortcode_attributes( $attr ) {
		//error_log( __LINE__ . ' mla_gallery_alt_shortcode_attributes attr = ' . var_export( $attr, true ), 0 );
		return $attr;
	} // mla_gallery_alt_shortcode_attributes

	/**
	 * If rtmedia=gallery, extract item IDs from the attachments array, convert them to rtMedia IDs
	 * and return them for use in the alternative gallery shortcode processing. 
	 *
	 * @since 1.06
	 *
	 * @param array $ids empty array, indicating no substitution
	 * @param string $ids_name parameter name
	 * @param array $attachments WP_Post objects returned by WP_Query->query, passed by reference
	 *
	 * @return array Substitute array of ID (or other) values to populate the parameter 
	 * @return string Complete 'ids_name="value,value"' parameter or an empty string to omit parameter
	 */
	public static function mla_gallery_alt_shortcode_ids( $ids, $ids_name, $attachments ) {
		//error_log( __LINE__ . " mla_gallery_alt_shortcode_ids( $ids_name ) attachments = " . var_export( $attachments, true ), 0 );

		if ( ! ( isset( self::$shortcode_attributes['rtmedia'] ) && ( 'gallery' === self::$shortcode_attributes['rtmedia'] ) ) ) {
			return $ids;
		}

		foreach( $attachments as $index => $attachment ) {
			if ( ! is_numeric( $index ) ) {
				continue;
			}

			if ( isset( self::$rtmedia_post_info[ $attachment->ID ] ) ) {
				$ids[] = self::$rtmedia_post_info[ $attachment->ID ]->id;
			}
		}

		//error_log( __LINE__ . " mla_gallery_alt_shortcode_ids( $ids_name ) ids = " . var_export( $ids, true ), 0 );
		return $ids;
	} // mla_gallery_alt_shortcode_ids

	/**
	 * Modifies the media query. It adds the filter to alter the WHERE parameter of the
	 * MySQL query and removes the context_id and context if set ( by the rtMedia plugin )
	 *
	 * @param  array     $media_query      Refer the `rtmedia_media_query` filter defined in the
	 *                                  rtMedia plugin
	 * @param  array    $action_query
	 * @param  array      $query
	 *
	 * @return array     $media_query
	 */
	public static function my_modify_media_query( $media_query, $action_query, $query ) {
		global $rtmedia_query, $media_query_clone_ids;
		//error_log( __LINE__ . ' my_modify_media_query media_query = ' . var_export( $media_query, true ), 0 );
		//error_log( __LINE__ . ' my_modify_media_query action_query = ' . var_export( $action_query, true ), 0 );
		//error_log( __LINE__ . ' my_modify_media_query query = ' . var_export( $query, true ), 0 );

		// Store the `media_ids` parameter to be used in the rtmedia-model-where-query filter
		$media_query_clone_ids = $media_query;

		if ( isset( $media_query['media_ids'] ) && '' != $media_query['media_ids'] ) {

			// Add the filter to modify the where parameter
			add_filter( 'rtmedia-model-where-query', 'MLABuddyPressHooksExample::my_rtmedia_model_shortcode_where_query_attributes', 10, 3 );

			// unset it, so that it wont affect the other rtmedia_gallery shortcodes on the same page
			unset( $media_query['media_ids'] );

			// unset from global query so that multiple gallery shortcode can work
			if ( isset( $rtmedia_query->query ) && isset( $rtmedia_query->query['media_ids'] ) ) {
				unset( $rtmedia_query->query['media_ids'] );
			}

			if ( isset( $media_query['context_id'] ) ) {
				unset( $media_query['context_id'] );
			}

			if ( isset( $media_query['context'] ) ) {
				unset( $media_query['context'] );
			}
		}

		return $media_query;
	} // my_modify_media_query

	/**
	 * Modify the WHERE parameter
	 *
	 * For the parameter description refer the `rtmedia-model-where-query` filter defined in the
	 * rtMedia plugin
	 */
	public static function my_rtmedia_model_shortcode_where_query_attributes( $where, $table_name, $join ) {
		global $rtmedia_query, $media_query_clone_ids;

		// Modify the WHERE parameter of the MySQL query
		if ( isset( $media_query_clone_ids['media_ids'] ) && '' != $media_query_clone_ids['media_ids'] ) {
			$where .= " AND $table_name.id IN ( " . $media_query_clone_ids['media_ids'] . ' )';
		}

		return $where;
	} // my_rtmedia_model_shortcode_where_query_attributes

	/**
	 * Remove `rtmedia-model-where-query` filter once our job is done
	 * so that it wont affect the other shortcodes
	 */
	public static function my_remove_rtmedia_model_shortcode_query_attributes() {
		remove_filter( 'rtmedia-model-where-query', 'my_rtmedia_model_shortcode_where_query_attributes', 10, 3 );
	} // my_remove_rtmedia_model_shortcode_query_attributes


	/**
	* Sets `media_ids` parameter in rtmedia query
	*
	* @param type $param
	*
	* @return array
	*/
	public static function my_rtmedia_allowed_attributes_parameter_in_query( $param = array() ) {
		$param[] = 'media_ids';
		return $param;
	} // my_rtmedia_allowed_attributes_parameter_in_query

	/**
	 * For buddypress_urls, rewrite the URL to reference the rtMedia version of the item
	 *
	 * @since 1.00
	 *
	 * @param array $item_values parameter_name => parameter_value pairs
	 */
	public static function mla_gallery_item_values( $item_values ) {
		//error_log( __LINE__ . ' MLABuddyPressHooksExample::mla_gallery_item_values $item_values = ' . var_export( $item_values, true ), 0 );

		/*
		 * We use a shortcode parameter of our own to apply our filters on a gallery-by-gallery basis,
		 * leaving other [mla_gallery] instances untouched. If the "my_filter" parameter is not present,
		 * we have nothing to do.
		 */		
		if ( ! isset( self::$shortcode_attributes['buddypress_urls'] ) ) {
			return $item_values; // leave them unchanged
		}

		// post_info holds the rtMedia information about the item
		if ( isset( self::$rtmedia_post_info[ $item_values['attachment_ID'] ] ) ) {
			$post_info = self::$rtmedia_post_info[ $item_values['attachment_ID'] ];
		} else {
			return $item_values; // no matching rtMedia item
		}

		// Rewrite the URL to reference the rtMedia version of the item
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
	} // mla_gallery_item_values
} // Class MLABuddyPressHooksExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLABuddyPressHooksExample::initialize');
?>
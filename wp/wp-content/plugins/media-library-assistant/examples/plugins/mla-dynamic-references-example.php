<?php
/**
 * Updates custom field(s) "where-used" inserted-in and featured-image when an item is edited
 *
 * @package MLA Dynamic References Example
 * @version 1.03
 */

/*
Plugin Name: MLA Dynamic References Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Updates custom field(s) "where-used" inserted-in and featured-image when an item is edited
Author: David Lingren
Version: 1.03
Author URI: http://fairtradejudaica.org/our-story/staff/

Copyright 2014 - 2015 David Lingren

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

/*
 * 
 */
 
/**
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding everything
 * else inside a class means this is the only name you have to worry about.
 *
 * @package MLA Dynamic References Example
 * @since 1.00
 */
class MLADynamicReferencesExample {
	/**
	 * Names of the custom field(s) to be maintained
	 *
	 * Enter the name of a custom field in each category you want to maintain.
	 * Enter an empty string or comment out each category you don't use.
	 *
	 * Set 'used_first_feature' and/or 'used_first_insert' to true if you want
	 * them to be the default selection in the Media Manager dropdown control.
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $field_names = array( 
		'featured' => 'featured',
		'featured_in' => 'featured_in',
		'inserted' => 'inserted',
		'inserted_in' => 'inserted_in',
		'used' => 'used',
		'used_in' => 'used_in',
		'used_first_feature' => true,
		'used_first_insert' => true,
	);

	/**
	 * WordPress version test for $wpdb->esc_like() Vs esc_sql()
	 *
	 * @since 1.00
	 *
	 * @var	boolean
	 */
	private static $wp_4dot0_plus = true;

	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 1.00
	 *
	 * @return	void
	 */
	public static function initialize() {
		/*
		 * The filter is only needed in the Admin area
		 */
		if ( ! is_admin() ) {
			return;
		}

		self::$wp_4dot0_plus = version_compare( get_bloginfo('version'), '4.0', '>=' );
		
		/*
		 * Defined in media-library-assistant/includes/class-mla-media-modal.php
		 * function mla_media_view_settings_filter
		 */
		add_filter( 'mla_media_modal_initial_filters', 'MLADynamicReferencesExample::mla_media_modal_initial_filters', 10, 2 );
		add_filter( 'mla_media_modal_settings', 'MLADynamicReferencesExample::mla_media_modal_settings', 10, 2 );

		/*
		 * Defined in wp-includes/post.php function wp_insert_post
		 */
		add_action( 'post_updated', 'MLADynamicReferencesExample::post_updated', 10, 3 );
		add_action( 'wp_insert_post', 'MLADynamicReferencesExample::wp_insert_post', 10, 3 );

		/*
		 * Defined in wp-includes/meta.php
		 */
		add_action( 'added_post_meta', 'MLADynamicReferencesExample::added_post_meta', 10, 4 );

		add_action( 'update_post_meta', 'MLADynamicReferencesExample::update_post_meta', 10, 4 );
		add_action( 'updated_post_meta', 'MLADynamicReferencesExample::updated_post_meta', 10, 4 );

		add_action( 'delete_post_meta', 'MLADynamicReferencesExample::delete_post_meta', 10, 4 );
		add_action( 'deleted_post_meta', 'MLADynamicReferencesExample::deleted_post_meta', 10, 4 );
	}

	/**
	 * Add the "Available" view to the Set Featured Image dropdown control
	 *
	 * @since 1.01
	 *
	 * @param array   $initial_values Settings passed to the Media Manager Modal Window scripts.
	 * @param WP_Post $post           Post object in which the MMMW is embeded.
	 */
	public static function mla_media_modal_initial_filters( $initial_values, $post ) {
		//error_log( __LINE__ . " mla_media_modal_settings post = " . var_export( $post, true ), 0 );

		if ( ! empty( self::$field_names['used'] ) ) {
			if ( self::$field_names['used_first_feature'] && isset( $initial_values['filterUploaded'] ) ) {
				$initial_values['filterUploaded'] = 'custom:' . self::$field_names['used'] . ',null';
			}

			if ( self::$field_names['used_first_insert'] && isset( $initial_values['filterMime'] ) ) {
				$initial_values['filterMime'] = 'custom:' . self::$field_names['used'] . ',null';
			}
		}
		
		//error_log( __LINE__ . " mla_media_modal_settings initial_values = " . var_export( $initial_values, true ), 0 );
		return $initial_values;
	}

	/**
	 * Add the "Available" view to the Set Featured Image dropdown control
	 *
	 * @since 1.01
	 *
	 * @param array   $settings Settings passed to the Media Manager Modal Window scripts.
	 * @param WP_Post $post     Post object in which the MMMW is embeded.
	 */
	public static function mla_media_modal_settings( $settings, $post ) {
		//error_log( __LINE__ . " mla_media_modal_settings settings = " . var_export( $settings, true ), 0 );
		//error_log( __LINE__ . " mla_media_modal_settings post = " . var_export( $post, true ), 0 );

		if ( ! empty( self::$field_names['used'] ) ) {
			if ( isset( $settings['mla_settings'] ) && isset( $settings['mla_settings']['uploadMimeTypes'] ) ) {
				$settings['mla_settings']['uploadMimeTypes']['custom:' . self::$field_names['used'] . ',null'] = 'Not Used';
				$settings['mla_settings']['uploadMimeTypes']['custom:' . self::$field_names['used'] . '=*'] = 'Already Used';
			}
		}
		//error_log( __LINE__ . " mla_media_modal_settings settings[mla_settings][uploadMimeTypes] = " . var_export( $settings['mla_settings']['uploadMimeTypes'], true ), 0 );
		
		return $settings;
	}

	/**
	 * Analyze before and after content when an existing item is updated
	 *
	 * @since 1.00
	 *
	 * @param int     $post_ID      Post ID.
	 * @param WP_Post $post_after   Post object following the update.
	 * @param WP_Post $post_before  Post object before the update.
	 */
	public static function post_updated( $post_ID, $post_after, $post_before ) {
		//error_log( __LINE__ . " post_updated( {$post_ID} ) post_after = " . var_export( $post_after, true ), 0 );
		if ( 'trash' == $post_after->post_status ) {
			$after = array();
		} else {
			$after = self::_attachments_in( $post_after->post_content );
		}
		
		//error_log( __LINE__ . " post_updated( {$post_ID} ) post_before = " . var_export( $post_before, true ), 0 );
		if ( 'trash' == $post_before->post_status ) {
			$attachments_before = array();
		} else {
			$attachments_before = self::_attachments_in( $post_before->post_content );
		}

		// Eliminate unchanged attachments
		$attachments_after = array();
		foreach( $after as $ID ) {
			if ( isset( $attachments_before[ $ID ] ) ) {
				unset( $attachments_before[ $ID ] );
			} else {
				$attachments_after[ $ID ] = $ID;
			}
		}
		
		self::_update_inserted_in( $attachments_after );
		self::_update_inserted_in( $attachments_before );
		
		if ( 'auto-draft' == $post_before->post_status ) {
			if ( $attachment = get_post_meta( $post_ID, '_thumbnail_id', true ) ) {
				self::_update_featured_in( $attachment );
			}
		}
	}

	/**
	 * Analyze post content when a new item is inserted i.e., $update == false
	 *
	 * @since 1.00
	 *
	 * @param int     $post_ID	Post ID.
	 * @param WP_Post $post		Post object following the update.
	 * @param WP_Post $update	true if updating, false if inserting.
	 */
	public static function wp_insert_post( $post_ID, $post, $update ) {
		//error_log( __LINE__ . " wp_insert_post( {$post_ID}, " . var_export( $update, true ) . " ) post = " . var_export( $post, true ), 0 );
		
		if ( $update || in_array( $post->post_status, array( 'inherit', 'auto-draft' ) ) ) {
			return;
		}
		
		$attachments = self::_attachments_in( $post->post_content );
		self::_update_inserted_in( $attachments );
	}

	/**
	 * Fires immediately after meta of a specific type is added.
	 *
	 * @since 1.00
	 *
	 * @param int    $mid        The meta ID after successful update.
	 * @param int    $object_id  Object ID.
	 * @param string $meta_key   Meta key.
	 * @param mixed  $meta_value Meta value.
	 */
	public static function added_post_meta( $mid, $object_id, $meta_key, $_meta_value ) {
		//error_log( __LINE__ . " added_post_meta( {$object_id}, {$meta_key} ) _meta_value = " . var_export( $_meta_value, true ), 0 );

		if ( '_thumbnail_id' == $meta_key ) {
			self::_update_featured_in( $_meta_value );
		}
	}

	/**
	 * Fires immediately before updating metadata of a specific type.
	 *
	 * @since 1.00
	 *
	 * @param int    $meta_id    ID of the metadata entry to update.
	 * @param int    $object_id  Object ID.
	 * @param string $meta_key   Meta key.
	 * @param mixed  $meta_value New meta value.
	 */
	public static function update_post_meta( $meta_id, $object_id, $meta_key, $_meta_value ) {
		if ( '_thumbnail_id' == $meta_key ) {
			// $_meta_value contains the NEW value. Must get the old value ourselves.
			self::$old_value = get_post_meta( $object_id, '_thumbnail_id', true );
			//error_log( __LINE__ . " update_post_meta( {$object_id}, {$meta_key} ) old_value = " . var_export( self::$old_value, true ), 0 );
		}
	}

	/**
	 * Featured Image value before update
	 *
	 * @since 1.00
	 *
	 * @var	integer
	 */
	private static $old_value = 0;

	/**
	 * Fires immediately after updating metadata of a specific type.
	 *
	 * @since 1.00
	 *
	 * @param int    $meta_id    ID of updated metadata entry.
	 * @param int    $object_id  Object ID.
	 * @param string $meta_key   Meta key.
	 * @param mixed  $meta_value Meta value.
	 */
	public static function updated_post_meta( $meta_id, $object_id, $meta_key, $_meta_value ) {
		//error_log( __LINE__ . " updated_post_meta( {$object_id}, {$meta_key} ) _meta_value = " . var_export( $_meta_value, true ), 0 );

		if ( '_thumbnail_id' == $meta_key ) {
			if ( self::$old_value ) {
				self::_update_featured_in( self::$old_value );
				self::$old_value = 0;
			}
			
			self::_update_featured_in( $_meta_value );
		}
	}

	/**
	 * Fires immediately before deleting metadata of a specific type.
	 *
	 * @since 1.00
	 *
	 * @param array  $meta_ids   An array of deleted metadata entry IDs.
	 * @param int    $object_id  Object ID.
	 * @param string $meta_key   Meta key.
	 * @param mixed  $meta_value Meta value.
	 */
	public static function delete_post_meta( $meta_ids, $object_id, $meta_key, $_meta_value ){
		if ( '_thumbnail_id' == $meta_key ) {
			// $_meta_value contains nothing. Must get the old value ourselves.
			self::$old_value = get_post_meta( $object_id, '_thumbnail_id', true );
			//error_log( __LINE__ . " delete_post_meta( {$object_id}, {$meta_key} ) old_value = " . var_export( self::$old_value, true ), 0 );
		}
	}

	/**
	 * Fires immediately after deleting metadata of a specific type.
	 *
	 * @since 1.00
	 *
	 * @param array  $meta_ids   An array of deleted metadata entry IDs.
	 * @param int    $object_id  Object ID.
	 * @param string $meta_key   Meta key.
	 * @param mixed  $meta_value Meta value.
	 */
	public static function deleted_post_meta( $meta_ids, $object_id, $meta_key, $_meta_value ){
		//error_log( __LINE__ . " deleted_post_meta( {$object_id}, {$meta_key} ) _meta_value = " . var_export( $_meta_value, true ), 0 );

		if ( '_thumbnail_id' == $meta_key && self::$old_value ) {
				self::_update_featured_in( self::$old_value );
				self::$old_value = 0;
		}
	}

	/**
	 * Update "Used/Used in" data for one attachment
	 *
	 * Called from _update_inserted_in and _update_featured_in.
	 *
	 * @since 1.00
	 *
	 * @param array	$attachments	attachment IDs ( ID => ID )
	 */
	private static function _update_used_in( $ID, $inserts = NULL, $features = NULL ) {
		//error_log( __LINE__ . " _update_used_in( {$ID} ) inserts = " . var_export( $inserts, true ), 0 );
		//error_log( __LINE__ . " _update_used_in( {$ID} ) features = " . var_export( $features, true ), 0 );

		if ( empty( self::$field_names['used_in'] ) && empty( self::$field_names['used'] ) ) {
			return;
		}
		
		if ( empty( $inserts ) ) {
			$inserts = self::_inserted_in( $ID );
		}

		if ( empty( $features ) ) {
			$features = self::_featured_in( $ID );
		}

		$results = array();
		$titles = array();
		foreach ( $features as $post_id => $value ) {
			if ( 'auto-draft' === $value->post_status ) {
				continue;
			}
				
			$results[] = sprintf( '%1$s (%2$s %3$d)', $value->post_title, $value->post_type, $post_id ); 
			$titles[] = $value->post_title; 
		}
		$final_results = implode( ',', $results );
		$final_titles = implode( ',', $titles );

		$results = array();
		$titles = array();
		// $inserts is indexed on file name; need a second level
		foreach ( $inserts as $files ) {
			foreach ( $files as $value ) {
				$results[] = sprintf( '%1$s (%2$s %3$d)', $value->post_title, $value->post_type, $value->ID ); 
				$titles[] = $value->post_title; 
			}
		}
		$final_results = trim( $final_results . implode( ',', $results ) );
		$final_titles = trim( $final_titles . implode( ',', $titles ) );
		
		if ( ! empty( self::$field_names['used_in'] ) ) {
			if ( empty( $final_results ) ) {
				delete_metadata( 'post', $ID, self::$field_names['used_in'] );
			} else {
				update_metadata( 'post', $ID, self::$field_names['used_in'], $final_results );
			}
		}
		
		if ( ! empty( self::$field_names['used'] ) ) {
			if ( empty( $final_titles ) ) {
				delete_metadata( 'post', $ID, self::$field_names['used'] );
			} else {
				update_metadata( 'post', $ID, self::$field_names['used'], $final_titles );
			}
		}
	}

	/**
	 * Update "Inserted in" data for one or more attachments
	 *
	 * @since 1.00
	 *
	 * @param array	$attachments	attachment IDs ( ID => ID )
	 */
	private static function _update_inserted_in( $attachments ) {
		//error_log( __LINE__ . " _update_inserted_in attachments = " . var_export( $attachments, true ), 0 );
		
		if ( empty( $attachments ) ) {
			return;
		} elseif ( is_numeric( $attachments ) ) {
			$attachments = absint( $attachments );
			$attachments = array( $attachments => $attachments );
		}

		foreach ( $attachments as $ID ){
			$inserts = self::_inserted_in( $ID );

			$results = array();
			$titles = array();
			// $inserts is indexed on file name; need a second level
			foreach ( $inserts as $files ) {
				foreach ( $files as $value ) {
					$results[] = sprintf( '%1$s (%2$s %3$d)', $value->post_title, $value->post_type, $value->ID ); 
					$titles[] = $value->post_title; 
				}
			}
			
			if ( ! empty( self::$field_names['inserted_in'] ) ) {
				if ( empty( $results ) ) {
					delete_metadata( 'post', $ID, self::$field_names['inserted_in'] );
				} else {
					update_metadata( 'post', $ID, self::$field_names['inserted_in'], implode( ',', $results ) );
				}
			}
			
			if ( ! empty( self::$field_names['inserted'] ) ) {
				if ( empty( $titles ) ) {
					delete_metadata( 'post', $ID, self::$field_names['inserted'] );
				} else {
					update_metadata( 'post', $ID, self::$field_names['inserted'], implode( ',', $titles ) );
				}
			}
			
			self::_update_used_in( $ID, $inserts, NULL );
		}
	}

	/**
	 * Update "Featured in" data for one or more attachments
	 *
	 * @since 1.00
	 *
	 * @param array	$attachments	attachment IDs ( ID => ID )
	 */
	private static function _update_featured_in( $attachments ) {
		//error_log( __LINE__ . " _update_featured_in attachments = " . var_export( $attachments, true ), 0 );
		
		if ( empty( $attachments ) ) {
			return;
		} elseif ( is_numeric( $attachments ) ) {
			$attachments = absint( $attachments );
			$attachments = array( $attachments => $attachments );
		}

		foreach ( $attachments as $ID ){
			$features = self::_featured_in( $ID );

			$results = array();
			$titles = array();
			foreach ( $features as $post_id => $value ) {
				if ( 'auto-draft' === $value->post_status ) {
					continue;
				}
				
				$results[] = sprintf( '%1$s (%2$s %3$d)', $value->post_title, $value->post_type, $post_id ); 
				$titles[] = $value->post_title; 
			}

			if ( ! empty( self::$field_names['featured_in'] ) ) {
				if ( empty( $results ) ) {
					delete_metadata( 'post', $ID, self::$field_names['featured_in'] );
				} else {
					update_metadata( 'post', $ID, self::$field_names['featured_in'], implode( ',', $results ) );
				}
			}
			
			if ( ! empty( self::$field_names['featured'] ) ) {
				if ( empty( $titles ) ) {
					delete_metadata( 'post', $ID, self::$field_names['featured'] );
				} else {
					update_metadata( 'post', $ID, self::$field_names['featured'], implode( ',', $titles ) );
				}
			}
			
			self::_update_used_in( $ID, NULL, $features );
		}
	}

	/**
	 * Find the attachments inserted in post_content
	 *
	 * @since 1.00
	 *
	 * @param	string	$post_content Post/Page contents.
	 *
	 * @return	array	Attachment IDs, indexed by ID ( ID => ID ).
	 */
	private static function _attachments_in( $post_content ){
		$inserts = array();
		$match_count = preg_match_all( '/\<img.*wp-image-(\d+).*\>/', $post_content, $matches );
		if ( ( $match_count == false ) || ( $match_count == 0 ) ) {
			return $inserts;
		}
		//error_log( __LINE__ . " _attachments_in( {$match_count} ) matches = " . var_export( $matches, true ), 0 );

		foreach ( $matches[1] as $match ) {
			$ID = absint( $match );
			$inserts[ $ID ] = $ID;
		}

		//error_log( __LINE__ . " _attachments_in( {$match_count} ) inserts = " . var_export( $inserts, true ), 0 );
		return $inserts;
	}

	/**
	 * Find the "Featured in" information for an attachment
	 *
	 * @since 1.00
	 *
	 * @param	int		$object_id  Attachment ID.
	 *
	 * @return	array	Featured in information object ( 'ID' =>, 'post_type' =>, 'post_status' =>, 'post_title' => ).
	 */
	private static function _featured_in( $object_id ){
		global $wpdb;

		$results = $wpdb->get_results( 
			"
			SELECT p.ID, p.post_type, p.post_status, p.post_title
			FROM {$wpdb->postmeta} AS m INNER JOIN {$wpdb->posts} AS p ON m.post_id = p.ID
			WHERE ( m.meta_key = '_thumbnail_id' )
			AND ( m.meta_value = {$object_id} ) AND (post_type <> 'revision')
			"
		);
		
		$features = array();
		foreach ( $results as $result ) {
			$features[ $result->ID ] = $result;
		}
		//error_log( __LINE__ . " _featured_in( {$object_id} ) features = " . var_export( $features, true ), 0 );

		return $features;
	}
	
	/**
	 * Find the "Inserted in" information for an attachment
	 *
	 * @since 1.00
	 *
	 * @param	int		$object_id  Attachment ID.
	 *
	 * @return	array	Inserted in information array( file_name => array( object ( 'ID' =>, 'post_type' =>, 'post_status' =>, 'post_title' =>, ) ) ).
	 */
	private static function _inserted_in( $object_id ){
		global $wpdb;

		$references = array();
		$references['base_file'] = get_post_meta( $object_id, '_wp_attached_file', true );
		$pathinfo = pathinfo($references['base_file']);
		$references['file'] = $pathinfo['basename'];
		if ( ( ! isset( $pathinfo['dirname'] ) ) || '.' == $pathinfo['dirname'] ) {
			$references['path'] = '/';
		} else {
			$references['path'] = $pathinfo['dirname'] . '/';
		}

		$attachment_metadata = get_post_meta( $object_id, '_wp_attachment_metadata', true );
		$sizes = isset( $attachment_metadata['sizes'] ) ? $attachment_metadata['sizes'] : NULL;
		if ( is_array( $sizes ) ) {
			// Using the name as the array key ensures each name is added only once
			foreach ( $sizes as $size => $size_info ) {
				$size_info['size'] = $size;
				$references['files'][ $references['path'] . $size_info['file'] ] = $size_info;
			}
		}

		$base_type = wp_check_filetype( $references['file'] );
		$base_reference = array(
			'file' => $references['file'],
			'width' => isset( $attachment_metadata['width'] ) ? $attachment_metadata['width'] : 0,
			'height' => isset( $attachment_metadata['height'] ) ? $attachment_metadata['height'] : 0,
			'mime_type' => isset( $base_type['type'] ) ? $base_type['type'] : 'unknown',
			'size' => 'full',
			);

		$references['files'][ $references['base_file'] ] = $base_reference;

		$query_parameters = array();
		$query = array();
		$query[] = "SELECT ID, post_type, post_status, post_title, CONVERT(`post_content` USING utf8 ) AS POST_CONTENT FROM {$wpdb->posts} WHERE (post_type <> 'revision') AND ( %s=%s";
		$query_parameters[] = '1'; // for empty file name array
		$query_parameters[] = '0'; // for empty file name array

		foreach ( $references['files'] as $file => $file_data ) {
			if ( empty( $file ) ) {
				continue;
			}

			$query[] = 'OR ( POST_CONTENT LIKE %s)';

			if ( self::$wp_4dot0_plus ) {
				$query_parameters[] = '%' . $wpdb->esc_like( $file ) . '%';
			} else {
				$query_parameters[] = '%' . like_escape( $file ) . '%';
			}
		}

		$query[] = ')';
		$query = join(' ', $query);

		$inserts = $wpdb->get_results(
			$wpdb->prepare( $query, $query_parameters )
		);

		if ( ! empty( $inserts ) ) {
			$references['inserts'][ $pathinfo['filename'] ] = $inserts;

			foreach ( $inserts as $index => $insert ) {
				unset( $references['inserts'][ $pathinfo['filename'] ][ $index ]->POST_CONTENT );
			} // foreach $insert
		} else {
			$references['inserts'] = array();
		}
		//error_log( __LINE__ . " _inserted_in( {$object_id} ) references = " . var_export( $references, true ), 0 );
		
		return $references['inserts'];
	}
} // Class MLADynamicReferencesExample

/*
 * Install the filter at an early opportunity
 */
add_action('init', 'MLADynamicReferencesExample::initialize');
?>
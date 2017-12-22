<?php
/**
 * Allows "owner only" access to the Media Library for the Subscriber role
 *
 * Created for support topic "Limiting users to their own uploaded media"
 * opened on 9/28/2017 by "drewmarksystems".
 * https://wordpress.org/support/topic/limiting-users-to-their-own-uploaded-media/
 *
 * @package MLA Subscriber Media Access Example
 * @version 1.00
 */

/*
Plugin Name: MLA Subscriber Media Access Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Allows "owner only" access to the Media Library for the Subscriber role
Author: David Lingren
Version: 1.00
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
 * Class MLA Subscriber Media Access Example allows "owner only" access to the Media Library
 * for the Subscriber role
 *
 * @package MLA Subscriber Media Access Example
 * @since 1.00
 */
class MLASubscriberMediaAccessExample {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 1.00
	 */
	public static function initialize() {
		global $current_user;

		// The filters are only useful for admin pages; exit if in the front end
		if ( !is_admin() ) {
			return;
		}

		// Use extra caution before applying actions & filters
		if ( ( $current_user instanceof WP_User ) && ( 0 !== $current_user->ID ) ) {
			if( 'subscriber' == $current_user->roles[0] && current_user_can( 'upload_files' ) ) {
				add_action( 'pre_get_posts', 'MLASubscriberMediaAccessExample::pre_get_posts', 10, 1 );
				add_filter( 'views_media_page_mla-menu', 'MLASubscriberMediaAccessExample::views_media_page_mla_menu', 10, 1 );
				add_filter( 'wp_count_attachments', 'MLASubscriberMediaAccessExample::wp_count_attachments', 10, 1 );
				
				// Run at lower priority to allow for thumbnail generation bulk action
				add_filter( 'mla_list_table_get_bulk_actions', 'MLASubscriberMediaAccessExample::mla_list_table_get_bulk_actions', 11, 1 );
			}
		}
	}

	/**
	 * Pre Get Posts for role = subscriber
	 *
	 * Restrict Media Library items to those owned by the subscriber
	 *
	 * @since 1.00
	 *
	 * @param	object	$wp_query Current WP_Query object
	 */
	public static function pre_get_posts( $wp_query ) {
		global $current_user;

		$wp_query->set( 'author', $current_user->ID );
	}

	/**
	 * WP Count Attachments for role = subscriber
	 *
	 * @since 1.00
	 *
	 * @param	object	$counts An object containing the attachment counts by mime type.
	 */
	public static function wp_count_attachments( $counts ) {
		global $wpdb;
		global $current_user;

		$and = wp_post_mime_type_where(''); // Default mime type // AND post_author = {$current_user->ID}
		$count = $wpdb->get_results("SELECT post_mime_type, COUNT(*) AS num_posts FROM $wpdb->posts WHERE post_type = 'attachment' AND post_status != 'trash' AND post_author = {$current_user->ID} $and GROUP BY post_mime_type", ARRAY_A);

		$counts = array();
	
		foreach((array)$count as $row){
			$counts[$row['post_mime_type']] = $row['num_posts'];
		}

		$counts['trash'] = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'attachment' AND post_author = {$current_user->ID} AND post_status = 'trash' $and");

		return $counts;
	}

	/**
	 * Media/Assistant submenu table views for role = subscriber
	 *
	 * @since 1.00
	 *
	 * @param	array	$views An array of available list table views.
	 *					format: view_slug => link to the view, with count
	 */
	public static function views_media_page_mla_menu( $views ) {
		global $wpdb;
		global $current_user;
	
		$detached_items = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'attachment' AND post_status != 'trash' AND post_parent < 1 AND post_author = {$current_user->ID}" );
		$base_url = 'upload.php?page=' . MLACore::ADMIN_PAGE_SLUG;

		// 'detached':
		if ( $detached_items ) {
			$class = strpos( $views['detached'], 'class="current"' ) ? ' class="current"' : '';
			$value = sprintf('Unattached <span class="count">(%s)</span>', number_format_i18n( $detached_items ) );
			$views['detached'] = '<a href="' . add_query_arg( array( 'detached' => '1' ), $base_url ) . '"' . $class . '>' . $value . '</a>';
		} else {
			unset( $views['detached'] );
		}

		unset( $views['attached'] );
		return $views;
	} // views_media_page_mla_menu

	/**
	 * Filter the MLA_List_Table bulk actions
	 *
	 * Removes Edit and Thumbnail from subscriber's bulk actions.
	 *
	 * @since 1.00
	 *
	 * @param	array	$actions	An array of bulk actions.
	 *								Format: 'slug' => 'Label'
	 */
	public static function mla_list_table_get_bulk_actions( $actions ) {
		unset( $actions['edit'] );
		
		if ( class_exists( 'MLA_Thumbnail' ) ) {
			unset( $actions[MLA_Thumbnail::MLA_GFI_ACTION] );
		}
		
		return $actions;
	} // mla_list_table_get_bulk_actions

	/**
	 * Perform one-time capabilities addition on plugin activation
	 *
	 * @since 1.00
	 */
	public static function mla_activation_hook( ) {
		$subscriber = get_role( 'subscriber' );
		
		if ( !array_key_exists( 'upload_files', $subscriber->capabilities ) ) {
			$subscriber->add_cap( 'upload_files' );
			$subscriber->add_cap( 'delete_posts' );
		}
	}

	/**
	 * Perform one-time capabilities removal on plugin deactivation
	 *
	 * @since 1.00
	 */
	public static function mla_deactivation_hook( ) {
		$subscriber = get_role( 'subscriber' );

		if ( array_key_exists( 'upload_files', $subscriber->capabilities ) ) {
			$subscriber->remove_cap( 'upload_files' );
			$subscriber->remove_cap( 'delete_posts' );
		}
	}
} // Class MLASubscriberMediaAccessExample

// Install the filters at an early opportunity
add_action('init', 'MLASubscriberMediaAccessExample::initialize');

// Register hooks that are fired when the plugin is activated or deactivated.
register_activation_hook( __FILE__, array( 'MLASubscriberMediaAccessExample', 'mla_activation_hook' ) );
register_deactivation_hook( __FILE__, array( 'MLASubscriberMediaAccessExample', 'mla_deactivation_hook' ) );
?>
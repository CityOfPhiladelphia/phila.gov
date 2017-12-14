<?php
/**
 * Removes Unattached items from the Media Library
 *
 * Adds a Tools/Unattached Fixit submenu with buttons to perform the operations.
 *
 * @package Unattached Fixit
 * @version 1.01
 */

/*
Plugin Name: MLA Unattached Fixit
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Removes Unattached items from the Media Library
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
 * Class Unattached Fixit implements a Tools submenu page with several image-fixing tools.
 *
 * Created for support topic "Bulk delete Unattached images/media"
 * opened on 11/24/2015 by "lododicesimo":
 * https://wordpress.org/support/topic/bulk-delete-unattached-imagesmedia
 *
 * @package Unattached Fixit
 * @since 1.00
 */
class Unattached_Fixit {
	/**
	 * Current version number
	 *
	 * @since 1.00
	 *
	 * @var	string
	 */
	const CURRENT_VERSION = '1.01';

	/**
	 * Slug prefix for registering and enqueueing submenu pages, style sheets and scripts
	 *
	 * @since 1.00
	 *
	 * @var	string
	 */
	const SLUG_PREFIX = 'unattachfixit-';

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
		self::$wp_4dot0_plus = version_compare( get_bloginfo('version'), '4.0', '>=' );
		
		//add_action( 'admin_init', 'Unattached_Fixit::admin_init_action' );
		add_action( 'admin_menu', 'Unattached_Fixit::admin_menu_action' );
	}

	/**
	 * Admin Init Action
	 *
	 * @since 1.00
	 *
	 * @return	void
	 */
	public static function admin_init_action() {
	}

	/**
	 * Add submenu page in the "Tools" section
	 *
	 * @since 1.00
	 *
	 * @return	void
	 */
	public static function admin_menu_action( ) {
		$current_page_hook = add_submenu_page( 'tools.php', 'Unattached Fixit Tools', 'Unattached Fixit', 'manage_options', self::SLUG_PREFIX . 'tools', 'Unattached_Fixit::render_tools_page' );
		add_filter( 'plugin_action_links', 'Unattached_Fixit::add_plugin_links_filter', 10, 2 );
	}

	/**
	 * Add the "Tools" link to the Plugins section entry
	 *
	 * @since 1.00
	 *
	 * @param	array 	array of links for the Plugin, e.g., "Activate"
	 * @param	string 	Directory and name of the plugin Index file
	 *
	 * @return	array	Updated array of links for the Plugin
	 */
	public static function add_plugin_links_filter( $links, $file ) {
		if ( $file == 'mla-unattached-fixit.php' ) {
			$tools_link = sprintf( '<a href="%s">%s</a>', admin_url( 'tools.php?page=' . self::SLUG_PREFIX . 'tools' ), 'Tools' );
			array_unshift( $links, $tools_link );
		}

		return $links;
	}

	/**
	 * Render (echo) the "Unattached Fixit" submenu in the Tools section
	 *
	 * @since 1.00
	 *
	 * @return	void Echoes HTML markup for the submenu page
	 */
	public static function render_tools_page() {
error_log( 'Unattached_Fixit::render_tools_page() $_REQUEST = ' . var_export( $_REQUEST, true ), 0 );
		if ( !current_user_can( 'manage_options' ) ) {
			echo "Unattached Fixit - Error</h2>\n";
			wp_die( 'You do not have permission to manage plugin settings.' );
		}
		
		$setting_actions = array(
			'help' => array( 'handler' => '', 'comment' => '<strong>Enter first and (optional) last attachment/item ID values above to restrict tool application range</strong>. To operate on one ID, enter just the "First ID". The default is to perform the operation on <strong>all Media Library items</strong>.<br />&nbsp;<br />You can find ID values in the "ID/Parent" column or by by hovering over the thumbnail image in the Media/Assistant submenu table; look for the number following <code>post=</code> in the item&rsquo;s URL.' ),
			'warning' => array( 'handler' => '', 'comment' => '<strong>These tools make permanent updates to your database.</strong> Make a backup before you use the tools so you can restore your old values if you don&rsquo;t like the results.' ),
			'trash' => array( 'handler' => '', 'comment' => 'You can <code>define (&quot;MEDIA_TRASH&quot;, true);</code> in your <code>wp-config.php</code> to activate the WordPress "Trash" feature for attachments.' ),

			'c0' => array( 'handler' => '', 'comment' => '<h3>Unattached Media Library item operations</h3>' ),
			'Trash Unattached' => array( 'handler' => '_trash_unattached_items',
				'comment' => 'Move unattached items to "media trash".' ),
			'Delete Unattached' => array( 'handler' => '_delete_unattached_items',
				'comment' => 'Permanently delete unattached items.' ),
 		);

		/*
		 * Conditional display of the "Trash" action
		 */
		if ( ! ( defined('MEDIA_TRASH') && MEDIA_TRASH ) ) {
			unset( $setting_actions['Trash Unattached'] );
		}
		
		echo '<div class="wrap">' . "\n";
		echo "\t\t" . '<div id="icon-tools" class="icon32"><br/></div>' . "\n";
		echo "\t\t" . '<h2>Unattached Fixit Tools v' . self::CURRENT_VERSION . '</h2>' . "\n";

		if ( !current_user_can( 'delete_posts' ) ) {
			echo "\t\t<br>ERROR: You are not allowed to delete/trash Media Library items.\n";
			return;
		}

		if ( isset( $_REQUEST[ self::SLUG_PREFIX . 'action' ] ) ) {
			$label = $_REQUEST[ self::SLUG_PREFIX . 'action' ];
			if( isset( $setting_actions[ $label ] ) ) {
				$action = $setting_actions[ $label ]['handler'];
				if ( ! empty( $action ) ) {
					if ( method_exists( 'Unattached_Fixit', $action ) ) {
						echo self::$action();
					} else {
						echo "\t\t<br>ERROR: handler does not exist for action: \"{$label}\"\n";
					}
				} else {
					echo "\t\t<br>ERROR: no handler for action: \"{$label}\"\n";
				}
			} else {
				echo "\t\t<br>ERROR: unknown action: \"{$label}\"\n";
			}
		}

		echo "\t\t" . '<div style="width:700px">' . "\n";
		echo "\t\t" . '<form action="' . admin_url( 'tools.php?page=' . self::SLUG_PREFIX . 'tools' ) . '" method="post" class="' . self::SLUG_PREFIX . 'tools-form-class" id="' . self::SLUG_PREFIX . 'tools-form-id">' . "\n";
		echo "\t\t" . '  <p class="submit" style="padding-bottom: 0;">' . "\n";
		echo "\t\t" . '    <table>' . "\n";

		echo "\t\t" . '      <tr valign="top">' . "\n";
		echo "\t\t" . '        <td width="24%" style="text-align: right; padding-right: 5px" >First Attachment ID</td>' . "\n";
		echo "\t\t" . '        <td width="24%" style="text-align: left;"><input name="' . self::SLUG_PREFIX . 'attachment_lower" type="text" size="5" value=""></td>' . "\n";
		echo "\t\t" . '      </tr>' . "\n";
		
		echo "\t\t" . '      <tr valign="top">' . "\n";
		echo "\t\t" . '        <td width="24%" style="text-align: right; padding-right: 5px" >Last Attachment ID</td>' . "\n";
		echo "\t\t" . '        <td style="text-align: left;"><input name="' . self::SLUG_PREFIX . 'attachment_upper" type="text" size="5" value=""></td>' . "\n";
		echo "\t\t" . '      </tr>' . "\n";
		
		echo "\t\t" . '      <tr valign="top">' . "\n";
		echo "\t\t" . '        <td width="24%" style="text-align: right; padding-right: 5px" >Attachment Limit</td>' . "\n";
		echo "\t\t" . '        <td style="text-align: left;"><input name="' . self::SLUG_PREFIX . 'attachment_limit" type="text" size="5" value=""></td>' . "\n";
		echo "\t\t" . '      </tr>' . "\n";
		
		echo "\t\t" . '    <table>' . "\n";
		
		foreach ( $setting_actions as $label => $action ) {
			if ( empty( $action['handler'] ) ) {
				echo "\t\t" . '      <tr><td colspan=2 style="padding: 2px 0px;">' . $action['comment'] . "</td></tr>\n";
			} else {
				echo "\t\t" . '      <tr><td width="150px">' . "\n";
				echo "\t\t" . '        <input name="' . self::SLUG_PREFIX . 'action" type="submit" class="button-primary" style="width: 140px;" value="' . $label . '" />&nbsp;&nbsp;' . "\n";
				echo "\t\t" . '      </td><td>' . "\n";
				echo "\t\t" . '        ' . $action['comment'] . "\n";
				echo "\t\t" . '      </td></tr>' . "\n";
			}
		}
			
		echo "\t\t" . '    </table>' . "\n";
		echo "\t\t" . '  </p>' . "\n";
		echo "\t\t" . '</form>' . "\n";
		echo "\t\t" . '</div>' . "\n";
		echo "\t\t" . '</div><!-- wrap -->' . "\n";
	}

	/**
	 * Compile array of attachment ID values for the operation
 	 *
	 * @since 1.00
	 *
	 * @return	array	
	 */
	private static function _get_attachment_ids() {
		global $wpdb;

		$range_clause = '';
				
		if ( ! empty( $_REQUEST[ self::SLUG_PREFIX . 'attachment_lower' ] ) ) {
			$lower_bound = (integer) $_REQUEST[ self::SLUG_PREFIX . 'attachment_lower' ];
		} else {
			$lower_bound = 0;
		}

		if ( ! empty( $_REQUEST[ self::SLUG_PREFIX . 'attachment_upper' ] ) ) {
			$upper_bound = (integer) $_REQUEST[ self::SLUG_PREFIX . 'attachment_upper' ];
			$range_clause = '( ID >= ' . $lower_bound . ' ) AND ( ID <= ' . $upper_bound . ' ) AND ';
		} elseif ( $lower_bound ) {
			$range_clause = '( ID = ' . $lower_bound . ' ) AND ';
		}

		if ( ! empty( $_REQUEST[ self::SLUG_PREFIX . 'attachment_limit' ] ) ) {
			$limit_clause = 'LIMIT ' . (integer) $_REQUEST[ self::SLUG_PREFIX . 'attachment_limit' ];
		} else {
			$limit_clause = '';
		}

		$query = sprintf( 'SELECT ID FROM %1$s WHERE %2$s( post_type = \'attachment\' ) AND ( post_status = \'inherit\' ) AND ( post_parent = 0 ) ORDER BY ID %3$s', $wpdb->posts, $range_clause, $limit_clause );
error_log( __LINE__ . ' Unattached_Fixit::_get_attachment_ids() $query = ' . var_export( $query, true ), 0 );
		$results = $wpdb->get_col( $query );
error_log( __LINE__ . ' Unattached_Fixit::_get_attachment_ids() $results = ' . var_export( $results, true ), 0 );

		return $results;
	} // _get_attachment_ids

	/**
	 * Move unattached items to "media trash"
	 * @since 1.00
	 *
	 * @return	string	HTML markup for results/messages
	 */
	private static function _trash_unattached_items() {
		/*
		 * Compile array of attachment ID values
		 */
		$attachment_ids = self::_get_attachment_ids();
		
		// Initialize statistics
		$attachment_count = count( $attachment_ids );
		$updates = 0;
		$errors = 0;
		
		foreach ( $attachment_ids as $attachment_id ) {
			if ( wp_trash_post( $attachment_id ) ) {
				$updates++;
			} else {
				$errors++;
			}
		} // foreach attachment
		
		return "<br>Trash Unattached matched {$attachment_count} attachments and made {$updates} update(s). There were {$errors} error(s).\n";
	} // _trash_unattached_items

	/**
	 * Permanently delete unattached items
	 * @since 1.00
	 *
	 * @return	string	HTML markup for results/messages
	 */
	private static function _delete_unattached_items() {
		/*
		 * Compile array of attachment ID values
		 */
		$attachment_ids = self::_get_attachment_ids();
		
		// Initialize statistics
		$attachment_count = count( $attachment_ids );
		$updates = 0;
		$errors = 0;
		
		foreach ( $attachment_ids as $attachment_id ) {
			if ( wp_delete_attachment( $attachment_id, true ) ) {
				$updates++;
			} else {
				$errors++;
			}
		} // foreach attachment
		
		return "<br>Delete Unattached matched {$attachment_count} attachments and made {$updates} update(s). There were {$errors} error(s).\n";
	} // _delete_unattached_items
} //Unattached_Fixit

/*
 * Install the submenu at an early opportunity
 */
add_action('init', 'Unattached_Fixit::initialize');
?>
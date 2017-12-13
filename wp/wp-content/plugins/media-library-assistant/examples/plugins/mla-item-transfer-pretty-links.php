<?php
/**
 * Converts "Transfer by Item Name" links to pretty links, adds URL rewrite rule to convert them back.
 *
 * In this example a Settings submenu page can be used to define values for the elements of pretty links
 * used to replace the default admin-ajax.php links in [mla_gallery] shortcodes.
 *
 * Created for support topic "How about [mla_gallery list=mask]?"
 * opened on 8/19/2017 by "lwcorp".
 * https://wordpress.org/support/topic/how-about-mla_gallery-listmask/
 *
 * @package MLA Item Transfer Pretty Links
 * @version 1.00
 */

/*
Plugin Name: MLA Item Transfer Pretty Links
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Converts "Transfer by Item Name" links to pretty links, adds URL rewrite rule to convert them back.
Author: David Lingren
Version: 1.00

Author URI: http://fairtradejudaica.org/our-story/staff/

Copyright 2014 - 2017 David Lingren

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
 * Class MLA Item Transfer Pretty Links hooks one of the filters provided by the IPTC/EXIF and Custom Field mapping features
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding enerything
 * else inside a class means this is the only name you have to worry about.
 *
 * @package MLA Item Transfer Pretty Links
 * @since 1.00
 */
class MLAItemTransferPrettyLinks {
	/**
	 * Current version number
	 *
	 * @since 1.00
	 *
	 * @var	string
	 */
	const CURRENT_VERSION = '1.00';

	/**
	 * Slug prefix for registering and enqueueing submenu pages, style sheets and scripts
	 *
	 * @since 1.00
	 *
	 * @var	string
	 */
	const SLUG_PREFIX = 'mlaprettylinks-';

	/**
	 * Initialization function, similar to __construct()
	 *
	 * Installs filters and actions that handle the MLA hooks for uploading and mapping.
	 *
	 * @since 1.00
	 */
	public static function initialize() {
		self::_load_settings();
		
		// Don't addd the old rules if they are about to change
		if ( !( isset( $_REQUEST[ self::SLUG_PREFIX . 'save-changes' ] ) || isset( $_REQUEST[ self::SLUG_PREFIX . 'delete-settings' ] ) ) ) {
			self::_add_rewrite_rules();
		}

		if ( is_admin() ) {
			// The Settings page is only useful in the admin section
			add_action( 'admin_menu', 'MLAItemTransferPrettyLinks::admin_menu' );
		} else {
			// Get ready to process [mla_gallery] shortodes
			add_filter( 'mla_gallery_arguments', 'MLAItemTransferPrettyLinks::mla_gallery_arguments', 10, 1 );
			add_filter( 'mla_gallery_item_values', 'MLAItemTransferPrettyLinks::mla_gallery_item_values', 10, 1 );
		}
	}

	/**
	 * Processing options
	 *
	 * This array specifies the components used in the pretty links and whether
	 * the mla_debug=log argument is appended to the links
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $settings = array ();

	/**
	 * Default processing options
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $default_settings = array (
						'option_active' => array( 'add_mla_debug' => false ),
						'transfer_label' => 'mla-transfer',
						'attachment_label' => 'download',
						'inline_label' => 'view',
					);

	/**
	 * Update the plugin options from the wp_options table or set defaults
	 *
	 * @since 1.00
	 */
	private static function _load_settings() {
		// Update the plugin options from the wp_options table or set defaults
		if ( empty( self::$settings ) ) {
			$settings = get_option( self::SLUG_PREFIX . 'settings' );
			if ( is_array( $settings ) ) {
				self::$settings = $settings;
			} else {
				self::$settings = self::$default_settings;
			}
		}
	}

	/**
	 * Add submenu page in the "Settings" section
	 *
	 * @since 1.00
	 */
	public static function admin_menu( ) {
		$current_page_hook = add_submenu_page( 'options-general.php', 'MLA Item Transfer Pretty Links', 'MLA pretty links', 'manage_options', self::SLUG_PREFIX . 'settings', 'MLAItemTransferPrettyLinks::add_submenu_page' );
		add_filter( 'plugin_action_links', 'MLAItemTransferPrettyLinks::plugin_action_links', 10, 2 );
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
	public static function plugin_action_links( $links, $file ) {
		if ( $file == 'mla-item-transfer-pretty-links.php' ) {
			$settings_link = sprintf( '<a href="%s">%s</a>', admin_url( 'options-general.php?page=' . self::SLUG_PREFIX . 'settings' ), 'Settings' );
			array_unshift( $links, $settings_link );
		}

		return $links;
	}

	/**
	 * Redirect pretty links to the appropriate AJAX admin page
	 *
	 * @since 1.00
	 */
	public static function template_redirect() {
		$pagename = get_query_var('pagename');
		if ( 'mla-named-transfer' === $pagename ) {
			$mla_item = get_query_var('mla_item');
			$mla_disposition = get_query_var('mla_disposition');
			$mla_debug = get_query_var('mla_debug');
			MLACore::mla_debug_add( __LINE__ . " MLAItemTransferPrettyLinks:template_redirect item = {$mla_item}, disposition = {$mla_disposition}, debug = <{$mla_debug}>", MLACore::MLA_DEBUG_CATEGORY_ANY );
			
			$args = array(
				'action' => 'mla_named_transfer',
				'mla_item' => $mla_item,
				'mla_disposition' => $mla_disposition,
			);
			
			if ( !empty( $mla_debug ) ) {
				$args['mla_debug'] = 'log';
			}
			
			wp_redirect( add_query_arg( $args, admin_url( 'admin-ajax.php' ) ), 302 );
			exit();
		}
	}

	/**
	 * Add custom query variables
	 *
	 * @param array $query_vars WordPress query variables
	 *
	 * @since 1.00
	 */
	public static function query_vars( $query_vars ) {
		$query_vars[] = 'mla_item';
		$query_vars[] = 'mla_disposition';
		$query_vars[] = 'mla_debug';
		
		return $query_vars;
	}

	/**
	 * Render (echo) the "MLA jhdean" submenu in the Settings section
	 *
	 * @since 1.00
	 *
	 * @return	void Echoes HTML markup for the submenu page
	 */
	public static function add_submenu_page() {
		MLACore::mla_debug_add( __LINE__ . " MLAItemTransferPrettyLinks:add_submenu_page() \$_REQUEST = " . var_export( $_REQUEST, true ), MLACore::MLA_DEBUG_CATEGORY_ANY );

		if ( !current_user_can( 'manage_options' ) ) {
			echo "MLA Item Transfer Pretty Links - Error</h2>\n";
			wp_die( 'You do not have permission to manage plugin settings.' );
		}
		
		echo '<div class="wrap">' . "\n";
		echo "\t\t" . '<h2>MLA Item Transfer Pretty Links v' . self::CURRENT_VERSION . '</h2>' . "\n";

		if ( !current_user_can( 'edit_pages' ) ) {
			echo "\t\t<br>ERROR: You are not allowed to edit Media Library items.\n";
			return;
		}

		$message = '';
		if ( isset( $_REQUEST[ self::SLUG_PREFIX . 'save-changes' ] ) ) {
			$message = self::_save_setting_changes();
		} elseif ( isset( $_REQUEST[ self::SLUG_PREFIX . 'delete-settings' ] ) ) {
			$message = self::_delete_settings();
		}
		
		if ( !empty( $message ) ) {
			$is_error = ( false !== strpos( $message, __( 'ERROR', 'media-library-assistant' ) ) );
			if ( $is_error ) {
				$messages_class = 'updated error';
			} else {
				$messages_class = 'updated notice is-dismissible';
			}
		
			echo "  <div class=\"{$messages_class}\" id=\"message\"><p>\n";
			echo '    ' . $message . "\n";
			echo "  </p>\n";

			if ( !$is_error ) {
				echo "  <button class=\"notice-dismiss\" type=\"button\"><span class=\"screen-reader-text\">Dismiss this notice.</span></button>\n";
			}

			echo "  </div>\n";
		}

		$add_mla_debug_checked = self::$settings['option_active']['add_mla_debug'] ? 'checked="checked" ' : '';
		$transfer_label = esc_html( self::$settings['transfer_label'] );
		$attachment_label = esc_html( self::$settings['attachment_label'] );
		$inline_label = esc_html( self::$settings['inline_label'] );
		
		echo "\t\t" . '<div style="width:700px">' . "\n";
		echo "\t\t" . '<form action="' . admin_url( 'options-general.php?page=' . self::SLUG_PREFIX . 'settings' ) . '" method="post" class="' . self::SLUG_PREFIX . 'settings-form-class" id="' . self::SLUG_PREFIX . 'settings-form-id">' . "\n";
		echo "\t\t" . '  <p class="submit" style="padding-bottom: 0;">' . "\n";
		echo "\t\t" . '    <table width=99%>' . "\n";

		echo "\t\t" . '      <tr><td colspan=2>Enter the "pretty link" elements for your site, then click Save Settings.</td></tr>' . "\n";
		echo "\t\t" . '      <tr><td colspan=2>Make sure the labels do not conflict with other WordPress elements, e.g., Custom Post Types.</td></tr>' . "\n";
		echo "\t\t" . '      <tr><td colspan=2>&nbsp;</td></tr>' . "\n";

		echo "\t\t" . '      <tr>' . "\n";
		echo "\t\t" . '        <td width="150px" valign="middle" style="text-align: right; padding-right: 5px" >Item Transfer Label:</td>' . "\n";
		echo "\t\t" . '        <td style="text-align: left;"><input name="' . self::SLUG_PREFIX . 'transfer_label" id="' . self::SLUG_PREFIX . 'transfer_label" type="text" size="40" value="' . $transfer_label . '"></td>' . "\n";
		echo "\t\t" . '      </tr>' . "\n";
		
		echo "\t\t" . '      <tr><td>&nbsp;</td><td>The permalink element denoting an MLA Item Transfer operation.</td></tr>' . "\n";
		echo "\t\t" . '      <tr><td>&nbsp;</td><td>Pick something that won&rsquo;t conflict, e.g., add a unique prefix such as "mla-".</td></tr>' . "\n";

		echo "\t\t" . '      <tr><td colspan=2>&nbsp;</td></tr>' . "\n";

		echo "\t\t" . '      <tr>' . "\n";
		echo "\t\t" . '        <td width="150px" valign="middle" style="text-align: right; padding-right: 5px" >Force Download Label:</td>' . "\n";
		echo "\t\t" . '        <td style="text-align: left;"><input name="' . self::SLUG_PREFIX . 'attachment_label" id="' . self::SLUG_PREFIX . 'attachment_label" type="text" size="40" value="' . $attachment_label . '"></td>' . "\n";
		echo "\t\t" . '      </tr>' . "\n";
		
		echo "\t\t" . '      <tr><td>&nbsp;</td><td>The permalink element denoting a forced download operation.</td></tr>' . "\n";
		echo "\t\t" . '      <tr><td>&nbsp;</td><td>Selected when link=download appears in the shortcode.</td></tr>' . "\n";

		echo "\t\t" . '      <tr><td colspan=2>&nbsp;</td></tr>' . "\n";

		echo "\t\t" . '      <tr>' . "\n";
		echo "\t\t" . '        <td width="150px" valign="middle" style="text-align: right; padding-right: 5px" >View in Browser Label:</td>' . "\n";
		echo "\t\t" . '        <td style="text-align: left;"><input name="' . self::SLUG_PREFIX . 'inline_label" id="' . self::SLUG_PREFIX . 'inline_label" type="text" size="40" value="' . $inline_label . '"></td>' . "\n";
		echo "\t\t" . '      </tr>' . "\n";
		
		echo "\t\t" . '      <tr><td>&nbsp;</td><td>The permalink element denoting a view in browser operation.</td></tr>' . "\n";
		echo "\t\t" . '      <tr><td>&nbsp;</td><td>Selected when link=file appears in the shortcode.</td></tr>' . "\n";

		echo "\t\t" . '      <tr><td colspan=2>&nbsp;</td></tr>' . "\n";

		echo "\t\t" . '      <tr>' . "\n";
		echo "\t\t" . '        <td width="150px" style="text-align: right; padding-right: 5px" ><input name="' . self::SLUG_PREFIX . 'option_active[]" id="' . self::SLUG_PREFIX . 'option_active_add_mla_debug" type="checkbox" ' . $add_mla_debug_checked . 'value="add_mla_debug">
</td>' . "\n";
		echo "\t\t" . '        <td valign="middle" style="text-align: left;">Add mla_debug=log to the generated links.</td>' . "\n";
		echo "\t\t" . '      </tr>' . "\n";
		
		echo "\t\t" . '      <tr><td colspan=2>&nbsp;</td></tr>' . "\n";

		echo "\t\t" . '      <tr><td width="150px">' . "\n";
		echo "\t\t" . '        <input name="' . self::SLUG_PREFIX . 'save-changes" type="submit" class="button-primary" style="width: 120px;" value="Save Settings" />' . "\n";
		echo "\t\t" . '      </td><td>' . "\n";
		echo "\t\t" . '        <input name="' . self::SLUG_PREFIX . 'delete-settings" type="submit" class="button-primary" style="width: 120px;" value="Delete Settings" />' . "\n";
		echo "\t\t" . '      </td></tr>' . "\n";
			
		echo "\t\t" . '    </table>' . "\n";
		echo "\t\t" . '  </p>' . "\n";
		echo "\t\t" . '</form>' . "\n";
		echo "\t\t" . '</div>' . "\n";
		echo "\t\t" . '</div><!-- wrap -->' . "\n";
	}

	/**
	 * Save settings as a WordPress wp_options entry
	 *
	 * @since 1.00
	 *
	 * @return	string	HTML markup for results/messages
	 */
	private static function _save_setting_changes() {
		$new_settings = self::$settings;
		
		$option_active = isset( $_REQUEST[ self::SLUG_PREFIX . 'option_active' ] ) ? $_REQUEST[ self::SLUG_PREFIX . 'option_active' ] : array();
		
		$new_settings['option_active']['add_mla_debug'] = in_array( 'add_mla_debug', $option_active );
		$new_settings['transfer_label'] = stripslashes( $_REQUEST[ self::SLUG_PREFIX . 'transfer_label' ] );
		$new_settings['attachment_label'] = stripslashes( $_REQUEST[ self::SLUG_PREFIX . 'attachment_label' ] );
		$new_settings['inline_label'] = stripslashes( $_REQUEST[ self::SLUG_PREFIX . 'inline_label' ] );
		
		if ( $new_settings === self::$settings ) {
			self::_add_rewrite_rules();
			return "Settings unchanged.\n";
		}

		$success = update_option( self::SLUG_PREFIX . 'settings', $new_settings, false );
		if ( $success )  {
			self::$settings = $new_settings;

			self::_add_rewrite_rules( true );
			
			return "Settings and rewrite rules have been updated.\n";
		}

		return "Settings update failed.\n";
	} // _save_setting_changes

	/**
	 * Delete WordPress wp_options entry
	 *
	 * @since 1.00
	 *
	 * @return	string	HTML markup for results/messages
	 */
	private static function _delete_settings() {
		delete_option( self::SLUG_PREFIX . 'settings' ); 
		self::$settings = self::$default_settings;
		self::_add_rewrite_rules( true );

		return "Settings removed from database and reset to default values.\n";
	} // _delete_settings
		
	/**
	 * Register rewrite rules defined by current settings
	 *
	 * @since 1.00
	 *
	 * @param boolean $flush True to flush rewrite rules to the database. Optional; default false.
	 */
	private static function _add_rewrite_rules( $flush = false ) {
		$add_mla_debug = self::$settings['option_active']['add_mla_debug'] ? '&mla_debug=log' : '';
		
		// Add the "force download" rule
		$regex = '^' . self::$settings['transfer_label'] . '/' . self::$settings['attachment_label'] . '/([^/]+)(/[0-9]+)?/?$';
		$redirect = 'index.php?pagename=mla-named-transfer&mla_item=$matches[1]&mla_disposition=attachment' . $add_mla_debug;
		add_rewrite_rule( $regex, $redirect, 'top' );

		// Add the "view in browser" rule
		$regex = '^' . self::$settings['transfer_label'] . '/' . self::$settings['inline_label'] . '/([^/]+)(/[0-9]+)?/?$';
		$redirect = 'index.php?pagename=mla-named-transfer&mla_item=$matches[1]&mla_disposition=inline' . $add_mla_debug;
		add_rewrite_rule( $regex, $redirect, 'top' );

		if ( $flush ) {
			flush_rewrite_rules();
		}
	} // _add_rewrite_rules

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
	 * used for the gallery display.  The computed values are in the $style_values, $markup_values and
	 * $item_values arrays passed to later filters below.
	 *
	 * @since 1.00
	 *
	 * @param	array	$all_display_parameters shortcode arguments merged with gallery display defaults, so every possible parameter is present
	 */
	public static function mla_gallery_arguments( $all_display_parameters ) {
		MLACore::mla_debug_add( __LINE__ . " MLAItemTransferPrettyLinks::mla_gallery_arguments link = " . var_export( $all_display_parameters['link'], true ), MLACore::MLA_DEBUG_CATEGORY_ANY );

		self::$all_display_parameters = $all_display_parameters;
		return $all_display_parameters;
	} // mla_gallery_arguments

	/**
	 * Replace MLA Named Item Transfer links with pretty links
	 *
	 * @since 1.00
	 *
	 * @param	array	$item_values parameter_name => parameter_value pairs
	 */
	public static function mla_gallery_item_values( $item_values ) {
		
		// We only care about MLA Named Item Transfer links
		if ( 'true' !== self::$all_display_parameters['mla_named_transfer'] ) {
			return $item_values;
		}
		
		// Create pretty link with all Content Parameters
		$match_count = preg_match( '#href=\'([^\']+)\'#', $item_values['filelink'], $matches, PREG_OFFSET_CAPTURE );
		if ( ! ( ( $match_count == false ) || ( $match_count == 0 ) ) ) {
			$url = $item_values['site_url'] . '/' . self::$settings['transfer_label'] . '/';

			if ( 'download' === self::$all_display_parameters['link'] ) {
				$url .= self::$settings['attachment_label'];
			} else {
				$url .= self::$settings['inline_label'];
			}

			$url .= '/' . $item_values['slug'];
			
			$item_values['link_url'] = $url;
			$item_values['link'] = preg_replace( '#' . $matches[0][0] . '#', sprintf( 'href=\'%1$s\'', $url ), $item_values['filelink'] );
		}
		
		MLACore::mla_debug_add( __LINE__ . " MLAItemTransferPrettyLinks::mla_gallery_item_values link_url = " . var_export( $item_values['link_url'], true ), MLACore::MLA_DEBUG_CATEGORY_ANY );

		return $item_values;
	} // mla_gallery_item_values

	/**
	 * Perform initial rewrite registration and flush on plugin activation
	 *
	 * @since 1.00
	 */
	public static function activation_hook( ) {
		self::_load_settings();
		self::_add_rewrite_rules( true );
	}

	/**
	 * Perform final rewrite removal and flush on plugin deactivation
	 *
	 * @since 1.00
	 */
	public static function deactivation_hook( ) {
		flush_rewrite_rules();
	}
} //MLAItemTransferPrettyLinks

// Install the filters at an early opportunity
add_action('init', 'MLAItemTransferPrettyLinks::initialize');

//add plugin query vars to WordPress
add_filter('query_vars', 'MLAItemTransferPrettyLinks::query_vars' );//register plugin custom pages display

//register plugin redirection logic
add_action( 'template_redirect', 'MLAItemTransferPrettyLinks::template_redirect' );
 
// Register hooks that are fired when the plugin is activated or deactivated.
register_activation_hook( __FILE__, array( 'MLAItemTransferPrettyLinks', 'activation_hook' ) );
register_deactivation_hook( __FILE__, array( 'MLAItemTransferPrettyLinks', 'deactivation_hook' ) );
?>
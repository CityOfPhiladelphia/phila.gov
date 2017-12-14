<?php
/**
 * Provides an example of the filters provided by the IPTC/EXIF and Custom Field mapping features
 *
 * In this example any combination of three operations can be selected with options
 * on a Settings submenu page:
 *
 *  - Remove a hyperlink from the Title, leaving a plain text value
 *  - Replace the Title by a hyperlink using the IPTC 2#005 Object Name value
 *  - Replace the Justified Image Grid "JIG Link" value with a link to the
 *    appropriate portfolio destination
 *
 * Created for support topic "EXIF/Template Value editing"
 * opened on 2/24/2015 by "jhdean".
 * https://wordpress.org/support/topic/exiftemplate-value-editing
 *
 * @package MLA jhdean Mapping Hooks Example
 * @version 1.04
 */

/*
Plugin Name: MLA jhdean Mapping Hooks Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Add or remove Title hyperlink, replace JIG Link; for Jeff Dean.
Author: David Lingren
Version: 1.04
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
 * Class MLA jhdean Mapping Hooks Example hooks one of the filters provided by the IPTC/EXIF and Custom Field mapping features
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding enerything
 * else inside a class means this is the only name you have to worry about.
 *
 * @package MLA jhdean Mapping Hooks Example
 * @since 1.00
 */
class MLAjhdeanMappingHooksExample {
	/**
	 * Current version number
	 *
	 * @since 1.03
	 *
	 * @var	string
	 */
	const CURRENT_VERSION = '1.04';

	/**
	 * Slug prefix for registering and enqueueing submenu pages, style sheets and scripts
	 *
	 * @since 1.03
	 *
	 * @var	string
	 */
	const SLUG_PREFIX = 'mlajhdean-';

	/**
	 * Initialization function, similar to __construct()
	 *
	 * Installs filters and actions that handle the MLA hooks for uploading and mapping.
	 *
	 * @since 1.00
	 */
	public static function initialize() {
		// The plugin is only useful in the admin section; exit if in the "front-end".
		if ( ! is_admin() )
			return;

		add_action( 'admin_menu', 'MLAjhdeanMappingHooksExample::admin_menu' );
		add_filter( 'mla_mapping_updates', 'MLAjhdeanMappingHooksExample::mla_mapping_updates_filter', 10, 5 );
		
		// Update the plugin options from the wp_options table or set defaults
		$settings = get_option( self::SLUG_PREFIX . 'settings' );
		if ( is_array( $settings ) ) {
			self::$settings = $settings;
		} else {
			self::$settings = self::$default_settings;
		}
	}

	/**
	 * Processing options
	 *
	 * This array specifies which of the three operations are active and the
	 * value of the Title hyperlink. Initialized in the initialize() function.
	 *
	 * Title hyperlink substitution parameters are:
	 *  - %1$s => IPTC Object Name in hyperlink-compatible format; no spaces or underscores, just dashes
	 *  - %2$s => IPTC Object Name in plain text format
	 *
	 * @since 1.03
	 *
	 * @var	array
	 */
	private static $settings = array ();

	/**
	 * Default processing options
	 *
	 * @since 1.03
	 *
	 * @var	array
	 */
	private static $default_settings = array (
						'option_active' => array( 'replace_jig_link' => false, 'remove_link' => false, 'add_link' => false ),
						'jig_hyperlink_href' => 'http://www.jeffreyhdean.dev/portfolio/%1$s/',
						'title_hyperlink_tag' => '<a id="detail-title" target="_blank" href="http://www.jhdstaging.org/portfolio/%1$s/">%2$s</a>',
					);

	/**
	 * Add submenu page in the "Settings" section
	 *
	 * @since 1.03
	 */
	public static function admin_menu( ) {
		$current_page_hook = add_submenu_page( 'options-general.php', 'MLA jhdean Mapping Hooks Example', 'MLA jhdean', 'manage_options', self::SLUG_PREFIX . 'settings', 'MLAjhdeanMappingHooksExample::add_submenu_page' );
		add_filter( 'plugin_action_links', 'MLAjhdeanMappingHooksExample::plugin_action_links', 10, 2 );
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
		if ( $file == 'mla-jhdean-mapping-hooks-example.php' ) {
			$settings_link = sprintf( '<a href="%s">%s</a>', admin_url( 'options-general.php?page=' . self::SLUG_PREFIX . 'settings' ), 'Settings' );
			array_unshift( $links, $settings_link );
		}

		return $links;
	}

	/**
	 * Render (echo) the "MLA jhdean" submenu in the Settings section
	 *
	 * @since 1.03
	 *
	 * @return	void Echoes HTML markup for the submenu page
	 */
	public static function add_submenu_page() {
		MLACore::mla_debug_add( __LINE__ . " MLAjhdeanMappingHooksExample:add_submenu_page() \$_REQUEST = " . var_export( $_REQUEST, true ), MLACore::MLA_DEBUG_CATEGORY_ANY );

		if ( !current_user_can( 'manage_options' ) ) {
			echo "MLA jhdean Mapping Hooks Example - Error</h2>\n";
			wp_die( 'You do not have permission to manage plugin settings.' );
		}
		
		echo '<div class="wrap">' . "\n";
		echo "\t\t" . '<div id="icon-tools" class="icon32"><br/></div>' . "\n";
		echo "\t\t" . '<h2>MLA jhdean Mapping Hooks Example v' . self::CURRENT_VERSION . '</h2>' . "\n";

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

		$no_change_checked = $remove_link_checked = $add_link_checked = '';
		if ( self::$settings['option_active']['remove_link'] ) {
			$remove_link_checked = 'checked="checked" ';
		} elseif ( self::$settings['option_active']['add_link'] ) {
			$add_link_checked = 'checked="checked" ';
		} else {
			$no_change_checked = 'checked="checked" ';
		}
		
		$replace_jig_link_checked = self::$settings['option_active']['replace_jig_link'] ? 'checked="checked" ' : '';
		$jig_hyperlink_href = esc_html( self::$settings['jig_hyperlink_href'] );
		$title_hyperlink_tag = esc_html( self::$settings['title_hyperlink_tag'] );
		
		echo "\t\t" . '<div style="width:700px">' . "\n";
		echo "\t\t" . '<form action="' . admin_url( 'options-general.php?page=' . self::SLUG_PREFIX . 'settings' ) . '" method="post" class="' . self::SLUG_PREFIX . 'settings-form-class" id="' . self::SLUG_PREFIX . 'settings-form-id">' . "\n";
		echo "\t\t" . '  <p class="submit" style="padding-bottom: 0;">' . "\n";
		echo "\t\t" . '    <table width=99%>' . "\n";

		echo "\t\t" . '      <tr><td colspan=2>Check the operation(s) you want to perform, then click Save Settings.</td></tr>' . "\n";
		echo "\t\t" . '      <tr><td colspan=2>Operation(s) will be performed when IPTX/EXIF mapping is done.</td></tr>' . "\n";
		echo "\t\t" . '      <tr><td colspan=2>&nbsp;</td></tr>' . "\n";

		echo "\t\t" . '      <tr>' . "\n";
		echo "\t\t" . '        <td width="150px" style="text-align: right; padding-right: 5px" ><input name="' . self::SLUG_PREFIX . 'option_active[]" id="' . self::SLUG_PREFIX . 'option_active_replace_jig_link" type="checkbox" ' . $replace_jig_link_checked . 'value="replace_jig_link">
</td>' . "\n";
		echo "\t\t" . '        <td valign="middle" style="text-align: left;">Replace JIG Link value</td>' . "\n";
		echo "\t\t" . '      </tr>' . "\n";
		
		echo "\t\t" . '      <tr>' . "\n";
		echo "\t\t" . '        <td width="150px" valign="middle" style="text-align: right; padding-right: 5px" >Destination:</td>' . "\n";
		echo "\t\t" . '        <td style="text-align: left;"><input name="' . self::SLUG_PREFIX . 'jig_hyperlink_href" id="' . self::SLUG_PREFIX . 'jig_hyperlink_href" type="text" size="80" value="' . $jig_hyperlink_href . '"></td>' . "\n";
		echo "\t\t" . '      </tr>' . "\n";
		
		echo "\t\t" . '      <tr><td>&nbsp;</td><td>JIG link substitution parameter is:</td></tr>' . "\n";
		echo "\t\t" . '      <tr><td>&nbsp;</td><td>- %1$s => IPTC Object Name in hyperlink-compatible format; no spaces or underscores, just dashes</td></tr>' . "\n";

		echo "\t\t" . '      <tr><td colspan=2>&nbsp;</td></tr>' . "\n";

		echo "\t\t" . '      <tr>' . "\n";
		echo "\t\t" . '        <td width="150px" style="text-align: right; padding-right: 5px" ><input name="' . self::SLUG_PREFIX . 'title_link" id="' . self::SLUG_PREFIX . 'title_link_no_change" type="radio" ' . $no_change_checked . 'value="no_change">
</td>' . "\n";
		echo "\t\t" . '        <td valign="middle" style="text-align: left;">No change to Title</td>' . "\n";
		echo "\t\t" . '      </tr>' . "\n";
		
		echo "\t\t" . '      <tr>' . "\n";
		echo "\t\t" . '        <td width="150px" style="text-align: right; padding-right: 5px" ><input name="' . self::SLUG_PREFIX . 'title_link" id="' . self::SLUG_PREFIX . 'title_link_remove_link" type="radio" ' . $remove_link_checked . 'value="remove_link">
</td>' . "\n";
		echo "\t\t" . '        <td valign="middle" style="text-align: left;">Remove hyperlink from Title</td>' . "\n";
		echo "\t\t" . '      </tr>' . "\n";
		
		echo "\t\t" . '      <tr>' . "\n";
		echo "\t\t" . '        <td width="150px" style="text-align: right; padding-right: 5px" ><input name="' . self::SLUG_PREFIX . 'title_link" id="' . self::SLUG_PREFIX . 'title_link_add_link" type="radio" ' . $add_link_checked . 'value="add_link">
</td>' . "\n";
		echo "\t\t" . '        <td valign="middle" style="text-align: left;">Add hyperlink to Title</td>' . "\n";
		echo "\t\t" . '      </tr>' . "\n";
		
		echo "\t\t" . '      <tr>' . "\n";
		echo "\t\t" . '        <td width="150px" valign="middle" style="text-align: right; padding-right: 5px" >Hyperlink:</td>' . "\n";
		echo "\t\t" . '        <td style="text-align: left;"><input name="' . self::SLUG_PREFIX . 'title_hyperlink_tag" id="' . self::SLUG_PREFIX . 'title_hyperlink_tag" type="text" size="80" value="' . $title_hyperlink_tag . '"></td>' . "\n";
		echo "\t\t" . '      </tr>' . "\n";
		
		echo "\t\t" . '      <tr><td>&nbsp;</td><td>Title hyperlink substitution parameters are:</td></tr>' . "\n";
		echo "\t\t" . '      <tr><td>&nbsp;</td><td>- %1$s => IPTC Object Name in hyperlink-compatible format; no spaces or underscores, just dashes</td></tr>' . "\n";
		echo "\t\t" . '      <tr><td>&nbsp;</td><td>- %2$s => IPTC Object Name in plain text format</td></tr>' . "\n";

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
	 * @since 1.03
	 *
	 * @return	string	HTML markup for results/messages
	 */
	private static function _save_setting_changes() {
		$new_settings = self::$settings;
		
		$option_active = isset( $_REQUEST[ self::SLUG_PREFIX . 'option_active' ] ) ? $_REQUEST[ self::SLUG_PREFIX . 'option_active' ] : array();
		
		$new_settings['option_active']['remove_link'] = 'remove_link' === $_REQUEST[ self::SLUG_PREFIX . 'title_link' ];
		$new_settings['option_active']['add_link'] = 'add_link' === $_REQUEST[ self::SLUG_PREFIX . 'title_link' ];
		$new_settings['option_active']['replace_jig_link'] = in_array( 'replace_jig_link', $option_active );
		$new_settings['jig_hyperlink_href'] = stripslashes( $_REQUEST[ self::SLUG_PREFIX . 'jig_hyperlink_href' ] );
		$new_settings['title_hyperlink_tag'] = stripslashes( $_REQUEST[ self::SLUG_PREFIX . 'title_hyperlink_tag' ] );
		
		if ( $new_settings === self::$settings ) {
			return "Settings unchanged.\n";
		}

		$success = update_option( self::SLUG_PREFIX . 'settings', $new_settings, false );
		if ( $success )  {
			self::$settings = $new_settings;
			return "Settings have been updated.\n";
		}

		return "Settings update failed.\n";
	} // _save_setting_changes

	/**
	 * Delete WordPress wp_options entry
	 *
	 * @since 1.04
	 *
	 * @return	string	HTML markup for results/messages
	 */
	private static function _delete_settings() {
		delete_option( self::SLUG_PREFIX . 'settings' ); 
		self::$settings = self::$default_settings;
		return "Settings removed from database and reset to default values.\n";
	} // _delete_settings
		
	/**
	 * Apply processing options to an attachment
	 *
	 * This filter is called AFTER all mapping rules are applied.
	 * You can add, change or remove updates for the attachment's
	 * standard fields, taxonomies and/or custom fields.
	 *
	 * @since 1.00
	 *
	 * @param array	$updates Updates for the attachment's standard fields,
	 *                       taxonomies and/or custom fields
	 * @param integer $post_id Attachment ID to be evaluated
	 * @param string $category Category/scope to evaluate against:
	 *                         custom_field_mapping or single_attachment_mapping
	 * @param array $settings Mapping rules
	 * @param array $attachment_metadata Attachment metadata, default NULL
	 */
	public static function mla_mapping_updates_filter( $updates, $post_id, $category, $settings, $attachment_metadata ) {
		MLACore::mla_debug_add( __LINE__ . " MLAjhdeanMappingHooksExample::mla_mapping_updates_filter( {$post_id}, {$category} ) updates = " . var_export( $updates, true ), MLACore::MLA_DEBUG_CATEGORY_ANY );

		// We are only concerned with Standard Field mapping
		if ( ! in_array( $category, array( 'iptc_exif_mapping', 'iptc_exif_standard_mapping' ) ) ) {
			return $updates;
		}
		
		if ( self::$settings['option_active']['remove_link'] || self::$settings['option_active']['add_link'] ) {
			/*
			 * If $updates[ 'post_title' ] is set, some mapping rule has been set up,
			 * so we respect the result. If not, use whatever the current Title value is.
			 */
			if ( isset( $updates[ 'post_title' ] ) ) {
				$old_title = $updates[ 'post_title' ];
			} else {
				$post = get_post( $post_id );
				$old_title = $post->post_title;
			}
			MLACore::mla_debug_add( __LINE__ . " MLAjhdeanMappingHooksExample::mla_mapping_updates_filter( {$post_id}, {$category} ) old_title = " . var_export( $old_title, true ), MLACore::MLA_DEBUG_CATEGORY_ANY );
		} // add_link or remove_link
		
		// Remove a hyperlink from the Title, leaving a plain text value
		if ( self::$settings['option_active']['remove_link'] ) {
			// Find all the hyperlinks in the gallery
			$match_count = preg_match( '!(<a [^>]*?>)(.*)(</a>)!', $old_title, $matches, PREG_OFFSET_CAPTURE );
			if ( $match_count ) {
				// Replace the links with just the plain text
				$replacement = $matches[2][0];
				$start = $matches[0][1];
				$length = strlen( $matches[0][0] );
				$new_title = substr_replace( $old_title, $replacement, $start, $length );

				if ( $old_title != $new_title ) {
					$updates[ 'post_title' ] = $old_title = trim( $new_title );
				}
			} // found link
		} // remove_link
		
		// Replace the Title by a hyperlink using the IPTC 2#005 Object Name value
		if ( self::$settings['option_active']['add_link'] ) {
			/*
			 * Derive the new Title from the IPTC Object Name, if present.
			 * You can use MLAOptions::mla_get_data_source() to get anything available.
			 */
			$my_setting = array(
				'data_source' => 'template',
				'meta_name' => '([+iptc:2#005+])',
				'option' => 'raw'
			);
			$object_name = trim( MLAOptions::mla_get_data_source( $post_id, 'single_attachment_mapping', $my_setting, NULL ) );
			MLACore::mla_debug_add( __LINE__ . " MLAjhdeanMappingHooksExample::mla_mapping_updates_filter( {$post_id}, {$category} ) object_name = " . var_export( $object_name, true ), MLACore::MLA_DEBUG_CATEGORY_ANY );
			if ( ! empty( $object_name ) ) {
				//$object_link = strtolower( str_replace( array( ' ', '-', '_', '.' ), '-', $object_name . ',' . $object_name ) );
				$object_link = sanitize_title( $object_name );
	
				$new_title = sprintf( self::$settings['title_hyperlink_tag'], $object_link, $object_name );
				MLACore::mla_debug_add( __LINE__ . " MLAjhdeanMappingHooksExample::mla_mapping_updates_filter( {$post_id}, {$category} ) new_title = " . var_export( $new_title, true ), MLACore::MLA_DEBUG_CATEGORY_ANY );
				
				if ( $old_title != $new_title ) {
					$updates[ 'post_title' ] = $new_title;
				}
			}
		} // add_link

		/*
		 * Replace the Justified Image Grid "JIG Link" value with a link to the
		 * appropriate portfolio destination
		 */
		if ( self::$settings['option_active']['replace_jig_link'] ) {
			$old_meta_value = get_post_meta( $post_id, '_jig_image_link' );
//			MLACore::mla_debug_add( __LINE__ . " MLAjhdeanMappingHooksExampleJIG::mla_mapping_updates_filter( {$post_id}, {$category} ) RAW old_meta_value = " . var_export( $old_meta_value, true ), MLACore::MLA_DEBUG_CATEGORY_ANY );
			if ( !empty( $old_meta_value ) ) {
				if ( is_array( $old_meta_value ) ) {
					if ( count( $old_meta_value ) == 1 ) {
						$old_meta_value = maybe_unserialize( current( $old_meta_value ) );
					} else {
						foreach ( $old_meta_value as $single_key => $single_value ) {
							$old_meta_value[ $single_key ] = maybe_unserialize( $single_value );
						}
					}
				}
			} else {
				$old_meta_value = '';
			}
			MLACore::mla_debug_add( __LINE__ . " MLAjhdeanMappingHooksExampleJIG::mla_mapping_updates_filter( {$post_id}, {$category} ) old_meta_value = " . var_export( $old_meta_value, true ), MLACore::MLA_DEBUG_CATEGORY_ANY );
	
			/*
			 * Derive the new value from the IPTC Object Name, if present.
			 * You can use MLAOptions::mla_get_data_source() to get anything available.
			 */
			$my_setting = array(
				'data_source' => 'template',
				'meta_name' => '([+iptc:2#005+])',
				'option' => 'raw'
			);
			$object_name = trim( MLAOptions::mla_get_data_source( $post_id, 'single_attachment_mapping', $my_setting, NULL ) );
			MLACore::mla_debug_add( __LINE__ . " MLAjhdeanMappingHooksExampleJIG::mla_mapping_updates_filter( {$post_id}, {$category} ) object_name = " . var_export( $object_name, true ), MLACore::MLA_DEBUG_CATEGORY_ANY );
	
			if ( ! empty( $object_name ) ) {
				//$object_link = strtolower( str_replace( array( ' ', '-', '_', '.' ), '-', $object_name ) );
				$object_link = sanitize_title( $object_name );
				$new_meta_value = sprintf( self::$settings['jig_hyperlink_href'], $object_link );
				MLACore::mla_debug_add( __LINE__ . " MLAjhdeanMappingHooksExampleJIG::mla_mapping_updates_filter( {$post_id}, {$category} ) new_meta_value = " . var_export( $new_meta_value, true ), MLACore::MLA_DEBUG_CATEGORY_ANY );
				
				if ( $old_meta_value != $new_meta_value ) {
					$result = update_post_meta( $post_id, '_jig_image_link', $new_meta_value );
					MLACore::mla_debug_add( __LINE__ . " MLAjhdeanMappingHooksExampleJIG::mla_mapping_updates_filter( {$post_id}, {$category} ) update_post_meta result = " . var_export( $result, true ), MLACore::MLA_DEBUG_CATEGORY_ANY );
				}
			} // object_name present
		} // replace_jig_link
		
		MLACore::mla_debug_add( __LINE__ . " MLAjhdeanMappingHooksExample::mla_mapping_updates_filter( {$post_id}, {$category} ) new updates = " . var_export( $updates, true ), MLACore::MLA_DEBUG_CATEGORY_ANY );
		return $updates;
	} // mla_mapping_updates_filter
} //MLAjhdeanMappingHooksExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLAjhdeanMappingHooksExample::initialize');
?>
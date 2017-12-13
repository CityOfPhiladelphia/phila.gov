<?php
/**
 * Configures and processes custom RSS2 feeds for Media Library items
 *
 * In this example custom RSS feeds returning Media Library items can be defined by options
 * on a Settings/MLA Feed submenu page. For example:
 *
 * Parameters: post_parent=all posts_per_page=6 attachment_category="[+template:([+request:mla-term+])+]"
 * Taxonomies: attachment_category,attachment_tag
 *
 * With the above parameters, a category-specific feed would be:
 *     http://l.mladev/mlafeed/?mla-term=abc
 * or
 *     http://l.mladev/?feed=mlafeed&mla-term=abc
 *
 * Created for support topic "Create a feed out of the media library"
 * opened on 8/21/2017 by "lwcorp".
 * https://wordpress.org/support/topic/create-a-feed-out-of-the-media-library/
 *
 * @package MLA Custom Feed Example
 * @version 1.10
 */

/*
Plugin Name: MLA Custom Feed Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Configures and processes custom RSS2 feeds for Media Library items
Author: David Lingren
Version: 1.10
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
 * Class MLA Custom Feed Example adjusts the [mla_gallery] posts_per_page value based on
 * WordPress conditional functions
 *
 * @package MLA Custom Feed Example
 * @since 1.00
 */
class MLACustomFeedExample {
	/**
	 * Current version number
	 *
	 * @since 1.00
	 *
	 * @var	string
	 */
	const CURRENT_VERSION = '1.10';

	/**
	 * Slug prefix for registering and enqueueing submenu pages, style sheets, scripts and settings
	 *
	 * @since 1.00
	 *
	 * @var	string
	 */
	const SLUG_PREFIX = 'mlafeed';

	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 1.00
	 *
	 * @return	void
	 */
	public static function initialize() {
//error_log( __LINE__ . ' MLACustomFeedExample::initialize $_REQUEST = ' . var_export( $_REQUEST, true ), 0 );
		
		// Set the appropriate hooks depending on admin or front-end mode
		if ( is_admin() ) {
			add_action( 'admin_menu', 'MLACustomFeedExample::admin_menu' );
			add_filter( 'set-screen-option', 'MLACustomFeedExample::mla_set_screen_option', 10, 3 );
			add_filter( 'screen_options_show_screen', 'MLACustomFeedExample::mla_screen_options_show_screen', 10, 2 );
		} else {
			if ( MLA_Custom_Feed_Query::get_option('add_mlafeeds') ) {
				$slugs = MLA_Custom_Feed_Query::mla_custom_feed_slugs('active');
				foreach ( $slugs as $ID => $slug ) {
//error_log( __LINE__ . ' MLACustomFeedExample::initialize adding feed ' . $slug, 0 );
					add_feed( $slug, 'MLACustomFeedExample::mla_custom_feed' );
				}
			}
		}
	}

	/**
	 * Add submenu page in the "Settings" section
	 *
	 * @since 1.00
	 */
	public static function admin_menu( ) {
		/*
		 * We need a tab-specific page ID to manage the screen options on the General tab.
		 * Use the URL suffix, if present. If the URL doesn't have a tab suffix, use '-general'.
		 * This hack is required to pass the WordPress "referer" validation.
		 */
		 if ( isset( $_REQUEST['page'] ) && is_string( $_REQUEST['page'] ) && ( MLACustomFeedExample::SLUG_PREFIX . '-settings-' == substr( $_REQUEST['page'], 0, strlen( MLACustomFeedExample::SLUG_PREFIX . '-settings-' ) ) ) ) {
			$tab = substr( $_REQUEST['page'], strlen( MLACustomFeedExample::SLUG_PREFIX . '-settings-' ) );
		 } else {
			$tab = 'general';
		 }

		$tab = self::_get_options_tablist( $tab ) ? '-' . $tab : '-general';
		$current_page_hook = add_submenu_page( 'options-general.php', 'MLA Custom Feed Example', 'MLA Feed', 'manage_options', MLACustomFeedExample::SLUG_PREFIX . '-settings' . $tab, 'MLACustomFeedExample::add_submenu_page' );
		add_action( 'load-' . $current_page_hook, 'MLACustomFeedExample::mla_add_menu_options' );
		add_filter( 'plugin_action_links', 'MLACustomFeedExample::plugin_action_links', 10, 2 );
	}

	/**
	 * Only show screen options on the General tab
	 *
	 * @since 1.10
	 *
	 * @param boolean $show_screen True to display "Screen Options", false to suppress them
	 * @param WP_Screen $this_screen Current WP_Screen instance.
	 *
	 * @return	boolean	True to display "Screen Options", false to suppress them
	 */
	public static function mla_screen_options_show_screen( $show_screen, $this_screen ) {
		// Make sure this is the Settings/MLA Feed screen
		if ( false === strpos( $this_screen->base, MLACustomFeedExample::SLUG_PREFIX . '-settings' ) ) {
			return $show_screen;
		}
		
		return false !== strpos( $this_screen->base, MLACustomFeedExample::SLUG_PREFIX . '-settings-general' );
	}

	/**
	 * Add the "XX Entries per page" filter to the Screen Options tab
	 *
	 * @since 1.10
	 */
	public static function mla_add_menu_options( ) {
		$option = 'per_page';

		$args = array(
			'label' => 'Feeds per page',
			'default' => 10,
			'option' =>  MLACustomFeedExample::SLUG_PREFIX . '_feeds_per_page' 
		);

		add_screen_option( $option, $args );
	}

	/**
	 * Save the "Entries per page" option set by this user
	 *
	 * @since 1.10
	 *
	 * @param	mixed	false or value returned by previous filter
	 * @param	string	Name of the option being changed
	 * @param	string	New value of the option
	 *
	 * @return	mixed	New value if this is our option, otherwise original status
	 */
	public static function mla_set_screen_option( $status, $option, $value ) {
		if ( MLACustomFeedExample::SLUG_PREFIX . '_feeds_per_page' == $option ) {
			return $value;
		}

		return $status;
	}

	/**
	 * Add the "Settings" link to the Plugins section entry
	 *
	 * @since 1.00
	 *
	 * @param	array 	array of links for the Plugin, e.g., "Activate"
	 * @param	string 	Directory and name of the plugin Index file
	 *
	 * @return	array	Updated array of links for the Plugin
	 */
	public static function plugin_action_links( $links, $file ) {
		if ( $file == 'mla-custom-feed-example/mla-custom-feed-example.php' ) {
			$settings_link = sprintf( '<a href="%s">%s</a>', admin_url( 'options-general.php?page=' . MLACustomFeedExample::SLUG_PREFIX . '-settings-general' ), 'Settings' );
			array_unshift( $links, $settings_link );
		}

		return $links;
	}

	/**
	 * Render (echo) the "MLA Feed" submenu in the Settings section
	 *
	 * @since 1.00
	 *
	 * @return	void Echoes HTML markup for the submenu page
	 */
	public static function add_submenu_page() {
//error_log( __LINE__ . " MLACustomFeedExample:add_submenu_page _REQUEST = " . var_export( $_REQUEST, true ), 0 );

		if ( !current_user_can( 'manage_options' ) ) {
			echo "MLA Custom Feed Example - Error</h2>\n";
			wp_die( 'You do not have permission to manage plugin settings.' );
		}

		// Load template array and initialize page-level values.
		self::$page_template_array = MLACore::mla_load_template( dirname( __FILE__ ) . '/admin-settings-page.tpl', 'path' );
		$current_tab_slug = isset( $_REQUEST['mla_tab'] ) ? $_REQUEST['mla_tab']: 'general';
		$current_tab = self::_get_options_tablist( $current_tab_slug );
		$page_values = array(
			'version' => 'v' . self::CURRENT_VERSION,
			'messages' => '',
			'tablist' => self::_compose_settings_tabs( $current_tab_slug ),
			'tab_content' => '',
		);

		// Compose tab content
		if ( $current_tab ) {
			if ( isset( $current_tab['render'] ) ) {
				$handler = $current_tab['render'];
				$page_content = call_user_func( $handler );
			} else {
				$page_content = array( 'message' => 'ERROR: Cannot render content tab', 'body' => '' );
			}
		} else {
			$page_content = array( 'message' => 'ERROR: Unknown content tab', 'body' => '' );
		}

		if ( ! empty( $page_content['message'] ) ) {
			if ( false !== strpos( $page_content['message'], 'ERROR' ) ) {
				$messages_class = 'updated error';
				$dismiss_button = '';
			} else {
				$messages_class = 'updated notice is-dismissible';
				$dismiss_button = "  <button class=\"notice-dismiss\" type=\"button\"><span class=\"screen-reader-text\">[+dismiss_text+].</span></button>\n";
			}

			$page_values['messages'] = MLAData::mla_parse_template( self::$page_template_array['messages'], array(
				 'mla_messages_class' => $messages_class ,
				 'messages' => $page_content['message'],
				 'dismiss_button' => $dismiss_button,
				 'dismiss_text' => 'Dismiss this notice',
			) );
		}

		$page_values['tab_content'] = $page_content['body'];
		echo MLAData::mla_parse_template( self::$page_template_array['page'], $page_values );
	}

	/**
	 * Template file for the Settings page(s) and parts
	 *
	 * This array contains all of the template parts for the Settings page(s). The array is built once
	 * each page load and cached for subsequent use.
	 *
	 * @since 1.10
	 *
	 * @var	array
	 */
	public static $page_template_array = NULL;

	/**
	 * Definitions for Settings page tab ids, titles and handlers
	 * Each tab is defined by an array with the following elements:
	 *
	 * array key => HTML id/name attribute and option database key (OMIT MLA_OPTION_PREFIX)
	 *
	 * title => tab label / heading text
	 * render => rendering function for tab messages and content. Usage:
	 *     $tab_content = ['render']( );
	 *
	 * @since 1.10
	 *
	 * @var	array
	 */
	private static $mla_tablist = array(
		'general' => array( 'title' => 'General', 'render' => array( 'MLACustomFeedExample', '_compose_general_tab' ) ),
		'documentation' => array( 'title' => 'Documentation', 'render' => array( 'MLACustomFeedExample', '_compose_documentation_tab' ) ),
		);

	/**
	 * Retrieve the list of options tabs or a specific tab value
	 *
	 * @since 1.10
	 *
	 * @param	string	Tab slug, to retrieve a single entry
	 *
	 * @return	array|false	The entire tablist ( $tab = NULL ), a single tab entry or false if not found/not allowed
	 */
	private static function _get_options_tablist( $tab = NULL ) {
		if ( is_string( $tab ) ) {
			if ( isset( self::$mla_tablist[ $tab ] ) ) {
				$results = self::$mla_tablist[ $tab ];
			} else {
				$results = false;
			}
		} else {
			$results = self::$mla_tablist;
		}

		return $results;
	}

	/**
	 * Compose the navigation tabs for the Settings subpage
	 *
	 * @since 1.10
	 * @uses $page_template_array contains tablist and tablist-item templates
 	 *
	 * @param	string	Optional data-tab-id value for the active tab, default 'general'
	 *
	 * @return	string	HTML markup for the Settings subpage navigation tabs
	 */
	private static function _compose_settings_tabs( $active_tab = 'general' ) {
		$tablist_item = self::$page_template_array['tablist-item'];
		$tabs = '';
		foreach ( self::_get_options_tablist() as $key => $item ) {
			$item_values = array(
				'data-tab-id' => $key,
				'nav-tab-active' => ( $active_tab == $key ) ? 'nav-tab-active' : '',
				'settings-page' => MLACustomFeedExample::SLUG_PREFIX . '-settings-' . $key,
				'title' => $item['title']
			);

			$tabs .= MLAData::mla_parse_template( $tablist_item, $item_values );
		} // foreach $item

		$tablist_values = array( 'tablist' => $tabs );
		return MLAData::mla_parse_template( self::$page_template_array['tablist'], $tablist_values );
	}

	/**
	 * Compose the Edit Custom Feed tab content
	 *
	 * @since 1.10
	 *
	 * @param	array	$item Data values for the item.
	 * @param	array	&$template Display templates.
	 * @return	array	'message' => status/error messages, 'body' => tab content
	 */
	private static function _compose_edit_custom_feed_tab( $item, &$template ) {
		$page_values = array(
			'form_url' => admin_url( 'options-general.php' ) . '?page=mlafeed-settings-general&mla_tab=general',
			'post_ID' => $item['post_ID'],
			'old_slug' => $item['slug'],
			'_wpnonce' => wp_nonce_field( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME, true, false ),

			'slug' => $item['slug'],
			'rss_selected' => '', // Set below
			'rss2_selected' => '',
			'rss_http_selected' => '',
			'title' => $item['title'],
			'link' => $item['link'],
			'description' => $item['description'],
			'current_selected' => 'current' === $item['last_build_date'] ? 'selected=selected' : '',
			'modified_selected' => 'modified' === $item['last_build_date'] ? 'selected=selected' : '',
			'ttl' => !empty( $item['ttl'] ) ? $item['ttl'] : '',
			'none_selected' => '', // Set below
			'hourly_selected' => '',
			'daily_selected' => '',
			'weekly_selected' => '',
			'monthly_selected' => '',
			'yearly_selected' => '',
			'update_frequency' => !empty( $item['update_frequency'] ) ? $item['update_frequency'] : '',
			'update_base' => $item['update_base'],
			'taxonomies' => $item['taxonomies'],
			'parameters' => $item['parameters'],
			'template_slug' => $item['template_slug'],
			'template_name' => $item['template_name'],
			
			'active_selected' => $item['active'] ? 'selected=selected' : '',
			'inactive_selected' => $item['active'] ? '' : 'selected=selected',
		);

		switch( $item['type'] ) {
			case 'rss':
				$page_values['rss_selected'] = 'selected="selected"';
				break;
			case 'rss-http':
				$page_values['rss_http_selected'] = 'selected="selected"';
				break;
			default:
				$page_values['rss2_selected'] = 'selected="selected"';
		} // type

		switch( $item['update_period'] ) {
			case 'none':
				$page_values['none_selected'] = 'selected="selected"';
				break;
			case 'daily':
				$page_values['daily_selected'] = 'selected="selected"';
				break;
			case 'weekly':
				$page_values['weekly_selected'] = 'selected="selected"';
				break;
			case 'monthly':
				$page_values['monthly_selected'] = 'selected="selected"';
				break;
			case 'yearly':
				$page_values['yearly_selected'] = 'selected="selected"';
				break;
			default:
				$page_values['hourly_selected'] = 'selected="selected"';
		} // update_period

		return array(
			'message' => '',
			'body' => MLAData::mla_parse_template( $template['single-item-edit'], $page_values )
		);
	}

	/**
	 * Compose the General tab content for the Settings subpage
	 *
	 * @since 1.10
	 * @uses $page_template_array contains tab content template(s)
 	 *
	 * @return	array	'message' => status/error messages, 'body' => tab content
	 */
	private static function _compose_general_tab( ) {
		$page_content = array( 'message' => '', 'body' => '' );

		// Initialize page messages and content, check for page-level Save Changes, Add/Update/Cancel Feed
		if ( !empty( $_REQUEST['mla-custom-feed-options-save'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content = self::_save_setting_changes( );
		} elseif ( !empty( $_REQUEST['mla-add-custom-feed-submit'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content['message'] = MLACustomFeedExample::_add_custom_feed();
			MLA_Custom_Feed_Query::mla_put_custom_feed_settings();
		} elseif ( !empty( $_REQUEST['mla-edit-custom-feed-submit'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content = MLACustomFeedExample::_update_custom_feed( $_REQUEST['mla_edit_custom_feed']['post_ID'], self::$page_template_array );
			MLA_Custom_Feed_Query::mla_put_custom_feed_settings();
		} elseif ( !empty( $_REQUEST['mla-edit-custom-feed-cancel'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );
			$page_content['message'] = 'Edit Custom Feed cancelled.';
		}

		if ( !empty( $page_content['body'] ) ) {
			return $page_content;
		}

		// Process bulk actions that affect an array of items
		$bulk_action = MLA_Custom_Feed_List_Table::mla_current_bulk_action();
		if ( $bulk_action && ( $bulk_action != 'none' ) ) {
			if ( array_key_exists( $bulk_action, MLA_Custom_Feed_List_Table::mla_get_bulk_actions() ) ) {
				if ( isset( $_REQUEST['cb_mla_item_ID'] ) ) {
					foreach ( $_REQUEST['cb_mla_item_ID'] as $post_ID ) {
						switch ( $bulk_action ) {
							case 'delete':
								$item_content = MLACustomFeedExample::_delete_custom_feed( $post_ID );
								break;
							default:
								$item_content = 'Bad action'; // UNREACHABLE
						} // switch $bulk_action

						$page_content['message'] .= $item_content . '<br>';
					} // foreach cb_attachment

					MLA_Custom_Feed_Query::mla_put_custom_feed_settings();
				} // isset cb_attachment
				else {
					/* translators: 1: action name, e.g., edit */
					$page_content['message'] = sprintf( 'Bulk Action %1$s - no items selected.', $bulk_action );
				}
			} else {
				/* translators: 1: bulk_action, e.g., delete, edit, execute, purge */
				$page_content['message'] = sprintf( 'Unknown bulk action %1$s', $bulk_action );
			}
		} // $bulk_action

		// Process row-level actions that affect a single item
		if ( !empty( $_REQUEST['mla_admin_action'] ) ) {
			check_admin_referer( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME );

			$page_content = array( 'message' => '', 'body' => '' );

			switch ( $_REQUEST['mla_admin_action'] ) {
				case MLACore::MLA_ADMIN_SINGLE_EDIT_DISPLAY:
					$item = MLA_Custom_Feed_Query::mla_find_custom_feed( $_REQUEST['mla_item_ID'] );
					$page_content = self::_compose_edit_custom_feed_tab( $item, self::$page_template_array );
					break;
				case MLACore::MLA_ADMIN_SINGLE_DELETE:
					$page_content['message'] = MLACustomFeedExample::_delete_custom_feed( $_REQUEST['mla_item_ID'] );
					MLA_Custom_Feed_Query::mla_put_custom_feed_settings();
					break;
				default:
					$page_content['message'] = sprintf( 'Unknown mla_admin_action - "%1$s"', $_REQUEST['mla_admin_action'] );
					break;
			} // switch ($_REQUEST['mla_admin_action'])
		} // (!empty($_REQUEST['mla_admin_action'])

		if ( !empty( $page_content['body'] ) ) {
			return $page_content;
		}

		// Display the General tab and the Custom Feed table
		$_SERVER['REQUEST_URI'] = remove_query_arg( array(
			'mla_admin_action',
			'mla_custom_feed_item',
			'mla_item_ID',
			'_wpnonce',
			'_wp_http_referer',
			'action',
			'action2',
			'cb_mla_item_ID',
			'mla-edit-custom-feed-cancel',
			'mla-edit-custom-feed-submit',
			'mla-custom-feed-options-save',
		), $_SERVER['REQUEST_URI'] );

		// Create an instance of our package class
		$MLA_Custom_Feed_List_Table = new MLA_Custom_Feed_List_Table();

		// Fetch, prepare, sort, and filter our data
		$MLA_Custom_Feed_List_Table->prepare_items();

		// Start with page-level option row(s)
		$page_values = array(
			'enable_custom_feeds_checked' => MLA_Custom_Feed_Query::get_option('add_mlafeeds') ? 'checked="checked" ' : '',
		);
		$options_list = MLAData::mla_parse_template( self::$page_template_array['page-level-options'], $page_values );

		// WPML requires that lang be the first argument after page
		$view_arguments = MLA_Custom_Feed_List_Table::mla_submenu_arguments();
		$form_language = isset( $view_arguments['lang'] ) ? '&lang=' . $view_arguments['lang'] : '';
		$form_arguments = '?page=' . MLACustomFeedExample::SLUG_PREFIX . '-settings-general' . $form_language . '&mla_tab=general';

		// We need to remember all the view arguments
		$view_args = '';
		foreach ( $view_arguments as $key => $value ) {
			// 'lang' has already been added to the form action attribute
			if ( in_array( $key, array( 'lang' ) ) ) {
				continue;
			}

			if ( is_array( $value ) ) {
				foreach ( $value as $element_key => $element_value )
					$view_args .= "\t" . sprintf( '<input type="hidden" name="%1$s[%2$s]" value="%3$s" />', $key, $element_key, esc_attr( $element_value ) ) . "\n";
			} else {
				$view_args .= "\t" . sprintf( '<input type="hidden" name="%1$s" value="%2$s" />', $key, esc_attr( $value ) ) . "\n";
			}
		}

		$page_values = array(
			'form_url' => admin_url( 'options-general.php' ) . $form_arguments,
			'view_args' => $view_args,
			'_wpnonce' => wp_nonce_field( MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME, true, false ),
			'results' => ! empty( $_REQUEST['s'] ) ? '<span class="alignright" style="margin-top: .5em; font-weight: bold">Search results for:&nbsp;</span>' : '',
			's' => isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '',
			'options_list' => $options_list,
		);

		$page_content['body'] = MLAData::mla_parse_template( self::$page_template_array['before-table'], $page_values );

		// Now we can render the completed list table
		ob_start();
		$MLA_Custom_Feed_List_Table->views();
		$MLA_Custom_Feed_List_Table->display();
		$page_content['body'] .= ob_get_contents();
		ob_end_clean();

		$page_content['body'] .= MLAData::mla_parse_template( self::$page_template_array['after-table'], $page_values );

		return $page_content;
	}

	/**
	 * Compose the General tab content for the Settings subpage
	 *
	 * @since 1.10
	 * @uses $page_template_array contains tab content template(s)
 	 *
	 * @return	array	'message' => status/error messages, 'body' => tab content
	 */
	private static function _compose_documentation_tab( ) {
		$page_content = array( 'message' => '', 'body' => '' );
		$page_values = array(
		);

		$page_content['body'] = MLAData::mla_parse_template( self::$page_template_array['documentation-tab'], $page_values );
		return $page_content;
	}

	/**
	 * Save settings as a WordPress wp_options entry
	 *
	 * @since 1.10
	 *
	 * @return	array	'message' => status/error messages, 'body' => tab content
	 */
	private static function _save_setting_changes() {
		$page_content = array( 'message' => 'Settings unchanged.', 'body' => '' );
		$changed = MLA_Custom_Feed_Query::update_option( 'add_mlafeeds', isset( $_REQUEST[ 'mla_enable_custom_feeds' ] ) );
		
		if ( $changed ) {
			$changed = MLA_Custom_Feed_Query::mla_put_custom_feed_settings();

			if ( false === $changed ) {
				$page_content['message'] = "Settings updated failed.";
			} elseif ( $changed ) {
				$page_content['message'] = "Settings have been updated.";
			}
		}

		return $page_content;		
	} // _save_setting_changes

	/**
	 * Add a custom feed from values in $_REQUEST
 	 *
	 * @since 1.10
	 * @uses $_REQUEST for field-level values
	 *
	 * @return string Message(s) reflecting the results of the operation
	 */
	private static function _add_custom_feed() {
		$mla_custom_feed = isset( $_REQUEST['mla_add_custom_feed'] ) ? stripslashes_deep( $_REQUEST['mla_add_custom_feed'] ) : array();

		// Validate new feed name
		if ( !empty( $mla_custom_feed['slug'] ) ) {
			$new_name = sanitize_title( $mla_custom_feed['slug'] );
		} else {
			return 'ERROR: No feed slug entered';
		}

		$message_list = '';
		
		if ( MLA_Custom_Feed_Query::mla_find_custom_feed_ID( $new_name ) ) {
			// Generate a unique name
			$index = 1;
			while( MLA_Custom_Feed_Query::mla_find_custom_feed_ID( $new_name . '-' . $index ) ) {
				$index++;
			}

			$default_name = $new_name . '-' . $index;
			
			$message_list .= sprintf( 'Warning: Duplicate new slug "%1$s", changed to "%2$s".<br>', $new_name, $default_name );
			$new_name = $default_name;
		} // duplicate name

		// Convert checkbox/dropdown controls to booleans
		$mla_custom_feed['active'] = '1' === $mla_custom_feed['status'];

		$new_rule = array(
			'post_ID' => 0,
			'slug' => $new_name,
			'type' => $mla_custom_feed['type'],
			'title' => $mla_custom_feed['title'],
			'link' => $mla_custom_feed['link'],
			'description' => $mla_custom_feed['description'],
			'last_build_date' => $mla_custom_feed['last_build_date'],
			'ttl' => $mla_custom_feed['ttl'],
			'update_period' => $mla_custom_feed['update_period'],
			'update_frequency' => $mla_custom_feed['update_frequency'],
			'update_base' => $mla_custom_feed['update_base'],
			'taxonomies' => $mla_custom_feed['taxonomies'],
			'parameters' => $mla_custom_feed['parameters'],
			'template_slug' => $mla_custom_feed['template_slug'],
			'template_name' => $mla_custom_feed['template_name'],
			'active' => $mla_custom_feed['active'],
			'changed' => true,
			'deleted' => false,
		);

		if ( MLA_Custom_Feed_Query::mla_add_custom_feed( $new_rule ) ) {
			return $message_list . 'Feed added';
		}

		return $message_list . 'ERROR: Feed addition failed';
	} // _add_custom_feed

	/**
	 * Update a custom feed from full-screen Edit Feed values in $_REQUEST
 	 *
	 * @since 1.10
	 * @uses $_REQUEST for field-level values
	 *
	 * @param integer $post_id ID value of rule to update
	 * @param	array	&$template Display templates.
	 * @return	array	'message' => status/error messages, 'body' => tab content
	 */
	private static function _update_custom_feed( $post_id, &$template ) {
		$error_message = '';
		$mla_custom_feed = isset( $_REQUEST['mla_edit_custom_feed'] ) ? stripslashes_deep( $_REQUEST['mla_edit_custom_feed'] ) : array();

		// Validate new feed name
		if ( !empty( $mla_custom_feed['slug'] ) ) {
			$mla_custom_feed['slug'] = sanitize_title( $mla_custom_feed['slug'] );
			if ( $mla_custom_feed['slug'] !== $mla_custom_feed['old_slug'] ) {
				if ( MLA_Custom_Feed_Query::mla_find_custom_feed_ID( $mla_custom_feed['slug'] ) ) {
					$error_message = 'ERROR: Feed already exists for the new name';
					$mla_custom_feed['slug'] = $mla_custom_feed['old_slug'];
				}
			}
		} else {
			$error_message = 'ERROR: New feed name is empty';
			$mla_custom_feed['slug'] = $mla_custom_feed['old_slug'];
		}

		// Convert form values to internal format
		unset( $mla_custom_feed['old_slug'] );
		$mla_custom_feed['active'] = '1' === $mla_custom_feed['status'];
		unset( $mla_custom_feed['status'] );
		$mla_custom_feed['ttl'] = absint( $mla_custom_feed['ttl'] );
		$mla_custom_feed['update_frequency'] = absint( $mla_custom_feed['update_frequency'] );

		if ( 'none' !== $mla_custom_feed['update_period'] && empty( $mla_custom_feed['update_frequency'] ) ) {
			$mla_custom_feed['update_frequency'] = 1;
		}
		
		if ( !empty( $mla_custom_feed['update_base'] ) ) {
			$mla_custom_feed['update_base'] = date( DATE_W3C, strtotime( $mla_custom_feed['update_base'] ) );
		}
		
		$new_feed = array_merge( $mla_custom_feed, array(
			'changed' => true,
			'deleted' => false,

		) );
//error_log( __LINE__ . ' _update_custom_feed new_feed = ' . var_export( $new_feed, true ), 0 );

		if ( empty( $error_message ) ) {
			if ( false === MLA_Custom_Feed_Query::mla_replace_custom_feed( $new_feed ) ) {
				$error_message = 'ERROR: Feed update failed';
			}
		}

		if ( empty( $error_message ) ) {
			return array( 'message' => 'Feed updated', 'body' => '' );
		}

		$page_content = self::_compose_edit_custom_feed_tab( $new_feed, $template );
		$page_content['message'] = $error_message;
		return $page_content;
	} // _update_custom_feed

	/**
	 * Delete a custom feed
 	 *
	 * @since 1.10
	 *
	 * @param integer $post_id ID value of rule to delete
	 *
	 * @return	array	Message(s) reflecting the results of the operation
	 */
	private static function _delete_custom_feed( $post_id ) {
		$feed = MLA_Custom_Feed_Query::mla_find_custom_feed( $post_id );
		if ( false === $feed ) {
			return "ERROR: _delete_custom_feed( {$post_id} ) feed not found.";
		}

		MLA_Custom_Feed_Query::mla_update_custom_feed( $post_id, 'deleted', true );
		return sprintf( 'Feed "%1$s" deleted.', $feed['slug'] );
	} // _delete_custom_feed

	/**
	 * Retrieve item assigned terms, formatted for use in feeds.
	 *
	 * All of the categories for the current post in the feed loop, will be
	 * retrieved and have feed markup added, so that they can easily be added to the
	 * RSS2, Atom, or RSS1 and RSS0.91 RDF feeds.
	 *
	 * @since 1.00
	 *
	 * @param string $taxonomies Taxonomy or comma-delimited taxonomies to process.
	 * @param string $type Optional, default is the type returned by get_default_feed().
	 *
	 * @return string All of the post categories for displaying in the feed.
	 */
	public static function mla_get_the_terms_rss( $taxonomies, $type = NULL ) {
		global $post;
		
		if ( empty( $type ) ) {
			$type = get_default_feed();
		}

		$filter = 'rss';
		if ( 'atom' == $type ) {
			$filter = 'raw';
		}

		$taxonomies = array_map( 'strtolower', array_map( 'trim', explode( ',', $taxonomies ) ) );
//error_log( __LINE__ . " MLACustomFeedExample::mla_get_the_terms_rss( {$post->ID} ) \$taxonomies = " . var_export( $taxonomies, true ), 0 );

		$term_names = array();
		foreach ( $taxonomies as $taxonomy ) {
			$terms = wp_get_object_terms( $post->ID, $taxonomy );
//error_log( __LINE__ . " MLACustomFeedExample::mla_get_the_terms_rss( {$post->ID}, {$taxonomy} ) \$terms = " . var_export( $terms, true ), 0 );
			foreach ( $terms as $term ) {
				$term_names[] = sanitize_term_field( 'name', $term->name, $term->term_id, $taxonomy, $filter);
			}
		}
		
		$term_names = array_unique( $term_names );
//error_log( __LINE__ . " MLACustomFeedExample::mla_get_the_terms_rss( {$post->ID} ) \$term_names = " . var_export( $term_names, true ), 0 );
	
		$the_list = '';
		foreach ( $term_names as $term_name ) {
			if ( 'rdf' == $type )
				$the_list .= "\t\t<dc:subject><![CDATA[$term_name]]></dc:subject>\n";
			elseif ( 'atom' == $type )
				$the_list .= sprintf( '<category scheme="%1$s" term="%2$s" />', esc_attr( get_bloginfo_rss( 'url' ) ), esc_attr( $term_name ) );
			else
				$the_list .= "\t\t<category><![CDATA[" . @html_entity_decode( $term_name, ENT_COMPAT, get_option('blog_charset') ) . "]]></category>\n";
		}
		
		return $the_list;
	} // mla_get_the_terms_rss
	
	/**
	 * Active feed settings, shared with the feed template file
	 *
	 * @since 1.10
	 *
	 * @var	array
	 */
	public static $active_feed = NULL;

	/**
	 * Process the MLA Custom Feed
	 *
	 * @since 1.00
	 *
	 * @param bool   $is_comment_feed Whether the feed is a comment feed.
	 * @param string $feed            The feed name.
	 */
	public static function mla_custom_feed( $is_comment_feed, $feed ) {
		global $wp_query;
//error_log( __LINE__ . ' MLACustomFeedExample::mla_custom_feed $_REQUEST = ' . var_export( $_REQUEST, true ), 0 );
//error_log( __LINE__ . ' MLACustomFeedExample::mla_custom_feed $wp_query->request = ' . var_export( $wp_query->request, true ), 0 );
//error_log( __LINE__ . ' MLACustomFeedExample::mla_custom_feed $wp_query->query  = ' . var_export( $wp_query->query , true ), 0 );
//error_log( __LINE__ . ' MLACustomFeedExample::mla_custom_feed $wp_query->queried_object  = ' . var_export( $wp_query->queried_object , true ), 0 );
//error_log( __LINE__ . ' MLACustomFeedExample::mla_custom_feed $wp_query->queried_object_id  = ' . var_export( $wp_query->queried_object_id , true ), 0 );

		if ( !class_exists( 'MLAShortcode_Support' ) ) {
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-shortcode-support.php' );
		}

		// Find the requested feed and verify active status
		MLACustomFeedExample::$active_feed = NULL;
		if ( $ID = MLA_Custom_Feed_Query::mla_find_custom_feed_ID( $feed ) ) {
			MLACustomFeedExample::$active_feed = MLA_Custom_Feed_Query::mla_find_custom_feed( $ID );
		}
		
		if ( empty( MLACustomFeedExample::$active_feed ) || ( false === MLACustomFeedExample::$active_feed['active'] ) ) {
			wp_die( "Feed \"{$feed}\" is not active." );
		}
		
		// Supply default values
		if ( empty( MLACustomFeedExample::$active_feed['title'] ) ) {
			MLACustomFeedExample::$active_feed['title'] = get_wp_title_rss();
		}
		
		if ( empty( MLACustomFeedExample::$active_feed['link'] ) ) {
			MLACustomFeedExample::$active_feed['link'] = get_bloginfo_rss('url');
		}
		
		if ( empty( MLACustomFeedExample::$active_feed['description'] ) ) {
			MLACustomFeedExample::$active_feed['description'] = get_bloginfo_rss('description');
		}

		// Build the data selection parameters
		$attr = MLACustomFeedExample::$active_feed['parameters'];
		if ( empty( $attr ) ) {
			$query = array();
		} elseif ( is_string( $attr ) ) {
			$attr = str_replace( '{+', '[+', str_replace( '+}', '+]', $attr ) );
			$query = shortcode_parse_atts( $attr );
		}

		// Taxonomy parameters can be passed in the URL, e.g., http://www.example.com/category/cat1,cat2/feed
		if ( is_object( $wp_query ) && is_array( $wp_query->query )) {
			$query = array_merge( $query, $wp_query->query );
		}
		
		// The mlafeed_parameters can contain {+request: ... +} parameters
		$replacement_values = MLAData::mla_expand_field_level_parameters( $attr, $query, array() );
		$attr = MLAData::mla_parse_template( $attr, $replacement_values );
		
//error_log( __LINE__ . ' MLACustomFeedExample::mla_custom_feed attr = ' . var_export( $attr, true ), 0 );
		// Find the feed items
		add_action( 'mla_gallery_wp_query_object', 'MLACustomFeedExample::mla_gallery_wp_query_object', 10, 1 );
		MLAShortcodes::mla_get_shortcode_attachments( 0, $attr, false );
		remove_action( 'mla_gallery_wp_query_object', 'MLACustomFeedExample::mla_gallery_wp_query_object', 10 );
//error_log( __LINE__ . ' MLACustomFeedExample::mla_custom_feed posts = ' . var_export( self::$wp_query_object->posts, true ), 0 );

		// Find the lastBuildDate
		if ( 'modified' === MLACustomFeedExample::$active_feed['last_build_date'] ) {
			$highest_date = '0000-00-00 00:00:00';
			foreach (self::$wp_query_object->posts as $post ) {
				if ( $highest_date < $post->post_modified_gmt ) {
				 $highest_date = $post->post_modified_gmt;
				}
			}
			
			MLACustomFeedExample::$active_feed['last_build_date'] = mysql2date( 'r', $highest_date, false );
		} else {
			MLACustomFeedExample::$active_feed['last_build_date'] = date( 'r' );
		}
		
		if ( !empty( MLACustomFeedExample::$active_feed['template_slug'] ) ) {
			if ( !empty( MLACustomFeedExample::$active_feed['template_name'] ) ) {
				get_template_part( MLACustomFeedExample::$active_feed['template_slug'], MLACustomFeedExample::$active_feed['template_name'] );
			} else {
				get_template_part( MLACustomFeedExample::$active_feed['template_slug'] );
			}
		} else {
			require( dirname( __FILE__ ) . '/mla-custom-feed-template.php' );
		}
	} // mla_custom_feed

	/**
	 * Save the WP_Query object for use in the Loop
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	public static $wp_query_object = NULL;

	/**
	 * MLA Gallery WP Query Object
	 *
	 * This action gives you an opportunity (read-only) to record anything you need from the WP_Query object used
	 * to select the attachments for gallery display. This is the ONLY point at which the WP_Query object is defined.
	 *
	 * @since 1.00
	 * @uses MLAShortcodes::$mla_gallery_wp_query_object
	 *
	 * @param	array	query arguments passed to WP_Query->query
	 */
	public static function mla_gallery_wp_query_object( $query_arguments ) {
		//error_log( __LINE__ . ' MLACustomFeedExample::mla_gallery_wp_query_object $query_arguments = ' . var_export( $query_arguments, true ), 0 );

		self::$wp_query_object = MLAShortcodes::$mla_gallery_wp_query_object;
	} // mla_gallery_wp_query_object
} // Class MLACustomFeedExample

/* 
 * The WP_List_Table class isn't automatically available to plugins
 */
if ( !class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Class MLA (Media Library Assistant) Custom Feed List Table implements the Custom Feed
 * admin settings submenu table
 *
 * Extends the core WP_List_Table class.
 *
 * @package MLA Custom Feed Example
 * @since 1.10
 */
class MLA_Custom_Feed_List_Table extends WP_List_Table {
	/**
	 * Initializes some properties from $_REQUEST variables, then
	 * calls the parent constructor to set some default configs.
	 *
	 * @since 1.10
	 *
	 * @return	void
	 */
	function __construct( ) {
		// MLA does not use this
		$this->modes = array(
			'list' => __( 'List View' ),
		);

		//Set parent defaults
		parent::__construct( array(
			'singular' => 'custom_feed', //singular name of the listed records
			'plural' => 'custom_feed', //plural name of the listed records
			'ajax' => true, //does this table support ajax?
			'screen' => 'settings_page_' . MLACustomFeedExample::SLUG_PREFIX . '-settings-general'
		) );

		// NOTE: There is one add_action call at the end of this source file.
	}

	/**
	 * Table column definitions
	 *
	 * This array defines table columns and titles where the key is the column slug (and class)
	 * and the value is the column's title text.
	 * 
	 * All of the columns are added to this array by MLA_Custom_Feed_List_Table::_localize_default_columns_array.
	 *
	 * @since 1.10
	 *
	 * @var	array
	 */
	private static $default_columns = array();

	/**
	 * Default values for hidden columns
	 *
	 * This array is used when the user-level option is not set, i.e.,
	 * the user has not altered the selection of hidden columns.
	 *
	 * The value on the right-hand side must match the column slug, e.g.,
	 * array(0 => 'ID_parent, 1 => 'title_name').
	 * 
	 * @since 1.10
	 *
	 * @var	array
	 */
	private static $default_hidden_columns	= array(
		// 'slug',
		'type',
		// 'title',
		'link',
		'description',
		'last_build_date',
		'ttl',
		'update_period',
		'update_frequency',
		'update_base',
		// 'taxonomies',
		// 'parameters',
		'template_slug',
		'template_name',
		// 'status',
	);

	/**
	 * Sortable column definitions
	 *
	 * This array defines the table columns that can be sorted. The array key
	 * is the column slug that needs to be sortable, and the value is database column
	 * to sort by. Often, the key and value will be the same, but this is not always
	 * the case (as the value is a column name from the database, not the list table).
	 *
	 * The array value also contains a boolean which is 'true' if the initial sort order
	 * for the column is DESC/Descending.
	 *
	 * @since 1.10
	 * @access private
	 * @var	array $default_sortable_columns {
	 *         @type array $$column_slug {
	 *                 @type string $orderby_name Database column or other sorting slug.
	 *                 @type boolean $descending Optional. True to make the initial orderby DESC.
	 *         }
	 * }
	 */
	private static $default_sortable_columns = array(
		'slug' => array('slug',false),
		'type' => array('type',false),
		'title' => array('title',false),
		'link' => array('link',false),
		'description' => array('description',true),
		'last_build_date' => array('last_build_date',true),
		'ttl' => array('ttl',false),
		'update_period' => array('update_period',false),
		'update_frequency' => array('update_frequency',false),
		'update_base' => array('update_base',false),
		'taxonomies' => array('taxonomies',false),
		'parameters' => array('parameters',false),
		'template_slug' => array('template_slug',false),
		'template_name' => array('template_name',false),
		'status' => array('status',false),
		);

	/**
	 * Access the default list of hidden columns
	 *
	 * @since 1.10
	 *
	 * @return	array	default list of hidden columns
	 */
	private static function _default_hidden_columns( ) {
		return self::$default_hidden_columns;
	}

	/**
	 * Return the names and display values of the sortable columns
	 *
	 * @since 1.10
	 *
	 * @return	array	name => array( orderby value, heading ) for sortable columns
	 */
	public static function mla_get_sortable_columns( ) {
		return self::$default_sortable_columns;
	}

	/**
	 * Process $_REQUEST, building $submenu_arguments
	 *
	 * @since 1.10
	 *
	 * @param boolean $include_filters Optional. Include the "click filter" values in the results. Default true.
	 * @return array non-empty view, search, filter and sort arguments
	 */
	public static function mla_submenu_arguments( $include_filters = true ) {
		static $submenu_arguments = NULL, $has_filters = NULL;

		if ( is_array( $submenu_arguments ) && ( $has_filters == $include_filters ) ) {
			return $submenu_arguments;
		}

		$submenu_arguments = array();
		$has_filters = $include_filters;

		// View arguments
		if ( isset( $_REQUEST['mla_custom_feed_view'] ) ) {
			$submenu_arguments['mla_custom_feed_view'] = $_REQUEST['mla_custom_feed_view'];
		}

		// Search box arguments
		if ( !empty( $_REQUEST['s'] ) ) {
			$submenu_arguments['s'] = urlencode( stripslashes( $_REQUEST['s'] ) );
		}

		// Filter arguments (from table header)
		if ( isset( $_REQUEST['mla_custom_feed_status'] ) && ( 'any' != $_REQUEST['mla_custom_feed_status'] ) ) {
			$submenu_arguments['mla_custom_feed_status'] = $_REQUEST['mla_custom_feed_status'];
		}

		// Sort arguments (from column header)
		if ( isset( $_REQUEST['order'] ) ) {
			$submenu_arguments['order'] = $_REQUEST['order'];
		}

		if ( isset( $_REQUEST['orderby'] ) ) {
			$submenu_arguments['orderby'] = $_REQUEST['orderby'];
		}

		return $submenu_arguments;
	}

	/**
	 * Called in the admin_init action because the list_table object isn't
	 * created in time to affect the "screen options" setup.
	 *
	 * @since 1.10
	 *
	 * @return	void
	 */
	public static function mla_admin_init( ) {
		if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == MLACustomFeedExample::SLUG_PREFIX . '-settings-general' ) {
			add_filter( 'get_user_option_managesettings_page_' . MLACustomFeedExample::SLUG_PREFIX . '-settings-generalcolumnshidden', 'MLA_Custom_Feed_List_Table::mla_manage_hidden_columns_filter', 10, 3 );
			add_filter( 'manage_settings_page_' . MLACustomFeedExample::SLUG_PREFIX . '-settings-general_columns', 'MLA_Custom_Feed_List_Table::mla_manage_columns_filter', 10, 0 );
		}
	}

	/**
	 * Handler for filter 'get_user_option_managesettings_page_mlafeed-settings-settings-generalcolumnshidden'
	 *
	 * Required because the screen.php get_hidden_columns function only uses
	 * the get_user_option result. Set when the file is loaded because the object
	 * is not created in time for the call from screen.php.
	 *
	 * @since 1.10
	 *
	 * @param mixed	false or array with current list of hidden columns, if any
	 * @param string	'managesettings_page_mlafeed-settings-settings-generalcolumnshidden'
	 * @param object WP_User object, if logged in
	 *
	 * @return	array	updated list of hidden columns
	 */
	public static function mla_manage_hidden_columns_filter( $result, $option, $user_data ) {
//error_log( __LINE__ . " MLACustomFeedExample::mla_manage_hidden_columns_filter ( {$option} ) result = " . var_export( $result, true ), 0 );

		if ( false !== $result ) {
			return $result;
		}

		return self::_default_hidden_columns();
	}

	/**
	 * Handler for filter 'manage_settings_page_mlafeed-settings_columns'
	 *
	 * This required filter dictates the table's columns and titles. Set when the
	 * file is loaded because the list_table object isn't created in time
	 * to affect the "screen options" setup.
	 *
	 * @since 1.10
	 *
	 * @return	array	list of table columns
	 */
	public static function mla_manage_columns_filter( ) {
		self::_localize_default_columns_array();
		return self::$default_columns;
	}

	/**
	 * Builds the $default_columns array with translated source texts.
	 *
	 * @since 1.10
	 *
	 * @return	void
	 */
	private static function _localize_default_columns_array( ) {
		if ( empty( self::$default_columns ) ) {
			// Build the default columns array at runtime to accomodate future calls to localization functions
			self::$default_columns = array(
				'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
				'slug' => 'Slug',
				'type' => 'Type',
				'title' => 'Title',
				'link' => 'Link',
				'description' => 'Description',
				'last_build_date' => 'Last Built',
				'ttl' => 'TTL',
				'update_period' => 'Update Period',
				'update_frequency' => 'Update Frequency',
				'update_base' => 'Update Base',
				'taxonomies' => 'Taxonomies',
				'parameters' => 'Parameters',
				'template_slug' => 'Template Slug',
				'template_name' => 'Template Name',
				'status' => 'Status',
			);
		}
	}

	/**
	 * Checks the current user's permissions
	 *
	 * @since 1.10
	 *
	 * @return bool
	 */
	public function ajax_user_can() {
		return current_user_can('manage_options');
	}

	/**
	 * Get the name of the default primary column.
	 *
	 * @since 1.10
	 * @access protected
	 *
	 * @return string Name of the default primary column
	 */
	protected function get_default_primary_column_name() {
		return 'slug';
	}

	/**
	 * Generate and display row actions links.
	 *
	 * @since 1.10
	 * @access protected
	 *
	 * @param object $item        Attachment being acted upon.
	 * @param string $column_name Current column name.
	 * @param string $primary     Primary column name.
	 * @return string Row actions output for media attachments.
	 */
	protected function handle_row_actions( $item, $column_name, $primary ) {
		if ( $primary === $column_name ) {
			$actions = $this->row_actions( $this->_build_rollover_actions( $item, $column_name ) );
			return $actions;
		}

		return '';
	}

	/**
	 * Add rollover actions to a table column
	 *
	 * @since 1.10
	 * 
	 * @param object An MLA custom_feed_rule object
	 * @param string	Current column name
	 *
	 * @return	array	Names and URLs of row-level actions
	 */
	private function _build_rollover_actions( $item, $column ) {
		$actions = array();

		// Compose view arguments
		$view_args = array_merge( array(
			'page' => MLACustomFeedExample::SLUG_PREFIX . '-settings-general',
			'mla_tab' => 'general',
			'mla_item_ID' => urlencode( $item->post_ID )
		), MLA_Custom_Feed_List_Table::mla_submenu_arguments() );

		if ( isset( $_REQUEST['paged'] ) ) {
			$view_args['paged'] = $_REQUEST['paged'];
		}

		if ( isset( $_REQUEST['order'] ) ) {
			$view_args['order'] = $_REQUEST['order'];
		}

		if ( isset( $_REQUEST['orderby'] ) ) {
			$view_args['orderby'] = $_REQUEST['orderby'];
		}

		$actions['edit'] = '<a href="' . add_query_arg( $view_args, wp_nonce_url( '?mla_admin_action=' . MLACore::MLA_ADMIN_SINGLE_EDIT_DISPLAY, MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="Edit this item">Edit</a>';

		$actions['delete'] = '<a class="delete-tag"' . ' href="' . add_query_arg( $view_args, wp_nonce_url( '?mla_admin_action=' . MLACore::MLA_ADMIN_SINGLE_DELETE, MLACore::MLA_ADMIN_NONCE_ACTION, MLACore::MLA_ADMIN_NONCE_NAME ) ) . '" title="Delete this item Permanently">Delete Permanently</a>';

		return $actions;
	}

	/**
	 * Supply a column value if no column-specific function has been defined
	 *
	 * Called when the parent class can't find a method specifically built for a
	 * given column. All columns should have a specific method, so this function
	 * returns a troubleshooting message.
	 *
	 * @since 1.10
	 *
	 * @param array	A singular item (one full row's worth of data)
	 * @param array	The name/slug of the column to be processed
	 * @return string Text or HTML to be placed inside the column
	 */
	function column_default( $item, $column_name ) {
		//Show the whole array for troubleshooting purposes
		return sprintf( 'column_default: %1$s, %2$s', $column_name, print_r( $item, true ) );
	}

	/**
	 * Displays checkboxes for using bulk actions. The 'cb' column
	 * is given special treatment when columns are processed.
	 *
	 * @since 1.10
	 * 
	 * @param object An MLA custom_feed_rule object
	 * @return string HTML markup to be placed inside the column
	 */
	function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="cb_mla_item_ID[]" value="%1$s" />',
		/*%1$s*/ $item->post_ID
		);
	}

	/**
	 * Populate the Slug column
	 *
	 * @since 1.10
	 * 
	 * @param object An MLA custom_feed_rule object
	 * @return string HTML markup to be placed inside the column
	 */
	function column_slug( $item ) {
		return esc_html( $item->slug );
	}

	/**
	 * Populate the Type column
	 *
	 * @since 1.10
	 * 
	 * @param object An MLA custom_feed_rule object
	 * @return string HTML markup to be placed inside the column
	 */
	function column_type( $item ) {
		return esc_html( $item->type );
	}

	/**
	 * Populate the Title column
	 *
	 * @since 1.10
	 * 
	 * @param object An MLA custom_feed_rule object
	 * @return string HTML markup to be placed inside the column
	 */
	function column_title( $item ) {
		return esc_html( $item->title );
	}

	/**
	 * Populate the Link Value column
	 *
	 * @since 1.10
	 * 
	 * @param object An MLA custom_feed_rule object
	 * @return string HTML markup to be placed inside the column
	 */
	function column_link( $item ) {
		return esc_html( $item->link );
	}

	/**
	 * Populate the Description column
	 *
	 * @since 1.10
	 * 
	 * @param object An MLA custom_feed_rule object
	 * @return string HTML markup to be placed inside the column
	 */
	function column_description( $item ) {
		return esc_html( $item->description );
	}

	/**
	 * Populate the Last Built column
	 *
	 * @since 1.10
	 * 
	 * @param object An MLA custom_feed_rule object
	 * @return string HTML markup to be placed inside the column
	 */
	function column_last_build_date( $item ) {
		return esc_html( $item->last_build_date );
	}

	/**
	 * Populate the Time-to-live column
	 *
	 * @since 1.10
	 * 
	 * @param object An MLA custom_feed_rule object
	 * @return string HTML markup to be placed inside the column
	 */
	function column_ttl( $item ) {
		return esc_html( $item->ttl );
	}

	/**
	 * Populate the Update Period column
	 *
	 * @since 1.10
	 * 
	 * @param object An MLA custom_feed_rule object
	 * @return string HTML markup to be placed inside the column
	 */
	function column_update_period( $item ) {
		return esc_html( $item->update_period );
	}

	/**
	 * Populate the Update Frequency column
	 *
	 * @since 1.10
	 * 
	 * @param object An MLA custom_feed_rule object
	 * @return string HTML markup to be placed inside the column
	 */
	function column_update_frequency( $item ) {
		return esc_html( $item->update_frequency );
	}

	/**
	 * Populate the Update Base column
	 *
	 * @since 1.10
	 * 
	 * @param object An MLA custom_feed_rule object
	 * @return string HTML markup to be placed inside the column
	 */
	function column_update_base( $item ) {
		return esc_html( $item->update_base );
	}

	/**
	 * Populate the Taxonomies column
	 *
	 * @since 1.10
	 * 
	 * @param object An MLA custom_feed_rule object
	 * @return string HTML markup to be placed inside the column
	 */
	function column_taxonomies( $item ) {
		return esc_html( $item->taxonomies );
	}

	/**
	 * Populate the Parameters column
	 *
	 * @since 1.10
	 * 
	 * @param object An MLA custom_feed_rule object
	 * @return string HTML markup to be placed inside the column
	 */
	function column_parameters( $item ) {
		return esc_html( $item->parameters );
	}

	/**
	 * Populate the Template Slug column
	 *
	 * @since 1.10
	 * 
	 * @param object An MLA custom_feed_rule object
	 * @return string HTML markup to be placed inside the column
	 */
	function column_template_slug( $item ) {
		return esc_html( $item->template_slug );
	}

	/**
	 * Populate the Tmplate Name column
	 *
	 * @since 1.10
	 * 
	 * @param object An MLA custom_feed_rule object
	 * @return string HTML markup to be placed inside the column
	 */
	function column_template_name( $item ) {
		return esc_html( $item->template_name );
	}

	/**
	 * Populate the Status column
	 *
	 * @since 1.10
	 * 
	 * @param object An MLA custom_feed_rule object
	 * @return string HTML markup to be placed inside the column
	 */
	function column_status( $item ) {
		if ( $item->active ) {
			return 'Active';
		} else {
			return 'Inactive';
		}
	}

	/**
	 * Display the pagination, adding view, search and filter arguments
	 *
	 * @since 1.10
	 * 
	 * @param string	'top' | 'bottom'
	 */
	function pagination( $which ) {
		$save_uri = $_SERVER['REQUEST_URI'];
		$_SERVER['REQUEST_URI'] = add_query_arg( MLA_Custom_Feed_List_Table::mla_submenu_arguments(), $save_uri );
		parent::pagination( $which );
		$_SERVER['REQUEST_URI'] = $save_uri;
	}

	/**
	 * This method dictates the table's columns and titles
	 *
	 * @since 1.10
	 * 
	 * @return	array	Column information: 'slugs'=>'Visible Titles'
	 */
	function get_columns( ) {
		return $columns = MLA_Custom_Feed_List_Table::mla_manage_columns_filter();
	}

	/**
	 * Returns the list of currently hidden columns from a user option or
	 * from default values if the option is not set
	 *
	 * @since 1.10
	 * 
	 * @return	array	Column information,e.g., array(0 => 'ID_parent, 1 => 'title_name')
	 */
	function get_hidden_columns( ) {
		$columns = get_user_option( 'managesettings_page_' . MLACustomFeedExample::SLUG_PREFIX . '-settings-generalcolumnshidden' );

		if ( is_array( $columns ) ) {
			return $columns;
		}

		return self::_default_hidden_columns();
	}

	/**
	 * Returns an array where the  key is the column that needs to be sortable
	 * and the value is db column to sort by.
	 *
	 * @since 1.10
	 * 
	 * @return	array	Sortable column information,e.g.,
	 * 					'slugs'=>array('data_values',boolean)
	 */
	function get_sortable_columns( ) {
		return self::$default_sortable_columns;
	}

	/**
	 * Returns HTML markup for one view that can be used with this table
	 *
	 * @since 1.10
	 *
	 * @param string $view_slug View slug
	 * @param array $custom_field_item count and labels for the View
	 * @param string $current_view Slug for current view 
	 * 
	 * @return	string | false	HTML for link to display the view, false if count = zero
	 */
	function _get_view( $view_slug, $custom_field_item, $current_view ) {
		static $base_url = NULL;

		$class = ( $view_slug == $current_view ) ? ' class="current"' : '';

		// Calculate the common values once per page load
		if ( is_null( $base_url ) ) {
			// Remember the view filters
			$base_url = 'options-general.php?page=' . MLACustomFeedExample::SLUG_PREFIX . '-settings-general&mla_tab=general';

			if ( isset( $_REQUEST['s'] ) ) {
				//$base_url = add_query_arg( array( 's' => $_REQUEST['s'] ), $base_url );
			}
		}

		$singular = sprintf('%s <span class="count">(%%s)</span>', $custom_field_item['singular'] );
		$plural = sprintf('%s <span class="count">(%%s)</span>', $custom_field_item['plural'] );
		$nooped_plural = _n_noop( $singular, $plural, 'media-library-assistant' );
		return "<a href='" . add_query_arg( array( 'mla_custom_feed_view' => $view_slug ), $base_url )
			. "'$class>" . sprintf( translate_nooped_plural( $nooped_plural, $custom_field_item['count'], 'media-library-assistant' ), number_format_i18n( $custom_field_item['count'] ) ) . '</a>';
	} // _get_view

	/**
	 * Returns an associative array listing all the views that can be used with this table.
	 * These are listed across the top of the page and managed by WordPress.
	 *
	 * @since 1.10
	 * 
	 * @return	array	View information,e.g., array ( id => link )
	 */
	function get_views( ) {
		// Find current view
		$current_view = isset( $_REQUEST['mla_custom_feed_view'] ) ? $_REQUEST['mla_custom_feed_view'] : 'all';

		// Generate the list of views, retaining keyword search criterion
		$s = isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : '';
		$custom_feed_items = MLA_Custom_Feed_Query::mla_tabulate_custom_feed_items( $s );
		$view_links = array();
		foreach ( $custom_feed_items as $slug => $item )
			$view_links[ $slug ] = self::_get_view( $slug, $item, $current_view );

		return $view_links;
	}

	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * @since 1.10
	 * 
	 * @return	array	Contains all the bulk actions: 'slugs'=>'Visible Titles'
	 */
	function get_bulk_actions( ) {
		return self::mla_get_bulk_actions();
	}

	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * @since 1.10
	 * 
	 * @return	array	Contains all the bulk actions: 'slugs'=>'Visible Titles'
	 */
	public static function mla_get_bulk_actions( ) {
		$actions = array();

		$actions['delete'] = 'Delete Permanently';

		return $actions;
	}

	/**
	 * Get the current action selected from the bulk actions dropdown
	 *
	 * @since 1.10
	 *
	 * @return string|false The action name or False if no action was selected
	 */
	public static function mla_current_bulk_action( )	{
		$action = false;

		if ( isset( $_REQUEST['action'] ) ) {
			if ( -1 != $_REQUEST['action'] ) {
				return $_REQUEST['action'];
			}

			$action = 'none';
		} // isset action

		if ( isset( $_REQUEST['action2'] ) ) {
			if ( -1 != $_REQUEST['action2'] ) {
				return $_REQUEST['action2'];
			}

			$action = 'none';
		} // isset action2

		return $action;
	}

	/**
	 * Get dropdown box of rule status values, i.e., Active/Inactive.
	 *
	 * @since 1.10
	 *
	 * @param string $selected Optional. Currently selected status. Default 'any'.
	 * @return string HTML markup for dropdown box.
	 */
	public static function mla_get_custom_field_status_dropdown( $selected = 'any' ) {
		$dropdown  = '<select name="mla_custom_feed_status" class="postform" id="name">' . "\n";

		$selected_attribute = ( $selected == 'any' ) ? ' selected="selected"' : '';
		$dropdown .= "\t" . sprintf( '<option value="any"%1$s>%2$s</option>', $selected_attribute, _wp_specialchars( 'Any Status' ) ) . "\n";

		$selected_attribute = ( $selected == 'active' ) ? ' selected="selected"' : '';
		$dropdown .= "\t" . sprintf( '<option value="active"%1$s>%2$s</option>', $selected_attribute, _wp_specialchars( 'Active' ) ) . "\n";

		$selected_attribute = ( $selected == 'inactive' ) ? ' selected="selected"' : '';
		$dropdown .= "\t" . sprintf( '<option value="inactive"%1$s>%2$s</option>', $selected_attribute, _wp_specialchars( 'Inactive' ) ) . "\n";

		$dropdown .= '</select>';

		return $dropdown;
	}

	/**
	 * Extra controls to be displayed between bulk actions and pagination
	 *
	 * Modeled after class-wp-posts-list-table.php in wp-admin/includes.
	 *
	 * @since 1.10
	 * 
	 * @param	string	'top' or 'bottom', i.e., above or below the table rows
	 *
	 * @return	void
	 */
	function extra_tablenav( $which ) {
		// Decide which actions to show
		if ( 'top' == $which ) {
			$actions = array( 'mla_custom_feed_status', 'mla_filter' );
		} else {
			$actions = array();
		}

		if ( empty( $actions ) ) {
			return;
		}

		echo ( '<div class="alignleft actions">' );

		foreach ( $actions as $action ) {
			switch ( $action ) {
				case 'mla_custom_feed_status':
					echo self::mla_get_custom_field_status_dropdown( isset( $_REQUEST['mla_custom_feed_status'] ) ? $_REQUEST['mla_custom_feed_status'] : 'any' );
					break;
				case 'mla_filter':
					submit_button( 'Filter', 'secondary', 'mla_filter', false, array( 'id' => 'template-query-submit' ) );
					break;
				default:
					// ignore anything else
			}
		}

		echo ( '</div>' );
	}

	/**
	 * Prepares the list of items for displaying
	 *
	 * This is where you prepare your data for display. This method will usually
	 * be used to query the database, sort and filter the data, and generally
	 * get it ready to be displayed. At a minimum, we should set $this->items and
	 * $this->set_pagination_args().
	 *
	 * @since 1.10
	 *
	 * @return	void
	 */
	function prepare_items( ) {
		$this->_column_headers = array(
			$this->get_columns(),
			$this->get_hidden_columns(),
			$this->get_sortable_columns() 
		);

		// REQUIRED for pagination.
		$total_items = MLA_Custom_Feed_Query::mla_count_custom_feed_settings( $_REQUEST );
		$user = get_current_user_id();
		$screen = get_current_screen();
		$option = $screen->get_option( 'per_page', 'option' );
		if ( is_string( $option ) ) {
			$per_page = get_user_meta( $user, $option, true );
		} else {
			$per_page = 10;
		}

		if ( empty( $per_page ) || $per_page < 1 ) {
			$per_page = $screen->get_option( 'per_page', 'default' );
		}

		// REQUIRED. We also have to register our pagination options & calculations.
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page' => $per_page, 
			'total_pages' => ceil( $total_items / $per_page )
		) );

		$current_page = $this->get_pagenum();

		/*
		 * REQUIRED. Assign sorted and paginated data to the items property, where 
		 * it can be used by the rest of the class.
		 */
		$this->items = MLA_Custom_Feed_Query::mla_query_custom_feed_settings( $_REQUEST, ( ( $current_page - 1 ) * $per_page ), $per_page );
	}

	/**
	 * Generates (echoes) content for a single row of the table
	 *
	 * @since 1.10
	 *
	 * @param object the current item
	 *
	 * @return void Echoes the row HTML
	 */
	function single_row( $item ) {
		static $row_class = '';
		$row_class = ( $row_class == '' ? ' class="alternate"' : '' );

		echo '<tr id="custom_feed-' . $item->post_ID . '"' . $row_class . '>';
		echo parent::single_row_columns( $item );
		echo '</tr>';
	}
} // class MLA_Custom_Feed_List_Table

/**
 * Class MLA (Media Library Assistant) Custom Feed Query implements the
 * searchable database of custom RSS feed settings.
 *
 * @package MLA Custom Feed Example
 * @since 1.10
 */
class MLA_Custom_Feed_Query {

	/**
	 * Callback to sort array by a 'name' key.
	 *
	 * @since 1.10
	 *
	 * @param array $a The first array.
	 * @param array $b The second array.
	 * @return integer The comparison result.
	 */
	private static function _sort_uname_callback( $a, $b ) {
		return strnatcasecmp( $a['name'], $b['name'] );
	}

	/**
	 * In-memory representation of the option settings, except "feeds"
	 *
	 * @since 1.10
	 *
	 * @var array $_settings {
	 *     @type boolean $add_mlafeeds Add active feeds to WordPress
	 *     }
	 */
	private static $_settings = NULL;

	/**
	 * One or more options have been changed since loading
	 *
	 * @since 1.10
	 *
	 * @var	boolean
	 */
	private static $_settings_changed = false;

	/**
	 * In-memory representation of the custom feed properties
	 *
	 * @since 1.10
	 *
	 * @var array $_custom_feed_settings {
	 *     Items by ID. Key $$ID is an index number starting with 1.
	 *
	 *     @type array $$ID {
	 *         Rule elements.
	 *
	 *         @type integer $post_ID Feed ID; equal to $$ID.
	 *         @type string $slug Feed slug.
	 *         @type string $type Feed type; 'rss', 'rss2', or 'rss-http'.
	 *         @type string $title Feed Title, default; get_wp_title_rss().
	 *         @type string $link The URL to the HTML website corresponding to the channel, default; get_bloginfo_rss('url').
	 *         @type string $description Feed Description, default; get_bloginfo_rss('description').
	 *         @type string $last_build_date Feed last update; 'current' or 'modified'.
	 *         @type integer $ttl Time-to-live (minutes) how long can the feed be cached, default; not added to feed.
	 *         @type string $update_period Period over which feed is updated; ( 'none', 'hourly' | 'daily' | 'weekly' | 'monthly' | 'yearly' ).
	 *         @type integer $update_frequency How often feed is updated within each period.
	 *         @type string $update_base Base date for the publishing schedule, e.g., 2000-01-01T12:00+00:00.
	 *         @type string $taxonomies Taxonomies from which to take "Category" values; default none.
	 *         @type string $parameters Data selection parameters for mla_get_shortcode_attachments, default; post_parent=all posts_per_page=6.
	 *         @type string $template_slug Theme "slug" argument for get_template_part().
	 *         @type string $template_name Theme "name" argument for get_template_part().
	 *         @type boolean $active True if feed should be added and processed.
	 *         @type boolean $changed True if the rule has changed since loading.
	 *         @type boolean $deleted True if the rule has been deleted since loading.
	 *     }
	 */
	private static $_custom_feed_settings = NULL;

	/**
	 * Highest existing custom feed setting ID value
	 *
	 * @since 1.10
	 *
	 * @var	integer
	 */
	private static $_custom_feed_highest_ID = 0;

	/**
	 * Default processing options
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $_default_settings = array (
						'add_mlafeeds' => true,
						'feeds' => array(
							1 => array(
									'slug' => 'mlafeed',
									'type' => 'rss-http',
									'title' => '',
									'link' => '',
									'description' => '',
									'last_build_date' => 'current',
									'ttl' => 0,
									'update_period' => 'hourly',
									'update_frequency' => 1,
									'update_base' => '2000-01-01T12:00+00:00',
									'taxonomies' =>  'attachment_category,attachment_tag',
									'parameters' => 'post_parent=all posts_per_page=6',
									'template_slug' => '', // 'rss',
									'template_name' => '', // 'custom-feed-template',
									'active' => true,
								 ), // mlafeed
						), // feeds
					);

	/**
	 * Assemble the in-memory representation of the custom feed settings
	 *
	 * @since 1.10
	 *
	 * @param boolean $force_refresh Optional. Force a reload of rules. Default false.
	 * @return boolean Success (true) or failure (false) of the operation
	 */
	private static function _get_custom_feed_settings( $force_refresh = false ) {
		if ( false == $force_refresh && NULL != self::$_custom_feed_settings ) {
			return true;
		}

		self::$_settings = array();
		self::$_settings_changed = false;
		self::$_custom_feed_settings = array();
		self::$_custom_feed_highest_ID = 0;
		$feed_slugs = array();
		$force_flush = false;
		// Update the plugin options from the wp_options table or set defaults
		$current_values = get_option( MLACustomFeedExample::SLUG_PREFIX . '-settings' );
//error_log( __LINE__ . ' MLA_Custom_Feed_Query::_get_custom_feed_settings get_option = ' . var_export( $current_values, true ), 0 );
		if ( !( is_array( $current_values ) && !empty( $current_values['feeds'] ) ) ) {
			$current_values = self::$_default_settings;
			// Rewrite rules must be reset to default feed names
			$force_flush = true;
//error_log( __LINE__ . ' MLA_Custom_Feed_Query::_get_custom_feed_settings defaults = ' . var_export( $current_values, true ), 0 );
		}

		foreach( $current_values['feeds'] as $current_value ) {
			$feed_slugs[ $current_value['slug'] ] = $current_value['slug'];
			$current_value['post_ID'] = ++self::$_custom_feed_highest_ID;
			$current_value['changed'] = false;
			$current_value['deleted'] = false;
			self::$_custom_feed_settings[ self::$_custom_feed_highest_ID ] = $current_value;
		}
//error_log( __LINE__ . ' MLA_Custom_Feed_Query::_get_custom_feed_settings _custom_feed_settings = ' . var_export( self::$_custom_feed_settings, true ), 0 );
		
		unset( $current_values['feeds'] );
		self::$_settings = $current_values;
		self::$_settings['feed_slugs'] = $feed_slugs;
//error_log( __LINE__ . ' MLA_Custom_Feed_Query::_get_custom_feed_settings _settings = ' . var_export( self::$_settings, true ), 0 );

		if ( $force_flush ) {
			self::flush_custom_feeds( $feed_slugs );
		}
		
		return true;
	}

	/**
	 * Flush the in-memory representation of the custom feed settings to the option value
	 *
	 * @since 1.10
	 *
	 * @return mixed Number of changes (integer) or failure (false) of the operation
	 */
	public static function mla_put_custom_feed_settings() {
		if ( NULL === self::$_custom_feed_settings ) {
			return false;
		}

		$new_settings = self::$_settings;
		unset( $new_settings['feed_slugs'] );
		$new_settings['feeds'] = array();
		$settings_changed = self::$_settings_changed ? 1 : 0;
		$feed_slugs = array();

		foreach( self::$_custom_feed_settings as $ID => $current_value ) {
			if ( $current_value['deleted'] ) {
				$settings_changed++;
				continue;
			}

			if ( $current_value['changed'] ) {
				$settings_changed++;
			}

			$new_value = $current_value;
			unset( $new_value['post_ID'] );
			unset( $new_value['changed'] );
			unset( $new_value['deleted'] );
			$new_settings['feeds'][] = $new_value;
			$feed_slugs[ $current_value['slug'] ] = $current_value['slug'];
		}

		if ( 0 < $settings_changed ) {
//error_log( __LINE__ . ' MLA_Custom_Feed_Query::mla_put_custom_feed_settings new_settings = ' . var_export( $new_settings, true ), 0 );
			if ( self::$_settings['feed_slugs'] !== $feed_slugs ) {
				self::flush_custom_feeds( $feed_slugs );
			}
			
			$update_result = update_option( MLACustomFeedExample::SLUG_PREFIX . '-settings', $new_settings, false );
			self::_get_custom_feed_settings( true );
			
			if ( $update_result ) {
				return $settings_changed;
			}
			
			return false;
		}
		
		return 0;
	}

	/**
	 * Add the custom feeds and then flush the rewrite rules
	 *
	 * @since 1.10
	 *
	 * @param array $slugs Custom feed slugs
	 */
	public static function flush_custom_feeds( $slugs ) {
		global $wp_rewrite;
//error_log( __LINE__ . ' MLA_Custom_Feed_Query::flush_custom_feeds slugs = ' . var_export( $slugs, true ), 0 );

		foreach ( $slugs as $slug ) {
			add_feed( $slug, 'MLACustomFeedExample::mla_custom_feed' );
		}

		$wp_rewrite->flush_rules( false );
	}

	/**
	 * Get a custom feed option setting
	 *
	 * @since 1.10
	 *
	 * @param string	$name Option name
	 *
	 * @return	mixed	Option value, if it exists else NULL
	 */
	public static function get_option( $name ) {
		if ( !self::_get_custom_feed_settings() ) {
			return NULL;
		}

		if ( !isset( self::$_settings[ $name ] ) ) {
			return NULL;
		}
		
		return self::$_settings[ $name ];
	}

	/**
	 * Update a custom feed option setting
	 *
	 * @since 1.10
	 *
	 * @param string $name Option name
	 * @param mixed	$new_value Option value
	 *
	 * @return mixed True if option value changed, false if value unchanged, NULL if failure
	 */
	public static function update_option( $name, $new_value ) {
		if ( !self::_get_custom_feed_settings() ) {
			return NULL;
		}

		$old_value = isset( self::$_settings[ $name ] ) ? self::$_settings[ $name ] : NULL;
		
		if ( $new_value === $old_value ) {
			return false;
		}
		
		self::$_settings[ $name ] = $new_value;
		self::$_settings_changed = true;

		return true;
	}

	/**
	 * Delete WordPress wp_options entry
	 *
	 * @since 1.10
	 *
	 * @return	boolean	True if reset worked else false
	 */
	public static function delete_settings() {
		delete_option( MLACustomFeedExample::SLUG_PREFIX . '-settings' ); 
		return self::_get_custom_feed_settings( true );
	}
		
	/**
	 * Sanitize and expand query arguments from request variables
	 *
	 * @since 1.10
	 *
	 * @param array	query parameters from web page, usually found in $_REQUEST
	 * @param int		Optional number of rows (default 0) to skip over to reach desired page
	 * @param int		Optional number of rows on each page (0 = all rows, default)
	 *
	 * @return	array	revised arguments suitable for query
	 */
	private static function _prepare_custom_feed_settings_query( $raw_request, $offset = 0, $count = 0 ) {
		/*
		 * Go through the $raw_request, take only the arguments that are used in the query and
		 * sanitize or validate them.
		 */
		if ( !is_array( $raw_request ) ) {
			error_log( __LINE__ . ' ERROR: MLA_Custom_Feed_Query::_prepare_custom_feed_settings_query non-array raw_request = ' .  var_export( $raw_request, true ), 0 );
			return NULL;
		}

		$clean_request = array (
			'mla_custom_feed_view' => 'all',
			'mla_custom_feed_status' => 'any',
			'orderby' => 'none',
			'order' => 'ASC',
			's' => ''
		);

		foreach ( $raw_request as $key => $value ) {
			switch ( $key ) {
				case 'mla_custom_feed_view':
				case 'mla_custom_feed_status':
					$clean_request[ $key ] = $value;
					break;
				case 'orderby':
					if ( 'none' == $value ) {
						$clean_request[ $key ] = $value;
					} else {
						if ( array_key_exists( $value, MLA_Custom_Feed_List_Table::mla_get_sortable_columns() ) ) {
							$clean_request[ $key ] = $value;
						}
					}
					break;
				case 'order':
					switch ( $value = strtoupper ($value ) ) {
						case 'ASC':
						case 'DESC':
							$clean_request[ $key ] = $value;
							break;
						default:
							$clean_request[ $key ] = 'ASC';
					}
					break;
				// ['s'] - Search items by one or more keywords
				case 's':
					$clean_request[ $key ] = stripslashes( trim( $value ) );
					break;
				default:
					// ignore anything else in $_REQUEST
			} // switch $key
		} // foreach $raw_request

		// Ignore incoming paged value; use offset and count instead
		if ( ( (int) $count ) > 0 ) {
			$clean_request['offset'] = $offset;
			$clean_request['posts_per_page'] = $count;
		}

		return $clean_request;
	}

	/**
	 * Query the plugin_examples items
	 *
	 * @since 1.10
	 *
	 * @param array	query parameters from web page, usually found in $_REQUEST
	 *
	 * @return	array	query results; array of MLA post_mime_type objects
	 */
	private static function _execute_custom_feed_settings_query( $request ) {
		if ( !self::_get_custom_feed_settings() ) {
			return array ();
		}

		// Sort and filter the list
		$keywords = isset( $request['s'] ) ? $request['s'] : '';
		preg_match_all('/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $keywords, $matches);
		$keywords = array_map( 'MLAQuery::mla_search_terms_tidy', $matches[0]);
		$view = isset( $request['mla_custom_feed_view'] ) ? $request['mla_custom_feed_view'] : 'all';
		$status = isset( $request['mla_custom_feed_status'] ) ? $request['mla_custom_feed_status'] : 'any';
		$index = 0;
		$sortable_items = array();

		foreach ( self::$_custom_feed_settings as $ID => $value ) {
			if ( !empty( $keywords ) ) {
				$found = false;
				foreach ( $keywords as $keyword ) {
					$found |= false !== stripos( $value['slug'], $keyword );
					$found |= false !== stripos( $value['title'], $keyword );
					$found |= false !== stripos( $value['description'], $keyword );
					$found |= false !== stripos( $value['taxonomies'], $keyword );
					$found |= false !== stripos( $value['parameters'], $keyword );
					$found |= false !== stripos( $value['template_slug'], $keyword );
					$found |= false !== stripos( $value['template_name'], $keyword );
				}

				if ( !$found ) {
					continue;
				}
			}

			switch( $view ) {
				case 'all':
				default:
					$found = true;
			}// $view

			if ( !$found ) {
				continue;
			}

			switch( $status ) {
				case 'active':
					$found = $value['active'];
					break;
				case 'inactive':
					$found = !$value['active'];
					break;
				default:
					$found = true;
			}// $view

			if ( !$found ) {
				continue;
			}

			switch ( $request['orderby'] ) {
				case 'slug':
				case 'type':
				case 'title':
				case 'link':
				case 'description':
				case 'last_build_date':
				case 'update_period':
				case 'update_base':
				case 'taxonomies':
				case 'parameters':
				case 'template_slug':
				case 'template_name':
					$sortable_items[ ( empty( $value[ $request['orderby'] ] ) ? chr(1) : $value[ $request['orderby'] ] ) . $ID ] = (object) $value;
					break;
				case 'ttl':
				case 'update_frequency':
					$sortable_items[ ( empty( $value[ $request['orderby'] ] ) ? 0 : absint( $value[ $request['orderby'] ] ) * 100 ) + absint( $ID ) ] = (object) $value;
					break;
				case 'status':
					$sortable_items[ ( $value['active'] ? 'Active' : 'Inactive' ) . $ID ] = (object) $value;
					break;
				default:
					$sortable_items[ absint( $ID ) ] = (object) $value;
					break;
			} //orderby
		}

		$sorted_items = array();
		$sorted_keys = array_keys( $sortable_items );
		natcasesort( $sorted_keys );
		foreach ( $sorted_keys as $key ) {
			$sorted_items[] = $sortable_items[ $key ];
		}

		if ( 'DESC' == $request['order'] ) {
			$sorted_items = array_reverse( $sorted_items, true );
		}

		// Paginate the sorted list
		$results = array();
		$offset = isset( $request['offset'] ) ? $request['offset'] : 0;
		$count = isset( $request['posts_per_page'] ) ? $request['posts_per_page'] : -1;
		foreach ( $sorted_items as $value ) {
			if ( $offset ) {
				$offset--;
			} elseif ( $count-- ) {
				$results[] = $value;
			} else {
				break;
			}
		}

		return $results;
	}

	/**
	 * Get the total number of MLA custom_feed objects
	 *
	 * @since 1.10
	 *
	 * @param array	Query variables, e.g., from $_REQUEST
	 *
	 * @return	integer	Number of MLA custom_feed objects
	 */
	public static function mla_count_custom_feed_settings( $request ) {
		$request = self::_prepare_custom_feed_settings_query( $request );
		$results = self::_execute_custom_feed_settings_query( $request );
		return count( $results );
	}

	/**
	 * Retrieve MLA custom_feed objects for list table display
	 *
	 * @since 1.10
	 *
	 * @param array	query parameters from web page, usually found in $_REQUEST
	 * @param int		number of rows to skip over to reach desired page
	 * @param int		number of rows on each page
	 *
	 * @return	array	MLA custom_feed objects
	 */
	public static function mla_query_custom_feed_settings( $request, $offset, $count ) {
		$request = self::_prepare_custom_feed_settings_query( $request, $offset, $count );
		$results = self::_execute_custom_feed_settings_query( $request );
		return $results;
	}

	/**
	 * Find a custom feed ID given its slug
	 *
	 * @since 1.10
 	 *
	 * @param string $slug MLA custom feed slug.
	 * @return integer Feed ID if the feed exists else zero (0).
	 */
	public static function mla_find_custom_feed_ID( $slug ) {
		if ( !self::_get_custom_feed_settings() ) {
			return false;
		}

		foreach( self::$_custom_feed_settings as $ID => $feed ) {
			if ( $slug === $feed['slug'] ) {
				return $ID;
			}
		}

		return 0;
	}

	/**
	 * Return the custom field slugs
	 *
	 * @since 1.10
 	 *
	 * @param string $status Optional. 'active', 'inactive' or 'any' (default).
	 * @return	array	MLA custom_feed ID => slug
	 */
	public static function mla_custom_feed_slugs( $status = 'any' ) {
		$slugs = array();

		if ( !self::_get_custom_feed_settings() ) {
			return $slugs;
		}

		foreach( self::$_custom_feed_settings as $ID => $feed ) {
			if ( $feed['deleted'] ) {
				continue;
			}
			
			if ( $feed['active'] ) {
				if ( 'inactive' === $status ) {
					continue;
				}
			} else {
				if ( 'active' === $status ) {
					continue;
				}
			}
			
			$slugs[ $ID ] = $feed['slug'];
		}

		return $slugs;
	}

	/**
	 * Find a custom feed given its ID
	 *
	 * @since 1.10
 	 *
	 * @param integer	$ID MLA custom feed ID
 	 *
	 * @return	array	MLA custom_feed array
	 * @return	boolean	false; MLA custom_feed does not exist
	 */
	public static function mla_find_custom_feed( $ID ) {
		if ( !self::_get_custom_feed_settings() ) {
			return false;
		}

		if ( isset( self::$_custom_feed_settings[ $ID ] ) ) {
			return self::$_custom_feed_settings[ $ID ];
		}

		return false;
	}

	/**
	 * Update a custom feed property given its ID and key.
	 *
	 * @since 1.10
 	 *
	 * @param integer $ID MLA custom feed ID.
	 * @param string $key MLA custom feed property.
	 * @param string $value MLA custom feed new value.
	 * @return mixed true if property changed, false if not or NULL if feed or property not found.
	 */
	public static function mla_update_custom_feed( $ID, $key, $value ) {
		if ( !self::_get_custom_feed_settings() ) {
			return NULL;
		}

		if ( !isset( self::$_custom_feed_settings[ $ID ] ) || !isset( self::$_custom_feed_settings[ $ID ][ $key ] ) ) {
			return NULL;
		}

		if ( self::$_custom_feed_settings[ $ID ][ $key ] === $value ) {
			return false;
		}

		self::$_custom_feed_settings[ $ID ][ $key ] = $value;
		self::$_custom_feed_settings[ $ID ]['changed'] = true;
		return true;
	}

	/**
	 * Replace a custom feed given its value array.
	 *
	 * @since 1.10
 	 *
	 * @param array $value MLA custom feed new value.
	 * @return boolean true if object exists else false.
	 */
	public static function mla_replace_custom_feed( $value ) {
		if ( !self::_get_custom_feed_settings() ) {
			return false;
		}

		if ( isset( self::$_custom_feed_settings[ $value['post_ID'] ] ) ) {
			self::$_custom_feed_settings[ $value['post_ID'] ] = $value;
			return true;
		}

		return false;
	}

	/**
	 * Insert a custom feed given its value array.
	 *
	 * @since 1.10
 	 *
	 * @param array $value MLA custom feed new value.
	 * @return boolean true if addition succeeds else false.
	 */
	public static function mla_add_custom_feed( $value ) {
		if ( !self::_get_custom_feed_settings() ) {
			return false;
		}

		$value['post_ID'] = ++self::$_custom_feed_highest_ID;
		$value['changed'] = true;
		$value['deleted'] = false;

		self::$_custom_feed_settings[ $value['post_ID'] ] = $value;
		return true;
	}

	/**
	 * Tabulate MLA custom_feed objects by view for list table display
	 *
	 * @since 1.10
	 *
	 * @param string	keyword search criterion, optional
	 *
	 * @return	array	( 'singular' label, 'plural' label, 'count' of items )
	 */
	public static function mla_tabulate_custom_feed_items( $s = '' ) {
		if ( empty( $s ) ) {
			$request = array( 'mla_custom_feed_view' => 'all' );
		} else {
			$request = array( 's' => $s );
		}

		$items = self::mla_query_custom_feed_settings( $request, 0, 0 );

		$template_items = array(
			'all' => array(
				'singular' => 'All',
				'plural' => 'All',
				'count' => 0 ),
		);

		foreach ( $items as $value ) {
			$template_items['all']['count']++;
		}

		return $template_items;
	}
} // class MLA_Custom_Feed_Query

// Install the filters at an early opportunity
add_action('init', 'MLACustomFeedExample::initialize');
/*
 * Actions are added here, when the source file is loaded, because the mla_compose_general_tab
 * function is called too late to be useful.
 */
add_action( 'admin_init', 'MLA_Custom_Feed_List_Table::mla_admin_init' );
?>
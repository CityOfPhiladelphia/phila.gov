<?php
/**
 * The Smart Media Categories (SMC) Plugin, Admin Mode.
 *
 * @package   Smart_Media_Categories_Admin
 * @author    David Lingren <dlingren@comcast.net>
 * @license   GPL-2.0+
 * @link      @TODO http://example.com
 * @copyright 2014 David Lingren
 */

/*
 * Include support classes
 */
require_once( 'includes/class-smc-sync-support.php' );
require_once( 'includes/class-smc-settings-support.php' );
SMC_Settings_Support::initialize();
require_once( 'includes/class-smc-automatic-support.php' );
SMC_Automatic_Support::initialize();

/**
 * Plugin class. This class works with the
 * administrative-side of the WordPress site.
 *
 * @package Smart_Media_Categories_Admin
 * @author  David Lingren <dlingren@comcast.net>
 */
class Smart_Media_Categories_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = NULL;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = NULL;

	/**
	 * Slug of the current 'edit-' Post Type.
	 *
	 * @since    1.0.9
	 *
	 * @var      string
	 */
	protected $current_edit_type = NULL;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {
		// Call $plugin_slug from public plugin class.
		$plugin = Smart_Media_Categories::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();
		$this->plugin_object = $plugin->get_plugin_object();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		/*
		 * Define custom functionality.
		 *
		 * Read more about actions and filters:
		 * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 * /

if ( isset( $_REQUEST['action'] ) ) {
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::__construct() action = ' . var_export( $_REQUEST['action'], true ), 0 );
if ( 'heartbeat' != $_REQUEST['action'] ) {
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::__construct() $_REQUEST = ' . var_export( $_REQUEST, true ), 0 );
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::__construct() $_GET = ' . var_export( $_GET, true ), 0 );
}
} // */

		// Determine Post Type, if possible
		if ( isset( $_REQUEST['post_type'] ) ) {		
			$this->current_edit_type = $_REQUEST['post_type'];
		}
		
		// Handle Ajax requests
		if ( defined('DOING_AJAX') && DOING_AJAX ) {
			add_action( 'wp_ajax_' . 'smc_find_posts', array( $this, 'action_wp_ajax_smc_find_posts' ) );

			if ( isset( $_REQUEST['screen'] ) && ( 'edit-post' == $_REQUEST['screen'] ) && isset( $_REQUEST['action'] ) && ( 'inline-save' == $_REQUEST['action'] ) ) {
				add_filter( "manage_posts_columns", array( $this, 'filter_manage_posts_columns' ), 10, 1 );
				add_action( "manage_posts_custom_column", array( $this, 'action_manage_posts_custom_column' ), 10, 2 );
			}
			
			return;
		} // DOING_AJAX
		
		// Process SMC-specific actions
		if ( isset( $_REQUEST['action'] ) ) {
			if ( in_array( $_REQUEST['action'], array( 'smc_posts_modal', 'smc_pages_modal' ) ) ) {
				$action = 'action_' . $_REQUEST['action'];
				add_action( 'admin_init', array( $this, $action ) ); 
			} elseif ( isset( $_REQUEST['sync_all_bulk'] ) ) {
				add_action( 'admin_init', array( $this, 'action_sync_all_bulk' ) ); 
			} elseif ( isset( $_REQUEST['sync_all_filter'] ) ) {
				add_action( 'admin_init', array( $this, 'action_sync_all_filter' ) ); 
			}
		}
		
		/*
		 * Intercept Posts/All Posts and Pages/All Pages submenus
		 */
		add_action( "load-edit.php", array( $this, 'action_load_edit_php' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {
		/*
		 * - Uncomment following lines if the admin class should only be available for super admins
		 * /
		if( ! is_super_admin() ) {
			return;
		} // */

		// If the single instance hasn't been set, set it now.
		if ( NULL == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    NULL    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {
		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		
		/*
		 * The All Posts/All Pages modal windows share styles at this time, but
		 * provide for different styles in the future.
		 */
		$is_smc_type = false;
		if ( 0 === strpos( $screen->id, 'edit-' ) ) {
			$is_smc_type = SMC_Settings_Support::is_smc_post_type( substr( $screen->id, 5 ) );
		}
		
		if ( $is_smc_type ) {
			// All Posts
			wp_enqueue_style( $this->plugin_slug .'-posts-pages-styles', plugins_url( 'assets/css/smc-posts-pages.css', __FILE__ ), array(), Smart_Media_Categories::VERSION );
		} elseif ( 'edit-page' == $screen->id ) {
			// All Pages
			wp_enqueue_style( $this->plugin_slug .'-posts-pages-styles', plugins_url( 'assets/css/smc-posts-pages.css', __FILE__ ), array(), Smart_Media_Categories::VERSION );
		} elseif ( $this->plugin_screen_hook_suffix == $screen->id ) {
			// Settings/Smart Media Categories
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/smc-admin.css', __FILE__ ), array(), Smart_Media_Categories::VERSION );
		}
	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    NULL    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {
		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		$screen = get_current_screen();
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::enqueue_admin_scripts $screen = ' . var_export( $screen, true ), 0 );

		if ( version_compare( get_bloginfo( 'version' ), '3.8', '>=' ) ) {
			$useDashicons = true;
		} else {
			$useDashicons = false;
		}

		$is_smc_type = false;
		if ( 0 === strpos( $screen->id, 'edit-' ) ) {
			$is_smc_type = SMC_Settings_Support::is_smc_post_type( substr( $screen->id, 5 ) );
		}
		
		if ( $is_smc_type ) {
			/*
			 * Posts/All Posts submenu
			 */
			wp_enqueue_script( $this->plugin_slug . 'find-posts-script', plugins_url( "assets/js/smc-find-posts{$suffix}.js", __FILE__ ), array( 'jquery' ), Smart_Media_Categories::VERSION );

			$script_variables = array(
				'comma' => _x( ',', 'tag_delimiter', 'smart-media-categories' ),
				'ajaxFailError' => __( 'An ajax.fail error has occurred. Please reload the page and try again.', 'smart-media-categories' ),
				'ajaxDoneError' => __( 'An ajax.done error has occurred. Please reload the page and try again.', 'smart-media-categories' ),
				'ntDelTitle' => __( 'Remove from sync list.', 'smart-media-categories' ),
				'syncAllChildren' => __( 'Sync All Children', 'smart-media-categories' ),
				'noChildren' => __( 'None - no children selected', 'smart-media-categories' ),
				'useDashicons' => $useDashicons,
			);

			wp_localize_script( $this->plugin_slug . 'find-posts-script', $this->plugin_object . '_posts_settings', $script_variables );
			
			wp_enqueue_script( $this->plugin_slug . 'sync-posts-script', plugins_url( "assets/js/smc-sync-posts{$suffix}.js", __FILE__ ), array( 'suggest', 'jquery', $this->plugin_slug . 'find-posts-script' ), Smart_Media_Categories::VERSION );

			/*
			 * Add the "Select Parent" popup window
			 */
			add_action( 'admin_footer', array( $this, 'action_posts_admin_footer' ) );
		} elseif ( 'edit-page' == $screen->id ) {
			/*
			 * Pages/All Pages submenu
			 */
			wp_enqueue_script( $this->plugin_slug . 'find-posts-script', plugins_url( "assets/js/smc-find-posts{$suffix}.js", __FILE__ ), array( 'jquery' ), Smart_Media_Categories::VERSION );

			$script_variables = array(
				'comma' => _x( ',', 'tag_delimiter', 'smart-media-categories' ),
				'ajaxFailError' => __( 'An ajax.fail error has occurred. Please reload the page and try again.', 'smart-media-categories' ),
				'ajaxDoneError' => __( 'An ajax.done error has occurred. Please reload the page and try again.', 'smart-media-categories' ),
				'syncAllChildren' => __( 'Sync All Children', 'smart-media-categories' ),
				'noChildren' => __( 'None - no children selected', 'smart-media-categories' ),
				'useDashicons' => $useDashicons,
			);

			wp_localize_script( $this->plugin_slug . 'find-posts-script', $this->plugin_object . '_posts_settings', $script_variables );
			
			/*
			 * Add the "Select Parent" popup window
			 */
			add_action( 'admin_footer', array( $this, 'action_pages_admin_footer' ) );
		} elseif ( $this->plugin_screen_hook_suffix == $screen->id ) {
			/*
			 * Settings/Smart Media Categories page
			 */
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/smc-admin.js', __FILE__ ), array( 'jquery' ), Smart_Media_Categories::VERSION );
		}
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		/*
		 * Add a settings page for this plugin to the Settings menu.
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			/* translators: 1: version number */
			sprintf( __( 'Smart Media Categories v%1$s Settings', 'smart-media-categories' ), Smart_Media_Categories::VERSION ),
			__( 'Smart Media Categories', 'smart-media-categories' ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'render_plugin_admin_page' )
		);

		SMC_Settings_Support::initialize_settings_page( $this->plugin_slug );
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function render_plugin_admin_page() {
		if ( isset( $_REQUEST['action'] ) && 'update' == $_REQUEST['action'] ) {
			$active_tab =  SMC_Settings_Support::get_active_tab();
			$options = isset( $_REQUEST[ $active_tab ] ) ? $_REQUEST[ $active_tab ] : array();
			
			if ( 'smc_automatic_options' == $active_tab ) {
				SMC_Settings_Support::validate_automatic_options( $options );
			}
		}
		
		include_once( 'views/smc-admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {
		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', 'smart-media-categories' ) . '</a>'
			),
			$links
		);
	}

	/*
	 * NOTE:     Actions are points in the execution of a page or process
	 *           lifecycle that WordPress fires.
	 *
	 *           Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * NOTE:     Filters are points of execution in which WordPress modifies data
	 *           before saving it or sending it to the browser.
	 *
	 *           Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 */

	/**
	 * Add the "Children" column to the Posts/All Posts and Pages/All Pages submenus
	 *
	 * @since    1.0.0
	 *
	 *
	 * @return	void	echoes HTML markup for the Attach popup
	 */
	public function action_load_edit_php() {
		$screen = get_current_screen();
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_load_edit_php $screen = ' . var_export( $screen, true ), 0 );

		$this->current_edit_type = isset( $screen->post_type ) ? $screen->post_type : 'post';

		/*
		 * filters/actions are in /wp-admin/includes/class-wp-posts-list-table.php
		 * filter "views_{$this->screen->id}" is in /wp-admin/includes/class-wp-list-table.php
		 */
		if ( 'page' === $this->current_edit_type ) {
			// Add the "Children" column to the Pages/All Pages submenus
			add_filter( "manage_pages_columns", array( $this, 'filter_manage_pages_columns' ), 10, 1 );
			add_action( "manage_pages_custom_column", array( $this, 'action_manage_pages_custom_column' ), 10, 2 );
		} elseif ( SMC_Settings_Support::is_smc_post_type( $this->current_edit_type ) ) {
			// Add the Sync and Unsync views to the Posts/All Posts submenu
			add_filter( "views_edit-{$this->current_edit_type}", array( $this, 'filter_views_edit_post' ), 10, 1 );

			// Add the Smart Media rollover action to the Posts/All Posts items
			add_filter( "post_row_actions", array( $this, 'filter_post_row_actions' ), 10, 2 );
			
			// Add the "Children" column to the Posts/All Posts and Pages/All Pages submenus
			add_filter( "manage_posts_columns", array( $this, 'filter_manage_posts_columns' ), 10, 1 );
			add_action( "manage_posts_custom_column", array( $this, 'action_manage_posts_custom_column' ), 10, 2 );
			
			// Add support for filtering on Sync/Unsync status
			add_filter( 'posts_clauses', array( $this, 'filter_posts_clauses' ), 10, 2 );
			
			if ( isset( $_REQUEST['smc_message'] ) ) {
				add_filter( 'bulk_post_updated_messages', array( $this, 'filter_bulk_post_updated_messages' ), 10, 2 );
			}
		} // is_smc_post_type
	}

	/**
	 * $clauses = (array) apply_filters_ref_array( 'posts_clauses', array( compact( $pieces ), &$this ) );
	 *
	 * @since    1.0.2
	 *
	 * @param	array		The list of clauses for the query.
	 * @param	WP_Query	The WP_Query instance (passed by reference).
	 *
	 * @return	array		updated list of clauses for the query
	 */
	public function filter_posts_clauses( $clauses, $wp_query ) {
		global $wpdb;
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::filter_posts_clauses $clauses = ' . var_export( $clauses, true ), 0 );
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::filter_posts_clauses query_type = ' . var_export( $wp_query->query_vars['post_type'], true ), 0 );
		if ( isset( $_REQUEST['smc_status'] ) && ( $wp_query->query_vars['post_type'] == $this->current_edit_type ) ) {
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::filter_posts_clauses $this->current_edit_type = ' . var_export( $this->current_edit_type, true ), 0 );
			$posts = implode( ',', SMC_Sync_support::get_posts_per_view( array( 'post_type' => $this->current_edit_type, 'smc_status' => $_REQUEST['smc_status'] ) ) );
			if ( empty( $posts ) ) {
				$posts = '0';
			}
			
			$clauses['where'] = " AND {$wpdb->posts}.ID IN ( " . $posts . ' )' . $clauses['where'];
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::filter_posts_clauses updated $clauses[where] = ' . var_export( $clauses['where'], true ), 0 );
		}
		
		return $clauses;
	}

	/**
	 * Add the "Smart Media" action to the Posts/All Posts submenu rollover actions
	 *
	 * @since    1.0.0
	 *
	 * @param array   An array of row action links
	 * @param WP_Post The post object
	 *
	 * @return	array	updated array of row action links
	 */
	public function filter_post_row_actions( $actions, $post ) {
		if (  SMC_Settings_Support::is_smc_post_type( $post->post_type ) ) {
			$actions['smart hide-if-no-js'] = sprintf( '<a href="#" onclick="smc.syncPosts.open( \'%1$s\' ); return false;" title="Sync terms">Smart&nbsp;Media</a>', $post->ID );
		}
		
		return $actions;
	}

	/**
	 * Add the "Children" column to the Posts/All Posts submenu
	 *
	 * @since    1.0.0
	 *
	 * @param	array	column definitions for the All Posts submenu table
	 *
	 * @return	array	updated column definitions for the All Posts submenu table
	 */
	public function filter_manage_posts_columns( $columns ) {
		$columns['smc_children'] = __( 'Children', 'smart-media-categories' );
		return $columns;
	}

	/**
	 * Compose <div> tags for term assignments
	 *
	 * @since 1.0.0
	 * 
	 * @param	integer	ID of the parent object
	 * @param	object	taxonomy object
	 * @param	array	object => term id and slug assignments
	 *
	 * @return	string	HTML <div> tags with assignment data
	 */
	private function _build_term_assignments( $post_id, &$tax_object, &$term_assignments ) {
		$tax_name = $tax_object->name;
		
		if ( isset( $term_assignments[ $post_id ][ $tax_name ] ) ) {
			$terms = $term_assignments[ $post_id ][ $tax_name ];
		} else {
			$terms = array();
		}
	
		$ids = array();
		if ( $tax_object->hierarchical ) {
			foreach( $terms as $term ) {
				$ids[] = $term['id'];
			}
	
			return '	<div class="smc-categories" id="' . $tax_name . '-' . $post_id . '">'
				. implode( ',', $ids ) . "</div>\n";
		} else {
			foreach( $terms as $term ) {
				$ids[] = $term['slug'];
			}
	
			return '	<div class="smc-tags" id="' . $tax_name. '-' .$post_id . '">'
				. esc_attr( implode( ', ', $ids ) ) . "</div>\n";
		}
	}

	/**
	 * Add hidden fields with the data for use in the "Smart Media Categories" popup
	 *
	 * @since 1.0.0
	 * 
	 * @param	object	The parent object
	 * @param	array	IDs of the children
	 * @param	array	object => term id and slug assignments
	 *
	 * @return	string	HTML <div> with row data
	 */
	private function _build_inline_data( $parent, $children, $term_assignments ) {
		$inline_data = "\n" . '<div class="hidden smc-inline" id="smc-inline-' . $parent->ID . "\">\n";
		$inline_data .= '<div class="smc-inline-post-title" id="post-title-' . $parent->ID . '">' . esc_html( $parent->post_title ) . "</div>\n";
		$taxonomies = get_object_taxonomies( 'attachment', 'objects' );
		$post_taxonomies = SMC_Settings_Support::smc_taxonomies();

		foreach ( $taxonomies as $tax_name => $tax_object ) {
			if ( $tax_object->show_ui && in_array( $tax_name, $post_taxonomies ) ) {
				$inline_data .= $this->_build_term_assignments( $parent->ID, $tax_object, $term_assignments );
			}
		}

		$inline_data .= "</div>\n";
		return $inline_data;
	}

	/**
	 * Returns HTML markup for one view that can be used with this table
	 *
	 * @since 1.0.2
	 *
	 * @param	string	View slug 
	 * @param	string	Slug for current view 
	 * 
	 * @return	string | false	HTML for link to display the view, false if count = zero
	 */
	function _get_view( $view_slug, $current_view ) {
		global $wpdb;
		static $posts_per_view = NULL,
			$view_singular = array (),
			$view_plural = array ();
		/*
		 * Calculate the common values once per page load
		 */
		if ( is_null( $posts_per_view ) ) {
			$posts_per_view = SMC_Sync_Support::get_posts_per_view( array( 'post_type' => $this->current_edit_type ) );
			$view_singular = array (
				'sync' => __( 'Synced', 'smart-media-categories' ),
				'unsync' => __( 'Unsynced', 'smart-media-categories' ),
			);
			$view_plural = array (
				'sync' => __( 'Synced', 'smart-media-categories' ),
				'unsync' => __( 'Unsynced', 'smart-media-categories' ),
			);
		}

		/*
		 * Make sure the slug is in our list and has posts
		 */
		if ( array_key_exists( $view_slug, $posts_per_view ) ) {
			$post_count = $posts_per_view[ $view_slug ];
			$singular = sprintf('%s <span class="count">(%%s)</span>', $view_singular[ $view_slug ] );
			$plural = sprintf('%s <span class="count">(%%s)</span>', $view_plural[ $view_slug ] );
			$nooped_plural = _n_noop( $singular, $plural, 'smart-media-categories' );
		} else {
			return false;
		}

		if ( $post_count ) {
			$query = array( 'smc_status' => $view_slug );
			$base_url = "edit.php?post_type={$this->current_edit_type}";
			$class = ( $view_slug == $current_view ) ? ' class="current"' : '';
			
			return "<a href='" . add_query_arg( $query, $base_url ) . "'$class>" . sprintf( translate_nooped_plural( $nooped_plural, $post_count, 'smart-media-categories' ), number_format_i18n( $post_count ) ) . '</a>';
		}

		return false;
	}

	/**
	 * Add the "Synced/Unsynced" views to the Posts/All Posts submenu
	 *
	 * This filter is applied in /wp-admin/includes/class-wp-list-table.php
	 *
	 * @since    1.0.2
	 *
	 * @param	array	column definitions for the All Posts submenu table
	 *
	 * @return	array	updated column definitions for the All Posts submenu table
	 */
	public function filter_views_edit_post( $views ) {
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::filter_views_edit_post $views = ' . var_export( $views, true ), 0 );

		if ( isset( $_REQUEST['smc_status'] ) ) {
			switch( $_REQUEST['smc_status'] ) {
				case 'sync':
					$current_view = 'sync';
					break;
				case 'unsync':
					$current_view = 'unsync';
					break;
				default:
					$current_view = '';
			} // smc_status
		} else {
			$current_view = '';
		}
		
		foreach ( $views as $slug => $view ) {
			// Find/update the current view
			if ( strpos( $view, ' class="current"' ) ) {
				if ( ! empty( $current_view ) ) {
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::filter_views_edit_post before = ' . var_export( $views[ $slug ], true ), 0 );
					$views[ $slug ] = str_replace( ' class="current"', '', $view );
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::filter_views_edit_post after = ' . var_export( $views[ $slug ], true ), 0 );
				} else {
					$current_view = $slug;
				}
			}
		} // each view
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::filter_views_edit_post $current_view = ' . var_export( $current_view, true ), 0 );
		
		if ( SMC_Settings_Support::is_smc_post_type( $this->current_edit_type ) ) {
			$value = $this->_get_view( 'sync', $current_view );
			if ( $value ) {
				$views['sync'] = $value;
			}
			
			$value = $this->_get_view( 'unsync', $current_view );
			if ( $value ) {
				$views['unsync'] = $value;
			}
		}
		
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::filter_views_edit_post updated $views = ' . var_export( $views, true ), 0 );
		return $views;
	}

	/**
	 * Render the "Children" column for the Posts/All Posts submenu
	 *
	 * @since    1.0.0
	 *
	 * @param	array	name of the column
	 * @param	array	ID of the post for which the content is desired
	 *
	 * @return	void	echoes HTML markup for the column content
	 */
	public function action_manage_posts_custom_column( $column_name, $post_id ) {
//		global $wp, $wp_query, $wp_the_query;
		global $post;
		static $smc_status = NULL;
//error_log( __LINE__ . " Smart_Media_Categories_Admin::action_manage_posts_custom_column( {$column_name}, {$post_id} )", 0 );

//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_manage_posts_custom_column wp = ' . var_export( $wp, true ), 0 );
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_manage_posts_custom_column wp_query = ' . var_export( $wp_query, true ), 0 );
		
		if ( 'smc_children' != $column_name ) {
			return;
		}
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_manage_posts_custom_column post = ' . var_export( $post, true ), 0 );

		// Retain the view when the Year/Month or Categories are set and "Filter" is clicked.
		if ( NULL == $smc_status && isset( $_REQUEST['smc_status'] ) ) {
			$smc_status = $_REQUEST['smc_status'];
			echo '<input type="hidden" name="smc_status" class="smc_status_page" value="' . $smc_status . '" />';
		}

		$args = array( 'post_parent' => $post_id, 'post_type' => 'attachment', 'post_status' => 'inherit' );
		$children = get_children( $args );
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_manage_posts_custom_column array_keys( $children ) = ' . var_export( array_keys( $children ), true ), 0 );
		$term_assignments = SMC_Sync_Support::get_terms( $post_id, array_keys( $children ) );
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_manage_posts_custom_column term_assignments = ' . var_export( $term_assignments, true ), 0 );
		if ( $children ) {
			$threshold = SMC_Settings_Support::get_option( 'scroll_threshold' );
			$count = count( $children );
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_manage_posts_custom_column $count = ' . var_export( $count, true ), 0 );
			if ( $threshold <= $count ) {
				$height = SMC_Settings_Support::get_option( 'scroll_height' );
				echo "<div style=\"width: 100%; height: {$height}; overflow: auto\">\n";
			}
			
			ksort( $children);
			foreach ($children as $child_id => $child ) {
				$sync_class = ( $term_assignments[ $child_id ]['smc_sync'] ) ? 'smc-sync-true' : 'smc-sync-false';
				
				echo sprintf( '<a class="hide-if-no-js %1$s" id="smc-child-%3$s" onclick="smc.findPosts.open( \'%2$s\',\'%3$s\',\'%5$s\' ); return false;" href="%4$s#the-list">%5$s (%3$s)</a><br>', /*%1$s*/ $sync_class, /*%2$s*/ $post_id, /*%3$s*/ $child->ID, /*%4$s*/ admin_url( 'edit.php' ), /*%5$s*/ esc_attr( $child->post_title ) );
			}

			if ( $threshold <= $count ) {
				echo "</div>\n";
			}
		} else {
			echo __( 'No Children', 'smart-media-categories' );
		}
		
		echo $this->_build_inline_data( $post, $children, $term_assignments );
	}

	/**
	 * Add the "Children" column to the Pages/All Pages submenu
	 *
	 * @since    1.0.0
	 *
	 * @param	array	column definitions for the All Posts submenu table
	 *
	 * @return	array	updated column definitions for the All Posts submenu table
	 */
	public function filter_manage_pages_columns( $columns ) {
		$columns['smc_children'] = __( 'Children', 'smart-media-categories' );
		return $columns;
	}

	/**
	 * Render the "Children" column for the Pages/All Pages submenu
	 *
	 * @since    1.0.0
	 *
	 * @param	array	name of the column
	 * @param	array	ID of the post for which the content is desired
	 *
	 * @return	void	echoes HTML markup for the column content
	 */
	public function action_manage_pages_custom_column( $column_name, $post_id ) {
//error_log( __LINE__ . " Smart_Media_Categories_Admin::action_manage_pages_custom_column( $column_name, $post_id )", 0 );
		if ( 'smc_children' != $column_name ) {
			return;
		}

		$args = array( 'post_parent' => $post_id, 'post_type' => 'attachment' );
		$children = get_children( $args );
		if ( $children ) {
			$threshold = SMC_Settings_Support::get_option( 'scroll_threshold' );
			$count = count( $children );
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_manage_pages_custom_column $count = ' . var_export( $count, true ), 0 );
			if ( $threshold <= $count ) {
				$height = SMC_Settings_Support::get_option( 'scroll_height' );
				echo "<div style=\"width: 100%; height: {$height}; overflow: auto\">\n";
			}
			
			ksort( $children);
			foreach ($children as $child_id => $child ) {
				$sync_class = 'smc-sync-true';
				echo sprintf( '<a class="hide-if-no-js %1$s" id="smc-child-%3$s" onclick="smc.findPosts.open( \'%2$s\',\'%3$s\',\'%5$s\' ); return false;" href="%4$s#the-list">%5$s (%3$s)</a><br>', /*%1$s*/ $sync_class, /*%2$s*/ $post_id, /*%3$s*/ $child->ID, /*%4$s*/ admin_url( 'edit.php' ), /*%5$s*/ esc_attr( $child->post_title ) );
			}

			if ( $threshold <= $count ) {
				echo "</div>\n";
			}
		} else {
			echo __( 'No Children', 'smart-media-categories' );
		}
	}

	/**
	 * Adds mapping update messages for display at the top of the All Posts/All Pages screen
	 *
	 * @since 1.0.0
	 *
	 * @param	array	messages for the Edit screen
	 *
	 * @return	array	updated messages
	 */
	public function filter_bulk_post_updated_messages( $messages, $counts ) {
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::filter_bulk_post_updated_messages $_REQUEST = ' . var_export( $_REQUEST, true ), 0 );

		if ( isset( $_REQUEST['smc_message'] ) ) {
			// Determine which message array to update
			if ( !empty( $_REQUEST['post_type'] ) && ( 'page' === $_REQUEST['post_type'] ) ) {
				$message_type = 'page';
			} else {
				$message_type = 'post';
			}
			
			$smc_message = explode( ',', $_REQUEST['smc_message'] );
			switch ( $smc_message[0] ) {
				case '101':
					$messages[ $message_type ]['updated'] = _n( '%s parent updated.', '%s parents updated.', $counts['updated'], 'smart-media-categories' );
					break;
				case '102':
					$messages[ $message_type ]['updated'] = __( 'No terms updated.', 'smart-media-categories' );
					break;
				case '103':
					$messages[ $message_type ]['updated'] = _n( '%s child terms updated.', '%s children terms updated.', $counts['updated'], 'smart-media-categories' );
					break;
				case '104':
					$messages[ $message_type ]['updated'] = _n( 'Parent and %s child terms updated.', 'Parent and %s children terms updated.', $counts['updated'], 'smart-media-categories' );
					break;
				case '105':
					$messages[ $message_type ]['updated'] = __( 'No parents updated.', 'smart-media-categories' );
					break;
				case '106':
					$parent_clause = sprintf( _n( '%d parent', '%d parents', $smc_message[1], 'smart-media-categories' ), $smc_message[1] );
					$children_clause = sprintf( _n( '%d child', '%d children', (integer)$smc_message[2], 'smart-media-categories' ), $smc_message[2] );
					$messages[ $message_type ]['updated'] = sprintf( __( 'All Children synced %1$s to %2$s.', 'smart-media-categories' ), $children_clause, $parent_clause );
					break;
				case '107':
					$parent_clause = sprintf( _n( '%d parent', '%d parents', $smc_message[1], 'smart-media-categories' ), $smc_message[1] );
					$children_clause = sprintf( _n( '%d child', '%d children', (integer)$smc_message[2], 'smart-media-categories' ), $smc_message[2] );
					$messages[ $message_type ]['updated'] = sprintf( __( 'Bulk Edit synced %1$s to %2$s.', 'smart-media-categories' ), $children_clause, $parent_clause );
					break;
			}
		}
	
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::filter_bulk_post_updated_messages $messages = ' . var_export( $messages, true ), 0 );
		return $messages;
	} // filter_bulk_post_updated_messages

	/**
	 * Move attachment(s) from one parent to another
	 *
	 * Adapted from case "attach": in /wp-admin/upload.php
	 *
	 * @since    1.0.0
	 *
	 * @param	integer	ID of the current parent post
	 * @param	integer	ID of the new parent post
	 * @param	array	IDs of the attachments to be reassigned
	 *
	 * @return	void	exits after wp_redirect to All Posts/All Pages submenu
	 */
	private function _reassign_parent( $old_parent, $new_parent, $attachments ) {
		global $wpdb;
//error_log( __LINE__ . " Smart_Media_Categories_Admin::_reassign_parent( $old_parent, $new_parent ) \$attachments = " . var_export( $attachments, true ), 0 );
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::_reassign_parent $_REQUEST = ' . var_export( $_REQUEST, true ), 0 );
		
		if ( !$old_parent ) {
			wp_die( __( 'Current parent ID not in request.', 'smart-media-categories' ) );
		}

		if ( !current_user_can( 'edit_post', $old_parent ) ) {
			wp_die( __( 'You are not allowed to edit the current parent.', 'smart-media-categories' ) );
		}

		if ( $new_parent && !current_user_can( 'edit_post', $new_parent ) ) {
			wp_die( __( 'You are not allowed to edit the new parent.', 'smart-media-categories' ) );
		}

		$attached = 0;
		if ( $old_parent != $new_parent ) {
			$attach = array();
			foreach ( $attachments as $att_id ) {
				$att_id = (int) $att_id;
	
				if ( !current_user_can( 'edit_post', $att_id ) )
					continue;
	
				$attach[] = $att_id;
			}
	
			if ( ! empty( $attach ) ) {
				foreach ( $attach as $att_id ) {
					if ( wp_update_post( array( 'ID' => $att_id, 'post_parent' => $new_parent ), true ) ) {
						$attached++;
					}
				}
			}
		}
		
		$location = 'edit.php';
		if ( !empty( $_REQUEST['post_type'] ) ) {
			$location .= '?post_type=' . $_REQUEST['post_type'];
		}

		if ( $referer = wp_get_referer() ) {
			if ( false !== strpos( $referer, 'edit.php' ) )
				$location = $referer;
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::_reassign_parent $location = ' . var_export( $location, true ), 0 );
		}
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::_reassign_parent $referer = ' . var_export( $referer, true ), 0 );

		if ( $attached ) {
			$location = add_query_arg( array( 'updated' => $attached, 'smc_message' => '101' ) , $location );
		} else {
			$location = add_query_arg( array( 'updated' => 1, 'smc_message' => '105' ) , $location );
		}
		
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::_reassign_parent $location = ' . var_export( $location, true ), 0 );
		wp_redirect( $location );
		exit();
	}

	/**
	 * Process the Posts/All Posts "Select Parent" popup form submission
	 *
	 * @since    1.0.0
	 *
	 * @return	void	exits after wp_redirect to All Posts submenu
	 */
	public function action_smc_posts_modal() {
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_smc_posts_modal $_POST = ' . var_export( $_POST, true ), 0 );
		
		if ( isset( $_REQUEST['smc-posts-modal-submit'] ) ) {
			$this->_reassign_parent( (int) $_REQUEST['parent'], (int) $_REQUEST['found_post_id'], (array) $_REQUEST['children'] );
		} elseif ( isset( $_REQUEST['smc-sync-update'] ) ) {
			$parent_id = (int) $_REQUEST['parent'];
			if ( !$parent_id ) {
				wp_die( __( 'Parent ID not in request.', 'smart-media-categories' ) );
			}
	
			//$parent = get_post( $parent_id );
			if ( !current_user_can( 'edit_post', $parent_id ) ) {
				wp_die( __( 'You are not allowed to edit this post.', 'smart-media-categories' ) );
			}

			// TODO: delete #smc-posts-modal-children
			if ( empty( $_REQUEST['children'] ) ) {
				$_REQUEST['children'] = array();
			} elseif ( empty( $_REQUEST['children'][0] ) ) {
				unset( $_REQUEST['children'][0] );
			}
			
			extract( SMC_Sync_Support::sync_terms( $parent_id, $_REQUEST['children'], $_REQUEST['tax_input'], $_REQUEST['tax_action'] ) );
		}

		if ( $parent_changed ) {
			$smc_message = '104';
		} elseif ( $updated ) {
			$smc_message = '103';
		} else {
			$smc_message = '102';
		}

		if ( ! $updated ) {
			$updated = 1;
		}

		$location = 'edit.php';
		if ( $referer = wp_get_referer() ) {
			if ( false !== strpos( $referer, 'edit.php' ) )
				$location = $referer;
		}

		$location = add_query_arg( array( 'updated' => $updated, 'smc_message' => $smc_message ) , $location );
		wp_redirect( $location );
		exit();
	}

	/**
	 * Process the Pages/All Pages "Select Parent" popup form submission
	 *
	 * @since    1.0.0
	 *
	 * @return	void	exits after wp_redirect to All Pages submenu
	 */
	public function action_smc_pages_modal() {
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_smc_pages_modal $_POST = ' . var_export( $_POST, true ), 0 );
		
		if ( isset( $_REQUEST['smc-posts-modal-submit'] ) ) {
			$this->_reassign_parent( (int) $_REQUEST['parent'], (int) $_REQUEST['found_post_id'], (array) $_REQUEST['children'] );
		}

		wp_redirect( admin_url( 'edit.php' ) . '?post_type=page' );
		exit();
	}

	/**
	 * Extract the view and pagination query arguments for reuse
	 *
	 * @since    1.0.2
	 *
	 * @return	array	view and pagination query arguments
	 */
	private function _view_query_args() {
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::_view_query_args $_REQUEST = ' . var_export( $_REQUEST, true ), 0 );
		$view_args = array();
		foreach( $_REQUEST as $key => $value ) {
			switch ( $key ) {
				case 's':
				case 'post_status':
				case 'post_type':
				case 'm':
				case 'cat':
				case 'paged':
				case 'mode':
				case 'smc_status':
					$view_args[ $key ] = urlencode( stripslashes( $value ) );
					break;
			}
		}

//error_log( __LINE__ . ' Smart_Media_Categories_Admin::_view_query_args $view_args = ' . var_export( $view_args, true ), 0 );
		return $view_args;
	}

	/**
	 * Process the "Sync All Children" button in the Bulk Edit area
	 *
	 * @since    1.0.2
	 *
	 * @return	void	exits after wp_redirect to All Pages submenu
	 */
	public function action_sync_all_bulk() {
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_sync_all_bulk $_GET = ' . var_export( $_GET, true ), 0 );
		$post_parents = isset( $_REQUEST['post'] ) ? $_REQUEST['post'] : array();
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_sync_all_bulk $post_parents = ' . var_export( $post_parents, true ), 0 );

		$assignments = SMC_Sync_Support::get_posts_per_view( array( 'post_type' => $this->current_edit_type, 'smc_status' => 'unsync', 'post_parents' => $post_parents, 'fields' => 'all' ) );
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_sync_all_bulk $assignments = ' . var_export( $assignments, true ), 0 );
		if ( ! empty( $assignments ) ) {
			$results = SMC_Sync_Support::sync_all( $assignments );
			$parent_count = $results['parent_count'];
			$children_count = $results['children_count'];
		} else {
			$parent_count = 0;
			$children_count = 0;
		}
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_sync_all_bulk $parent_count = ' . var_export( $parent_count, true ), 0 );
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_sync_all_bulk $children_count = ' . var_export( $children_count, true ), 0 );
		
		$view_args = $this->_view_query_args();
		$view_args['updated'] = 1;
		$view_args['smc_message'] = '107,' . $parent_count . ',' . $children_count;
		
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_sync_all_bulk URL = ' . var_export( add_query_arg( $view_args, admin_url( 'edit.php' ) ), true ), 0 );
		wp_redirect( add_query_arg( $view_args, admin_url( 'edit.php' ) ) );
		exit();
	}

	/**
	 * Process the "Sync All Children" button in the Posts/All Posts extra navigation area
	 *
	 * @since    1.0.2
	 *
	 * @return	void	exits after wp_redirect to All Pages submenu
	 */
	public function action_sync_all_filter() {
		global $wp_query;
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_sync_all_filter $_GET = ' . var_export( $_GET, true ), 0 );

		$assignments = SMC_Sync_Support::get_posts_per_view( array( 'post_type' => $this->current_edit_type, 'smc_status' => 'unsync', 'fields' => 'all' ) );
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_sync_all_filter $assignments = ' . var_export( $assignments, true ), 0 );

		$view_args = $this->_view_query_args();
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_sync_all_filter $view_args = ' . var_export( $view_args, true ), 0 );
		
		$parent_count = 0;
		$children_count = 0;
		$paged_index = 1;
		$max_num_pages = 1;
		add_filter( 'posts_clauses', array( $this, 'filter_posts_clauses' ), 10, 2 );
		while ( $paged_index <= $max_num_pages ) {
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_sync_all_filter SYNCH $paged_index = ' . var_export( $paged_index, true ), 0 );
			$_GET['paged'] = $paged_index++;
			wp_edit_posts_query();
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_sync_all_filter $wp_query = ' . var_export( $wp_query, true ), 0 );
			$max_num_pages = $wp_query->max_num_pages;
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_sync_all_filter $max_num_pages = ' . var_export( $max_num_pages, true ), 0 );
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_sync_all_filter $wp_query->posts = ' . var_export( $wp_query->posts, true ), 0 );
			$unsync_assignments = array();
			foreach( $wp_query->posts as $post ) {
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_sync_all_filter TEST $post->ID = ' . var_export( $post->ID, true ), 0 );
				if ( array_key_exists( $post->ID, $assignments ) ) {
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_sync_all_filter SYNCH $post->ID = ' . var_export( $post->ID, true ), 0 );
					$unsync_assignments[ $post->ID ] = $assignments[ $post->ID ];
				}
			} // each post
			
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_sync_all_filter unsync_assignments = ' . var_export( $unsync_assignments, true ), 0 );
			if ( ! empty( $unsync_assignments ) ) {
				$results = SMC_Sync_Support::sync_all( $unsync_assignments );
				$parent_count += $results['parent_count'];
				$children_count += $results['children_count'];
			}
		} // each page
		remove_filter( 'posts_clauses', array( $this, 'filter_posts_clauses' ), 10 );

		$view_args['updated'] = 1;
		$view_args['smc_message'] = '106,' . $parent_count . ',' . $children_count;
		
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_sync_all_filter URL = ' . var_export( add_query_arg( $view_args, admin_url( 'edit.php' ) ), true ), 0 );
		wp_redirect( add_query_arg( $view_args, admin_url( 'edit.php' ) ) );
		exit();
	}

	/**
	 * Maximum number of posts per "Select Parent" page.
	 *
	 * @since   1.0.1
	 *
	 * @var     string
	 */
	const POSTS_PER_PAGE = "50";

	/**
	 * Ajax handler to fetch candidates for the "Select Parent" popup form
	 *
	 * Adapted from wp_ajax_find_posts in /wp-admin/includes/ajax-actions.php.
	 * Adds filters for post type and pagination.
	 *
	 * @since 1.0.1
	 *
	 * @return	void	passes results to wp_send_json_success() for JSON encoding and transmission
	 */
	public function action_wp_ajax_smc_find_posts() {
		global $wpdb;
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_wp_ajax_smc_find_posts $_REQUEST = ' . var_export( $_REQUEST, true ), 0 );

		check_ajax_referer( 'smc_find_posts' );

		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		unset( $post_types['attachment'] );
	
		$s = stripslashes( $_REQUEST['smc_set_parent_search_text'] );
		$count = isset( $_REQUEST['smc_set_parent_count'] ) ? $_REQUEST['smc_set_parent_count'] : self::POSTS_PER_PAGE;
		$paged = isset( $_REQUEST['smc_set_parent_paged'] ) ? $_REQUEST['smc_set_parent_paged'] : '1';

		$args = array(
			'post_type' => ( 'all' == $_REQUEST['smc_set_parent_post_type'] ) ? array_keys( $post_types ) : $_REQUEST['smc_set_parent_post_type'],
			'post_status' => 'any',
			'posts_per_page' => $count,
			'paged' => $paged,
		);
		
		if ( '' !== $s )
			$args['s'] = $s;
	
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_wp_ajax_smc_find_posts $args = ' . var_export( $args, true ), 0 );
		$posts = get_posts( $args );
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_wp_ajax_smc_find_posts $posts = ' . var_export( $posts, true ), 0 );
	
		if ( ( ! $posts ) && $paged > 1 ) {
			$args['paged'] = $paged = 1;
			$posts = get_posts( $args );
		}

		$found = count( $posts );
		
		$html = '<input name="smc_set_parent_count" id="smc-set-parent-count" type="hidden" value="' . $count . "\">\n";
		$html .= '<input name="smc_set_parent_paged" id="smc-set-parent-paged" type="hidden" value="' . $paged . "\">\n";
		$html .= '<input name="smc_set_parent_found" id="smc-set-parent-found" type="hidden" value="' . $found . "\">\n";

		$html .= '<table class="widefat"><thead><tr><th class="found-radio"><br /></th><th>'.__('Title').'</th><th class="no-break">'.__('Type').'</th><th class="no-break">'.__('Date').'</th><th class="no-break">'.__('Status').'</th></tr></thead><tbody>' . "\n";
		if ( $found ) {
			$alt = '';
			foreach ( $posts as $post ) {
				$title = trim( $post->post_title ) ? $post->post_title : __( '(no title)' );
				$alt = ( 'alternate' == $alt ) ? '' : 'alternate';
		
				switch ( $post->post_status ) {
					case 'publish' :
					case 'private' :
						$stat = __('Published');
						break;
					case 'future' :
						$stat = __('Scheduled');
						break;
					case 'pending' :
						$stat = __('Pending Review');
						break;
					case 'draft' :
						$stat = __('Draft');
						break;
				}
		
				if ( '0000-00-00 00:00:00' == $post->post_date ) {
					$time = '';
				} else {
					/* translators: date format in table columns, see http://php.net/date */
					$time = mysql2date(__('Y/m/d'), $post->post_date);
				}
		
				$html .= '<tr class="' . trim( 'found-posts ' . $alt ) . '"><td class="found-radio"><input type="radio" id="found-'.$post->ID.'" name="found_post_id" value="' . esc_attr($post->ID) . '"></td>';
				$html .= '<td><label for="found-'.$post->ID.'">' . esc_html( $title ) . '</label></td><td class="no-break">' . esc_html( $post_types[$post->post_type]->labels->singular_name ) . '</td><td class="no-break">'.esc_html( $time ) . '</td><td class="no-break">' . esc_html( $stat ). ' </td></tr>' . "\n";
			} // foreach post
		} else {
				$html .= '<tr class="' . trim( 'found-posts ' ) . '"><td class="found-radio">&nbsp;</td>';
				$html .= '<td colspan="4">No results found.</td></tr>' . "\n";
		}
	
		$html .= "</tbody></table>\n";
	
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_wp_ajax_smc_find_posts $html = ' . var_export( $html, true ), 0 );
		wp_send_json_success( $html );
	}

	/**
	 * Process the "Select Parent" popup form submission
	 *
	 * @since    1.0.0
	 *
	 *
	 * @return	void	echoes HTML markup for the Attach popup
	 */
	public function action_wp_ajax_smc_posts_modal() {
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::action_wp_ajax_smc_posts_modal $_POST = ' . var_export( $_POST, true ), 0 );
		wp_redirect( admin_url( 'edit.php' ) . '?post_type=page' );
		exit();
	}

	/**
	 * Compose a Post Type Options list with current selection
 	 *
	 * @since 1.0.1
	 * @uses $mla_option_templates contains row and table templates
	 *
	 * @param	string 	current selection or 'all' (default)
	 *
	 * @return	string	HTML markup with select field options
	 */
	private function _compose_post_type_select( $selection = 'all' ) {
		$option_template = '                <option %1$s value="%2$s">%3$s</option>' . "\n";
		$options = sprintf( $option_template, ( 'all' == $selection ) ? 'selected="selected"' : '', 'all', '&mdash; ' . __( 'All Post Types', 'media-library-assistant' ) . ' &mdash;' );

		$post_types = get_post_types( array( 'public' => true ), 'objects' );	
		unset( $post_types['attachment'] );

		foreach ( $post_types as $key => $value ) {
			$options .= sprintf( $option_template, ( $key == $selection ) ? 'selected="selected"' : '', $key, $value->labels->name );
		} // foreach post_type

		$select_template = '            <select name="smc_set_parent_post_type" id="smc-set-parent-post-type">' . "\n" . '%1$s' . "\n" . '            </select>'
 . "\n";
		return sprintf( $select_template, $options );
	} // _compose_post_type_select

	/**
	 * Compose the "Smart Media Categories" popup HTML elements
	 *
	 * @since    1.0.0
	 *
	 *
	 * @return	array	HTML markup elements for the SMC popup; middle_column, right_column, parent_dropdown
	 */
	private function _build_smc_popup_form() {
		$results = array();
		
		// Build separate arrays of hierarchical and flat taxonomies
		$taxonomies = get_object_taxonomies( 'attachment', 'objects' );
		$post_taxonomies = SMC_Settings_Support::smc_taxonomies();
//error_log( __LINE__ . ' Smart_Media_Categories_Admin::_build_smc_popup_form post_taxonomies = ' . var_export( $post_taxonomies, true ), 0 );

		$hierarchical_taxonomies = array();
		$flat_taxonomies = array();
		foreach ( $taxonomies as $tax_name => $tax_object ) {
			if ( $tax_object->show_ui && in_array( $tax_name, $post_taxonomies ) ) {
				if ( $tax_object->hierarchical && $tax_object->show_ui ) {
					$hierarchical_taxonomies[$tax_name] = $tax_object;
				} elseif ( $tax_object->show_ui ) {
					$flat_taxonomies[$tax_name] = $tax_object;
				}
			}
		}

		// Build the middle column - hierarchical taxonomies
		$category_blocks = '';
		foreach ($hierarchical_taxonomies as $tax_slug => $tax_object ) {
			if ( current_user_can( $tax_object->cap->assign_terms ) ) {
				$tax_name = esc_html( $tax_object->labels->name );
				$tax_attr = esc_attr( $tax_slug );

				ob_start();
				wp_terms_checklist( NULL, array( 'taxonomy' => $tax_slug ) );
				$tax_checklist = ob_get_clean();
				
				ob_start();
				include( 'views/smc-sync-tax-options.php' );
				$taxonomy_options = ob_get_clean();
				
				ob_start();
				include( 'views/smc-sync-category-block.php' );
				$category_block = ob_get_clean();
				
				$category_blocks .= $category_block;
			} // current_user_can
		} // foreach $hierarchical_taxonomies

		ob_start();
		include( 'views/smc-sync-category-fieldset.php' );
		$middle_column = ob_get_clean();

		// Build the right column - flat taxonomies
		$tag_blocks = '';
		foreach ($flat_taxonomies as $tax_slug => $tax_object ) {
			if ( current_user_can( $tax_object->cap->assign_terms ) ) {
				$tax_name = esc_html( $tax_object->labels->name );
				$tax_attr = esc_attr( $tax_slug );

				ob_start();
				include( 'views/smc-sync-tax-options.php' );
				$taxonomy_options = ob_get_clean();
				
				ob_start();
				include( 'views/smc-sync-tag-block.php' );
				$tag_block = ob_get_clean();
				
				$tag_blocks .= $tag_block;
			} // current_user_can
		} // foreach $flat_taxonomies

		ob_start();
		include( 'views/smc-sync-tag-fieldset.php' );
		$right_column = ob_get_clean();
		
		ob_start();
		include( 'views/smc-sync-div.php' );
		$results['sync_box'] = ob_get_clean();
		
		return $results;
	}

	/**
	 * Add the "Select Parent" popup HTML to the Pages/All Pages submenu
	 *
	 * @since    1.0.0
	 *
	 *
	 * @return	void	echoes HTML markup for the Attach popup
	 */
	public function action_posts_admin_footer() {
		$form_url = admin_url( 'edit.php' );
		$form_action = 'smc_posts_modal';
		$post_type_dropdown = $this->_compose_post_type_select();
		$count = self::POSTS_PER_PAGE;
		$paged = '1';
		$found = '0';
		extract( $this->_build_smc_popup_form() );
		include_once( 'views/smc-posts-modal-form.php' );
	}

	/**
	 * Add the "Select Parent" popup HTML to the Pages/All Pages submenu
	 *
	 * @since    1.0.0
	 *
	 *
	 * @return	void	echoes HTML markup for the Attach popup
	 */
	public function action_pages_admin_footer() {
		$form_url = admin_url( 'edit.php' ) . '?post_type=page';
		$form_action = 'smc_pages_modal';
		$post_type_dropdown = $this->_compose_post_type_select();
		$count = self::POSTS_PER_PAGE;
		$paged = '1';
		$found = '0';
		$sync_box = '';
		include_once( 'views/smc-posts-modal-form.php' );
	}
}
?>
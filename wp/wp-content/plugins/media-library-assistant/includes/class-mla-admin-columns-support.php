<?php
/**
 * Media Library Assistant Admin Columns (plugin) Support
 *
 * @package Media Library Assistant
 * @since 2.22
 */
defined( 'ABSPATH' ) or die();

/**
 * Class CPAC Storage Model MLA (Media Library Assistant) supports the Admin Columns plugin
 *
 * @package Media Library Assistant
 * @since 2.22
 */
class CPAC_Storage_Model_MLA extends CPAC_Storage_Model {
	/**
	 * Identifies submenu entry in the Admin sidebar, e.g., Media/Assistant in Media
	 *
	 * @since 2.25
	 * @var string
	 */
	public $subpage;

	/**
	 * Initializes some properties, installs filters and then
	 * calls the parent constructor to set some default configs.
	 *
	 * @since 2.22
	 */
	public function __construct() {
		$this->key            = 'mla-media-assistant';
		$this->label          = __( 'Media Library Assistant' );
		$this->singular_label = __( 'Assistant' );
		$this->type           = 'media';
		$this->meta_type      = 'post';
		$this->page           = 'upload';
		$this->subpage        = MLACore::ADMIN_PAGE_SLUG;
		$this->post_type      = 'attachment';
		$this->menu_type      = 'other';

		// Increased the priority to overrule 3th party plugins such as Media Tags
		add_filter( 'manage_media_page_' . MLACore::ADMIN_PAGE_SLUG . '_columns', array( $this, 'add_headings' ), 100 );
		add_filter( 'mla_list_table_column_default', array( $this, 'manage_value' ), 100, 3 );

		parent::__construct();
	}

	/**
	 * Added in Admin Columns update to v2.4.9
	 *
	 * @since 2.23
	 */
	public function init_manage_columns() {

		//add_filter( "manage_{$this->page}_columns", array( $this, 'add_headings' ), 100 );
		//add_action( 'manage_comments_custom_column', array( $this, 'manage_value' ), 100, 2 );
	}

	/**
	 * Returns the Media/Assistant submenu table column definitions
	 *
	 * @since 2.22
	 *
	 * @return	array	( 'column_slug' => 'column_heading' )
	 */
	public function get_default_columns() {
		if ( ! class_exists( 'MLAQuery' ) ) {
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-data-query.php' );
			MLAQuery::initialize();
		}

		return apply_filters( 'mla_list_table_get_columns', MLAQuery::$default_columns );
	}

	/**
	 * Returns the Media/Assistant submenu table column slugs/keys
	 *
	 * @since 2.22
	 *
	 * @return	array	( index => 'column_slug' )
	 */
	public function get_default_column_names() {
		if ( ! class_exists( 'MLAQuery' ) ) {
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-data-query.php' );
			MLAQuery::initialize();
		}

		return array_keys( apply_filters( 'mla_list_table_get_columns', MLAQuery::$default_columns ) );
	}

	/**
	 * Returns the custom fields assigned to Media Library items, removing those already present
	 * in the Media/Assistant submenu table
	 *
	 * @since 2.22
	 *
	 * @return	array	( index => array( 0 => 'custom field name' ) )
	 */
	public function get_meta() {
		global $wpdb;

		/*
		 * Find all of the custom field names assigned to Media Library items
		 */
		$meta = $wpdb->get_results( "SELECT DISTINCT meta_key FROM {$wpdb->postmeta} pm JOIN {$wpdb->posts} p ON pm.post_id = p.ID WHERE p.post_type = 'attachment' ORDER BY 1", ARRAY_N );

		/*
		 * Find the fields already present in the submenu table
		 */
		$mla_columns = apply_filters( 'mla_list_table_get_columns', MLAQuery::$default_columns );
		$mla_custom = array();
		foreach ( $mla_columns as $slug => $heading ) {
			if ( 'c_' === substr( $slug, 0, 2 ) ) {
				$mla_custom[] = $heading;
			}
		}
		
		/*
		 * Remove the fields already present in the submenu table
		 */
		foreach ( $meta as $index => $value ) {
			if ( in_array( esc_html( current( $value ) ), $mla_custom ) ) {
				unset( $meta[ $index ] );
			}
		}
		
		return $meta;
	}

	/**
	 * Return the content of an Admin Columns custom column
	 *
	 * @since 2.22
	 *
	 * @param	string	$content Current column content (empty string)
	 * @param	object	$item Current Media Library item
	 * @param	string	$column_name Current column slug
	 *
	 * @return string Column value or NULL if not an Admin Columns custom column
	 */
	public function manage_value( $content, $item, $column_name ) {
		$media_id = $item->ID;

		if ( ! ( $column = $this->get_column_by_name( $column_name ) ) ) {
			return NULL;
		}

		$value = $column->get_value( $media_id );

		// hooks
		$value = apply_filters( "cac/column/value", $value, $media_id, $column, $this->key );
		$value = apply_filters( "cac/column/value/{$this->type}", $value, $media_id, $column, $this->key );

		return $value;
	}

	/**
	 * Test for current screen = the Media/Assistant submenu screen,
	 * For Admin Columns 2.4.9+
	 *
	 * @since 2.23
	 *
	 * @return boolean true if the Media/Assistant submenu is the current screen
	 */
	public function is_current_screen() {
		$is_current_screen = parent::is_current_screen();
		if ( ! $is_current_screen ) {
			if ( ! empty( $_REQUEST['page'] ) && MLACore::ADMIN_PAGE_SLUG == $_REQUEST['page'] ) {
				$is_current_screen = true;
			}
		}

		return $is_current_screen;
	}

	/**
	 * Test for current screen = the Media/Assistant submenu screen
	 *
	 * @since 2.22
	 *
	 * @return boolean true if the Media/Assistant submenu is the current screen
	 */
	public function is_columns_screen() {
		$is_columns_screen = parent::is_columns_screen();
		if ( ! $is_columns_screen ) {
			if ( ! empty( $_REQUEST['page'] ) && MLACore::ADMIN_PAGE_SLUG == $_REQUEST['page'] ) {
				$is_columns_screen = true;
			}
		}

		return $is_columns_screen;
	}

	/**
	 * Return a link to the Media/Assistant submenu screen
	 *
	 * @since 2.22
	 *
	 * @return string Link to the Media/Assistant submenu screen
	 */
	protected function get_screen_link() {
		return is_network_admin() ? network_admin_url( $this->page . '.php?page=' . MLACore::ADMIN_PAGE_SLUG ) : admin_url( $this->page . '.php?page=' . MLACore::ADMIN_PAGE_SLUG );
	}

	/**
	 * Return a link to the Media/Assistant submenu Edit columns screen
	 *
	 * @since 2.22
	 *
	 * @return string Link to the Media/Assistant submenu Edit columns screen
	 */
	public function get_edit_link() {
		return add_query_arg( array( 'page'     => 'codepress-admin-columns',
		                             'cpac_key' => $this->key
		), admin_url( 'options-general.php' ) );
	}
} // class CPAC_Storage_Model_MLA
<?php
/**
 * Media Library Assistant Shortcode interface functions
 *
 * @package Media Library Assistant
 * @since 0.1
 */

/**
 * Class MLA (Media Library Assistant) Shortcodes defines the shortcodes available
 * to MLA users and loads the support class if the shortcodes are executed.
 *
 * @package Media Library Assistant
 * @since 0.20
 */
class MLAShortcodes {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 0.20
	 *
	 * @return	void
	 */
	public static function initialize() {
		global $sitepress, $polylang;

		/*
		 * Check for WPML/Polylang presence before loading language support class,
		 * then immediately initialize it since we're already in the "init" action.
		 */
		if ( is_object( $sitepress ) ) {
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-wpml-shortcode-support.php' );
			MLA_WPML_Shortcodes::initialize();
		} elseif ( is_object( $polylang ) ) {
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-polylang-shortcode-support.php' );
			MLA_Polylang_Shortcodes::initialize();
		}

		add_shortcode( 'mla_gallery', 'MLAShortcodes::mla_gallery_shortcode' );
		add_shortcode( 'mla_tag_cloud', 'MLAShortcodes::mla_tag_cloud_shortcode' );
		add_shortcode( 'mla_term_list', 'MLAShortcodes::mla_term_list_shortcode' );

		/*
		 * Avoid wptexturize defect
		 */
		if ( version_compare( get_bloginfo('version'), '4.0', '>=' ) ) {
			add_filter( 'no_texturize_shortcodes', 'MLAShortcodes::mla_no_texturize_shortcodes_filter', 10, 1 );
		}
	}

	/**
	 * Prevents wptexturizing of the [mla_gallery] shortcode, avoiding a bug in WP 4.0.
	 * 
	 * Defined as public because it's a filter.
	 *
	 * @since 1.94
	 *
	 * @param	array	list of "do not texturize" shortcodes
	 *
	 * @return	array	updated list of "do not texturize" shortcodes
	 */
	public static function mla_no_texturize_shortcodes_filter( $no_texturize_shortcodes ) {
		if ( ! in_array( 'mla_gallery', $no_texturize_shortcodes ) ) {
			$no_texturize_shortcodes[] = 'mla_gallery';
			$no_texturize_shortcodes[] = 'mla_tag_cloud';
		}

		return $no_texturize_shortcodes;
	}

	/**
	 * The MLA Gallery shortcode.
	 *
	 * Compatibility shim for MLAShortcode_Support::mla_gallery_shortcode
	 *
	 * @since .50
	 *
	 * @param array $attr Attributes of the shortcode
	 * @param string $content Optional content for enclosing shortcodes; used with mla_alt_shortcode
	 *
	 * @return string HTML content to display gallery.
	 */
	public static function mla_gallery_shortcode( $attr, $content = NULL ) {
		if ( !class_exists( 'MLAShortcode_Support' ) ) {
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-shortcode-support.php' );
		}
		
		return MLAShortcode_Support::mla_gallery_shortcode( $attr, $content );
	}

	/**
	 * The MLA Tag Cloud shortcode.
	 *
	 * Compatibility shim for MLAShortcode_Support::mla_tag_cloud_shortcode
	 *
	 * @since 1.60
	 *
	 * @param array $attr Attributes of the shortcode.
	 * @param string $content Optional content for enclosing shortcodes
	 *
	 * @return string HTML content to display the tag cloud.
	 */
	public static function mla_tag_cloud_shortcode( $attr, $content = NULL ) {
		if ( !class_exists( 'MLAShortcode_Support' ) ) {
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-shortcode-support.php' );
		}

		return MLAShortcode_Support::mla_tag_cloud_shortcode( $attr, $content );
	}

	/**
	 * The MLA Term List shortcode.
	 *
	 * Compatibility shim for MLAShortcode_Support::mla_term_list_shortcode
	 *
	 * @since 2.25
	 *
	 * @param array $attr Attributes of the shortcode.
	 * @param string $content Optional content for enclosing shortcodes
	 *
	 * @return string HTML content to display the tag cloud.
	 */
	public static function mla_term_list_shortcode( $attr, $content = NULL ) {
		if ( !class_exists( 'MLAShortcode_Support' ) ) {
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-shortcode-support.php' );
		}

		return MLAShortcode_Support::mla_term_list_shortcode( $attr, $content );
	}

	/**
	 * The WP_Query object used to select items for the gallery.
	 *
	 * Defined as a public, static variable so it can be inspected from the
	 * "mla_gallery_wp_query_object" action. Set to NULL at all other times.
	 *
	 * @since 1.51
	 *
	 * @var	object
	 */
	public static $mla_gallery_wp_query_object = NULL;

	/**
	 * Parses shortcode parameters and returns the gallery objects
	 *
	 * Compatibility shim for MLAShortcode_Support::mla_get_shortcode_attachments
	 *
	 * @since .50
	 *
	 * @param int Post ID of the parent
	 * @param array Attributes of the shortcode
	 * @param boolean true to calculate and return ['found_posts'] as an array element
	 *
	 * @return array List of attachments returned from WP_Query
	 */
	public static function mla_get_shortcode_attachments( $post_parent, $attr, $return_found_rows = NULL ) {
		if ( !class_exists( 'MLAShortcode_Support' ) ) {
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-shortcode-support.php' );
		}

		return MLAShortcode_Support::mla_get_shortcode_attachments( $post_parent, $attr, $return_found_rows );
	}

	/**
	 * Retrieve the terms in one or more taxonomies.
	 *
	 * Compatibility shim for MLAShortcode_Support::mla_get_terms
	 *
	 * @since 1.60
	 *
	 * @param	array	taxonomies to search and query parameters
	 *
	 * @return	array	array of term objects, empty if none found
	 */
	public static function mla_get_terms( $attr ) {
		if ( !class_exists( 'MLAShortcode_Support' ) ) {
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-shortcode-support.php' );
		}

		return MLAShortcode_Support::mla_get_terms( $attr );
	}

	/**
	 * Get IPTC/EXIF or custom field mapping data source; front end posts/pages mode
	 *
	 * Compatibility shim for MLAData_Source::mla_get_data_source.
	 *
	 * @since 1.70
	 *
	 * @param	integer	post->ID of attachment
	 * @param	string 	category/scope to evaluate against: custom_field_mapping or single_attachment_mapping
	 * @param	array	data source specification ( name, *data_source, *keep_existing, *format, mla_column, quick_edit, bulk_edit, *meta_name, *option, no_null )
	 * @param	array 	(optional) _wp_attachment_metadata, default NULL (use current postmeta database value)
	 *
	 * @return	string|array	data source value
	 */
	public static function mla_get_data_source( $post_id, $category, $data_value, $attachment_metadata = NULL ) {
		if ( !class_exists( 'MLAData_Source' ) ) {
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-data-source.php' );
		}

		return MLAData_Source::mla_get_data_source( $post_id, $category, $data_value, $attachment_metadata );
	} // mla_get_data_source

	/**
	 * Identify custom field mapping data source; front end posts/pages mode
	 *
	 * Compatibility shim for MLAData_Source::mla_is_data_source.
	 *
	 * @since 1.80
	 *
	 * @param	string 	candidate data source name
	 *
	 * @return	boolean	true if candidate name matches a data source
	 */
	public static function mla_is_data_source( $candidate_name ) {
		if ( !class_exists( 'MLAData_Source' ) ) {
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-data-source.php' );
		}

		return MLAData_Source::mla_is_data_source( $candidate_name );
	}
} // Class MLAShortcodes
?>
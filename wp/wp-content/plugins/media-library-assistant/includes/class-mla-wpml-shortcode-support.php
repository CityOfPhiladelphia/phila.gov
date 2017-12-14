<?php
/**
 * Media Library Assistant WPML Shortcode Support classes, front-end mode
 *
 * This file is conditionally loaded in MLAShortcodes::initialize after a check for WPML presence.
 *
 * @package Media Library Assistant
 * @since 2.40
 */

/**
 * Class MLA (Media Library Assistant) WPML Shortcodes provides front-end support for the
 * WPML Multilingual CMS family of plugins, including WPML Media
 *
 * @package Media Library Assistant
 * @since 2.40
 */
class MLA_WPML_Shortcodes {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * This function contains add_action and add_filter calls.
	 *
	 * @since 2.40
	 *
	 * @return	void
	 */
	public static function initialize() {
		 /*
		  * Defined in /media-library-assistant/includes/class-mla-shortcode-support.php
		  */
		add_filter( 'mla_get_terms_query_arguments', 'MLA_WPML_Shortcodes::mla_get_terms_query_arguments', 10, 1 );
		add_filter( 'mla_get_terms_clauses', 'MLA_WPML_Shortcodes::mla_get_terms_clauses', 10, 1 );
	}

	/**
	 * MLA Tag Cloud Query Arguments
	 *
	 * Saves [mla_tag_cloud] query parameters for use in MLA_WPML_Shortcodes::mla_get_terms_clauses.
	 *
	 * @since 2.40
	 * @uses MLA_WPML_Shortcodes::$all_query_parameters
	 *
	 * @param	array	shortcode arguments merged with attachment selection defaults
	 */
	public static function mla_get_terms_query_arguments( $all_query_parameters ) {
		self::$all_query_parameters = $all_query_parameters;

		return $all_query_parameters;
	} // mla_get_terms_query_arguments

	/**
	 * Save the query arguments
	 *
	 * @since 2.40
	 *
	 * @var	array
	 */
	private static $all_query_parameters = array();

	/**
	 * MLA Tag Cloud Query Clauses
	 *
	 * Adds language-specific clauses to filter the cloud terms.
	 *
	 * @since 2.11
	 * @uses MLA_WPML_Shortcodes::$all_query_parameters
	 *
	 * @param	array	SQL clauses ( 'fields', 'join', 'where', 'order', 'orderby', 'limits' )
	 */
	public static function mla_get_terms_clauses( $clauses ) {
		global $wpdb, $sitepress;

		if ( 'all' != ( $current_language = $sitepress->get_current_language() ) ) {
			$clauses['join'] = preg_replace( '/(^.* AS tt ON t.term_id = tt.term_id)/m', '${1}' . ' JOIN `' . $wpdb->prefix . 'icl_translations` AS icl_t ON icl_t.element_id = tt.term_taxonomy_id', $clauses['join'] );

			$clauses['where'] .= " AND icl_t.language_code = '" . $current_language . "'";

			if ( is_string( $query_taxonomies = self::$all_query_parameters['taxonomy'] ) ) {
				$query_taxonomies = array ( $query_taxonomies );
			}

			$taxonomies = array();
			foreach ( $query_taxonomies as $taxonomy) {
				$taxonomies[] = 'tax_' . $taxonomy;
			}

			$clauses['where'] .= "AND icl_t.element_type IN ( '" . join( "','", $taxonomies ) . "' )";
		}

		return $clauses;
	} // mla_get_terms_clauses
} // Class MLA_WPML_Shortcodes
?>
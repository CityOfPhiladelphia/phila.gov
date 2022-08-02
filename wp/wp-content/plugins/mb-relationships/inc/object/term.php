<?php
/**
 * The term object that handle query arguments for "to" and list for "from" relationships.
 *
 * @package    Meta Box
 * @subpackage MB Relationships
 */

/**
 * The term object.
 */
class MBR_Term implements MBR_Object_Interface {
	/**
	 * Get current object ID.
	 *
	 * @return int
	 */
	public function get_current_admin_id() {
		return filter_input( INPUT_GET, 'tag_ID', FILTER_SANITIZE_NUMBER_INT );
	}

	/**
	 * Get current object ID.
	 *
	 * @return int
	 */
	public function get_current_id() {
		return get_queried_object_id();
	}

	/**
	 * Get HTML link to the object.
	 *
	 * @param int $id Object ID.
	 *
	 * @return string
	 */
	public function get_link( $id ) {
		$term = get_term( $id );
		return '<a href="' . get_edit_term_link( $id ) . '">' . esc_html( $term->name ) . '</a>';
	}

	/**
	 * Render HTML of the object to show in the frontend.
	 *
	 * @param WP_Term $item Term object.
	 *
	 * @return string
	 */
	public function render( $item, $atts ) {
		if ( 'false' === $atts['link'] ) {
			return esc_html( $item->name );
		}
		return '<a href="' . get_term_link( $item ) . '">' . esc_html( $item->name ) . '</a>';
	}

	/**
	 * Render HTML of the object on the back end (admin column).
	 *
	 * @param WP_Post $item Post object.
	 * @return string
	 */
	public function render_admin( $item, $config ) {
		$text = $item->name;
		if ( empty( $config['link'] ) || 'view' === $config['link'] ) {
			$link = get_term_link( $item );
		}
		if ( false === $config['link'] ) {
			return $text;
		}
		if ( 'edit' === $config['link'] ) {
			$link = get_edit_term_link( $item );
		}
		return '<a href="' . esc_url( $link ) . '">' . esc_html( $text ) . '</a>';
	}

	/**
	 * Get database ID field.
	 *
	 * @return string
	 */
	public function get_db_field() {
		return 'term_id';
	}
}

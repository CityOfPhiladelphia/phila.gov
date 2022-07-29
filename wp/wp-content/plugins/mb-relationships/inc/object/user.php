<?php
/**
 * The user object that handle query arguments for "to" and list for "from" relationships.
 *
 * @package    Meta Box
 * @subpackage MB Relationships
 */

/**
 * The user object.
 */
class MBR_User implements MBR_Object_Interface {
	/**
	 * Get current object ID.
	 *
	 * @return int
	 */
	public function get_current_admin_id() {
		$user_id = false;
		$screen  = get_current_screen();
		if ( 'profile' === $screen->id ) {
			$user_id = get_current_user_id();
		} elseif ( 'user-edit' === $screen->id ) {
			$user_id = isset( $_REQUEST['user_id'] ) ? absint( $_REQUEST['user_id'] ) : false;
		}

		return $user_id;
	}

	/**
	 * Get current object ID.
	 *
	 * @return int
	 */
	public function get_current_id() {
		return get_current_user_id();
	}

	/**
	 * Get HTML link to the object.
	 *
	 * @param int $id Object ID.
	 *
	 * @return string
	 */
	public function get_link( $id ) {
		$user = get_userdata( $id );
		return '<a href="' . admin_url( 'user-edit.php?user_id=' . $id ) . '">' . esc_html( $user->display_name ) . '</a>';
	}

	/**
	 * Render HTML of the object to show in the frontend.
	 *
	 * @param WP_User $item User object.
	 *
	 * @return string
	 */
	public function render( $item, $atts ) {
		return $item->display_name;
	}

	/**
	 * Render HTML of the object on the back end (admin column).
	 *
	 * @param WP_Post $item Post object.
	 * @return string
	 */
	public function render_admin( $item, $config ) {
		$text = $item->display_name;
		if ( empty( $config['link'] ) || 'view' === $config['link'] ) {
			$link = get_author_posts_url( $item->ID );
		}
		if ( false === $config['link'] ) {
			return $text;
		}
		if ( 'edit' === $config['link'] ) {
			$link = get_edit_user_link( $item->ID );
		}
		return '<a href="' . esc_url( $link ) . '">' . esc_html( $text ) . '</a>';
	}

	/**
	 * Get database ID field.
	 *
	 * @return string
	 */
	public function get_db_field() {
		return 'ID';
	}
}

<?php
/**
 * Group field class.
 * @package    Meta Box
 * @subpackage Meta Box Group
 */

/**
 * Class for group field.
 * @package    Meta Box
 * @subpackage Meta Box Group
 */
class RWMB_Group_Field extends RWMB_Field
{
	/**
	 * Queue to store the group fields' meta(s). Used to get child field meta.
	 * @var array
	 */
	protected static $meta_queue = array();

	/**
	 * Enqueue scripts and styles.
	 */
	public static function admin_enqueue_scripts()
	{
		wp_enqueue_style( 'rwmb-group', plugins_url( 'group.css', __FILE__ ) );
		wp_enqueue_script( 'rwmb-group', plugins_url( 'group.js', __FILE__ ) );
	}

	/**
	 * Get group field HTML.
	 *
	 * @param mixed $meta
	 * @param array $field
	 * @return string
	 */
	public static function html( $meta, $field )
	{
		ob_start();

		// Add filter to child field meta value, make sure it's added only once
		if ( empty( self::$meta_queue ) )
		{
			add_filter( 'rwmb_field_meta', array( __CLASS__, 'child_field_meta' ), 10, 3 );
		}

		// Add group value to the queue
		array_unshift( self::$meta_queue, $meta );

		// Add clone index to make sure each child field has an unique ID.
		$clone_index = '';
		if ( $field['clone'] && preg_match( '|_\d+$|', $field['id'], $match ) )
		{
			$clone_index = $match[0];
		}

		foreach ( $field['fields'] as $child_field )
		{
			$child_field['attributes']['name'] = $child_field['field_name'] = self::child_field_name( $field['field_name'], $child_field['field_name'] );
			$child_field['attributes']['id']   = ( isset( $child_field['attributes']['id'] ) ? $child_field['attributes']['id'] : $child_field['id'] ) . $clone_index;
			call_user_func( array( RW_Meta_Box::get_class_name( $child_field ), 'show' ), $child_field, RWMB_Group::$saved );
		}

		// Remove group value from the queue
		array_shift( self::$meta_queue );

		// Remove filter to child field meta value and reset class's parent field's meta
		if ( empty( self::$meta_queue ) )
		{
			remove_filter( 'rwmb_field_meta', array( __CLASS__, 'child_field_meta' ) );
		}
		return ob_get_clean();
	}

	/**
	 * Change the way we get meta value for child fields
	 *
	 * @param mixed $meta        Meta value
	 * @param array $child_field Child field
	 * @param bool  $saved       Has the meta box been saved?
	 *
	 * @return mixed
	 */
	public static function child_field_meta( $meta, $child_field, $saved )
	{
		$group_meta = reset( self::$meta_queue );
		$child_id   = $child_field['id'];
		if ( isset( $group_meta[$child_id] ) )
		{
			return $group_meta[$child_id];
		}
		if ( ! $saved && isset( $child_field['std'] ) )
		{
			return $child_field['std'];
		}
		if ( $child_field['multiple'] )
		{
			return array();
		}
		return '';
	}

	/**
	 * Get meta value, make sure value is an array (of arrays if field is cloneable)
	 * Don't escape value
	 *
	 * @param int   $post_id
	 * @param bool  $saved
	 * @param array $field
	 *
	 * @return mixed
	 */
	public static function meta( $post_id, $saved, $field )
	{
		$meta = get_post_meta( $post_id, $field['id'], true ); // Always save as single value

		// Use $field['std'] only when the meta box hasn't been saved (i.e. the first time we run)
		$meta = ! $saved && '' === $meta ? $field['std'] : $meta;

		// Make sure returned value is an array
		if ( empty( $meta ) )
			$meta = array();

		// If cloneable, make sure each sub-value is an array
		if ( $field['clone'] )
		{
			// Make sure there's at least 1 sub-value
			if ( empty( $meta ) )
				$meta[0] = array();

			foreach ( $meta as $k => $v )
			{
				$meta[$k] = (array) $v;
			}
		}

		return $meta;
	}

	/**
	 * Change child field name to form parent[child]
	 *
	 * @param string $parent Parent field's name
	 * @param string $child  Child field's name
	 * @return string
	 */
	public static function child_field_name( $parent, $child )
	{
		$pos  = strpos( $child, '[' );
		$pos  = false === $pos ? strlen( $child ) : $pos;
		$name = $parent . '[' . substr( $child, 0, $pos ) . ']' . substr( $child, $pos );

		return $name;
	}

	/**
	 * Add clone button.
	 *
	 * @param array $field Field parameter
	 * @return string $html
	 */
	public static function add_clone_button( $field )
	{
		$text = apply_filters( 'rwmb_group_add_clone_button_text', __( '+ Add more', 'meta-box-group' ), $field );
		return "<a href='#' class='rwmb-button button-primary add-clone'>$text</a>";
	}
}

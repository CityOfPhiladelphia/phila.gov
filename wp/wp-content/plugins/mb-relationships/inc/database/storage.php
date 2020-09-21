<?php
class MBR_Storage {
	/**
	 * Relationship factory.
	 */
	private $factory;

	public function __construct( MBR_Relationship_Factory $factory ) {
		$this->factory = $factory;
	}

	/**
	 * Retrieve metadata for the specified object.
	 *
	 * @param int        $object_id ID of the object metadata is for. In this case, it will be a row's id
	 *                              of table.
	 * @param string     $meta_key  Optional. Metadata key. If not specified, retrieve all metadata for
	 *                              the specified object. In this case, it will be column name.
	 * @param bool|array $args      Optional, default is false.
	 *                              If true, return only the first value of the specified meta_key.
	 *                              If is array, use the `single` element.
	 *                              This parameter has no effect if meta_key is not specified.
	 *
	 * @return mixed Single metadata value, or array of values.
	 */
	public function get( $object_id, $meta_key, $args = false ) {
		global $wpdb;

		$type         = $this->get_type( $meta_key );
		$relationship = $this->factory->get( $type );

		if ( $relationship->reciprocal ) {
			$results = $wpdb->get_results( $wpdb->prepare(
				"SELECT `to`, `ID`, `order_from` AS `order`
				FROM {$wpdb->mb_relationships}
				WHERE `from`=%d AND `type`=%s 
				UNION
				SELECT `from`, `ID`, `order_to` AS `order`
				FROM {$wpdb->mb_relationships}
				WHERE `to`=%d AND `type`=%s
				ORDER BY `order` ASC, `ID` DESC",
				$object_id,
				$type,
				$object_id,
				$type
			), ARRAY_N );

			return array_map( function( $pair ) {
				return reset( $pair );
			}, $results );
		}

		$target = $this->get_target( $meta_key );
		$source = $this->get_source( $meta_key );

		return $wpdb->get_col( $wpdb->prepare(
			"SELECT `$target` FROM {$wpdb->mb_relationships} WHERE `$source`=%d AND `type`=%s ORDER BY order_$source",
			$object_id,
			$type
		) );
	}

	/**
	 * Add metadata to cache
	 *
	 * @param int    $object_id  ID of the object metadata is for.
	 * @param string $meta_key   Metadata key.
	 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
	 * @param bool   $unique     Optional, default is false.
	 *                           Whether the specified metadata key should be unique for the object.
	 *                           If true, and the object already has a value for the specified metadata key,
	 *                           no change will be made.
	 */
	public function add( $object_id, $meta_key, $meta_value, $unique = false ) {
	}

	/**
	 * Update object relationships.
	 *
	 * @param int    $object_id  ID of the object metadata is for.
	 * @param string $meta_key   Metadata key.
	 * @param mixed  $meta_value Metadata value. Must be serializable if non-scalar.
	 * @param mixed  $prev_value Optional. If specified, only update existing metadata entries with
	 *                           the specified value. Otherwise, update all entries.
	 *
	 * @return bool
	 */
	public function update( $object_id, $meta_key, $meta_value, $prev_value = '' ) {
		global $wpdb;

		$meta_value = array_unique( array_filter( (array) $meta_value ) );
		$target     = $this->get_target( $meta_key );
		$source     = $this->get_source( $meta_key );
		$type       = $this->get_type( $meta_key );
		$orders     = $this->get_target_orders( $object_id, $type, $source, $target );

		$this->delete( $object_id, $meta_key );

		$x = 0;
		foreach ( $meta_value as $id ) {
			$x++;
			$order = isset( $orders[ $id ] ) ? $orders[ $id ] : 0;
			$wpdb->insert( $wpdb->mb_relationships, [
				$source         => $object_id,
				$target         => $id,
				'type'          => $type,
				"order_$source" => $x,
				"order_$target" => $order,
			], ['%d', '%d', '%s', '%d', '%d'] );
		}
		return true;
	}

	/**
	 * Delete object relationships.
	 *
	 * @param int    $object_id  ID of the object metadata is for.
	 * @param string $meta_key   Metadata key. If empty, delete row.
	 * @param mixed  $meta_value Optional. Metadata value. Must be serializable if non-scalar. If specified, only delete
	 *                           metadata entries with this value. Otherwise, delete all entries with the specified meta_key.
	 *                           Pass `null, `false`, or an empty string to skip this check. (For backward compatibility,
	 *                           it is not possible to pass an empty string to delete those entries with an empty string
	 *                           for a value).
	 * @param bool   $delete_all Optional, default is false. If true, delete matching metadata entries for all objects,
	 *                           ignoring the specified object_id. Otherwise, only delete matching metadata entries for
	 *                           the specified object_id.
	 *
	 * @return bool True on successful delete, false on failure.
	 */
	public function delete( $object_id, $meta_key = '', $meta_value = '', $delete_all = false ) {
		global $wpdb;

		$type   = $this->get_type( $meta_key );
		$source = $this->get_source( $meta_key );

		$wpdb->delete( $wpdb->mb_relationships, [
			$source => $object_id,
			'type'  => $type,
		] );

		$relationship = $this->factory->get( $type );
		if ( $relationship->reciprocal ) {
			$target = $this->get_target( $meta_key );
			$wpdb->delete( $wpdb->mb_relationships, [
				$target => $object_id,
				'type'  => $type,
			] );
		}
		return true;
	}

	/**
	 * Get relationship type from submitted field name "{$type}_to" or "{$type}_from".
	 *
	 * @param string $name Submitted field name.
	 * @return string
	 */
	private function get_type( $name ) {
		return substr( $name, 0, -1 - strlen( $this->get_target( $name ) ) );
	}

	/**
	 * Get relationship target from submitted field name "{$type}_to" or "{$type}_from".
	 *
	 * @param string $name Submitted field name.
	 * @return string
	 */
	private function get_target( $name ) {
		return '_to' === substr( $name, -3 ) ? 'to' : 'from';
	}

	/**
	 * Get relationship source from submitted field name "{$type}_to" or "{$type}_from".
	 *
	 * @param string $name Submitted field name.
	 * @return string
	 */
	private function get_source( $name ) {
		$target = $this->get_target( $name );
		return 'to' === $target ? 'from' : 'to';
	}

	/**
	 * Get orders of connected objects (in the target column).
	 *
	 * @return array Array of [object_id => order].
	 */
	private function get_target_orders( $object_id, $type, $source, $target ) {
		global $wpdb;

		$items = $wpdb->get_results( $wpdb->prepare(
			"SELECT `$target` AS `id`, `order_$target` AS `order` FROM {$wpdb->mb_relationships} WHERE `$source` = %d AND `type` = %s
			UNION SELECT `$source` AS `id`, `order_$source` AS `order` FROM {$wpdb->mb_relationships} WHERE `$target` = %d AND `type` = %s",
			$object_id,
			$type,
			$object_id,
			$type
		), ARRAY_A );
		return wp_list_pluck( $items, 'order', 'id' );
	}
}

<?php
/**
 * Public API helper functions.
 */

class MB_Relationships_API {
	/**
	 * Reference to relationship factory.
	 */
	private static $factory;

	/**
	 * Reference to post query object.
	 */
	private static $post_query;

	/**
	 * Reference to term query object.
	 */
	private static $term_query;

	/**
	 * Reference to user query object.
	 */
	private static $user_query;

	public static function set_relationship_factory( MBR_Relationship_Factory $factory ) {
		self::$factory = $factory;
	}

	public static function set_post_query( MBR_Query_Post $post_query ) {
		self::$post_query = $post_query;
	}

	public static function set_term_query( MBR_Query_Term $term_query ) {
		self::$term_query = $term_query;
	}

	public static function set_user_query( MBR_Query_User $user_query ) {
		self::$user_query = $user_query;
	}

	/**
	 * Register a relationship.
	 *
	 * @param array $settings Relationship parameters.
	 * @return MBR_Relationship
	 */
	public static function register( $settings ) {
		return self::$factory->build( $settings );
	}

	public static function get_relationship( $id ) {
		return self::$factory->get( $id );
	}

	public static function get_relationship_settings( $id ) {
		return self::$factory->get_settings( $id );
	}

	public static function get_all_relationships() {
		return self::$factory->all();
	}

	public static function get_all_relationships_settings() {
		return self::$factory->all_settings();
	}

	/**
	 * Check if 2 objects has a relationship.
	 *
	 * @param int    $from From object ID.
	 * @param int    $to   To object ID.
	 * @param string $id   Relationship ID.
	 * @return bool
	 */
	public static function has( $from, $to, $id ) {
		$relationship = self::$factory->get( $id );
		return $relationship ? $relationship->has( $from, $to ) : false;
	}

	/**
	 * Add a relationship for 2 objects.
	 *
	 * @param int    $from       From object ID.
	 * @param int    $to         To object ID.
	 * @param string $id         Relationship ID.
	 * @param int    $order_from The order on the "from" side.
	 * @param int    $order_to   The order on the "to" side.
	 * @return bool
	 */
	public static function add( $from, $to, $id, $order_from = 1, $order_to = 1 ) {
		$relationship = self::$factory->get( $id );

		return $relationship ? $relationship->add( $from, $to, $order_from, $order_to ) : false;
	}

	/**
	 * Delete a relationship for 2 objects.
	 *
	 * @param int    $from From object ID.
	 * @param int    $to   To object ID.
	 * @param string $id   Relationship ID.
	 * @return bool
	 */
	public static function delete( $from, $to, $id ) {
		$relationship = self::$factory->get( $id );
		return $relationship ? $relationship->delete( $from, $to ) : false;
	}

	/**
	 * Get connected items for each object in the list.
	 *
	 * @param array $args       Relationship query arguments.
	 * @param array $query_vars Extra query variables.
	 */
	public static function each_connected( $args, $query_vars = array() ) {
		$args         = wp_parse_args( $args, [
			'id'       => '',
			'property' => 'connected',
		] );
		$relationship = self::$factory->get( $args['id'] );
		if ( ! $relationship ) {
			return;
		}

		$direction    = isset( $args['from'] ) ? 'from' : 'to';
		$connected    = isset( $args['from'] ) ? 'to' : 'from';
		$object_type  = $relationship->get_object_type( $connected );
		$id_key       = $relationship->get_db_field( $direction );
		$query_object = $object_type . '_query';

		// if this is not reciprocal we need to derive the relationship key
		$relationship_key = 'mbr_' .  $args['id'] . '_' . $direction;
		$items = self::$$query_object->query( $args, $query_vars, $relationship );
		self::distribute( $args[ $direction ], $items, $args['property'], $id_key, $relationship_key );
	}

	/**
	 * Get connected items.
	 *
	 * @param array $args Relationship arguments.
	 * @return array
	 */
	public static function get_connected( $args ) {
		$args         = wp_parse_args( $args, [
			'id' => '',
		] );
		$relationship = self::$factory->get( $args['id'] );
		if ( ! $relationship ) {
			return array();
		}

		$connected    = isset( $args['from'] ) ? 'to' : 'from';
		$object_type  = $relationship->get_object_type( $connected );
		$query_object = $object_type . '_query';

		return self::$$query_object->query( $args, array(), $relationship );
	}

	/**
	 * Given a list of objects and another list of connected items,
	 * distribute each connected item to it's respective counterpart.
	 *
	 * @param array  $items     				List of objects.
	 * @param array  $connected 				List of connected objects.
	 * @param string $property  				Name of connected array property.
	 * @param string $id_key    				ID key of the objects.
	 * @param string $relationship_key	Non-reciprocal constructed key.
	 * @return array
	 */
	private static function distribute( &$items, $connected, $property, $id_key, $relationship_key ) {
		foreach ( $items as &$item ) {
			$item->$property = self::filter( $connected, $item->$id_key, $relationship_key );
		}
		return $items;
	}

	/**
	 * Filter to find the matched items.
	 * Non-reciprocal relationships: uses constructed key.
	 * Reciprocal relationships: uses mbr_from and mbr_to keys.
	 *
	 * @param array  $items     				Connected items.
	 * @param string $object_id 				Connected object ID.
	 * @param string $relationship_key	Non-reciprocal constructed key.
	 * @return array
	 */
	private static function filter( $items, $object_id, $relationship_key ) {
		$items = array_filter( $items, function( $item ) use ( $object_id, $relationship_key ) {
			return ( isset( $item->$relationship_key ) && $item->$relationship_key == $object_id )
				|| ( isset( $item->mbr_from ) && $item->mbr_from == $object_id )
				|| ( isset( $item->mbr_to ) && $item->mbr_to == $object_id );
		} );
		return $items;
	}
}

<?php
/**
 * The relationship class.
 * Registers meta boxes and custom fields for objects, displays and handles data.
 *
 * @package    Meta Box
 * @subpackage MB Relationships
 */

/**
 * The relationship class.
 *
 * @property array  $from From side settings.
 * @property array  $to   To side settings.
 * @property string $id   Relationship ID.
 */
class MBR_Relationship {
	/**
	 * The relationship settings.
	 *
	 * @var array
	 */
	protected $settings;

	/**
	 * The object factory.
	 *
	 * @var MBR_Object_Factory
	 */
	private $object_factory;

	/**
	 * Register a relationship.
	 *
	 * @param array              $settings       Relationship settings.
	 * @param MBR_Object_Factory $object_factory The instance of the API class.
	 */
	public function __construct( $settings, MBR_Object_Factory $object_factory ) {
		$this->settings       = $settings;
		$this->object_factory = $object_factory;
	}

	/**
	 * Magic method to quick access to relationship settings.
	 *
	 * @param string $name Setting name.
	 *
	 * @return mixed
	 */
	public function __get( $name ) {
		return isset( $this->settings[ $name ] ) ? $this->settings[ $name ] : '';
	}

	/**
	 * Check if 2 objects has a relationship.
	 *
	 * @param int $from From object ID.
	 * @param int $to   To object ID.
	 *
	 * @return bool
	 */
	public function has( $from, $to ) {
		global $wpdb;

		$rel_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT `ID` FROM {$wpdb->mb_relationships} WHERE `from`=%d AND `to`=%d AND `type`=%s",
				$from,
				$to,
				$this->id
			)
		);

		return (bool) $rel_id;
	}

	/**
	 * Add a relationship for 2 objects.
	 *
	 * @param int $from       From object ID.
	 * @param int $to         To object ID.
	 * @param int $order_from The order on the "from" side.
	 * @param int $order_to   The order on the "to" side.
	 *
	 * @return bool
	 */
	public function add( $from, $to, $order_from, $order_to ) {
		global $wpdb;

		if ( $this->has( $from, $to ) ) {
			return false;
		}

		$result = $wpdb->insert(
			$wpdb->mb_relationships,
			array(
				'from'       => $from,
				'to'         => $to,
				'type'       => $this->id,
				'order_from' => $order_from,
				'order_to'   => $order_to,
			),
			array(
				'%d',
				'%d',
				'%s',
				'%d',
				'%d',
			)
		);
		do_action( 'mb_relationships_add', $from, $to, $this->id, $order_from, $order_to, $result );
		return $result;
	}

	/**
	 * Delete a relationship for 2 objects.
	 *
	 * @param int $from From object ID.
	 * @param int $to   To object ID.
	 *
	 * @return bool
	 */
	public function delete( $from, $to ) {
		global $wpdb;

		$result = $wpdb->delete(
			$wpdb->mb_relationships,
			array(
				'from' => $from,
				'to'   => $to,
				'type' => $this->id,
			)
		);
		do_action( 'mb_relationships_delete', $from, $to, $this->id, $result );
		return $result;
	}

	/**
	 * Get relationship object types.
	 *
	 * @param string $side "from" or "to".
	 *
	 * @return string
	 */
	public function get_object_type( $side ) {
		return $this->{$side}['object_type'];
	}

	/**
	 * Check if the relationship has an object type on either side.
	 *
	 * @param mixed $type Object type.
	 *
	 * @return bool
	 */
	public function has_object_type( $type ) {
		return $type === $this->get_object_type( 'from' ) || $type === $this->get_object_type( 'to' );
	}

	/**
	 * Get the database ID field of "from" or "to" object.
	 *
	 * @param string $side "from" or "to".
	 *
	 * @return string
	 */
	public function get_db_field( $side ) {
		$object = $this->object_factory->build( $this->get_object_type( $side ) );

		return $object->get_db_field();
	}
}

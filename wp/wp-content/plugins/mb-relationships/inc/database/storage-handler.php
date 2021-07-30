<?php
/**
 * Storage handler, which sets the correct storage for meta box objects.
 *
 * @package    Meta Box
 * @subpackage MB Relationships
 */

/**
 * Storage handler class.
 */
class MBR_Storage_Handler {
	/**
	 * Reference to relationship factory.
	 *
	 * @var MBR_Relationship_Factory
	 */
	protected $factory;

	/**
	 * The storage object for relationships table.
	 *
	 * @var RWMB_Storage_Interface
	 */
	protected $storage;

	/**
	 * Constructor.
	 *
	 * @param MBR_Relationship_Factory $factory Reference to relationship factory.
	 */
	public function __construct( MBR_Relationship_Factory $factory ) {
		$this->factory = $factory;
	}

	/**
	 * Class initialize.
	 */
	public function init() {
		add_filter( 'rwmb_get_storage', array( $this, 'filter_storage' ), 10, 3 );
		add_action( 'deleted_post', array( $this, 'delete_object_data' ) );
		add_action( 'deleted_user', array( $this, 'delete_object_data' ) );
		add_action( 'delete_term', array( $this, 'delete_object_data' ) );
	}

	/**
	 * Filter storage object.
	 *
	 * @param RWMB_Storage_Interface $storage     Storage object.
	 * @param string                 $object_type Object type.
	 * @param RW_Meta_Box            $meta_box    Meta box object.
	 *
	 * @return mixed
	 */
	public function filter_storage( $storage, $object_type, $meta_box ) {
		global $wpdb;

		if ( ! $meta_box || ! $this->is_relationships( $meta_box ) ) {
			return $storage;
		}
		if ( ! $this->storage ) {
			$this->storage = new MBR_Storage( $this->factory );
		}

		return $this->storage;
	}

	/**
	 * Check if meta box is relationships.
	 *
	 * @param RW_Meta_Box $meta_box Meta box object.
	 *
	 * @return bool
	 */
	protected function is_relationships( $meta_box ) {
		return 'relationships_table' === $meta_box->storage_type;
	}

	/**
	 * Delete object data in cache and in the database.
	 *
	 * @param int $object_id Object ID.
	 */
	public function delete_object_data( $object_id ) {
		$object_type   = str_replace( array( 'deleted_', 'delete_' ), '', current_filter() );
		$relationships = $this->factory->filter_by( $object_type );
		foreach ( $relationships as $relationship ) {
			$this->delete_object_relationships( $object_id, $relationship->id );
		}
	}

	/**
	 * Delete all relationships to an object.
	 *
	 * @param int    $object_id ID of the object metadata is for.
	 * @param string $type      The relationship type.
	 */
	protected function delete_object_relationships( $object_id, $type ) {
		global $wpdb;
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $wpdb->mb_relationships WHERE `type`=%s AND (`from`=%d OR `to`=%d)",
				$type,
				$object_id,
				$object_id
			)
		);
	}
}

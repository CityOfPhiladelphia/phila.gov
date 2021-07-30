<?php
/**
 * Normalizes the query arguments.
 *
 * @package    Meta Box
 * @subpackage MB Relationships
 */

/**
 * Normalizer class.
 */
class MBR_Query_Normalizer {
	/**
	 * The relationship factory.
	 *
	 * @var MBR_Relationship_Factory
	 */
	protected $factory;

	/**
	 * Constructor
	 *
	 * @param MBR_Relationship_Factory $factory The relationship factory.
	 */
	public function __construct( MBR_Relationship_Factory $factory ) {
		$this->factory = $factory;
	}

	/**
	 * Normalize relationship query args.
	 *
	 * @param array $args Query arguments.
	 */
	public function normalize( &$args ) {
		// Query by single relationship.
		if ( ! isset( $args['relation'] ) ) {
			$args = $this->normalize_args( $args );
			return;
		}

		// Query by multiple relationships.
		$new_args = array(
			'relation' => $args['relation'],
		);
		unset( $args['relation'] );
		foreach ( $args as $value ) {
			$value = $this->normalize_args( $value );
			array_push( $new_args, $value );
		}
		$args = $new_args;
	}

	/**
	 * Get object IDs from list of objects.
	 *
	 * @param array  $items    Array of objects or IDs.
	 * @param string $id_field Object ID field.
	 *
	 * @return array
	 */
	protected function get_ids( $items, $id_field ) {
		$items = (array) $items;
		$first = reset( $items );
		return is_numeric( $first ) ? $items : wp_list_pluck( $items, $id_field );
	}

	/**
	 * Normalizes single relationship query arguments.
	 *
	 * @param array $args Query arguments.
	 */
	protected function normalize_args( $args ) {
		$direction    = isset( $args['from'] ) ? 'from' : 'to';
		$relationship = $this->factory->get( $args['id'] );

		$args['id_field']   = $relationship->get_db_field( $direction );
		$args['direction']  = $direction;
		$args['items']      = $this->get_ids( $args[ $direction ], $args['id_field'] );
		$args['reciprocal'] = $relationship->reciprocal;
		$args['from']       = isset( $args['from'] ) ? $args['from'] : null;
		$args['to']         = isset( $args['to'] ) ? $args['to'] : null;

		unset( $args[ $direction ] );
		return $args;
	}
}

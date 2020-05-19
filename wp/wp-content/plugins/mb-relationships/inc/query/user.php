<?php
/**
 * Query for related users using WP_Query.
 *
 * @package    Meta Box
 * @subpackage MB Relationships
 */

/**
 * Class MBR_Query_User
 */
class MBR_Query_User {
	/**
	 * Query normalizer.
	 *
	 * @var MBR_Query_Normalizer
	 */
	protected $normalizer;

	/**
	 * Constructor
	 *
	 * @param MBR_Query_Normalizer $normalizer Query normalizer.
	 */
	public function __construct( MBR_Query_Normalizer $normalizer ) {
		$this->normalizer = $normalizer;
	}

	/**
	 * Filter the WordPress query to get connected users.
	 */
	public function init() {
		add_action( 'pre_user_query', array( $this, 'parse_query' ), 20 );
	}

	/**
	 * Parse query variables.
	 * Fires after the main query vars have been parsed.
	 *
	 * @param WP_User_Query $query The current WP_User_Query instance, passed by reference.
	 */
	public function parse_query( WP_User_Query $query ) {
		global $wpdb;

		$args = $query->get( 'relationship' );
		if ( ! $args ) {
			return;
		}
		$this->normalizer->normalize( $args );

		$relationship_query = new MBR_Query( $args );

		$clauses = array();
		$map     = array(
			'fields' => 'query_fields',
			'join'   => 'query_from',
			'where'  => 'query_where',
		);
		foreach ( $map as $clause => $key ) {
			$clauses[ $clause ] = $query->$key;
		}
		$clauses = $relationship_query->alter_clauses( $clauses, "$wpdb->users.ID" );

		foreach ( $map as $clause => $key ) {
			$query->$key = $clauses[ $clause ];
		}
	}

	/**
	 * Query and get list of items.
	 *
	 * @param array            $args         Relationship arguments.
	 * @param array            $query_vars   Extra query variables.
	 * @param MBR_Relationship $relationship Relationship object.
	 *
	 * @return array
	 */
	public function query( $args, $query_vars, $relationship ) {
		$query_vars = wp_parse_args(
			$query_vars,
			array(
				'relationship' => $args,
			)
		);
		return get_users( $query_vars );
	}
}

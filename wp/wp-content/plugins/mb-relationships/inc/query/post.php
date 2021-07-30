<?php
/**
 * Query for related posts using WP_Query.
 *
 * @package    Meta Box
 * @subpackage MB Relationships
 */

/**
 * Post query class.
 */
class MBR_Query_Post {
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
	 * Filter the WordPress query to get connected posts.
	 */
	public function init() {
		add_action( 'parse_query', array( $this, 'parse_query' ), 20 );
		add_filter( 'posts_clauses', array( $this, 'posts_clauses' ), 20, 2 );
	}

	/**
	 * Parse query variables.
	 * Fires after the main query vars have been parsed.
	 *
	 * @param WP_Query $query The WP_Query instance (passed by reference).
	 */
	public function parse_query( WP_Query $query ) {
		$args = $query->get( 'relationship' );
		if ( ! $args ) {
			return;
		}
		$this->normalizer->normalize( $args );
		$query->set( 'relationship', $args );

		$query->relationship_query = new MBR_Query( $args );

		$post_type = $query->get( 'post_type' );
		if ( ! $post_type ) {
			$relationship = MB_Relationships_API::get_relationship( $args['id'] );
			$target       = 'from' === $args['direction'] ? 'to' : 'from';
			$post_type    = isset( $relationship->$target['field']['post_type'] )
				? $relationship->$target['field']['post_type']
				: 'any';
			$query->set( 'post_type', $post_type );
		}
		$query->set( 'suppress_filters', false );
		$query->set( 'ignore_sticky_posts', true );

		$query->is_home    = false;
		$query->is_archive = true;
	}

	/**
	 * Filters all query clauses at once, for convenience.
	 *
	 * Covers the WHERE, GROUP BY, JOIN, ORDER BY, DISTINCT,
	 * fields (SELECT), and LIMITS clauses.
	 *
	 * @param array    $clauses The list of clauses for the query.
	 * @param WP_Query $query   The WP_Query instance (passed by reference).
	 *
	 * @return array
	 */
	public function posts_clauses( $clauses, WP_Query $query ) {
		global $wpdb;

		if ( ! isset( $query->relationship_query ) ) {
			return $clauses;
		}

		return $query->relationship_query->alter_clauses( $clauses, "$wpdb->posts.ID", isset( $query->query['orderby'] ) );
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
		$query_vars = wp_parse_args( $query_vars, [
			'relationship'        => $args,
			'nopaging'            => true,
			'suppress_filters'    => false,
			'ignore_sticky_posts' => true,
		] );
		$query      = new WP_Query( $query_vars );
		return $query->posts;
	}
}


<?php
/**
 * The relationship query class that alters the WordPress query to get the connected items.
 */

class MBR_Query {
	/**
	 * The relationship query variables.
	 *
	 * @var array
	 */
	private $args;

	public function __construct( $args ) {
		$this->args = $args;
	}

	/**
	 * Modify the WordPress query to get connected object.
	 *
	 * @param array  $clauses         Query clauses.
	 * @param string $id_column       Database column for object ID.
	 * @param bool   $pass_thru_order If TRUE use the WP_Query orderby clause.
	 *
	 * @return mixed
	 */
	public function alter_clauses( &$clauses, $id_column, $pass_thru_order = false ) {
		$this->handle_query_join( $clauses, $id_column, $pass_thru_order );

		if ( empty( $this->args['relation'] ) && ! empty( $this->args['sibling'] ) ) {
			$this->handle_query_sibling( $clauses, $id_column );
		}

		global $wpdb;
		$clauses['groupby'] = empty( $clauses['groupby'] ) ? "$wpdb->posts.ID" : "{$clauses['groupby']}, $wpdb->posts.ID";

		return $clauses;
	}

	/**
	 * Modify query JOIN statement. Support querying by multiple relationships.
	 *
	 * @param array  $clauses         Query clauses.
	 * @param string $id_column       Database column for object ID.
	 * @param bool   $pass_thru_order If TRUE use the WP_Query orderby clause.
	 */
	public function handle_query_join( &$clauses, $id_column, $pass_thru_order ) {
		global $wpdb;

		$join_type     = 'AND';
		$relationships = [];

		if ( isset( $this->args['relation'] ) ) {
			$join_type = $this->args['relation'];
			unset( $this->args['relation'] );
			$relationships = $this->args;
		} else {
			$relationships[] = $this->args;
		}

		$joins = [];
		foreach ( $relationships as $relationship ) {
			$joins[] = $this->build_join( $relationship, $clauses, $id_column, $pass_thru_order );
		}
		$joins = implode( " $join_type ", $joins );

		$clauses['join'] .= " INNER JOIN $wpdb->mb_relationships AS mbr ON $joins";
	}

	private function build_join( $relationship, &$clauses, $id_column, $pass_thru_order ) {
		global $wpdb;

		$source = $relationship['direction'];
		$target = 'from' === $source ? 'to' : 'from';
		$items  = implode( ',', array_map( 'absint', $relationship['items'] ) );

		if ( $relationship['reciprocal'] ) {
			$fields             = 'mbr.from AS mbr_from, mbr.to AS mbr_to';
			$clauses['fields'] .= empty( $clauses['fields'] ) ? $fields : " , $fields";
			if ( empty( $clauses['groupby'] ) ) {
				$clauses['groupby'] = 'mbr_from, mbr_to';
			}

			return sprintf(
				" (mbr.type = '%s' AND ((mbr.from = $id_column AND mbr.to IN (%s)) OR (mbr.to = $id_column AND mbr.from IN (%s)))) ",
				$relationship['id'],
				$items,
				$items
			);
		}

		if ( ! $pass_thru_order ) {
			$orderby            = "mbr.order_$source";
			$clauses['orderby'] = 't.term_id' === $id_column ? "ORDER BY $orderby" : $orderby;
		}

		$fields             = "mbr.$source AS mbr_origin";
		$clauses['fields'] .= empty( $clauses['fields'] ) ? $fields : " , $fields";
		if ( empty( $clauses['groupby'] ) ) {
			$clauses['groupby'] = 'mbr_origin';
		}

		return sprintf(
			" (mbr.$target = $id_column AND mbr.type = '%s' AND mbr.$source IN (%s)) ",
			$relationship['id'],
			$items
		);
	}

	/**
	 * Modify query to get sibling items. Do not support querying by multiple relationships.
	 *
	 * @param array  $clauses   Query clauses.
	 * @param string $id_column Database column for object ID.
	 */
	public function handle_query_sibling( &$clauses, $id_column ) {
		global $wpdb;

		$source = $this->args['direction'];
		$target = 'from' === $source ? 'to' : 'from';
		$items  = array_map( 'absint', $this->args['items'] );
		$ids    = implode( ',', $items );
		$items  = "(
			SELECT DISTINCT `$target`
			FROM $wpdb->mb_relationships
			WHERE `type` = '{$this->args['id']}'
			AND `$source` IN ($ids)
		)";
		$tmp    = $source;
		$source = $target;
		$target = $tmp;

		$clauses['join'] = " INNER JOIN $wpdb->mb_relationships AS mbr ON mbr.$target = $id_column";

		$where  = sprintf(
			"mbr.type = '%s' AND mbr.$source IN (%s)",
			$this->args['id'],
			$items
		);
		$where .= " AND mbr.$target NOT IN ($ids)";

		$clauses['where'] .= empty( $clauses['where'] ) ? $where : " AND $where";
	}
}

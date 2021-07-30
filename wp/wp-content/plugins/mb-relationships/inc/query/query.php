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
		$clauses['groupby'] = empty( $clauses['groupby'] ) ? $id_column : "{$clauses['groupby']}, $id_column";

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
		$source = $relationship['direction'];
		$target = 'from' === $source ? 'to' : 'from';
		$items  = implode( ',', array_map( 'absint', $relationship['items'] ) );

		if ( $relationship['reciprocal'] ) {
			$fields             = "mbr.from AS mbr_from, mbr.to AS mbr_to, mbr.ID AS mbr_id, CASE WHEN mbr.to = $id_column THEN mbr.order_from WHEN mbr.from = $id_column THEN mbr.order_to END AS `mbr_order`";
			$clauses['fields'] .= empty( $clauses['fields'] ) ? $fields : " , $fields";
			if ( ! $pass_thru_order ) {
				$clauses['orderby'] = '`mbr_order` ASC, mbr_id DESC';
			}
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

		$alias              = "mbr_{$relationship['id']}_{$source}";
		$fields             = "mbr.$source AS `$alias`";
		$clauses['fields'] .= empty( $clauses['fields'] ) ? $fields : " , $fields";
		if ( empty( $clauses['groupby'] ) ) {
			$clauses['groupby'] = "`$alias`";
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

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
		// Single relationship.
		if ( empty( $this->args['relation'] ) ) {
			if ( empty( $this->args['sibling'] ) ) {
				$this->handle_single_relationship_join( $clauses, $id_column, $pass_thru_order );
			} else {
				$this->handle_single_relationship_sibling( $clauses, $id_column );
			}
		}
		// Multiple relationships.
		else {
			$this->handle_multiple_relationships( $clauses, $id_column );
		}

		$clauses['groupby'] = empty( $clauses['groupby'] ) ? $id_column : "{$clauses['groupby']}, $id_column";

		return $clauses;
	}

	/**
	 * Modify query JOIN statement. Do not support querying by multiple relationships.
	 *
	 * @param array  $clauses         Query clauses.
	 * @param string $id_column       Database column for object ID.
	 * @param bool   $pass_thru_order If TRUE use the WP_Query orderby clause.
	 */
	public function handle_single_relationship_join( &$clauses, $id_column, $pass_thru_order ) {
		global $wpdb;

		$join             = $this->build_single_relationship_join( $this->args, $clauses, $id_column, $pass_thru_order );
		$clauses['join'] .= " INNER JOIN $wpdb->mb_relationships AS mbr ON $join";
	}

	private function build_single_relationship_join( $relationship, &$clauses, $id_column, $pass_thru_order ) {
		$source = $relationship['direction'];
		$target = 'from' === $source ? 'to' : 'from';
		$items  = implode( ',', array_map( 'absint', $relationship['items'] ) );

		if ( $relationship['reciprocal'] ) {
			$fields             = "mbr.from AS mbr_from, mbr.to AS mbr_to, mbr.ID AS mbr_id, CASE WHEN mbr.to = $id_column THEN mbr.order_from WHEN mbr.from = $id_column THEN mbr.order_to END AS `mbr_order`";
			$clauses['fields'] .= empty( $clauses['fields'] ) ? $fields : " , $fields";

			if ( ! $pass_thru_order ) {

				if ( 't.term_id' === $id_column ) {
					$clauses['orderby'] = 'ORDER BY `mbr_order` ASC, mbr_id';
					$clauses['order']   = 'DESC';
				} else {
					$clauses['orderby'] = '`mbr_order` ASC, mbr_id DESC';
				}
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
	public function handle_single_relationship_sibling( &$clauses, $id_column ) {
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

	/**
	 * Modify query join & where statement for multi-relationship.
	 *
	 * @param string $clauses   Query clauses.
	 * @param array  $args   $WP_query args object.
	 */
	public function handle_multiple_relationships( &$clauses, $id_column ) {
		global $wpdb;
		$relation = $this->args['relation'];
		unset( $this->args['relation'] );
		$relationships = $this->args;
		$objects       = array();

		foreach ( $relationships as $relationship ) {
			$type          = $relationship['id'];
			$source        = $relationship['direction'];
			$items         = implode( ',', $relationship['items'] );
			$items         = empty( $relationship['reciprocal'] ) ? "`$source` IN ($items)" : "(`from` IN ($items) OR `to` IN ($items))";
			$query_results = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT `from`,`to` FROM $wpdb->mb_relationships
					WHERE `type`=%s AND $items",
					$type
				)
			);
			$object_ids    = array();
			foreach ( $query_results as $result ) {
				if ( empty( $relationship['reciprocal'] ) ) {
					$object_ids[] = 'from' === $source ? $result->to : $result->from;
				}
				if ( ! empty( $relationship['reciprocal'] ) && 'from' === $source && in_array( $result->from, $relationship['items'] ) ) {
					$object_ids[] = $result->to;
				}
				if ( ! empty( $relationship['reciprocal'] ) && 'to' === $source && in_array( $result->to, $relationship['items'] ) ) {
					$object_ids[] = $result->from;
				}
			}
			$objects[] = $object_ids;

		}
		if ( empty( $objects ) ) {
			$clauses['where'] .= ( empty( $clauses['where'] ) ? '' : ' AND' ) . " {$id_column} IN(-1)";
			return ;
		}
		$merge_object_ids = $objects[0];
		foreach ( $objects as $object ) {
			$merge_object_ids = 'OR' === $relation
				? array_merge( $merge_object_ids, $object )
				: array_intersect( $merge_object_ids, $object );
		}
		if ( empty( $merge_object_ids ) ) {
			$clauses['where'] .= ( empty( $clauses['where'] ) ? '' : ' AND' ) . " {$id_column} IN(-1)";
			return ;
		}
		$merge_object_ids = array_unique( $merge_object_ids );
		$merge_object_ids = implode( ',', $merge_object_ids );

		$clauses['where'] .= ( empty( $clauses['where'] ) ? '' : ' AND' ) . " {$id_column} IN($merge_object_ids)";
	}
}

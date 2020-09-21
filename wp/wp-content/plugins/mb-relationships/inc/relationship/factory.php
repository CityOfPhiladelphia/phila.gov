<?php
/**
 * The simple relationship factory.
 *
 * @package    Meta Box
 * @subpackage MB Relationships
 */

/**
 * Relationship factory class.
 */
class MBR_Relationship_Factory {
	private $object_factory;
	private $relationships = [];
	private $relationships_settings = [];
	private $filter_type;

	/**
	 * Constructor.
	 *
	 * @param MBR_Object_Factory $object_factory Reference to object factory.
	 */
	public function __construct( MBR_Object_Factory $object_factory ) {
		$this->object_factory = $object_factory;
	}

	/**
	 * Build a new relationship.
	 *
	 * @param array $settings Relationship settings.
	 *
	 * @return MBR_Relationship
	 */
	public function build( $settings ) {
		$settings = $this->normalize( $settings );

		$relationship  = new MBR_Relationship( $settings, $this->object_factory );
		$admin_columns = new MBR_Admin_Columns( $settings, $this->object_factory );
		$admin_columns->init();
		$meta_boxes = new MBR_Meta_Boxes( $settings );
		$meta_boxes->init();

		$this->relationships[ $settings['id'] ]          = $relationship;
		$this->relationships_settings[ $settings['id'] ] = $settings;

		// hook into post-registration action
		do_action( 'mb_relationships_registered', $settings );

		return $this->relationships[ $settings['id'] ];
	}

	public function get( $id ) {
		return isset( $this->relationships[ $id ] ) ? $this->relationships[ $id ] : null;
	}

	public function get_settings( $id ) {
		return isset( $this->relationships_settings[ $id ] ) ? $this->relationships_settings[ $id ] : null;
	}

	public function all() {
		return $this->relationships;
	}

	public function all_settings() {
		return $this->relationships_settings;
	}

	/**
	 * Filter relationships by object type.
	 *
	 * @param string $type Object type.
	 *
	 * @return array
	 */
	public function filter_by( $type ) {
		$this->filter_type = $type;
		return array_filter( $this->relationships, array( $this, 'is_filtered' ) );
	}

	/**
	 * Check if relationship has an object type on either side.
	 *
	 * @param MBR_Relationship $relationship Relationship object.
	 *
	 * @return bool
	 */
	protected function is_filtered( MBR_Relationship $relationship ) {
		return $relationship->has_object_type( $this->filter_type );
	}

	/**
	 * Normalize relationship settings.
	 *
	 * @param array $settings Relationship settings.
	 *
	 * @return array
	 */
	protected function normalize( $settings ) {
		$settings         = wp_parse_args( $settings, [
			'id'         => '',
			'from'       => '',
			'to'         => '',
			'label_from' => __( 'Connects To', 'mb-relationships' ),
			'label_to'   => __( 'Connected From', 'mb-relationships' ),
			'reciprocal' => false,
		] );
		$settings['from'] = $this->normalize_side( $settings['from'], $settings['label_from'] );
		$settings['to']   = $this->normalize_side( $settings['to'], $settings['label_to'] );

		$settings = apply_filters( 'mb_relationships_settings', $settings );

		return $settings;
	}

	/**
	 * Normalize settings for a "from" or "to" side.
	 *
	 * @param array|string $settings  Array of settings or post type (string) for short.
	 *
	 * @return array
	 */
	protected function normalize_side( $settings, $label ) {
		$default = array(
			'object_type'   => 'post',
			'empty_message' => __( 'No connections', 'mb-relationships' ),
			'meta_box'      => array(
				'title'    => $label,
				'hidden'   => false,
				'context'  => 'side',
				'priority' => 'low',
			),
			'field'         => array(
				'type'      => 'post',
				'post_type' => 'post',
			),
		);

		if ( is_string( $settings ) ) {
			$settings = array(
				'field' => array(
					'post_type' => $settings,
				),
			);
		}
		$settings             = array_merge( $default, $settings );
		$settings['meta_box'] = array_merge( $default['meta_box'], $settings['meta_box'] );
		$settings['field']    = array_merge( $default['field'], $settings['field'] );

		$this->migrate_syntax( $settings );

		// Fixed settings.
		$settings['field']['clone']        = true;
		$settings['field']['sort_clone']   = true;
		$settings['field']['relationship'] = true;

		$settings['meta_box']['storage_type'] = 'relationships_table';

		return $settings;
	}

	/**
	 * Migrate from old/simple syntax to the formal one.
	 *
	 * @param  array $settings Relationship settings for a side.
	 * @return array
	 */
	private function migrate_syntax( &$settings ) {
		$meta_box = &$settings['meta_box'];
		$field    = &$settings['field'];

		// General settings.
		if ( ! empty( $meta_box['empty_message'] ) ) {
			$settings['empty_message'] = $meta_box['empty_message'];
			unset( $meta_box['empty_message'] );
		}

		// Field genral settings.
		if ( ! empty( $meta_box['field_title'] ) ) {
			$field['name'] = $meta_box['field_title'];
			unset( $meta_box['field_title'] );
		}
		if ( ! empty( $settings['query_args'] ) ) {
			$field['query_args'] = $settings['query_args'];
			unset( $settings['query_args'] );
		}

		// Post.
		if ( ! empty( $settings['post_type'] ) ) {
			$field['post_type'] = $settings['post_type'];
			unset( $settings['post_type'] );
		}
		if ( 'post' === $settings['object_type'] ) {
			$field['type']          = 'post';
			$meta_box['post_types'] = array( $field['post_type'] );
		}

		// Term.
		if ( ! empty( $settings['taxonomy'] ) ) {
			$field['taxonomy'] = $settings['taxonomy'];
			unset( $settings['taxonomy'] );
		}
		if ( 'term' === $settings['object_type'] ) {
			$field['type']          = 'taxonomy_advanced';
			$meta_box['taxonomies'] = array( $field['taxonomy'] );
			unset( $field['post_type'] );
		}

		// User.
		if ( 'user' === $settings['object_type'] ) {
			$field['type']    = 'user';
			$meta_box['type'] = 'user';
			unset( $field['post_type'] );
		}
	}
}

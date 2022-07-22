<?php
/**
 * Create plugin shortcodes.
 *
 * @package    Meta Box
 * @subpackage MB Relationships
 */

/**
 * Shortcodes class.
 */
class MBR_Shortcodes {
	/**
	 * The relationship factory object.
	 *
	 * @var MBR_Relationship_Factory
	 */
	protected $rel_factory;

	/**
	 * The object factory.
	 *
	 * @var MBR_Object_Factory
	 */
	protected $obj_factory;

	/**
	 * MBR_Shortcodes constructor.
	 *
	 * @param MBR_Relationship_Factory $rel_factory The relationship factory object.
	 * @param MBR_Object_Factory       $obj_factory The post object.
	 */
	public function __construct( MBR_Relationship_Factory $rel_factory, MBR_Object_Factory $obj_factory ) {
		$this->rel_factory = $rel_factory;
		$this->obj_factory = $obj_factory;
	}

	/**
	 * Initialization.
	 */
	public function init() {
		add_shortcode( 'mb_relationships', array( $this, 'render' ) );
	}

	/**
	 * Render the shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @return string
	 */
	public function render( $atts ) {
		if ( empty( $atts['id'] ) ) {
			return '';
		}
		$relationship = $this->rel_factory->get( $atts['id'] );
		if ( ! isset( $relationship ) ) {
			return '';
		}
		$connected   = isset( $atts['from'] ) ? 'to' : 'from';
		$object_type = $relationship->get_object_type( $connected );
		$object      = $this->obj_factory->build( $object_type );

		$atts = shortcode_atts(
			array(
				'id'        => '',
				'items'     => $object->get_current_id(),
				'direction' => 'from',
				'mode'      => 'ul',
				'separator' => '',
				'link'      => 'true',
			),
			$atts
		);

		$atts[ $atts['direction'] ] = $atts['items'];

		$items = MB_Relationships_API::get_connected( $atts );
		if ( empty( $items ) ) {
			return '';
		}
		$items = array_map(
			function( $item ) use ( $object, $atts ) {
				return $object->render( $item, $atts );
			},
			$items
		);

		switch ( $atts['mode'] ) {
			case 'ul':
				return '<ul><li>' . implode( '</li><li>', $items ) . '</li></ul>';
			case 'ol':
				return '<ol><li>' . implode( '</li><li>', $items ) . '</li></ol>';
			case 'inline':
				return implode( ', ', $items );
			case 'custom':
				return implode( $atts['separator'], $items );
		}
		return '';
	}
}

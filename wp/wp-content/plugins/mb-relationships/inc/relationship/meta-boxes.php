<?php
/**
 * The meta boxes class.
 * Registers meta boxes for relationships objects.
 *
 * @package    Meta Box
 * @subpackage MB Relationships
 */

/**
 * The meta boxes class.
 *
 * @property array  $from From side settings.
 * @property array  $to   To side settings.
 * @property string $id   Relationship ID.
 */
class MBR_Meta_Boxes {
	/**
	 * The relationship settings.
	 *
	 * @var array
	 */
	private $settings;

	/**
	 * Constructor.
	 *
	 * @param array $settings Relationship settings.
	 */
	public function __construct( $settings ) {
		$this->settings = $settings;
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
	 * Setup hooks to create meta boxes for relationships, using Meta Box API.
	 */
	public function init() {
		add_filter( 'rwmb_meta_boxes', array( $this, 'register_meta_boxes' ) );
	}

	/**
	 * Register 2 meta boxes for "from" and "to" sides.
	 *
	 * @param array $meta_boxes Meta boxes array.
	 *
	 * @return array
	 */
	public function register_meta_boxes( $meta_boxes ) {
		// Reciprocal relationships: only one meta box.
		if ( $this->reciprocal ) {
			$meta_boxes[] = $this->parse_meta_box( 'from' );
			return $meta_boxes;
		}

		if ( ! $this->from['meta_box']['hidden'] ) {
			$meta_boxes[] = $this->parse_meta_box( 'from' );
		}
		if ( ! $this->to['meta_box']['hidden'] ) {
			$meta_boxes[] = $this->parse_meta_box( 'to' );
		}

		return $meta_boxes;
	}

	/**
	 * Parse meta box settings.
	 *
	 * @param  string $source "from" or "to".
	 * @return array
	 */
	private function parse_meta_box( $source ) {
		$target = 'from' === $source ? 'to' : 'from';

		$field       = $this->{$target}['field'];
		$field['id'] = "{$this->id}_{$target}";

		$meta_box           = $this->{$source}['meta_box'];
		$meta_box['id']     = "{$this->id}_relationships_{$target}";
		$meta_box['fields'] = array( $field );

		return $meta_box;
	}
}

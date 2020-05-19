<?php
/**
 * The simple object factory.
 *
 * @package    Meta Box
 * @subpackage MB Relationships
 */

/**
 * Object factory class.
 */
class MBR_Object_Factory {
	/**
	 * For storing instances.
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 * Get object based on type.
	 *
	 * @param string $type Object type.
	 *
	 * @return MBR_Object_Interface
	 */
	public function build( $type ) {
		if ( isset( $this->data[ $type ] ) ) {
			return $this->data[ $type ];
		}

		$class               = 'MBR_' . ucfirst( $type );
		$this->data[ $type ] = new $class();

		return $this->data[ $type ];
	}
}

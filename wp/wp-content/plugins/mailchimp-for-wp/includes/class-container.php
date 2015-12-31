<?php

/**
 * Class MC4WP_Service_Container
 *
 * @access private
 * @ignore
 */
class MC4WP_Container implements ArrayAccess {

	/**
	 * @var array
	 */
	protected $services = array();

	/**
	 * @var array
	 */
	protected $resolved_services = array();

	/**
	 * @param $name
	 * @return boolean
	 */
	public function has( $name ) {
		return isset( $this->services[ $name ] );
	}

	/**
	 * @param $name
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function get( $name ) {

		if( ! $this->has( $name ) ) {
			throw new Exception( sprintf( 'No service named %s was registered.', $name ) );
		}

		$service = $this->services[ $name ];

		// is this a resolvable service?
		if( is_callable( $service ) ) {

			// resolve service if it's not resolved yet
			if( ! isset( $this->resolved_services[ $name ] ) ) {
				$this->resolved_services[ $name ] = call_user_func( $service );
			}

			return $this->resolved_services[ $name ];
		}

		return $this->services[ $name ];
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Whether a offset exists
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 *
	 * @param mixed $offset <p>
	 *                      An offset to check for.
	 *                      </p>
	 *
	 * @return boolean true on success or false on failure.
	 * </p>
	 * <p>
	 * The return value will be casted to boolean if non-boolean was returned.
	 */
	public function offsetExists( $offset ) {
		return $this->has( $offset );
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to retrieve
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 *
	 * @param mixed $offset <p>
	 *                      The offset to retrieve.
	 *                      </p>
	 *
	 * @return mixed Can return all value types.
	 */
	public function offsetGet( $offset ) {
		return $this->get( $offset );
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to set
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 *
	 * @param mixed $offset <p>
	 *                      The offset to assign the value to.
	 *                      </p>
	 * @param mixed $value  <p>
	 *                      The value to set.
	 *                      </p>
	 *
	 * @return void
	 */
	public function offsetSet( $offset, $value ) {
		$this->services[ $offset ] = $value;
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to unset
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 *
	 * @param mixed $offset <p>
	 *                      The offset to unset.
	 *                      </p>
	 *
	 * @return void
	 */
	public function offsetUnset( $offset ) {
		unset( $this->services[ $offset ] );
}}
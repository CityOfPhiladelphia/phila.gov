<?php

/**
 * Class MC4WP_Debug_Log
 *
 * Simple logging class which writes to a file, loosely based on PSR-3.
 */
class MC4WP_Debug_Log{

	/**
	 * Detailed debug information
	 */
	const DEBUG = 100;

	/**
	 * Interesting events
	 *
	 * Examples: Visitor subscribed
	 */
	const INFO = 200;

	/**
	 * Exceptional occurrences that are not errors
	 *
	 * Examples: User already subscribed
	 */
	const WARNING = 300;

	/**
	 * Runtime errors
	 */
	const ERROR = 400;

	/**
	 * Logging levels from syslog protocol defined in RFC 5424
	 *
	 * @var array $levels Logging levels
	 */
	protected static $levels = array(
		self::DEBUG     => 'DEBUG',
		self::INFO      => 'INFO',
		self::WARNING   => 'WARNING',
		self::ERROR     => 'ERROR',
	);

	/**
	 * @var string The file to which messages should be written.
	 */
	public $file;

	/**
	 * @var int Only write messages with this level or higher
	 */
	public $level;

	/**
	 * @var resource
	 */
	protected $stream;

	/**
	 * MC4WP_Debug_Log constructor.
	 *
	 * @param string $file
	 * @param mixed $level;
	 */
	public function __construct( $file, $level = self::DEBUG ) {
		$this->file = $file;
		$this->level = self::to_level( $level );
	}

	/**
	 * @param mixed $level
	 * @param string $message
	 * @return boolean
	 */
	public function log( $level, $message ) {

		$level = self::to_level( $level );

		// only log if message level is higher than log level
		if( $level < $this->level ) {
			return false;
		}

		// generate line
		$level_name = self::get_level_name( $level );
		$message = (string) $message;
		$datetime = date( 'Y-m-d H:i:s', ( time() - date('Z') ) + ( get_option( 'gmt_offset', 0 ) * 3600 ) );
		$message = sprintf( '[%s] %s: %s', $datetime, $level_name, $message ) . PHP_EOL;

		// open file stream (write only)
		if( is_null( $this->stream ) ) {
			$this->stream = fopen( $this->file, 'a' );
		}

		// lock file while we write, ignore errors (not much we can do)
		flock( $this->stream, LOCK_EX );

		// write the message to the file
		fwrite( $this->stream, $message );

		// unlock file again, but don't close it for remainder of this request
		flock( $this->stream, LOCK_UN );

		return true;
	}

	/**
	 * @param string $message
	 * @return boolean
	 */
	public function warning( $message ) {
		return $this->log( self::WARNING, $message );
	}

	/**
	 * @param string $message
	 * @return boolean
	 */
	public function info( $message ) {
		return $this->log( self::INFO, $message );
	}

	/**
	 * @param string $message
	 * @return boolean
	 */
	public function error( $message ) {
		return $this->log( self::ERROR, $message );
	}

	/**
	 * @param string $message
	 * @return boolean
	 */
	public function debug( $message ) {
		return $this->log( self::DEBUG, $message );
	}

	/**
	 * Converts PSR-3 levels to local ones if necessary
	 *
	 * @param string|int Level number or name (PSR-3)
	 * @return int
	 */
	public static function to_level( $level ) {

		if ( is_string( $level ) ) {

			$level = strtoupper( $level );
			if( defined( __CLASS__ . '::' . $level ) ) {
				return constant( __CLASS__ . '::'  . $level );
			}

			throw new InvalidArgumentException( 'Level "' . $level . '" is not defined, use one of: ' . implode( ', ', array_keys( self::$levels ) ) );
		}

		return $level;
	}

	/**
	 * Gets the name of the logging level.
	 *
	 * @param  int    $level
	 * @return string
	 */
	public static function get_level_name( $level ) {

		if ( ! isset( self::$levels[ $level ] ) ) {
			throw new InvalidArgumentException( 'Level "' . $level . '" is not defined, use one of: ' . implode( ', ', array_keys( self::$levels ) ) );
		}

		return self::$levels[ $level ];
	}

}


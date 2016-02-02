<?php
/**
 * Image processing support for mla_viewer and thumbnail generation
 *
 * @package Media Library Assistant
 * @since 2.13
 */

/**
 * Class MLA (Media Library Assistant) Image Processor provides PDF thumbnails
 * for the [mla_gallery] mla_viewer
 * and Media/Assistant thumbnail generator.
 *
 * @package Media Library Assistant
 * @since 2.10
 */
class MLAImageProcessor {
	/**
	 * Log debug information if true
	 *
	 * @since 2.12
	 *
	 * @var boolean
	 */
	public static $mla_debug = false;

	/**
	 * Generate a unique, writable file in the temporary directory
	 *
	 * @since 2.10
	 *
	 * @param	string	$extension File extension for the temporary file
	 *
	 * @return	string	Writable path and file name.
	 */
	private static function _get_temp_file( $extension = '.tmp' ) {
		static $temp = NULL;

		/*
		 * Find a temp directory
		 */
		if ( NULL == $temp ) {
			if ( function_exists('sys_get_temp_dir') ) {
				$temp = sys_get_temp_dir();
				if ( @is_dir( $temp ) ) {
					$temp = rtrim( $temp, '/\\' ) . '/';
				}
			} else {
				$temp = ini_get('upload_tmp_dir');
				if ( @is_dir( $temp ) ) {
					$temp = rtrim( $temp, '/\\' ) . '/';
				} else {
					$temp = '/tmp/';
					if ( false == @is_dir( $temp ) ) {
						self::_mla_debug_add( 'MLAImageProcessor::_get_temp_file Temp directory failure' );
						return false;
					}
				}
			}
		}

		/*
		 * Create a unique file
		 */
		$path = $temp . uniqid( mt_rand() ) . $extension;
		$f = @fopen( $path, 'a' );
		if ( $f === false ) {
			self::_mla_debug_add( 'MLAImageProcessor::_get_temp_file Temp file failure' );
			return false;
		}

		fclose( $f );
		return $path;
	}

	/**
	 * Imagick object for the image to be streamed
	 *
	 * @since 2.10
	 *
	 * @var Imagick
	 */
	protected static $image;

	/**
	 * Direct Ghostscript file conversion
	 *
	 * @since 2.10
	 * @uses self::$image loads the converted file to this Imagick object
	 *
	 * @param	string	$file Input file, e.g., a PDF document
	 * @param	string	$frame Page/frame within the file, zero-based
	 * @param	string	$resolution Output file DPI. Default 72.
	 * @param	string	$output_type Output MIME type; 'image/jpeg' or 'image/png'.
	 * @param	string	$explicit_path Optional. Non-standard location to override default search, e.g., 'C:\Program Files (x86)\gs\gs9.15\bin\gswin32c.exe'
	 *
	 * @return	boolean	true if conversion succeeds else false
	 */
	private static function _ghostscript_convert( $file, $frame, $resolution, $output_type, $explicit_path = '' ) {
		/*
		 * Look for exec() - from http://stackoverflow.com/a/12980534/866618
		 */
		if ( ini_get('safe_mode') ) {
			self::_mla_debug_add( 'MLAImageProcessor::_ghostscript_convert safe_mode failure' );
			return false;
		}

		$blacklist = preg_split( '/,\s*/', ini_get('disable_functions') . ',' . ini_get('suhosin.executor.func.blacklist') );
		if ( in_array('exec', $blacklist) ) {
			self::_mla_debug_add( 'MLAImageProcessor::_ghostscript_convert blacklist failure' );
			return false;
		}

		/*
		 * Look for the Ghostscript executable
		 */
		$ghostscript_path = NULL;
		do {

			if ( 'WIN' === strtoupper( substr( PHP_OS, 0, 3) ) ) {
				if ( ! empty( $explicit_path ) ) {
					$ghostscript_path = exec( 'dir /o:n/s/b "' . $explicit_path . '"' );
					if ( ! empty( $ghostscript_path ) ) {
						break;
					} else {
						$ghostscript_path = NULL;
						break;
					}
				}

				if ( $ghostscript_path = getenv('GSC') ) {
					break;
				}

				$ghostscript_path = exec('where gswin*c.exe');
				if ( ! empty( $ghostscript_path ) ) {
					break;
				}

				$ghostscript_path = exec('dir /o:n/s/b "C:\Program Files\gs\*gswin*c.exe"');
				if ( ! empty( $ghostscript_path ) ) {
					break;
				}

				$ghostscript_path = exec('dir /o:n/s/b "C:\Program Files (x86)\gs\*gswin32c.exe"');
				if ( ! empty( $ghostscript_path ) ) {
					break;
				}

				$ghostscript_path = NULL;
				break;
			} // Windows platform

			if ( ! empty( $explicit_path ) ) {
				exec( 'test -e ' . $explicit_path, $dummy, $ghostscript_path );
				if ( $explicit_path !== $ghostscript_path ) {
					$ghostscript_path = NULL;
				}

				break;
			}

			$ghostscript_path = exec('which gs');
			if ( ! empty( $ghostscript_path ) ) {
				break;
			}

			$test_path = '/usr/bin/gs';
			exec('test -e ' . $test_path, $dummy, $ghostscript_path);

			if ( $test_path !== $ghostscript_path ) {
				$ghostscript_path = NULL;
			}
		} while ( false );
		self::_mla_debug_add( 'MLAImageProcessor::_ghostscript_convert ghostscript_path = ' . var_export( $ghostscript_path, true ) );
		
		if ( isset( $ghostscript_path ) ) {
			if ( 'image/jpeg' == $output_type ) {
				$device = 'jpeg';
				$extension = '.jpg';
			} else {
				$device = 'png16m';
				$extension = '.png';
			}

			/*
			 * Generate a unique temporary file
			 */
			$output_file = self::_get_temp_file( $extension );

			$cmd = escapeshellarg( $ghostscript_path ) . ' -sDEVICE=%1$s -r%2$dx%2$d -dFirstPage=%3$d -dLastPage=%3$d -dFitPage -o %4$s %5$s 2>&1';
			$cmd = sprintf( $cmd, $device, $resolution, ( $frame + 1 ), escapeshellarg( $output_file ), escapeshellarg( $file ) );
			exec( $cmd, $stdout, $return );
			if ( 0 != $return ) {
				self::_mla_debug_add( "ERROR: _ghostscript_convert exec returned '{$return}, cmd = " . var_export( $cmd, true ) );
				self::_mla_debug_add( "ERROR: _ghostscript_convert exec returned '{$return}, details = " . var_export( $stdout, true ) );
				return false;
			}

			try {
				self::$image->readImage( $output_file );
			}
			catch ( Exception $e ) {
				self::_mla_debug_add( "ERROR: _ghostscript_convert readImage Exception = " . var_export( $e->getMessage(), true ) );
				return false;
			}

			@unlink( $output_file );
			return true;
		} // found Ghostscript

		self::_mla_debug_add( 'MLAImageProcessor::_ghostscript_convert Ghostscript detection failure' );
		return false;
	} // _ghostscript_convert

	/**
	 * Prepare the image for output, scaling and flattening as required
	 *
	 * @since 2.10
	 * @uses self::$image updates the image in this Imagick object
	 *
	 * @param	integer	zero or new width
	 * @param	integer	zero or new height
	 * @param	boolean	proportional fit (true) or exact fit (false)
	 * @param	string	output MIME type
	 * @param	integer	compression quality; 1 - 100
	 *
	 * @return void
	 */
	private static function _prepare_image( $width, $height, $best_fit, $type, $quality ) {
		if ( is_callable( array( self::$image, 'scaleImage' ) ) ) {
			if ( 0 < $width && 0 < $height ) {
				// Both are set; use them as-is
				self::$image->scaleImage( $width, $height, $best_fit );
			} elseif ( 0 < $width || 0 < $height ) {
				// One is set; scale the other one proportionally if reducing
				$image_size = self::$image->getImageGeometry();

				if ( $width && isset( $image_size['width'] ) && $width < $image_size['width'] ) {
					self::$image->scaleImage( $width, 0 );
				} elseif ( $height && isset( $image_size['height'] ) && $height < $image_size['height'] ) {
					self::$image->scaleImage( 0, $height );
				}
			} else {
				// Neither is specified, apply defaults
				self::$image->scaleImage( 150, 0 );
			}
		}

		if ( 0 < $quality && 101 > $quality ) {
			if ( 'image/jpeg' == $type ) {
				self::$image->setImageCompressionQuality( $quality );
				self::$image->setImageCompression( imagick::COMPRESSION_JPEG );
			}
			else {
				self::$image->setImageCompressionQuality( $quality );
			}
		}

		if ( 'image/jpeg' == $type ) {				
			if ( is_callable( array( self::$image, 'setImageBackgroundColor' ) ) ) {				
				self::$image->setImageBackgroundColor('white');
			}

			if ( is_callable( array( self::$image, 'mergeImageLayers' ) ) ) {
				self::$image = self::$image->mergeImageLayers( imagick::LAYERMETHOD_FLATTEN );
			} elseif ( is_callable( array( self::$image, 'flattenImages' ) ) ) {				
				self::$image = self::$image->flattenImages();
			}
		}
	} // _prepare_image

	/**
	 * Log debug information
	 *
	 * @since 2.12
	 *
	 * @param	string	$message Error message.
	 */
	private static function _mla_debug_add( $message ) {
		if ( self::$mla_debug ) {
			if ( class_exists( 'MLACore' ) ) {
				MLACore::mla_debug_add( $message );
			} else {
				error_log( $message, 0);
			}
		}
	}

	/**
	 * Abort the operation and exit
	 *
	 * @since 2.10
	 *
	 * @param	string	$message Error message.
	 * @param	string	$title Optional. Error title. Default empty.
	 * @param	integer	$response Optional. HTML response code. Default 500.

	 * @return	void	echos page content and calls exit();
	 */
	private static function _mla_die( $message, $title = '', $response = 500 ) {
		self::_mla_debug_add( __LINE__ . " _mla_die( '{$message}', '{$title}', '{$response}' )" );
		exit();
	}

	/**
	 * Log the message and return error message array
	 *
	 * @since 2.10
	 *
	 * @param	string	$message Error message.
	 * @param	string	$line Optional. Line number in the caller.
	 *
	 * @return	 array( 'error' => message )
	 */
	private static function _mla_error_return( $message, $line = '' ) {
		self::_mla_debug_add( $line . " MLAImageProcessor::_mla_error_return '{$message}'" );
		return array( 'error' => $message );
	}

	/**
	 * Process Imagick thumbnail conversion request, e.g., for a PDF thumbnail
	 *
	 * Replaces download_url() in the Codex "Function Reference/wp handle sideload" example.
	 *
	 * @since 2.13
	 *
	 * @param	string	$input_file Path and name of the source file relative to upload directory
	 * @param	array	$args Generation parameters
	 *
	 * @return	array	file attributes ( 'file', 'url', 'type' ) on success, ( 'error' ) on failure
	 */
	public static function mla_handle_thumbnail_sideload( $input_file, $args ) {
		if ( ! class_exists( 'Imagick' ) ) {
			return self::_mla_error_return( 'Imagick not installed', __LINE__ );
		}

		if( ini_get( 'zlib.output_compression' ) ) { 
			ini_set( 'zlib.output_compression', 'Off' );
		}

		if ( ! is_file( $input_file ) ) {
			return self::_mla_error_return( 'File not found: ' . $input_file, __LINE__ );
		}

		/*
		 * Process generation parameters and supply defaults
		 */
		$width = isset( $args['width'] ) ? abs( intval( $args['width'] ) ) : 0;
		$height = isset( $args['height'] ) ? abs( intval( $args['height'] ) ) : 0;
		$type = isset( $args['type'] ) ? $args['type'] : 'image/jpeg';
		$quality = isset( $args['quality'] ) ? abs( intval( $args['quality'] ) ) : 0;
		$frame = isset( $args['frame'] ) ? abs( intval( $args['frame'] ) ) : 0;
		$resolution = isset( $args['resolution'] ) ? abs( intval( $args['resolution'] ) ) : 72;
		$best_fit = isset( $args['best_fit'] ) ? (boolean) $args['best_fit'] : false;
		$ghostscript_path = isset( $args['ghostscript_path'] ) ? $args['ghostscript_path'] : '';

		/*
		 * Convert the file to an image format and load it
		 */
		try {
			self::$image = new Imagick();

			/*
			 * this must be called before reading the image, otherwise has no effect - 
			 * "-density {$x_resolution}x{$y_resolution}"
			 * this is important to give good quality output, otherwise text might be unclear
			 * default resolution is 72,72
			 */
			self::$image->setResolution( $resolution, $resolution );

			$result = self::_ghostscript_convert( $input_file, $frame, $resolution, $type, $ghostscript_path );

			if ( false === $result ) {
				try {
					self::$image->readImage( $input_file . '[' . $frame . ']' );
				}
				catch ( Exception $e ) {
					self::$image->readImage( $input_file . '[0]' );
				}

				if ( 'image/jpeg' == $type ) {
					$extension = 'JPG';
				} else {
					$extension = 'PNG';
				}

				self::$image->setImageFormat( $extension );
			}

			if ( ! self::$image->valid() ) {
				self::_mla_die( 'File not loaded', __LINE__, 404 );
			}
		}
		catch ( Exception $e ) {
			return self::_mla_error_return( 'Image load exception: ' . $e->getMessage(), __LINE__ );
		}

		/*
		 * Prepare the output image; resize and flatten, if necessary
		 */
		try {
			self::_prepare_image( $width, $height, $best_fit, $type, $quality );
			}
		catch ( Exception $e ) {
			return self::_mla_error_return( '_prepare_image exception: ' . $e->getMessage(), __LINE__ );
		}

		/*
		 * Write the image to an appropriately-named file
		 */
		try {
			$output_file = wp_tempnam( $input_file );
			self::$image->writeImage( $output_file );
		}
		catch ( Exception $e ) {
			@unlink( $output_file );
			return self::_mla_error_return( 'Image write exception: ' . $e->getMessage(), __LINE__ );
		}

		// array based on $_FILE as seen in PHP file uploads
		$results = array(
			'name' => basename( $input_file ),
			'type' => $type,
			'tmp_name' => $output_file,
			'error' => 0,
			'size' => filesize( $output_file ),
		);		

		return	$results;
	}

	/**
	 * Process Imagick image stream request, e.g., for a PDF thumbnail
	 *
	 * Requires mla_stream_file (relative to wp_upload_dir ) in $_REQUEST;
	 * optional $_REQUEST parameters are:
	 * 		mla_stream_width, mla_stream_height, mla_stream_frame, mla_stream_resolution,
	 *		mla_stream_quality, mla_stream_type, mla_stream_fit, mla_ghostscript_path
	 *
	 * @since 2.10
	 *
	 * @return	void	echos image content and calls exit();
	 */
	public static function mla_process_stream_image() {
		self::_mla_debug_add( 'MLAImageProcessor::mla_process_stream_image REQUEST = ' . var_export( $_REQUEST, true ) );
		if ( ! class_exists( 'Imagick' ) ) {
			self::_mla_die( 'Imagick not installed', __LINE__, 500 );
		}

		if( ini_get( 'zlib.output_compression' ) ) { 
			ini_set( 'zlib.output_compression', 'Off' );
		}

		$file = $_REQUEST['mla_stream_file'];
		if ( ! is_file( $file ) ) {
			self::_mla_die( 'File not found', __LINE__, 404 );
		}

		$use_mutex = isset( $_REQUEST['mla_single_thread'] );
		$width = isset( $_REQUEST['mla_stream_width'] ) ? abs( intval( $_REQUEST['mla_stream_width'] ) ) : 0;
		$height = isset( $_REQUEST['mla_stream_height'] ) ? abs( intval( $_REQUEST['mla_stream_height'] ) ) : 0;
		$type = isset( $_REQUEST['mla_stream_type'] ) ? $_REQUEST['mla_stream_type'] : 'image/jpeg';
		$quality = isset( $_REQUEST['mla_stream_quality'] ) ? abs( intval( $_REQUEST['mla_stream_quality'] ) ) : 0;
		$frame = isset( $_REQUEST['mla_stream_frame'] ) ? abs( intval( $_REQUEST['mla_stream_frame'] ) ) : 0;
		$resolution = isset( $_REQUEST['mla_stream_resolution'] ) ? abs( intval( $_REQUEST['mla_stream_resolution'] ) ) : 72;
		/*
		 * If mla_ghostscript_path is present, a non-standard GS location can be found in a file written by
		 * the [mla_gallery] shortcode processor.
		 */
		$ghostscript_path = isset( $_REQUEST['mla_ghostscript_path'] ) ? $_REQUEST['mla_ghostscript_path'] : '';
		if ( ! empty( $ghostscript_path ) ) {
			$ghostscript_path = @file_get_contents( dirname( __FILE__ ) . '/' . 'mla-ghostscript-path.txt' );
		}

		if ( $use_mutex ) {
			$temp_file = self::_get_temp_file();
			@unlink( $temp_file );
			$temp_file = pathinfo( $temp_file, PATHINFO_DIRNAME ) . '/mla-mutex.txt';

			$mutex = new MLAMutex();
			$mutex->init( 1, $temp_file );
			$mutex->acquire();
			self::_mla_debug_add( 'MLAImageProcessor::mla_process_stream_image begin file = ' . var_export( $file, true ) );
		}

		/*
		 * Convert the file to an image format and load it
		 */
		try {
			self::$image = new Imagick();

			/*
			 * this must be called before reading the image, otherwise has no effect - 
			 * "-density {$x_resolution}x{$y_resolution}"
			 * this is important to give good quality output, otherwise text might be unclear
			 * default resolution is 72,72
			 */
			self::$image->setResolution( $resolution, $resolution );

			//$result = false;
			$result = self::_ghostscript_convert( $file, $frame, $resolution, $type, $ghostscript_path );

			if ( false === $result ) {
				try {
					self::$image->readImage( $file . '[' . $frame . ']' );
				}
				catch ( Exception $e ) {
					self::$image->readImage( $file . '[0]' );
				}

				if ( 'image/jpeg' == $type ) {
					$extension = 'JPG';
				} else {
					$extension = 'PNG';
				}

				self::$image->setImageFormat( $extension );
			}

			if ( ! self::$image->valid() ) {
				self::_mla_die( 'File not loaded', __LINE__, 404 );
			}
		}
		catch ( Exception $e ) {
			self::_mla_die( 'Image load exception: ' . $e->getMessage(), __LINE__, 404 );
		}

		/*
		 * Prepare the output image; resize and flatten, if necessary
		 */
		try {
			if ( isset( $_REQUEST['mla_stream_fit'] ) ) {
				$best_fit = ( '1' == $_REQUEST['mla_stream_fit'] );
			} else {
				$best_fit = false;
			}

			self::_prepare_image( $width, $height, $best_fit, $type, $quality );
			}
		catch ( Exception $e ) {
			self::_mla_die( '_prepare_image exception: ' . $e->getMessage(), __LINE__, 500 );
		}

		/*
		 * Stream the image back to the requestor
		 */
		try {
			header( "Content-Type: $type" );
			echo self::$image->getImageBlob();
		}
		catch ( Exception $e ) {
			self::_mla_die( 'Image stream exception: ' . $e->getMessage(), __LINE__, 500 );
		}

		if ( $use_mutex ) {
			$mutex->release();
		}

		exit();
	} // mla_process_stream_image
} // Class MLAImageProcessor

/**
 * Class MLA (Media Library Assistant) Mutex provides a simple "mutual exclusion" semaphore
 * for the [mla_gallery] mla_viewer=single option
 *
 * Adapted from the example by mr.smaon@gmail.com in the PHP Manual "Semaphore Functions" page. 
 *
 * @package Media Library Assistant
 * @since 2.10
 */
class MLAMutex {
	/**
	 * Semaphore identifier returned by sem_get()
	 *
	 * @since 2.10
	 *
	 * @var resource
	 */
	private $sem_id;

	/**
	 * True if the semaphore has been acquired
	 *
	 * @since 2.10
	 *
	 * @var boolean
	 */
	private $is_acquired = false;

	/**
	 * True if using a file lock instead of a semaphore
	 *
	 * @since 2.10
	 *
	 * @var boolean
	 */
	private $use_file_lock = false;

	/**
	 * Name of the (locked) file used as a semaphore 
	 *
	 * @since 2.10
	 *
	 * @var string
	 */
	private $filename = '';

	/**
	 * File system pointer resource of the (locked) file used as a semaphore 
	 *
	 * @since 2.10
	 *
	 * @var resource
	 */
	private $filepointer;

	/**
	 * Initializes the choice of semaphore Vs file lock
	 *
	 * @since 2.10
	 *
	 * @param	boolean	$use_lock True to force use of file locking
	 *
	 * @return	void
	 */
	function __construct( $use_lock = false ) 	{
		/*
		 * If sem_ functions are not available require file locking
		 */
		if ( ! is_callable( 'sem_get' ) ) {
			$use_lock = true;
		}

		if ( $use_lock || 'WIN' == substr( PHP_OS, 0, 3 ) ) {
			$this->use_file_lock = true;
		}
	}

	/**
	 * Creates the semaphore or sets the (lock) file name
	 *
	 * @since 2.10
	 *
	 * @param	integer	$id Key to identify the semaphore
	 * @param	string	$filename Absolute path and name of the file for locking
	 *
	 * @return	boolean	True if the initialization succeeded
	 */
	public function init( $id, $filename = '' ) {

		if( $this->use_file_lock ) {
			if( empty( $filename ) ) {
				return false;
			} else {
				$this->filename = $filename;
			}
		} else {
			if( ! ( $this->sem_id = sem_get( $id, 1) ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Acquires the semaphore or opens and locks the file
	 *
	 * @since 2.10
	 *
	 * @return	boolean	True if the acquisition succeeded
	 */
	public function acquire() {
		if( $this->use_file_lock ) {
			if ( empty( $this->filename ) ) {
				return true;
			}

			if( false == ( $this->filepointer = @fopen( $this->filename, "w+" ) ) ) {
				return false;
			}

			if( false == flock( $this->filepointer, LOCK_EX ) ) {
				return false;
			}
		} else {
			if ( ! sem_acquire( $this->sem_id ) ) {
				return false;
			}
		}

		$this->is_acquired = true;
		return true;
	}

	/**
	 * Releases the semaphore or unlocks and closes (but does not unlink) the file
	 *
	 * @since 2.10
	 *
	 * @return	boolean	True if the release succeeded
	 */
	public function release() {
		if( ! $this->is_acquired ) {
			return true;
		}

		if( $this->use_file_lock ) {
			if( false == flock( $this->filepointer, LOCK_UN ) ) {
				return false;
			}

			fclose( $this->filepointer );
		} else {
			if ( ! sem_release( $this->sem_id ) ) {
				return false;
			}
		}

		$this->is_acquired = false;
		return true;
	}

	/**
	 * Returns the semaphore identifier, if it exists, else NULL
	 *
	 * @since 2.10
	 *
	 * @return	resource	Semaphore identifier or NULL
	 */
	public function getId() {
		return $this->sem_id;
	}
} // MLAMutex
?>
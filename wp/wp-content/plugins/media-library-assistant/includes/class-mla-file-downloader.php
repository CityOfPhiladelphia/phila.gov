<?php
/**
 * File download support for [mla_gallery]
 *
 * @package Media Library Assistant
 * @since 2.32
 */

/**
 * Class MLA (Media Library Assistant) File Downloader provides file streaming to client.
 *
 * @package Media Library Assistant
 * @since 2.32
 */
class MLAFileDownloader {
	/**
	 * Log debug information if true
	 *
	 * @since 2.32
	 *
	 * @var boolean
	 */
	public static $mla_debug = false;

	/**
	 * Process secure file download
	 *
	 * Requires mla_download_file and mla_download_type in $_REQUEST.
	 *
	 * @since 2.32
	 *
	 * @param	array	$args ( mla_download_file, mla_download_type, optional content_disposition )
	 * @return	void	echos file contents and calls exit();
	 */
	public static function mla_process_download_file( $args = NULL ) {
		if ( empty( $args ) ) {
			$args = $_REQUEST;
		}

		self::_mla_debug_add( 'MLAFileDownloader::mla_process_download_file, args = ' . var_export( $args, true ) );

		if ( !empty( $args['error'] ) ) {
			$message = $args['error'];
		} else {
			$message = '';

			if ( isset( $args['mla_download_file'] ) && isset( $args['mla_download_type'] ) ) {
				if( ini_get( 'zlib.output_compression' ) ) { 
					ini_set( 'zlib.output_compression', 'Off' );
				}

				$disposition = 'attachment';
				if ( !empty( $args['mla_disposition'] ) ) {
					$disposition = trim( strtolower( $args['mla_disposition'] ) );
					switch ( $disposition ) {
						case 'attachment':
						case 'download':
							$disposition = 'attachment';
							break;
						case 'inline':
						case 'view':
						case 'file':
							$disposition = 'inline';
							break;
						default:
							$message = 'ERROR: content disposition invalid.';
					}
				}

				$file_name = $args['mla_download_file'];
				$match_name = str_replace( '\\', '/', $file_name );
				$base_dir = pathinfo( __FILE__, PATHINFO_DIRNAME );
				$match_dir = str_replace( '\\', '/', $base_dir );
				$allowed_path = substr( $match_dir, 0, strpos( $match_dir, 'plugins' ) );

				if ( 0 !== strpos( $match_name, $allowed_path ) ) {
					$message = 'ERROR: download path out of bounds.';
				} elseif ( false !== strpos( $match_name, '..' ) ) {
					$message = 'ERROR: download path invalid.';
				}
			} else {
				$message = 'ERROR: download argument(s) not set.';
			}
		}

		if ( empty( $message ) ) {
			header('Pragma: public'); 	// required
			header('Expires: 0');		// no cache
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Last-Modified: '.gmdate ( 'D, d M Y H:i:s', filemtime ( $file_name ) ).' GMT');
			header('Cache-Control: private',false);
			header('Content-Type: '.$args['mla_download_type']);

			if ( 'attachment' === $disposition ) {
				header('Content-Disposition: attachment; filename="'.basename( $file_name ).'"');
				header('Content-Transfer-Encoding: binary');
			}

			header('Content-Length: '.filesize( $file_name ));	// provide file size
			header('Connection: close');

			readfile( $file_name );
		} else {
			self::_mla_debug_add( $message );

			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
			echo '<html xmlns="http://www.w3.org/1999/xhtml">';
			echo '<head>';
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
			echo '<title>Download Error</title>';
			echo '</head>';
			echo '';
			echo '<body>';
			echo $message;
			echo '</body>';
			echo '</html> ';
		}

		exit();
	}

	/**
	 * Log debug information
	 *
	 * @since 2.32
	 *
	 * @param	string	$message Error message.
	 */
	private static function _mla_debug_add( $message ) {
		if ( self::$mla_debug ) {
			if ( class_exists( 'MLACore' ) ) {
				MLACore::mla_debug_mode( 'log' );
				MLACore::mla_debug_add( $message );
			} else {
				error_log( $message, 0);
			}
		}
	}

	/**
	 * Abort the operation and exit
	 *
	 * @since 2.32
	 *
	 * @param	string	$message Error message.
	 * @param	string	$title Optional. Error title. Default empty.
	 * @param	integer	$response Optional. HTML response code. Default 500.

	 * @return	void	echos page content and calls exit();
	 */
	public static function mla_die( $message, $title = '', $response = 500 ) {
		self::_mla_debug_add( __LINE__ . " mla_die( '{$message}', '{$title}', '{$response}' )" );
		exit();
	}

	/**
	 * Log the message and return error message array
	 *
	 * @since 2.32
	 *
	 * @param	string	$message Error message.
	 * @param	string	$line Optional. Line number in the caller.
	 *
	 * @return	 array( 'error' => message )
	 */
	private static function _mla_error_return( $message, $line = '' ) {
		self::_mla_debug_add( $line . " MLAFileDownloader::_mla_error_return '{$message}'" );
		return array( 'error' => $message );
	}
} // Class MLAFileDownloader
?>
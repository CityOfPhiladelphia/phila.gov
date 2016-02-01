<?php
/**
 * Stand-alone stream image handler for the mla_viewer
 *
 * @package Media Library Assistant
 * @since 2.10
 */

/*
 * Process mla_viewer image stream requests
 */
//@ini_set('error_log','C:\Program Files (x86)\Apache Software Foundation\Apache24\logs\php-errors.log');

require_once( pathinfo( __FILE__, PATHINFO_DIRNAME ) . '/class-mla-image-processor.php' );

if ( isset( $_REQUEST['mla_stream_file'] ) ) {
	MLAImageProcessor::$mla_debug = isset( $_REQUEST['mla_debug'] ) && 'log' == $_REQUEST['mla_debug'];
	MLAImageProcessor::mla_process_stream_image();
}

MLAImageProcessor::_mla_die( 'mla_stream_file not set', __LINE__, 500 );
?>
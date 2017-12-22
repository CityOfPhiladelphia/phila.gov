<?php
/**
 * Stand-alone file download handler for the [mla_gallery]
 *
 * @package Media Library Assistant
 * @since 2.32
 */

/*
 * Process [mla_gallery link=download] requests
 */
//@ini_set('error_log','C:\Program Files (x86)\Apache Software Foundation\Apache24\logs\php-errors.log');

require_once( pathinfo( __FILE__, PATHINFO_DIRNAME ) . '/class-mla-file-downloader.php' );

if ( isset( $_REQUEST['mla_download_file'] ) && isset( $_REQUEST['mla_download_type'] ) ) {
	MLAFileDownloader::$mla_debug = isset( $_REQUEST['mla_debug'] ) && 'log' == $_REQUEST['mla_debug'];
	MLAFileDownloader::mla_process_download_file();
}

MLAFileDownloader::mla_die( 'MLA File Download parameters not set', __LINE__, 500 );
?>
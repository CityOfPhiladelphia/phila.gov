<?php
/*
Plugin Name: WP Offload S3 Lite
Plugin URI: http://wordpress.org/extend/plugins/amazon-s3-and-cloudfront/
Description: Automatically copies media uploads to Amazon S3 for storage and delivery. Optionally configure Amazon CloudFront for even faster delivery.
Author: Delicious Brains
Version: 1.2.1
Author URI: https://deliciousbrains.com/
Network: True
Text Domain: amazon-s3-and-cloudfront
Domain Path: /languages/

// Copyright (c) 2013 Delicious Brains. All rights reserved.
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// **********************************************************************
//
// Forked Amazon S3 for WordPress with CloudFront (http://wordpress.org/extend/plugins/tantan-s3-cloudfront/)
// which is a fork of Amazon S3 for WordPress (http://wordpress.org/extend/plugins/tantan-s3/).
// Then completely rewritten.
*/

$GLOBALS['aws_meta']['amazon-s3-and-cloudfront']['version'] = '1.2.1';

$aws_plugin_version_required = '1.0.4';

require_once dirname( __FILE__ ) . '/classes/wp-aws-compatibility-check.php';
require_once dirname( __FILE__ ) . '/classes/as3cf-utils.php';

add_action( 'activated_plugin', array( 'AS3CF_Utils', 'deactivate_other_instances' ) );

global $as3cf_compat_check;
$as3cf_compat_check = new WP_AWS_Compatibility_Check(
	'WP Offload S3 Lite',
	'amazon-s3-and-cloudfront',
	__FILE__,
	'Amazon Web Services',
	'amazon-web-services',
	$aws_plugin_version_required
);

function as3cf_init( $aws ) {
	global $as3cf_compat_check;

	if ( method_exists( 'WP_AWS_Compatibility_Check', 'is_plugin_active' ) && $as3cf_compat_check->is_plugin_active( 'amazon-s3-and-cloudfront-pro/amazon-s3-and-cloudfront-pro.php' ) ) {
		// Don't load if pro plugin installed
		return;
	}

	if ( ! $as3cf_compat_check->is_compatible() ) {
		return;
	}

	global $as3cf;
	$abspath = dirname( __FILE__ );
	require_once $abspath . '/include/functions.php';
	require_once $abspath . '/classes/as3cf-error.php';
	require_once $abspath . '/classes/as3cf-filter.php';
	require_once $abspath . '/classes/filters/as3cf-local-to-s3.php';
	require_once $abspath . '/classes/filters/as3cf-s3-to-local.php';
	require_once $abspath . '/classes/as3cf-notices.php';
	require_once $abspath . '/classes/as3cf-stream-wrapper.php';
	require_once $abspath . '/classes/as3cf-plugin-compatibility.php';
	require_once $abspath . '/classes/amazon-s3-and-cloudfront.php';

	// Autoloader
	require_once $abspath . '/wp-offload-s3-autoloader.php';
	new WP_Offload_S3_Autoloader( 'WP_Offload_S3', $abspath );

	$as3cf = new Amazon_S3_And_CloudFront( __FILE__, $aws );
}

add_action( 'aws_init', 'as3cf_init' );

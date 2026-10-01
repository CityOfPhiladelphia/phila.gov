<?php
/**
 * Core Rollback
 *
 * @author  Andy Fragen
 * @license MIT
 * @link    https://github.com/afragen/core-rollback
 * @package core-rollback
 */

/**
 * Plugin Name:       Core Rollback
 * Plugin URI:        https://github.com/afragen/core-rollback
 * Description:       Seamless rollback of WordPress Core to latest release or any outdated, secure release using the Core Update API and core update methods.
 * Version:           1.4.3
 * Author:            Andy Fragen
 * License:           MIT
 * Domain Path:       /languages
 * Text Domain:       core-rollback
 * Network:           true
 * GitHub Plugin URI: https://github.com/afragen/core-rollback
 * Primary Branch:    main
 * Requires PHP:      5.6
 * Requires at least: 4.1
 */

namespace Fragen\Rollback;

/*
 * Exit if called directly.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once __DIR__ . '/vendor/autoload.php';
add_action(
	'init',
	function () {
		( new Bootstrap() )->run();
	}
);

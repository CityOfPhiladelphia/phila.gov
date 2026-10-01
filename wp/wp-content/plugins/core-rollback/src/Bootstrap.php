<?php
/**
 * Core Rollback
 *
 * @author  Andy Fragen
 * @license MIT
 * @link    https://github.com/afragen/core-rollback
 * @package core-rollback
 */

namespace Fragen\Rollback;

/*
 * Exit if called directly.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Bootstrap
 */
class Bootstrap {
	/**
	 * Let's get started.
	 *
	 * @return void
	 */
	public function run() {
		( new Core() )->load_hooks();
		( new Settings() )->load_hooks();
	}
}

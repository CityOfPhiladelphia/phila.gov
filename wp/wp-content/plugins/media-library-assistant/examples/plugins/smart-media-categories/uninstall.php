<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Smart_Media_Categories
 * @author    David Lingren <dlingren@comcast.net>
 * @license   GPL-2.0+
 * @link      @TODO http://example.com
 * @copyright 2014 David Lingren
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// @TODO: Define uninstall functionality here
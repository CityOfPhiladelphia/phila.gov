<?php
/**
 * Create tables for the plugin.
 *
 * @package    Meta Box
 * @subpackage MB Relationships
 */

/**
 * The tables class
 */
class MBR_Table {
	/**
	 * Constructor.
	 */
	public function __construct() {
		global $wpdb;

		// Register new table.
		$wpdb->tables[]         = 'mb_relationships';
		$wpdb->mb_relationships = $wpdb->prefix . 'mb_relationships';
	}

	/**
	 * Create shared table for all relationships.
	 */
	public function create() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// Create new table.
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "
			CREATE TABLE {$wpdb->mb_relationships} (
				`ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`from` bigint(20) unsigned NOT NULL,
				`to` bigint(20) unsigned NOT NULL,
				`type` varchar(44) NOT NULL default '',
				`order_from` bigint(20) unsigned NOT NULL,
				`order_to` bigint(20) unsigned NOT NULL,
				PRIMARY KEY  (`ID`),
				KEY `from` (`from`),
				KEY `to` (`to`),
				KEY `type` (`type`)
			) $charset_collate;
		";
		dbDelta( $sql );
	}
}

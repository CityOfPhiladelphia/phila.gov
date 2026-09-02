<?php
namespace FileBird;

defined( 'ABSPATH' ) || exit;

class Install {
	public static function create_tables() {
		global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();

		$table_fbv = $wpdb->prefix . 'fbv';
		//type == 0: folder
		//type == 1: collection
		if ( $wpdb->get_var( "show tables like '{$wpdb->prefix}fbv'" ) != $table_fbv ) {
			$sql = 'CREATE TABLE ' . $table_fbv . ' (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(250) NOT NULL,
            `parent` int(11) NOT NULL DEFAULT 0,
            `type` int(2) NOT NULL DEFAULT 0,
            `ord` int(11) NULL DEFAULT 0,
            `created_by` int(11) NULL DEFAULT 0,
            PRIMARY KEY (id),
            UNIQUE KEY `id` (id)) ' . $charset_collate . ';';
			dbDelta( $sql );
		}

		$table = $wpdb->prefix . 'fbv_attachment_folder';
		//type == 0: folder
		//type == 1: collection
		if ( $wpdb->get_var( "show tables like '{$wpdb->prefix}fbv_attachment_folder'" ) != $table ) {
			$sql = 'CREATE TABLE ' . $table . ' (
            `folder_id` int(11) unsigned NOT NULL,
            `attachment_id` bigint(20) unsigned NOT NULL,
            PRIMARY KEY( `folder_id`, `attachment_id`)
            )' . $charset_collate . ';';
			dbDelta( $sql );
		}
	}
}
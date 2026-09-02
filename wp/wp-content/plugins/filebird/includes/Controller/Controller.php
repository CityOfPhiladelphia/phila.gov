<?php
namespace FileBird\Controller;

defined( 'ABSPATH' ) || exit;

class Controller {

	protected $folder_table   = 'fbv';
	protected $relation_table = 'fbv_attachment_folder';

	public function __construct() {

	}

	protected function loadView( $view, $data = array(), $return_html = false ) {
		$viewPath = NJFB_PLUGIN_PATH . 'views/' . $view . '.php';
		if ( ! file_exists( $viewPath ) ) {
			die( 'View <strong>' . esc_html( $viewPath ) . '</strong> not found!' );
		}
		extract( $data );
		if ( $return_html === true ) {
			ob_start();
			include_once $viewPath;
			return ob_get_clean();
		}
		include_once $viewPath;
	}
	protected function getTable( $table ) {
		global $wpdb;
		return $wpdb->prefix . $table;
	}
	protected function getNodeClass( $id, $count = 0 ) {
		return "fbv_$id fbvc_$count";
	}
}

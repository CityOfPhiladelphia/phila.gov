<?php
namespace FileBird\Controller;

defined( 'ABSPATH' ) || exit;

use FileBird\Model\Folder as FolderModel;

class FolderUser extends Controller {
	protected static $instance = null;

	private $is_enabled;
	private $current_user_id;

	public function __construct() {
		$this->is_enabled      = $this->isEnabled();
		$this->current_user_id = get_current_user_id();

		if ( $this->is_enabled ) {
			add_filter( 'fbv_data_before_inserting_folder', array( $this, 'filterDataBeforeInsertingFolder' ) );
			add_filter( 'fbv_in_not_in_uncategorized_where', array( $this, 'filterUncategorizedWhere' ), 10, 2 );
			add_filter( 'fbv_in_not_in_created_by', array( $this, 'fbv_in_not_in_created_by' ) );
		}
	}

	public function fbv_in_not_in_created_by() {
		return $this->current_user_id;
	}
	public function filterDataBeforeInsertingFolder( $data ) {
		$data['created_by'] = $this->current_user_id;
		return $data;
	}

	public function filterUncategorizedWhere( $where, $folder_table ) {
		return sprintf( '`folder_id` IN (SELECT `id` FROM %1$s WHERE `created_by` = %2$d)', $folder_table, $this->current_user_id );
	}

	private function isEnabled() {
		return get_option( 'njt_fbv_folder_per_user', '0' ) === '1';
	}

}

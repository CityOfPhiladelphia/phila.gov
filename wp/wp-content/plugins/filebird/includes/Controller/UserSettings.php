<?php

namespace FileBird\Controller;

defined( 'ABSPATH' ) || exit;

use FileBird\Model\Folder as FolderModel;

class UserSettings {
	protected static $instance = null;

	private $userId  = '';
	public $settings = array();

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
			self::$instance->doHooks();
		}
		return self::$instance;
	}

	public function __construct() {
		$this->userId   = get_current_user_id();
		$this->settings = $this->getAllSettings();
	}

	public function doHooks() {
		add_filter( 'fbv_data', array( $this, 'addUserSettingsData' ), 10, 1 );
	}

	public function getAllSettings() {
		return array(
			'default_folder'     => $this->getDefaultSelectedFolder(),
			'default_sort_files' => $this->getDefaultSortFiles(),
		);
	}

	public function addUserSettingsData( $data ) {
		$data['user_settings'] = $this->settings;
		return $data;
	}

	public function getDefaultSortFiles() {
		return get_user_meta( $this->userId, '_njt_fbv_default_sort_files', true );
	}

	public function getDefaultSelectedFolder() {
		$folder_id = get_user_meta( $this->userId, '_njt_fbv_default_folder', true );
		$folder_id = intval( $folder_id );

		if ( $folder_id > 0 ) {
			if ( is_null( FolderModel::findById( $folder_id ) ) ) {
				$folder_id = -1;
			}
		}
		return $folder_id;
	}

	public function setDefaultSelectedFolder( $value ) {
		$value = (int) $value;
		update_user_meta( $this->userId, '_njt_fbv_default_folder', $value );
	}

	public function setDefaultSortFiles( $value ) {
		update_user_meta( $this->userId, '_njt_fbv_default_sort_files', $value );
	}
}

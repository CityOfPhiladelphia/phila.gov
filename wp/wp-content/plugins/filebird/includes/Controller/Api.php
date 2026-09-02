<?php
namespace FileBird\Controller;

use FileBird\Model\Folder as FolderModel;
use FileBird\Classes\Helpers as Helpers;
use FileBird\Classes\Tree;

defined( 'ABSPATH' ) || exit;


class Api {
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'registerRestFields' ) );
	}


	public function registerRestFields() {
		register_rest_route(
			NJFB_REST_URL,
			'fbv-api',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'restApi' ),
				'permission_callback' => array( $this, 'resAdminPermissionsCheck' ),
			)
		);

		//GET http://yoursite/wp-json/filebird/public/v1/folders
		register_rest_route(
			NJFB_REST_PUBLIC_URL,
			'folders',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'publicRestApiGetFolders' ),
				'permission_callback' => array( $this, 'resPublicPermissionsCheck' ),
			)
		);

		//GET http://yoursite/wp-json/filebird/public/v1/folder/?folder_id=
		register_rest_route(
			NJFB_REST_PUBLIC_URL,
			'folder',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'publicRestApiGetFolderDetail' ),
				'permission_callback' => array( $this, 'resPublicPermissionsCheck' ),
			)
		);

		//POST http://yoursite/wp-json/filebird/public/v1/folder/set-attachment
		//ids=&folder=
		register_rest_route(
			NJFB_REST_PUBLIC_URL,
			'folder/set-attachment',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'publicRestApiSetAttachment' ),
				'permission_callback' => array( $this, 'resPublicPermissionsCheck' ),
			)
		);

		//GET http://yoursite/wp-json/filebird/public/v1/attachment-id/?folder_id=
		register_rest_route(
			NJFB_REST_PUBLIC_URL,
			'attachment-id',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'publicRestApiGetAttachmentIds' ),
				'permission_callback' => array( $this, 'resPublicPermissionsCheck' ),
			)
		);

		//GET http://yoursite/wp-json/filebird/public/v1/attachment-count/?folder_id=
		register_rest_route(
			NJFB_REST_PUBLIC_URL,
			'attachment-count',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'publicRestApiGetAttachmentCount' ),
				'permission_callback' => array( $this, 'resPublicPermissionsCheck' ),
			)
		);

		//POST http://yoursite/wp-json/filebird/public/v1/folders
		//parent_id=&name=
		register_rest_route(
			NJFB_REST_PUBLIC_URL,
			'folders',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'publicRestApiNewFolder' ),
				'permission_callback' => array( $this, 'resPublicPermissionsCheck' ),
			)
		);

	}
	public function restApi( $request ) {
		$act = $request->get_param( 'act' );
		$act = isset( $act ) ? sanitize_text_field( $act ) : '';
		if ( $act == 'generate-key' ) {
			$key = $this->generateRandomString( 40 );
			update_option( 'fbv_rest_api_key', $key );
			wp_send_json_success( array( 'key' => $key ) );
		}
		wp_send_json_error(
			array(
				'mess' => __( 'Invalid action', 'filebird' ),
			)
		);
	}

	private function addFolderCounter( $folders, $counter ) {
		if ( is_array( $folders ) ) {
			foreach ( $folders as $k => $v ) {
				$folders[ $k ]['li_attr']['data-count'] = isset( $counter[ $v['id'] ] ) ? $counter[ $v['id'] ] : 0;
				if ( isset( $v['children'] ) ) {
					$folders[ $k ]['children'] = $this->addFolderCounter( $v['children'], $counter );
				}
			}
		}
		return $folders;
	}

	public function publicRestApiGetFolders() {
		$data = array();
		$data['folders'] = Tree::getFolders( null );
		$counter         = Tree::getAllFoldersAndCount();

		$results['folders'] = $this->addFolderCounter( $data['folders'], $counter );

		wp_send_json_success( $results );
	}
	public function publicRestApiGetFolderDetail( $request ) {
		$folder_id = $request->get_param( 'folder_id' );
		wp_send_json_success(
			array(
				'folder' => FolderModel::findById( $folder_id, 'id, name, parent' ),
			)
		);
	}
	public function publicRestApiGetAttachmentIds( $request ) {
		$folder_id = $request->get_param( 'folder_id' );

		if ( $folder_id != '' ) {
			wp_send_json_success(
				array(
					'attachment_ids' => Helpers::getAttachmentIdsByFolderId( $folder_id ),
				)
			);
		}
		wp_send_json_error(
			array(
				'mess' => __( 'folder_id is missing.', 'filebird' ),
			)
		);
	}
	public function publicRestApiGetAttachmentCount( $request ) {
		$folder_id = $request->get_param( 'folder_id' );
		$icl_lang  = $request->get_param( 'icl_lang' );
		if ( $folder_id !== '' ) {
			$count = 0;
			if ( $folder_id > 0 ) {
				$count = Helpers::getAttachmentCountByFolderId( $folder_id );
			} elseif ( $folder_id == -1 ) {
				$count = is_null( $icl_lang ) ? Tree::getCount( -1 ) : Tree::getCount( -1, $icl_lang );
			} else {
				//Uncategorized
				$total            = is_null( $icl_lang ) ? Tree::getCount( -1 ) : Tree::getCount( -1, $icl_lang );
				$folders          = is_null( $icl_lang ) ? Tree::getAllFoldersAndCount() : Tree::getAllFoldersAndCount( $icl_lang );
				$count_of_folders = 0;
				foreach ( $folders as $k => $v ) {
					$count_of_folders += $v;
				}
				$count = $total - $count_of_folders;
			}
			wp_send_json_success(
				array(
					'count' => $count,
				)
			);
		}
		wp_send_json_error(
			array(
				'mess' => __( 'folder_id is missing.', 'filebird' ),
			)
		);
	}
	public function publicRestApiNewFolder( $request ) {
		$parent_id = (int) ( $request->get_param( 'parent_id' ) );
		$name      = sanitize_text_field( $request->get_param( 'name' ) );

		if ( $name != '' ) {
			$id = FolderModel::newOrGet( $name, $parent_id );
			wp_send_json_success( array( 'id' => $id ) );
		}
		wp_send_json_error(
			array(
				'mess' => __( 'Required fields are missing.', 'filebird' ),
			)
		);
	}
	public function publicRestApiSetAttachment( $request ) {
		$ids    = $request->get_param( 'ids' );
		$folder = $request->get_param( 'folder' );

		$ids    = ! empty( $ids ) ? Helpers::sanitize_array( $ids ) : '';
		$folder = sanitize_text_field( $folder );

		if ( \is_numeric( $ids ) ) {
			$ids = array( $ids );
		}

		if ( $ids != '' && is_numeric( $folder ) ) {
			FolderModel::setFoldersForPosts( $ids, $folder );
			wp_send_json_success();
		}
		wp_send_json_error(
			array(
				'mess' => __( 'Validation failed', 'filebird' ),
			)
		);
	}
	public function resAdminPermissionsCheck() {
		return current_user_can( 'upload_files' ) && current_user_can( 'manage_options' );
	}
	public function resPublicPermissionsCheck() {
		$key = get_option( 'fbv_rest_api_key', '' );
		if ( \strlen( $key ) == 40 ) {
			return $key === $this->getBearerToken();
		}
		return false;
	}

	private function generateRandomString( $length = 10 ) {
		$characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen( $characters );
		$randomString     = '';
		for ( $i = 0; $i < $length; $i++ ) {
			$randomString .= $characters[ wp_rand( 0, $charactersLength - 1 ) ];
		}
		return $randomString;
	}
	private function getAuthorizationHeader() {
		$headers = null;
		if ( isset( $_SERVER['Authorization'] ) ) {
			$headers = trim( $_SERVER['Authorization'] );
		} elseif ( isset( $_SERVER['HTTP_AUTHORIZATION'] ) ) { //Nginx or fast CGI
			$headers = trim( $_SERVER['HTTP_AUTHORIZATION'] );
		} elseif ( function_exists( 'apache_request_headers' ) ) {
			$requestHeaders = apache_request_headers();
			// Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
			$requestHeaders = array_combine( array_map( 'ucwords', array_keys( $requestHeaders ) ), array_values( $requestHeaders ) );
			//print_r($requestHeaders);
			if ( isset( $requestHeaders['Authorization'] ) ) {
				$headers = trim( $requestHeaders['Authorization'] );
			}
		}
		return $headers;
	}
	private function getBearerToken() {
		$token   = null;
		$headers = $this->getAuthorizationHeader();
		// HEADER: Get the access token from the header
		if ( ! empty( $headers ) ) {
			if ( preg_match( '/Bearer\s(\S+)/', $headers, $matches ) ) {
				$token = $matches[1];
			}
		}
		if ( is_null( $token ) && isset( $_REQUEST['token'] ) ) {
			$token = $_REQUEST['token'];
		}
		return $token;
	}
}

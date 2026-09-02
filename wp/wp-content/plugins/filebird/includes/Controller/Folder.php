<?php

namespace FileBird\Controller;

use FileBird\Controller\Convert as ConvertController;
use FileBird\Classes\Convert as ConvertModel;
use FileBird\Controller\Exclude;
use FileBird\Controller\UserSettings;
use FileBird\Controller\FolderUser;
use FileBird\Controller\Api;

use FileBird\Model\Folder as FolderModel;
use FileBird\Classes\Helpers as Helpers;
use FileBird\Classes\Tree;
use FileBird\Controller\Attachment\SizeMeta;

use FileBird\I18n as I18n;
use FileBird\Page\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Folder Controller
 */

class Folder extends Controller {
	protected static $instance = null;
	private $userSettings      = null;

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
			self::$instance->doHooks();
		}
		return self::$instance;
	}

	public function __construct() {     }

	private function doHooks() {
		add_filter( 'media_library_infinite_scrolling', '__return_true' );
		add_filter( 'ajax_query_attachments_args', array( $this, 'ajaxQueryAttachmentsArgs' ), 20 );
		add_filter( 'mla_media_modal_query_final_terms', array( $this, 'ajaxQueryAttachmentsArgs' ), 20 );
		add_filter( 'restrict_manage_posts', array( $this, 'restrictManagePosts' ) );
		add_filter( 'posts_clauses', array( $this, 'postsClauses' ), 10, 2 );
		add_filter( 'attachment_fields_to_save', array( $this, 'attachment_fields_to_save' ), 10, 2 );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueAdminScripts' ), PHP_INT_MAX );
		add_action( 'rest_api_init', array( $this, 'registerRestFields' ) );
		add_action( 'add_attachment', array( $this, 'addAttachment' ) );
		add_action( 'delete_attachment', array( $this, 'deleteAttachment' ) );
		add_action( 'pre-upload-ui', array( $this, 'actionPluploadUi' ) );
		add_action( 'wp_ajax_fbv_first_folder_notice', array( $this, 'ajax_first_folder_notice' ) );
		// add_action( 'wp_ajax_fbv_close_buy_pro_dialog', array($this, 'ajaxCloseBuyProDialog'));
		add_action( 'admin_notices', array( $this, 'adminNotices' ) );
		add_action( 'attachment_fields_to_edit', array( $this, 'attachment_fields_to_edit' ), 10, 2 );

		add_filter( 'wp_edited_image_metadata', array( $this, 'edited_image_metadata' ), 10, 3 );

		$this->userSettings = UserSettings::getInstance();

		// MailPoet plugin support
		SizeMeta::getInstance()->doHooks();
		new Exclude();
		new Api();
		new FolderUser();

		$this->check_update_database();

		add_filter( 'mailpoet_conflict_resolver_whitelist_script', array( $this, 'mailpoet_conflict_resolver_whitelist_script' ), 10, 1 );
		add_filter( 'mailpoet_conflict_resolver_whitelist_style', array( $this, 'mailpoet_conflict_resolver_whitelist_style' ), 10, 1 );

		add_filter( 'users_have_additional_content', array( $this, 'users_have_additional_content' ), 10, 2 );
		add_action( 'deleted_user', array( $this, 'deleted_user' ), 10, 3 );
	}

	public function mailpoet_conflict_resolver_whitelist_script( $scripts ) {
		$scripts[] = 'filebird';
		$scripts[] = 'filebird-pro';
		return $scripts;
	}

	public function mailpoet_conflict_resolver_whitelist_style( $styles ) {
		$styles[] = 'filebird';
		$styles[] = 'filebird-pro';
		return $styles;
	}

	public function adminNotices() {
		global $pagenow;

		$optionFirstFolder = get_option( 'fbv_first_folder_notice' );
		if ( FolderModel::countFolder() === 0 && $pagenow !== 'upload.php' &&
		( $optionFirstFolder === false || time() >= intval( $optionFirstFolder ) ) ) {
			include NJFB_PLUGIN_PATH . '/views/notices/html-notice-first-folder.php';
		}

		if ( $pagenow !== 'upload.php' && apply_filters( 'fbv_update_database_notice', false ) ) {
			include NJFB_PLUGIN_PATH . '/views/notices/html-notice-update-database.php';
		}
	}

	public function ajax_first_folder_notice() {
		check_ajax_referer( 'fbv_nonce', 'nonce', true );
		update_option( 'fbv_first_folder_notice', time() + 30 * 60 * 60 * 24 ); //After 3 months show
		wp_send_json_success();
	}

	public function check_update_database() {
		if( is_admin() ) {
			$is_converted = get_option( 'fbv_old_data_updated_to_v4', '0' );
			if ( $is_converted !== '1' ) {
				if ( ConvertController::countOldFolders() > 0 && ! isset( $_GET['autorun'] ) ) {
					add_filter( 'fbv_update_database_notice', '__return_true' );
				}
			}
		}
	}

	public function registerRestFields() {
		register_rest_route(
			NJFB_REST_URL,
			'get-folders',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'ajaxGetFolder' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
			)
		);
		register_rest_route(
			NJFB_REST_URL,
			'gutenberg-get-folders',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'ajaxGutenbergGetFolder' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
			)
		);

		register_rest_route(
			NJFB_REST_URL,
			'new-folder',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'ajaxNewFolder' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
			)
		);
		register_rest_route(
			NJFB_REST_URL,
			'update-folder',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'ajaxUpdateFolder' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
			)
		);
		register_rest_route(
			NJFB_REST_URL,
			'update-folder-ord',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'ajaxUpdateFolderOrd' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
			)
		);
		register_rest_route(
			NJFB_REST_URL,
			'delete-folder',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'ajaxDeleteFolder' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
			)
		);
		register_rest_route(
			NJFB_REST_URL,
			'set-folder-attachments',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'ajaxSetFolder' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
			)
		);
		register_rest_route(
			NJFB_REST_URL,
			'generate-attachment-size',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( SizeMeta::getInstance(), 'apiCallback' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
			)
		);
		register_rest_route(
			NJFB_REST_URL,
			'update-tree',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'ajaxUpdateTree' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
			)
		);
		register_rest_route(
			NJFB_REST_URL,
			'get-relations',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'ajaxGetRelations' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
			)
		);
		register_rest_route(
			NJFB_REST_URL,
			'set-settings',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'ajaxSetSettings' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
			)
		);

		register_rest_route(
			NJFB_REST_URL,
			'export-csv',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'exportCSV' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
			)
		);

		register_rest_route(
			NJFB_REST_URL,
			'import-csv',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'importCSV' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
			)
		);

		register_rest_route(
			NJFB_REST_URL,
			'import-csv-detail',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'getImportCSVDetail' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
			)
		);
	}
	public function resPermissionsCheck() {
		 return current_user_can( 'upload_files' );
	}

	public function enqueueAdminScripts( $screenId ) {
		if ( function_exists( 'get_current_screen' ) ) {
			if ( $screenId == 'upload.php' ) {
				wp_register_script( 'jquery-resizable', NJFB_PLUGIN_URL . 'assets/js/jquery-resizable.min.js' );
				wp_enqueue_script( 'jquery-resizable' );
			}
		}

		if ( $screenId !== 'pagebuilders' ) {
			wp_enqueue_script( 'fbv-setting', NJFB_PLUGIN_URL . 'assets/js/setting.js', array( 'jquery' ), NJFB_VERSION, false );
		}

		if ( $screenId === Settings::getInstance()->settingPageSuffix ) {
			wp_enqueue_style( 'fbv-pro-tippy', NJFB_PLUGIN_URL . 'assets/css/tippy.min.css', array(), NJFB_VERSION );
		}

		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-droppable' );

		if ( wp_is_mobile() ) {
			wp_enqueue_script( 'jquery-touch-punch-fixed', NJFB_PLUGIN_URL . 'assets/js/jquery.ui.touch-punch.js', array( 'jquery-ui-widget', 'jquery-ui-mouse' ), NJFB_VERSION, false );
		}

		wp_enqueue_script( 'fbv-folder', NJFB_PLUGIN_URL . 'assets/dist/js/app.js', array(), NJFB_VERSION, false );
		wp_enqueue_script( 'fbv-lib', NJFB_PLUGIN_URL . 'assets/js/jstree/jstree.min.js', array(), NJFB_VERSION, false );

		wp_enqueue_style( 'fbv-folder', NJFB_PLUGIN_URL . 'assets/dist/css/app.css', array(), NJFB_VERSION );
		wp_style_add_data( 'fbv-folder', 'rtl', 'replace' );

		wp_localize_script(
			'fbv-folder',
			'fbv_data',
			apply_filters(
				'fbv_data',
				array(
					'nonce'                    => wp_create_nonce( 'fbv_nonce' ),
					'rest_nonce'               => wp_create_nonce( 'wp_rest' ),
					'nonce_error'              => __( 'Your request can\'t be processed.', 'filebird' ),
					'current_folder'           => ( ( isset( $_GET['fbv'] ) ) ? (int) sanitize_text_field( $_GET['fbv'] ) : -1 ), //-1: all files. 0: uncategorized
					'folders'                  => FolderModel::allFolders( 'id as term_id, name as term_name', array( 'term_id', 'term_name' ) ),
					'relations'                => FolderModel::getRelations(),
					'is_upload_screen'         => 'upload.php' === $screenId ? '1' : '0',
					'i18n'                     => I18n::getTranslation(),
					'media_mode'               => get_user_option( 'media_library_mode', get_current_user_id() ),
					'json_url'                 => apply_filters( 'filebird_json_url', get_rest_url( null, NJFB_REST_URL ) ),
					'media_url'                => admin_url( 'upload.php' ),
					'asset_url'              => NJFB_PLUGIN_URL . 'assets/',
					'auto_import_url'          => esc_url(
						add_query_arg(
							array(
								'page'    => 'filebird-settings',
								'tab'     => 'tools',
								'autorun' => 'true',
							),
							admin_url( 'admin.php' )
						)
					),
					'pll_lang'                 => apply_filters( 'fbv_pll_lang', '' ),
					'is_new_user'              => get_option( 'fbv_is_new_user', false ),
					'import_other_plugins'     => ConvertModel::getInstance()->get_plugin3rd_folders_to_import(),
					'import_other_plugins_url' => esc_url(
						add_query_arg(
							array(
								'page' => 'filebird-settings',
								'tab'  => 'import',
							),
							admin_url( 'admin.php' )
						)
					),
					'update_database_notice'   => apply_filters( 'fbv_update_database_notice', false ),
					'utils'                    => new \stdClass(),
				// 'close_buy_pro_dialog' => time() < get_option('fbv_close_buy_pro_dialog', time())
				)
			)
		);
	}

	public function restrictManagePosts() {
		$screen = get_current_screen();
		if ( $screen->id == 'upload' ) {
			$fbv     = ( ( isset( $_GET['fbv'] ) ) ? (int) sanitize_text_field( $_GET['fbv'] ) : -1 );
			$folders = FolderModel::allFolders();

			$all       = new \stdClass();
			$all->id   = -1;
			$all->name = __( 'All Folders', 'filebird' );

			$uncategorized       = new \stdClass();
			$uncategorized->id   = 0;
			$uncategorized->name = __( 'Uncategorized', 'filebird' );

			array_unshift( $folders, $all, $uncategorized );
			echo '<select name="fbv" id="filter-by-fbv" class="fbv-filter attachment-filters fbv">';
			foreach ( $folders as $k => $folder ) {
				echo sprintf( '<option value="%1$d" %3$s>%2$s</option>', $folder->id, $folder->name, selected( $folder->id, $fbv, false ) );
			}
			echo '</select>';
		}
	}

	public function postsClauses( $clauses, $query ) {
		global $wpdb;
		if ( $query->get( 'post_type' ) !== 'attachment' ) {
			return $clauses;
		}

		if ( Helpers::isListMode() && ! isset( $_GET['fbv'] ) ) {
			return $clauses;
		}

		$fbvPropery = $query->get( 'fbv' );

		if ( isset( $_GET['fbv'] ) || $fbvPropery !== '' ) {
			$fbv = isset( $_GET['fbv'] ) ? (int) sanitize_text_field( $_GET['fbv'] ) : (int) $fbvPropery;

			if ( $fbv === -1 ) {
				return $clauses;
			} elseif ( $fbv === 0 ) {
				$clauses = FolderModel::getRelationsWithFolderUser( $clauses );
			} else {
				$clauses['join']  .= $wpdb->prepare( " LEFT JOIN {$wpdb->prefix}fbv_attachment_folder AS fbva ON fbva.attachment_id = {$wpdb->posts}.ID AND fbva.folder_id = %d ", $fbv );
				$clauses['where'] .= ' AND fbva.folder_id IS NOT NULL';
			}
		}
		return $clauses;
	}
	public function addAttachment( $post_id ) {
		 $fbv = ( ( isset( $_REQUEST['fbv'] ) ) ? sanitize_text_field( $_REQUEST['fbv'] ) : '' );
		if ( $fbv != '' ) {
			if ( is_numeric( $fbv ) ) {
				$parent = $fbv;
			} else {
				$fbv    = explode( '/', ltrim( rtrim( $fbv, '/' ), '/' ) );
				$parent = (int) $fbv[0];
				if ( $parent < 0 ) {
					$parent = 0; //important
				}
				if ( apply_filters( 'fbv_auto_create_folders', true ) ) {
					unset( $fbv[0] );
					foreach ( $fbv as $k => $v ) {
						$parent = FolderModel::newOrGet( $v, $parent );
					}
				}
			}
			FolderModel::setFoldersForPosts( $post_id, $parent );
		}
	}
	public function deleteAttachment( $post_id ) {
		FolderModel::deleteFoldersOfPost( $post_id );
	}

	public function ajaxQueryAttachmentsArgs( $query ) {
		if ( isset( $_REQUEST['query']['fbv'] ) ) {
			$fbv = $_REQUEST['query']['fbv'];
			if ( is_array( $fbv ) ) {
				$fbv = array_map( 'intval', $fbv );
			} else {
				$fbv = intval( $fbv );
			}
			$query['fbv'] = $fbv;
		}
		return $query;
	}
	public function ajaxGetFolder( $request ) {
		$icl_lang = $request->get_param( 'icl_lang' );
		$pll_lang = $request->get_param( 'pll_lang' );
		$sort     = sanitize_text_field( $request->get_param( 'sort' ) );

		$order_by    = null;
		$sort_option = 'reset';

		$lang = null;

		if ( ! is_null( $icl_lang ) ) {
			$lang = $icl_lang;
		}
		if ( ! is_null( $pll_lang ) ) {
			$lang = $pll_lang;
		}

		if ( \in_array( $sort, array( 'name_asc', 'name_desc', 'reset' ) ) ) {
			if ( 'name_asc' === $sort ) {
				$order_by    = 'LENGTH(name) ASC, name ASC';
				$sort_option = $sort;
			} elseif ( 'name_desc' === $sort ) {
				$order_by    = 'LENGTH(name) DESC, name DESC';
				$sort_option = $sort;
			}
			update_option( 'njt_fb_sort_folder', $sort_option );
		} else {
			$njt_fb_sort_folder = get_option( 'njt_fb_sort_folder', 'reset' );
			if ( $njt_fb_sort_folder === 'reset' ) {
				$order_by = null;
			} elseif ( $njt_fb_sort_folder === 'name_asc' ) {
				$order_by = 'name asc';
			} elseif ( $njt_fb_sort_folder === 'name_desc' ) {
				$order_by = 'name desc';
			}
		}

		$tree = Tree::getFolders( $order_by, false );

		wp_send_json_success(
			array(
				'tree'         => $tree,
				'folder_count' => array(
					'total'   => Tree::getCount( -1, $lang ),
					'folders' => Tree::getAllFoldersAndCount( $lang ),
				),
			)
		);
	}
	public function ajaxGutenbergGetFolder() {
		$_folders = Tree::getFolders( null, true, 0, true );
		$folders  = array(
			array(
				'value'    => 0,
				'label'    => __( 'Please choose folder', 'filebird' ),
				'disabled' => true,
			),
		);
		foreach ( $_folders as $k => $v ) {
			$folders[] = array(
				'value' => $v['id'],
				'label' => $v['text'],
			);
		}

		wp_send_json_success( $folders );
	}
	public function ajaxNewFolder( $request ) {
		$name   = $request->get_param( 'name' );
		$parent = $request->get_param( 'parent' );
		$name   = isset( $name ) ? sanitize_text_field( wp_unslash( $name ) ) : '';
		$parent = isset( $parent ) ? sanitize_text_field( $parent ) : '';
		if ( $name != '' && $parent != '' ) {
			$insert = FolderModel::newOrGet( $name, $parent, false );
			if ( $insert !== false ) {
				wp_send_json_success( array( 'id' => $insert ) );
			} else {
				wp_send_json_error( array( 'mess' => __( 'A folder with this name already exists. Please choose another one.', 'filebird' ) ) );
			}
		} else {
			wp_send_json_error(
				array(
					'mess' => __( 'Validation failed', 'filebird' ),
				)
			);
		}
	}
	public function ajaxUpdateFolder( $request ) {
		$id     = $request->get_param( 'id' );
		$parent = $request->get_param( 'parent' );
		$name   = $request->get_param( 'name' );

		$id     = isset( $id ) ? sanitize_text_field( $id ) : '';
		$parent = isset( $parent ) ? intval( sanitize_text_field( $parent ) ) : '';
		$name   = isset( $name ) ? sanitize_text_field( wp_unslash( $name ) ) : '';

		$current_user_id = get_current_user_id();
		$folder_per_user = get_option( 'njt_fbv_folder_per_user', '0' ) === '1';

		if ( is_numeric( $id ) && is_numeric( $parent ) && !empty( $name ) && FolderModel::verifyAuthor( $id, $current_user_id, $folder_per_user ) ) {
			$update = FolderModel::updateFolderName( $name, $parent, $id );
			if ( $update === true ) {
				wp_send_json_success();
			} else {
				wp_send_json_error( array( 'mess' => __( 'A folder with this name already exists. Please choose another one.', 'filebird' ) ) );
			}
		}
		wp_send_json_error();
	}
	public function ajaxUpdateFolderOrd( $request ) {
		$id     = $request->get_param( 'id' );
		$parent = $request->get_param( 'parent' );
		$ord    = $request->get_param( 'ord' );

		$id     = isset( $id ) ? sanitize_text_field( $id ) : '';
		$parent = isset( $parent ) ? sanitize_text_field( wp_unslash( $parent ) ) : '';
		$ord    = isset( $ord ) ? sanitize_text_field( wp_unslash( $ord ) ) : '';
		if ( is_numeric( $id ) && is_numeric( $parent ) && is_numeric( $ord ) ) {
			FolderModel::updateOrdAndParent( $id, $ord, $parent );
			wp_send_json_success();
		}
		wp_send_json_error();
	}
	public function ajaxDeleteFolder( $request ) {
		$ids = $request->get_param( 'ids' );
		$ids = isset( $ids ) ? Helpers::sanitize_array( $ids ) : '';
		if ( $ids != '' ) {
			if ( ! is_array( $ids ) ) {
				$ids = array( $ids );
			}
			$ids = array_map( 'intval', $ids );

			$current_user_id = get_current_user_id();
			$folder_per_user = get_option( 'njt_fbv_folder_per_user', '0' ) === '1';

			foreach ( $ids as $k => $v ) {
				if ( $v > 0 && FolderModel::verifyAuthor( $v, $current_user_id, $folder_per_user ) ) {
					FolderModel::deleteFolderAndItsChildren( $v );
				}
			}
			wp_send_json_success();
		}
		wp_send_json_error(
			array(
				'mess' => __( 'Can\'t delete folder, please try again later', 'filebird' ),
			)
		);
	}
	public function ajaxSetFolder( $request ) {
		 $ids   = $request->get_param( 'ids' );
		$folder = $request->get_param( 'folder' );

		$ids    = isset( $ids ) ? Helpers::sanitize_array( $ids ) : '';
		$folder = isset( $folder ) ? sanitize_text_field( $folder ) : '';
		if ( $ids != '' && is_array( $ids ) && is_numeric( $folder ) ) {
			FolderModel::setFoldersForPosts( $ids, $folder );
			wp_send_json_success();
		}
		wp_send_json_error(
			array(
				'mess' => __( 'Validation failed', 'filebird' ),
			)
		);
	}
	public function ajaxUpdateTree( $request ) {
		$tree = $request->get_param( 'tree' );

		$tree = isset( $tree ) ? sanitize_text_field( $tree ) : '';
		if ( $tree != '' ) {
			$tree = preg_replace( '#[^0-9,()]#', '', $tree );
			FolderModel::rawInsert( '(id, ord, parent) VALUES ' . $tree . ' ON DUPLICATE KEY UPDATE ord=VALUES(ord),parent=VALUES(parent)' );
			wp_send_json_success(
				array(
					'mess' => __( 'Folder tree has been updated.', 'filebird' ),
				)
			);
		}
		wp_send_json_error(
			array(
				'mess' => __( 'Validation failed', 'filebird' ),
			)
		);
	}
	public function ajaxGetRelations() {
		wp_send_json_success(
			array(
				'relations' => FolderModel::getRelations(),
			)
		);
	}
	public function ajaxSetSettings( $request ) {
		$folder_id = $request->get_param( 'folder_id' );

		$folder_id = isset( $folder_id ) ? intval( $folder_id ) : -1;
		$this->userSettings->setDefaultSelectedFolder( $folder_id );
		wp_send_json_success();
	}

	public function attachment_fields_to_edit( $form_fields, $post ) {
		$screen = null;
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();

			if ( ! is_null( $screen ) && 'attachment' === $screen->id ) {
				return $form_fields;
			}
		}

		$fbv_folder         = FolderModel::getFolderFromPostId( $post->ID );
		$fbv_folder         = count( $fbv_folder ) > 0 ? $fbv_folder[0] : (object) array(
			'folder_id' => 0,
			'name'      => __( 'Uncategorized', 'filebird' ),
		);
		$folder_name = esc_attr( $fbv_folder->name );
		$folder_id = (int)$fbv_folder->folder_id;
		$post_id = (int)$post->ID;
		$form_fields['fbv'] = array(
			'html'  => "<div class='fbv-attachment-edit-wrapper' data-folder-id='{$folder_id}' data-attachment-id='{$post_id}'><input readonly type='text' value='{$folder_name}'/></div>",
			'label' => esc_html__( 'FileBird folder:', 'filebird' ),
			'helps' => esc_html__( 'Click on the button to move this file to another folder', 'filebird' ),
			'input' => 'html',
		);

		return $form_fields;
	}

	public function attachment_fields_to_save( $post, $attachment ) {
		if ( isset( $attachment['fbv'] ) ) {
			FolderModel::setFoldersForPosts( $post['ID'], $attachment['fbv'] );
		}
		return $post;
	}

	public function edited_image_metadata( $new_image_meta, $new_attachment_id, $attachment_id ) {
		$folder = FolderModel::getFolderFromPostId( $attachment_id );
		if( is_array( $folder ) && count( $folder ) > 0 ) {
			if( (int) $folder[0]->folder_id > 0 ) {
				FolderModel::setFoldersForPosts( $new_attachment_id, (int) $folder[0]->folder_id );
			}
		}
		return $new_image_meta;
	}
	private static function addFolderToZip( &$zip, $children, $parent_dir = '' ) {
		foreach ( $children as $k => $v ) {
			$folder_name = $v->name;
			$folder_id   = $v->id;

			// $folder_name = sanitize_title( $folder_name );

			$attachment_ids = Helpers::getAttachmentIdsByFolderId( $folder_id );
			$empty_dir      = $parent_dir != '' ? $parent_dir . '/' . $folder_name : $folder_name;
			$zip->addEmptyDir( $empty_dir );

			foreach ( $attachment_ids as $k => $id ) {
				$file = get_attached_file( $id );
				if ( $file ) {
					$zip->addFile( $file, $empty_dir . '/' . \basename( $file ) );
				}
			}
			if ( \is_array( $v->children ) ) {
				self::addFolderToZip( $zip, $v->children, $empty_dir );
			}
		}
	}
	public function actionPluploadUi() {
		$this->loadView( 'particle/folder_dropdown' );
	}
	private function _buildQuery( $tree, $parent ) {
		$results = array();
		$ord     = 0;
		foreach ( $tree as $k => $v ) {
			// if($v['key'] < 1) continue;
			$results[] = sprintf( '(%1$d, %2$d, %3$d)', $v['id'], $ord, $parent );
			if ( isset( $v['children'] ) && is_array( $v['children'] ) && count( $v['children'] ) > 0 ) {
				$children = $this->_buildQuery( $v['children'], $v['id'] );
				foreach ( $children as $k2 => $v2 ) {
					$results[] = $v2;
				}
			}
			$ord++;
		}
		return $results;
	}

	public function getFlatTree( $data = array(), $parent = 0, $default = null, $level = 0 ) {
		$tree = is_null( $default ) ? array() : $default;
		foreach ( $data as $k => $v ) {
			if ( $v->parent == $parent ) {
				$node     = array(
					'title' => str_repeat( '-', $level ) . $v->name,
					'value' => $v->id,
				);
				$tree[]   = $node;
				$children = $this->getFlatTree( $data, $v->id, null, $level + 1 );
				foreach ( $children as $k2 => $child ) {
					$tree[] = $child;
				}
			}
		}
		return $tree;
	}

	public function exportCSV() {
		global $wpdb;

		$folders       = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}fbv", ARRAY_A );
		$attachmentIds = $wpdb->get_results( "SELECT folder_id, GROUP_CONCAT(attachment_id SEPARATOR '|') as attachment_ids FROM {$wpdb->prefix}fbv_attachment_folder GROUP BY (folder_id)", OBJECT_K );

		foreach ( $folders as $key => $folder ) {
			$folders[ $key ]['attachment_ids'] = $attachmentIds[ $folder['id'] ]->attachment_ids ? $attachmentIds[ $folder['id'] ]->attachment_ids : '';
		}

		return new \WP_REST_Response( array( 'folders' => $folders ) );
	}

	public function buildTree( array &$data, $parentId = 0 ) {
		$tree = array();
		foreach ( $data as &$node ) {
			if ( $node['parent'] == $parentId ) {
				$children = $this->buildTree( $data, $node['id'] );
				if ( $children ) {
					$node['children'] = $children;
				}
				$tree[] = $node;
				unset( $node );
			}
		}
		return $tree;
	}

	public function run_import_folders( $folders, $parent = 0 ) {
        $folders_created = array();

		foreach ( $folders as $folder ) {
			$new_folder_id  = FolderModel::newOrGet( $folder['name'], $parent );
			$attachment_ids = ! empty( $folder['attachment_ids'] ) ? explode( '|', $folder['attachment_ids'] ) : false;
            array_push( $folders_created, $folder['id'] );

			if ( $attachment_ids && false !== $new_folder_id ) {
				FolderModel::setFoldersForPosts( $attachment_ids, $new_folder_id );
			}

			if ( isset( $folder['children'] ) && count( $folder['children'] ) > 0 ) {
				$new_child_folders = $this->run_import_folders( $folder['children'], $new_folder_id );
				$folders_created   = array_merge( $folders_created, $new_child_folders );
			}
		}

		return $folders_created;
    }

	public function restoreFolderStructure( $folders ) {
		$tree            = $this->buildTree( $folders );
        $folders_created = $this->run_import_folders( $tree );

		$mess = sprintf( __( 'Congratulations! We imported successfully %d folders into <strong>FileBird.</strong>', 'filebird' ), count( $folders_created ) );

		return new \WP_REST_Response( array( 'mess' => $mess ) );
	}

	public function readCSV( \WP_REST_Request $request ) {
		$params  = $request->get_file_params();
		$handle  = \fopen( $params['file']['tmp_name'], 'r' );
		$data    = array();
		$columns = array();
		if ( false !== $handle ) {
			$count = 1;
			while ( 1 ) {
				$row = fgetcsv( $handle, 0 );
				if ( 1 === $count ) {
					$columns = $row;
					$count++;
					continue;
				}
				if ( false === $row ) {
					break;
				}
				foreach ( $columns as $key => $col ) {
					$tmp[ $col ] = $row[ $key ];
				}
				$data[] = $tmp;
			}
		}
		\fclose( $handle );

		$check = array_diff(
			$columns,
			array(
				'id',
				'name',
				'parent',
				'type',
				'ord',
				'created_by',
				'attachment_ids',
			)
		);

		if ( count( $check ) > 0 ) {
			return new \WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'The uploaded file was not generated by FileBird. Please check again.', 'filebird' ),
				)
			);
		}

		return $data;
	}

	public function importCSV( \WP_REST_Request $request ) {
		$data      = $this->readCSV( $request );
		$createdBy = intval( $request->get_param( 'userId' ) );

		if ( $createdBy != '-1' ) {
			$data = array_filter(
                $data,
                function( $item ) use ( $createdBy ) {
				return $item['created_by'] == $createdBy;
				}
                );
		}

		$result = $this->restoreFolderStructure( $data );

		return new \WP_REST_Response( array( 'success' => $result ) );
	}

	public function getImportCSVDetail( \WP_REST_Request $request ) {
		$data = $this->readCSV( $request );

		$users = \get_users(
            array(
				'include' => array_unique( array_column( $data, 'created_by' ) ),
			)
            );

		$usersReturn = array();

		foreach ( $users as $user ) {
			$usersReturn[ $user->ID ] = $user->data->display_name . ' ' . __( 'folders', 'filebird' );
		}

		return new \WP_REST_Response( $usersReturn );
	}

	public function deleted_user( $id, $reassign, $user ) {
		if ( $reassign === null ) {
			FolderModel::deleteByAuthor( $id );
		} else {
			FolderModel::updateAuthor( $id, (int) $reassign );
		}
	}
	public function users_have_additional_content( $users_have_content, $userids ) {
		global $wpdb;
		if ( $userids && ! $users_have_content ) {
			if ( $wpdb->get_var( "SELECT id FROM {$wpdb->prefix}fbv WHERE created_by IN( " . implode( ',', $userids ) . ' ) LIMIT 1' ) ) {
				$users_have_content = true;
			}
		}
		return $users_have_content;
	}
}

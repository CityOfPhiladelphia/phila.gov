<?php
namespace FileBird\Classes;

use FileBird\Controller\Convert as ConvertController;
use FileBird\Model\Folder as FolderModel;
use FileBird\Classes\Helpers;

defined( 'ABSPATH' ) || exit;

class Convert {

	protected static $instance = null;
	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
			self::$instance->doHooks();
		}
		return self::$instance;
	}

	public function __construct() {
	}

	private function doHooks() {
		add_action( 'rest_api_init', array( $this, 'registerRestFields' ) );
	}

	public function registerRestFields() {
		register_rest_route(
			NJFB_REST_URL,
			'fb-import',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'ajaxImport' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
			)
		);
		register_rest_route(
			NJFB_REST_URL,
			'fb-import-insert-folder',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'ajaxImportInsertFolder' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
			)
		);
		register_rest_route(
			NJFB_REST_URL,
			'fb-import-after-inserting',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'ajaxImportAfterInserting' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
			)
		);
		register_rest_route(
			NJFB_REST_URL,
			'fb-no-thanks',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'ajaxNoThanks' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
			)
		);
		//get old data
		register_rest_route(
			NJFB_REST_URL,
			'fb-get-old-data',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'ajaxGetOldData' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
			)
		);
		//insert old data
		register_rest_route(
			NJFB_REST_URL,
			'fb-insert-old-data',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'ajaxInsertOldData' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
			)
		);
		//wipe old data
		register_rest_route(
			NJFB_REST_URL,
			'fb-wipe-old-data',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'ajaxWipeOldData' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
			)
		);
		//wipe old data
		register_rest_route(
			NJFB_REST_URL,
			'fb-wipe-clear-all-data',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'ajaxClearAllData' ),
				'permission_callback' => array( $this, 'resPermissionsCheck' ),
			)
		);
	}
	public function resPermissionsCheck() {
		return current_user_can( 'upload_files' );
	}

	public function plugin_import_description( $counter, $pluginName ) {
		return wp_kses_post( sprintf( __( 'We found you have %1$s categories you created from <strong>%2$s</strong> plugin. Would you like to import it to <strong>FileBird</strong>?', 'filebird' ), $counter, $pluginName ) );
	}

	public function get_plugin3rd_folders_to_import() {
		global $pagenow;

		$sites = array();

		if ( $pagenow !== 'upload.php' ) {
			return $sites;
		}

		$oldEnhancedFolders = array();
		if ( ! $this->isUpdated( 'enhanced' ) && ! $this->isNoThanks( 'enhanced' ) ) {
			$oldEnhancedFolders = $this->getOldFolders( 'enhanced', true );
		}

		$oldWpmlfFolders = array();
		if ( ! $this->isUpdated( 'wpmlf' ) && ! $this->isNoThanks( 'wpmlf' ) ) {
			$oldWpmlfFolders = $this->getOldFolders( 'wpmlf', true );
		}

		$oldWpmfFolders = array();
		if ( ! $this->isUpdated( 'wpmf' ) && ! $this->isNoThanks( 'wpmf' ) ) {
			$oldWpmfFolders = $this->getOldFolders( 'wpmf', true );
		}

		$oldRealMediaFolders = array();
		if ( ! $this->isUpdated( 'realmedia' ) && ! $this->isNoThanks( 'realmedia' ) ) {
			$oldRealMediaFolders = $this->getOldFolders( 'realmedia', true );
		}

		$oldHappyFilesFolders = array();
		if ( ! $this->isUpdated( 'happyfiles' ) && ! $this->isNoThanks( 'happyfiles' ) ) {
			$oldHappyFilesFolders = $this->getOldFolders( 'happyfiles', true );
		}

		$oldPremioFolders = array();
		if ( ! $this->isUpdated( 'premio' ) && ! $this->isNoThanks( 'premio' ) ) {
			$oldPremioFolders = $this->getOldFolders( 'premio', true );
		}

		$oldFemlFolders = array();
		if ( ! $this->isUpdated( 'feml' ) && ! $this->isNoThanks( 'feml' ) ) {
			$oldFemlFolders = $this->getOldFolders( 'feml', true );
		}

		$oldMediamaticFolders = array();
		if ( ! $this->isUpdated( 'mediamatic' ) && ! $this->isNoThanks( 'mediamatic' ) ) {
			$oldMediamaticFolders = $this->getOldFolders( 'mediamatic', true );
		}

		if ( count( $oldEnhancedFolders ) > 3 ) {
			$sites[] = array(
				'site'  => 'enhanced',
				'title' => 'Enhanced Media Library',
				'desc'  => $this->plugin_import_description( count( $oldEnhancedFolders ), 'Enhanced Media Library' ),
			);
		}
		if ( count( $oldWpmlfFolders ) > 3 ) {
			$sites[] = array(
				'site'  => 'wpmlf',
				'title' => 'Media Library Folders',
				'desc'  => $this->plugin_import_description( count( $oldWpmlfFolders ), 'Media Library Folders' ),
			);
		}
		if ( count( $oldWpmfFolders ) > 3 ) {
			$sites[] = array(
				'site'  => 'wpmf',
				'title' => 'WP Media folder',
				'desc'  => $this->plugin_import_description( count( $oldWpmfFolders ), 'WP Media folder' ),
			);
		}
		if ( count( $oldRealMediaFolders ) > 3 ) {
			$sites[] = array(
				'site'  => 'realmedia',
				'title' => 'WP Real Media Library',
				'desc'  => $this->plugin_import_description( count( $oldRealMediaFolders ), 'WP Real Media Library' ),
			);
		}
		if ( count( $oldHappyFilesFolders ) > 3 ) {
			$sites[] = array(
				'site'  => 'happyfiles',
				'title' => 'HappyFiles',
				'desc'  => $this->plugin_import_description( count( $oldHappyFilesFolders ), 'HappyFiles' ),
			);
		}
		if ( count( $oldPremioFolders ) > 3 ) {
			$sites[] = array(
				'site'  => 'premio',
				'title' => 'premio',
				'desc'  => $this->plugin_import_description( count( $oldPremioFolders ), 'Folders' ),
			);
		}

		if ( count( $oldFemlFolders ) > 3 ) {
			$sites[] = array(
				'site'  => 'feml',
				'title' => 'feml',
				'desc'  => $this->plugin_import_description( count( $oldFemlFolders ), 'WP Media Folders' ),
			);
		}

		if ( count( $oldMediamaticFolders ) > 3 ) {
			$sites[] = array(
				'site'  => 'mediamatic',
				'title' => 'Mediamatic',
				'desc'  => $this->plugin_import_description( count( $oldMediamaticFolders ), 'Mediamatic' ),
			);
		}

		return $sites;
	}

	public function adminNotice() {
		global $pagenow;

		$sites = array();

		if ( $pagenow !== 'upload.php' ) {
			return;
		}

		$oldEnhancedFolders = array();
		if ( ! $this->isUpdated( 'enhanced' ) && ! $this->isNoThanks( 'enhanced' ) ) {
			$oldEnhancedFolders = $this->getOldFolders( 'enhanced', true );
		}

		$oldWpmlfFolders = array();
		if ( ! $this->isUpdated( 'wpmlf' ) && ! $this->isNoThanks( 'wpmlf' ) ) {
			$oldWpmlfFolders = $this->getOldFolders( 'wpmlf', true );
		}

		$oldWpmfFolders = array();
		if ( ! $this->isUpdated( 'wpmf' ) && ! $this->isNoThanks( 'wpmf' ) ) {
			$oldWpmfFolders = $this->getOldFolders( 'wpmf', true );
		}

		$oldRealMediaFolders = array();
		if ( ! $this->isUpdated( 'realmedia' ) && ! $this->isNoThanks( 'realmedia' ) ) {
			$oldRealMediaFolders = $this->getOldFolders( 'realmedia', true );
		}

		$oldHappyFilesFolders = array();
		if ( ! $this->isUpdated( 'happyfiles' ) && ! $this->isNoThanks( 'happyfiles' ) ) {
			$oldHappyFilesFolders = $this->getOldFolders( 'happyfiles', true );
		}

		$oldMediamaticFolders = array();
		if ( ! $this->isUpdated( 'mediamatic' ) && ! $this->isNoThanks( 'mediamatic' ) ) {
			$oldMediamaticFolders = $this->getOldFolders( 'mediamatic', true );
		}

		if ( count( $oldEnhancedFolders ) > 3 ) {
			$sites[] = array(
				'site'  => 'enhanced',
				'title' => 'Enhanced Media Library',
			);
		}
		if ( count( $oldWpmlfFolders ) > 3 ) {
			$sites[] = array(
				'site'  => 'wpmlf',
				'title' => 'Media Library Folders',
			);
		}
		if ( count( $oldWpmfFolders ) > 3 ) {
			$sites[] = array(
				'site'  => 'wpmf',
				'title' => 'WP Media folder',
			);
		}
		if ( count( $oldRealMediaFolders ) > 3 ) {
			$sites[] = array(
				'site'  => 'realmedia',
				'title' => 'WP Real Media Library',
			);
		}
		if ( count( $oldHappyFilesFolders ) > 3 ) {
			$sites[] = array(
				'site'  => 'happyfiles',
				'title' => 'HappyFiles',
			);
		}
		if ( count( $oldMediamaticFolders ) > 3 ) {
			$sites[] = array(
				'site'  => 'mediamatic',
				'title' => 'Mediamatic',
			);
		}

		foreach ( $sites as $k => $site ) :
			$c = 0;
			if ( $site['site'] == 'enhanced' ) {
				$c = count( $oldEnhancedFolders );
			} elseif ( $site['site'] == 'wpmlf' ) {
				$c = count( $oldWpmlfFolders );
			} elseif ( $site['site'] == 'wpmf' ) {
				$c = count( $oldWpmfFolders );
			} elseif ( $site['site'] == 'realmedia' ) {
				$c = count( $oldRealMediaFolders );
			} elseif ( $site['site'] == 'happyfiles' ) {
				$c = count( $oldHappyFilesFolders );
			} elseif ( $site['site'] == 'mediamatic' ) {
				$c = count( $oldMediamaticFolders );
			}
			?>
		  <div class="njt notice notice-warning <?php echo esc_attr( $site['site'] ); ?> is-dismissible">
			<p>
			  <strong><?php esc_html_e( 'Import categories to FileBird', 'filebird' ); ?></strong>
			</p>
			<p>
			  <?php _e( sprintf( __( 'We found you have %1$s categories you created from <strong>%2$s</strong> plugin. Would you like to import it to <strong>FileBird</strong>?', 'filebird' ), $c, $site['title'] ) ); ?>
			</p>
						  <a target="_blank" href="
						  <?php
							echo esc_url(
								add_query_arg(
									array(
										'page' => 'filebird-settings',
										'tab'  => 'import',
									),
									admin_url( 'admin.php' )
								)
							);
							?>
													" class="button button-primary"><?php esc_html_e( 'Import Now', 'filebird' ); ?></a> 
			  <button class="button njt_fb_no_thanks_btn" data-site="<?php echo esc_attr( $site['site'] ); ?>"><?php esc_html_e( 'No, thanks', 'filebird' ); ?></button> 
			</p>
		  </div>
		<?php endforeach; ?>
		  <?php
	}
	public function ajaxNoThanks( $request ) {
		$site = $request->get_param( 'site' );

		$site = isset( $site ) ? sanitize_text_field( $site ) : '';
		// if ( ! wp_verify_nonce( $nonce, 'fbv_nonce' ) ){
		//   wp_send_json_error(array('mess' => __('Nonce error')));
		//   exit();
		// }
		if ( $site == 'enhanced' ) {
			update_option( 'njt_fb_enhanced_no_thanks', '1' );
		} elseif ( $site == 'wpmlf' ) {
			update_option( 'njt_fb_wpmlf_no_thanks', '1' );
		} elseif ( $site == 'wpmf' ) {
			update_option( 'njt_fb_wpmf_no_thanks', '1' );
		} elseif ( $site == 'realmedia' ) {
			update_option( 'njt_fb_realmedia_no_thanks', '1' );
		} elseif ( $site == 'happyfiles' ) {
			update_option( 'njt_fb_happyfiles_no_thanks', '1' );
		} elseif ( $site == 'premio' ) {
			update_option( 'njt_fb_premio_no_thanks', '1' );
		} elseif ( $site == 'feml' ) {
			update_option( 'njt_fb_feml_no_thanks', '1' );
		} elseif ( $site == 'mediamatic' ) {
			update_option( 'njt_fb_mediamatic_no_thanks', '1' );
		} elseif ( $site == 'all' ) {
			update_option( 'njt_fb_happyfiles_no_thanks', '1' );
			update_option( 'njt_fb_realmedia_no_thanks', '1' );
			update_option( 'njt_fb_wpmf_no_thanks', '1' );
			update_option( 'njt_fb_enhanced_no_thanks', '1' );
			update_option( 'njt_fb_wpmlf_no_thanks', '1' );
			update_option( 'njt_fb_premio_no_thanks', '1' );
			update_option( 'njt_fb_feml_no_thanks', '1' );
			update_option( 'njt_fb_mediamatic_no_thanks', '1' );
		}

		wp_send_json_success(
			array(
				'mess' => __( 'Success', 'filebird' ),
			)
		);
	}

	public function ajaxGetOldData() {
		$folders       = ConvertController::getOldFolers();
		$folders_chunk = array_chunk( $folders, 20 );
		wp_send_json_success(
			array(
				'folders' => $folders_chunk,
			)
		);
	}
	public function ajaxInsertOldData( $request ) {
		$folders = isset( $request ) ? $request->get_params()['folders'] : '';
		if ( $folders != '' ) {
			ConvertController::insertToNewTable( $folders );
			update_option( 'fbv_old_data_updated_to_v4', '1' );
			wp_send_json_success( array( 'mess' => __( 'success', 'filebird' ) ) );
		} else {
			wp_send_json_error( array( 'mess' => __( 'validation failed', 'filebird' ) ) );
		}
	}

	public function ajaxWipeOldData() {
		global $wpdb;
		$queries = array(
			'DELETE FROM ' . $wpdb->prefix . 'termmeta WHERE `term_id` IN (SELECT `term_id` FROM ' . $wpdb->prefix . "term_taxonomy WHERE `taxonomy` = 'nt_wmc_folder')",
			'DELETE FROM ' . $wpdb->prefix . 'term_relationships WHERE `term_taxonomy_id` IN (SELECT `term_taxonomy_id` FROM ' . $wpdb->prefix . "term_taxonomy WHERE `taxonomy` = 'nt_wmc_folder')",
			'DELETE FROM ' . $wpdb->prefix . 'terms WHERE `term_id` IN (SELECT `term_id` FROM ' . $wpdb->prefix . "term_taxonomy WHERE `taxonomy` = 'nt_wmc_folder')",
			'DELETE FROM ' . $wpdb->prefix . "term_taxonomy WHERE `taxonomy` = 'nt_wmc_folder'",
		);
		foreach ( $queries as $k => $query ) {
			$wpdb->query( $query );
		}
		wp_send_json_success(
			array(
				'mess' => __( 'Successfully wiped.', 'filebird' ),
			)
		);
	}
	public function ajaxClearAllData() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'fbv';
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) ) ) == $table_name ) {
			FolderModel::deleteAll();

			update_option( 'njt_fb_updated_from_enhanced', '0' );
			update_option( 'njt_fb_updated_from_wpmlf', '0' );
			update_option( 'njt_fb_updated_from_wpmf', '0' );
			update_option( 'njt_fb_updated_from_realmedia', '0' );
			update_option( 'njt_fb_updated_from_happyfiles', '0' );
			update_option( 'njt_fb_updated_from_feml', '0' );
			update_option( 'njt_fb_updated_from_mediamatic', '0' );

			wp_send_json_success(
				array(
					'mess' => __( 'Successfully cleared.', 'filebird' ),
				)
			);
		} else {
			wp_send_json_error(
				array(
					'mess' => __( 'Please try again.', 'filebird' ),
				)
			);
		}

	}
	public function isUpdated( $site ) {
		global $wpdb;
		$is = false;
		if ( $site == 'enhanced' ) {
			$is = get_option( 'njt_fb_updated_from_enhanced', '0' ) === '1';
		} elseif ( $site == 'wpmlf' ) {
			$is = get_option( 'njt_fb_updated_from_wpmlf', '0' ) === '1';
		} elseif ( $site == 'wpmf' ) {
			$is = get_option( 'njt_fb_updated_from_wpmf', '0' ) === '1';
		} elseif ( $site == 'realmedia' ) {
			$is = get_option( 'njt_fb_updated_from_realmedia', '0' ) === '1';
		} elseif ( $site == 'happyfiles' ) {
			$is = get_option( 'njt_fb_updated_from_happyfiles', '0' ) === '1';
		} elseif ( $site == 'premio' ) {
			$is = get_option( 'njt_fb_updated_from_premio', '0' ) === '1';
		} elseif ( $site == 'feml' ) {
			$is = get_option( 'njt_fb_updated_from_feml', '0' ) === '1';
		} elseif ( $site == 'mediamatic' ) {
			$is = get_option( 'njt_fb_updated_from_mediamatic', '0' ) === '1';
		}

		return $is;
	}
	public function isNoThanks( $site ) {
		if ( $site == 'enhanced' ) {
			return get_option( 'njt_fb_enhanced_no_thanks', '0' ) === '1';
		} elseif ( $site == 'wpmlf' ) {
			return get_option( 'njt_fb_wpmlf_no_thanks', '0' ) === '1';
		} elseif ( $site == 'wpmf' ) {
			return get_option( 'njt_fb_wpmf_no_thanks', '0' ) === '1';
		} elseif ( $site == 'realmedia' ) {
			return get_option( 'njt_fb_realmedia_no_thanks', '0' ) === '1';
		} elseif ( $site == 'happyfiles' ) {
			return get_option( 'njt_fb_happyfiles_no_thanks', '0' ) === '1';
		} elseif ( $site == 'premio' ) {
			return get_option( 'njt_fb_premio_no_thanks', '0' ) === '1';
		} elseif ( $site == 'feml' ) {
			return get_option( 'njt_fb_feml_no_thanks', '0' ) === '1';
		} elseif ( $site == 'mediamatic' ) {
			return get_option( 'njt_fb_mediamatic_no_thanks', '0' ) === '1';
		}
	}

	public function ajaxImport( $request ) {
		$site = $request->get_param( 'site' );

		$site = isset( $site ) ? sanitize_text_field( $site ) : '';

		$this->beforeGettingNewFolders( $site );
		$folders = $this->getOldFolders( $site, true );
		$count   = count( $folders );
		$folders = array_chunk( $folders, 20 );

		wp_send_json_success(
			array(
				'folders' => $folders,
				'count'   => $count,
				'site'    => $site,
			)
		);
		exit();
	}
	public function ajaxImportInsertFolder( $request ) {
		$site    = $request->get_param( 'site' );
		$folders = $request->get_param( 'folders' );

		$site    = isset( $site ) ? sanitize_text_field( $site ) : '';
		$folders = isset( $folders ) ? $this->sanitize_arr( $folders ) : '';

		$this->insertFolderAndItsAtt( $site, $folders );

		wp_send_json_success();
		exit();
	}
	public function ajaxImportAfterInserting( $request ) {
		$site  = $request->get_param( 'site' );
		$count = $request->get_param( 'count' );

		$site  = isset( $site ) ? sanitize_text_field( $site ) : '';
		$count = isset( $count ) ? sanitize_text_field( $count ) : '';
		$this->afterInsertingNewFolders( $site );
		$this->updateUpdated( $site );

		$mess = sprintf( __( 'Congratulations! We imported successfully %d folders into <strong>FileBird.</strong>', 'filebird' ), $count );
		wp_send_json_success(
			array(
				'mess' => $mess,
			)
		);
		exit();
	}
	private function beforeGettingNewFolders( $site ) {
		if ( $site == 'enhanced' ) {
			if ( get_option( 'njt_fb_updated_from_enhanced', '0' ) == '1' ) {
				wp_send_json_success(
					array(
						'mess' => __( 'Already Updated', 'filebird' ),
					)
				);
				exit();
			}
		} elseif ( $site == 'wpmlf' ) {
			if ( get_option( 'njt_fb_updated_from_wpmlf', '0' ) == '1' ) {
				wp_send_json_success(
					array(
						'mess' => __( 'Already Updated', 'filebird' ),
					)
				);
				exit();
			}
		} elseif ( $site == 'wpmf' ) {
			if ( get_option( 'njt_fb_updated_from_wpmf', '0' ) == '1' ) {
				wp_send_json_success(
					array(
						'mess' => __( 'Already Updated', 'filebird' ),
					)
				);
				exit();
			}
		} elseif ( $site == 'realmedia' ) {
			if ( get_option( 'njt_fb_updated_from_realmedia', '0' ) == '1' ) {
				wp_send_json_success(
					array(
						'mess' => __( 'Already Updated', 'filebird' ),
					)
				);
				exit();
			}
		} elseif ( $site == 'happyfiles' ) {
			if ( get_option( 'njt_fb_updated_from_happyfiles', '0' ) == '1' ) {
				wp_send_json_success(
					array(
						'mess' => __( 'Already Updated', 'filebird' ),
					)
				);
				exit();
			}
		} elseif ( $site == 'premio' ) {
			if ( get_option( 'njt_fb_updated_from_premio', '0' ) == '1' ) {
				wp_send_json_success(
					array(
						'mess' => __( 'Already Updated', 'filebird' ),
					)
				);
				exit();
			}
		} elseif ( $site == 'feml' ) {
			if ( get_option( 'njt_fb_updated_from_feml', '0' ) == '1' ) {
				wp_send_json_success(
					array(
						'mess' => __( 'Already Updated', 'filebird' ),
					)
				);
				exit();
			}
		} elseif ( $site == 'mediamatic' ) {
			if ( get_option( 'njt_fb_updated_from_mediamatic', '0' ) == '1' ) {
				wp_send_json_success(
					array(
						'mess' => __( 'Already Updated', 'filebird' ),
					)
				);
				exit();
			}
		}
	}
	public function getOldFolders( $site, $flat = false ) {
		$folders = array();
		if ( $site == 'enhanced' ) {
			$folders = Helpers::foldersFromEnhanced( 0, $flat );
		} elseif ( $site == 'wpmlf' ) {
			$folders = Helpers::foldersFromWpmlf( 0, $flat );
		} elseif ( $site == 'wpmf' ) {
			$folders = Helpers::foldersFromWpmf( 0, $flat );
		} elseif ( $site == 'realmedia' ) {
			$folders = Helpers::foldersFromRealMedia( -1, $flat );
			foreach ( $folders as $k => $folder ) {
				$folders[ $k ]->parent = $folder->parent == '-1' ? 0 : $folder->parent;
			}
		} elseif ( $site == 'happyfiles' ) {
			$folders = Helpers::foldersFromHappyFiles( 0, $flat );
		} elseif ( $site == 'premio' ) {
			$folders = Helpers::foldersFromPremio( 0, $flat );
		} elseif ( $site == 'feml' ) {
			$folders = Helpers::foldersFromWpfeml( 0, $flat );
		} elseif ( $site == 'mediamatic' ) {
			$folders = Helpers::foldersFromMediamatic( 0, $flat );
		}
		return $folders;
	}
	public function insertFolderAndItsAtt( $site, $folders ) {
		foreach ( $folders as $k => $folder ) {
			if ( \is_array( $folder ) ) {
				$folder = json_decode( wp_json_encode( $folder ) );
			}
			$new_parent = $folder->parent;
			if ( $new_parent > 0 ) {
				$new_parent = get_option( 'njt_new_term_id_' . $new_parent );
			}
			$inserted = FolderModel::newOrGet( $folder->title, $new_parent );
			update_option( 'njt_new_term_id_' . $folder->id, $inserted );
			$atts = $this->getAttOfFolder( $site, $folder );
			FolderModel::setFoldersForPosts( $atts, $inserted );
		}
	}
	public function getAttOfFolder( $site, $folder ) {
		global $wpdb;
		$att = array();
		if ( is_array( $folder ) ) {
			$folder = json_decode( wp_json_encode( $folder ) );
		}

		if ( $site == 'enhanced' ) {
			$att = $wpdb->get_col( $wpdb->prepare( 'SELECT object_id FROM %1$s WHERE term_taxonomy_id = %2$d', $wpdb->term_relationships, $folder->term_taxonomy_id ) );
		} elseif ( $site == 'wpmlf' ) {
			$folder_table = $wpdb->prefix . 'mgmlp_folders';
			$sql          = $wpdb->prepare(
				"select ID from {$wpdb->prefix}posts 
          LEFT JOIN $folder_table ON({$wpdb->prefix}posts.ID = $folder_table.post_id)
          LEFT JOIN {$wpdb->prefix}postmeta AS pm ON (pm.post_id = {$wpdb->prefix}posts.ID) 
          where post_type = 'attachment' 
          and folder_id = %s
          AND pm.meta_key = '_wp_attached_file' 
          order by post_date desc",
				$folder->id
			);
			$att          = $wpdb->get_col( $sql );
		} elseif ( $site == 'wpmf' ) {
			$att = $wpdb->get_col( $wpdb->prepare( 'SELECT object_id FROM %1$s WHERE term_taxonomy_id = %2$d', $wpdb->term_relationships, $folder->term_taxonomy_id ) );
		} elseif ( $site == 'realmedia' ) {
			$folder_table = $wpdb->prefix . 'realmedialibrary_posts';
			$att          = $wpdb->get_col( $wpdb->prepare( 'SELECT attachment FROM %1$s WHERE fid = %2$d', $folder_table, $folder->id ) );
		} elseif ( $site == 'happyfiles' ) {
			$att = $wpdb->get_col( $wpdb->prepare( 'SELECT object_id FROM %1$s WHERE term_taxonomy_id = %2$d', $wpdb->term_relationships, $folder->term_taxonomy_id ) );
		} elseif ( $site == 'premio' ) {
			$att = $wpdb->get_col( $wpdb->prepare( "SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d", $folder->term_taxonomy_id ) );
		} elseif ( $site == 'feml' ) {
			$att = $wpdb->get_col( $wpdb->prepare( "SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d", $folder->term_taxonomy_id ) );
		} elseif ( $site == 'mediamatic' ) {
			$att = $wpdb->get_col( $wpdb->prepare( "SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d", $folder->term_taxonomy_id ) );
		}
		return $att;
	}
	private function afterInsertingNewFolders( $site ) {
		global $wpdb;
		$wpdb->delete( $wpdb->termmeta, array( 'meta_key' => 'njt_old_term_id' ) );
	}
	private function updateUpdated( $site ) {
		if ( $site == 'enhanced' ) {
			update_option( 'njt_fb_updated_from_enhanced', '1' );
		} elseif ( $site == 'wpmlf' ) {
			update_option( 'njt_fb_updated_from_wpmlf', '1' );
		} elseif ( $site == 'wpmf' ) {
			update_option( 'njt_fb_updated_from_wpmf', '1' );
		} elseif ( $site == 'realmedia' ) {
			update_option( 'njt_fb_updated_from_realmedia', '1' );
		} elseif ( $site == 'happyfiles' ) {
			update_option( 'njt_fb_updated_from_happyfiles', '1' );
		} elseif ( $site == 'premio' ) {
			update_option( 'njt_fb_updated_from_premio', '1' );
		} elseif ( 'feml' === $site ) {
			update_option( 'njt_fb_updated_from_feml', '1' );
		} elseif ( $site == 'mediamatic' ) {
			update_option( 'njt_fb_updated_from_mediamatic', '1' );
		}
	}

	private function sanitize_arr( $arr ) {
		if ( is_array( $arr ) ) {
			return array_map( array( $this, 'sanitize_arr' ), $arr );
		} else {
			return sanitize_text_field( $arr );
		}
	}
}

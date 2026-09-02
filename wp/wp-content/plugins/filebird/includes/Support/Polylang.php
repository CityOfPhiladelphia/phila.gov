<?php
namespace FileBird\Support;

use FileBird\Model\Folder as FolderModel;
use FileBird\Controller\Controller;

defined( 'ABSPATH' ) || exit;

class Polylang extends Controller {
	private $active;
	private $lang;
	private $lang_id = null;

	public function __construct() {
		global $polylang;

		$this->active = function_exists( 'pll_get_post_translations' );
		if ( $this->active ) {
			if ( $polylang->options['media_support'] == 1 ) {
				$this->lang = PLL()->model->get_language( get_user_meta( get_current_user_id(), 'pll_filter_content', true ) );
				if ( $this->lang ) {
					$this->lang_id = $this->lang->term_id;
				}
				if ( $this->lang_id != null ) {
					add_filter( 'fbv_speedup_get_count_query', '__return_true' );
				}
				add_action( 'pll_translate_media', array( $this, 'duplicateAttachmentToFolder' ), 10, 3 );
				add_filter( 'fbv_get_count_query', array( $this, 'fbv_get_count_query' ), 10, 3 );
				add_filter( 'fbv_all_folders_and_count', array( $this, 'all_folders_and_count_query' ), 10, 2 );
				add_filter( 'fbv_pll_lang', array( $this, 'fbv_pll_lang' ), 10, 1 );
				add_filter( 'fbv_data', array( $this, 'fbv_data' ), 10, 1 );
			}
		}
	}

	public function fbv_data( $data ) {
		$data['icl_lang'] = null;
		return $data;
	}

	public function get_preferred_language( $lang ) {
		$pll_lang = PLL()->model->get_language( $lang );

		if ( $pll_lang ) {
			if ( version_compare( POLYLANG_VERSION, '3.4', '>=' ) ) {
				return $pll_lang->get_tax_prop( 'language', 'term_taxonomy_id' );
			} else {
				return $pll_lang->term_props['language']['term_taxonomy_id'];
			}
		}
		return $this->lang_id;
	}

	public function fbv_pll_lang( $lang ) {
		// Fixed for new polylang version
		// if ( ! PLL_SETTINGS && ! PLL_ADMIN && PLL()->model->get_languages_list() ) {
		// 	return true;
		// }
		return pll_current_language();
	}

	public function fbv_get_count_query( $q, $folder_id, $lang ) {
		$lang_id = $this->lang_id;

		if ( $lang ) {
			$lang_id = $this->get_preferred_language( $lang );
		}

		global $wpdb;
		if ( is_null( $lang_id ) ) {
			return $q;
		} else {
			if ( $folder_id == -1 ) {
				$q  = "SELECT COUNT(tmp.ID) FROM
        (   
            SELECT posts.ID
            FROM $wpdb->posts AS posts
            LEFT JOIN $wpdb->term_relationships AS trs 
            ON posts.ID = trs.object_id
            WHERE posts.post_type = 'attachment' 
            AND posts.ID NOT IN (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_elementor_is_screenshot') ";
				$q .= "AND trs.term_taxonomy_id IN ({$lang_id})";
				$q .= "AND (posts.post_status = 'inherit' OR posts.post_status = 'private')
            GROUP BY posts.ID
        ) as tmp";
			} else {
				if ( $folder_id > 0 ) {
					$q  = "SELECT COUNT(tmp.ID) FROM
          (   
              SELECT posts.ID
              FROM $wpdb->posts AS posts
              LEFT JOIN $wpdb->term_relationships AS trs ON posts.ID = trs.object_id
              RIGHT JOIN {$wpdb->prefix}fbv_attachment_folder as fbv ON (posts.ID = fbv.attachment_id)
              WHERE posts.post_type = 'attachment' 
              AND posts.ID NOT IN (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_elementor_is_screenshot') 
              AND fbv.folder_id = " . (int) $folder_id . ' ';
					$q .= "AND trs.term_taxonomy_id IN ({$lang_id}) ";
					$q .= "AND (posts.post_status = 'inherit' OR posts.post_status = 'private')
              GROUP BY posts.ID
          ) as tmp";
					// exit($q);
				}
			}
			return $q;
		}
	}

	public function all_folders_and_count_query( $query, $lang ) {
		global $wpdb;

		$lang_id = $this->lang_id;

		if ( $lang ) {
			$lang_id = $this->get_preferred_language( $lang );
		}

		$select = '';
		$join   = '';
		$where  = '';
		if ( is_null( $lang_id ) ) {
			$select = "SELECT fbva.folder_id as folder_id, count(DISTINCT(fbva.attachment_id)) as count
                  FROM {$wpdb->prefix}fbv_attachment_folder AS fbva";
		} else {
			$select = "SELECT fbva.folder_id as folder_id, count(fbva.attachment_id) as count
                  FROM {$wpdb->prefix}fbv_attachment_folder AS fbva";
			$join  .= " INNER JOIN {$wpdb->term_relationships} AS trs ON fbva.attachment_id = trs.object_id ";
			$where .= " AND trs.term_taxonomy_id IN ({$lang_id}) ";
		}

		$join .= " INNER JOIN {$wpdb->prefix}fbv as fbv ON fbv.id = fbva.folder_id ";
		$join .= " INNER JOIN {$wpdb->posts} as posts ON posts.ID = fbva.attachment_id ";

		$where .= " WHERE posts.post_type = 'attachment' AND (posts.post_status = 'inherit' OR posts.post_status = 'private') ";
		$where .= ' AND fbv.created_by = ' . apply_filters( 'fbv_in_not_in_created_by', '0' ) . ' GROUP BY fbva.folder_id';

		$query = $select . $join . $where;
		return $query;
	}

	public function duplicateAttachmentToFolder( $post_id, $tr_id, $lang_slug ) {
		$folders_of_source = FolderModel::getFoldersOfPost( $post_id );
		FolderModel::setFoldersForPosts( $tr_id, $folders_of_source );
	}
}
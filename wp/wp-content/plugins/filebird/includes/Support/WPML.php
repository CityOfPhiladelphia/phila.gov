<?php
namespace FileBird\Support;

use FileBird\Model\Folder as FolderModel;
use FileBird\Controller\Controller;

defined( 'ABSPATH' ) || exit;

class WPML extends Controller {
	protected $post_translations;
	private $sitepress;
	private $lang;
	private $wpdb;
	private $table_icl_translations;
	private $cpt_sync_options;

	public function __construct() {
		global $sitepress, $wpdb;
		if ( $sitepress === null || get_class( $sitepress ) !== 'SitePress' ) {
			return;
		}
		$this->sitepress              = $sitepress;
		$this->lang                   = $sitepress->get_current_language();
		$this->wpdb                   = $wpdb;
		$this->table_icl_translations = $wpdb->prefix . 'icl_translations';
		$this->post_translations      = $sitepress->post_translations();
		$this->cpt_sync_options       = $this->sitepress->get_setting( 'custom_posts_sync_option', array() );

		add_action( 'fbv_after_set_folder', array( $this, 'fbvAfterSetFolder' ), 10, 2 );
		add_filter( 'wpml_pre_parse_query', array( $this, 'preParseQuery' ) );
		add_filter( 'wpml_post_parse_query', array( $this, 'postParseQuery' ) );
		add_action( 'wp_ajax_fbv_sync_wpml', array( $this, 'syncWPML' ) );

		if ( ! isset( $this->cpt_sync_options['attachment'] ) || $this->cpt_sync_options['attachment'] != '0' ) {
			add_filter( 'fbv_in_not_in_query', array( $this, 'fbv_in_not_in_query' ), 10, 2 );
			add_filter( 'fbv_get_count_query', array( $this, 'fbv_get_count_query' ), 10, 3 );
			add_filter( 'fbv_speedup_get_count_query', '__return_true' );
			add_filter( 'fbv_all_folders_and_count', array( $this, 'all_folders_and_count_query' ), 10, 2 );
		}
	}

	public function syncWPML() {
		global $wpdb;

		check_ajax_referer( 'fbv_nonce', 'nonce', true );

		$translationNotInFolder = $wpdb->get_results(
			"SELECT GROUP_CONCAT( IF(fbv.folder_id is NULL, icl.element_id, NULL) ) as attachment_ids, GROUP_CONCAT(DISTINCT(fbv.folder_id)) as folder_id
			FROM `{$wpdb->prefix}icl_translations` icl
			LEFT JOIN `{$wpdb->prefix}fbv_attachment_folder` fbv 
			ON fbv.attachment_id = icl.element_id
			WHERE ( icl.element_type = 'post_attachment' )
			GROUP BY icl.trid
			HAVING ( COUNT(icl.element_id) > COUNT(fbv.folder_id) AND COUNT(fbv.folder_id) > 0 )"
        );

		foreach ( $translationNotInFolder as $translation ) {
			$elementIds = explode( ',', $translation->attachment_ids );
			$folderId   = intval( $translation->folder_id );

			foreach ( $elementIds as $elementId ) {
				FolderModel::setFoldersForPosts( $elementId, $folderId );
			}
		}

		return wp_send_json(
			array(
				'message' => __( 'Done!', 'filebird' ),
			)
		);
	}

	public function fbv_get_count_query( $q, $folder_id, $lang ) {
		global $wpdb;
		if ( $folder_id == -1 ) {
			$q = "SELECT COUNT(*) FROM {$wpdb->posts} 
            JOIN {$this->table_icl_translations} wpml_translations ON {$wpdb->posts}.ID = wpml_translations.element_id
            AND wpml_translations.element_type = CONCAT('post_', {$wpdb->posts}.post_type)
            WHERE 1=1
            AND {$wpdb->posts}.ID NOT IN 
            (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_elementor_is_screenshot')
            AND wpml_translations.element_type = 'post_attachment'
            AND (( {$wpdb->posts}.post_status = 'inherit' OR {$wpdb->posts}.post_status = 'private'))"; //->This query maybe not correct in grid mode

			if ( $lang == 'all' ) {
				$q .= $this->all_langs_where();
			} else {
				$where = $this->specific_lang_where( $lang );
				$q    .= $where;
			}
		} else {
			$q = "SELECT count(wpmlt.element_id) FROM {$this->table_icl_translations} AS wpmlt 
      INNER JOIN {$wpdb->posts} as posts ON posts.ID = wpmlt.element_id 
      INNER join {$wpdb->prefix}fbv_attachment_folder as fbvaf on wpmlt.element_id = fbvaf.attachment_id 
      WHERE 
      posts.ID NOT IN (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_elementor_is_screenshot') 
      AND (post_status = 'inherit' OR post_status = 'private') AND wpmlt.element_type = 'post_attachment' AND wpmlt.language_code = '{$this->lang}' AND fbvaf.folder_id = " . (int) $folder_id;
		}
		return $q;
	}
	public function fbv_in_not_in_query( $q, $fbv ) {
		global $wpdb;
		if ( $fbv == 0 ) {
			$q = "SELECT wpml_translations.element_id FROM {$this->table_icl_translations} AS wpml_translations 
      INNER JOIN {$wpdb->posts} as posts ON posts.ID = wpml_translations.element_id 
      INNER join {$wpdb->prefix}fbv_attachment_folder as fbvaf on wpml_translations.element_id = fbvaf.attachment_id 
      WHERE (post_status = 'inherit' OR post_status = 'private') AND wpml_translations.element_type = 'post_attachment'";
			if ( $this->lang == 'all' ) {
				$q .= $this->all_langs_where();
			} else {
				$q .= "AND wpml_translations.language_code IN ('$this->lang')";
			}
		} elseif ( is_array( $fbv ) ) {
			$q = "SELECT wpml_translations.element_id FROM {$this->table_icl_translations} AS wpml_translations 
      INNER JOIN {$wpdb->posts} as posts ON posts.ID = wpml_translations.element_id 
      INNER join {$wpdb->prefix}fbv_attachment_folder as fbvaf on wpml_translations.element_id = fbvaf.attachment_id 
      WHERE (post_status = 'inherit' OR post_status = 'private') AND wpml_translations.element_type = 'post_attachment'";
			if ( $this->lang == 'all' ) {
				$q .= $this->all_langs_where();
			} else {
				$q .= "AND wpml_translations.language_code IN ('$this->lang')";
			}
			$q .= 'AND fbvaf.folder_id IN (' . implode( ', ', $fbv ) . ')';
		}
		return $q;
	}
	public function all_folders_and_count_query( $query, $lang ) {
		global $wpdb;
		$query = "SELECT fbva.folder_id as folder_id, count(fbva.attachment_id) as count FROM {$wpdb->prefix}fbv_attachment_folder AS fbva 
    INNER JOIN {$wpdb->prefix}fbv as fbv ON fbv.id = fbva.folder_id 
    INNER JOIN {$this->table_icl_translations} AS wpml_translations ON fbva.attachment_id = wpml_translations.element_id 
    INNER JOIN {$wpdb->posts} ON {$wpdb->posts}.ID = fbva.attachment_id 
    WHERE ({$wpdb->posts}.post_status = 'inherit' OR {$wpdb->posts}.post_status = 'private') AND wpml_translations.element_type = 'post_attachment'";
		if ( $lang == 'all' ) {
			$query .= $this->all_langs_where();
		} else {
			$where  = $this->specific_lang_where( $lang );
			$query .= $where;
		}
		$query .= 'AND fbv.created_by = ' . apply_filters( 'fbv_in_not_in_created_by', '0' ) . ' GROUP BY fbva.folder_id';

		return $query;
	}
	public function fbvAfterSetFolder( $id, $folder ) {
		global $wpdb;
		$cpt_sync_options = $this->sitepress->get_setting( 'custom_posts_sync_option', array() );
		if ( isset( $cpt_sync_options['attachment'] ) && $cpt_sync_options['attachment'] == '0' ) {
			return;
		}
		$post              = get_post( $id );
		$post_type         = $post->post_type;
		$post_trid         = $this->sitepress->get_element_trid( $id, 'post_' . $post_type );
		$post_translations = $this->sitepress->get_element_translations( $post_trid, 'post_' . $post_type );

		foreach ( $post_translations as $post_language => $translated_post ) {
			$translated_post_id = $translated_post->element_id;
			if ( ! $translated_post_id ) {
				continue;
			}
			FolderModel::setFoldersForPosts( $translated_post_id, (int) $folder, false );
		}
	}
	public function filterInNotIn( $query ) {
		$query = $this->adjust_q_var_pids( $query, 'post__not_in' );
		$query = $this->adjust_q_var_pids( $query, 'post__in' );
		return $query;
	}
	public function preParseQuery( $q ) {
		if ( ! empty( $q->query_vars['post_type'] ) && $q->query_vars['post_type'] == 'attachment' ) {
			$cpt_sync_options = $this->sitepress->get_setting( 'custom_posts_sync_option', array() );
			if ( isset( $cpt_sync_options['attachment'] ) && $cpt_sync_options['attachment'] == '0' ) {
				$q->query_vars['fbv_backup_post__in']     = $q->query_vars['post__in'];
				$q->query_vars['fbv_backup_post__not_in'] = $q->query_vars['post__not_in'];
				$q->query_vars['post__in']                = array();
				$q->query_vars['post__not_in']            = array();
			}
		}
		return $q;
	}
	public function postParseQuery( $q ) {
		if ( ! empty( $q->query_vars['post_type'] ) && $q->query_vars['post_type'] == 'attachment' ) {
			$cpt_sync_options = $this->sitepress->get_setting( 'custom_posts_sync_option', array() );
			if ( isset( $cpt_sync_options['attachment'] ) && $cpt_sync_options['attachment'] == '0' ) {
				$q->query_vars['post__in']     = $q->query_vars['fbv_backup_post__in'];
				$q->query_vars['post__not_in'] = $q->query_vars['fbv_backup_post__not_in'];
				unset( $q->query_vars['fbv_backup_post__in'] );
				unset( $q->query_vars['fbv_backup_post__not_in'] );
			}
		}
		return $q;
	}
	private function adjust_q_var_pids( $q, $index ) {
		if ( ! empty( $q[ $index ] ) ) {

			$untranslated = $q[ $index ];
			$this->post_translations->prefetch_ids( $untranslated );
			$current_lang = $this->sitepress->get_current_language();
			$pid          = array();
			foreach ( $q[ $index ] as $p ) {
				$pid[] = $this->post_translations->element_id_in( $p, $current_lang, true );
			}
			$q[ $index ] = $pid;
		}

		return $q;
	}
	public function countArgs( $args ) {
		$args['suppress_filters'] = false;
		return $args;
	}

	public function all_langs_where() {
		return ' AND wpml_translations.language_code IN (' . \wpml_prepare_in( array_keys( $this->sitepress->get_active_languages() ) ) . ') ';
	}

	public function specific_lang_where( $lang ) {
		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
		$default_language = $this->sitepress->get_default_language();
		$current_language = $lang;
		return $this->wpdb->prepare(
			' AND ( ( ( wpml_translations.language_code = %s OR '
			. $this->display_as_translated_snippet( $current_language, $default_language )
			. ' ) AND '
			. $this->in_translated_types_snippet()
			. ' ) OR ' . $this->in_translated_types_snippet( true ) . ' )',
			$current_language
		);
	}

	public function display_as_translated_snippet( $current_language, $fallback_language ) {
		$content_types      = null;
		$skip_content_check = true;

		if ( ! apply_filters( 'wpml_should_force_display_as_translated_snippet', false ) ) {
			$post_types = $this->sitepress->get_display_as_translated_documents();
			if ( ! $post_types || ! apply_filters( 'wpml_should_use_display_as_translated_snippet', ! is_admin(), $post_types ) ) {
				return '0';
			}
			$content_types      = array_keys( $post_types );
			$skip_content_check = false;
		}

		$display_as_translated_query = new \WPML_Display_As_Translated_Posts_Query( $this->wpdb );

		return $display_as_translated_query->get_language_snippet( $current_language, $fallback_language, $content_types, $skip_content_check );
	}

	public function in_translated_types_snippet( $not = false, $posts_alias = false ) {
		$not         = $not ? ' NOT ' : '';
		$posts_alias = $posts_alias ? $posts_alias : $this->wpdb->posts;

		$post_types = $this->sitepress->get_translatable_documents( false );
		if ( $post_types ) {
			return "{$posts_alias}.post_type {$not} IN (" . wpml_prepare_in( array_keys( $post_types ) ) . ' ) ';
		} else {
			return '';
		}
	}
}
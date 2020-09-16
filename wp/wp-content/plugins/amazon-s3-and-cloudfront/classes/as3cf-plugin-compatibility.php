<?php
/**
 * Plugin Compatibility
 *
 * @package     amazon-s3-and-cloudfront
 * @subpackage  Classes/Plugin-Compatibility
 * @copyright   Copyright (c) 2015, Delicious Brains
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.8.3
 */

use DeliciousBrains\WP_Offload_Media\Items\Media_Library_Item;
use DeliciousBrains\WP_Offload_Media\Providers\Storage\Storage_Provider;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AS3CF_Plugin_Compatibility Class
 *
 * This class handles compatibility code for third party plugins used in conjunction with AS3CF
 *
 * @since 0.8.3
 */
class AS3CF_Plugin_Compatibility {

	/**
	 * @var Amazon_S3_And_CloudFront
	 */
	protected $as3cf;

	/**
	 * @var array
	 */
	protected static $stream_wrappers = array();

	/**
	 * @var array
	 */
	protected $compatibility_addons;

	/**
	 * @var array
	 */
	private $removed_files = array();

	/**
	 * @param Amazon_S3_And_CloudFront $as3cf
	 */
	function __construct( $as3cf ) {
		$this->as3cf = $as3cf;

		$this->compatibility_init();
	}

	/**
	 * Register the compatibility hooks for the plugin.
	 */
	function compatibility_init() {
		/*
		 * WP_Customize_Control
		 * /wp-includes/class-wp-customize_control.php
		 */
		add_filter( 'attachment_url_to_postid', array( $this, 'customizer_background_image' ), 10, 2 );

		/*
		 * Responsive Images WP 4.4
		 */
		add_filter( 'wp_get_attachment_metadata', array( $this, 'wp_get_attachment_metadata' ), 10, 2 );
		add_filter( 'wp_calculate_image_srcset', array( $this, 'wp_calculate_image_srcset' ), 10, 5 );
		add_filter( 'wp_calculate_image_srcset_meta', array( $this, 'wp_calculate_image_srcset_meta' ), 10, 4 );

		// Maybe warn about PHP version if in admin screens.
		add_action( 'admin_init', array( $this, 'maybe_warn_about_php_version' ) );

		// WordPress MU Domain Mapping plugin compatibility.
		add_filter( 'as3cf_get_orig_siteurl', array( $this, 'get_orig_siteurl' ) );

		if ( $this->as3cf->is_plugin_setup( true ) ) {
			$this->compatibility_init_if_setup();
		}
	}

	/**
	 * Register the compatibility hooks as long as the plugin is setup.
	 */
	function compatibility_init_if_setup() {
		// Turn on stream wrapper S3 file
		add_filter( 'as3cf_get_attached_file', array( $this, 'get_stream_wrapper_file' ), 20, 4 );

		/*
		 * Legacy filter
		 * 'as3cf_get_attached_file_copy_back_to_local'
		 */
		add_filter( 'as3cf_get_attached_file', array( $this, 'legacy_copy_back_to_local' ), 10, 4 );

		/*
		 * WP_Image_Editor
		 * /wp-includes/class-wp-image-editor.php
		 */
		add_action( 'as3cf_pre_upload_attachment', array( $this, 'image_editor_remove_files' ), 10, 3 );
		add_filter( 'as3cf_get_attached_file_noop', array( $this, 'image_editor_download_file' ), 10, 4 );
		add_filter( 'as3cf_get_attached_file', array( $this, 'image_editor_download_file' ), 10, 4 );
		add_filter( 'as3cf_upload_attachment_local_files_to_remove', array( $this, 'image_editor_remove_original_image' ), 10, 3 );
		add_filter( 'as3cf_get_attached_file', array( $this, 'customizer_crop_download_file' ), 10, 4 );
		add_filter( 'as3cf_upload_attachment_local_files_to_remove', array( $this, 'customizer_crop_remove_original_image' ), 10, 3 );
		add_filter( 'wp_unique_filename', array( $this, 'customizer_crop_unique_filename' ), 10, 3 );

		/*
		 * Regenerate Thumbnails (before v3)
		 * https://wordpress.org/plugins/regenerate-thumbnails/
		 */
		add_filter( 'as3cf_get_attached_file', array( $this, 'regenerate_thumbnails_download_file' ), 10, 4 );

		/**
		 * Regenerate Thumbnails v3+ and other REST-API using plugins that need a local file.
		 */
		add_filter( 'rest_dispatch_request', array( $this, 'rest_dispatch_request_copy_back_to_local' ), 10, 4 );

		/*
		 * WP-CLI Compatibility
		 */
		if ( defined( 'WP_CLI' ) && class_exists( 'WP_CLI' ) ) {
			WP_CLI::add_hook( 'before_invoke:media regenerate', array( $this, 'enable_get_attached_file_copy_back_to_local' ) );
		}
	}

	/**
	 * Allow any process to trigger the copy back to local with
	 * the filter 'as3cf_get_attached_file_copy_back_to_local'
	 *
	 * @param string             $url
	 * @param string             $file
	 * @param int                $attachment_id
	 * @param Media_Library_Item $as3cf_item
	 *
	 * @return string
	 */
	function legacy_copy_back_to_local( $url, $file, $attachment_id, Media_Library_Item $as3cf_item ) {
		$copy_back_to_local = apply_filters( 'as3cf_get_attached_file_copy_back_to_local', false, $file, $attachment_id, $as3cf_item );
		if ( false === $copy_back_to_local ) {
			// Not copying back file
			return $url;
		}

		if ( ( $file = $this->copy_provider_file_to_server( $as3cf_item, $file ) ) ) {
			// Return the file if successfully downloaded from S3
			return $file;
		};

		// Return S3 URL as a fallback
		return $url;
	}

	/**
	 * Enables copying missing local files back to the server when `get_attached_file` filter is called.
	 */
	public function enable_get_attached_file_copy_back_to_local() {
		add_filter( 'as3cf_get_attached_file_copy_back_to_local', '__return_true' );

		// Monitor any files that are subsequently removed.
		add_filter( 'as3cf_upload_attachment_local_files_to_remove', array(
			$this,
			'monitor_local_files_to_remove',
		), 10, 3 );

		// Prevent subsequent attempts to copy back after upload and remove.
		add_filter( 'as3cf_get_attached_file_copy_back_to_local', array(
			$this,
			'prevent_copy_back_to_local_after_remove',
		), 10, 4 );
	}

	/**
	 * Keeps track of local files that are removed after upload.
	 *
	 * @param array   $files_to_remove
	 * @param integer $post_id
	 * @param string  $file_path
	 *
	 * @return array
	 */
	public function monitor_local_files_to_remove( $files_to_remove, $post_id, $file_path ) {
		$this->removed_files = array_merge( $this->removed_files, $files_to_remove );

		return $files_to_remove;
	}

	/**
	 * Prevent subsequent attempts to copy back after upload and remove.
	 *
	 * @param bool               $copy_back_to_local
	 * @param string             $file
	 * @param integer            $attachment_id
	 * @param Media_Library_Item $as3cf_item
	 *
	 * @return bool
	 */
	public function prevent_copy_back_to_local_after_remove( $copy_back_to_local, $file, $attachment_id, Media_Library_Item $as3cf_item ) {
		if ( $copy_back_to_local && in_array( $file, $this->removed_files ) ) {
			$copy_back_to_local = false;
		}

		return $copy_back_to_local;
	}

	/**
	 * Is this an AJAX process?
	 *
	 * @return bool
	 */
	function is_ajax() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return true;
		}

		return false;
	}

	/**
	 * Check the current request is a specific one based on action and
	 * optional context
	 *
	 * @param string            $action_key
	 * @param bool              $ajax
	 * @param null|string|array $context_key
	 *
	 * @return bool
	 */
	function maybe_process_on_action( $action_key, $ajax, $context_key = null ) {
		if ( $ajax !== $this->is_ajax() ) {
			return false;
		}

		$var_type = 'GET';

		if ( isset( $_GET['action'] ) ) {
			$action = $this->as3cf->filter_input( 'action' );
		} else if ( isset( $_POST['action'] ) ) {
			$var_type = 'POST';
			$action   = $this->as3cf->filter_input( 'action', INPUT_POST );
		} else {
			return false;
		}

		$context_check = true;
		if ( ! is_null( $context_key ) ) {
			$global  = constant( 'INPUT_' . $var_type );
			$context = $this->as3cf->filter_input( 'context', $global );

			if ( is_array( $context_key ) ) {
				$context_check = in_array( $context, $context_key );
			} else {
				$context_check = ( $context_key === $context );
			}
		}

		return ( $action_key === sanitize_key( $action ) && $context_check );
	}

	/**
	 * Generic method for copying back an S3 file to the server on a specific AJAX action
	 *
	 * @param string             $action_key Action that must be in process
	 * @param bool               $ajax       Must the process be an AJAX one?
	 * @param string             $url        S3 URL
	 * @param string             $file       Local file path of image
	 * @param Media_Library_Item $as3cf_item S3 meta data
	 *
	 * @return string
	 */
	function copy_image_to_server_on_action( $action_key, $ajax, $url, $file, Media_Library_Item $as3cf_item ) {
		if ( false === $this->maybe_process_on_action( $action_key, $ajax ) ) {
			return $url;
		}

		if ( ( $file = $this->copy_provider_file_to_server( $as3cf_item, $file ) ) ) {
			// Return the file if successfully downloaded from S3
			return $file;
		};

		return $url;
	}

	/**
	 * Get the file path of the original image file before an update
	 *
	 * @param int    $post_id
	 * @param string $file_path
	 *
	 * @return bool|string
	 */
	function get_original_image_file( $post_id, $file_path ) {
		// remove original main image after edit
		$meta          = get_post_meta( $post_id, '_wp_attachment_metadata', true );
		$original_file = trailingslashit( dirname( $file_path ) ) . wp_basename( $meta['file'] );
		if ( file_exists( $original_file ) ) {
			return $original_file;
		}

		return false;
	}

	/**
	 * Allow the WordPress Image Editor to remove edited version of images
	 * if the original image is being restored and 'IMAGE_EDIT_OVERWRITE' is set
	 *
	 * @param bool  $pre
	 * @param int   $post_id
	 * @param array $data
	 *
	 * @return bool
	 */
	public function image_editor_remove_files( $pre, $post_id, $data ) {
		if ( ! isset( $_POST['do'] ) || 'restore' !== $_POST['do'] ) {
			return $pre;
		}

		if ( ! defined( 'IMAGE_EDIT_OVERWRITE' ) || ! IMAGE_EDIT_OVERWRITE ) {
			return $pre;
		}

		$as3cf_item = Media_Library_Item::get_by_source_id( $post_id );

		if ( ! $as3cf_item ) {
			return $pre;
		}

		$this->remove_edited_image_files( $post_id, $as3cf_item );

		// Update object key with original filename
		$restored_filename = wp_basename( $data['file'] );
		$old_filename      = wp_basename( $as3cf_item->path() );
		$item_path         = str_replace( $old_filename, $restored_filename, $as3cf_item->path() );

		$as3cf_item = new Media_Library_Item(
			$as3cf_item->provider(),
			$as3cf_item->region(),
			$as3cf_item->bucket(),
			$item_path,
			$as3cf_item->is_private(),
			$as3cf_item->source_id(),
			$as3cf_item->source_path(),
			wp_basename( $as3cf_item->original_source_path() ),
			$as3cf_item->extra_info(),
			$as3cf_item->id()
		);

		$as3cf_item->save();

		return true;
	}

	/**
	 * Remove edited image files from S3.
	 *
	 * @param int                $attachment_id
	 * @param Media_Library_Item $as3cf_item
	 */
	protected function remove_edited_image_files( $attachment_id, Media_Library_Item $as3cf_item ) {
		$keys = AS3CF_Utils::get_attachment_edited_keys( $attachment_id, $as3cf_item );

		if ( empty( $keys ) ) {
			return;
		}

		$this->as3cf->delete_objects( $as3cf_item->region(), $as3cf_item->bucket(), $keys );
	}

	/**
	 * Allow the WordPress Image Editor to edit files that have been copied to provider
	 * but removed from the local server, by copying them back temporarily
	 *
	 * @param string             $url
	 * @param string             $file
	 * @param int                $attachment_id
	 * @param Media_Library_Item $as3cf_item
	 *
	 * @return string
	 */
	function image_editor_download_file( $url, $file, $attachment_id, Media_Library_Item $as3cf_item ) {
		if ( ! $this->is_ajax() ) {
			return $url;
		}

		// When the image-editor restores the original it requests the edited image,
		// but we actually need to copy back the original image at this point
		// for the restore to be successful and edited images to be deleted from the bucket
		// via image_editor_remove_files()
		if ( isset( $_POST['do'] ) && 'restore' == $_POST['do'] ) {
			$backup_sizes      = get_post_meta( $attachment_id, '_wp_attachment_backup_sizes', true );
			$original_filename = $backup_sizes['full-orig']['file'];

			$as3cf_item_orig = new Media_Library_Item(
				$as3cf_item->provider(),
				$as3cf_item->region(),
				$as3cf_item->bucket(),
				$as3cf_item->normalized_path_dir() . $original_filename,
				$as3cf_item->is_private(),
				$as3cf_item->source_id(),
				$as3cf_item->source_path(),
				wp_basename( $as3cf_item->original_source_path() ),
				$as3cf_item->extra_info(),
				$as3cf_item->id()
			);
			$orig_file       = dirname( $file ) . '/' . $original_filename;

			// Copy the original file back to the server for the restore process
			$this->copy_provider_file_to_server( $as3cf_item_orig, $orig_file );

			// Copy the edited file back to the server as well, it will be cleaned up later
			if ( $provider_file = $this->copy_provider_file_to_server( $as3cf_item, $file ) ) {
				// Return the file if successfully downloaded from bucket.
				return $provider_file;
			}
		}

		$action = filter_input( INPUT_GET, 'action' ) ?: filter_input( INPUT_POST, 'action' );

		if ( in_array( $action, array( 'image-editor', 'imgedit-preview' ) ) ) { // input var okay
			foreach ( debug_backtrace() as $caller ) {
				if ( isset( $caller['function'] ) && '_load_image_to_edit_path' == $caller['function'] ) {
					// check this has been called by '_load_image_to_edit_path' so as only to copy back once
					if ( $provider_file = $this->copy_provider_file_to_server( $as3cf_item, $file ) ) {
						// Return the file if successfully downloaded from bucket.
						return $provider_file;
					}
				}
			}
		}

		return $url;
	}

	/**
	 * Allow the WordPress Image Editor to remove the main image file after it has been copied
	 * back from S3 after it has done the edit.
	 *
	 * @param array  $files
	 * @param int    $post_id
	 * @param string $file_path
	 *
	 * @return array
	 */
	function image_editor_remove_original_image( $files, $post_id, $file_path ) {
		if ( ! $this->is_ajax() ) {
			return $files;
		}

		if ( isset( $_POST['action'] ) && 'image-editor' === sanitize_key( $_POST['action'] ) ) { // input var okay
			// remove original main image after edit
			if ( ( $original_file = $this->get_original_image_file( $post_id, $file_path ) ) ) {
				$files[] = $original_file;
			}
		}

		return $files;
	}

	/**
	 * Generic check for Customizer crop actions
	 *
	 * @return bool
	 */
	public function is_customizer_crop_action() {
		$header_crop = $this->maybe_process_on_action( 'custom-header-crop', true );

		$context    = array( 'site-icon', 'custom_logo' );
		$image_crop = $this->maybe_process_on_action( 'crop-image', true, $context );

		if ( ! $header_crop && ! $image_crop ) {
			// Not doing a Customizer action
			return false;
		}

		return true;
	}

	/**
	 * Allow the WordPress Customizer to crop images that have been copied to bucket
	 * but removed from the local server, by copying them back temporarily.
	 *
	 * @param string             $url
	 * @param string             $file
	 * @param int                $attachment_id
	 * @param Media_Library_Item $as3cf_item
	 *
	 * @return string
	 */
	public function customizer_crop_download_file( $url, $file, $attachment_id, Media_Library_Item $as3cf_item ) {
		if ( false === $this->is_customizer_crop_action() ) {
			return $url;
		}

		if ( $this->as3cf->attachment_just_uploaded( $attachment_id ) ) {
			return $url;
		}

		if ( ( $file = $this->copy_provider_file_to_server( $as3cf_item, $file ) ) ) {
			// Return the file if successfully downloaded from bucket.
			return $file;
		};

		return $url;
	}

	/**
	 * Allow the WordPress Image Editor to remove the main image file after it has been copied
	 * back from S3 after it has done the edit.
	 *
	 * @param array  $files
	 * @param int    $post_id
	 * @param string $file_path
	 *
	 * @return array
	 */
	function customizer_crop_remove_original_image( $files, $post_id, $file_path ) {
		if ( false === $this->is_customizer_crop_action() ) {
			return $files;
		}

		// remove original main image after edit
		if ( ( $original_file = $this->get_original_image_file( $_POST['id'], $file_path ) ) ) {
			$files[] = $original_file;
		}

		return $files;
	}

	/**
	 * Show the correct background image in the customizer
	 *
	 * @param int|null $post_id
	 * @param string   $url
	 *
	 * @return int|null
	 */
	function customizer_background_image( $post_id, $url ) {
		if ( ! is_null( $post_id ) ) {
			return $post_id;
		}

		// There seems to be a bug in the WP Customizer whereby sometimes it puts the attachment ID on the URL.
		if ( is_numeric( $url ) ) {
			$as3cf_item = Media_Library_Item::get_by_source_id( $url );

			// If we found an offloaded Media Library item for that ID, job's a good'n'.
			if ( $as3cf_item ) {
				$post_id = $url;
			}
		} else {
			$post_id = $this->as3cf->filter_provider->get_attachment_id_from_url( $url );
		}

		// Must return null if not found.
		if ( empty( $post_id ) ) {
			return null;
		} else {
			return $post_id;
		}
	}

	/**
	 * Allow the Regenerate Thumbnails plugin to copy the bucket file back to the local
	 * server when the file is missing on the server via get_attached_file.
	 *
	 * @param string             $url
	 * @param string             $file
	 * @param int                $attachment_id
	 * @param Media_Library_Item $as3cf_item
	 *
	 * @return string
	 */
	function regenerate_thumbnails_download_file( $url, $file, $attachment_id, Media_Library_Item $as3cf_item ) {
		return $this->copy_image_to_server_on_action( 'regeneratethumbnail', true, $url, $file, $as3cf_item );
	}

	/**
	 * Download a file from bucket if the file does not exist locally and places it where
	 * the attachment's file should be.
	 *
	 * @param Media_Library_Item $as3cf_item
	 * @param string             $file
	 *
	 * @return string|bool File if downloaded, false on failure
	 */
	public function copy_provider_file_to_server( Media_Library_Item $as3cf_item, $file ) {
		$filename = wp_basename( $file );

		// Make sure the directory exists
		$dir = dirname( $file );
		if ( ! wp_mkdir_p( $dir ) ) {
			$error_message = sprintf( __( 'The local directory %s does not exist and could not be created.', 'amazon-s3-and-cloudfront' ), $dir );
			AS3CF_Error::log( sprintf( __( 'There was an error attempting to download the file %s from the bucket: %s', 'amazon-s3-and-cloudfront' ), $as3cf_item->key( $filename ), $error_message ) );

			return false;
		}

		try {
			$this->as3cf->get_provider_client( $as3cf_item->region(), true )->get_object( array(
				'Bucket' => $as3cf_item->bucket(),
				'Key'    => $as3cf_item->key( $filename ),
				'SaveAs' => $file,
			) );
		} catch ( Exception $e ) {
			AS3CF_Error::log( sprintf( __( 'There was an error attempting to download the file %s from the bucket: %s', 'amazon-s3-and-cloudfront' ), $as3cf_item->key( $filename ), $e->getMessage() ) );

			return false;
		}

		return $file;
	}

	/**
	 * Register stream wrappers per region
	 *
	 * @param string $region
	 *
	 * @return Storage_Provider|null
	 * @throws Exception
	 */
	protected function register_stream_wrapper( $region ) {
		$stored_region = ( '' === $region ) ? $this->as3cf->get_default_region() : $region;

		if ( ! empty( self::$stream_wrappers[ $stored_region ] ) ) {
			return self::$stream_wrappers[ $stored_region ];
		}

		$client = $this->as3cf->get_provider_client( $region, true );

		if ( ! empty( $client ) && $client->register_stream_wrapper( $region ) ) {
			self::$stream_wrappers[ $stored_region ] = $client;

			return $client;
		}

		return null;
	}

	/**
	 * Allow access to the remote file via the stream wrapper.
	 * This is useful for compatibility with plugins when attachments are removed from the local server after upload.
	 *
	 * @param string             $url
	 * @param string             $file
	 * @param int                $attachment_id
	 * @param Media_Library_Item $as3cf_item
	 *
	 * @return string
	 * @throws Exception
	 */
	public function get_stream_wrapper_file( $url, $file, $attachment_id, Media_Library_Item $as3cf_item ) {
		if ( $url === $file ) {
			// Abort if an earlier hook to get the file has been called and it has been copied back.
			return $file;
		}

		// Make sure the region stream wrapper is registered.
		$client = $this->register_stream_wrapper( $as3cf_item->region() );

		if ( ! empty( $client ) ) {
			return $client->prepare_stream_wrapper_file( $as3cf_item->region(), $as3cf_item->bucket(), $as3cf_item->key() );
		}

		return $url;
	}

	/**
	 * Fixes comparison of attachment metadata to already urlencoded content during 'the_content' filter.
	 *
	 * @param array $data
	 * @param int   $attachment_id
	 *
	 * @return array
	 */
	public function wp_get_attachment_metadata( $data, $attachment_id ) {
		global $wp_current_filter;

		if (
			is_array( $wp_current_filter ) &&
			! empty( $wp_current_filter[0] ) &&
			'the_content' === $wp_current_filter[0] &&
			$this->as3cf->is_attachment_served_by_provider( $attachment_id )
		) {
			// Ensure each filename is encoded the same way as URL, slightly fixed up for wp_basename() manipulation compatibility.
			if ( ! empty( $data['file'] ) ) {
				$data['file'] = AS3CF_Utils::encode_filename_in_path( $data['file'] );
			}

			if ( ! empty( $data['sizes'] ) ) {
				$data['sizes'] = array_map( function ( $size ) {
					$size['file'] = AS3CF_Utils::encode_filename_in_path( $size['file'] );

					return $size;
				}, $data['sizes'] );
			}
		}

		return $data;
	}

	/**
	 * Adds 'srcset' and 'sizes' attributes to an existing S3 'img' element.
	 *
	 * @param string $image         An HTML 'img' element to be filtered.
	 * @param array  $image_meta    The image meta data as returned by 'wp_get_attachment_metadata()'.
	 * @param int    $attachment_id Image attachment ID.
	 *
	 * @return string Converted 'img' element with 'srcset' and 'sizes' attributes added.
	 */
	public function wp_image_add_srcset_and_sizes( $image, $image_meta, $attachment_id ) {
		// Ensure the image meta exists.
		if ( empty( $image_meta['sizes'] ) ) {
			return $image;
		}

		if ( ! is_string( $image ) ) {
			return $image;
		}

		$image_src = preg_match( '/src="([^"]+)"/', $image, $match_src ) ? $match_src[1] : '';
		list( $image_src ) = explode( '?', $image_src );

		// Return early if we couldn't get the image source.
		if ( ! $image_src ) {
			return $image;
		}

		// Bail early if an image has been inserted and later edited.
		if ( preg_match( '/-e[0-9]{13}/', $image_meta['file'], $img_edit_hash ) && strpos( wp_basename( $image_src ), $img_edit_hash[0] ) === false ) {

			return $image;
		}

		$width  = preg_match( '/ width="([0-9]+)"/', $image, $match_width ) ? (int) $match_width[1] : 0;
		$height = preg_match( '/ height="([0-9]+)"/', $image, $match_height ) ? (int) $match_height[1] : 0;

		if ( ! $width || ! $height ) {
			/*
			 * If attempts to parse the size value failed, attempt to use the image meta data to match
			 * the image file name from 'src' against the available sizes for an attachment.
			 */
			$image_filename = wp_basename( $image_src );

			if ( $image_filename === wp_basename( $image_meta['file'] ) ) {
				$width  = (int) $image_meta['width'];
				$height = (int) $image_meta['height'];
			} else {
				foreach ( $image_meta['sizes'] as $image_size_data ) {
					if ( $image_filename === $image_size_data['file'] ) {
						$width  = (int) $image_size_data['width'];
						$height = (int) $image_size_data['height'];
						break;
					}
				}
			}
		}

		if ( ! $width || ! $height ) {
			return $image;
		}

		$size_array = array( $width, $height );
		$srcset     = wp_calculate_image_srcset( $size_array, $image_src, $image_meta, $attachment_id );
		$sizes      = false;

		if ( $srcset ) {
			// Check if there is already a 'sizes' attribute.
			$sizes = strpos( $image, ' sizes=' );

			if ( ! $sizes ) {
				$sizes = wp_calculate_image_sizes( $size_array, $image_src, $image_meta, $attachment_id );
			}
		}

		if ( $srcset && $sizes ) {
			// Format the 'srcset' and 'sizes' string and escape attributes.
			$attr = sprintf( ' srcset="%s"', esc_attr( $srcset ) );

			if ( is_string( $sizes ) ) {
				$attr .= sprintf( ' sizes="%s"', esc_attr( $sizes ) );
			}

			// Add 'srcset' and 'sizes' attributes to the image markup.
			$image = preg_replace( '/<img ([^>]+?)[\/ ]*>/', '<img $1' . $attr . ' />', $image );
		}

		return $image;
	}

	/**
	 * Alter the image meta data to add srcset support for object versioned provider URLs.
	 *
	 * @param array  $image_meta
	 * @param array  $size_array
	 * @param string $image_src
	 * @param int    $attachment_id
	 *
	 * @return array
	 */
	public function wp_calculate_image_srcset_meta( $image_meta, $size_array, $image_src, $attachment_id ) {
		if ( empty( $image_meta['file'] ) ) {
			// Corrupt `_wp_attachment_metadata`
			return $image_meta;
		}

		if ( false !== strpos( $image_src, $image_meta['file'] ) ) {
			// Path matches URL, no need to change
			return $image_meta;
		}

		if ( ! ( $as3cf_item = $this->as3cf->is_attachment_served_by_provider( $attachment_id ) ) ) {
			// Attachment not uploaded to S3, abort
			return $image_meta;
		}

		$image_basename = AS3CF_Utils::encode_filename_in_path( wp_basename( $image_meta['file'] ) );

		if ( false === strpos( AS3CF_Utils::encode_filename_in_path( $as3cf_item->path() ), $image_basename ) ) {
			// Not the correct attachment, abort
			return $image_meta;
		}

		// Strip the meta file prefix so that just the filename will always match
		// the S3 URL regardless of different prefixes for the offloaded file.
		// Also ensure filename is encoded the same way as URL.
		$image_meta['file'] = $image_basename;

		// Ensure each size filename is encoded the same way as URL.
		if ( ! empty( $image_meta['sizes'] ) ) {
			$image_meta['sizes'] = array_map( function ( $size ) {
				$size['file'] = AS3CF_Utils::encode_filename_in_path( $size['file'] );

				return $size;
			}, $image_meta['sizes'] );
		}

		return $image_meta;
	}

	/**
	 * Replace local URLs with provider ones for srcset image sources.
	 *
	 * @param array  $sources
	 * @param array  $size_array
	 * @param string $image_src
	 * @param array  $image_meta
	 * @param int    $attachment_id
	 *
	 * @return array
	 */
	public function wp_calculate_image_srcset( $sources, $size_array, $image_src, $image_meta, $attachment_id ) {
		if ( ! is_array( $sources ) ) {
			// Sources corrupt
			return $sources;
		}

		if ( ! ( $as3cf_item = $this->as3cf->is_attachment_served_by_provider( $attachment_id ) ) ) {
			// Attachment not uploaded to S3, abort
			return $sources;
		}

		foreach ( $sources as $width => $source ) {
			$filename     = wp_basename( $source['url'] );
			$size         = $this->find_image_size_from_width( $image_meta['sizes'], $width, $filename );
			$provider_url = $this->as3cf->get_attachment_provider_url( $attachment_id, $as3cf_item, null, $size, $image_meta );

			if ( false === $provider_url || is_wp_error( $provider_url ) ) {
				// Skip URLs not offloaded to S3
				continue;
			}

			$sources[ $width ]['url'] = $provider_url;
		}

		return $sources;
	}

	/**
	 * Helper function to find size name from width and filename
	 *
	 * @param array  $sizes
	 * @param string $width
	 * @param string $filename
	 *
	 * @return null|string
	 */
	protected function find_image_size_from_width( $sizes, $width, $filename ) {
		foreach ( $sizes as $name => $size ) {
			if ( $width === absint( $size['width'] ) && $size['file'] === $filename ) {
				return $name;
			}
		}

		return null;
	}

	/**
	 * Filters the result when generating a unique file name for a customizer crop.
	 *
	 * @param string $filename Unique file name.
	 * @param string $ext      File extension, eg. ".png".
	 * @param string $dir      Directory path.
	 *
	 * @return string
	 */
	public function customizer_crop_unique_filename( $filename, $ext, $dir ) {
		if ( false === $this->is_customizer_crop_action() ) {
			return $filename;
		}

		// Get parent Post ID for cropped image.
		$post_id = filter_input( INPUT_POST, 'id', FILTER_VALIDATE_INT );

		$filename = $this->as3cf->filter_unique_filename( $filename, $ext, $dir, $post_id );

		return $filename;
	}

	/**
	 * Display an admin message if PHP version is soon to be unsupported by plugin.
	 *
	 * NOTE: This is not added to AWS SDK compatibility checks as it is remaining compatible with earlier PHP versions.
	 * This function should be removed or reworked once PHP 5.5 is required.
	 */
	public function maybe_warn_about_php_version() {
		$key_base = 'php-version-55';

		if ( version_compare( PHP_VERSION, '5.5', '<' ) ) {
			$message = sprintf(
				__( '<strong>Warning:</strong> This site is using PHP %1$s, in a future update WP Offload Media will require PHP %2$s or later. %3$s', 'amazon-s3-and-cloudfront' ),
				PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION,
				'5.5',
				$this->as3cf->more_info_link( '/wp-offload-media/doc/php-version-requirements/', 'upgrade-php-version' )
			);

			// Show notice to user if not already dismissed.
			$args = array(
				'custom_id'         => $key_base . '-site',
				'type'              => 'notice-warning',
				'flash'             => false,
				'only_show_to_user' => false,
			);

			if ( ! in_array( $args['custom_id'], $this->as3cf->notices->get_dismissed_notices() ) ) {
				$this->as3cf->notices->add_notice( $message, $args );
			} else {
				// If user has dismissed site-wide notice but we're in settings pages, show notice.
				$args['custom_id']             = $key_base . '-settings';
				$args['dismissible']           = false;
				$args['only_show_in_settings'] = true;

				$this->as3cf->notices->add_notice( $message, $args );
			}
		} else {
			// If PHP version (now) OK, make sure notices not shown.
			$this->as3cf->notices->remove_notice_by_id( $key_base . '-site' );
			$this->as3cf->notices->remove_notice_by_id( $key_base . '-settings' );
		}
	}

	/**
	 * Filters the REST dispatch request to determine whether route needs compatibility actions.
	 *
	 * @param bool            $dispatch_result Dispatch result, will be used if not empty.
	 * @param WP_REST_Request $request         Request used to generate the response.
	 * @param string          $route           Route matched for the request.
	 * @param array           $handler         Route handler used for the request.
	 *
	 * @return bool
	 */
	public function rest_dispatch_request_copy_back_to_local( $dispatch_result, $request, $route, $handler ) {
		$routes = array(
			'/regenerate-thumbnails/v\d+/regenerate/',
		);

		$routes = apply_filters( 'as3cf_rest_api_enable_get_attached_file_copy_back_to_local', $routes );
		$routes = is_array( $routes ) ? $routes : (array) $routes;

		if ( ! empty( $routes ) ) {
			foreach ( $routes as $match_route ) {
				if ( preg_match( '@' . $match_route . '@i', $route ) ) {
					$this->enable_get_attached_file_copy_back_to_local();
					break;
				}
			}
		}

		return $dispatch_result;
	}

	/**
	 * Domain Mapping may have overridden the original siteurl that is needed for search/replace.
	 *
	 * @param string $siteurl
	 *
	 * @return mixed
	 */
	public function get_orig_siteurl( $siteurl ) {
		if ( defined( 'DOMAIN_MAPPING' ) && function_exists( 'get_original_url' ) ) {
			$siteurl = get_original_url( 'siteurl' );
		}

		return $siteurl;
	}
}

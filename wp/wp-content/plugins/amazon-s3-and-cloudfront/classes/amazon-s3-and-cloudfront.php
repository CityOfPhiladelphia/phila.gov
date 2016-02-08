<?php

class Amazon_S3_And_CloudFront extends AWS_Plugin_Base {

	/**
	 * @var Amazon_Web_Services
	 */
	private $aws;

	/**
	 * @var Aws\S3\S3Client
	 */
	private $s3client;

	/**
	 * @var string
	 */
	protected $plugin_title;

	/**
	 * @var string
	 */
	protected $plugin_menu_title;

	/**
	 * @var array
	 */
	protected static $admin_notices = array();

	/**
	 * @var
	 */
	protected static $plugin_page;

	/**
	 * @var string
	 */
	protected $plugin_prefix = 'as3cf';

	/**
	 * @var string
	 */
	protected $default_tab = '';

	/**
	 * @var AS3CF_Notices
	 */
	public $notices;

	/**
	 * @var string
	 */
	public $hook_suffix;

	/**
	 * @var array Store if each bucket, used by the plugin and addons, is writable
	 */
	protected static $buckets_check = array();

	/**
	 * @var array
	 */
	protected $encode_files = array();

	/**
	 * @var AS3CF_Plugin_Compatibility
	 */
	public $plugin_compat;

	const DEFAULT_ACL = 'public-read';
	const PRIVATE_ACL = 'private';
	const DEFAULT_EXPIRES = 900;
	const DEFAULT_REGION = 'us-east-1';

	const SETTINGS_KEY = 'tantan_wordpress_s3';

	/**
	 * @param string              $plugin_file_path
	 * @param Amazon_Web_Services $aws
	 * @param string|null         $slug
	 */
	function __construct( $plugin_file_path, $aws, $slug = null ) {
		$this->plugin_slug = ( is_null( $slug ) ) ? 'amazon-s3-and-cloudfront' : $slug;

		parent::__construct( $plugin_file_path );

		$this->aws = $aws;
		$this->notices = AS3CF_Notices::get_instance( $this, $plugin_file_path );

		$this->init( $plugin_file_path );
	}

	/**
	 * Abstract class constructor
	 *
	 * @param string $plugin_file_path
	 */
	function init( $plugin_file_path ) {
		self::$plugin_page       = $this->plugin_slug;
		$this->plugin_title      = __( 'Offload S3', 'amazon-s3-and-cloudfront' );
		$this->plugin_menu_title = __( 'S3 and CloudFront', 'amazon-s3-and-cloudfront' );

		new AS3CF_Upgrade_Region_Meta( $this );
		new AS3CF_Upgrade_File_Sizes( $this );
		new AS3CF_Upgrade_Meta_WP_Error( $this );

		// Plugin setup
		add_action( 'aws_admin_menu', array( $this, 'admin_menu' ) );
		add_filter( 'plugin_action_links', array( $this, 'plugin_actions_settings_link' ), 10, 2 );
		add_filter( 'pre_get_space_used', array( $this, 'multisite_get_spaced_used' ) );

		// UI AJAX
		add_action( 'wp_ajax_as3cf-get-buckets', array( $this, 'ajax_get_buckets' ) );
		add_action( 'wp_ajax_as3cf-save-bucket', array( $this, 'ajax_save_bucket' ) );
		add_action( 'wp_ajax_as3cf-create-bucket', array( $this, 'ajax_create_bucket' ) );
		add_action( 'wp_ajax_as3cf-manual-save-bucket', array( $this, 'ajax_save_bucket' ) );
		add_action( 'wp_ajax_as3cf-get-url-preview', array( $this, 'ajax_get_url_preview' ) );

		// Rewriting URLs, doesn't depend on plugin being setup
		add_filter( 'wp_get_attachment_url', array( $this, 'wp_get_attachment_url' ), 99, 2 );
		add_filter( 'get_image_tag', array( $this, 'maybe_encode_get_image_tag' ), 99, 6 );
		add_filter( 'wp_get_attachment_image_src', array( $this, 'maybe_encode_wp_get_attachment_image_src' ), 99, 4 );
		add_filter( 'wp_prepare_attachment_for_js', array( $this, 'maybe_encode_wp_prepare_attachment_for_js' ), 99, 3 );
		add_filter( 'image_get_intermediate_size', array( $this, 'maybe_encode_image_get_intermediate_size' ), 99, 3 );
		add_filter( 'get_attached_file', array( $this, 'get_attached_file' ), 10, 2 );

		// Communication with S3, plugin needs to be setup
		add_filter( 'wp_handle_upload_prefilter', array( $this, 'wp_handle_upload_prefilter' ), 1 );
		add_filter( 'wp_update_attachment_metadata', array( $this, 'wp_update_attachment_metadata' ), 110, 2 );
		add_filter( 'delete_attachment', array( $this, 'delete_attachment' ), 20 );
		add_filter( 'update_attached_file', array( $this, 'update_attached_file' ), 100, 2 );

		// include compatibility code for other plugins
		$this->plugin_compat = new AS3CF_Plugin_Compatibility( $this );

		load_plugin_textdomain( 'amazon-s3-and-cloudfront', false, dirname( plugin_basename( $plugin_file_path ) ) . '/languages/' );

		// Register modal scripts and styles
		$this->register_modal_assets();
	}

	/**
	 * Get the plugin title to be used in page headings
	 *
	 * @return string
	 */
	function get_plugin_page_title() {
		return apply_filters( 'as3cf_settings_page_title', $this->plugin_title );
	}

	/**
	 * Get the plugin prefix in slug format, ie. replace underscores with hyphens
	 *
	 * @return string
	 */
	function get_plugin_prefix_slug() {
		return str_replace( '_', '-', $this->plugin_prefix );
	}

	/**
	 * Get the nonce key for the settings form of the plugin
	 *
	 * @return string
	 */
	function get_settings_nonce_key() {
		return $this->get_plugin_prefix_slug() . '-save-settings';
	}

	/**
	 * Accessor for a plugin setting with conditions to defaults and upgrades
	 *
	 * @param string $key
	 * @param mixed  $default
	 *
	 * @return int|mixed|string|WP_Error
	 */
	function get_setting( $key, $default = '' ) {
		// use settings from $_POST when generating URL preview via AJAX
		if ( isset( $_POST['action'] ) && 'as3cf-get-url-preview' == sanitize_key( $_POST['action'] ) ) { // input var okay
			$value = 0;
			if ( isset( $_POST[ $key ] ) ) { // input var okay
				$value = $_POST[ $key ]; // input var okay
				if ( is_array( $value ) ) {
					// checkbox is checked
					$value = 1;
				}
			}

			return $value;
		}

		$settings = $this->get_settings();

		// If legacy setting set, migrate settings
		if ( isset( $settings['wp-uploads'] ) && $settings['wp-uploads'] && in_array( $key, array( 'copy-to-s3', 'serve-from-s3' ) ) ) {
			return '1';
		}

		// Turn on object versioning by default
		if ( 'object-versioning' == $key && ! isset( $settings['object-versioning'] ) ) {
			return '1';
		}

		// Default object prefix
		if ( 'object-prefix' == $key && ! isset( $settings['object-prefix'] ) ) {
			return $this->get_default_object_prefix();
		}

		// Default use year and month folders
		if ( 'use-yearmonth-folders' == $key && ! isset( $settings['use-yearmonth-folders'] ) ) {
			return get_option( 'uploads_use_yearmonth_folders' );
		}

		// Default enable object prefix - enabled unless path is empty
		if ( 'enable-object-prefix' == $key ) {
			if ( isset( $settings['enable-object-prefix'] ) && '0' == $settings['enable-object-prefix'] ) {
				return 0;
			}

			if ( isset( $settings['object-prefix'] ) && '' == trim( $settings['object-prefix'] ) ) {
				return 0;
			} else {
				return 1;
			}
		}

		// Region of bucket if not already retrieved
		if ( 'region' == $key && ! isset( $settings['region'] ) ) {
			$bucket = $this->get_setting( 'bucket' );
			$region = $default;
			if ( $bucket ) {
				$region = $this->get_bucket_region( $bucket );
			}

			// Store the region for future use
			parent::set_setting( 'region', $region );
			$this->save_settings();

			return $region;
		}

		// Region of bucket translation
		if ( 'region' == $key && isset( $settings['region'] ) ) {

			return $this->translate_region( $settings['region'] );
		}

		// Domain setting since 0.8
		if ( 'domain' == $key && ! isset( $settings['domain'] ) ) {
			if ( $this->get_setting( 'cloudfront' ) ) {
				$domain = 'cloudfront';
			} elseif ( $this->get_setting( 'virtual-host' ) ) {
				$domain = 'virtual-host';
			} elseif ( $this->use_ssl() ) {
				$domain = 'path';
			} else {
				$domain = 'subdomain';
			}

			return $domain;
		}

		// SSL radio buttons since 0.8
		if ( 'ssl' == $key && ! isset( $settings['ssl'] ) ) {
			if ( $this->get_setting( 'force-ssl', false ) ) {
				$ssl = 'https';
			} else {
				$ssl = 'request';
			}

			return $ssl;
		}

		$value = parent::get_setting( $key, $default );

		if ( 'bucket' == $key && defined( 'AS3CF_BUCKET' ) ) {
			$bucket = AS3CF_BUCKET;

			if ( $bucket !== $value ) {
				// Save the defined bucket
				parent::set_setting( 'bucket', $bucket );
				// Clear region
				$this->remove_setting( 'region' );
				$this->save_settings();
			}

			return $bucket;
		}

		return apply_filters( 'as3cf_setting_' . $key, $value );
	}

	/**
	 * Setter for a plugin setting with custom hooks
	 *
	 * @param string $key
	 * @param mixed  $value
	 */
	function set_setting( $key, $value ) {
		// Run class specific hooks before the setting is saved
		$this->pre_set_setting( $key, $value );

		$value = apply_filters( 'as3cf_set_setting_' . $key, $value );

		// Remove disallowed characters from custom domain
		if ( 'cloudfront' === $key ) {
			$value = $this->sanitize_custom_domain( $value );
		}

		parent::set_setting( $key, $value );
	}

	/**
	 * Sanitize custom domain
	 *
	 * @param string $domain
	 *
	 * @return string
	 */
	function sanitize_custom_domain( $domain ) {
		$domain = preg_replace( '@^[a-zA-Z]*:\/\/@', '', $domain );
		$domain = preg_replace( '@[^a-zA-Z0-9\.\-]@', '', $domain );

		return $domain;
	}

	/**
	 * Return the default object prefix
	 *
	 * @return string
	 */
	function get_default_object_prefix() {
		if ( is_multisite() ) {
			return 'wp-content/uploads/';
		}

		$uploads = wp_upload_dir();
		$parts   = parse_url( $uploads['baseurl'] );
		$path    = ltrim( $parts['path'], '/' );

		return trailingslashit( $path );
	}

	/**
	 * Allowed mime types array that can be edited for specific S3 uploading
	 *
	 * @return array
	 */
	function get_allowed_mime_types() {
		return apply_filters( 'as3cf_allowed_mime_types', get_allowed_mime_types() );
	}

	/**
	 * Wrapper for scheduling  cron jobs
	 *
	 * @param string      $hook
	 * @param null|string $interval Defaults to hook if not supplied
	 * @param array       $args
	 */
	public function schedule_event( $hook, $interval = null, $args = array() ) {
		if ( is_null( $interval ) ) {
			$interval = $hook;
		}
		if ( ! wp_next_scheduled( $hook ) ) {
			wp_schedule_event( time(), $interval, $hook, $args );
		}
	}

	/**
	 * Wrapper for clearing scheduled events for a specific cron job
	 *
	 * @param string $hook
	 */
	public function clear_scheduled_event( $hook ) {
		$timestamp = wp_next_scheduled( $hook );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, $hook );
		}
	}

	/**
	 * Generate a preview of the URL of files uploaded to S3
	 *
	 * @param bool $escape
	 * @param string $suffix
	 *
	 * @return string
	 */
	function get_url_preview( $escape = true, $suffix = 'photo.jpg' ) {
		$scheme = $this->get_s3_url_scheme();
		$bucket = $this->get_setting( 'bucket' );
		$path   = $this->get_file_prefix();
		$region = $this->get_setting( 'region' );
		if ( is_wp_error( $region ) ) {
			$region = '';
		}

		$domain = $this->get_s3_url_domain( $bucket, $region, null, array(), true );

		$url = $scheme . '://' . $domain . '/' . $path . $suffix;

		// replace hyphens with non breaking hyphens for formatting
		if ( $escape ) {
			$url = str_replace( '-', '&#8209;', $url );
		}

		return $url;
	}

	/**
	 * AJAX handler for get_url_preview()
	 */
	function ajax_get_url_preview() {
		$this->verify_ajax_request();

		$url = $this->get_url_preview();

		$out = array(
			'success' => '1',
			'url'     => $url,
		);

		$this->end_ajax( $out );
	}

	/**
	 * Delete bulk objects from an S3 bucket
	 *
	 * @param string $region
	 * @param string $bucket
	 * @param array  $objects
	 * @param bool   $log_error
	 * @param bool   $return_on_error
	 * @param bool   $force_new_s3_client if we are deleting in bulk, force new S3 client
	 *                                    to cope with possible different regions
	 *
	 * @return bool
	 */
	function delete_s3_objects( $region, $bucket, $objects, $log_error = false, $return_on_error = false, $force_new_s3_client = false ) {
		$chunks = array_chunk( $objects, 1000 );

		try {
			foreach ( $chunks as $chunk ) {
				$this->get_s3client( $region, $force_new_s3_client )->deleteObjects( array(
					'Bucket'  => $bucket,
					'Objects' => $chunk,
				) );
			}
		} catch ( Exception $e ) {
			if ( $log_error ) {
				error_log( 'Error removing files from S3: ' . $e->getMessage() );
			}

			return false;
		}

		return true;
	}

	/**
	 * Removes an attachment's files from S3.
	 *
	 * @param int   $post_id
	 * @param array $s3object
	 * @param bool  $remove_backup_sizes       remove previous edited image versions
	 * @param bool  $log_error
	 * @param bool  $return_on_error
	 * @param bool  $force_new_s3_client       if we are deleting in bulk, force new S3 client
	 *                                         to cope with possible different regions
	 */
	function remove_attachment_files_from_s3( $post_id, $s3object, $remove_backup_sizes = true, $log_error = false, $return_on_error = false, $force_new_s3_client = false  ) {
		$prefix = trailingslashit( dirname( $s3object['key'] ) );
		$bucket = $s3object['bucket'];
		$region = $this->get_s3object_region( $s3object );
		$paths  = $this->get_attachment_file_paths( $post_id, false, false, $remove_backup_sizes );

		if ( is_wp_error( $region ) ) {
			$region = '';
		}

		$objects_to_remove = array();

		foreach ( $paths as $path ) {
			$objects_to_remove[] = array(
				'Key' => $prefix . basename( $path ),
			);
		}

		// finally delete the objects from S3
		$this->delete_s3_objects( $region, $bucket, $objects_to_remove, $log_error, $return_on_error, $force_new_s3_client );
	}

	/**
	 * Removes an attachment and intermediate image size files from S3
	 *
	 * @param int  $post_id
	 * @param bool $force_new_s3_client if we are deleting in bulk, force new S3 client
	 *                                  to cope with possible different regions
	 */
	function delete_attachment( $post_id, $force_new_s3_client = false  ) {
		if ( ! $this->is_plugin_setup() ) {
			return;
		}

		if ( ! ( $s3object = $this->get_attachment_s3_info( $post_id ) ) ) {
			return;
		}

		$this->remove_attachment_files_from_s3( $post_id, $s3object, true, true, true, $force_new_s3_client );

		delete_post_meta( $post_id, 'amazonS3_info' );
	}

	/**
	 * Handles the upload of the attachment to S3 when an attachment is updated using
	 * the 'wp_update_attachment_metadata' filter
	 *
	 * @param array $data meta data for attachment
	 * @param int   $post_id
	 *
	 * @return array
	 */
	function wp_update_attachment_metadata( $data, $post_id ) {
		if ( ! $this->is_plugin_setup() ) {
			return $data;
		}

		if ( ! ( $old_s3object = $this->get_attachment_s3_info( $post_id ) ) && ! $this->get_setting( 'copy-to-s3' ) ) {
			// abort if not already uploaded to S3 and the copy setting is off
			return $data;
		}

		// allow S3 upload to be cancelled for any reason
		$pre = apply_filters( 'as3cf_pre_update_attachment_metadata', false, $data, $post_id, $old_s3object );
		if ( false !== $pre ) {
			return $data;
		}

		// upload attachment to S3
		$data = $this->upload_attachment_to_s3( $post_id, $data );

		return $data;
	}

	/**
	 * Upload attachment to S3
	 *
	 * @param int         $post_id
	 * @param array|null  $data
	 * @param string|null $file_path
	 * @param bool        $force_new_s3_client if we are uploading in bulk, force new S3 client
	 *                                         to cope with possible different regions
	 * @param bool        $remove_local_files
	 *
	 * @return array|WP_Error $s3object|$meta If meta is supplied, return it. Else return S3 meta
	 */
	function upload_attachment_to_s3( $post_id, $data = null, $file_path = null, $force_new_s3_client = false, $remove_local_files = true ) {
		$return_metadata = null;
		if ( is_null( $data ) ) {
			$data = wp_get_attachment_metadata( $post_id, true );
		} else {
			// As we have passed in the meta, return it later
			$return_metadata = $data;
		}

		// Allow S3 upload to be hijacked / cancelled for any reason
		$pre = apply_filters( 'as3cf_pre_upload_attachment', false, $post_id, $data );
		if ( false !== $pre ) {
			if ( ! is_null( $return_metadata ) ) {
				// If the attachment metadata is supplied, return it
				return $data;
			}

			$error_msg = is_string( $pre ) ? $pre : __( 'Upload aborted by filter \'as3cf_pre_upload_attachment\'', 'amazon-s3-and-cloudfront' );

			return $this->return_upload_error( $error_msg );
		}

		if ( is_null( $file_path ) ) {
			$file_path = get_attached_file( $post_id, true );
		}

		// Check file exists locally before attempting upload
		if ( ! file_exists( $file_path ) ) {
			$error_msg = sprintf( __( 'File %s does not exist', 'amazon-s3-and-cloudfront' ), $file_path );

			return $this->return_upload_error( $error_msg, $return_metadata );
		}

		$file_name     = basename( $file_path );
		$type          = get_post_mime_type( $post_id );
		$allowed_types = $this->get_allowed_mime_types();

		// check mime type of file is in allowed S3 mime types
		if ( ! in_array( $type, $allowed_types ) ) {
			$error_msg = sprintf( __( 'Mime type %s is not allowed', 'amazon-s3-and-cloudfront' ), $type );

			return $this->return_upload_error( $error_msg, $return_metadata );
		}

		$acl = self::DEFAULT_ACL;

		// check the attachment already exists in S3, eg. edit or restore image
		if ( ( $old_s3object = $this->get_attachment_s3_info( $post_id ) ) ) {
			// use existing non default ACL if attachment already exists
			if ( isset( $old_s3object['acl'] ) ) {
				$acl = $old_s3object['acl'];
			}
			// use existing prefix
			$prefix = dirname( $old_s3object['key'] );
			$prefix = ( '.' === $prefix ) ? '' : $prefix . '/';
			// use existing bucket
			$bucket = $old_s3object['bucket'];
			// get existing region
			if ( isset( $old_s3object['region'] ) ) {
				$region = $old_s3object['region'];
			};
		} else {
			// derive prefix from various settings
			if ( isset( $data['file'] ) ) {
				$time = $this->get_folder_time_from_url( $data['file'] );
			} else {
				$time = $this->get_attachment_folder_time( $post_id );
				$time = date( 'Y/m', $time );
			}

			$prefix = $this->get_file_prefix( $time, $post_id );

			// use bucket from settings
			$bucket = $this->get_setting( 'bucket' );
			$region = $this->get_setting( 'region' );
			if ( is_wp_error( $region ) ) {
				$region = '';
			}
		}

		$acl = apply_filters( 'wps3_upload_acl', $acl, $type, $data, $post_id, $this ); // Old naming convention, will be deprecated soon
		$acl = apply_filters( 'as3cf_upload_acl', $acl, $data, $post_id );

		$s3object = array(
			'bucket' => $bucket,
			'key'    => $prefix . $file_name,
			'region' => $region,
		);

		// store acl if not default
		if ( $acl != self::DEFAULT_ACL ) {
			$s3object['acl'] = $acl;
		}

		$s3client = $this->get_s3client( $region, $force_new_s3_client );

		$args = array(
			'Bucket'      => $bucket,
			'Key'         => $prefix . $file_name,
			'SourceFile'  => $file_path,
			'ACL'         => $acl,
			'ContentType' => $type,
		);

		// If far future expiration checked (10 years)
		if ( $this->get_setting( 'expires' ) ) {
			$args['CacheControl'] = 'max-age=315360000';
			$args['Expires']      = date( 'D, d M Y H:i:s O', time() + 315360000 );
		}

		// Handle gzip on supported items
		if ( $this->should_gzip_file( $file_path, $type ) && false !== ( $gzip_body = gzencode( file_get_contents( $file_path ) ) ) ) {
			unset( $args['SourceFile'] );

			$args['Body']            = $gzip_body;
			$args['ContentEncoding'] = 'gzip';
		}
		$args = apply_filters( 'as3cf_object_meta', $args, $post_id );

		do_action( 'as3cf_upload_attachment_pre_remove', $post_id, $s3object, $prefix, $args );

		$files_to_remove = array();

		if ( file_exists( $file_path ) ) {
			$files_to_remove[] = $file_path;
			try {
				$s3client->putObject( $args );
			}
			catch ( Exception $e ) {
				$error_msg = sprintf( __( 'Error uploading %s to S3: %s', 'amazon-s3-and-cloudfront' ), $file_path, $e->getMessage() );

				return $this->return_upload_error( $error_msg, $return_metadata );
			}
		}

		delete_post_meta( $post_id, 'amazonS3_info' );

		add_post_meta( $post_id, 'amazonS3_info', $s3object );

		$file_paths        = $this->get_attachment_file_paths( $post_id, true, $data );
		$additional_images = array();

		$filesize_total = 0;
		$remove_local_files_setting = $this->get_setting( 'remove-local-file' );

		if ( $remove_local_files_setting ) {
			$bytes = filesize( $file_path );
			if ( false !== $bytes ) {
				// Store in the attachment meta data for use by WP
				$data['filesize'] = $bytes;

				if ( is_null( $return_metadata ) ) {
					// Update metadata with filesize
					update_post_meta( $post_id, '_wp_attachment_metadata', $data );
				}

				// Add to the file size total
				$filesize_total += $bytes;
			}
		}

		foreach ( $file_paths as $file_path ) {
			if ( ! in_array( $file_path, $files_to_remove ) ) {
				$additional_images[] = array(
					'Key'        => $prefix . basename( $file_path ),
					'SourceFile' => $file_path,
				);

				$files_to_remove[] = $file_path;

				if ( $remove_local_files_setting ) {
					// Record the file size for the additional image
					$bytes = filesize( $file_path );
					if ( false !== $bytes ) {
						$filesize_total += $bytes;
					}
				}
			}
		}

		foreach ( $additional_images as $image ) {
			try {
				$args = array_merge( $args, $image );
				$args['ACL'] = self::DEFAULT_ACL;
				$s3client->putObject( $args );
			}
			catch ( Exception $e ) {
				error_log( 'Error uploading ' . $args['SourceFile'] . ' to S3: ' . $e->getMessage() );
			}
		}

		if ( $remove_local_files ) {
			if ( $remove_local_files_setting ) {
				// Allow other functions to remove files after they have processed
				$files_to_remove = apply_filters( 'as3cf_upload_attachment_local_files_to_remove', $files_to_remove, $post_id, $file_path );
				// Remove duplicates
				$files_to_remove = array_unique( $files_to_remove );

				// Delete the files
				$this->remove_local_files( $files_to_remove );
			}
		}

		// Store the file size in the attachment meta if we are removing local file
		if ( $remove_local_files_setting ) {
			if ( $filesize_total > 0 ) {
				// Add the total file size for all image sizes
				update_post_meta( $post_id, 'wpos3_filesize_total', $filesize_total );
			}
		} else {
			if ( isset( $data['filesize'] ) ) {
				// Make sure we don't have a cached file sizes in the meta
				unset( $data['filesize'] );

				if ( is_null( $return_metadata ) ) {
					// Remove the filesize from the metadata
					update_post_meta( $post_id, '_wp_attachment_metadata', $data );
				}

				delete_post_meta( $post_id, 'wpos3_filesize_total' );
			}
		}

		do_action( 'wpos3_post_upload_attachment', $post_id, $s3object );

		if ( ! is_null( $return_metadata ) ) {
			// If the attachment metadata is supplied, return it
			return $data;
		}

		return $s3object;
	}

	/**
	 * Should gzip file
	 *
	 * @param string $file_path
	 * @param string $type
	 *
	 * @return bool
	 */
	protected function should_gzip_file( $file_path, $type ) {
		$mimes = $this->get_mime_types_to_gzip( true );

		if ( in_array( $type, $mimes ) && is_readable( $file_path ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get mime types to gzip
	 *
	 * @param bool $media_library
	 *
	 * @return array
	 */
	protected function get_mime_types_to_gzip( $media_library = false ) {
		$mimes = apply_filters( 'as3cf_gzip_mime_types', array(
			'css'   => 'text/css',
			'eot'   => 'application/vnd.ms-fontobject',
			'html'  => 'text/html',
			'ico'   => 'image/x-icon',
			'js'    => 'application/javascript',
			'json'  => 'application/json',
			'otf'   => 'application/x-font-opentype',
			'rss'   => 'application/rss+xml',
			'svg'   => 'image/svg+xml',
			'ttf'   => 'application/x-font-ttf',
			'woff'  => 'application/font-woff',
			'woff2' => 'application/font-woff2',
			'xml'   => 'application/xml',
		), $media_library );

		return $mimes;
	}

	/**
	 * Helper to return meta data on upload error
	 *
	 * @param string     $error_msg
	 * @param array|null $return
	 *
	 * @return array|WP_Error
	 */
	protected function return_upload_error( $error_msg, $return = null ) {
		if ( is_null( $return ) ) {
			return new WP_Error( 'exception', $error_msg );
		}

		error_log( $error_msg );

		return $return;
	}

	/**
	 * Remove files from the local site
	 *
	 * @param array $file_paths array of files to remove
	 */
	function remove_local_files( $file_paths ) {
		foreach ( $file_paths as $path ) {
			if ( ! @unlink( $path ) ) {
				error_log( 'Error removing local file ' . $path );
			}
		}
	}

	/**
	 * Add HiDPi suffix to a file path
	 *
	 * @param string $orig_path
	 *
	 * @return string
	 */
	function get_hidpi_file_path( $orig_path ) {
		$hidpi_suffix = apply_filters( 'as3cf_hidpi_suffix', '@2x' );

		return $this->apply_file_suffix( $orig_path, $hidpi_suffix );
	}

	/**
	 * Helper to apply a suffix to a file path
	 *
	 * @param string $file
	 * @param string $suffix
	 *
	 * @return string
	 */
	function apply_file_suffix( $file, $suffix ) {
		$pathinfo = pathinfo( $file );

		return $pathinfo['dirname'] . '/' . $pathinfo['filename'] . $suffix . '.' . $pathinfo['extension'];
	}

	/**
	 * Get the object versioning string prefix
	 *
	 * @param int $post_id
	 *
	 * @return string
	 */
	function get_object_version_string( $post_id ) {
		if ( $this->get_setting( 'use-yearmonth-folders' ) ) {
			$date_format = 'dHis';
		} else {
			$date_format = 'YmdHis';
		}

		$time = $this->get_attachment_folder_time( $post_id );

		$object_version = date( $date_format, $time ) . '/';
		$object_version = apply_filters( 'as3cf_get_object_version_string', $object_version );

		return $object_version;
	}

	/**
	 * Get the upload folder time from given URL
	 *
	 * @param string $url
	 *
	 * @return null|string
	 */
	function get_folder_time_from_url( $url ) {
		preg_match( '@[0-9]{4}/[0-9]{2}@', $url, $matches );

		if ( isset( $matches[0] ) ) {
			return $matches[0];
		}

		return null;
	}

	/**
	 * Get the time of attachment upload.
	 *
	 * Use post datetime if attached.
	 *
	 * @param int $post_id
	 *
	 * @return int|string
	 */
	function get_attachment_folder_time( $post_id ) {
		$time = current_time( 'timestamp' );

		if ( ! ( $attach = get_post( $post_id ) ) ) {
			return $time;
		}

		if ( ! $attach->post_parent ) {
			return $time;
		}

		if ( ! ( $post = get_post( $attach->post_parent ) ) ) {
			return $time;
		}

		if ( substr( $post->post_date_gmt, 0, 4 ) > 0 ) {
			return strtotime( $post->post_date_gmt . ' +0000' );
		}

		return $time;
	}

	/**
	 * Create unique names for file to be uploaded to AWS
	 * This only applies when the remove local file option is enabled
	 *
	 * @param array   $file An array of data for a single file.
	 *
	 * @return array $file The altered file array with AWS unique filename.
	 */
	function wp_handle_upload_prefilter( $file ) {
		if ( ! $this->get_setting( 'copy-to-s3' ) || ! $this->is_plugin_setup() ) {
			return $file;
		}

		$filename = $file['name'];

		// sanitize the file name before we begin processing
		$filename = sanitize_file_name( $filename );

		// separate the filename into a name and extension
		$info = pathinfo( $filename );
		$ext  = ! empty( $info['extension'] ) ? '.' . $info['extension'] : '';
		$name = basename( $filename, $ext );

		// edge case: if file is named '.ext', treat as an empty name
		if ( $name === $ext ) {
			$name = '';
		}

		// rebuild filename with lowercase extension as S3 will have converted extension on upload
		$ext      = strtolower( $ext );
		$filename = $info['filename'] . $ext;
		$time     = current_time( 'mysql' );

		// Get time if uploaded in post screen
		$post_id = filter_input( INPUT_POST, 'post_id', FILTER_VALIDATE_INT );
		if ( isset( $post_id ) ) {
			$time = $this->get_post_time( $post_id );
		}

		if ( ! $this->does_file_exist( $filename, $time ) ) {
			// File doesn't exist locally or on S3, return it
			return $file;
		}

		$file['name'] = $this->generate_unique_filename( $name, $ext, $time );

		return $file;
	}

	/**
	 * Get post time
	 *
	 * @param int $post_id
	 *
	 * @return string
	 */
	function get_post_time( $post_id ) {
		$time = current_time( 'mysql' );

		if ( ! $post = get_post( $post_id ) ) {
			return $time;
		}

		if ( substr( $post->post_date, 0, 4 ) > 0 ) {
			$time = $post->post_date;
		}

		return $time;
	}

	/**
	 * Does file exist
	 *
	 * @param string $filename
	 * @param string $time
	 *
	 * @return bool
	 */
	function does_file_exist( $filename, $time ) {
		if ( $this->does_file_exist_local( $filename, $time ) ) {
			return true;
		}

		if ( ! $this->get_setting( 'object-versioning' ) && $this->does_file_exist_s3( $filename, $time ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Does file exist local
	 *
	 * @param string $filename
	 * @param string $time
	 *
	 * @return bool
	 */
	function does_file_exist_local( $filename, $time ) {
		global $wpdb;

		$path = wp_upload_dir( $time );
		$path = ltrim( $path['subdir'], '/' );

		if ( '' !== $path ) {
			$path = trailingslashit( $path );
		}
		$file = $path . $filename;

		$sql = $wpdb->prepare( "
			SELECT COUNT(*)
			FROM $wpdb->postmeta
			WHERE meta_key = %s
			AND meta_value = %s
		", '_wp_attached_file', $file );

		return (bool) $wpdb->get_var( $sql );
	}

	/**
	 * Does file exist s3
	 *
	 * @param string $filename
	 * @param string $time
	 *
	 * @return bool
	 */
	function does_file_exist_s3( $filename, $time ) {
		$bucket = $this->get_setting( 'bucket' );
		$region = $this->get_setting( 'region' );

		if ( is_wp_error( $region ) ) {
			return false;
		}

		$s3client = $this->get_s3client( $region );
		$prefix = ltrim( trailingslashit( $this->get_object_prefix() ), '/' );
		$prefix .= ltrim( trailingslashit( $this->get_dynamic_prefix( $time ) ), '/' );

		return $s3client->doesObjectExist( $bucket, $prefix . $filename );
	}

	/**
	 * Generate unique filename
	 *
	 * @param string $name
	 * @param string $ext
	 * @param string $time
	 *
	 * @return string
	 */
	function generate_unique_filename( $name, $ext, $time ) {
		$count    = 1;
		$filename = $name . $count . $ext;

		while ( $this->does_file_exist( $filename, $time ) ) {
			$count++;
			$filename = $name . $count . $ext;
		}

		return $filename;
	}

	/**
	 * Get attachment url
	 *
	 * @param string $url
	 * @param int    $post_id
	 *
	 * @return bool|mixed|void|WP_Error
	 */
	function wp_get_attachment_url( $url, $post_id ) {
		$new_url = $this->get_attachment_url( $post_id );
		if ( false === $new_url ) {
			return $url;
		}

		$new_url = apply_filters( 'wps3_get_attachment_url', $new_url, $post_id, $this ); // Old naming convention, will be deprecated soon
		$new_url = apply_filters( 'as3cf_wp_get_attachment_url', $new_url, $post_id );

		return $new_url;
	}

	/**
	 * Get attachment s3 info
	 *
	 * @param int $post_id
	 *
	 * @return mixed
	 */
	function get_attachment_s3_info( $post_id ) {
		return get_post_meta( $post_id, 'amazonS3_info', true );
	}

	/**
	 * Check the plugin is correctly setup
	 *
	 * @return bool
	 */
	function is_plugin_setup() {
		if ( is_wp_error( $this->aws->get_client() ) ) {
			// AWS not configured
			return false;
		}

		if ( false === (bool) $this->get_setting( 'bucket' ) ) {
			// No bucket selected
			return false;
		}

		if ( is_wp_error( $this->get_setting( 'region' ) ) ) {
			// Region error when retrieving bucket location
			return false;
		}

		// All good, let's do this
		return true;
	}

	/**
	 * Generate a link to download a file from Amazon S3 using query string
	 * authentication. This link is only valid for a limited amount of time.
	 *
	 * @param int         $post_id Post ID of the attachment
	 * @param int|null    $expires Seconds for the link to live
	 * @param string|null $size    Size of the image to get
	 * @param array       $headers Header overrides for request
	 * @param bool        $skip_rewrite_check
	 *
	 * @return mixed|void|WP_Error
	 */
	function get_secure_attachment_url( $post_id, $expires = null, $size = null, $headers = array(), $skip_rewrite_check = false ) {
		if ( is_null( $expires ) ) {
			$expires = self::DEFAULT_EXPIRES;
		}
		return $this->get_attachment_url( $post_id, $expires, $size, null, $headers, $skip_rewrite_check );
	}

	/**
	 * Return the scheme to be used in URLs
	 *
	 * @param string|null $ssl
	 *
	 * @return string
	 */
	function get_s3_url_scheme( $ssl = null ) {
		if ( $this->use_ssl( $ssl ) ) {
			$scheme = 'https';
		}
		else {
			$scheme = 'http';
		}

		return $scheme;
	}

	/**
	 * Determine when to use https in URLS
	 *
	 * @param string|null $ssl
	 *
	 * @return bool
	 */
	function use_ssl( $ssl = null ) {
		$use_ssl = false;

		if ( is_null( $ssl ) ) {
			$ssl = $this->get_setting( 'ssl' );
		}

		if ( 'request' == $ssl && is_ssl() ) {
			$use_ssl = true;
		} else if ( 'https' == $ssl ) {
			$use_ssl = true;
		}

		return apply_filters( 'as3cf_use_ssl', $use_ssl );
	}

	/**
	 * Get the custom object prefix if enabled
	 *
	 * @param string $toggle_setting
	 *
	 * @return string
	 */
	function get_object_prefix( $toggle_setting = 'enable-object-prefix' ) {
		if ( $this->get_setting( $toggle_setting ) ) {
			$prefix = trim( $this->get_setting( 'object-prefix' ) );
		} else {
			$prefix = '';
		}

		return $prefix;
	}

	/**
	 * Get the file prefix
	 *
	 * @param null|string $time
	 * @param null|int $post_id
	 *
	 * @return string
	 */
	function get_file_prefix( $time = null, $post_id = null ) {
		$prefix = ltrim( trailingslashit( $this->get_object_prefix() ), '/' );
		$prefix .= ltrim( trailingslashit( $this->get_dynamic_prefix( $time ) ), '/' );

		if ( $this->get_setting( 'object-versioning' ) ) {
			$prefix .= $this->get_object_version_string( $post_id );
		}

		return $prefix;
	}

	/**
	 * Get the region specific prefix for S3 URL
	 *
	 * @param string   $region
	 * @param null|int $expires
	 *
	 * @return string
	 */
	function get_s3_url_prefix( $region = '', $expires = null ) {
		$prefix = 's3';

		if ( '' !== $region ) {
			$delimiter = '-';
			if ( 'eu-central-1' == $region && ! is_null( $expires ) ) {
				// if we are creating a secure URL for a Frankfurt base file use the alternative delimiter
				// http://docs.aws.amazon.com/general/latest/gr/rande.html#s3_region
				$delimiter = '.';
			}

			$prefix .= $delimiter . $region;
		}

		return $prefix;
	}

	/**
	 * Get the S3 url for the files
	 *
	 * @param string $bucket
	 * @param string $region
	 * @param int    $expires
	 * @param array  $args    Allows you to specify custom URL settings
	 * @param bool   $preview When generating the URL preview sanitize certain output
	 *
	 * @return mixed|string|void
	 */
	function get_s3_url_domain( $bucket, $region = '', $expires = null, $args = array(), $preview = false ) {
		if ( ! isset( $args['cloudfront'] ) ) {
			$args['cloudfront'] = $this->get_setting( 'cloudfront' );
		}

		if ( ! isset( $args['domain'] ) ) {
			$args['domain'] = $this->get_setting( 'domain' );
		}

		if ( ! isset( $args['ssl'] ) ) {
			$args['ssl'] = $this->get_setting( 'ssl' );
		}

		$prefix = $this->get_s3_url_prefix( $region, $expires );

		if ( 'cloudfront' === $args['domain'] && is_null( $expires ) && $args['cloudfront'] ) {
			$cloudfront = $args['cloudfront'];
			if ( $preview ) {
				$cloudfront = $this->sanitize_custom_domain( $cloudfront );
			}

			$s3_domain = $cloudfront;
		}
		elseif ( 'virtual-host' === $args['domain'] ) {
			$s3_domain = $bucket;
		}
		elseif ( 'path' === $args['domain'] || $this->use_ssl( $args['ssl'] ) ) {
			$s3_domain = $prefix . '.amazonaws.com/' . $bucket;
		}
		else {
			$s3_domain = $bucket . '.' . $prefix . '.amazonaws.com';
		}

		return $s3_domain;
	}

	/**
	 * Get the url of the file from Amazon S3
	 *
	 * @param int         $post_id            Post ID of the attachment
	 * @param int|null    $expires            Seconds for the link to live
	 * @param string|null $size               Size of the image to get
	 * @param array|null  $meta               Pre retrieved _wp_attachment_metadata for the attachment
	 * @param array       $headers            Header overrides for request
	 * @param bool        $skip_rewrite_check Always return the URL regardless of the 'Rewrite File URLs' setting.
	 *                                        Useful for the EDD and Woo addons to not break download URLs when the
	 *                                        option is disabled.
	 *
	 * @return bool|mixed|void|WP_Error
	 */
	function get_attachment_url( $post_id, $expires = null, $size = null, $meta = null, $headers = array(), $skip_rewrite_check = false ) {
		if ( ! ( $s3object = $this->is_attachment_served_by_s3( $post_id, $skip_rewrite_check ) ) ) {
			return false;
		}

		return $this->get_attachment_s3_url( $post_id, $s3object, $expires, $size, $meta, $headers );
	}

	/**
	 * Get the S3 URL for an attachment
	 *
	 * @param int         $post_id
	 * @param array       $s3object
	 * @param null|int    $expires
	 * @param null|string $size
	 * @param null|array  $meta
	 * @param array       $headers
	 *
	 * @return mixed|void|WP_Error
	 */
	public function get_attachment_s3_url( $post_id, $s3object, $expires = null, $size = null, $meta = null, $headers = array() ) {
		$scheme = $this->get_s3_url_scheme();

		// We don't use $this->get_s3object_region() here because we don't want
		// to make an AWS API call and slow down page loading
		if ( isset( $s3object['region'] ) && self::DEFAULT_REGION !== $s3object['region'] ) {
			$region = $this->translate_region( $s3object['region'] );
		} else {
			$region = '';
		}

		// force use of secured url when ACL has been set to private
		if ( is_null( $expires ) && isset( $s3object['acl'] ) && self::PRIVATE_ACL == $s3object['acl'] ) {
			$expires = self::DEFAULT_EXPIRES;
		}

		$domain_bucket = $this->get_s3_url_domain( $s3object['bucket'], $region, $expires );

		if ( ! is_null( $size ) ) {
			if ( is_null( $meta ) ) {
				$meta = get_post_meta( $post_id, '_wp_attachment_metadata', true );
			}
			if ( isset( $meta['sizes'][ $size ]['file'] ) ) {
				$size_prefix      = dirname( $s3object['key'] );
				$size_file_prefix = ( '.' === $size_prefix ) ? '' : $size_prefix . '/';

				$s3object['key'] = $size_file_prefix . $meta['sizes'][ $size ]['file'];
			}
		}

		if ( ! is_null( $expires ) && $this->is_plugin_setup() ) {
			try {
				$expires    = time() + $expires;
				$secure_url = $this->get_s3client( $region )->getObjectUrl( $s3object['bucket'], $s3object['key'], $expires, $headers );

				return apply_filters( 'as3cf_get_attachment_secure_url', $secure_url, $s3object, $post_id, $expires, $headers );
			}
			catch ( Exception $e ) {
				return new WP_Error( 'exception', $e->getMessage() );
			}
		}

		$file = $this->encode_filename_in_path( $s3object['key'] );
		$url  = $scheme . '://' . $domain_bucket . '/' . $file;

		return apply_filters( 'as3cf_get_attachment_url', $url, $s3object, $post_id, $expires, $headers );
	}

	/**
	 * Maybe encode attachment URLs when retrieving the image tag
	 *
	 * @param string $html
	 * @param int    $id
	 * @param string $alt
	 * @param string $title
	 * @param string $align
	 * @param string $size
	 *
	 * @return string
	 */
	public function maybe_encode_get_image_tag( $html, $id, $alt, $title, $align, $size ) {
		if ( ! $this->is_attachment_served_by_s3( $id ) ) {
			return $html;
		}

		preg_match( '@\ssrc=[\'\"]([^\'\"]*)[\'\"]@', $html, $matches );

		if ( ! isset( $matches[1] ) ) {
			// Can't establish img src
			return $html;
		}

		$img_src     = $matches[1];
		$encoded_src = $this->encode_filename_in_path( $img_src );

		return str_replace( $img_src, $encoded_src, $html );
	}

	/**
	 * Maybe encode URLs for images that represent an attachment
	 *
	 * @param array|bool   $image
	 * @param int          $attachment_id
	 * @param string|array $size
	 * @param bool         $icon
	 *
	 * @return array
	 */
	public function maybe_encode_wp_get_attachment_image_src( $image, $attachment_id, $size, $icon ) {
		if ( ! $this->is_attachment_served_by_s3( $attachment_id ) ) {
			return $image;
		}

		if ( isset( $image[0] ) ) {
			$image[0] = $this->encode_filename_in_path( $image[0] );
		}

		return $image;
	}

	/**
	 * Maybe encode URLs when outputting attachments in the media grid
	 *
	 * @param array      $response
	 * @param int|object $attachment
	 * @param array      $meta
	 *
	 * @return array
	 */
	public function maybe_encode_wp_prepare_attachment_for_js( $response, $attachment, $meta ) {
		if ( ! $this->is_attachment_served_by_s3( $attachment->ID ) ) {
			return $response;
		}

		if ( isset( $response['url'] ) ) {
			$response['url'] = $this->encode_filename_in_path( $response['url'] );
		}

		if ( isset( $response['sizes'] ) && is_array( $response['sizes'] ) ) {
			foreach ( $response['sizes'] as $key => $value ) {
				$response['sizes'][ $key ]['url'] = $this->encode_filename_in_path( $value['url'] );
			}
		}

		return $response;
	}

	/**
	 * Maybe encode URLs when retrieving intermediate sizes.
	 *
	 * @param array        $data
	 * @param int          $post_id
	 * @param string|array $size
	 *
	 * @return array
	 */
	public function maybe_encode_image_get_intermediate_size( $data, $post_id, $size ) {
		if ( ! $this->is_attachment_served_by_s3( $post_id ) ) {
			return $data;
		}

		if ( isset( $data['url'] ) ) {
			$data['url'] = $this->encode_filename_in_path( $data['url'] );
		}

		return $data;
	}

	/**
	 * Is attachment served by S3.
	 *
	 * @param int  $attachment_id
	 * @param bool $skip_rewrite_check
	 *
	 * @return bool|array
	 */
	public function is_attachment_served_by_s3( $attachment_id, $skip_rewrite_check = false ) {
		if ( ! $skip_rewrite_check && ! $this->get_setting( 'serve-from-s3' ) ) {
			// Not serving S3 URLs
			return false;
		}

		if ( ! ( $s3object = $this->get_attachment_s3_info( $attachment_id ) ) ) {
			// File not uploaded to S3
			return false;
		}

		return $s3object;
	}

	/**
	 * Encode file names according to RFC 3986 when generating urls
	 * As per Amazon https://forums.aws.amazon.com/thread.jspa?threadID=55746#jive-message-244233
	 *
	 * @param string $file
	 *
	 * @return string Encoded filename with path prefix untouched
	 */
	function encode_filename_in_path( $file ) {
		$url = parse_url( $file );

		if ( ! isset( $url['path'] ) ) {
			// Can't determine path, return original
			return $file;
		}

		if ( in_array( $this->normalize_file_path( $url['path'] ), $this->encode_files ) ) {
			// Already encoded, return original
			return $file;
		}

		$file_path         = dirname( $file );
		$file_path         = ( '.' !== $file_path ) ? trailingslashit( $file_path ) : '';
		$file_name         = basename( $url['path'] );
		$encoded_file_name = rawurlencode( $file_name );
		$encoded_file_path = $file_path . $encoded_file_name;

		if ( $file_name === $encoded_file_name ) {
			// File name doesn't need encoding, return original
			return $file;
		}

		$normalized_file_path = $this->normalize_file_path( $encoded_file_path );

		if ( ! in_array( $normalized_file_path, $this->encode_files ) ) {
			$this->encode_files[] = $normalized_file_path;
		}

		return str_replace( $file_name, $encoded_file_name, $file );
	}

	/**
	 * Normalize file path
	 *
	 * @param string $path
	 *
	 * @return string mixed
	 */
	function normalize_file_path( $path ) {
		$url = parse_url( $path );

		if ( isset( $url['scheme'] ) ) {
			$path = str_replace( $url['scheme'] . '://', '', $path );
		}

		return '/' . ltrim( $path, '/' );
	}

	/**
	 * Allow processes to update the file on S3 via update_attached_file()
	 *
	 * @param string $file
	 * @param int $attachment_id
	 *
	 * @return string
	 */
	function update_attached_file( $file, $attachment_id ) {
		if ( ! $this->is_plugin_setup() ) {
			return $file;
		}

		if ( ! ( $s3object = $this->get_attachment_s3_info( $attachment_id ) ) ) {
			return $file;
		}

		$file = apply_filters( 'as3cf_update_attached_file', $file, $attachment_id, $s3object );

		return $file;
	}

	/**
	 * Return the S3 URL when the local file is missing
	 * unless we know the calling process is and we are happy
	 * to copy the file back to the server to be used
	 *
	 * @param string $file
	 * @param int    $attachment_id
	 *
	 * @return string
	 */
	function get_attached_file( $file, $attachment_id ) {
		if ( file_exists( $file ) || ! ( $s3object = $this->is_attachment_served_by_s3( $attachment_id ) ) ) {
			return $file;
		}

		$url = $this->get_attachment_url( $attachment_id );

		// return the URL by default
		$file = apply_filters( 'as3cf_get_attached_file', $url, $file, $attachment_id, $s3object );

		return $file;
	}

	/**
	 * Helper method for returning data to AJAX call
	 *
	 * @param array $return
	 */
	function end_ajax( $return = array() ) {
		echo json_encode( $return );
		exit;
	}

	function verify_ajax_request() {
		if ( ! is_admin() || ! wp_verify_nonce( sanitize_key( $_POST['_nonce'] ), sanitize_key( $_POST['action'] ) ) ) { // input var okay
			wp_die( __( 'Cheatin&#8217; eh?', 'amazon-s3-and-cloudfront' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'amazon-s3-and-cloudfront' ) );
		}
	}

	function ajax_check_bucket() {
		if ( ! isset( $_POST['bucket_name'] ) || ! ( $bucket = sanitize_text_field( $_POST['bucket_name'] ) ) ) { // input var okay
			$out = array( 'error' => __( 'No bucket name provided.', 'amazon-s3-and-cloudfront' ) );

			$this->end_ajax( $out );
		}

		return strtolower( $bucket );
	}

	/**
	 * Handler for AJAX callback to create a bucket in S3
	 */
	function ajax_create_bucket() {
		$this->verify_ajax_request();

		$bucket = $this->ajax_check_bucket();

		if ( defined( 'AS3CF_REGION' ) ) {
			// Are we defining the region?
			$region = AS3CF_REGION;
		} else {
			// Are we specifying the region via the form?
			$region = isset( $_POST['region'] ) ? sanitize_text_field( $_POST['region'] ) : null; // input var okay
		}

		$result = $this->create_bucket( $bucket, $region );
		if ( is_wp_error( $result ) ) {
			$out = $this->prepare_bucket_error( $result, false );

			$this->end_ajax( $out );
		}

		// check if we were previously selecting a bucket manually via the input
		$previous_manual_bucket_select = $this->get_setting( 'manual_bucket', false );

		$args = array(
			'_nonce' => wp_create_nonce( 'as3cf-create-bucket' )
		);

		$this->save_bucket_for_ajax( $bucket, $previous_manual_bucket_select, $region, $args );
	}

	/**
	 * Create an S3 bucket
	 *
	 * @param string      $bucket_name
	 * @param null|string $region option location constraint
	 *
	 * @return bool|WP_Error
	 */
	function create_bucket( $bucket_name, $region = null ) {
		try {
			$args = array( 'Bucket' => $bucket_name );

			if ( defined( 'AS3CF_REGION' ) ) {
				// Make sure we always use the defined region
				$region = AS3CF_REGION;
			}

			if ( ! is_null( $region ) && self::DEFAULT_REGION !== $region ) {
				$args['LocationConstraint'] = $region;
			}

			$this->get_s3client()->createBucket( $args );
		}
		catch ( Exception $e ) {
			return new WP_Error( 'exception', $e->getMessage() );
		}

		return true;
	}

	/**
	 * Handler for AJAX callback to save the selection of a bucket
	 */
	function ajax_save_bucket() {
		$this->verify_ajax_request();

		$bucket = $this->ajax_check_bucket();

		$manual = false;
		// are we inputting the bucket manually?
		if ( isset( $_POST['action'] ) && false !== strpos( $_POST['action'], 'manual-save-bucket' ) ) {
			$manual = true;
		}

		$this->save_bucket_for_ajax( $bucket, $manual );
	}

	/**
	 * Wrapper method for saving a bucket when creating or selecting
	 *
	 * @param string      $bucket
	 * @param bool|false  $manual_select
	 * @param null|string $region
	 * @param array       $defaults
	 */
	function save_bucket_for_ajax( $bucket, $manual_select = false, $region = null, $defaults = array() ) {
		$region = $this->save_bucket( $bucket, $manual_select, $region );

		if ( ! is_wp_error( $region ) ) {
			$out = array(
				'success' => '1',
				'region'  => $region,
			);

			$out = wp_parse_args( $out, $defaults );

			$can_write = $this->check_write_permission( $bucket, $region );

			if ( is_wp_error( $can_write ) ) {
				$out = $this->prepare_bucket_error( $can_write );
			} else {
				$out['can_write'] = $can_write;
			}
		} else {
			$out = $this->prepare_bucket_error( $region );
		}

		$this->end_ajax( $out );
	}

	/**
	 * Prepare the bucket error before returning to JS
	 *
	 * @param WP_Error $object
	 * @param bool     $single Are we dealing with a single bucket?
	 *
	 * @return array
	 */
	function prepare_bucket_error( $object, $single = true ) {
		if ( 'Access Denied' === $object->get_error_message() ) {
			// If the bucket error is access denied, show our notice message
			$out = array( 'error' => $this->get_access_denied_notice_message( $single ) );
		} else {
			$out = array( 'error' => $object->get_error_message() );
		}

		return $out;
	}

	/**
	 * Perform custom actions before the setting is saved
	 *
	 * @param string $key
	 * @param string $value
	 */
	function pre_set_setting( $key, $value ) {
		if ( 'bucket' === $key && ! $this->get_setting( 'bucket' ) ) {
			// first time bucket select - enable main options by default
			$this->set_setting( 'copy-to-s3', '1' );
			$this->set_setting( 'serve-from-s3', '1' );
		}
	}

	/**
	 * Save bucket and bucket's region
	 *
	 * @param string      $bucket_name
	 * @param bool        $manual if we are entering the bucket via the manual input form
	 * @param null|string $region
	 *
	 * @return string|bool region on success
	 */
	function save_bucket( $bucket_name, $manual = false, $region = null ) {
		if ( $bucket_name ) {
			$this->get_settings();

			$this->set_setting( 'bucket', $bucket_name );

			if ( is_null( $region ) ) {
				// retrieve the bucket region if not supplied
				$region = $this->get_bucket_region( $bucket_name );
				if ( is_wp_error( $region ) ) {
					return $region;
				}
			}

			if ( self::DEFAULT_REGION === $region ) {
				$region = '';
			}

			$this->set_setting( 'region', $region );

			if ( $manual ) {
				// record that we have entered the bucket via the manual form
				$this->set_setting( 'manual_bucket', true );
			} else {
				$this->remove_setting( 'manual_bucket' );
			}

			$this->save_settings();

			return $region;
		}

		return false;
	}

	/**
	 * Get all AWS regions
	 *
	 * @return array
	 */
	function get_aws_regions() {
		$regionEnum  = new ReflectionClass( 'Aws\Common\Enum\Region' );
		$all_regions = $regionEnum->getConstants();

		$regions = array();
		foreach ( $all_regions as $label => $region ) {
			// Nicely format region name
			if ( self::DEFAULT_REGION === $region ) {
				$label = 'US Standard';
			} else {
				$label = strtolower( $label );
				$label = str_replace( '_', ' ', $label );
				$label = ucwords( $label );
			}

			$regions[ $region ] = $label;
		}

		return $regions;
	}

	/**
	 * Add the settings menu item
	 *
	 * @param Amazon_Web_Services $aws
	 */
	function admin_menu( $aws ) {
		$hook_suffix = $aws->add_page( $this->get_plugin_page_title(), $this->plugin_menu_title, 'manage_options', $this->plugin_slug, array( $this, 'render_page' ) );

		if ( false !== $hook_suffix ) {
			$this->hook_suffix = $hook_suffix;
			add_action( 'load-' . $this->hook_suffix, array( $this, 'plugin_load' ) );
		}
	}

	/**
	 * Get the S3 client
	 *
	 * @param bool|string $region specify region to client for signature
	 * @param bool        $force  force return of new S3 client when swapping regions
	 *
	 * @return Aws\S3\S3Client
	 */
	function get_s3client( $region = false, $force = false ) {
		if ( is_null( $this->s3client ) || $force ) {

			if ( $region ) {
				$args = array(
					'region'    => $this->translate_region( $region ),
					'signature' => 'v4',
				);
			} else {
				$args = array();
			}

			$client = $this->aws->get_client()->get( 's3', $args );
			$this->set_client( $client );
		}

		return $this->s3client;
	}

	/**
	 * Setter for S3 client
	 *
	 * @param Aws\S3\S3Client $client
	 */
	public function set_client( $client ) {
		$this->s3client = $client;
	}

	/**
	 * Get the region of a bucket
	 *
	 * @param string $bucket
	 *
	 * @return string|WP_Error
	 */
	function get_bucket_region( $bucket ) {
		try {
			$region = $this->get_s3client()->getBucketLocation( array( 'Bucket' => $bucket ) );
		} catch ( Exception $e ) {
			$error_msg_title = '<strong>' . __( 'Error Getting Bucket Region', 'amazon-s3-and-cloudfront' ) . '</strong> &mdash;';
			$error_msg       = sprintf( __( 'There was an error attempting to get the region of the bucket %s: %s', 'amazon-s3-and-cloudfront' ), $bucket, $e->getMessage() );
			error_log( $error_msg );

			return new WP_Error( 'exception', $error_msg_title . $error_msg );
		}

		$region = $this->translate_region( $region['Location'] );

		return $region;
	}

	/**
	 * Get the region of the bucket stored in the S3 metadata.
	 *
	 *
	 * @param array $s3object
	 * @param int   $post_id  - if supplied will update the s3 meta if no region found
	 *
	 * @return string|WP_Error - region name
	 */
	function get_s3object_region( $s3object, $post_id = null ) {
		if ( ! isset( $s3object['region'] ) ) {
			// if region hasn't been stored in the s3 metadata retrieve using the bucket
			$region = $this->get_bucket_region( $s3object['bucket'] );
			if ( is_wp_error( $region ) ) {
				return $region;
			}

			$s3object['region'] = $region;

			if ( ! is_null( $post_id ) ) {
				// retrospectively update s3 metadata with region
				update_post_meta( $post_id, 'amazonS3_info', $s3object );
			}
		}

		return $s3object['region'];
	}

	/**
	 * Translate older bucket locations to newer S3 region names
	 * http://docs.aws.amazon.com/general/latest/gr/rande.html#s3_region
	 *
	 * @param $region
	 *
	 * @return string
	 */
	function translate_region( $region ) {
		if ( ! is_string( $region ) ) {
			// Don't translate any region errors
			return $region;
		}

		$region = strtolower( $region );

		switch ( $region ) {
			case 'eu':
				$region = 'eu-west-1';
				break;
		}

		return $region;
	}

	/**
	 * AJAX handler for get_buckets()
	 */
	function ajax_get_buckets() {
		$this->verify_ajax_request();

		$result = $this->get_buckets();
		if ( is_wp_error( $result ) ) {
			$out = $this->prepare_bucket_error( $result, false );
		} else {
			$out = array(
				'success' => '1',
				'buckets' => $result,
			);
		}

		$this->end_ajax( $out );
	}

	/**
	 * Get a list of buckets from S3
	 *
	 * @return array|WP_Error - list of buckets
	 */
	function get_buckets() {
		try {
			$result = $this->get_s3client()->listBuckets();
		}
		catch ( Exception $e ) {
			return new WP_Error( 'exception', $e->getMessage() );
		}

		return $result['Buckets'];
	}

	/**
	 * Checks the user has write permission for S3
	 *
	 * @param string $bucket
	 * @param string $region
	 *
	 * @return bool|WP_Error
	 */
	function check_write_permission( $bucket = null, $region = null ) {
		if ( is_null( $bucket ) ) {
			if ( ! ( $bucket = $this->get_setting( 'bucket' ) ) ) {
				// if no bucket set then no need check
				return true;
			}
		}

		if ( isset( self::$buckets_check[ $bucket ] ) ) {
			return self::$buckets_check[ $bucket ];
		}

		$file_name     = 'as3cf-permission-check.txt';
		$file_contents = __( 'This is a test file to check if the user has write permission to S3. Delete me if found.', 'amazon-s3-and-cloudfront' );

		$path = $this->get_object_prefix();
		$key  = $path . $file_name;

		$args = array(
			'Bucket' => $bucket,
			'Key'    => $key,
			'Body'   => $file_contents,
			'ACL'    => 'public-read',
		);

		try {
			// need to set region for buckets in non default region
			if ( is_null( $region ) ) {
				$region = $this->get_setting( 'region' );

				if ( is_wp_error( $region ) ) {
					return $region;
				}
			}
			// attempt to create the test file
			$this->get_s3client( $region, true )->putObject( $args );
			// delete it straight away if created
			$this->get_s3client()->deleteObject( array(
				'Bucket' => $bucket,
				'Key'    => $key,
			) );
			$can_write = true;
		} catch ( Exception $e ) {
			// if we encounter an error that isn't access denied, throw that error
			if ( ! $e instanceof Aws\Common\Exception\ServiceResponseException || 'AccessDenied' !== $e->getExceptionCode() ) {
				$error_msg = sprintf( __( 'There was an error attempting to check the permissions of the bucket %s: %s', 'amazon-s3-and-cloudfront' ), $bucket, $e->getMessage() );
				error_log( $error_msg );

				return new WP_Error( 'exception', $error_msg );
			}

			// write permission not found
			$can_write = false;
		}

		self::$buckets_check[ $bucket ] = $can_write;

		return $can_write;
	}

	/**
	 * Render error messages in a view for bucket permission and access issues
	 */
	function render_bucket_permission_errors() {
		$can_write = $this->check_write_permission();
		// catch any checking issues
		if ( is_wp_error( $can_write ) ) {
			$this->render_view( 'error-fatal', array( 'message' => $can_write->get_error_message() ) );
			$can_write = true;
		}
		// display a error message if the user does not have write permission to S3 bucket
		$this->render_view( 'error-access', array( 'can_write' => $can_write ) );
	}

	/**
	 * Register modal scripts and styles so they can be enqueued later
	 */
	function register_modal_assets() {
		$version = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : $this->plugin_version;
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		$src = plugins_url( 'assets/css/modal.css', $this->plugin_file_path );
		wp_register_style( 'as3cf-modal', $src, array(), $version );

		$src = plugins_url( 'assets/js/modal' . $suffix . '.js', $this->plugin_file_path );
		wp_register_script( 'as3cf-modal', $src, array( 'jquery' ), $version, true );
	}

	function plugin_load() {
		$version = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : $this->plugin_version;
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		$src = plugins_url( 'assets/css/styles.css', $this->plugin_file_path );
		wp_enqueue_style( 'as3cf-styles', $src, array( 'as3cf-modal' ), $version );

		$src = plugins_url( 'assets/js/script' . $suffix . '.js', $this->plugin_file_path );
		wp_enqueue_script( 'as3cf-script', $src, array( 'jquery', 'as3cf-modal' ), $version, true );

		wp_localize_script( 'as3cf-script',
			'as3cf',
			array(
				'strings'         => array(
					'create_bucket_error'         => __( 'Error creating bucket', 'amazon-s3-and-cloudfront' ),
					'create_bucket_name_short'    => __( 'Bucket name too short.', 'amazon-s3-and-cloudfront' ),
					'create_bucket_name_long'     => __( 'Bucket name too long.', 'amazon-s3-and-cloudfront' ),
					'create_bucket_invalid_chars' => __( 'Invalid character. Bucket names can contain lowercase letters, numbers, periods and hyphens.', 'amazon-s3-and-cloudfront' ),
					'save_bucket_error'           => __( 'Error saving bucket', 'amazon-s3-and-cloudfront' ),
					'get_buckets_error'           => __( 'Error fetching buckets', 'amazon-s3-and-cloudfront' ),
					'get_url_preview_error'       => __( 'Error getting URL preview: ', 'amazon-s3-and-cloudfront' ),
					'save_alert'                  => __( 'The changes you made will be lost if you navigate away from this page', 'amazon-s3-and-cloudfront' )
				),
				'nonces'          => array(
					'create_bucket'   => wp_create_nonce( 'as3cf-create-bucket' ),
					'manual_bucket'   => wp_create_nonce( 'as3cf-manual-save-bucket' ),
					'get_buckets'     => wp_create_nonce( 'as3cf-get-buckets' ),
					'save_bucket'     => wp_create_nonce( 'as3cf-save-bucket' ),
					'get_url_preview' => wp_create_nonce( 'as3cf-get-url-preview' ),
				),
				'is_pro'          => $this->is_pro(),
				'aws_bucket_link' => $this->get_aws_bucket_link(),
			)
		);

		$this->handle_post_request();
		$this->http_prepare_download_log();
		$this->check_for_gd_imagick();

		do_action( 'as3cf_plugin_load' );
	}

	/**
	 * Whitelist of settings allowed to be saved
	 *
	 * @return array
	 */
	function get_settings_whitelist() {
		return array(
			'bucket',
			'region',
			'domain',
			'virtual-host',
			'expires',
			'permissions',
			'cloudfront',
			'object-prefix',
			'copy-to-s3',
			'serve-from-s3',
			'remove-local-file',
			'ssl',
			'hidpi-images',
			'object-versioning',
			'use-yearmonth-folders',
			'enable-object-prefix',
		);
	}

	/**
	 * Handle the saving of the settings page
	 */
	function handle_post_request() {
		if ( empty( $_POST['plugin'] ) || $this->get_plugin_slug() != sanitize_key( $_POST['plugin'] ) ) { // input var okay
			return;
		}

		if ( empty( $_POST['action'] ) || 'save' != sanitize_key( $_POST['action'] ) ) { // input var okay
			return;
		}

		if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['_wpnonce'] ), $this->get_settings_nonce_key() ) ) { // input var okay
			die( __( "Cheatin' eh?", 'amazon-s3-and-cloudfront' ) );
		}

		do_action( 'as3cf_pre_save_settings' );

		$post_vars = $this->get_settings_whitelist();

		foreach ( $post_vars as $var ) {
			$this->remove_setting( $var );

			if ( ! isset( $_POST[ $var ] ) ) { // input var okay
				continue;
			}

			$value = sanitize_text_field( $_POST[ $var ] ); // input var okay

			$this->set_setting( $var, $value );
		}

		$this->save_settings();

		$url = $this->get_plugin_page_url( array( 'updated' => '1' ) );
		wp_redirect( $url );
		exit;
	}

	/**
	 * Helper method to return the settings page URL for the plugin
	 *
	 * @param array  $args
	 * @param string $url_method To prepend to admin_url()
	 * @param bool   $escape     Should we escape the URL
	 *
	 * @return string
	 */
	function get_plugin_page_url( $args = array(), $url_method = 'network', $escape = true ) {
		$default_args = array(
			'page' => self::$plugin_page,
		);

		$args = array_merge( $args, $default_args );

		switch ( $url_method ) {
			case 'self':
				$base_url = self_admin_url( 'admin.php' );
				break;
			case '':
				$base_url = admin_url( 'admin.php' );
				break;
			default:
				$base_url = network_admin_url( 'admin.php' );
		}

		// Add a hash to the URL
		$hash = false;
		if ( isset( $args['hash'] ) ) {
			$hash = $args['hash'];
			unset( $args['hash'] );
		} else if ( $this->default_tab ) {
			$hash = $this->default_tab;
		}

		$url = add_query_arg( $args, $base_url );

		if ( $hash ) {
			$url .= '#' . $hash;
		}

		if ( $escape ) {
			$url = esc_url_raw( $url );
		}

		return $url;
	}

	/**
	 * Display the main settings page for the plugin
	 */
	function render_page() {
		$this->aws->render_view( 'header', array( 'page_title' => $this->get_plugin_page_title(), 'page' => 'as3cf' ) );

		$aws_client = $this->aws->get_client();

		if ( is_wp_error( $aws_client ) ) {
			$this->render_view( 'error-fatal', array( 'message' => $aws_client->get_error_message() ) );
		}
		else {
			$this->render_view( 'settings-tabs' );
			do_action( 'as3cf_pre_settings_render' );
			$this->render_view( 'settings' );
			do_action( 'as3cf_post_settings_render' );
		}

		$this->aws->render_view( 'footer' );
	}

	/**
	 * Get the tabs available for the plugin settings page
	 *
	 * @return array
	 */
	function get_settings_tabs() {
		$tabs = array(
			'media'   => _x( 'Media Library', 'Show the media library tab', 'amazon-s3-and-cloudfront' ),
			'support' => _x( 'Support', 'Show the support tab', 'amazon-s3-and-cloudfront' )
		);

		return apply_filters( 'as3cf_settings_tabs', $tabs );
	}

	/**
	 * Get the prefix path for the files
	 *
	 * @param string $time
	 *
	 * @return string
	 */
	function get_dynamic_prefix( $time = null ) {
		$prefix = '';
		if ( $this->get_setting( 'use-yearmonth-folders' ) ) {
			$uploads = wp_upload_dir( $time );
			$prefix  = str_replace( $this->get_base_upload_path(), '', $uploads['path'] );
		}

		// support legacy MS installs (<3.5 since upgraded) for subsites
		if ( is_multisite() && ! ( is_main_network() && is_main_site() ) && false === strpos( $prefix, 'sites/' ) ) {
			$details          = get_blog_details( get_current_blog_id() );
			$legacy_ms_prefix = 'sites/' . $details->blog_id . '/';
			$legacy_ms_prefix = apply_filters( 'as3cf_legacy_ms_subsite_prefix', $legacy_ms_prefix, $details );
			$prefix           = '/' . trailingslashit( ltrim( $legacy_ms_prefix, '/' ) ) . ltrim( $prefix, '/' );
		}

		return $prefix;
	}

	/**
	 * Get the base upload path
	 * without the multisite subdirectory
	 *
	 * @return string
	 */
	function get_base_upload_path() {
		if ( defined( 'UPLOADS' ) && ! ( is_multisite() && get_site_option( 'ms_files_rewriting' ) ) ) {
			return ABSPATH . UPLOADS;
		}

		$upload_path = trim( get_option( 'upload_path' ) );

		if ( empty( $upload_path ) || 'wp-content/uploads' == $upload_path ) {
			return WP_CONTENT_DIR . '/uploads';
		} elseif ( 0 !== strpos( $upload_path, ABSPATH ) ) {
			// $dir is absolute, $upload_path is (maybe) relative to ABSPATH
			return path_join( ABSPATH, $upload_path );
		} else {
			return $upload_path;
		}
	}

	/**
	 * Get all the blog IDs for the multisite network used for table prefixes
	 *
	 * @return array
	 */
	function get_blog_ids() {
		$args = array(
			'limit'    => false,
			'spam'     => 0,
			'deleted'  => 0,
			'archived' => 0,
		);
		$blogs = wp_get_sites( $args );

		$blog_ids = array();
		foreach ( $blogs as $blog ) {
			$blog_ids[] = $blog['blog_id'];
		}

		return $blog_ids;
	}

	/**
	 * Check whether the pro addon is installed.
	 *
	 * @return bool
	 */
	function is_pro() {
		if ( ! class_exists( 'Amazon_S3_And_CloudFront_Pro' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the link to the bucket on the AWS console
	 *
	 * @param string $bucket
	 * @param string $prefix
	 *
	 * @return string
	 */
	function get_aws_bucket_link( $bucket = '', $prefix = '' ) {
		if ( '' !== $prefix ) {
			$prefix = '&prefix=' . urlencode( $prefix );
		}

		return 'https://console.aws.amazon.com/s3/home?bucket=' . $bucket . $prefix;
	}

	/**
	 * Apply ACL to an attachment and associated files
	 *
	 * @param int    $post_id
	 * @param array  $s3object
	 * @param string $acl
	 */
	function set_attachment_acl_on_s3( $post_id, $s3object, $acl ) {
		// set ACL as private
		$args = array(
			'ACL'    => $acl,
			'Bucket' => $s3object['bucket'],
			'Key'    => $s3object['key'],
		);

		$region   = ( isset( $s3object['region'] ) ) ? $s3object['region'] : false;
		$s3client = $this->get_s3client( $region, true );

		try {
			$s3client->PutObjectAcl( $args );
			$s3object['acl'] = $acl;

			// Add attachment to ACL update notice
			$message = $this->make_acl_admin_notice_text( $s3object );
			$this->notices->add_notice( $message );

			// update S3 meta data
			if ( $acl == self::DEFAULT_ACL ) {
				unset( $s3object['acl'] );
			}
			update_post_meta( $post_id, 'amazonS3_info', $s3object );
		} catch ( Exception $e ) {
			error_log( 'Error setting ACL to ' . $acl . ' for ' . $s3object['key'] . ': ' . $e->getMessage() );
		}
	}

	/**
	 * Make admin notice text for when object ACL has changed
	 *
	 * @param array $s3object
	 *
	 * @return string
	 */
	function make_acl_admin_notice_text( $s3object ) {
		$filename = basename( $s3object['key'] );
		$acl      = $this->get_acl_display_name( $s3object['acl'] );

		return sprintf( __( 'The file %s has been given %s permissions on Amazon S3.', 'amazon-s3-and-cloudfront' ), "<strong>{$filename}</strong>", "<strong>{$acl}</strong>" );
	}

	/**
	 * Check if PHP GD and Imagick is installed
	 */
	function check_for_gd_imagick() {
		$gd_enabled      = $this->gd_enabled();
		$imagick_enabled = $this->imagick_enabled();

		if( ! $gd_enabled && ! $imagick_enabled ) {
			$this->notices->add_notice(
				__( '<strong>Image Manipulation Library Missing</strong> &mdash; Looks like you don\'t have an image manipulation library installed on this server and configured with PHP. You may run into trouble if you try to edit images. Please setup GD or ImageMagick.', 'amazon-s3-and-cloudfront' ),
				array( 'flash' => false, 'only_show_to_user' => false, 'only_show_in_settings' => true )
			);
		}
	}

	/**
	 * Diagnostic information for the support tab
	 *
	 * @param bool $escape
	 */
	function output_diagnostic_info( $escape = true ) {
		global $table_prefix;
		global $wpdb;

		echo 'site_url(): ';
		echo esc_html( site_url() );
		echo "\r\n";

		echo 'home_url(): ';
		echo esc_html( home_url() );
		echo "\r\n";

		echo 'Database Name: ';
		echo esc_html( $wpdb->dbname );
		echo "\r\n";

		echo 'Table Prefix: ';
		echo esc_html( $table_prefix );
		echo "\r\n";

		echo 'WordPress: ';
		echo bloginfo( 'version' );
		if ( is_multisite() ) {
			echo ' Multisite';
		}
		echo "\r\n";

		echo 'Web Server: ';
		echo esc_html( ! empty( $_SERVER['SERVER_SOFTWARE'] ) ? $_SERVER['SERVER_SOFTWARE'] : '' );
		echo "\r\n";

		echo 'PHP: ';
		if ( function_exists( 'phpversion' ) ) {
			echo esc_html( phpversion() );
		}
		echo "\r\n";

		echo 'MySQL: ';
		echo esc_html( empty( $wpdb->use_mysqli ) ? mysql_get_server_info() : mysqli_get_server_info( $wpdb->dbh ) );
		echo "\r\n";

		echo 'ext/mysqli: ';
		echo empty( $wpdb->use_mysqli ) ? 'no' : 'yes';
		echo "\r\n";

		echo 'PHP Memory Limit: ';
		if ( function_exists( 'ini_get' ) ) {
			echo esc_html( ini_get( 'memory_limit' ) );
		}
		echo "\r\n";

		echo 'WP Memory Limit: ';
		echo esc_html( WP_MEMORY_LIMIT );
		echo "\r\n";

		echo 'Blocked External HTTP Requests: ';
		if ( ! defined( 'WP_HTTP_BLOCK_EXTERNAL' ) || ! WP_HTTP_BLOCK_EXTERNAL ) {
			echo 'None';
		} else {
			$accessible_hosts = ( defined( 'WP_ACCESSIBLE_HOSTS' ) ) ? WP_ACCESSIBLE_HOSTS : '';

			if ( empty( $accessible_hosts ) ) {
				echo 'ALL';
			} else {
				echo 'Partially (Accessible Hosts: ' . esc_html( $accessible_hosts ) . ')';
			}
		}
		echo "\r\n";

		echo 'WP Locale: ';
		echo esc_html( get_locale() );
		echo "\r\n";

		echo 'Debug Mode: ';
		echo esc_html( ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? 'Yes' : 'No' );
		echo "\r\n";

		echo 'WP Max Upload Size: ';
		echo esc_html( size_format( wp_max_upload_size() ) );
		echo "\r\n";

		echo 'PHP Time Limit: ';
		if ( function_exists( 'ini_get' ) ) {
			echo esc_html( ini_get( 'max_execution_time' ) );
		}
		echo "\r\n";

		echo 'PHP Error Log: ';
		if ( function_exists( 'ini_get' ) ) {
			echo esc_html( ini_get( 'error_log' ) );
		}
		echo "\r\n";

		echo 'WP Cron: ';
		echo esc_html( ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) ? 'Disabled' : 'Enabled' );
		echo "\r\n";

		echo 'fsockopen: ';
		if ( function_exists( 'fsockopen' ) ) {
			echo 'Enabled';
		} else {
			echo 'Disabled';
		}
		echo "\r\n";

		echo 'OpenSSL: ';
		if ( $this->open_ssl_enabled() ) {
			echo esc_html( OPENSSL_VERSION_TEXT );
		} else {
			echo 'Disabled';
		}
		echo "\r\n";

		echo 'cURL: ';
		if ( function_exists( 'curl_init' ) ) {
			echo 'Enabled';
		} else {
			echo 'Disabled';
		}
		echo "\r\n";

		echo 'Zlib Compression: ';
		if ( function_exists( 'gzcompress' ) ) {
			echo 'Enabled';
		} else {
			echo 'Disabled';
		}
		echo "\r\n";

		echo 'PHP GD: ';
		if ( $this->gd_enabled() ) {
			$gd_info = gd_info();
			echo isset( $gd_info['GD Version'] ) ? esc_html( $gd_info['GD Version'] ) : 'Enabled';
		} else {
			echo 'Disabled';
		}
		echo "\r\n";

		echo 'Imagick: ';
		if ( $this->imagick_enabled() ) {
			echo 'Enabled';
		} else {
			echo 'Disabled';
		}
		echo "\r\n\r\n";

		$media_counts = $this->diagnostic_media_counts();

		echo 'Media Files: ';
		echo number_format_i18n( $media_counts['all'] );
		echo "\r\n";

		echo 'Media Files on S3: ';
		echo number_format_i18n( $media_counts['s3'] );
		echo "\r\n";

		echo 'Number of Image Sizes: ';
		$sizes = count( get_intermediate_image_sizes() );
		echo number_format_i18n( $sizes );
		echo "\r\n\r\n";

		echo 'Bucket: ';
		echo $this->get_setting( 'bucket' );
		echo "\r\n";
		echo 'Region: ';
		$region = $this->get_setting( 'region' );
		if ( ! is_wp_error( $region ) ) {
			echo $region;
		}
		echo "\r\n";
		echo 'Copy Files to S3: ';
		echo $this->on_off( 'copy-to-s3' );
		echo "\r\n";
		echo 'Rewrite File URLs: ';
		echo $this->on_off( 'serve-from-s3' );
		echo "\r\n";
		echo "\r\n";

		echo 'URL Preview: ';
		echo $this->get_url_preview( $escape );
		echo "\r\n";
		echo "\r\n";

		echo 'Domain: ';
		echo $this->get_setting( 'domain' );
		echo "\r\n";
		echo 'Enable Path: ';
		echo $this->on_off( 'enable-object-prefix' );
		echo "\r\n";
		echo 'Custom Path: ';
		echo $this->get_setting( 'object-prefix' );
		echo "\r\n";
		echo 'Use Year/Month: ';
		echo $this->on_off( 'use-yearmonth-folders' );
		echo "\r\n";
		echo 'SSL: ';
		echo $this->get_setting( 'ssl' );
		echo "\r\n";
		echo 'Remove Files From Server: ';
		echo $this->on_off( 'remove-local-file' );
		echo "\r\n";
		echo 'Object Versioning: ';
		echo $this->on_off( 'object-versioning' );
		echo "\r\n";
		echo 'Far Future Expiration Header: ';
		echo $this->on_off( 'expires' );
		echo "\r\n";
		echo 'Copy HiDPI (@2x) Images: ';
		echo $this->on_off( 'hidpi-images' );
		echo "\r\n\r\n";

		do_action( 'as3cf_diagnostic_info' );
		if ( has_action( 'as3cf_diagnostic_info' ) ) {
			echo "\r\n";
		}

		echo "Active Plugins:\r\n";
		$active_plugins = (array) get_option( 'active_plugins', array() );
		$plugin_details = array();

		if ( is_multisite() ) {
			$network_active_plugins = wp_get_active_network_plugins();
			$active_plugins = array_map( array( $this, 'remove_wp_plugin_dir' ), $network_active_plugins );
		}

		foreach ( $active_plugins as $plugin ) {
			$plugin_details[] = $this->get_plugin_details( WP_PLUGIN_DIR . '/' . $plugin );
		}

		asort( $plugin_details );
		echo implode( '', $plugin_details );

		$mu_plugins = wp_get_mu_plugins();
		if ( $mu_plugins ) {
			$mu_plugin_details = array();
			echo "\r\n";
			echo "Must-use Plugins:\r\n";

			foreach ( $mu_plugins as $mu_plugin ) {
				$mu_plugin_details[] = $this->get_plugin_details( $mu_plugin );
			}

			asort( $mu_plugin_details );
			echo implode( '', $mu_plugin_details );
		}
	}

	/**
	 * Helper for displaying settings
	 *
	 * @param string $key setting key
	 *
	 * @return string
	 */
	function on_off( $key ) {
		$value = $this->get_setting( $key, 0 );

		return ( 1 == $value ) ? 'On' : 'Off';
	}

	/**
	 * Helper to display plugin details
	 *
	 * @param string $plugin_path
	 * @param string $suffix
	 *
	 * @return string
	 */
	function get_plugin_details( $plugin_path, $suffix = '' ) {
		$plugin_data = get_plugin_data( $plugin_path );
		if ( empty( $plugin_data['Name'] ) ) {
			return basename( $plugin_path );
		}

		return sprintf( "%s%s (v%s) by %s\r\n", $plugin_data['Name'], $suffix, $plugin_data['Version'], strip_tags( $plugin_data['AuthorName'] ) );
	}

	/**
	 * Helper to remove the plugin directory from the plugin path
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	function remove_wp_plugin_dir( $path ) {
		$plugin = str_replace( WP_PLUGIN_DIR, '', $path );

		return substr( $plugin, 1 );
	}

	/**
	 * Check for as3cf-download-log and related nonce and if found begin the
	 * download of the diagnostic log
	 *
	 * @return void
	 */
	function http_prepare_download_log() {
		if ( isset( $_GET['as3cf-download-log'] ) && wp_verify_nonce( $_GET['nonce'], 'as3cf-download-log' ) ) {
			ob_start();
			$this->output_diagnostic_info( false );
			$log      = ob_get_clean();
			$url      = parse_url( home_url() );
			$host     = sanitize_file_name( $url['host'] );
			$filename = sprintf( '%s-diagnostic-log-%s.txt', $host, date( 'YmdHis' ) );
			header( 'Content-Description: File Transfer' );
			header( 'Content-Type: application/octet-stream' );
			header( 'Content-Length: ' . strlen( $log ) );
			header( 'Content-Disposition: attachment; filename=' . $filename );
			echo $log;
			exit;
		}
	}

	/**
	 * Return human friendly ACL name
	 *
	 * @param string $acl
	 *
	 * @return string
	 */
	function get_acl_display_name( $acl ) {
		return ucwords( str_replace( '-', ' ', $acl ) );
	}

	/**
	 * Detect if OpenSSL is enabled
	 *
	 * @return bool
	 */
	function open_ssl_enabled() {
		if ( defined( 'OPENSSL_VERSION_TEXT' ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Detect if PHP GD is enabled
	 *
	 * @return bool
	 */
	function gd_enabled() {
		if ( extension_loaded( 'gd' ) && function_exists( 'gd_info' ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Detect is Imagick is enabled
	 *
	 * @return bool
	 */
	function imagick_enabled() {
		if ( extension_loaded( 'imagick' ) && class_exists( 'Imagick' ) && class_exists( 'ImagickPixel' ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Is the current blog ID that specified in wp-config.php
	 *
	 * @param int $blog_id
	 *
	 * @return bool
	 */
	function is_current_blog( $blog_id ) {
		$default = defined( 'BLOG_ID_CURRENT_SITE' ) ? BLOG_ID_CURRENT_SITE : 1;

		if ( $default === $blog_id ) {
			return true;
		}

		return false;
	}

	/**
	 * Helper to switch to a Multisite blog
	 *  - If the site is MS
	 *  - If the blog is not the current blog defined
	 *
	 * @param $blog_id
	 */
	function switch_to_blog( $blog_id ) {
		if ( is_multisite() && ! $this->is_current_blog( $blog_id ) ) {
			switch_to_blog( $blog_id );
		}
	}

	/**
	 * Helper to restore to the current Multisite blog
	 *  - If the site is MS
	 *  - If the blog is not the current blog defined
	 *
	 * @param $blog_id
	 */
	function restore_current_blog( $blog_id ) {
		if ( is_multisite() && ! $this->is_current_blog( $blog_id ) ) {
			restore_current_blog();
		}
	}

	/**
	 * Get all the table prefixes for the blogs in the site. MS compatible
	 *
	 * @param array $exclude_blog_ids blog ids to exclude
	 *
	 * @return array associative array with blog ID as key, prefix as value
	 */
	function get_all_blog_table_prefixes( $exclude_blog_ids = array() ) {
		global $wpdb;
		$prefix = $wpdb->prefix;

		$table_prefixes = array();

		if ( ! in_array( 1, $exclude_blog_ids ) ) {
			$table_prefixes[1] = $prefix;
		}

		if ( is_multisite() ) {
			$blog_ids = $this->get_blog_ids();
			foreach ( $blog_ids as $blog_id ) {
				if ( in_array( $blog_id, $exclude_blog_ids ) ) {
					continue;
				}
				$table_prefixes[ $blog_id ] = $wpdb->get_blog_prefix( $blog_id );
			}
		}

		return $table_prefixes;
	}

	/**
	 * Get file paths for all attachment versions.
	 *
	 * @param int        $attachment_id
	 * @param bool       $exists_locally
	 * @param array|bool $meta
	 * @param bool       $include_backups
	 *
	 * @return array
	 */
	public function get_attachment_file_paths( $attachment_id, $exists_locally = true, $meta = false, $include_backups = true ) {
		$paths     = array();
		$file_path = get_attached_file( $attachment_id, true );
		$file_name = basename( $file_path );
		$backups   = get_post_meta( $attachment_id, '_wp_attachment_backup_sizes', true );

		if ( ! $meta ) {
			$meta = get_post_meta( $attachment_id, '_wp_attachment_metadata', true );
		}

		$original_file = $file_path; // Not all attachments will have meta

		if ( isset( $meta['file'] ) ) {
			$original_file = str_replace( $file_name, basename( $meta['file'] ), $file_path );
		}

		// Original file
		$paths[] = $original_file;

		// Sizes
		if ( isset( $meta['sizes'] ) ) {
			foreach ( $meta['sizes'] as $size ) {
				if ( isset( $size['file'] ) ) {
					$paths[] = str_replace( $file_name, $size['file'], $file_path );
				}
			}
		}

		// Thumb
		if ( isset( $meta['thumb'] ) ) {
			$paths[] = str_replace( $file_name, $meta['thumb'], $file_path );
		}

		// Backups
		if ( $include_backups && is_array( $backups ) ) {
			foreach ( $backups as $backup ) {
				$paths[] = str_replace( $file_name, $backup['file'], $file_path );
			}
		}

		// HiDPI
		if ( $this->get_setting( 'hidpi-images' ) ) {
			foreach ( $paths as $path ) {
				$paths[] = $this->get_hidpi_file_path( $path );
			}
		}

		// Allow other processes to add files to be uploaded
		$paths = apply_filters( 'as3cf_attachment_file_paths', $paths, $attachment_id, $meta );

		// Remove duplicates
		$paths = array_unique( $paths );

		// Remove paths that don't exist
		if ( $exists_locally ) {
			foreach ( $paths as $key => $path ) {
				if ( ! file_exists( $path ) ) {
					unset( $paths[ $key ] );
				}
			}
		}

		return $paths;
	}

	/**
	 * Get the access denied bucket error notice message
	 *
	 * @param bool $single
	 *
	 * @return string
	 */
	function get_access_denied_notice_message( $single = true ) {
		$quick_start = sprintf( '<a class="js-link" href="%s">%s</a>', 'https://deliciousbrains.com/wp-offload-s3/doc/quick-start-guide/', __( 'Quick Start Guide', 'amazon-s3-and-cloudfront' ) );

		$message = sprintf( __( "Looks like we don't have write access to this bucket. It's likely that the user you've provided access keys for hasn't been granted the correct permissions. Please see our %s for instructions on setting up permissions correctly.", 'amazon-s3-and-cloudfront' ), $quick_start );
		if ( ! $single ) {
			$message = sprintf( __( "Looks like we don't have access to the buckets. It's likely that the user you've provided access keys for hasn't been granted the correct permissions. Please see our %s for instructions on setting up permissions correctly.", 'amazon-s3-and-cloudfront' ), $quick_start );

		}

		return $message;
	}

	/**
	 * Used to give a realistic total of storage space used on a Multisite subsite,
	 * when there have been attachments uploaded to S3 but removed from server
	 *
	 * @param $space_used bool
	 *
	 * @return float|int
	 */
	function multisite_get_spaced_used( $space_used ) {
		global $wpdb;

		// Sum the total file size (including image sizes) for all S3 attachments
		$sql = "SELECT SUM( meta_value ) AS bytes_total
				FROM {$wpdb->postmeta}
				WHERE meta_key = 'wpos3_filesize_total'";

		$space_used = $wpdb->get_var( $sql );

		// Get local upload sizes
		$upload_dir = wp_upload_dir();
		$space_used += get_dirsize( $upload_dir['basedir'] );

		if ( $space_used > 0 ) {
			// Convert to bytes to MB
			$space_used = $space_used / 1024 / 1024;
		}

		return $space_used;
	}

	/**
	 * Memory exceeded
	 *
	 * Ensures the a process never exceeds 90% of the maximum WordPress memory.
	 *
	 * @param null|string $filter_name Name of filter to apply to the return
	 *
	 * @return bool
	 */
	public function memory_exceeded( $filter_name = null ) {
		$memory_limit   =  $this->get_memory_limit() * 0.9; // 90% of max memory
		$current_memory = memory_get_usage( true );
		$return         = false;

		if ( $current_memory >= $memory_limit ) {
			$return = true;
		}

		if ( is_null( $filter_name ) || ! is_string( $filter_name ) ) {
			return $return;
		}

		return apply_filters( $filter_name, $return );
	}

	/**
	 * Get memory limit
	 *
	 * @return int
	 */
	public function get_memory_limit() {
		if ( function_exists( 'ini_get' ) ) {
			$memory_limit = ini_get( 'memory_limit' );
		} else {
			// Sensible default
			$memory_limit = '128M';
		}

		if ( ! $memory_limit || -1 == $memory_limit ) {
			// Unlimited, set to 32GB
			$memory_limit = '32000M';
		}

		return intval( $memory_limit ) * 1024 * 1024;
	}

	/**
	 * Count attachments on a site
	 *
	 * @param string    $prefix
	 * @param null|bool $uploaded_to_s3
	 *      null  - All attachments
	 *      true  - Attachments only uploaded to S3
	 *      false - Attachments not uploaded to S3
	 *
	 * @return null|string
	 */
	public function count_attachments( $prefix, $uploaded_to_s3 = null ) {
		global $wpdb;

		$sql = "SELECT COUNT(*)
				FROM `{$prefix}posts` p";

		$where = "WHERE p.post_type = 'attachment'";

		if ( ! is_null( $uploaded_to_s3 ) && is_bool( $uploaded_to_s3 ) ) {
			$sql .= " LEFT OUTER JOIN `{$prefix}postmeta` pm
					ON p.`ID` = pm.`post_id`
					AND pm.`meta_key` = 'amazonS3_info'";

			$operator = $uploaded_to_s3 ? 'not ' : '';
			$where .= " AND pm.`post_id` is {$operator}null";
		}

		$sql .= ' ' . $where;

		return $wpdb->get_var( $sql );
	}

	/**
	 * Get the total attachment and total S3 attachment counts for the diagnostic log
	 *
	 * @return array
	 */
	protected function diagnostic_media_counts() {
		if ( false === ( $attachment_counts = get_site_transient( 'wpos3_attachment_counts' ) ) ) {
			$table_prefixes = $this->get_all_blog_table_prefixes();
			$all_media      = 0;
			$all_media_s3   = 0;

			foreach ( $table_prefixes as $blog_id => $table_prefix ) {
				$count = $this->count_attachments( $table_prefix );
				$all_media += $count;
				$s3_count = $this->count_attachments( $table_prefix, true );
				$all_media_s3 += $s3_count;
			}

			$attachment_counts = array(
				'all' => $all_media,
				's3'  => $all_media_s3,
			);

			set_site_transient( 'wpos3_attachment_counts', $attachment_counts, 2 * HOUR_IN_SECONDS );
		}

		return $attachment_counts;
	}

	/**
	 * Throw error
	 *
	 * @param string $code
	 * @param string $message
	 *
	 * @return WP_Error
	 */
	public function _throw_error( $code, $message = '' ) {
		return new WP_Error( $code, $message );
	}

	/**
	 * More info link
	 *
	 * @param string $url
	 * @param string $hash
	 * @param bool   $append_campaign
	 *
	 * @return string
	 */
	public function more_info_link( $url, $hash = '', $append_campaign = true ) {
		if ( $append_campaign ) {
			$campaign = $this->is_pro() ? 'os3-pro-plugin' : 'os3-free-plugin';
			$url .= '?utm_source=insideplugin&utm_medium=web&utm_content=more-info&utm_campaign=' . $campaign;
		}

		if ( ! empty( $hash ) ) {
			$url .= '#' . $hash;
		}

		return sprintf( '<a class="more-info" href="%s">%s</a> &raquo;', esc_url( $url ), __( 'More info', 'amazon-s3-and-cloudfront' ) );
	}
}

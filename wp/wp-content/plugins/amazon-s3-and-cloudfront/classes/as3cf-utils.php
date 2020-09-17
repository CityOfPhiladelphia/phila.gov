<?php
/**
 * Plugin Utilities
 *
 * @package     amazon-s3-and-cloudfront
 * @subpackage  Classes/Utils
 * @copyright   Copyright (c) 2015, Delicious Brains
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

use DeliciousBrains\WP_Offload_Media\Items\Media_Library_Item;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'AS3CF_Utils' ) ) {

	/**
	 * AS3CF_Utils Class
	 *
	 * This class contains utility functions that need to be available
	 * across the Pro plugin codebase
	 *
	 */
	class AS3CF_Utils {

		/**
		 * Get post ID.
		 *
		 * @param null|int|WP_Post $post Optional. Post ID or post object. Defaults to current post.
		 *
		 * @return int
		 */
		public static function get_post_id( $post = null ) {
			return (int) get_post_field( 'ID', $post );
		}

		/**
		 * Trailing slash prefix string ensuring no leading slashes.
		 *
		 * @param $string
		 *
		 * @return string
		 */
		public static function trailingslash_prefix( $string ) {
			return static::unleadingslashit( trailingslashit( trim( $string ) ) );
		}

		/**
		 * Ensure string has a leading slash, like in absolute paths.
		 *
		 * @param $string
		 *
		 * @return string
		 */
		public static function leadingslashit( $string ) {
			return '/' . static::unleadingslashit( $string );
		}

		/**
		 * Ensure string has no leading slash, like in relative paths.
		 *
		 * @param $string
		 *
		 * @return string
		 */
		public static function unleadingslashit( $string ) {
			return ltrim( trim( $string ), '/\\' );
		}

		/**
		 * Ensure string has a leading and trailing slash, like in absolute directory paths.
		 *
		 * @param $string
		 *
		 * @return string
		 */
		public static function leadingtrailingslashit( $string ) {
			return static::leadingslashit( trailingslashit( trim( $string ) ) );
		}

		/**
		 * Remove scheme from URL.
		 *
		 * @param string $url
		 *
		 * @return string
		 */
		public static function remove_scheme( $url ) {
			return preg_replace( '/^(?:http|https):/', '', $url );
		}

		/**
		 * Remove size from filename (image[-100x100].jpeg).
		 *
		 * @param string $url
		 * @param bool   $remove_extension
		 *
		 * @return string
		 */
		public static function remove_size_from_filename( $url, $remove_extension = false ) {
			$url = preg_replace( '/^(\S+)-[0-9]{1,4}x[0-9]{1,4}(\.[a-zA-Z0-9\.]{2,})?/', '$1$2', $url );

			if ( $remove_extension ) {
				$ext = pathinfo( $url, PATHINFO_EXTENSION );
				$url = str_replace( ".$ext", '', $url );
			}

			return $url;
		}

		/**
		 * Reduce the given URL down to the simplest version of itself.
		 *
		 * Useful for matching against the full version of the URL in a full-text search
		 * or saving as a key for dictionary type lookup.
		 *
		 * @param string $url
		 *
		 * @return string
		 */
		public static function reduce_url( $url ) {
			$parts = self::parse_url( $url );
			$host  = isset( $parts['host'] ) ? $parts['host'] : '';
			$port  = isset( $parts['port'] ) ? ":{$parts['port']}" : '';
			$path  = isset( $parts['path'] ) ? $parts['path'] : '';

			return '//' . $host . $port . $path;
		}

		/**
		 * Parses a URL into its components. Compatible with PHP < 5.4.7.
		 *
		 * @param string $url       The URL to parse.
		 *
		 * @param int    $component PHP_URL_ constant for URL component to return.
		 *
		 * @return mixed An array of the parsed components, mixed for a requested component, or false on error.
		 */
		public static function parse_url( $url, $component = -1 ) {
			$url       = trim( $url );
			$no_scheme = 0 === strpos( $url, '//' );

			if ( $no_scheme ) {
				$url = 'http:' . $url;
			}

			$parts = parse_url( $url, $component );

			if ( 0 < $component ) {
				return $parts;
			}

			if ( $no_scheme && is_array( $parts ) ) {
				unset( $parts['scheme'] );
			}

			return $parts;
		}

		/**
		 * Is the string a URL?
		 *
		 * @param string $string
		 *
		 * @return bool
		 */
		public static function is_url( $string ) {
			if ( ! is_string( $string ) ) {
				return false;
			}

			if ( preg_match( '@^(?:https?:)?//[a-zA-Z0-9\-]+@', $string ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Is the string a relative URL?
		 *
		 * @param $string
		 *
		 * @return bool
		 */
		public static function is_relative_url( $string ) {
			if ( empty( $string ) || ! is_string( $string ) ) {
				return false;
			}

			$url = static::parse_url( $string );

			return ( empty( $url['scheme'] ) && empty( $url['host'] ) );
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
		public static function get_attachment_file_paths( $attachment_id, $exists_locally = true, $meta = false, $include_backups = true ) {
			$file_path = get_attached_file( $attachment_id, true );
			$paths     = array(
				'original' => $file_path,
			);

			if ( ! $meta ) {
				$meta = get_post_meta( $attachment_id, '_wp_attachment_metadata', true );
			}

			if ( is_wp_error( $meta ) ) {
				return $paths;
			}

			$file_name = wp_basename( $file_path );

			// If file edited, current file name might be different.
			if ( isset( $meta['file'] ) ) {
				$paths['file'] = str_replace( $file_name, wp_basename( $meta['file'] ), $file_path );
			}

			// Thumb
			if ( isset( $meta['thumb'] ) ) {
				$paths['thumb'] = str_replace( $file_name, $meta['thumb'], $file_path );
			}

			// Original Image (when large image scaled down to threshold size and used as "full").
			if ( isset( $meta['original_image'] ) ) {
				$paths['original_image'] = str_replace( $file_name, $meta['original_image'], $file_path );
			}

			// Sizes
			if ( isset( $meta['sizes'] ) ) {
				foreach ( $meta['sizes'] as $size => $file ) {
					if ( isset( $file['file'] ) ) {
						$paths[ $size ] = str_replace( $file_name, $file['file'], $file_path );
					}
				}
			}

			$backups = get_post_meta( $attachment_id, '_wp_attachment_backup_sizes', true );

			// Backups
			if ( $include_backups && is_array( $backups ) ) {
				foreach ( $backups as $size => $file ) {
					if ( isset( $file['file'] ) ) {
						$paths[ $size ] = str_replace( $file_name, $file['file'], $file_path );
					}
				}
			}

			// Allow other processes to add files to be uploaded
			$paths = apply_filters( 'as3cf_attachment_file_paths', $paths, $attachment_id, $meta );

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
		 * Get an attachment's edited file paths.
		 *
		 * @param int $attachment_id
		 *
		 * @return array
		 */
		public static function get_attachment_edited_file_paths( $attachment_id ) {
			$paths = self::get_attachment_file_paths( $attachment_id, false );
			$paths = array_filter( $paths, function ( $path ) {
				return preg_match( '/-e[0-9]{13}(?:-[0-9]{1,4}x[0-9]{1,4})?\./', wp_basename( $path ) );
			} );

			return $paths;
		}

		/**
		 * Get an attachment's edited S3 keys.
		 *
		 * @param int                $attachment_id
		 * @param Media_Library_Item $as3cf_item
		 *
		 * @return array
		 */
		public static function get_attachment_edited_keys( $attachment_id, Media_Library_Item $as3cf_item ) {
			$paths = self::get_attachment_edited_file_paths( $attachment_id );
			$paths = array_map( function ( $path ) use ( $as3cf_item ) {
				return array( 'Key' => $as3cf_item->key( wp_basename( $path ) ) );
			}, $paths );

			return $paths;
		}

		/**
		 * Get intermediate size from attachment filename.
		 * If multiple sizes exist with same filename, only the first size found will be returned.
		 *
		 * @param int    $attachment_id
		 * @param string $filename
		 *
		 * @return string
		 */
		public static function get_intermediate_size_from_filename( $attachment_id, $filename ) {
			$sizes = self::get_attachment_file_paths( $attachment_id, false );

			foreach ( $sizes as $size => $file ) {
				if ( wp_basename( $file ) === $filename ) {
					return $size;
				}
			}

			return '';
		}

		/**
		 * Strip edited image suffix and extension from path.
		 *
		 * @param string $path
		 *
		 * @return string
		 */
		public static function strip_image_edit_suffix_and_extension( $path ) {
			$parts    = pathinfo( $path );
			$filename = preg_replace( '/-e[0-9]{13}/', '', $parts['filename'] );

			return str_replace( $parts['basename'], $filename, $path );
		}

		/**
		 * Create a site link for given URL.
		 *
		 * @param string $url
		 * @param string $text
		 *
		 * @return string
		 */
		public static function dbrains_link( $url, $text ) {
			return sprintf( '<a href="%s">%s</a>', esc_url( $url ), esc_html( $text ) );
		}

		/**
		 * Check whether two URLs share the same domain.
		 *
		 * @param string $url_a
		 * @param string $url_b
		 *
		 * @return bool
		 */
		public static function url_domains_match( $url_a, $url_b ) {
			if ( ! static::is_url( $url_a ) || ! static::is_url( $url_b ) ) {
				return false;
			}

			return static::parse_url( $url_a, PHP_URL_HOST ) === static::parse_url( $url_b, PHP_URL_HOST );
		}

		/**
		 * Get the current domain.
		 *
		 * @return string|false
		 */
		public static function current_domain() {
			return parse_url( home_url(), PHP_URL_HOST );
		}

		/**
		 * Get the base domain of the current domain.
		 *
		 * @return string
		 */
		public static function current_base_domain() {
			return static::base_domain( static::current_domain() );
		}

		/**
		 * Get the base domain of the supplied domain.
		 *
		 * @param string $domain
		 *
		 * @return string
		 */
		public static function base_domain( $domain ) {
			if ( WP_Http::is_ip_address( $domain ) ) {
				return $domain;
			}

			$parts = explode( '.', $domain );

			// localhost etc.
			if ( is_string( $parts ) ) {
				return $domain;
			}

			if ( count( $parts ) < 3 ) {
				return $domain;
			}

			// Just knock off the first segment.
			unset( $parts[0] );

			return implode( '.', $parts );
		}

		/**
		 * Very basic check of whether domain is real.
		 *
		 * @param string $domain
		 *
		 * @return bool
		 *
		 * Note: Very early version, may extend with further "local" domain checks if relevant.
		 */
		public static function is_public_domain( $domain ) {
			// We're not going to test SEO etc. for ip addresses.
			if ( WP_Http::is_ip_address( $domain ) ) {
				return false;
			}

			$parts = explode( '.', $domain );

			// localhost etc.
			if ( is_string( $parts ) ) {
				return false;
			}

			// TODO: Maybe check domain TLD.

			return true;
		}

		/**
		 * Is given URL considered SEO friendly?
		 *
		 * @param string $url
		 *
		 * @return bool
		 */
		public static function seo_friendly_url( $url ) {
			$domain      = static::base_domain( parse_url( $url, PHP_URL_HOST ) );
			$base_domain = static::current_base_domain();

			// If either domain is not a public domain then skip checks.
			if ( ! static::is_public_domain( $domain ) || ! static::is_public_domain( $base_domain ) ) {
				return true;
			}

			if ( substr( $domain, -strlen( $base_domain ) ) === $base_domain ) {
				return true;
			}

			return false;
		}

		/**
		 * A safe wrapper for deactivate_plugins()
		 */
		public static function deactivate_plugins() {
			if ( ! function_exists( 'deactivate_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			call_user_func_array( 'deactivate_plugins', func_get_args() );
		}

		/**
		 * Get the first defined constant from the given list of constant names.
		 *
		 * @param array $constants
		 *
		 * @return string|false string constant name if defined, otherwise false if none are defined
		 */
		public static function get_first_defined_constant( $constants ) {
			if ( ! empty( $constants ) ) {
				foreach ( (array) $constants as $constant ) {
					if ( defined( $constant ) ) {
						return $constant;
					}
				}
			}

			return false;
		}

		/**
		 * Ensure returned keys are for correct attachment.
		 *
		 * @param array $keys
		 *
		 * @return array
		 */
		public static function validate_attachment_keys( $attachment_id, $keys ) {
			$paths     = self::get_attachment_file_paths( $attachment_id, false );
			$filenames = array_map( 'wp_basename', $paths );

			foreach ( $keys as $key => $value ) {
				$filename = wp_basename( $value );

				if ( ! in_array( $filename, $filenames ) ) {
					unset( $keys[ $key ] );
				}
			}

			return $keys;
		}

		/**
		 * Sanitize custom domain
		 *
		 * @param string $domain
		 *
		 * @return string
		 */
		public static function sanitize_custom_domain( $domain ) {
			$domain = preg_replace( '@^[a-zA-Z]*:\/\/@', '', $domain );
			$domain = preg_replace( '@[^a-zA-Z0-9\.\-]@', '', $domain );

			return $domain;
		}

		/**
		 * Decode file name in potentially URL encoded URL path.
		 *
		 * @param string $file
		 *
		 * @return string
		 */
		public static function decode_filename_in_path( $file ) {
			$url = parse_url( $file );

			if ( ! isset( $url['path'] ) ) {
				// Can't determine path, return original
				return $file;
			}

			$file_name = wp_basename( $url['path'] );

			if ( false === strpos( $file_name, '%' ) ) {
				// File name not encoded, return original
				return $file;
			}

			$decoded_file_name = rawurldecode( $file_name );

			return str_replace( $file_name, $decoded_file_name, $file );
		}

		/**
		 * Returns indexed array of full size paths, e.g. orig and edited.
		 *
		 * @param array $paths Associative array of sizes and relative paths
		 *
		 * @return array
		 *
		 * @see get_attachment_file_paths
		 */
		public static function fullsize_paths( $paths ) {
			if ( is_array( $paths ) && ! empty( $paths ) ) {
				return array_values( array_unique( array_intersect_key( $paths, array_flip( array( 'original', 'file', 'full-orig', 'original_image' ) ) ) ) );
			} else {
				return array();
			}
		}

		/**
		 * Converts an array of upload file paths to all be relative paths.
		 * If any path is not absolute or does begin with current uploads base dir it will not be altered.
		 *
		 * @param array $paths Array of upload file paths, absolute or relative.
		 *
		 * @return array Input array with values switched to relative upload file paths.
		 */
		public static function make_upload_file_paths_relative( $paths ) {
			if ( empty( $paths ) ) {
				return array();
			}

			if ( ! is_array( $paths ) ) {
				$paths = array( $paths );
			}

			$uploads = wp_upload_dir();
			$basedir = trailingslashit( $uploads['basedir'] );
			$offset  = strlen( $basedir );

			foreach ( $paths as $key => $path ) {
				if ( 0 === strpos( $path, $basedir ) ) {
					$paths[ $key ] = substr( $path, $offset );
				}
			}

			return $paths;
		}

		/**
		 * Convert dimensions to size
		 *
		 * @param int   $attachment_id
		 * @param array $dimensions
		 *
		 * @return null|string
		 */
		public static function convert_dimensions_to_size_name( $attachment_id, $dimensions ) {
			$w                     = ( isset( $dimensions[0] ) && $dimensions[0] > 0 ) ? $dimensions[0] : 1;
			$h                     = ( isset( $dimensions[1] ) && $dimensions[1] > 0 ) ? $dimensions[1] : 1;
			$original_aspect_ratio = $w / $h;
			$meta                  = wp_get_attachment_metadata( $attachment_id );

			if ( ! isset( $meta['sizes'] ) || empty( $meta['sizes'] ) ) {
				return null;
			}

			$sizes = $meta['sizes'];
			uasort( $sizes, function ( $a, $b ) {
				// Order by image area
				return ( $a['width'] * $a['height'] ) - ( $b['width'] * $b['height'] );
			} );

			$nearest_matches = array();

			foreach ( $sizes as $size => $value ) {
				if ( $w > $value['width'] || $h > $value['height'] ) {
					continue;
				}

				$aspect_ratio = $value['width'] / $value['height'];

				if ( $aspect_ratio === $original_aspect_ratio ) {
					return $size;
				}

				$nearest_matches[] = $size;
			}

			// Return nearest match
			if ( ! empty( $nearest_matches ) ) {
				return $nearest_matches[0];
			}

			return null;
		}

		/**
		 * Maybe convert size to string
		 *
		 * @param int   $attachment_id
		 * @param mixed $size
		 *
		 * @return null|string
		 */
		public static function maybe_convert_size_to_string( $attachment_id, $size ) {
			if ( is_array( $size ) ) {
				return static::convert_dimensions_to_size_name( $attachment_id, $size );
			}

			return $size;
		}

		/**
		 * Encode file names according to RFC 3986 when generating urls
		 * As per Amazon https://forums.aws.amazon.com/thread.jspa?threadID=55746#jive-message-244233
		 *
		 * @param string $file
		 *
		 * @return string Encoded filename
		 */
		public static function encode_filename_in_path( $file ) {
			$url = parse_url( $file );

			if ( ! isset( $url['path'] ) ) {
				// Can't determine path, return original
				return $file;
			}

			if ( isset( $url['query'] ) ) {
				// Manually strip query string, as passing $url['path'] to basename results in corrupt � characters
				$file_name = wp_basename( str_replace( '?' . $url['query'], '', $file ) );
			} else {
				$file_name = wp_basename( $file );
			}

			if ( false !== strpos( $file_name, '%' ) ) {
				// File name already encoded, return original
				return $file;
			}

			$encoded_file_name = rawurlencode( $file_name );

			if ( $file_name === $encoded_file_name ) {
				// File name doesn't need encoding, return original
				return $file;
			}

			return str_replace( $file_name, $encoded_file_name, $file );
		}
	}
}

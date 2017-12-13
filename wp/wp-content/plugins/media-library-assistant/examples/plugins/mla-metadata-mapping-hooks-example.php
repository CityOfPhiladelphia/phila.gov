<?php
/**
 * Provides an advanced example of the filters provided by the IPTC/EXIF and Custom Field mapping features
 *
 * In this example image metadata for new uploads is regenerated when other plugins such as "Image Rotation Fixer"
 * delete it in the process of altering the image. The metadata is extracted and saved in the "mla_upload_prefilter" filter,
 * then regenerated in the "mla_update_attachment_metadata_prefilter" filter. Three support functions at the bottom of the
 * file do the actual work.
 *
 * @package MLA Metadata Mapping Hooks Example
 * @version 1.03
 */

/*
Plugin Name: MLA Metadata Mapping Hooks Example
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Provides an advanced example of the filters provided by the IPTC/EXIF and Custom Field mapping features
Author: David Lingren
Version: 1.03
Author URI: http://fairtradejudaica.org/our-story/staff/

Copyright 2014-2016 David Lingren

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You can get a copy of the GNU General Public License by writing to the
	Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA
*/

/**
 * Class MLA Metadata Hooks Example hooks all of the filters provided by the IPTC/EXIF and Custom Field mapping features
 *
 * Call it anything you want, but give it an unlikely and hopefully unique name. Hiding everything
 * else inside a class means this is the only name you have to worry about.
 *
 * @package MLA Metadata Mapping Hooks Example
 * @since 1.00
 */
class MLAMetadataHooksExample {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * Installs filters and actions that handle the MLA hooks for uploading and mapping.
	 *
	 * @since 1.00
	 *
	 * @return	void
	 */
	public static function initialize() {
		/*
		 * The filters are only useful in the admin section; exit if in the "front-end" posts/pages. 
		 */
		if ( ! is_admin() )
			return;

		add_filter( 'mla_upload_prefilter', 'MLAMetadataHooksExample::mla_upload_prefilter_filter', 10, 2 );
		add_filter( 'mla_upload_filter', 'MLAMetadataHooksExample::mla_upload_filter_filter', 10, 2 );
		add_action( 'mla_add_attachment', 'MLAMetadataHooksExample::mla_add_attachment_action', 10, 1 );
		add_filter( 'mla_update_attachment_metadata_options', 'MLAMetadataHooksExample::mla_update_attachment_metadata_options_filter', 10, 3 );
		add_filter( 'mla_update_attachment_metadata_prefilter', 'MLAMetadataHooksExample::mla_update_attachment_metadata_prefilter_filter', 10, 3 );
		add_action( 'mla_begin_mapping', 'MLAMetadataHooksExample::mla_begin_mapping_action', 10, 2 );
		add_filter( 'mla_update_attachment_metadata_postfilter', 'MLAMetadataHooksExample::mla_update_attachment_metadata_postfilter_filter', 10, 3 );
	}

	/**
	 * Save the original image metadata when a file is first uploaded
	 *
	 * Array elements are:
	 * 		'post_id' => 0,
	 *		'mla_iptc_metadata' => array(),
	 *		'mla_xmp_metadata' => array(),
	 *		'mla_pdf_metadata' => array(),
	 *		'wp_image_metadata' => array(),
	 * 		'preload_file' => array( 'name', 'type', 'tmp_name', 'error', 'size' )
	 * 		'postload_file' => array( 'file', 'url', 'type' )
	 * 		'id3_metadata' => array(),
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $image_metadata = array();

	/**
	 * Save the original JPEG metadata when a file is first uploaded
	 *
	 * Each array element is: 
	 *     array ( 'marker' => integer section marker, 'content' => string section contents ) 
	 *
	 * @since 1.00
	 *
	 * @var	array
	 */
	private static $raw_metadata = array();

	/*
	 * Selected JPEG Section Markers
	 */
	const SOF0	= 0xC0; // 192 Baseline Encoding
	const SOI	= 0xD8; // 216 Start of image
	const EOI	= 0xD9; // 217 End of image
	const SOS	= 0xDA; // 218 Start of scan (image data)
	const APP0	= 0xE0; // 224 Application segment 0 JFIF Header
	const APP1	= 0xE1; // 225 Application segment 1 EXIF/XMP
	const APP2	= 0xE2; // 226 Application segment 2 EXIF Flashpix extensions
	const APP13	= 0xED; // 237 Application segment 13 IPTC
	const COM	= 0xFE; // 254 Comment

	/**
	 * MLA Mapping Upload Prefilter
	 *
	 * This filter gives you an opportunity to record the original IPTC, EXIF and
	 * WordPress image_metadata before the file is stored in the Media Library.
	 * You can also modify the file name that will be used in the Media Library.
	 *
	 * Many plugins and image editing functions alter or destroy this information,
	 * so this may be your last change to preserve it.
	 *
	 * @since 1.00
	 *
	 * @param	array	the file name, type and location
	 * @param	array	the IPTC, EXIF and WordPress image_metadata
	 *
	 * @return	array	updated file name and other information
	 */
	public static function mla_upload_prefilter_filter( $file, $image_metadata ) {
		/*
		 * Uncomment the error_log statements in any of the filters to see what's passed in
		 */
		//error_log( 'MLAMetadataHooksExample::mla_upload_prefilter_filter $file = ' . var_export( $file, true ), 0 );
		//error_log( 'MLAMetadataHooksExample::mla_upload_prefilter_filter $image_metadata = ' . var_export( $image_metadata, true ), 0 );

		/*
		 * Save the information for use in the later filters
		 */
		self::$image_metadata = $image_metadata;
		self::$image_metadata['preload_file'] = $file;

		/*
		 * Save the EXIF, XMP, IPTC and COM data from JPEG files
		 */
		if ( 'image/jpeg' == $file['type'] ) {
			self::$raw_metadata = self::_extract_jpeg_metadata( $file['tmp_name'] );
		} else {
			self::$raw_metadata = array();
		}

		//error_log( 'MLAMetadataHooksExample::mla_upload_prefilter_filter $raw_metadata = ' . var_export( self::$raw_metadata, true ), 0 );
		return $file;
	} // mla_upload_prefilter_filter

	/**
	 * MLA Mapping Upload Filter
	 *
	 * This filter gives you an opportunity to record some additional metadata
	 * for audio and video media after the file is stored in the Media Library.
	 *
	 * Many plugins and other functions alter or destroy this information,
	 * so this may be your last change to preserve it.
	 *
	 * @since 1.00
	 *
	 * @param	array	the file name, type and location
	 * @param	array	the ID3 metadata for audio and video files
	 *
	 * @return	array	updated file name, type and location
	 */
	public static function mla_upload_filter_filter( $file, $id3_data ) {
		//error_log( 'MLAMetadataHooksExample::mla_upload_filter_filter $file = ' . var_export( $file, true ), 0 );
		//error_log( 'MLAMetadataHooksExample::mla_upload_filter_filter $id3_data = ' . var_export( $id3_data, true ), 0 );

		/*
		 * Save the information for use in the later filters
		 */
		self::$image_metadata['postload_file'] = $file;
		self::$image_metadata['id3_metadata'] = $id3_data;

		return $file;
	} // mla_upload_filter_filter

	/**
	 * MLA Add Attachment Action
	 *
	 * This filter is called at the end of the wp_insert_attachment() function,
	 * after the file is in place and the post object has been created in the database.
	 *
	 * By this time, other plugins have probably run their own 'add_attachment' filters
	 * and done their work/damage to metadata, etc.
	 *
	 * @since 1.00
	 *
	 * @param	integer	The Post ID of the new attachment
	 *
	 * @return	void
	 */
	public static function mla_add_attachment_action( $post_id ) {
		//error_log( 'MLAMetadataHooksExample::mla_add_attachment_action $post_id = ' . var_export( $post_id, true ), 0 );

		/*
		 * Save the information for use in the later filters
		 */
		self::$image_metadata['post_id'] = $post_id;
		//error_log( 'MLAMetadataHooksExample::mla_add_attachment_action self::$image_metadata = ' . var_export( self::$image_metadata, true ), 0 );
	} // mla_add_attachment_action

	/**
	 * MLA Update Attachment Metadata Options
	 *
	 * This filter lets you inspect or change the processing options that will
	 * control the MLA mapping rules in the update_attachment_metadata filter.
	 *
	 * The options are:
	 *		is_upload - true if this is part of the original file upload process
	 *		enable_iptc_exif_mapping - true to apply IPTC/EXIF mapping to file uploads
	 *		enable_custom_field_mapping - true to apply custom field mapping to file uploads
	 *		enable_iptc_exif_update - true to apply IPTC/EXIF mapping to updates
	 *		enable_custom_field_update - true to apply custom field mapping to updates
	 *
	 * @since 1.00
	 *
	 * @param	array	Processing options, e.g., 'is_upload'
	 * @param	array	attachment metadata
	 * @param	integer	The Post ID of the new/updated attachment
	 *
	 * @return	array	updated processing options
	 */
	public static function mla_update_attachment_metadata_options_filter( $options, $data, $post_id ) {
		//error_log( 'MLAMetadataHooksExample::mla_update_attachment_metadata_options_filter $options = ' . var_export( $options, true ), 0 );
		//error_log( 'MLAMetadataHooksExample::mla_update_attachment_metadata_options_filter $data = ' . var_export( $data, true ), 0 );
		//error_log( 'MLAMetadataHooksExample::mla_update_attachment_metadata_options_filter $post_id = ' . var_export( $post_id, true ), 0 );

		return $options;
	} // mla_update_attachment_metadata_options_filter

	/**
	 * MLA Update Attachment Metadata Prefilter
	 *
	 * This filter is called at the end of the wp_update_attachment_metadata() function,
	 * BEFORE any MLA mapping rules are applied. The prefilter gives you an
	 * opportunity to record or update the metadata before the mapping.
	 *
	 * The wp_update_attachment_metadata() function is called at the end of the file upload process and at
	 * several later points, such as when an image attachment is edited or by
	 * plugins that alter the attachment file.
	 *
	 * @since 1.00
	 *
	 * @param	array	attachment metadata
	 * @param	integer	The Post ID of the new/updated attachment
	 * @param	array	Processing options, e.g., 'is_upload'
	 *
	 * @return	array	updated attachment metadata
	 */
	public static function mla_update_attachment_metadata_prefilter_filter( $data, $post_id, $options ) {
		//error_log( 'MLAMetadataHooksExample::mla_update_attachment_metadata_prefilter_filter $data = ' . var_export( $data, true ), 0 );
		//error_log( 'MLAMetadataHooksExample::mla_update_attachment_metadata_prefilter_filter $post_id = ' . var_export( $post_id, true ), 0 );
		//error_log( 'MLAMetadataHooksExample::mla_update_attachment_metadata_prefilter_filter $options = ' . var_export( $options, true ), 0 );

		/*
		 * If the metadata has been stripped, try to replace it
		 * NOTE: Uncomment/comment the "self::" and "$data = " lines to activate/deactivate
		 */
		if ( isset( $data['image_meta']['created_timestamp'] ) && empty( $data['image_meta']['created_timestamp'] ) ) {
			self::_replace_jpeg_metadata( self::$image_metadata['postload_file']['file'], self::$raw_metadata );
			$data = wp_generate_attachment_metadata( $post_id, self::$image_metadata['postload_file']['file'] );
			//error_log( 'MLAMetadataHooksExample::mla_update_attachment_metadata_prefilter_filter regenerated data = ' . var_export( $data, true ), 0 );
		}

		return $data;
	} // mla_update_attachment_metadata_prefilter_filter

	/**
	 * MLA Begin Mapping Action
	 *
	 * This action is called once, before any mapping rules are executed for any item(s).
	 *
	 * @since 1.01
	 *
	 * @param	string 	what kind of mapping action is starting:
	 *					single_custom, single_iptc_exif, bulk_custom, bulk_iptc_exif,
	 *					create_metadata, update_metadata, custom_fields, custom_rule,
	 *					iptc_exif_standard, iptc_exif_taxonomy, iptc_exif_custom,
	 *					iptc_exif_custom_rule
	 * @param	mixed	Attachment ID or NULL, depending on scope
	 *
	 * @return	void	updated mapping rules
	 */
	public static function mla_begin_mapping_action( $source, $post_id = NULL ) {
		//error_log( 'MLAMappingHooksExample::mla_begin_mapping_action $source = ' . var_export( $source, true ), 0 );
		//error_log( 'MLAMappingHooksExample::mla_begin_mapping_action $post_id = ' . var_export( $post_id, true ), 0 );

		/*
		 * Replace the metadata removed from the images processed by the Easy Watermark plugin
		 */
		if ( 'image/jpeg' == self::$image_metadata['postload_file']['type'] ) {
			$metadata = self::_extract_jpeg_metadata( self::$image_metadata['postload_file']['file'] );
			//error_log( 'MLAMetadataHooksExample::mla_update_attachment_metadata_postfilter_filter $metadata = ' . var_export( $metadata, true ), 0 );
			$no_metadata = true;
			foreach ( $metadata as $section ) {
				if ( in_array( $section['marker'], array( self::APP1, self::APP2, self::APP13 ) ) ) {
					$no_metadata = false;
					break;
				}
			}
			
			if ( $no_metadata ) {
				self::_replace_jpeg_metadata( self::$image_metadata['postload_file']['file'], self::$raw_metadata );
			}
		}
	} // mla_begin_mapping_action

	/**
	 * MLA Update Attachment Metadata Postfilter
	 *
	 * This filter is called AFTER MLA mapping rules are applied during
	 * wp_update_attachment_metadata() processing. The postfilter gives you
	 * an opportunity to record or update the metadata after the mapping.
	 *
	 * @since 1.00
	 *
	 * @param	array	attachment metadata
	 * @param	integer	The Post ID of the new/updated attachment
	 * @param	array	Processing options, e.g., 'is_upload'
	 *
	 * @return	array	updated attachment metadata
	 */
	public static function mla_update_attachment_metadata_postfilter_filter( $data, $post_id, $options ) {
		//error_log( 'MLAMetadataHooksExample::mla_update_attachment_metadata_postfilter_filter $data = ' . var_export( $data, true ), 0 );
		//error_log( 'MLAMetadataHooksExample::mla_update_attachment_metadata_postfilter_filter $post_id = ' . var_export( $post_id, true ), 0 );
		//error_log( 'MLAMetadataHooksExample::mla_update_attachment_metadata_postfilter_filter $options = ' . var_export( $options, true ), 0 );

		return $data;
	} // mla_update_attachment_metadata_postfilter_filter

	/**
	 * Enumerate the sections of a JPEG file
 	 *
	 * Returns an array of section descriptors, indexed by the section order, i.e., 0, 1, 2 ...
	 *
	 * Each array element is an array, containing:
	 *		marker => section marker, e.g., 0xD8, 0xE0, 0xED
	 *		offset => offset in the file of the "0xFF" marker introducing the section
	 *		length => number of bytes in the section, including the "0xFF", marker byte and length field (if applicable)
	 *
	 * @since 1.01
	 *
	 * @param	string	File Contents
	 *
	 * @return	array	section list ( index => array( 'marker', 'offset', 'length' )
	 */
	private static function _enumerate_jpeg_sections( &$file_contents ) {
		$file_length = strlen( $file_contents );
		$file_offset = 0;
		$section_array = array();

		while ( $file_offset < $file_length ) {
			$section_value = array();

			// Find a marker
			for ( $i = 0; $i < 7; $i++ ) {
				if ( 0xFF != ord( $file_contents[ $file_offset + $i ] ) ) {
					break;
				}
			}

			$section_value['marker'] = $marker = ord( $file_contents[ $file_offset + $i ] );

			if ( $marker >= self::SOF0 && $marker <= self::COM ) {
				$section_value['offset'] = $file_offset + ( $i - 1);

				if ( ( self::SOI == $marker ) || ( self::EOI == $marker ) ) {
					$file_offset = $file_offset + ( $i + 1 );
				} elseif ( self::SOS == $marker ) {
					// Start of Scan precedes image data; skip to end of file/image
					$file_offset = $file_length - 2;

					// Scan backwards for End of Image marker
					while ( ( 0xFF != ord( $file_contents[ $file_offset ] ) ) || ( self::EOI != ord( $file_contents[ $file_offset + 1 ] ) ) ) {
						$file_offset--;
						if ( $file_offset == $start_of_image ) {
							// Give up - no End of Image marker
							$file_offset = $file_length;
							break;
						}
					}
				} else {
					// Big Endian length
					$length = 256 * ord( $file_contents[ $file_offset + ++$i ] );
					$length += ord( $file_contents[ $file_offset + ++$i ] );
					$file_offset = $section_value['offset'] + 2 + $length;
				}
			}
			else {
				// No marker or invalid marker
				if ( 0 < $i ) {
					$section_value['offset'] = $file_offset + ( $i - 1 );
				} else {
					$section_value['offset'] = $file_offset + $i;
				}
				$file_offset = $file_offset + ( $i + 1 );

				while ( $file_offset < $file_length ) {
					if ( 0xFF == ord( $file_contents[ $file_offset ] ) ) {
						break;
					} else {
						$file_offset++;
					}
				}
			} // invalid marker

			$section_value['length'] = $file_offset - $section_value['offset'];
			$section_array[] = $section_value;
			//error_log( 'MLAMetadataHooksExample::_enumerate_jpeg_sections $section_value = ' . var_export( $section_value, true ), 0 );
		} // while offset < length

		return $section_array;
	} // _enumerate_jpeg_sections

	/**
	 * Extract IPTC, EXIF/XMP and Comment data from a JPEG file
 	 *
	 * Returns an array of section content, indexed by the section order
	 *
	 * Each array element is an array, containing:
	 *		marker => section marker, e.g., 0xD8, 0xE0, 0xED
	 *		content => data bytes in the section
	 *
	 * @since 1.01
	 *
	 * @param	string	Absolute path to the file
	 *
	 * @return	array	section list ( index => array( 'marker', 'content' )
	 */
	private static function _extract_jpeg_metadata( $path ) {
		$metadata = array();
		$file_contents = file_get_contents( $path, true );
		 if ( $file_contents ) {
			 $sections = self::_enumerate_jpeg_sections( $file_contents );
			 foreach( $sections as $section ) {
				 if ( in_array( $section['marker'], array( self::APP1, self::APP2, self::APP13, self::COM ) ) ) {
					$metadata[] = array( 'marker' => $section['marker'],
					 	'content' => substr( $file_contents, $section['offset'], $section['length'] )
					);
				 } // found metadata
			 } // foreach section
		 }
		 
		//error_log( 'MLAMetadataHooksExample::_extract_jpeg_metadata $metadata = ' . var_export( $metadata, true ), 0 );
		return $metadata;
	} // _extract_jpeg_metadata

	/**
	 * Add/replace IPTC, EXIF/XMP and Comment data in a JPEG file
 	 *
	 * @since 1.01
	 *
	 * @param	string	Absolute path to the destination file
	 * @param	array	Metadata sections from _extract_jpeg_metadata
	 *
	 * @return	void
	 */
	private static function _replace_jpeg_metadata( $path, $metadata ) {
		//error_log( 'MLAMetadataHooksExample::_replace_jpeg_metadata $path = ' . var_export( $path, true ), 0 );
		//error_log( 'MLAMetadataHooksExample::_replace_jpeg_metadata $marker = ' . var_export( $metadata[0]['marker'], true ), 0 );

		$pathinfo = pathinfo( $path );
		$temp_path = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '-MLA' . $pathinfo['extension'];
		//error_log( 'MLAMetadataHooksExample::_replace_jpeg_metadata $temp_path = ' . var_export( $temp_path, true ), 0 );

		/*
		 * Default to the old COM section if the destination file lacks one
		 */
		$COM_section = NULL;
		foreach ( $metadata as $section ) {
			if ( self::COM == $section['marker'] ) {
				$COM_section = $section['content'];
			}
		}

		/*
		 * Strip the destination "APP1, APP2, APP13" sections.
		 * Separate out the SOI, APP0 and COM sections.
		 */
		$SOI_section = NULL;
		$APP0_section = NULL;
		$destination_sections = array ();
		$file_contents = file_get_contents( $path, true );
		 if ( $file_contents ) {
			 $destination_sections = self::_enumerate_jpeg_sections( $file_contents );
			foreach ( $destination_sections as $index => $value ) {
				if ( self::SOI == $value['marker'] ) {
					$SOI_section = substr( $file_contents, $value['offset'], $value['length'] );
					unset( $destination_sections[ $index ] );
				} elseif  ( self::APP0 == $value['marker'] ) {
					$APP0_section = substr( $file_contents, $value['offset'], $value['length'] );
					unset( $destination_sections[ $index ] );
				} elseif  ( self::COM == $value['marker'] ) {
					$COM_section = substr( $file_contents, $value['offset'], $value['length'] );
					unset( $destination_sections[ $index ] );
				} elseif ( ( self::APP1 == $value['marker'] ) || ( self::APP2 == $value['marker'] ) || ( self::APP13 == $value['marker'] ) ) {
					unset( $destination_sections[ $index ] );
				}
			}
			//error_log( 'MLAMetadataHooksExample::_replace_jpeg_metadata $SOI_section = ' . var_export( $SOI_section, true ), 0 );
			//error_log( 'MLAMetadataHooksExample::_replace_jpeg_metadata $APP0_section = ' . var_export( $APP0_section, true ), 0 );
			//error_log( 'MLAMetadataHooksExample::_replace_jpeg_metadata $COM_section = ' . var_export( $COM_section, true ), 0 );
			//error_log( 'MLAMetadataHooksExample::_replace_jpeg_metadata $destination_sections = ' . var_export( $destination_sections, true ), 0 );

			if ( ( NULL == $SOI_section ) || ( NULL == $APP0_section ) ) {
				return;
			}

			@unlink( $temp_path );
			$temp_handle = @fopen( $temp_path, 'wb' );
			if ( false === $temp_handle ) {
				//error_log( 'MLAMetadataHooksExample::_replace_jpeg_metadata fopen error = ' . var_export( error_get_last(), true ), 0 );
				return;
			}

			if ( false === @fwrite( $temp_handle, $SOI_section ) ) {
				//error_log( 'MLAMetadataHooksExample::_replace_jpeg_metadata fwrite SOI error = ' . var_export( error_get_last(), true ), 0 );
				@fclose( $temp_handle );
				@unlink( $temp_path );
				return;
			}

			if ( false === @fwrite( $temp_handle, $APP0_section ) ) {
				//error_log( 'MLAMetadataHooksExample::_replace_jpeg_metadata fwrite APP0 error = ' . var_export( error_get_last(), true ), 0 );
				@fclose( $temp_handle );
				@unlink( $temp_path );
				return;
			}

			if ( ! empty( $COM_section ) ) {
				if ( false === @fwrite( $temp_handle, $COM_section ) ) {
					//error_log( 'MLAMetadataHooksExample::_replace_jpeg_metadata fwrite COM error = ' . var_export( error_get_last(), true ), 0 );
					@fclose( $temp_handle );
					@unlink( $temp_path );
					return;
				}
			}

			foreach ( $metadata as $section ) {
		//error_log( 'MLAMetadataHooksExample::_replace_jpeg_metadata metadata $section marker = ' . var_export( $section['marker'], true ), 0 );
				if ( false === @fwrite( $temp_handle, $section['content'] ) ) {
					//error_log( 'MLAMetadataHooksExample::_replace_jpeg_metadata fwrite metadata marker = ' . var_export( $section['marker'], true ), 0 );
					//error_log( 'MLAMetadataHooksExample::_replace_jpeg_metadata fwrite metadata error = ' . var_export( error_get_last(), true ), 0 );
					@fclose( $temp_handle );
					@unlink( $temp_path );
					return;
				}
			}

			foreach ( $destination_sections as $section ) {
		//error_log( 'MLAMetadataHooksExample::_replace_jpeg_metadata destination_sections $section marker = ' . var_export( $section['marker'], true ), 0 );
				if ( false === @fwrite( $temp_handle, substr( $file_contents, $section['offset'], $section['length'] ) ) ) {
					//error_log( 'MLAMetadataHooksExample::_replace_jpeg_metadata fwrite destination_sections marker = ' . var_export( $section['marker'], true ), 0 );
					//error_log( 'MLAMetadataHooksExample::_replace_jpeg_metadata fwrite destination_sections error = ' . var_export( error_get_last(), true ), 0 );
					@fclose( $temp_handle );
					@unlink( $temp_path );
					return;
				}
			}

			if ( false === @fclose( $temp_handle ) ) {
				//error_log( 'ERROR: MLAMetadataHooksExample::_replace_jpeg_metadata fclose = ' . var_export( error_get_last(), true ), 0 );
				return;
			}

			if ( false === @unlink( $path ) ) {
				//error_log( 'ERROR: MLAMetadataHooksExample::_replace_jpeg_metadata unlink = ' . var_export( error_get_last(), true ), 0 );
				return;
			}

			if ( false === @rename( $temp_path, $path ) ) {
				//error_log( 'ERROR: MLAMetadataHooksExample::_replace_jpeg_metadata rename = ' . var_export( error_get_last(), true ), 0 );
				return;
			}
		 } // if $file_contents
	} // _replace_jpeg_metadata
} //MLAMetadataHooksExample

/*
 * Install the filters at an early opportunity
 */
add_action('init', 'MLAMetadataHooksExample::initialize');
?>
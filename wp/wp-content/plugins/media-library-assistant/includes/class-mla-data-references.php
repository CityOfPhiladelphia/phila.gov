<?php
/**
 * Database query support for "where-used" reporting
 *
 * @package Media Library Assistant
 * @since 2.20
 */

/**
 * Class MLA (Media Library Assistant) Query provides database query support
 * for "where-used" reporting needs
 *
 * @package Media Library Assistant
 * @since 2.20
 */
class MLAReferences {
	/**
	 * Find Featured Image and inserted image/link references to an attachment
	 * 
	 * Called from MLAQuery::mla_fetch_attachment_references, which handles conditional
	 * loading of this file.
	 *
	 * @since 0.1
	 *
	 * @param	int	post ID of attachment
	 * @param	int	post ID of attachment's parent, if any
	 * @param	boolean	True to compute references, false to return empty values
	 *
	 * @return	array	Reference information; see $references array comments
	 */
	public static function mla_fetch_attachment_references_handler( $ID, $parent, $add_references = true ) {
		global $wpdb;
		static $save_id = -1, $references, $inserted_in_option = NULL;

		if ( $save_id == $ID ) {
			return $references;
		} elseif ( $ID == -1 ) {
			$save_id = -1;
			return NULL;
		}

		/*
		 * inserted_option	'enabled', 'base' or 'disabled'
		 * tested_reference	true if any of the four where-used types was processed
		 * found_reference	true if any where-used array is not empty()
		 * found_parent		true if $parent matches a where-used post ID
		 * is_unattached	true if $parent is zero (0)
		 * base_file		relative path and name of the uploaded file, e.g., 2012/04/image.jpg
		 * path				path to the file, relative to the "uploads/" directory, e.g., 2012/04/
		 * file				The name portion of the base file, e.g., image.jpg
		 * files			base file and any other image size files. Array key is path and file name.
		 *					Non-image file value is a string containing file name without path
		 *					Image file value is an array with file name, width and height
		 * features			Array of objects with the post_type and post_title of each post
		 *					that has the attachment as a "Featured Image"
		 * inserts			Array of specific files (i.e., sizes) found in one or more posts/pages
		 *					as an image (<img>) or link (<a href>). The array key is the path and file name.
		 *					The array value is an array with the ID, post_type and post_title of each reference
		 * mla_galleries	Array of objects with the post_type and post_title of each post
		 *					that was returned by an [mla_gallery] shortcode
		 * galleries		Array of objects with the post_type and post_title of each post
		 *					that was returned by a [gallery] shortcode
		 * parent_type		'post' or 'page' or the custom post type of the attachment's parent
		 * parent_status	'publish', 'private', 'future', 'pending', 'draft'
		 * parent_title		post_title of the attachment's parent
		 * parent_errors	UNATTACHED, ORPHAN, BAD/INVALID PARENT
		 */
		$references = array(
			'inserted_option' => '',
			'tested_reference' => false,
			'found_reference' => false,
			'found_parent' => false,
			'is_unattached' => ( ( (int) $parent ) === 0 ),
			'base_file' => '',
			'path' => '',
			'file' => '',
			'files' => array(),
			'features' => array(),
			'inserts' => array(),
			'mla_galleries' => array(),
			'galleries' => array(),
			'parent_type' => '',
			'parent_status' => '',
			'parent_title' => '',
			'parent_errors' => ''
		);

		if ( ! $add_references ) {
			return $references;
		}

		/*
		 * Fill in Parent data
		 */
		$parent_data = MLAQuery::mla_fetch_attachment_parent_data( $parent );
		if ( isset( $parent_data['parent_type'] ) ) {
			$references['parent_type'] = $parent_data['parent_type'];
		}

		if ( isset( $parent_data['parent_status'] ) ) {
			$references['parent_status'] = $parent_data['parent_status'];
		}

		if ( isset( $parent_data['parent_title'] ) ) {
			$references['parent_title'] = $parent_data['parent_title'];
		}

		$references['base_file'] = get_post_meta( $ID, '_wp_attached_file', true );
		$pathinfo = pathinfo($references['base_file']);
		$references['file'] = $pathinfo['basename'];
		if ( ( ! isset( $pathinfo['dirname'] ) ) || '.' == $pathinfo['dirname'] ) {
			$references['path'] = '/';
		} else {
			$references['path'] = $pathinfo['dirname'] . '/';
		}

		$attachment_metadata = get_post_meta( $ID, '_wp_attachment_metadata', true );
		$sizes = isset( $attachment_metadata['sizes'] ) ? $attachment_metadata['sizes'] : NULL;
		if ( is_array( $sizes ) ) {
			// Using the name as the array key ensures each name is added only once
			foreach ( $sizes as $size => $size_info ) {
				$size_info['size'] = $size;
				$references['files'][ $references['path'] . $size_info['file'] ] = $size_info;
			}
		}

		$base_type = wp_check_filetype( $references['file'] );
		$base_reference = array(
			'file' => $references['file'],
			'width' => isset( $attachment_metadata['width'] ) ? $attachment_metadata['width'] : 0,
			'height' => isset( $attachment_metadata['height'] ) ? $attachment_metadata['height'] : 0,
			'mime_type' => isset( $base_type['type'] ) ? $base_type['type'] : 'unknown',
			'size' => 'full',
			);

		$references['files'][ $references['base_file'] ] = $base_reference;

		/*
		 * Process the where-used settings option
		 */
		if ('checked' == MLACore::mla_get_option( MLACoreOptions::MLA_EXCLUDE_REVISIONS ) ) {
			$exclude_revisions = "(post_type <> 'revision') AND ";
		} else {
			$exclude_revisions = '';
		}

		/*
		 * Accumulate reference test types, e.g., 0 = no tests, 4 = all tests
		 */
		$reference_tests = 0;

		/*
		 * Look for the "Featured Image(s)", if enabled
		 */
		if ( MLACore::$process_featured_in ) {
			$reference_tests++;
			$features = $wpdb->get_results( 
					"
					SELECT post_id
					FROM {$wpdb->postmeta}
					WHERE meta_key = '_thumbnail_id' AND meta_value = {$ID}
					"
			);

			if ( ! empty( $features ) ) {
				foreach ( $features as $feature ) {
					$feature_results = $wpdb->get_results(
							"
							SELECT ID, post_type, post_status, post_title
							FROM {$wpdb->posts}
							WHERE {$exclude_revisions}(ID = {$feature->post_id})
							"
					);

					if ( ! empty( $feature_results ) ) {
						$references['found_reference'] = true;
						$references['features'][ $feature->post_id ] = $feature_results[0];

						if ( $feature->post_id == $parent ) {
							$references['found_parent'] = true;
						}
					} // ! empty
				} // foreach $feature
			}
		} // $process_featured_in

		/*
		 * Look for item(s) inserted in post_content
		 */
		$references['inserted_option'] = $inserted_in_option;
		if ( MLACore::$process_inserted_in ) {
			$reference_tests++;

			if ( NULL == $inserted_in_option ) {
				$inserted_in_option = MLACore::mla_get_option( MLACoreOptions::MLA_INSERTED_IN_TUNING );
				$references['inserted_option'] = $inserted_in_option;
			}

			if ( 'base' == $inserted_in_option ) {
				$query_parameters = array();
				$query = array();
				$query[] = "SELECT ID, post_type, post_status, post_title, CONVERT(`post_content` USING utf8 ) AS POST_CONTENT FROM {$wpdb->posts} WHERE {$exclude_revisions} ( %s=%s";
				$query_parameters[] = '1'; // for empty file name array
				$query_parameters[] = '0'; // for empty file name array

				foreach ( $references['files'] as $file => $file_data ) {
					if ( empty( $file ) ) {
						continue;
					}

					$query[] = 'OR ( POST_CONTENT LIKE %s)';

					if ( MLAQuery::$wp_4dot0_plus ) {
						$query_parameters[] = '%' . $wpdb->esc_like( $file ) . '%';
					} else {
						$query_parameters[] = '%' . like_escape( $file ) . '%';
					}
				}

				$query[] = ')';
				$query = join(' ', $query);

				$inserts = $wpdb->get_results(
					$wpdb->prepare( $query, $query_parameters )
				);

				if ( ! empty( $inserts ) ) {
					$references['found_reference'] = true;
					$references['inserts'][ $pathinfo['filename'] ] = $inserts;

					foreach ( $inserts as $index => $insert ) {
						unset( $references['inserts'][ $pathinfo['filename'] ][ $index ]->POST_CONTENT );
						if ( $insert->ID == $parent ) {
							$references['found_parent'] = true;
						}
					} // foreach $insert
				} // ! empty
			} else { // process base names
				foreach ( $references['files'] as $file => $file_data ) {
					if ( empty( $file ) ) {
						continue;
					}

					if ( MLAQuery::$wp_4dot0_plus ) {
						$like = $wpdb->esc_like( $file );
					} else {
						$like = like_escape( $file );
					}

					$inserts = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT ID, post_type, post_status, post_title FROM {$wpdb->posts}
							WHERE {$exclude_revisions}(CONVERT(`post_content` USING utf8 ) LIKE %s)", "%{$like}%"
						)
					);

					if ( ! empty( $inserts ) ) {
						$references['found_reference'] = true;
						$references['inserts'][ $file_data['file'] ] = $inserts;

						foreach ( $inserts as $insert ) {
							if ( $insert->ID == $parent ) {
								$references['found_parent'] = true;
							}
						} // foreach $insert
					} // ! empty
				} // foreach $file
			} // process intermediate sizes
		} // $process_inserted_in

		/*
		 * Look for [mla_gallery] references
		 */
		if ( MLACore::$process_mla_gallery_in ) {
			$reference_tests++;
			if ( self::_build_mla_galleries( MLACoreOptions::MLA_MLA_GALLERY_IN_TUNING, self::$mla_galleries, '[mla_gallery', $exclude_revisions ) ) {
				$galleries = self::_search_mla_galleries( self::$mla_galleries, $ID );
				if ( ! empty( $galleries ) ) {
					$references['found_reference'] = true;
					$references['mla_galleries'] = $galleries;

					foreach ( $galleries as $post_id => $gallery ) {
						if ( $post_id == $parent ) {
							$references['found_parent'] = true;
						}
					} // foreach $gallery
				} else { // ! empty
					$references['mla_galleries'] = array();
				}
			}
		} // $process_mla_gallery_in

		/*
		 * Look for [gallery] references
		 */
		if ( MLACore::$process_gallery_in ) {
			$reference_tests++;
			if ( self::_build_mla_galleries( MLACoreOptions::MLA_GALLERY_IN_TUNING, self::$galleries, '[gallery', $exclude_revisions ) ) {
				$galleries = self::_search_mla_galleries( self::$galleries, $ID );
				if ( ! empty( $galleries ) ) {
					$references['found_reference'] = true;
					$references['galleries'] = $galleries;

					foreach ( $galleries as $post_id => $gallery ) {
						if ( $post_id == $parent ) {
							$references['found_parent'] = true;
						}
					} // foreach $gallery
				} else { // ! empty
					$references['galleries'] = array();
				}
			}
		} // $process_gallery_in

		/*
		 * Evaluate and summarize reference tests
		 */
		$errors = '';
		if ( 0 == $reference_tests ) {
			$references['tested_reference'] = false;
			$errors .= '(' . __( 'NO REFERENCE TESTS', 'media-library-assistant' ) . ')';
		} else {
			$references['tested_reference'] = true;
			$suffix = ( 4 == $reference_tests ) ? '' : '?';

			if ( !$references['found_reference'] ) {
				$errors .= '(' . sprintf( __( 'ORPHAN', 'media-library-assistant' ) . '%1$s) ', $suffix );
			}

			if ( !$references['found_parent'] && ! empty( $references['parent_title'] ) ) {
				$errors .= '(' . sprintf( __( 'UNUSED', 'media-library-assistant' ) . '%1$s) ', $suffix );
			}
		}

		if ( $references['is_unattached'] ) {
			$errors .= '(' . __( 'UNATTACHED', 'media-library-assistant' ) . ')';
		} elseif ( empty( $references['parent_title'] ) ) {
			$errors .= '(' . __( 'INVALID PARENT', 'media-library-assistant' ) . ')';
		}

		$references['parent_errors'] = trim( $errors );

		$save_id = $ID;
		$references = apply_filters( 'mla_fetch_attachment_references', $references, $ID, $parent );
		return $references;
	}

	/**
	 * Add Featured Image and inserted image/link references to an array of attachments
	 * 
	 * Called from MLAQuery::mla_fetch_attachment_references, which handles conditional
	 * loading of this file.
	 *
	 * @since 1.94
	 *
	 * @param	array	WP_Post objects, passed by reference
	 *
	 * @return	void	updates WP_Post objects with new mla_references property
	 */
	public static function mla_attachment_array_fetch_references_handler( &$attachments ) {
		global $wpdb;

		/*
		 * See element definitions above
		 */
		$initial_references = array(
			'inserted_option' => '',
			'tested_reference' => false,
			'found_reference' => false,
			'found_parent' => false,
			'is_unattached' => true,
			'base_file' => '',
			'path' => '',
			'file' => '',
			'files' => array(),
			'features' => array(),
			'inserts' => array(),
			'mla_galleries' => array(),
			'galleries' => array(),
			'parent_type' => '',
			'parent_status' => '',
			'parent_title' => '',
			'parent_errors' => ''
		);

		$inserted_in_option = MLACore::mla_get_option( MLACoreOptions::MLA_INSERTED_IN_TUNING );
		$initial_references['inserted_option'] = $inserted_in_option;

		/*
		 * Make sure there's work to do; otherwise initialize the attachment data and return
		 */
		if ( false == ( MLACore::$process_featured_in || MLACore::$process_inserted_in || MLACore::$process_gallery_in || MLACore::$process_mla_gallery_in ) ) {
			foreach ( $attachments as $attachment_index => $attachment ) {
				$attachments[ $attachment_index ]->mla_references = $initial_references;
			}

			return;
		}

		/*
		 * Collect the raw data for where-used analysis
		 */
		$attachment_ids = array();
		$files = array();
		foreach ( $attachments as $index => $attachment ) {
			$attachment_ids[ $index ] = $attachment->ID;
			$references = array( 'files' => array() );
			if ( isset( $attachment->mla_wp_attached_file ) ) {
				$references['base_file'] = $attachment->mla_wp_attached_file;
			} else {
				$references['base_file'] = '';
			}

			$pathinfo = pathinfo($references['base_file']);
			if ( ( ! isset( $pathinfo['dirname'] ) ) || '.' == $pathinfo['dirname'] ) {
				$references['path'] = '/';
			} else {
				$references['path'] = $pathinfo['dirname'] . '/';
			}

			$references['file'] = $pathinfo['basename'];

			if ( isset( $attachment->mla_wp_attachment_metadata ) ) {
				$attachment_metadata = $attachment->mla_wp_attachment_metadata;
			} else {
				$attachment_metadata = '';
			}

			$sizes = isset( $attachment_metadata['sizes'] ) ? $attachment_metadata['sizes'] : NULL;
			if ( ! empty( $sizes ) && is_array( $sizes ) ) {
				/* Using the path and name as the array key ensures each name is added only once */
				foreach ( $sizes as $size => $size_info ) {
					$size_info['size'] = $size;
					$references['files'][ $references['path'] . $size_info['file'] ] = $size_info;
				}
			}

			if ( ! empty( $references['base_file'] ) ) {
				$base_type = wp_check_filetype( $references['file'] );
				$base_reference = array(
					'file' => $references['file'],
					'width' => isset( $attachment_metadata['width'] ) ? $attachment_metadata['width'] : 0,
					'height' => isset( $attachment_metadata['height'] ) ? $attachment_metadata['height'] : 0,
					'mime_type' => ( isset( $base_type['type'] ) && false !== $base_type['type'] ) ? $base_type['type'] : 'unknown',
					'size' => 'full',
					);

				$references['files'][ $references['base_file'] ] = $base_reference;
			}

			$files[ $index ] = $references;
		}

		if ('checked' == MLACore::mla_get_option( MLACoreOptions::MLA_EXCLUDE_REVISIONS ) ) {
			$exclude_revisions = " AND (p.post_type <> 'revision')";
		} else {
			$exclude_revisions = '';
		}

		$features = array();
		if ( MLACore::$process_featured_in && ! empty( $attachment_ids ) ) {
			$attachment_ids = implode( ',', $attachment_ids );
			$results = $wpdb->get_results( 
					"
					SELECT m.meta_value, p.ID, p.post_type, p.post_status, p.post_title
					FROM {$wpdb->postmeta} AS m INNER JOIN {$wpdb->posts} AS p ON m.post_id = p.ID
					WHERE ( m.meta_key = '_thumbnail_id' )
					AND ( m.meta_value IN ( {$attachment_ids} ) ){$exclude_revisions}
					"
			);

			foreach ( $results as $result ) {
				$features[ $result->meta_value ][ $result->ID ] = (object) array( 'ID' => $result->ID, 'post_title' => $result->post_title, 'post_type' => $result->post_type, 'post_status' => $result->post_status );
			}
		} // $process_featured_in

		if ( ! empty( $exclude_revisions ) ) {
			$exclude_revisions = " AND (post_type <> 'revision')";
		}

		if ( MLACore::$process_inserted_in ) {
			$query_parameters = array();
			$query = array();
			$query[] = "SELECT ID, post_type, post_status, post_title, CONVERT(`post_content` USING utf8 ) AS POST_CONTENT FROM {$wpdb->posts} WHERE ( %s=%s";
			// for empty file name array
			$query_parameters[] = '1';
			$query_parameters[] = '0';

			foreach ( $files as $file ) {
				foreach ( $file['files'] as $base_name => $file_data ) {
					$query[] = 'OR ( POST_CONTENT LIKE %s)';

					if ( MLAQuery::$wp_4dot0_plus ) {
						$query_parameters[] = '%' . $wpdb->esc_like( $base_name ) . '%';
					} else {
						$query_parameters[] = '%' . like_escape( $base_name ) . '%';
					}
				}
			}

			$query[] = "){$exclude_revisions}";
			$query = join(' ', $query);

			$results = $wpdb->get_results(
				$wpdb->prepare( $query, $query_parameters )
			);

			/*
			 * Match each post with inserts back to the attachments
			 */
			$inserts = array();
			if ( ! empty( $results ) ) {
				foreach ( $files as $index => $file ) {
					foreach ( $file['files'] as $base_name => $file_data ) {
						foreach ( $results as $result ) {
							if ( false !== strpos( $result->POST_CONTENT, $base_name ) ) {
								$insert = clone $result;
								unset( $insert->POST_CONTENT);
								$insert->file_name = $file_data['file'];
								$inserts[ $index ][] = $insert;
							}
						} // foreach post with inserts
					} // foreach base_name
				} // foreach attachment
			} // results
		} // process_inserted_in

		if ( MLACore::$process_mla_gallery_in ) {
			$have_mla_galleries = self::_build_mla_galleries( MLACoreOptions::MLA_MLA_GALLERY_IN_TUNING, self::$mla_galleries, '[mla_gallery', $exclude_revisions );
		} else {
			$have_mla_galleries = false;
		}

		if ( MLACore::$process_gallery_in ) {
			$have_galleries = self::_build_mla_galleries( MLACoreOptions::MLA_GALLERY_IN_TUNING, self::$galleries, '[gallery', $exclude_revisions );
		} else {
			$have_mla_galleries = false;
		}

		foreach ( $attachments as $attachment_index => $attachment ) {
			$references = array_merge( $initial_references, $files[ $attachment_index ] );

			/*
			 * Fill in Parent data
			 */
			if ( ( (int) $attachment->post_parent ) === 0 ) {
				$references['is_unattached'] = true;
			} else {
				$references['is_unattached'] = false;

				if ( isset( $attachment->parent_type ) ) {
					$references['parent_type'] = $attachment->parent_type;
				}

				if ( isset( $attachment->parent_status ) ) {
					$references['parent_status'] = $attachment->parent_status;
				}

				if ( isset( $attachment->parent_title ) ) {
					$references['parent_title'] = $attachment->parent_title;
				}
			}

			/*
			 * Accumulate reference test types, e.g., 0 = no tests, 4 = all tests
			 */
			$reference_tests = 0;

			/*
			 * Look for the "Featured Image(s)", if enabled
			 */
			if ( MLACore::$process_featured_in ) {
				$reference_tests++;
				if ( isset( $features[ $attachment->ID ] ) ) {
					foreach ( $features[ $attachment->ID ] as $id => $feature ) {
						$references['found_reference'] = true;
						$references['features'][ $id ] = $feature;

						if ( $id == $attachment->post_parent ) {
							$references['found_parent'] = true;
						}
					} // foreach $feature
				}
			} // $process_featured_in

			/*
			 * Look for item(s) inserted in post_content
			 */
			if ( MLACore::$process_inserted_in ) {
				$reference_tests++;

				if ( isset( $inserts[ $attachment_index ] ) ) {
					$references['found_reference'] = true;
					foreach( $inserts[ $attachment_index ] as $insert ) {
						$ref_insert = clone $insert;
						unset( $ref_insert->file_name );

						if ( 'base' == $inserted_in_option ) {
							$ref_key = pathinfo( $references['base_file'], PATHINFO_FILENAME );
						} else {
							$ref_key = $insert->file_name;
						}

						$references['inserts'][ $ref_key ][ $insert->ID ] = $ref_insert;
						if ( $insert->ID == $attachment->post_parent ) {
							$references['found_parent'] = true;
						}
					} // each insert
				} else {
					$references['inserts'] = array();
				}
			} // $process_inserted_in

			/*
			 * Look for [mla_gallery] references
			 */
			if ( MLACore::$process_mla_gallery_in ) {
				$reference_tests++;
				if ( self::_build_mla_galleries( MLACoreOptions::MLA_MLA_GALLERY_IN_TUNING, self::$mla_galleries, '[mla_gallery', $exclude_revisions ) ) {
					$galleries = self::_search_mla_galleries( self::$mla_galleries, $attachment->ID );
					if ( ! empty( $galleries ) ) {
						$references['found_reference'] = true;
						$references['mla_galleries'] = $galleries;

						foreach ( $galleries as $post_id => $gallery ) {
							if ( $post_id == $attachment->post_parent ) {
								$references['found_parent'] = true;
							}
						} // foreach $gallery
					} else { // ! empty
						$references['mla_galleries'] = array();
					}
				}
			} // $process_mla_gallery_in

			/*
			 * Look for [gallery] references
			 */
			if ( MLACore::$process_gallery_in ) {
				$reference_tests++;
				if ( self::_build_mla_galleries( MLACoreOptions::MLA_GALLERY_IN_TUNING, self::$galleries, '[gallery', $exclude_revisions ) ) {
					$galleries = self::_search_mla_galleries( self::$galleries, $attachment->ID );
					if ( ! empty( $galleries ) ) {
						$references['found_reference'] = true;
						$references['galleries'] = $galleries;

						foreach ( $galleries as $post_id => $gallery ) {
							if ( $post_id == $attachment->post_parent ) {
								$references['found_parent'] = true;
							}
						} // foreach $gallery
					} else { // ! empty
						$references['galleries'] = array();
					}
				}
			} // $process_gallery_in

			/*
			 * Evaluate and summarize reference tests
			 */
			$errors = '';
			if ( 0 == $reference_tests ) {
				$references['tested_reference'] = false;
				$errors .= '(' . __( 'NO REFERENCE TESTS', 'media-library-assistant' ) . ')';
			} else {
				$references['tested_reference'] = true;
				$suffix = ( 4 == $reference_tests ) ? '' : '?';

				if ( !$references['found_reference'] ) {
					$errors .= '(' . sprintf( __( 'ORPHAN', 'media-library-assistant' ) . '%1$s) ', $suffix );
				}

				if ( !$references['found_parent'] && ! empty( $references['parent_title'] ) ) {
					$errors .= '(' . sprintf( __( 'UNUSED', 'media-library-assistant' ) . '%1$s) ', $suffix );
				}
			}

			if ( $references['is_unattached'] ) {
				$errors .= '(' . __( 'UNATTACHED', 'media-library-assistant' ) . ')';
			} elseif ( empty( $references['parent_title'] ) ) {
				$errors .= '(' . __( 'INVALID PARENT', 'media-library-assistant' ) . ')';
			}

			$references['parent_errors'] = trim( $errors );
			$attachments[ $attachment_index ]->mla_references = apply_filters( 'mla_fetch_attachment_references', $references, $attachment->ID, (int) $attachment->post_parent );
		} // foreach $attachment
	}

	/**
	 * Objects containing [gallery] shortcodes
	 *
	 * This array contains all of the objects containing one or more [gallery] shortcodes
	 * and array(s) of which attachments each [gallery] contains. The arrays are built once
	 * each page load and cached for subsequent calls.
	 *
	 * The outer array is keyed by post_id. It contains an associative array with:
	 * ['parent_title'] post_title of the gallery parent, 
	 * ['parent_type'] 'post' or 'page' or the custom post_type of the gallery parent,
	 * ['parent_status'] 'publish', 'private', 'future', 'pending', 'draft'
	 * ['results'] array ( ID => ID ) of attachments appearing in ANY of the parent's galleries.
	 * ['galleries'] array of [gallery] entries numbered from one (1), containing:
	 * galleries[X]['query'] contains a string with the arguments of the [gallery], 
	 * galleries[X]['results'] contains an array ( ID ) of post_ids for the objects in the gallery.
	 *
	 * @since 0.70
	 *
	 * @var	array
	 */
	private static $galleries = NULL;

	/**
	 * Objects containing [mla_gallery] shortcodes
	 *
	 * This array contains all of the objects containing one or more [mla_gallery] shortcodes
	 * and array(s) of which attachments each [mla_gallery] contains. The arrays are built once
	 * each page load and cached for subsequent calls.
	 *
	 * @since 0.70
	 *
	 * @var	array
	 */
	private static $mla_galleries = NULL;

	/**
	 * Invalidates the $mla_galleries or $galleries array and cached values
	 *
	 * Called from MLAQuery::mla_flush_mla_galleries, which handles conditional
	 * loading of this file.
	 *
	 * @since 1.00
	 *
	 * @param	string name of the gallery's cache/option variable
	 *
	 * @return	void
	 */
	public static function mla_flush_mla_galleries_handler( $option_name ) {
		switch ( $option_name ) {
			case MLACoreOptions::MLA_GALLERY_IN_TUNING:
				self::$galleries = NULL;
				break;
			case MLACoreOptions::MLA_MLA_GALLERY_IN_TUNING:
				self::$mla_galleries = NULL;
				break;
			default:
				//	ignore everything else
		} // switch
	}

	/**
	 * Builds the $mla_galleries or $galleries array
	 *
	 * @since 0.70
	 *
	 * @param	string name of the gallery's cache/option variable
	 * @param	array by reference to the private static galleries array variable
	 * @param	string the shortcode to be searched for and processed
	 * @param	boolean true to exclude revisions from the search
	 *
	 * @return	boolean true if the galleries array is not empty
	 */
	private static function _build_mla_galleries( $option_name, &$galleries_array, $shortcode, $exclude_revisions ) {
		global $wpdb, $post;

		if ( is_array( $galleries_array ) ) {
			if ( ! empty( $galleries_array ) ) {
				return true;
			} else {
				return false;
			}
		}

		$option_value = MLACore::mla_get_option( $option_name );
		if ( 'disabled' == $option_value ) {
			return false;
		} elseif ( 'cached' == $option_value ) {
			$galleries_array = get_transient( MLA_OPTION_PREFIX . 't_' . $option_name );
			if ( is_array( $galleries_array ) ) {
				if ( ! empty( $galleries_array ) ) {
					return true;
				} else {
					return false;
				}
			} else {
				$galleries_array = NULL;
			}
		} // cached

		/* 
		 * The MLAShortcodes class is only loaded when needed.
		 */
		if ( !class_exists( 'MLAShortcodes' ) ) {
			require_once( MLA_PLUGIN_PATH . 'includes/class-mla-shortcodes.php' );
		}
		
		/*
		 * $galleries_array is null, so build the array
		 */
		$galleries_array = array();

		if ( $exclude_revisions ) {
			$exclude_revisions = "(post_type <> 'revision') AND ";
		} else {
			$exclude_revisions = '';
		}

		if ( MLAQuery::$wp_4dot0_plus ) {
			$like = $wpdb->esc_like( $shortcode );
		} else {
			$like = like_escape( $shortcode );
		}

		$results = $wpdb->get_results(
			$wpdb->prepare(
				"
				SELECT ID, post_type, post_status, post_title, post_content
				FROM {$wpdb->posts}
				WHERE {$exclude_revisions}(
					CONVERT(`post_content` USING utf8 )
					LIKE %s)
				", "%{$like}%"
			)
		);

		if ( empty( $results ) ) {
			return false;
		}

		foreach ( $results as $result ) {
			$count = preg_match_all( "/\\{$shortcode}([^\\]]*)\\]/", $result->post_content, $matches, PREG_PATTERN_ORDER );
			if ( $count ) {
				$result_id = $result->ID;
				$galleries_array[ $result_id ]['parent_title'] = $result->post_title;
				$galleries_array[ $result_id ]['parent_type'] = $result->post_type;
				$galleries_array[ $result_id ]['parent_status'] = $result->post_status;
				$galleries_array[ $result_id ]['results'] = array();
				$galleries_array[ $result_id ]['galleries'] = array();
				$instance = 0;

				foreach ( $matches[1] as $index => $match ) {
					/*
					 * Filter out shortcodes that are not an exact match
					 */
					if ( empty( $match ) || ( ' ' == substr( $match, 0, 1 ) ) ) {
						$instance++;
						/*
						 * Remove trailing "/" from XHTML-style self-closing shortcodes
						 */
						$galleries_array[ $result_id ]['galleries'][ $instance ]['query'] = trim( rtrim( $matches[1][$index], '/' ) );
						$galleries_array[ $result_id ]['galleries'][ $instance ]['results'] = array();
						$post = $result; // set global variable for mla_gallery_shortcode
						$attachments = MLAShortcodes::mla_get_shortcode_attachments( $result_id, $galleries_array[ $result_id ]['galleries'][ $instance ]['query'] . ' cache_results=false update_post_meta_cache=false update_post_term_cache=false where_used_query=this-is-a-where-used-query' );

						if ( is_string( $attachments ) ) {
							/* translators: 1: post_type, 2: post_title, 3: post ID, 4: query string, 5: error message */
							trigger_error( htmlentities( sprintf( __( '(%1$s) %2$s (ID %3$d) query "%4$s" failed, returning "%5$s"', 'media-library-assistant' ), $result->post_type, $result->post_title, $result->ID, $galleries_array[ $result_id ]['galleries'][ $instance ]['query'], $attachments) ), E_USER_WARNING );
						} elseif ( ! empty( $attachments ) ) {
							foreach ( $attachments as $attachment ) {
								$galleries_array[ $result_id ]['results'][ $attachment->ID ] = $attachment->ID;
								$galleries_array[ $result_id ]['galleries'][ $instance ]['results'][] = $attachment->ID;
							}
						}
					} // exact match
				} // foreach $match
			} // if $count
		} // foreach $result

	/*
	 * Maybe cache the results
	 */	
	if ( 'cached' == $option_value ) {
		set_transient( MLA_OPTION_PREFIX . 't_' . $option_name, $galleries_array, 900 ); // fifteen minutes
	}

	return true;
	}

	/**
	 * Search the $mla_galleries or $galleries array
	 *
	 * @since 0.70
	 *
	 * @param	array	by reference to the private static galleries array variable
	 * @param	int		the attachment ID to be searched for and processed
	 *
	 * @return	array	All posts/pages with one or more galleries that include the attachment.
	 * 					The array key is the parent_post ID; each entry contains post_title and post_type.
	 */
	private static function _search_mla_galleries( &$galleries_array, $attachment_id ) {
		$gallery_refs = array();
		if ( ! empty( $galleries_array ) ) {
			foreach ( $galleries_array as $parent_id => $gallery ) {
				if ( in_array( $attachment_id, $gallery['results'] ) ) {
					$gallery_refs[ $parent_id ] = array ( 'ID' => $parent_id, 'post_title' => $gallery['parent_title'], 'post_type' => $gallery['parent_type'], 'post_status' => $gallery['parent_status'] );
				}
			} // foreach gallery
		} // ! empty

		return $gallery_refs;
	}
} // class MLAReferences
?>
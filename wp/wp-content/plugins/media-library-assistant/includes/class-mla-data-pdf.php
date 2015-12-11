<?php
/**
 * Meta data parsing functions for PDF documents
 *
 * @package Media Library Assistant
 * @since 2.10
 */

/**
 * Class MLA (Media Library Assistant) PDF extracts legacy and XMP meta data from PDF files
 *
 * @package Media Library Assistant
 * @since 2.10
 */
class MLAPDF {
	/**
	 * Array of PDF indirect objects
	 *
	 * This array contains all of the indirect object offsets and lengths.
	 * The array key is ( object ID * 1000 ) + object generation.
	 * The array value is array( number, generation, start, optional /length )
	 *
	 * @since 2.10
	 *
	 * @var	array
	 */
	private static $pdf_indirect_objects = NULL;

	/**
	 * Parse a cross-reference table subsection into the array of indirect object definitions
	 * 
	 * A cross-reference subsection is a sequence of 20-byte entries, each with offset and generation values.
	 * @since 2.10
	 *
	 * @param	string	buffer containing the subsection
	 * @param	integer	offset within the buffer of the first entry
	 * @param	integer	number of the first object in the subsection
	 * @param	integer	number of entries in the subsection
	 * 
	 * @return	void
	 */
	private static function _parse_pdf_xref_subsection( &$xref_section, $offset, $object_id, $count ) {

		while ( $count-- ) {
			$match_count = preg_match( '/(\d+) (\d+) (.)/', $xref_section, $matches, 0, $offset);

			if ( $match_count ) {
				if ( 'n' == $matches[3] ) {
					$key = ( $object_id * 1000 ) + $matches[2];
					if ( ! isset( self::$pdf_indirect_objects[ $key ] ) ) {
						self::$pdf_indirect_objects[ $key ] = array( 'number' => $object_id, 'generation' => (integer) $matches[2], 'start' => (integer) $matches[1] );
					}
				}

				$object_id++;
				$offset += 20;
			} else {
				break;
			}
		}
	}

	/**
	 * Parse a cross-reference table section into the array of indirect object definitions
	 * 
	 * Creates the array of indirect object offsets and lengths
	 * @since 2.10
	 *
	 * @param	string	full path and file name
	 * @param	integer	offset within the file of the xref id and count entry
	 * 
	 * @return	integer	length of the section
	 */
	private static function _parse_pdf_xref_section( $file_name, $file_offset ) {
		$xref_max = $chunksize = 16384;			
		$xref_section = file_get_contents( $file_name, true, NULL, $file_offset, $chunksize );
		$xref_length = 0;

		while ( preg_match( '/^[\x00-\x20]*(\d+) (\d+)[\x00-\x20]*/', substr($xref_section, $xref_length), $matches, 0 ) ) {
			$object_id = $matches[1];
			$count = $matches[2];
			$offset = $xref_length + strlen( $matches[0] );
			$xref_length = $offset + ( 20 * $count );

			if ( $xref_max < $xref_length ) {
				$xref_max += $chunksize;
				$xref_section = file_get_contents( $file_name, true, NULL, $file_offset, $xref_max );
			}

			self::_parse_pdf_xref_subsection( $xref_section, $offset, $object_id, $count );
		} // while preg_match subsection header

		return $xref_length;
	}

	/**
	 * Parse a cross-reference steam into the array of indirect object definitions
	 * 
	 * Creates the array of indirect object offsets and lengths
	 * @since 2.10
	 *
	 * @param	string	full path and file name
	 * @param	integer	offset within the file of the xref id and count entry
	 * @param	string	"/W" entry, representing the size of the fields in a single entry
	 * 
	 * @return	integer	length of the stream
	 */
	private static function _parse_pdf_xref_stream( $file_name, $file_offset, $entry_parms_string ) {
		$chunksize = 16384;			
		$xref_section = file_get_contents( $file_name, true, NULL, $file_offset, $chunksize );

		if ( 'stream' == substr( $xref_section, 0, 6 ) ) {
			$tag_length = 7;
			if ( chr(0x0D) == $xref_section[6] ) {
				$tag_length++;
			}
		} else {
			return 0;
		}

		/*
		 * If necessary and possible, expand the $xref_section until it contains the end tag
		 */
		$new_chunksize = $chunksize;
		if ( false === ( $end_tag = strpos( $xref_section, 'endstream', $tag_length ) ) && ( $chunksize == strlen( $xref_section ) ) ) {
			$new_chunksize = $chunksize + $chunksize;
			$xref_section = file_get_contents( $file_name, true, NULL, $file_offset, $new_chunksize );
			while ( false === ( $end_tag = strpos( $xref_section, 'endstream' ) ) && ( $new_chunksize == strlen( $xref_section ) ) ) {
				$new_chunksize = $new_chunksize + $chunksize;
				$xref_section = file_get_contents( $file_name, true, NULL, $file_offset, $new_chunksize );
			} // while not found
		} // if not found

		if ( false == $end_tag ) {
			$length = 0;
		} else {
			$length = $end_tag - $tag_length;
		}

		if ( false == $end_tag ) {
			return 0;
		}

		return $length;

		$entry_parms = explode( ' ', $entry_parms_string );
		$object_id = $matches[1];
		$count = $matches[2];
		$offset = strlen( $matches[0] );
		$length = $offset + ( 20 * $count );

		if ( $chunksize < $length ) {
			$xref_section = file_get_contents( $file_name, true, NULL, $file_offset, $length );
			$offset = 0;
		}

		while ( $count-- ) {
			$match_count = preg_match( '/(\d+) (\d+) (.)/', $xref_section, $matches, 0, $offset);
			if ( $match_count ) {
				if ( 'n' == $matches[3] ) {
					$key = ( $object_id * 1000 ) + $matches[2];
					if ( ! isset( self::$pdf_indirect_objects[ $key ] ) ) {
						self::$pdf_indirect_objects[ $key ] = array( 'number' => $object_id, 'generation' => (integer) $matches[2], 'start' => (integer) $matches[1] );
					}
				}

				$object_id++;
				$offset += 20;
			} else {
				break;
			}
		}

		return $length;
	}

	/**
	 * Build an array of indirect object definitions
	 * 
	 * Creates the array of indirect object offsets and lengths
	 * @since 2.10
	 *
	 * @param	string	The entire PDF document, passsed by reference
	 *
	 * @return	void
	 */
	private static function _build_pdf_indirect_objects( &$string ) {
		if ( ! is_null( self::$pdf_indirect_objects ) ) {
			return;
		}

		$match_count = preg_match_all( '!(\d+)\\h+(\d+)\\h+obj|endobj|stream(\x0D\x0A|\x0A)|endstream!', $string, $matches, PREG_OFFSET_CAPTURE );
		self::$pdf_indirect_objects = array();
		$object_level = 0;
		$is_stream = false;
		for ( $index = 0; $index < $match_count; $index++ ) {
			if ( $is_stream ) {
				if ( 'endstream' == substr( $matches[0][ $index ][0], 0, 9 ) ) {
					$is_stream = false;
				}
			} elseif ( 'endobj' == substr( $matches[0][ $index ][0], 0, 6 ) ) {
				$object_level--;
				$object_entry['/length'] = $matches[0][ $index ][1] - $object_entry['start'];
				self::$pdf_indirect_objects[ ($object_entry['number'] * 1000) + $object_entry['generation'] ] = $object_entry;
			} elseif ( 'obj' == substr( $matches[0][ $index ][0], -3 ) ) {
				$object_level++;
				$object_entry = array( 
					'number' => $matches[1][ $index ][0],
					'generation' => $matches[2][ $index ][0],
					'start' => $matches[0][ $index ][1] + strlen( $matches[0][ $index ][0] )
					);
			} elseif ( 'stream' == substr( $matches[0][ $index ][0], 0, 6 ) ) {
				$is_stream = true;
			} else {
				/* translators: 1: ERROR tag 2: index */
				error_log( sprintf( _x( '%1$s: _build_pdf_indirect_objects bad value at $index = "%2$d".', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $index ), 0 );
			}
		} // for each match
	}

	/**
	 * Find the offset, length and contents of an indirect object containing a dictionary
	 *
	 * The function searches the entire file, if necessary, to find the last/most recent copy of the object.
	 * This is required because Adobe Acrobat does NOT increment the generation number when it reuses an object.
	 * 
	 * @since 2.10
	 *
	 * @param	string	full path and file name
	 * @param	integer	The object number
	 * @param	integer	The object generation number; default zero (0)
	 * @param	integer	The desired object instance (when multiple instances are present); default "highest/latest"
	 *
	 * @return	mixed	NULL on failure else array( 'start' => offset in the file, 'length' => object length, 'content' => dictionary contents )
	 */
	private static function _find_pdf_indirect_dictionary( $file_name, $object, $generation = 0, $instance = NULL ) {
		$chunksize = 16384;			
		$key = ( $object * 1000 ) + $generation;
		if ( isset( self::$pdf_indirect_objects ) && isset( self::$pdf_indirect_objects[ $key ] ) ) {
			$file_offset = self::$pdf_indirect_objects[ $key ]['start'];
		} else { // found object location
			$file_offset = 0;
		}

		$object_starts = array();
		$object_content = file_get_contents( $file_name, true, NULL, $file_offset, $chunksize );
//error_log( __LINE__ . " MLAPDF::_find_pdf_indirect_dictionary( {$file_name}, {$file_offset} ) object_content = \r\n" . MLAData::mla_hex_dump( $object_content ), 0 );

		/*
		 * Match the object header
		 */
		$pattern = sprintf( '!%1$d\\h+%2$d\\h+obj[\\x00-\\x20]*(<<)!', $object, $generation );
//error_log( __LINE__ . " MLAPDF::_find_pdf_indirect_dictionary( {$object}, {$generation} ) pattern = " . var_export( $pattern, true ), 0 );
		$match_count = preg_match( $pattern, $object_content, $matches, PREG_OFFSET_CAPTURE );
//error_log( __LINE__ . " MLAPDF::_find_pdf_indirect_dictionary( {$match_count} ) matches = " . var_export( $matches, true ), 0 );
		if ( $match_count ) {
			$object_starts[] = array( 'offset' => $file_offset, 'start' => $matches[1][1]);
//error_log( __LINE__ . " MLAPDF::_find_pdf_indirect_dictionary( {$file_offset}, {$matches[1][1]} ) object_content = \r\n" . MLAData::mla_hex_dump( substr( $object_content, $matches[1][1] ), 512 ), 0 );
			$match_count = 0;
		}

		/*
		 * If necessary and possible, advance the $object_content through the file until it contains the start tag
		 */
		if ( 0 == $match_count && ( $chunksize == strlen( $object_content ) ) ) {
			$file_offset += ( $chunksize - 16 );
			$object_content = file_get_contents( $file_name, true, NULL, $file_offset, $chunksize );
			$match_count = preg_match( $pattern, $object_content, $matches, PREG_OFFSET_CAPTURE );
//error_log( __LINE__ . " MLAPDF::_find_pdf_indirect_dictionary( {$match_count} ) matches = " . var_export( $matches, true ), 0 );

			if ( $match_count ) {
				$object_starts[] = array( 'offset' => $file_offset, 'start' => $matches[1][1]);
//error_log( __LINE__ . " MLAPDF::_find_pdf_indirect_dictionary( {$file_offset}, {$matches[1][1]} ) object_content = \r\n" . MLAData::mla_hex_dump( substr( $object_content, $matches[1][1] ), 512 ), 0 );
				$match_count = 0;
			}

			while ( 0 == $match_count && ( $chunksize == strlen( $object_content ) ) ) {
				$file_offset += ( $chunksize - 16 );
				$object_content = file_get_contents( $file_name, true, NULL, $file_offset, $chunksize );
				$match_count = preg_match( $pattern, $object_content, $matches, PREG_OFFSET_CAPTURE );
//error_log( __LINE__ . " MLAPDF::_find_pdf_indirect_dictionary( {$match_count} ) matches = " . var_export( $matches, true ), 0 );

				if ( $match_count ) {
					$object_starts[] = array( 'offset' => $file_offset, 'start' => $matches[1][1]);
//error_log( __LINE__ . " MLAPDF::_find_pdf_indirect_dictionary( {$file_offset}, {$matches[1][1]} ) object_content = \r\n" . MLAData::mla_hex_dump( substr( $object_content, $matches[1][1] ), 512 ), 0 );
					$match_count = 0;
				}
			} // while not found
		} // if not found
//error_log( __LINE__ . " MLAPDF::_find_pdf_indirect_dictionary object_starts = " . var_export( $object_starts, true ), 0 );

		/*
		 * Return the highest/latest instance unless a specific instance is requested
		 */
		$object_count = count( $object_starts );
		if ( is_null( $instance ) ) {
			$object_start = array_pop( $object_starts );
		} else {
			$instance = absint( $instance );
			$object_start = isset( $object_starts[ $instance ] ) ? $object_starts[ $instance ] : NULL;
		}
	
		if ( is_null( $object_start ) ) {
			return NULL;
		} else {
			$file_offset = $object_start['offset'];
			$object_content = file_get_contents( $file_name, true, NULL, $file_offset, $chunksize );
			$start = $object_start['start'];
		}

		/*
		 * If necessary and possible, expand the $object_content until it contains the end tag
		 */
		$pattern = '!>>[\\x00-\\x20]*[endobj|stream]!';
		$match_count = preg_match( $pattern, $object_content, $matches, PREG_OFFSET_CAPTURE, $start );
		if ( 0 == $match_count && ( $chunksize == strlen( $object_content ) ) ) {
			$file_offset = $file_offset + $start;
			$start = 0;
			$new_chunksize = $chunksize + $chunksize;
			$object_content = file_get_contents( $file_name, true, NULL, $file_offset, $new_chunksize );
			$match_count = preg_match( $pattern, $object_content, $matches, PREG_OFFSET_CAPTURE, $start );

			while ( 0 == $match_count && ( $new_chunksize == strlen( $object_content ) ) ) {
				$new_chunksize = $new_chunksize + $chunksize;
				$object_content = file_get_contents( $file_name, true, NULL, $file_offset, $new_chunksize );
				$match_count = preg_match( $pattern, $object_content, $matches, PREG_OFFSET_CAPTURE, $start );
			} // while not found
		} // if not found

		if ( 0 == $match_count ) {
			return NULL;
		}

		if ($match_count) {
			$results = array( 'count' => $object_count, 'start' => $file_offset + $start, 'length' => ($matches[0][1] + 2) - $start );
			$results['content'] = substr( $object_content, $start, $results['length'] );
//error_log( __LINE__ . " MLAPDF::_find_pdf_indirect_dictionary results = " . var_export( $results, true ), 0 );
			return $results;
		} // found trailer

		return NULL; 
	}

	/**
	 * Parse a PDF Unicode (16-bit Big Endian) object
	 * 
	 * @since 2.10
	 *
	 * @param	string	PDF string of 16-bit characters
	 *
	 * @return	string	UTF-8 encoded string
	 */
	private static function _parse_pdf_UTF16BE( &$source_string ) {
		$output = '';
		for ($index = 2; $index < strlen( $source_string ); ) {
			$value = ( ord( $source_string[ $index++ ] ) << 8 ) + ord( $source_string[ $index++ ] );
 			if ( $value < 0x80 ) {
				$output .= chr( $value );
			} elseif ( $value < 0x100 ) {
				$output .= MLAData::$utf8_chars[ $value - 0x80 ];
			} else {
				$output .= '.'; // TODO encode the rest
			}
		}

		return $output;
	}

	/**
	 * Parse a PDF string object
	 * 
	 * Returns an array with one dictionary entry. The array also has a '/length' element containing
	 * the number of bytes occupied by the string in the source string, including the enclosing parentheses. 
	 *
	 * @since 2.10
	 *
	 * @param	string	data within which the string occurs
	 * @param	integer	offset within the source string of the opening '(' character.
	 *
	 * @return	array	( key => array( 'type' => type, 'value' => value, '/length' => length ) ) for the string
	 */
	private static function _parse_pdf_string( &$source_string, $offset ) {
		if ( '(' != $source_string[ $offset ] ) {
			return array( 'type' => 'unknown', 'value' => '', '/length' => 0 );
		}

		/*
		 * Brute force, here we come...
		 */
		$output = '';
		$level = 0;
		$in_string = true;
		$index = $offset + 1;
		while ( $in_string ) {
			$byte = $source_string[ $index++ ];
			if ( '\\' == $byte ) {
				switch ( $source_string[ $index ] ) {
					case chr( 0x0A ):
						if ( chr( 0x0D ) == $source_string[ $index + 1 ] ) {
							$index++;
						}

						break;
					case chr( 0x0D ):
						if ( chr( 0x0A ) == $source_string[ $index + 1 ] ) {
							$index++;
						}

						break;
					case 'n':
						$output .= chr( 0x0A );
						break;
					case 'r':
						$output .= chr( 0x0D );
						break;
					case 't':
						$output .= chr( 0x09 );
						break;
					case 'b':
						$output .= chr( 0x08 );
						break;
					case 'f':
						$output .= chr( 0x0C );
						break;
					default: // could be a 1- to 3-digit octal value
						$digit_limit = $index + 3;
						$digit_index = $index;
						while ( $digit_index < $digit_limit ) {
							if ( ! ctype_digit( $source_string[ $digit_index ] ) ) {
								break;
							} else {
								$digit_index++;
							}
						}

						if ( $digit_count = $digit_index - $index ) {
							$output .= chr( octdec( substr( $source_string, $index, $digit_count ) ) );
							$index += $digit_count - 1;
						} else { // accept the character following the backslash
							$output .= $source_string[ $index ];
						}
				} // switch

				$index++;
			} else { // REVERSE SOLIDUS
				if ( '(' == $byte ) {
					$level++;
				} elseif ( ')' == $byte ) {
					if ( 0 == $level-- ) {
						$in_string = false;
						continue;
					}
				}

				$output .= $byte;
			} // just another 8-bit value, but check for balanced parentheses
		} // $in_string

		return array( 'type' => 'string', 'value' => $output, '/length' => $index - $offset );
	}

	/**
	 * Parse a PDF Linearization Parameter Dictionary object
	 * 
	 * Returns an array of dictionary contents, classified by object type: boolean, numeric, string, hex (string),
	 * indirect (object), name, array, dictionary, stream, and null.
	 * The array also has a '/length' element containing the number of bytes occupied by the
	 * dictionary in the source string, excluding the enclosing delimiters, if passed in.
	 * @since 2.10
	 *
	 * @param	string	data within which the object occurs, typically the start of a PDF document
	 * @param	integer	filesize of the PDF document, for validation purposes, or zero (0) to ignore filesize
	 *
	 * @return	mixed	array of dictionary objects on success, false on failure
	 */
	private static function _parse_pdf_LPD_dictionary( &$source_string, $filesize ) {
		$header = substr( $source_string, 0, 1024 );
		$match_count = preg_match( '!obj[\x00-\x20]*<<(/Linearized).*(>>)[\x00-\x20]*endobj!', $header, $matches, PREG_OFFSET_CAPTURE );

		if ( $match_count ) {
			$LPD = self::_parse_pdf_dictionary( $header, $matches[1][1] );
		}

		return false;
	}

	/**
	 * Parse a PDF dictionary object
	 * 
	 * Returns an array of dictionary contents, classified by object type: boolean, numeric, string, hex (string),
	 * indirect (object), name, array, dictionary, stream, and null.
	 * The array also has a '/length' element containing the number of bytes occupied by the
	 * dictionary in the source string, excluding the enclosing delimiters.
	 *
	 * @since 2.10
	 *
	 * @param	string	data within which the string occurs
	 * @param	integer	offset within the source string of the opening '<<' characters or the first content character.
	 *
	 * @return	array	( '/length' => length, key => array( 'type' => type, 'value' => value ) ) for each dictionary field
	 */
	private static function _parse_pdf_dictionary( &$source_string, $offset ) {
		/*
		 * Find the end of the dictionary
		 */
		if ( '<<' == substr( $source_string, $offset, 2 ) ) {
			$nest = $offset + 2;
		} else {
			$nest = $offset;
		}

		$level = 1;
		do {
			$dictionary_end = strpos( $source_string, '>>', $nest );
			if ( false === $dictionary_end ) {
					/* translators: 1: ERROR tag 2: source offset 3: nest level */
				error_log( sprintf( _x( '%1$s: _parse_pdf_dictionary offset = %2$d, nest = %3$d.', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $offset, $nest ), 0 );
					/* translators: 1: ERROR tag 2: dictionary excerpt */
				error_log( sprintf( _x( '%1$s: _parse_pdf_dictionary no end delimiter dump = %2$s.', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), MLAData::mla_hex_dump( substr( $source_string, $offset, 128 ), 128, 16 ) ), 0 );
				return array( '/length' => 0 );
			}

			$nest = strpos( $source_string, '<<', $nest );
			if ( false === $nest ) {
				$nest = $dictionary_end + 2;
				$level--;
			} elseif ( $nest < $dictionary_end ) {
				$nest += 2;
				$level++;
			} else {
				$nest = $dictionary_end + 2;
				$level--;
			}
		} while ( $level );

		$dictionary_length = $dictionary_end + 2 - $offset;
		$dictionary = array();

		// \x00-\x20 for whitespace
		// \(|\)|\<|\>|\[|\]|\{|\}|\/|\% for delimiters
		$match_count = preg_match_all( '!/([^\x00-\x20|\(|\)|\<|\>|\[|\]|\{|\}|\/|\%]*)([\x00-\x20]*)!', substr( $source_string, $offset, $dictionary_length ), $matches, PREG_OFFSET_CAPTURE );
		$end_data = -1;
		for ( $match_index = 0; $match_index < $match_count; $match_index++ ) {
			$name = $matches[1][ $match_index ][0];
			$value_start = $offset + $matches[2][ $match_index ][1] + strlen( $matches[2][ $match_index ][0] );

			/*
			 * Skip over false matches within a string or nested dictionary
			 */
			if ( $value_start < $end_data ) {
				continue;
			}

			$end_data = -1;
			$value_count = preg_match(
				'!(\/?[^\/\x0D\x0A]*)!',
				substr( $source_string, $value_start, ($dictionary_end - $value_start ) ), $value_matches, PREG_OFFSET_CAPTURE );

			if ( 1 == $value_count ) {
				$value = trim( $value_matches[0][0] );
				$length = strlen( $value );
				$dictionary[ $name ]['value'] = $value;
				if ( ! isset( $value[0] ) ) {
					/* translators: 1: ERROR tag 2: entry name 3: value excerpt */
					error_log( sprintf( _x( '%1$s: _parse_pdf_dictionary bad value [ %2$s ] dump = %3$s', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $name, MLAData::mla_hex_dump( $value, 32, 16 ) ), 0 );
					continue;
				}

				if ( in_array( $value, array( 'true', 'false' ) ) ) {
					$dictionary[ $name ]['type'] = 'boolean';
				} elseif ( is_numeric( $value ) ) {
					$dictionary[ $name ]['type'] = 'numeric';
				} elseif ( '(' == $value[0] ) {
					$dictionary[ $name ] = self::_parse_pdf_string( $source_string, $value_start );
					$end_data = $value_start + $dictionary[ $name ]['/length'];
					unset( $dictionary[ $name ]['/length'] );
				} elseif ( '<' == $value[0] ) {
					if ( '<' == $value[1] ) {
						$dictionary[ $name ]['value'] = self::_parse_pdf_dictionary( $source_string, $value_start );
						$dictionary[ $name ]['type'] = 'dictionary';
						$end_data = $value_start + 4 + $dictionary[ $name ]['value']['/length'];
						unset( $dictionary[ $name ]['value']['/length'] );
					} else {
						$dictionary[ $name ]['type'] = 'hex';
					}
				} elseif ( '/' == $value[0] ) {
					$dictionary[ $name ]['value'] = substr( $value, 1 );
					$dictionary[ $name ]['type'] = 'name';
					$match_index++; // Skip to the next key
				} elseif ( '[' == $value[0] ) {
					$dictionary[ $name ]['type'] = 'array';
					$array_length = strpos( $source_string, ']', $value_start ) - ($value_start + 1);
					$dictionary[ $name ]['value'] = substr( $source_string, $value_start + 1, $array_length );
					$end_data = 2 + $value_start + $array_length;
				} elseif ( 'null' == $value ) {
					$dictionary[ $name ]['type'] = 'null';
				} elseif ( 'stream' == substr( $value, 0, 6 ) ) {
					$dictionary[ $name ]['type'] = 'stream';
				} else {
					$object_count = preg_match( '!(\d+)\h+(\d+)\h+R!', $value, $object_matches );

					if ( 1 == $object_count ) {
						$dictionary[ $name ]['type'] = 'indirect';
						$dictionary[ $name ]['object'] = $object_matches[1];
						$dictionary[ $name ]['generation'] = $object_matches[2];
					} else {
						$dictionary[ $name ]['type'] = 'unknown';
					}
				}
			} else {
				$dictionary[ $matches[1][ $match_index ][0] ] = array( 'value' => '' );
				$dictionary[ $matches[1][ $match_index ][0] ]['type'] = 'nomatch';
			}
		} // foreach match

		$dictionary['/length'] = $dictionary_length;
		return $dictionary;
	}

	/**
	 * Extract dictionary from traditional cross-reference + trailer documents
	 * 
	 * @since 2.10
	 *
	 * @param	string	full path to the desired file
	 * @param	integer	offset within file of the cross-reference table
	 *
	 * @return	mixed	array of "PDF dictionary arrays", newest first, or NULL on failure
	 */
	private static function _extract_pdf_trailer( $file_name, $file_offset ) {
		$chunksize = 16384; 
		$tail = file_get_contents( $file_name, true, NULL, $file_offset, $chunksize );
		$chunk_offset = 0;

		/*
		 * look for traditional xref and trailer
		 */
		if ( 'xref' == substr( $tail, $chunk_offset, 4 ) ) {
			$xref_length =	self::_parse_pdf_xref_section( $file_name, $file_offset + $chunk_offset + 4 );
//error_log( __LINE__ . " MLAPDF::_extract_pdf_trailer xref_length = " . var_export( $xref_length, true ), 0 );
			$chunk_offset += 4 + $xref_length;

			if ( $chunk_offset > ( $chunksize - 1024 ) ) {
				$file_offset += $chunk_offset;
				$tail = file_get_contents( $file_name, true, NULL, $file_offset, $chunksize );
				$chunk_offset = 0; 
			}
//error_log( __LINE__ . " MLAPDF::_extract_pdf_trailer( {$file_offset} ) tail = \r\n" . MLAData::mla_hex_dump( $tail, 0, 16, 0 ), 0 );

			$match_count = preg_match( '/[\x00-\x20]*trailer[\x00-\x20]+/', $tail, $matches, PREG_OFFSET_CAPTURE, $chunk_offset );
//error_log( __LINE__ . " MLAPDF::_extract_pdf_trailer( {$match_count} ) matches = " . var_export( $matches, true ), 0 );
			if ( $match_count ) {
				$chunk_offset = $matches[0][1] + strlen( $matches[0][0] );
				$dictionary = self::_parse_pdf_dictionary( $tail, $chunk_offset );
//error_log( __LINE__ . " MLAPDF::_extract_pdf_trailer dictionary = " . var_export( $dictionary, true ), 0 );

				if ( isset( $dictionary['Prev'] ) ) {
					$other_trailers =  self::_extract_pdf_trailer( $file_name, $dictionary['Prev']['value'] );
				} else {
					$other_trailers = NULL;
				}

				if ( is_array( $other_trailers ) ) {
					$other_trailers = array_merge( $other_trailers, array( $dictionary ) );
					return $other_trailers;
				} else {
					return array( $dictionary );
				}
			} // found 'trailer'
		} else { // found 'xref'
		/*
		 * Look for a cross-reference stream
		 */
		$match_count = preg_match( '!(\d+)\\h+(\d+)\\h+obj[\x00-\x20]*!', $tail, $matches, PREG_OFFSET_CAPTURE );
		if ( $match_count ) {
			$chunk_offset = $matches[0][1] + strlen( $matches[0][0] );

			if ( '<<' == substr( $tail, $chunk_offset, 2) ) {
				$dictionary = self::_parse_pdf_dictionary( $tail, $chunk_offset );

				/*
				 * Parse the cross-reference stream following the dictionary, if present
				 */
				 if ( isset( $dictionary['Type'] ) && 'XRef' == $dictionary['Type']['value'] ) {
		 			$xref_length =	self::_parse_pdf_xref_stream( $file_name, $file_offset + $chunk_offset + (integer) $dictionary['/length'], $dictionary['W']['value'] );
				 }

				if ( isset( $dictionary['Prev'] ) ) {
					$other_trailers =  self::_extract_pdf_trailer( $file_name, $dictionary['Prev']['value'] );
				} else {
					$other_trailers = NULL;
				}

				if ( is_array( $other_trailers ) ) {
					$other_trailers = array_merge( array( $dictionary ), $other_trailers );
					return $other_trailers;
				} else {
					return array( $dictionary );
				}
			} // found cross-reference stream dictionary
		} // found cross-reference stream object
	}

		return NULL;
	}

	/**
	 * Extract Metadata from a PDF file
	 * 
	 * @since 2.10
	 *
	 * @param	string	full path to the desired file
	 *
	 * @return	array	( 'xmp' => array( key => value ), 'pdf' => array( key => value ) ) for each metadata field, in string format
	 */
	public static function mla_extract_pdf_metadata( $file_name ) {
		$xmp = array();
		$metadata = array();
		self::$pdf_indirect_objects = NULL;
		$chunksize = 16384;

		if ( ! file_exists( $file_name ) ) {
			return array( 'xmp' => $xmp, 'pdf' => $metadata );
		}

		$filesize = filesize( $file_name );
		$file_offset = ( $chunksize < $filesize ) ? ( $filesize - $chunksize ) : 0;
		$tail = file_get_contents( $file_name, false, NULL, $file_offset );
//error_log( __LINE__ . " MLAPDF::mla_extract_pdf_metadata( {$file_name}, {$file_offset} ) tail = \r\n" . MLAData::mla_hex_dump( $tail ), 0 );

		if ( 0 == $file_offset ) {
			$header = substr( $tail, 0, 128 );
		} else {
			$header = file_get_contents( $file_name, false, NULL, 0, 128 );
		}
//error_log( __LINE__ . " MLAPDF::mla_extract_pdf_metadata( {$file_name}, {$file_offset} ) header = \r\n" . MLAData::mla_hex_dump( $header ), 0 );

		if ( '%PDF-' == substr( $header, 0, 5 ) ) {
			$metadata['PDF_Version'] = substr( $header, 1, 7 );
			$metadata['PDF_VersionNumber'] = substr( $header, 5, 3 );
		}

		/*
		 * Find the xref and (optional) trailer
		 */
		$match_count = preg_match_all( '/startxref[\x00-\x20]+(\d+)[\x00-\x20]+\%\%EOF/', $tail, $matches, PREG_OFFSET_CAPTURE );
		if ( 0 == $match_count ) {
			/* translators: 1: ERROR tag 2: path and file */
			error_log( sprintf( _x( '%1$s: File "%2$s", startxref not found.', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $path ), 0 );
			return array( 'xmp' => $xmp, 'pdf' => $metadata );
		}

		$startxref = (integer) $matches[1][ $match_count - 1 ][0];
		$trailer_dictionaries = self::_extract_pdf_trailer( $file_name, $startxref );
//error_log( __LINE__ . " MLAPDF::mla_extract_pdf_metadata trailer_dictionaries = " . var_export( $trailer_dictionaries, true ), 0 );
		if ( is_array( $trailer_dictionaries ) ) {
			$info_reference = NULL;
			foreach ( $trailer_dictionaries as $trailer_dictionary ) 
			if ( isset( $trailer_dictionary['Info'] ) ) {
				$info_reference = $trailer_dictionary['Info'];
				break;
			}
//error_log( __LINE__ . " MLAPDF::mla_extract_pdf_metadata info_reference = " . var_export( $info_reference, true ), 0 );

			if ( isset( $info_reference ) ) {	
				$info_object = self::_find_pdf_indirect_dictionary( $file_name, $info_reference['object'], $info_reference['generation'] );

				/*
				 * Handle single or multiple Info instances
				 */
				$info_objects = array();
				if ( $info_object ) {
					if ( 1 == $info_object['count'] ) {
						$info_objects[] = $info_object;
					} else {
						for ( $index = 0; $index < $info_object['count']; $index++ ) {
							$info_objects[] = self::_find_pdf_indirect_dictionary( $file_name, $info_reference['object'], $info_reference['generation'], $index );
						}
					}
				}
//error_log( __LINE__ . " MLAPDF::mla_extract_pdf_metadata info_objects = " . var_export( $info_objects, true ), 0 );

				foreach( $info_objects as $info_object ) {
					$info_dictionary = self::_parse_pdf_dictionary( $info_object['content'], 0 );
//error_log( __LINE__ . " MLAPDF::mla_extract_pdf_metadata info_dictionary = " . var_export( $info_dictionary, true ), 0 );
					unset( $info_dictionary['/length'] );

					foreach ( $info_dictionary as $name => $value ) {
						if ( 'string' == $value['type'] ) {
							$prefix = substr( $value['value'], 0, 2 );
							if ( 'D:' == $prefix ) {
								$metadata[ $name ] = MLAData::mla_parse_pdf_date( $value['value'] );
							} elseif ( ( chr(0xFE) . chr(0xFF) ) == $prefix )  {
								$metadata[ $name ] = self::_parse_pdf_UTF16BE( $value['value'] );
							} else {
								$metadata[ $name ] = $value['value'];
							}
						 } else {
							$metadata[ $name ] = $value['value'];
						 }
					} // each info entry
				} // foreach Info object
				
				/*
				 * Remove spurious "Filter" dictionaries
				 */
				unset( $metadata['Filter'] );
				unset( $metadata['Length'] );
				unset( $metadata['Length1'] );
			} // found Info reference
//error_log( __LINE__ . ' MLAPDF::mla_extract_pdf_metadata pdf metadata = ' . var_export( $metadata, true ), 0 );

			/*
			 * Look for XMP Metadata
			 */
			$root_reference = NULL;
//error_log( __LINE__ . " MLAPDF::mla_extract_pdf_metadata info_dictionary = " . var_export( $info_dictionary, true ), 0 );
			foreach ( $trailer_dictionaries as $trailer_dictionary ) {
				if ( isset( $trailer_dictionary['Root'] ) ) {
					$root_reference = $trailer_dictionary['Root'];
					break;
				}
			}
//error_log( __LINE__ . " MLAPDF::mla_extract_pdf_metadata root_reference = " . var_export( $root_reference, true ), 0 );
			
			if ( isset( $root_reference ) ) {	
				$root_object = self::_find_pdf_indirect_dictionary( $file_name, $root_reference['object'], $root_reference['generation'] );
//error_log( __LINE__ . " MLAPDF::mla_extract_pdf_metadata root_object = " . var_export( $root_object, true ), 0 );
				if ( $root_object ) {
					$root_dictionary = self::_parse_pdf_dictionary( $root_object['content'], 0 );
//error_log( __LINE__ . " MLAPDF::mla_extract_pdf_metadata root_dictionary = " . var_export( $root_dictionary, true ), 0 );
					unset( $root_dictionary['/length'] );

					if ( isset( $root_dictionary['Metadata'] ) ) {
						$xmp_object = self::_find_pdf_indirect_dictionary( $file_name, $root_dictionary['Metadata']['object'], $root_dictionary['Metadata']['generation'] );
//error_log( __LINE__ . " MLAPDF::mla_extract_pdf_metadata xmp_object = " . var_export( $xmp_object, true ), 0 );
						$xmp = MLAData::mla_parse_xmp_metadata( $file_name, $xmp_object['start'] + $xmp_object['length'] );

						if ( is_array( $xmp ) ) {
							$metadata = array_merge( $metadata, $xmp );
						} else {
							$xmp = array();
							$xmp = MLAData::mla_parse_xmp_metadata( $file_name, 0 );
//error_log( __LINE__ . ' MLAPDF::mla_extract_pdf_metadata recovered xmp = ' . var_export( $xmp, true ), 0 );
						}
					} // found Metadata reference
				} // found Root object
			} // found Root reference
		} // found trailer_dictionaries
//error_log( __LINE__ . ' MLAPDF::mla_extract_pdf_metadata pdf = ' . var_export( $metadata, true ), 0 );
//error_log( __LINE__ . ' MLAPDF::mla_extract_pdf_metadata xmp = ' . var_export( $xmp, true ), 0 );

		return array( 'xmp' => $xmp, 'pdf' => $metadata );
	}
} // class MLAPDF
?>
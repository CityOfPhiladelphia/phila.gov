<?php
/**
 * Database and template file access for MLA needs
 *
 * @package Media Library Assistant
 * @since 0.1
 */

/**
 * Class MLA (Media Library Assistant) Data provides database and template file access for MLA needs
 *
 * The _template functions are inspired by the book "WordPress 3 Plugin Development Essentials."
 * Templates separate HTML markup from PHP code for easier maintenance and localization.
 *
 * @package Media Library Assistant
 * @since 0.1
 */
class MLAData {
	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 0.1
	 */
	public static function initialize() {
		self::$search_parameters =& MLAQuery::$search_parameters;
		self::$query_parameters =& MLAQuery::$query_parameters;

		add_action( 'save_post', 'MLAData::mla_save_post_action', 10, 1);
		add_action( 'edit_attachment', 'MLAData::mla_save_post_action', 10, 1);
		add_action( 'add_attachment', 'MLAData::mla_save_post_action', 10, 1);
	}

	/**
	 * Find a complete template, balancing opening and closing delimiters
	 *
	 * @since 1.50
	 *
	 * @param	string	A string possibly starting with '[+template:'
	 *
	 * @return	string	'' or template string starting with '[+template:' and ending with the matching '+]'
	 */
	private static function _find_template_substring( $tpl ) {
		if ( '[+template:' == substr( $tpl, 0, 11 ) ) {
			$nest = 11;
			$level = 1;
			do {
				$template_end = strpos( $tpl, '+]', $nest );
				if ( false === $template_end ) {
					/* translators: 1: ERROR tag 2: template excerpt */
					MLACore::mla_debug_add( __LINE__ . sprintf( _x( '%1$s: _find_template_substring no template end delimiter, tail = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), substr( $tpl, $offset ) ), MLACore::MLA_DEBUG_CATEGORY_ANY );
					return '';
				}

				$nest = strpos( $tpl, '[+', $nest );
				if ( false === $nest ) {
					$nest = $template_end + 2;
					$level--;
				} elseif ( $nest < $template_end ) {
					$nest += 2;
					$level++;
				} else {
					$nest = $template_end + 2;
					$level--;
				}

			} while ( $level );

			$template_length = $template_end + 2;
			$template_content = substr( $tpl, 0, $template_length );
			return $template_content;
		} // found template

		return '';
	}

	/**
	 * Expand a template, replacing placeholders with their values
	 *
	 * Will return an array of values if one or more of the placeholders returns an array.
	 *
	 * @since 1.50
	 *
	 * @param	string	A formatting string containing [+placeholders+]
	 * @param	array	An associative array containing keys and values e.g. array('key' => 'value')
	 *
	 * @return	mixed	string or array, depending on placeholder values. Placeholders corresponding
	 * to the keys of the markup_values will be replaced with their values.
	 */
	public static function mla_parse_array_template( $tpl, $markup_values ) {
		$result = array();	
		$offset = 0;
		while ( false !== $start = strpos( $tpl, '[+', $offset ) ) {
			if ( $offset < $start ) {
				$result[] = substr( $tpl, $offset, ( $start - $offset ) );
			}

			if ( $template_content = self::_find_template_substring( substr( $tpl, $start ) ) ) {
				$template_length = strlen( $template_content );
				$template_content = substr( $template_content, 11, $template_length - (11 + 2) );
				$template_content = self::_expand_field_level_template( $template_content, $markup_values, true );

				foreach ( $template_content as $value )
					$result[] = $value;

				$offset = $start + $template_length;
			} else { // found template
				if ( false === $end = strpos( $tpl, '+]', $offset ) ) {
					/* translators: 1: ERROR tag 2: template excerpt */
					MLACore::mla_debug_add( __LINE__ . sprintf( _x( '%1$s: mla_parse_array_template no template end delimiter, tail = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), substr( $tpl, $offset ) ), MLACore::MLA_DEBUG_CATEGORY_ANY );
					return $tpl;
				} // no end delimiter

				$key = substr( $tpl, $start + 2, $end - $start - 2 );
				if ( isset( $markup_values[ $key ] ) ) {
					$result[] = $markup_values[ $key ];
				} else { // found key and scalar value
					$result[] = substr( $tpl, $start, ( $end + 2 ) - $start );
				}

				$offset = $end + 2;
			} // simple substitution
		} // while substitution parameter present

		if ( $offset < strlen( $tpl ) ) {
			$result[] = substr( $tpl, $offset );
		}

		/*
		 * Build a final result, eliminating empty elements and expanding array elements
		 */
		$final = array();
		foreach ( $result as $element ) {
			if ( is_scalar( $element ) ) {
				$element = trim( $element );
				if ( ! empty( $element ) ) {
					$final[] = $element;	
				}
			} elseif ( is_array( $element ) ) {
				foreach ($element as $key => $value ) {
					if ( is_scalar( $value ) ) {
						$value = trim( $value );
					} elseif ( ! empty( $value ) ) {
						$value = var_export( $value, true );
					}

					/*
					 * Preserve any keys with string values
					 */
					if ( ! empty( $value ) ) {
						if ( is_integer( $key ) ) {
							$final[] = $value;
						} else {
							$final[ $key ] = $value;					
						}
					}
				}
			} elseif ( ! empty( $element ) ) {
				$final[] = var_export( $element, true );
			}
		}

		if ( 1 == count( $final ) ) {
			$final = $final[0];
		}

		return $final;
	}

	/**
	 * Expand a template, replacing placeholders with their values
	 *
	 * A simple parsing function for basic templating.
	 *
	 * @since 0.1
	 *
	 * @param	string	A formatting string containing [+placeholders+]
	 * @param	array	An associative array containing keys and values e.g. array('key' => 'value')
	 *
	 * @return	strng	Placeholders corresponding to the keys of the markup_values will be replaced with their values.
	 */
	public static function mla_parse_template( $tpl, $markup_values ) {
		/*
		 * If templates are present we must step through $tpl and expand them
		 */
		if ( isset( $markup_values['[+template_count+]'] ) ) {
			$offset = 0;
			while ( false !== $start = strpos( $tpl, '[+', $offset ) ) {
				if ( $template_content = self::_find_template_substring( substr( $tpl, $start ) ) ) {
					$template_length = strlen( $template_content );
					$template_content = substr( $template_content, 11, $template_length - (11 + 2) );
					$template_content = self::_expand_field_level_template( $template_content, $markup_values );
					$tpl = substr_replace( $tpl, $template_content, $start, $template_length );
					$offset = $start;
				} else { // found template
					if ( false === $end = strpos( $tpl, '+]', $offset ) ) {
					/* translators: 1: ERROR tag 2: template excerpt */
					MLACore::mla_debug_add( __LINE__ . sprintf( _x( '%1$s: mla_parse_template no end delimiter, tail = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), substr( $tpl, $offset ) ), MLACore::MLA_DEBUG_CATEGORY_ANY );
						return $tpl;
					} // no end delimiter

					$key = substr( $tpl, $start + 2, $end - $start - 2 );
					if ( isset( $markup_values[ $key ] ) && is_scalar( $markup_values[ $key ] ) ) {
						$tpl = substr_replace( $tpl, $markup_values[ $key ], $start, strlen( $key ) + 4 );
						$offset = $start;
					} else { // found key and scalar value
						$offset += strlen( $key ) + 4;
					}
				} // simple substitution
			} // while substitution parameter present
		} else { // template(s) present
			/*
			 * No templates means a simple string substitution will suffice
			 */
			foreach ( $markup_values as $key => $value ) {
				if ( is_scalar( $value ) ) {
					$tpl = str_replace( '[+' . $key . '+]', $value, $tpl );
				}
			}
		}

		return $tpl;
	}

	/**
	 * Find a complete (test) element, balancing opening and closing delimiters
	 *
	 * @since 1.50
	 *
	 * @param	string	A string possibly starting with '('
	 *
	 * @return	string	'' or template string starting with '(' and ending with the matching ')'
	 */
	private static function _find_test_substring( $tpl ) {
		if ( '(' == $tpl[0] ) {
			$nest = 1;
			$level = 1;
			do {
				$test_end = strpos( $tpl, ')', $nest );
				if ( false === $test_end ) {
					/* translators: 1: ERROR tag 2: template string */
					MLACore::mla_debug_add( __LINE__ . sprintf( _x( '%1$s: _find_test_substring no end delimiter, tail = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), substr( $tpl, $nest ) ), MLACore::MLA_DEBUG_CATEGORY_ANY );
					return '';
				}

				$nest = strpos( $tpl, '(', $nest );
				if ( false === $nest ) {
					$nest = $test_end + 1;
					$level--;
				} elseif ( $nest < $test_end ) {
					$nest += 1;
					$level++;
				} else {
					$nest = $test_end + 1;
					$level--;
				}
			} while ( $level );

			$test_length = $test_end + 1;
			$test_content = substr( $tpl, 0, $test_length );
			return $test_content;
		} // found test element

		return '';
	}

	/**
	 * Convert field-level "template:" string into its component parts
	 *
	 * @since 1.50
	 *
	 * @param	string	Template content with string, test and choice elements
	 *
	 * @return	array	( node => array( type => "string | test | choice | template", length => bytes, value => string | node(s) ) )
	 */
	private static function _parse_field_level_template( $tpl ) {
		$index = 0;
		$max_length = strlen( $tpl );
		$test_level = 0;
		$output = '';
		$output_values = array();
		$choice_values = array();
		while ( $index < $max_length ) {
			$byte = $tpl[ $index++ ];
			if ( '\\' == $byte ) {
				if ( $index == $max_length ) {
					$output .= $byte;
					continue;
				} // template ends with a backslash

				switch ( $tpl[ $index ] ) {
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
						if ( $max_length < ( $digit_limit = $index + 3 ) ) {
							$digit_limit = $max_length;
						}

						$digit_index = $index;
						while ( $digit_index < $digit_limit )
							if ( ! ctype_digit( $tpl[ $digit_index ] ) ) {
								break;
							} else {
								$digit_index++;
							}

						if ( $digit_count = $digit_index - $index ) {
							$output .= chr( octdec( substr( $tpl, $index, $digit_count ) ) );
							$index += $digit_count - 1;
						} else { // accept the character following the backslash
							$output .= $tpl[ $index ];
						}
				} // switch

				$index++;
			} // REVERSE SOLIDUS (backslash)
			elseif ( '(' == $byte ) {
				if ( ! empty( $output ) ) {
					$output_values[] = array( 'type' => 'string', 'value' => $output, 'length' => strlen( $output ) );
					$output = '';				
				}

				$test_content = self::_find_test_substring( substr( $tpl, $index - 1 ) );
				if ( 2 < $test_length = strlen( $test_content ) ) {
					$values = self::_parse_field_level_template( substr( $test_content, 1, strlen( $test_content ) - 2 ) );
					$output_values[] = array( 'type' => 'test', 'value' => $values, 'length' => strlen( $test_content ) );
					$index += strlen( $test_content ) - 1;
				} // found a value
				elseif ( 2 == $test_length ) {
					$index++; // empty test string
				} else {
					$test_content = __( 'ERROR', 'media-library-assistant' ) . ': ' . __( 'Test; no closing parenthesis ', 'media-library-assistant' );
					$output_values[] = array( 'type' => 'string', 'value' => $test_content, 'length' => strlen( $test_content ) );
				} // bad test string
			} // (test) element
			elseif ( '|' == $byte ) {
				/*
				 * Turn each alternative within a choice element into a conditional
				 */

				if ( ! empty( $output ) ) {
					$output_values[] = array( 'type' => 'string', 'value' => $output, 'length' => strlen( $output ) );
					$output = '';				
				}

				$length = 0;
				foreach ( $output_values as $value ) 
					if ( isset( $value['length'] ) ) {
						$length += $value['length'];
					}

				$choice_values[] = array( 'type' => 'test', 'value' => $output_values, 'length' => $length );
				$output_values = array();
			} // choice element
			elseif ( '[' == $byte && '+template:' == substr( $tpl, $index, 10 ) ) {
				if ( ! empty( $output ) ) {
					$output_values[] = array( 'type' => 'string', 'value' => $output, 'length' => strlen( $output ) );
					$output = '';				
				}

				$template_content = self::_find_template_substring( substr( $tpl, $index - 1 ) );
				$values = self::_parse_field_level_template( substr( $template_content, 11, strlen( $template_content ) - (11 + 2) ) );
				if ( 'template' == $values['type'] ) {
					$output_values = array_merge( $output_values, $values['value'] );
				} else {
					$output_values[] = $values;
				}

				$index += strlen( $template_content ) - 1;
			} // nested template
			elseif ( '[' == $byte ) {
				$match_count = preg_match( '/\[\+.+?\+\]/', $tpl, $matches, 0, $index - 1 );
				if ( $match_count ) {
					// found substitution parameter
					$output .= $matches[0];
					$index += strlen( $matches[0] ) - 1;
				} else {
					$output .= $byte;
				}
			} // maybe substitution parameter
			else {
				$output .= $byte;
			}
		} // $index < $max_length

		if ( ! empty( $output ) ) {
			$output_values[] = array( 'type' => 'string', 'value' => $output, 'length' => strlen( $output ) );
		}

		if ( ! empty( $choice_values ) ) {
			if ( ! empty( $output_values ) ) {
				$length = 0;
				foreach ( $output_values as $value ) 
					if ( isset( $value['length'] ) ) {
						$length += $value['length'];
					}

				$choice_values[] = array( 'type' => 'test', 'value' => $output_values, 'length' => $length );
			}

			return array( 'type' => 'choice', 'value' => $choice_values, 'length' => $max_length );
		}

		if ( 1 == count( $output_values ) ) {
			return $output_values[0];
		}

		return array ( 'type' => 'template', 'value' => $output_values, 'length' => $max_length );
	}

	/**
	 * Analyze a field-level "template:" element, expanding Field-level Markup Substitution Parameters
	 *
	 * Will return an array of values if one or more of the placeholders returns an array.
	 *
	 * @since 1.50
	 *
	 * @param	array	A field-level template element node
	 * @param	array	An array of markup substitution values
	 *
	 * @return	mixed	string or array, depending on placeholder values. Placeholders corresponding to the keys of the markup_values will be replaced with their values.
	 */
	private static function _evaluate_template_array_node( $node, $markup_values = array() ) {
		$result = array();
		/*
		 * Check for an array of sub-nodes
		 */
		if ( ! isset( $node['type'] ) ) {
			foreach ( $node as $value ) {
				$node_result = self::_evaluate_template_array_node( $value, $markup_values );
				foreach ( $node_result as $value )
					$result[] = $value;
			}
		} else { // array of sub-nodes
			switch ( $node['type'] ) {
				case 'string':
					$result[] = self::mla_parse_array_template( $node['value'], $markup_values );
					break;
				case 'test':
					$node_value = $node['value'];

					if ( isset( $node_value['type'] ) ) {
						$node_result = self::_evaluate_template_array_node( $node_value, $markup_values );
						foreach ( $node_result as $value )
							$result[] = $value;
					} else { // single node
						foreach ( $node_value as $value ) {
							$node_result = self::_evaluate_template_array_node( $value, $markup_values );
							foreach ( $node_result as $value )
								$result[] = $value;
						}
					} // array of nodes

					foreach ($result as $element )
						if ( is_scalar( $element ) && false !== strpos( $element, '[+' ) ) {
							$result = array();
							break;
						} elseif ( is_array( $element ) ) {
							foreach ( $element as $value ) 
								if ( is_scalar( $value ) && false !== strpos( $value, '[+' ) ) {
									$result = array();
									break;
								}
						} // is_array

					break;
				case 'choice':
					foreach ( $node['value'] as $value ) {
						$node_result = self::_evaluate_template_array_node( $value, $markup_values );
						if ( ! empty( $node_result ) ) {
							foreach ( $node_result as $value )
								$result[] = $value;
							break;
						}
					}

					break;
				case 'template':
					foreach ( $node['value'] as $value ) {
						$node_result = self::_evaluate_template_array_node( $value, $markup_values );
						foreach ( $node_result as $value )
							$result[] = $value;
					}

					break;
				default:
					/* translators: 1: ERROR tag 2: node type, e.g., template */
					MLACore::mla_debug_add( __LINE__ . sprintf( _x( '%1$s: _evaluate_template_array_node unknown type "%2$s".', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $node ), MLACore::MLA_DEBUG_CATEGORY_ANY );
			} // node type
		} // isset node type

		return $result;				
	}

	/**
	 * Analyze a field-level "template:" element, expanding Field-level Markup Substitution Parameters
	 *
	 * @since 1.50
	 *
	 * @param	array	A field-level template element node
	 * @param	array	An array of markup substitution values
	 *
	 * @return	string	String with expanded values, if any
	 */
	private static function _evaluate_template_node( $node, $markup_values = array() ) {
		$results = '';
		/*
		 * Check for an array of sub-nodes
		 */
		if ( ! isset( $node['type'] ) ) {
			foreach ( $node as $value )
				$results .= self::_evaluate_template_node( $value, $markup_values );

			return $results;
		} // array of sub-nodes

		switch ( $node['type'] ) {
			case 'string':
				return self::mla_parse_template( $node['value'], $markup_values );
			case 'test':
				$node_value = $node['value'];

				if ( isset( $node_value['type'] ) ) {
					$results = self::_evaluate_template_node( $node_value, $markup_values );
				} else { // single node
					foreach ( $node_value as $value )
						$results .= self::_evaluate_template_node( $value, $markup_values );
				} // array of nodes

				if ( false === strpos( $results, '[+' ) ) {
					return $results;
				}

				break;
			case 'choice':
				foreach ( $node['value'] as $value ) {
					$results = self::_evaluate_template_node( $value, $markup_values );
					if ( ! empty( $results ) ) {
						return $results;
					}
				}

				break;
			case 'template':
				foreach ( $node['value'] as $value )
					$results .= self::_evaluate_template_node( $value, $markup_values );

				return $results;
			default:
				/* translators: 1: ERROR tag 2: node type, e.g., template */
				MLACore::mla_debug_add( __LINE__ . sprintf( _x( '%1$s: _evaluate_template_node unknown type "%2$s".', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $node ), MLACore::MLA_DEBUG_CATEGORY_ANY );
		} // node type

		return '';				
	}

	/**
	 * Analyze a field-level "template:" element, expanding Field-level Markup Substitution Parameters
	 *
	 * @since 1.50
	 *
	 * @param	string	A formatting string containing [+placeholders+]
	 * @param	array	An array of markup substitution values
	 * @param	boolean	True to return array value(s), false to return a string
	 *
	 * @return	mixed	Element with expanded string/array values, if any
	 */
	private static function _expand_field_level_template( $tpl, $markup_values = array(), $return_arrays = false ) {
		/*
	 	 * Step 1: parse the template and build the tree of its elements
		 * root node => array( type => "string | test | choice | template", value => string | node(s) )
		 */
		$root_element = self::_parse_field_level_template( $tpl );
		unset( $markup_values['[+template_count+]'] );

		/*
		 * Step 2: Remove all the empty elements from the $markup_values,
		 * so the evaluation of conditional and choice elements is simplified.
		 */
		foreach ( $markup_values as $key => $value ) {
			if ( is_scalar( $value ) ) {
				$value = trim( $value );
			}

			if ( empty( $value ) ) {
				unset( $markup_values[ $key ] );
			}
		}

		/*
		 * Step 3: walk the element tree and process each node
		 */
		if ( $return_arrays ) {
			$results = self::_evaluate_template_array_node( $root_element, $markup_values );
		} else {
			$results = self::_evaluate_template_node( $root_element, $markup_values );
		}

		return $results;
	}

	/**
	 * Process an markup field array value according to the supplied data-format option
	 *
	 * @since 1.50
	 *
	 * @param	array	an array of scalar values
	 * @param	string	data option; 'text'|'single'|'export'|'array'|'multi'
	 * @param	boolean	Optional: for option 'multi', retain existing values
	 *
	 * @return	array	( parameter => value ) for all field-level parameters and anything in $markup_values
	 */
	private static function _process_field_level_array( $record, $option = 'text', $keep_existing = false ) {
		switch ( $option ) {
			case 'single':
				$text = sanitize_text_field( current( $record ) );
				break;
			case 'export':
				$text = sanitize_text_field( var_export( $record, true ) );
				break;
			case 'unpack':
				if ( is_array( $record ) ) {
					$clean_data = array();
					foreach ( $record as $key => $value ) {
						if ( is_array( $value ) ) {
							$clean_data[ $key ] = '(ARRAY)';
						} elseif ( is_string( $value ) ) {
							$clean_data[ $key ] = self::_bin_to_utf8( substr( $value, 0, 256 ) );
						} else {
							$clean_data[ $key ] = $value;
						}
					}

					$text = sanitize_text_field( var_export( $clean_data, true) );
				} else {
					$text = sanitize_text_field( var_export( $record, true ) );
				}
				break;
			case 'multi':
				$record[0x80000000] = 'multi';
				$record[0x80000001] = $keep_existing;
				// fallthru
			case 'array':
				$text = $record;
				break;
			default: // or 'text'
				$text = '';
				foreach ( $record as $term ) {
					$term_name = sanitize_text_field( $term );
					$text .= strlen( $text ) ? ', ' . $term_name : $term_name;
				}
		} // $option

		return $text;
	}

	/**
	 * Process an argument list within a field-level parameter format specification
	 *
	 * @since 2.02
	 *
	 * @param	string	arguments, e.g., ('d/m/Y H:i:s' , "arg, \" two" ) without parens
	 *
	 * @return	array	individual arguments, e.g. array( 0 => 'd/m/Y H:i:s', 1 => 'arg, \" two' )
	 */
	private static function _parse_arguments( $argument_string ) {
		$argument_string = trim( $argument_string, " \n\t\r\0\x0B," );
		$arguments = array();

		while ( strlen( $argument_string ) ) {
			$argument = '';
			$index = 0;

			// Check for enclosing quotes
			$delimiter = $argument_string[0];
			if ( '\'' == $delimiter || '"' == $delimiter ) {
				$index++;
			} else {
				$delimiter = '';
			}

			while ( $index < strlen( $argument_string ) ) {
				$byte = $argument_string[ $index++ ];
				if ( '\\' == $byte ) {
					switch ( $source_string[ $index ] ) {
						case 'n':
							$argument .= chr( 0x0A );
							break;
						case 'r':
							$argument .= chr( 0x0D );
							break;
						case 't':
							$argument .= chr( 0x09 );
							break;
						case 'b':
							$argument .= chr( 0x08 );
							break;
						case 'f':
							$argument .= chr( 0x0C );
							break;
						default: // could be a 1- to 3-digit octal value
							$digit_limit = $index + 3;
							$digit_index = $index;
							while ( $digit_index < $digit_limit ) {
								if ( ! ctype_digit( $argument_string[ $digit_index ] ) ) {
									break;
								} else {
									$digit_index++;
								}
							}

							if ( $digit_count = $digit_index - $index ) {
								$argument .= chr( octdec( substr( $argument_string, $index, $digit_count ) ) );
								$index += $digit_count - 1;
							} else { // accept the character following the backslash
								$argument .= $argument_string[ $index ];
							}
					} // switch

					$index++;
				} else { // backslash
					if ( $delimiter == $byte || ( empty( $delimiter ) && ',' == $byte ) ) {
						break;
					}

					$argument .= $byte;
				} // just another 8-bit value, but check for closing delimiter
			} // index < strlen

			$arguments[] = $argument;
			$argument_string = trim( substr( $argument_string, $index ), " \n\t\r\0\x0B," );
		} // strlen( $argument_string )

		return $arguments;
	}

	/**
	 * Apply field-level format options to field-level content
	 *
	 * @since 2.10
	 *
	 * @param	string	field-level content
	 * @param	array	format code and aguments
	 *
	 * @return	string	formatted field-level content
	 */
	public static function mla_apply_field_level_format( $value, $args ) {
		if ( 'attr' == $args['format'] ) {
			$value = esc_attr( $value );
		} elseif ( 'url' == $args['format'] ) {
			$value = urlencode( $value );
		} elseif ( ( 'commas' == $args['format'] ) && is_numeric( $value ) ) {
			$value = number_format( (float)$value );
		} elseif ( 'timestamp' == $args['format'] && is_numeric( $value ) ) {
			/*
			 * date "Returns a string formatted according to the given format string using the given integer"
			 */
			$format = empty( $args['args'] ) ? 'd/m/Y H:i:s' : $args['args'];
			if ( is_array( $format ) ) {
				$format = $format[0];
			}

			$value = date( $format , (integer) $value );
		} elseif ( 'date' == $args['format'] ) {
			/*
			 * strtotime will "Parse about any English textual datetime description into a Unix timestamp"
			 * If it succeeds we can format the timestamp for display
			 */
			$format = empty( $args['args'] ) ? 'd/m/Y H:i:s' : $args['args'];
			$timestamp = strtotime( $value );
			if( false !== $timestamp ) {
				if ( is_array( $format ) ) {
					$format = $format[0];
				}

				$value = date( $format, $timestamp );
			}
		} elseif ( 'fraction' == $args['format'] ) {
			$show_fractions = true;
			if ( ! empty( $args['args'] ) ) {
				if ( is_array( $args['args'] ) ) {
					if ( is_numeric( $args['args'][0] ) ) {
						$format = '%1$+.' . absint( $args['args'][0] ) . 'f';
					} else {
						$format = $args['args'][0];
					}

					$show_fractions = ( 'false' !== strtolower( trim( $args['args'][1] ) ) );
				} else {
					if ( is_numeric( $args['args'] ) ) {
						$format = '%1$+.' . absint( $args['args'] ) . 'f';
					} else {
						$format = $args['args'];
					}
				}
			} else {
				$format = '%1$+.2f';
			}

			$fragments = array_map( 'intval', explode( '/', $value ) );
			if ( 1 == count( $fragments ) ) {
				$value = trim( $value );
				if ( ! empty( $value ) ) {
					$value = $value;
				}
			} else {
				if ( $fragments[0] ) {
					if ( 1 == $fragments[1] ) {
						$value = sprintf( '%1$+d', $fragments[0] );
					} elseif ( 0 != $fragments[1] ) {
						$value = $fragments[0] / $fragments[1];
						if ( $show_fractions && ( -1 <= $value ) && ( 1 >= $value ) ) {
							$value = sprintf( '%1$+d/%2$d', $fragments[0], $fragments[1] );
						} else {
							$value = sprintf( $format, $value );
						}
					} // fractional value
				} // non-zero numerator
			} // valid denominator
		} elseif ( 'substr' == $args['format'] ) {
			$start = 0;
			$length = strlen( $value );

			if ( ! empty( $args['args'] ) ) {
				if ( is_array( $args['args'] ) ) {
					$start = intval( $args['args'][0] );

					if ( 1 < count( $args['args'] ) ) {
						$length = intval( $args['args'][1] );
					}
				} else {
					$start = intval( $args['args'] );
				}
			}

			if ( false === $value = substr( $value, $start, $length ) ) {
				$value = '';
			}
		} 

		return $value;
	}

	/**
	 * Analyze a template, expanding Field-level Markup Substitution Parameters
	 *
	 * Field-level parameters must have one of the following prefix values:
	 * template, request, query, custom, terms, meta, iptc, exif, xmp, pdf.
	 * All but request and query require an attachment ID.
	 *
	 * @since 1.50
	 *
	 * @param	string	A formatting string containing [+placeholders+]
	 * @param	array	Optional: an array of values from the query, if any, e.g. shortcode parameters
	 * @param	array	Optional: an array of values to add to the returned array
	 * @param	integer	Optional: attachment ID for attachment-specific placeholders
	 * @param	boolean	Optional: for option 'multi', retain existing values
	 * @param	string	Optional: default option value
	 *
	 * @return	array	( parameter => value ) for all field-level parameters and anything in $markup_values
	 */
	public static function mla_expand_field_level_parameters( $tpl, $query = NULL, $markup_values = array(), $post_id = 0, $keep_existing = false, $default_option = 'text' ) {
		static $cached_post_id = 0, $item_metadata = NULL, $attachment_metadata = NULL, $id3_metadata = NULL;
		if ( $cached_post_id != $post_id ) {
			$item_metadata = NULL;
			$attachment_metadata = NULL;
			$id3_metadata = NULL;
			$cached_post_id = $post_id;
		}

		$placeholders = self::mla_get_template_placeholders( $tpl, $default_option );
		$template_count = 0;
		foreach ($placeholders as $key => $value ) {
			if ( isset( $markup_values[ $key ] ) ) {
				continue;
			}

			switch ( $value['prefix'] ) {
				case 'template':
					$markup_values = self::mla_expand_field_level_parameters( $value['value'], $query , $markup_values, $post_id, $keep_existing, $default_option );
					$template_count++;
					break;
				case 'meta':
					if ( is_null( $item_metadata ) ) {
						if ( 0 < $post_id ) {
							$item_metadata = get_metadata( 'post', $post_id, '_wp_attachment_metadata', true );
						} else {
							break;
						}
					}

					$markup_values[ $key ] = self::mla_find_array_element( $value['value'], $item_metadata, $value['option'] );
					break;
				case 'query':
					if ( isset( $query ) && isset( $query[ $value['value'] ] ) ) {
						$markup_values[ $key ] = $query[ $value['value'] ];
					} else {
						$markup_values[ $key ] = '';
					}

					break;
				case 'request':
					if ( isset( $_REQUEST[ $value['value'] ] ) ) {
						$record = $_REQUEST[ $value['value'] ];
					} else {
						$record = '';
					}

					if ( is_scalar( $record ) ) {
						$text = sanitize_text_field( (string) $record );
					} elseif ( is_array( $record ) ) {
						if ( 'export' == $value['option'] ) {
							$text = sanitize_text_field( var_export( $record, true ) );
						} else {
							$text = '';
							foreach ( $record as $term ) {
								$term_name = sanitize_text_field( $term );
								$text .= strlen( $text ) ? ',' . $term_name : $term_name;
							}
						}
					} // is_array

					$markup_values[ $key ] = $text;
					break;
				case 'terms':
					if ( 0 < $post_id ) {
						$terms = get_object_term_cache( $post_id, $value['value'] );
						if ( false === $terms ) {
							$terms = wp_get_object_terms( $post_id, $value['value'] );
							wp_cache_add( $post_id, $terms, $value['value'] . '_relationships' );
						}
					} else {
						break;
					}

					$text = '';
					if ( is_wp_error( $terms ) ) {
						$text = implode( ',', $terms->get_error_messages() );
					} elseif ( ! empty( $terms ) ) {
						if ( 'single' == $value['option'] || 1 == count( $terms ) ) {
							reset( $terms );
							$term = current( $terms );
							$text = sanitize_term_field( 'name', $term->name, $term->term_id, $value['value'], 'display' );
						} elseif ( 'export' == $value['option'] ) {
							$text = sanitize_text_field( var_export( $terms, true ) );
						} else {
							foreach ( $terms as $term ) {
								$term_name = sanitize_term_field( 'name', $term->name, $term->term_id, $value['value'], 'display' );
								$text .= strlen( $text ) ? ', ' . $term_name : $term_name;
							}
						}
					}

					$markup_values[ $key ] = $text;
					break;
				case 'custom':
					if ( 0 < $post_id ) {
						$record = get_metadata( 'post', $post_id, $value['value'], 'single' == $value['option'] );
						if ( empty( $record ) && 'ALL_CUSTOM' == $value['value'] ) {
							$meta_values = MLAQuery::mla_fetch_attachment_metadata( $post_id );
							$clean_data = array();
							foreach( $meta_values as $meta_key => $meta_value ) {
								if ( 0 !== strpos( $meta_key, 'mla_item_' ) ) {
									continue;
								}

								$meta_key = substr( $meta_key, 9 );
								if ( is_array( $meta_value ) ) {
									$clean_data[ $meta_key ] = '(ARRAY)';
								} elseif ( is_string( $meta_value ) ) {
									$clean_data[ $meta_key ] = self::_bin_to_utf8( substr( $meta_value, 0, 256 ) );
								} else {
									$clean_data[ $meta_key ] = $meta_value;
								}
							} // foreach value

							/*
							 * Convert the array to text, strip the outer "array( ... ,)" literal,
							 * the interior linefeed/space/space separators and backslashes.
							 */
							$record = var_export( $clean_data, true);
							$record = substr( $record, 7, strlen( $record ) - 10 );
							$record = str_replace( chr(0x0A).'  ', ' ', $record );
							$record = str_replace( '\\', '', $record );
						} // ALL_CUSTOM
					} else {
						break;
					}

					$text = '';
					if ( is_wp_error( $record ) ) {
						$text = implode( ',', $record->get_error_messages() );
					} elseif ( ! empty( $record ) ) {
						if ( is_scalar( $record ) ) {
							$text = ( 'raw' == $value['format'] ) ? (string) $record : sanitize_text_field( (string) $record );
						} elseif ( is_array( $record ) ) {
							if ( 'export' == $value['option'] ) {
								$text = ( 'raw' == $value['format'] ) ? var_export( $record, true ) : sanitize_text_field( var_export( $record, true ) );
							} else {
								$text = '';
								foreach ( $record as $term ) {
									$term_name = ( 'raw' == $value['format'] ) ? $term : sanitize_text_field( $term );
									$text .= strlen( $text ) ? ', ' . $term_name : $term_name;
								}
							}
						} // is_array
					} // ! empty

					$markup_values[ $key ] = $text;
					break;
				case 'iptc':
					if ( is_null( $attachment_metadata ) ) {
						if ( 0 < $post_id ) {
							$attachment_metadata = self::mla_fetch_attachment_image_metadata( $post_id );
						} else {
							break;
						}
					}

					$markup_values[ $key ] = self::mla_iptc_metadata_value( $value['value'], $attachment_metadata, $value['option'], $keep_existing );
					break;
				case 'exif':
					if ( is_null( $attachment_metadata ) ) {
						if ( 0 < $post_id ) {
							$attachment_metadata = self::mla_fetch_attachment_image_metadata( $post_id );
						} else {
							break;
						}
					}

					$record = self::mla_exif_metadata_value( $value['value'], $attachment_metadata, $value['option'], $keep_existing );
					if ( is_array( $record ) ) {
						$markup_values[ $key ] = self::_process_field_level_array( $record, $value['option'], $keep_existing );
					} else {
						$markup_values[ $key ] = $record;
					}

					break;
				case 'xmp':
					if ( is_null( $attachment_metadata ) ) {
						if ( 0 < $post_id ) {
							$attachment_metadata = self::mla_fetch_attachment_image_metadata( $post_id );
						} else {
							break;
						}
					}

					$markup_values[ $key ] = self::mla_xmp_metadata_value( $value['value'], $attachment_metadata['mla_xmp_metadata'], $value['option'], $keep_existing );
					break;
				case 'id3':
					if ( is_null( $id3_metadata ) ) {
						if ( 0 < $post_id ) {
							$id3_metadata = self::mla_fetch_attachment_id3_metadata( $post_id );
						} else {
							break;
						}
					}

					$markup_values[ $key ] = self::mla_id3_metadata_value( $value['value'], $id3_metadata, $value['option'], $keep_existing );
					break;
				case 'pdf':
					if ( is_null( $attachment_metadata ) ) {
						if ( 0 < $post_id ) {
							$attachment_metadata = self::mla_fetch_attachment_image_metadata( $post_id );
						} else {
							break;
						}
					}

					$record = self::mla_pdf_metadata_value( $value['value'], $attachment_metadata );
					if ( is_array( $record ) ) {
						$markup_values[ $key ] = self::_process_field_level_array( $record, $value['option'], $keep_existing );
					} else {
						$markup_values[ $key ] = $record;
					}

					break;
				case '':
					$candidate = str_replace( '{', '[', str_replace( '}', ']', $value['value'] ) );
					$key = str_replace( '{', '[', str_replace( '}', ']', $key ) );

					if ( MLAShortcodes::mla_is_data_source( $candidate ) ) {
						$data_value = array(
							'data_source' => $candidate,
							'keep_existing' => false,
							'format' => 'raw',
							'option' => $value['option'] ); // single, export, text for array values, e.g., alt_text

						$markup_values[ $key ] = MLAShortcodes::mla_get_data_source( $post_id, 'single_attachment_mapping', $data_value );
					} elseif ( isset( $markup_values[ $value['value'] ] ) ) {
						/*
						 * A standard element can have a format modifier, e.g., commas, attr
						 */
						$markup_values[ $key ] = $markup_values[ $value['value'] ];
					}

					break;
				default:
					// ignore anything else
			} // switch

			if ( isset( $markup_values[ $key ] ) ) {
				$markup_values[ $key ] = self::mla_apply_field_level_format( $markup_values[ $key ], $value );
			} // isset( $markup_values[ $key ] )
		} // foreach placeholder

		if ( $template_count ) {
			$markup_values['[+template_count+]'] = $template_count;
		}

		return $markup_values;
	}

	/**
	 * Analyze a template, returning an array of the placeholders it contains
	 *
	 * @since 0.90
	 *
	 * @param	string	A formatting string containing [+placeholders+]
	 * @param	string	Optional: default option value
	 *
	 * @return	array	Placeholder information: each entry is an array with
	 * 					['prefix'] => string, ['value'] => string, ['option'] => string 'text'|single'|'export'|'array'|'multi'
	 */
	public static function mla_get_template_placeholders( $tpl, $default_option = 'text' ) {
		$results = array();

		/*
		 * Look for and process templates, removing them from the input so substitution parameters within
		 * the template are not expanded. They will be expanded when the template itself is expanded.
		 */
		while ( false !== ( $template_offset = strpos( $tpl, '[+template:' ) ) ) {
			$nest = $template_offset + 11;
			$level = 1;
			do {
				$template_end = strpos( $tpl, '+]', $nest );
				if ( false === $template_end ) {
					/* translators: 1: ERROR tag 2: template excerpt */
					MLACore::mla_debug_add( __LINE__ . sprintf( _x( '%1$s: mla_get_template_placeholders no template-end delimiter dump = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), self::mla_hex_dump( substr( $tpl, $template_offset, 128 ), 128, 16 ) ), MLACore::MLA_DEBUG_CATEGORY_ANY );
					return array();
				}

				$nest = strpos( $tpl, '[+', $nest );
				if ( false === $nest ) {
					$nest = $template_end + 2;
					$level--;
				} elseif ( $nest < $template_end ) {
					$nest += 2;
					$level++;
				} else {
					$nest = $template_end + 2;
					$level--;
				}

			} while ( $level );

			$template_length = $template_end + 2 - $template_offset;
			$template_content = substr( $tpl, $template_offset + 11, $template_length - (11 + 2) );
			$placeholders = self::mla_get_template_placeholders( $template_content );
			$result = array( 'template:' . $template_content => array( 'prefix' => 'template', 'value' => $template_content, 'option' => $default_option, 'format' => 'native' ) );
			$results = array_merge( $results, $result, $placeholders );
			$tpl = substr_replace( $tpl, '', $template_offset, $template_length );
		} // found a template

		$match_count = preg_match_all( '/\[\+.+?\+\]/', $tpl, $matches );
		if ( ( $match_count == false ) || ( $match_count == 0 ) ) {
			return $results;
		}

		foreach ( $matches[0] as $match ) {
			$key = substr( $match, 2, (strlen( $match ) - 4 ) );
			$result = array( 'prefix' => '', 'value' => '', 'option' => $default_option, 'format' => 'native' );
			$match_count = preg_match( '/\[\+([^:]+):(.+)/', $match, $matches );
			if ( 1 == $match_count ) {
				$result['prefix'] = $matches[1];
				$tail = $matches[2];
			} else {
				$tail = substr( $match, 2);
			}

			$match_count = preg_match( '/([^,]+)(,(text|single|export|unpack|array|multi|commas|raw|attr|url|timestamp|date|fraction|substr))(\(([^)]+)\))*\+\]/', $tail, $matches );
			if ( 1 == $match_count ) {
				$result['value'] = $matches[1];
				if ( ! empty( $matches[5] ) ) {
					$args = self::_parse_arguments( $matches[5] );

					if ( 1 == count( $args ) ) {
						$args = $args[0];
					}
				} else {
					$args = '';
				}

				if ( 'commas' == $matches[3] ) {		
					$result['option'] = 'text';
					$result['format'] = 'commas';
				} elseif ( 'raw' == $matches[3] ) {		
					$result['option'] = 'text';
					$result['format'] = 'raw';
				} elseif ( 'attr' == $matches[3] ) {		
					$result['option'] = 'text';
					$result['format'] = 'attr';
				} elseif ( 'url' == $matches[3] ) {		
					$result['option'] = 'text';
					$result['format'] = 'url';
				} elseif ( 'timestamp' == $matches[3] ) {		
					$result['option'] = 'text';
					$result['format'] = 'timestamp';
					$result['args'] = $args;
				} elseif ( 'date' == $matches[3] ) {		
					$result['option'] = 'text';
					$result['format'] = 'date';
					$result['args'] = $args;
				} elseif ( 'fraction' == $matches[3] ) {		
					$result['option'] = 'text';
					$result['format'] = 'fraction';
					$result['args'] = $args;
				} elseif ( 'substr' == $matches[3] ) {		
					$result['option'] = 'text';
					$result['format'] = 'substr';
					$result['args'] = $args;
				} else {
					$result['option'] = $matches[3];
				}

			} else {
				$result['value'] = substr( $tail, 0, (strlen( $tail ) - 2 ) );
			}

		$results[ $key ] = $result;
		} // foreach

		return $results;
	}

	/**
	 * WP_Query filter "parameters"
	 *
	 * This array defines parameters for the query's join, where and orderby filters.
	 * The parameters are set up in the _prepare_list_table_query function, and
	 * any further logic required to translate those values is contained in the filters.
	 *
	 * Array index values are: use_alt_text_view, use_postmeta_view, use_orderby_view,
	 * alt_text_value, postmeta_key, postmeta_value, patterns, detached,
	 * orderby, order, mla-metavalue, debug (also in search_parameters)
	 *
	 * @since 0.30
	 *
	 * @var	array
	 */
	public static $query_parameters = array();

	/**
	 * WP_Query 'posts_search' filter "parameters"
	 *
	 * This array defines parameters for the query's posts_search filter, which uses
	 * 'search_string' to add a clause to the query's WHERE clause. It is shared between
	 * the list_table-query functions here and the mla_get_shortcode_attachments function
	 * in class-mla-shortcodes.php. This array passes the relevant parameters to the filter.
	 *
	 * Array index values are:
	 * ['mla_terms_search']['phrases']
	 * ['mla_terms_search']['taxonomies']
	 * ['mla_terms_search']['radio_phrases'] => AND/OR
	 * ['mla_terms_search']['radio_terms'] => AND/OR
	 * ['s'] => numeric for ID/parent search
	 * ['mla_search_fields'] => 'content', 'title', 'excerpt', 'alt-text', 'name', 'terms'
	 * Note: 'alt-text' is not supported in [mla_gallery]
	 * ['mla_search_connector'] => AND/OR
	 * ['sentence'] => entire string must match as one "keyword"
	 * ['exact'] => entire string must match entire field value
	 * ['debug'] => internal element, console/log/shortcode/none
	 * ['tax_terms_count'] => internal element, shared with JOIN and GROUP BY filters
	 *
	 * @since 2.00
	 *
	 * @var	array
	 */
	public static $search_parameters = array();

	/**
	 * Get the total number of attachment posts
	 *
	 * Compatibility shim for MLAQuery::mla_count_list_table_items
	 *
	 * @since 0.30
	 *
	 * @param	array	Query variables, e.g., from $_REQUEST
	 * @param	int		(optional) number of rows to skip over to reach desired page
	 * @param	int		(optional) number of rows on each page
	 *
	 * @return	integer	Number of attachment posts
	 */
	public static function mla_count_list_table_items( $request, $offset = NULL, $count = NULL ) {
		return MLAQuery::mla_count_list_table_items( $request, $offset, $count );
	}
	
	/**
	 * Retrieve attachment objects for list table display
	 *
	 * Compatibility shim for MLAQuery::mla_query_list_table_items
	 *
	 * @since 0.1
	 *
	 * @param	array	query parameters from web page, usually found in $_REQUEST
	 * @param	int		number of rows to skip over to reach desired page
	 * @param	int		number of rows on each page
	 *
	 * @return	array	attachment objects (posts) including parent data, meta data and references
	 */
	public static function mla_query_list_table_items( $request, $offset, $count ) {
		return MLAQuery::mla_query_list_table_items( $request, $offset, $count );
	}
	
	/** 
	 * Retrieve an Attachment array given a $post_id
	 *
	 * The (associative) array will contain every field that can be found in
	 * the posts and postmeta tables, and all references to the attachment.
	 * 
	 * @since 0.1
	 * @uses $post WordPress global variable
	 * 
	 * @param	integer	The ID of the attachment post
	 * @param	boolean	True to add references, false to skip references
	 *
	 * @return	NULL|array NULL on failure else associative array
	 */
	public static function mla_get_attachment_by_id( $post_id, $add_references = true ) {
		global $post;
		static $save_id = -1, $post_data;

		if ( $post_id == $save_id ) {
			return $post_data;
		} elseif ( $post_id == -1 ) {
			$save_id = -1;
			return NULL;
		}

		$item = get_post( $post_id );
		if ( empty( $item ) ) {
			/* translators: 1: ERROR tag 2: post ID */
			MLACore::mla_debug_add( __LINE__ . sprintf( _x( '%1$s: mla_get_attachment_by_id(%2$d) not found.', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $post_id ), MLACore::MLA_DEBUG_CATEGORY_ANY );
			return NULL;
		}

		if ( $item->post_type != 'attachment' ) {
			/* translators: 1: ERROR tag 2: post ID 3: post_type */
			MLACore::mla_debug_add( __LINE__ . sprintf( _x( '%1$s: mla_get_attachment_by_id(%2$d) wrong post_type "%3$s".', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $post_id, $item->post_type ), MLACore::MLA_DEBUG_CATEGORY_ANY );
			return NULL;
		}

		$post_data = (array) $item;
		$post = $item;
		setup_postdata( $item );

		/*
		 * Add parent data
		 */
		$post_data = array_merge( $post_data, MLAQuery::mla_fetch_attachment_parent_data( $post_data['post_parent'] ) );

		/*
		 * Add meta data
		 */
		$post_data = array_merge( $post_data, MLAQuery::mla_fetch_attachment_metadata( $post_id ) );

		/*
		 * Add references, if requested, or "empty" references array
		 */
		$post_data['mla_references'] = MLAQuery::mla_fetch_attachment_references( $post_id, $post_data['post_parent'], $add_references );

		$save_id = $post_id;
		return $post_data;
	}

	/**
	 * Adds or replaces the value of a key in a possibly nested array structure
	 *
	 * @since 1.51
	 *
	 * @param string key value, e.g. array1.array2.element
	 * @param mixed replacement value, string or array, by reference
	 * @param array PHP nested arrays, by reference
	 *
	 * @return boolean	true if $needle element set, false if not
	 */
	private static function _set_array_element( $needle, &$value, &$haystack ) {
		$key_array = explode( '.', $needle );
		$key = array_shift( $key_array );

		if ( empty( $key_array ) ) {
			$haystack[ $key ] = $value;
			return true;
		} // lowest level

		/*
		 * If an intermediate key is not an array, leave it alone and fail.
		 * If an intermediate key does not exist, create an empty array for it.
		 */
		if ( isset( $haystack[ $key ] ) ) {
			if ( ! is_array( $haystack[ $key ] ) ) {
				return false;
			}
		} else {
			$haystack[ $key ] = array();
		}

		return self::_set_array_element( implode( $key_array, '.' ), $value, $haystack[ $key ] );
	}

	/**
	 * Deletes the value of a key in a possibly nested array structure
	 *
	 * @since 1.51
	 *
	 * @param string key value, e.g. array1.array2.element
	 * @param array PHP nested arrays, by reference
	 *
	 * @return boolean	true if $needle element found, false if not
	 */
	private static function _unset_array_element( $needle, &$haystack ) {
		$key_array = explode( '.', $needle );
		$key = array_shift( $key_array );

		if ( empty( $key_array ) ) {
			if ( isset( $haystack[ $key ] ) ) {
				unset( $haystack[ $key ] );
				return true;
			}

			return false;
		} // lowest level

		if ( isset( $haystack[ $key ] ) ) {
			return self::_unset_array_element( implode( $key_array, '.' ), $haystack[ $key ] );
		}

		return false;
	}

	/**
	 * Finds the value of a key in a possibly nested array structure
	 *
	 * Used primarily to extract fields from the _wp_attachment_metadata custom field.
	 * Also used with the audio/video ID3 metadata exposed in WordPress 3.6 and later.
	 *
	 * @since 1.30
	 *
	 * @param string key value, e.g. array1.array2.element
	 * @param array PHP nested arrays
	 * @param string data option; 'text'|'single'|'export'|'array'|'multi'
	 * @param boolean keep existing values - for 'multi' option
	 *
	 * @return mixed string or array value matching key(.key ...) or ''
	 */
	public static function mla_find_array_element( $needle, $haystack, $option, $keep_existing = false ) {
		$key_array = explode( '.', $needle );
		if ( is_array( $key_array ) ) {
			foreach ( $key_array as $key ) {
				/*
				 * The '*' key means:
				 * 1) needle.* => accept any value, or 
				 * 2) needle.*.tail => search each sub-array using "tail"
				 *    and build an array of results.
				 */
				if ( '*' == $key ) {
					if ( false !== ( $tail = strpos( $needle, '*.' ) ) ) { 
						$tail = substr( $needle, $tail + 2 );
						if ( ! empty( $tail ) ) {
							if ( is_array( $haystack ) ) {
								$results = array();
								foreach ( $haystack as $substack ) {
									$results[] = self::mla_find_array_element( $tail, $substack, $option, $keep_existing );
								}

								if ( 1 == count( $results ) ) {
									$haystack = $results[0];
								} else {
									$haystack = $results;
								}
							} else {
								$haystack = '';
							}
						} // found tail
					} // found .*.

					break;
				} else {
					if ( is_array( $haystack ) ) {
						if ( isset( $haystack[ $key ] ) ) {
							$haystack = $haystack[ $key ];
						} else {
							$haystack = '';
						}
					} else {
						$haystack = '';
					}
				} // * != key
			} // foreach $key
		} else {
			$haystack = '';
		}

		if ( is_array( $haystack ) ) {
			switch ( $option ) {
				case 'single':
					$haystack = current( $haystack );
					break;
				case 'export':
					$haystack = var_export( $haystack, true );
					break;
				case 'unpack':
					if ( is_array( $haystack ) ) {
						$clean_data = array();
						foreach ( $haystack as $key => $value ) {
							if ( is_array( $value ) ) {
								$clean_data[ $key ] = '(ARRAY)';
							} elseif ( is_string( $value ) ) {
								$clean_data[ $key ] = self::_bin_to_utf8( substr( $value, 0, 256 ) );
							} else {
								$clean_data[ $key ] = $value;
							}
						}

						$haystack = var_export( $clean_data, true);
					} else {
						$haystack = var_export( $record, true );
					}
					break;
				case 'multi':
					$haystack[0x80000000] = $option;
					$haystack[0x80000001] = $keep_existing;
					// fallthru
				case 'array':
					return $haystack;
					break;
				default:
					$haystack = self::_bin_to_utf8( @implode( ', ', $haystack ) );
			} // $option
		} // is_array

		return self::_bin_to_utf8( $haystack );
	} // mla_find_array_element

	/**
	 * Invalidates $mla_galleries and $galleries arrays and cached values after post, page or attachment updates
	 *
	 * @since 1.00
	 *
	 * @param	integer ID of post/page/attachment; not used at this time
	 *
	 * @return	void
	 */
	public static function mla_save_post_action( $post_id ) {
		MLAQuery::mla_flush_mla_galleries( MLACoreOptions::MLA_GALLERY_IN_TUNING );
		MLAQuery::mla_flush_mla_galleries( MLACoreOptions::MLA_MLA_GALLERY_IN_TUNING );
	}

	/**
	 * Parse a PDF date string
	 * 
	 * @since 1.50
	 *
	 * @param	string	PDF date string of the form D:YYYYMMDDHHmmSSOHH'mm
	 *
	 * @return	string	formatted date string YYYY-MM-DD HH:mm:SS
	 */
	public static function mla_parse_pdf_date( $source_string ) {
		if ( 'D:' == substr( $source_string, 0, 2) && ctype_digit( substr( $source_string, 2, 12 ) ) ) {
			return sprintf( '%1$s-%2$s-%3$s %4$s:%5$s:%6$s',
				substr( $source_string, 2, 4),
				substr( $source_string, 6, 2),
				substr( $source_string, 8, 2),
				substr( $source_string, 10, 2),
				substr( $source_string, 12, 2),
				substr( $source_string, 14, 2) );
		}

		return $source_string;
	}

	/**
	 * Parse a ISO 8601 Timestamp
	 * 
	 * @since 1.50
	 *
	 * @param	string	ISO string of the form YYYY-MM-DDTHH:MM:SS-HH:MM (inc time zone)
	 *
	 * @return	string	formatted date string YYYY-MM-DD HH:mm:SS
	 */
	private static function _parse_iso8601_date( $source_string ) {
		if ( 1 == preg_match( '/^\\d\\d\\d\\d-\\d\\d-\\d\\dT\\d\\d:\\d\\d:\\d\\d(Z|[+-]\\d\\d:\\d\\d)/', $source_string ) ) {
			return sprintf( '%1$s-%2$s-%3$s %4$s:%5$s:%6$s',
				substr( $source_string, 0, 4),
				substr( $source_string, 5, 2),
				substr( $source_string, 8, 2),
				substr( $source_string, 11, 2),
				substr( $source_string, 14, 2),
				substr( $source_string, 17, 2) );
		}

		return $source_string;
	}

	/**
	 * Parse an XMP array value, stripping namespace prefixes and Seq/Alt/Bag arrays
	 * 
	 * @since 2.10
	 *
	 * @param	array	XMP multi-valued element
	 *
	 * @return	mixed	Simplified array or string value
	 */
	private static function _parse_xmp_array( $values ) {
		if ( is_scalar( $values ) ) {
			return $values;
		}

		if ( isset( $values['rdf:Alt'] ) ) {
			return self::_parse_xmp_array( $values['rdf:Alt'] );
		}

		if ( isset( $values['rdf:Bag'] ) ) {
			return self::_parse_xmp_array( $values['rdf:Bag'] );
		}

		if ( isset( $values['rdf:Seq'] ) ) {
			return self::_parse_xmp_array( $values['rdf:Seq'] );
		}

		$results = array();
		foreach( $values as $key => $value ) {
			if ( false !== ($colon = strpos( $key, ':' ) ) ) {
				$new_key = substr( $key, $colon + 1 );
			} else {
				$new_key = $key;
			}

			if ( is_array( $value ) ) {
				$results[ $new_key ] = self::_parse_xmp_array( $value );
			} else {
				$results[ $new_key ] = self::_parse_iso8601_date( $value );
			}
		}

		return $results;
	}

	/**
	 * Search the namespace array for a non-empty value
	 * 
	 * @since 2.10
	 *
	 * @param	array	namespace array
	 * @param	string	namespace
	 * @param	string	key
	 *
	 * @return	string	trimmed value of the key within the namespace
	 */
	private static function _nonempty_value( &$namespace_array, $namespace, $key ) {
		$result = '';
		
		if ( isset( $namespace_array[ $namespace ] ) && isset( $namespace_array[ $namespace ][ $key ] ) ) {
			if ( is_array( $namespace_array[ $namespace ][ $key ] ) ) {
				$result = @implode( ',', $namespace_array[ $namespace ][ $key ] );
			} else {
				$result = (string) $namespace_array[ $namespace ][ $key ];
			}
		}
		
		$trim_value = trim( $result, " \n\t\r\0\x0B," );
		if ( empty( $trim_value ) ) {
			$result = '';
		}
		
		return $result;
	}

	/**
	 * Extract XMP meta data from a file
	 * 
	 * @since 2.10
	 *
	 * @param	string	full path and file name
	 * @param	integer	offset within the file of the search start point
	 *
	 * @return	mixed	array of metadata values or NULL on failure
	 */
	public static function mla_parse_xmp_metadata( $file_name, $file_offset ) {
//error_log( __LINE__ . " MLAData::mla_parse_xmp_metadata( {$file_name}, {$file_offset} ) ", 0 );
		$chunksize = 16384;			
		$xmp_chunk = file_get_contents( $file_name, true, NULL, $file_offset, $chunksize );
//error_log( __LINE__ . " MLAData::mla_parse_xmp_metadata( {$file_offset} ) chunk = \r\n" . MLAData::mla_hex_dump( $xmp_chunk ), 0 );

		/*
		 * If necessary and possible, advance the $xmp_chunk through the file until it contains the start tag
		 */
		if ( false === ( $start_tag = strpos( $xmp_chunk, '<x:xmpmeta' ) ) && ( $chunksize == strlen( $xmp_chunk ) ) ) {
			$new_offset = $file_offset + ( $chunksize - 16 );
//error_log( __LINE__ . " MLAData::mla_parse_xmp_metadata( {$new_offset} ) ", 0 );
			$xmp_chunk = file_get_contents( $file_name, true, NULL, $new_offset, $chunksize );
//error_log( __LINE__ . " MLAData::mla_parse_xmp_metadata( {$new_offset} ) chunk = \r\n" . MLAData::mla_hex_dump( $xmp_chunk ), 0 );
			while ( false === ( $start_tag = strpos( $xmp_chunk, '<x:xmpmeta' ) ) && ( $chunksize == strlen( $xmp_chunk ) ) ) {
				$new_offset = $new_offset + ( $chunksize - 16 );
//error_log( __LINE__ . " MLAData::mla_parse_xmp_metadata( {$new_offset} ) ", 0 );
				$xmp_chunk = file_get_contents( $file_name, true, NULL, $new_offset, $chunksize );
//error_log( __LINE__ . " MLAData::mla_parse_xmp_metadata( {$new_offset} ) chunk = \r\n" . MLAData::mla_hex_dump( $xmp_chunk ), 0 );
			} // while not found
		} else { // if not found
			$new_offset = $file_offset;
		}

//error_log( __LINE__ . " MLAData::mla_parse_xmp_metadata( {$start_tag} ) ", 0 );
		if ( false === $start_tag ) {
			return NULL;
		}

		/*
		 * If necessary and possible, expand the $xmp_chunk until it contains the start tag
		 */
		if ( false === ( $end_tag = strpos( $xmp_chunk, '</x:xmpmeta>', $start_tag ) ) && ( $chunksize == strlen( $xmp_chunk ) ) ) {
			$new_offset = $new_offset + $start_tag;
			$start_tag = 0;
			$new_chunksize = $chunksize + $chunksize;
			$xmp_chunk = file_get_contents( $file_name, true, NULL, $new_offset, $new_chunksize );
			while ( false === ( $end_tag = strpos( $xmp_chunk, '</x:xmpmeta>' ) ) && ( $new_chunksize == strlen( $xmp_chunk ) ) ) {
				$new_chunksize = $new_chunksize + $chunksize;
				$xmp_chunk = file_get_contents( $file_name, true, NULL, $new_offset, $new_chunksize );
			} // while not found
		} // if not found

		if ( false === $end_tag ) {
			return NULL;
		}

		$xmp_string = "<?xml version='1.0'?>\n" . substr($xmp_chunk, $start_tag, ( $end_tag + 12 ) - $start_tag );
//error_log( __LINE__ . " MLAData::mla_parse_xmp_metadata xmp_string = " . var_export( $xmp_string, true ), 0 );
//error_log( __LINE__ . "  MLAData::mla_parse_xmp_metadata xmp_string = \r\n" . MLAData::mla_hex_dump( $xmp_string ), 0 );
		// experimental damage repair for GodsHillPC 
		$xmp_string = str_replace( "\000", '0', $xmp_string );
		$xmp_values = array();
		$xml_parser = xml_parser_create('UTF-8');
		if ( xml_parser_set_option( $xml_parser, XML_OPTION_SKIP_WHITE, 0 ) && xml_parser_set_option( $xml_parser, XML_OPTION_CASE_FOLDING, 0 ) ) {
			if ( 0 == xml_parse_into_struct( $xml_parser, $xmp_string, $xmp_values ) ) {
				MLACore::mla_debug_add( __LINE__ . __( 'ERROR', 'media-library-assistant' ) . ': ' . _x( 'mla_parse_xmp_metadata xml_parse_into_struct failed.', 'error_log', 'media-library-assistant' ), MLACore::MLA_DEBUG_CATEGORY_ANY );
				$xmp_values = array();
			}
		} else {
			MLACore::mla_debug_add( __LINE__ . __( 'ERROR', 'media-library-assistant' ) . ': ' . _x( 'mla_parse_xmp_metadata set option failed.', 'error_log', 'media-library-assistant' ), MLACore::MLA_DEBUG_CATEGORY_ANY );
		}

		xml_parser_free($xml_parser);

		if ( empty( $xmp_values ) ) {
			return NULL;
		}
//error_log( __LINE__ . " MLAData::mla_parse_xmp_metadata xmp_values = " . var_export( $xmp_values, true ), 0 );

		$levels = array();
		$current_level = 0;
		$results = array();
		$xmlns = array();
		foreach ( $xmp_values as $index => $value ) {
//error_log( __LINE__ . " MLAData::mla_parse_xmp_metadata xmp_values( {$index} ) value = " . var_export( $value, true ), 0 );
			$language = 'x-default';
			$node_attributes = array();
			if ( isset( $value['attributes'] ) ) {
				foreach ( $value['attributes'] as $att_tag => $att_value ) {
					$att_value = self::_bin_to_utf8( $att_value );

					if ( 'xmlns:' == substr( $att_tag, 0, 6 ) ) {
						$xmlns[ substr( $att_tag, 6 ) ] = $att_value;
					} elseif ( 'x:xmptk' == $att_tag ) {
						$results['xmptk'] = $att_value;
					} elseif ( 'xml:lang' == $att_tag ) {
						$language = $att_value;
					} else {
						$key = explode( ':', $att_tag );
						switch ( count( $key ) ) {
							case 1:
								$att_ns = 'unknown';
								$att_name = $key[0];
								break;
							case 2:
								$att_ns = $key[0];
								$att_name = $key[1];
								break;
							default:
								$att_ns = array_shift( $key );
								$att_name = implode( ':', $key );
								break;
						} // count

						if ( ! in_array( $att_tag, array( 'rdf:about', 'rdf:parseType' ) ) ) {
							$node_attributes[ $att_tag ] = self::_bin_to_utf8( $att_value );
						}
					}
				}
			} // attributes

			switch ( $value['type'] ) {
				case 'open':
					$levels[ ++$current_level ] = array( 'key' => $value['tag'], 'values' => $node_attributes );
//error_log( __LINE__ . " MLAData::mla_parse_xmp_metadata xmp_values( {$current_level}, {$index} ) case 'open': top_level = " . var_export( $levels[ $current_level ], true ), 0 );
					break;
				case 'close':
					if ( 0 < --$current_level ) {
						$top_level = array_pop( $levels );
//error_log( __LINE__ . " MLAData::mla_parse_xmp_metadata xmp_values( {$current_level}, {$index} ) case 'close': top_level = " . var_export( $top_level, true ), 0 );
//error_log( __LINE__ . " MLAData::mla_parse_xmp_metadata xmp_values( {$current_level}, {$index} ) case 'close': levels( {$current_level} ) before = " . var_export( $levels, true ), 0 );
						if ( 'rdf:li' == $top_level['key'] ) {
							$levels[ $current_level ]['values'][] = $top_level['values'];
						} else {
							if ( isset( $levels[ $current_level ]['values'][ $top_level['key'] ] ) ) {
								$levels[ $current_level ]['values'][ $top_level['key'] ] = array_merge( (array) $levels[ $current_level ]['values'][ $top_level['key'] ], $top_level['values'] );
							} else {
								$levels[ $current_level ]['values'][ $top_level['key'] ] = $top_level['values'];
							}
						}
					}
//error_log( __LINE__ . " MLAData::mla_parse_xmp_metadata xmp_values( {$current_level}, {$index} ) case 'close': levels( {$current_level} ) after = " . var_export( $levels, true ), 0 );
					break;
				case 'complete':
					if ( 'x-default' != $language ) {
						break;
					}

//error_log( __LINE__ . " MLAData::mla_parse_xmp_metadata xmp_values( {$index} ) case 'complete': node_attributes = " . var_export( $node_attributes, true ), 0 );
					if ( empty( $node_attributes ) ) {
						if ( isset( $value['value'] ) ) {
							$complete_value = self::_bin_to_utf8( $value['value'] );
						} else {
							$complete_value = '';
						}
					} else {
						$complete_value = $node_attributes;
					}

//error_log( __LINE__ . " MLAData::mla_parse_xmp_metadata xmp_values( {$index} ) case 'complete': value = " . var_export( $value, true ), 0 );
//error_log( __LINE__ . " MLAData::mla_parse_xmp_metadata xmp_values( {$index} ) case 'complete': complete_value = " . var_export( $complete_value, true ), 0 );
//error_log( __LINE__ . " MLAData::mla_parse_xmp_metadata xmp_values( {$index} ) case 'complete': (array) complete_value = " . var_export( (array) $complete_value, true ), 0 );
					if ( 'rdf:li' == $value['tag'] ) {
						$levels[ $current_level ]['values'][] = $complete_value;
					} else {
						if ( isset( $levels[ $current_level ]['values'][ $value['tag'] ] ) ) {
							$new_value = (array) $levels[ $current_level ]['values'][ $value['tag'] ];
							$levels[ $current_level ]['values'][ $value['tag'] ] = array_merge( $new_value, (array) $complete_value );
						} else {
							$levels[ $current_level ]['values'][ $value['tag'] ] = $complete_value;
						}
					}

//error_log( __LINE__ . " MLAData::mla_parse_xmp_metadata xmp_values( {$index}, {$current_level} ) case 'complete': values = " . var_export( $levels[ $current_level ]['values'], true ), 0 );
					break;
				default:
//error_log( __LINE__ . " MLAData::mla_parse_xmp_metadata xmp_values( {$index}, {$current_level} ) ignoring type = " . var_export( $value['type'], true ), 0 );
			} // switch on type
//error_log( __LINE__ . " MLAData::mla_parse_xmp_metadata xmp_values( {$index}, {$current_level} ) levels = " . var_export( $levels, true ), 0 );
		} // foreach value
//error_log( __LINE__ . " MLAData::mla_parse_xmp_metadata levels = " . var_export( $levels, true ), 0 );
//error_log( __LINE__ . " MLAData::mla_parse_xmp_metadata xmlns = " . var_export( $xmlns, true ), 0 );

		/*
		 * Parse "namespace:name" names into arrays of simple names
		 * NOTE: The string "XAP" or "xap" appears in some namespaces, keywords,
		 * and related names in stored XMP data. It reflects an early internal
		 * code name for XMP; the names have been preserved for compatibility purposes.
		 */
		$namespace_arrays = array();
		foreach ( $levels[1]['values']['rdf:RDF']['rdf:Description'] as $key => $value ) {
			if ( is_string( $value ) ) {
				$value = self::_parse_iso8601_date( self::mla_parse_pdf_date( $value ) );
			} elseif ( is_array( $value ) ) {
				$value = self::_parse_xmp_array( $value );
			}

			if ( false !== ($colon = strpos( $key, ':' ) ) ) {
				$array_name = substr( $key, 0, $colon );
				$array_index = substr( $key, $colon + 1 );
				$namespace_arrays[ $array_name ][ $array_index ] = $value;

				if ( ! isset( $results[ $array_index ] ) && in_array( $array_name, array( 'xmp', 'xmpMM', 'xmpRights', 'xap', 'xapMM', 'dc', 'pdf', 'pdfx', 'mwg-rs' ) ) ) {
					if ( is_array( $value ) && 1 == count( $value ) && isset( $value[0] ) ) {
						$results[ $array_index ] = $value[0];
					} else {
						$results[ $array_index ] = $value;
					}
				}
			} // found namespace
		}
//error_log( __LINE__ . " MLAData::mla_parse_xmp_metadata results = " . var_export( $results, true ), 0 );

		/*
		 * Try to populate all the PDF-standard keys (except Trapped)
		 * Title - The document's title
		 * Author - The name of the person who created the document
		 * Subject - The subject of the document
		 * Keywords - Keywords associated with the document
		 * Creator - the name of the conforming product that created the original document
		 * Producer - the name of the conforming product that converted it to PDF
		 * CreationDate - The date and time the document was created
		 * ModDate - The date and time the document was most recently modified
		 */
		if ( ! isset( $results['Title'] ) ) {
			$replacement = self::_nonempty_value( $namespace_arrays, 'dc', 'title' );
			if ( ! empty( $replacement ) ) {
				$results['Title'] = $replacement;
			}
		}

		if ( ! isset( $results['Author'] ) ) {
			$replacement = self::_nonempty_value( $namespace_arrays, 'dc', 'creator' );
			if ( ! empty( $replacement ) ) {
				$results['Author'] = $replacement;
			}
		}

		if ( ! isset( $results['Subject'] ) ) {
			$replacement = self::_nonempty_value( $namespace_arrays, 'dc', 'description' );
			if ( ! empty( $replacement ) ) {
				$results['Subject'] = $replacement;
			}
		}

		/*
		 * Keywords are special, since they are often assigned to taxonomy terms.
		 * Build or preserve an array if there are multiple values; string for single values.
		 * "pdf:Keywords" uses a ';' delimiter, "dc:subject" uses an array.
		 */
		$keywords = array();
		if ( isset( $results['Keywords'] ) ) {
			if ( false !== strpos( $results['Keywords'], ';' ) ) {
				$terms = array_map( 'trim', explode( ';', $results['Keywords'] ) );
				foreach ( $terms as $term )
					if ( ! empty( $term ) ) {
						$keywords[ $term ] = $term;
					}
			} elseif ( false !== strpos( $results['Keywords'], ',' ) ) {
				$terms = array_map( 'trim', explode( ',', $results['Keywords'] ) );
				foreach ( $terms as $term )
					if ( ! empty( $term ) ) {
						$keywords[ $term ] = $term;
					}
			} else {
				$term = trim( $results['Keywords'] );
				if ( ! empty( $term ) ) {
					$keywords[ $term ] = $term;
				}
			}
		} // Keywords

		if ( isset( $namespace_arrays['dc'] ) && isset( $namespace_arrays['dc']['subject'] ) ) {
			if ( is_array( $namespace_arrays['dc']['subject'] ) ) {
				foreach ( $namespace_arrays['dc']['subject'] as $term ) {
					$term = trim( $term, " \n\t\r\0\x0B," );
					if ( ! empty( $term ) ) {
						$keywords[ $term ] = $term;
					}
				}
			} elseif ( is_string( $namespace_arrays['dc']['subject'] ) ) {
				$term = trim ( $namespace_arrays['dc']['subject'], " \n\t\r\0\x0B," );
				if ( ! empty( $term ) ) {
					$keywords[ $term ] = $term;
				}
			}
		} // dc:subject

		if ( ! empty( $keywords ) ) {
			if ( 1 == count( $keywords ) ) {
				$results['Keywords'] = array_shift( $keywords );
			} else {
				$results['Keywords'] = array();
				foreach ( $keywords as $term ) {
					$results['Keywords'][] = $term;
				}
			}
		}

//		if ( ! isset( $results['Producer'] ) ) {
//		}

		if ( ! isset( $results['Creator'] ) ) {
			$replacement = self::_nonempty_value( $namespace_arrays, 'xmp', 'CreatorTool' );
			if ( ! empty( $replacement ) ) {
				$results['Creator'] = $replacement;
			} else {
				$replacement = self::_nonempty_value( $namespace_arrays, 'xap', 'CreatorTool' );
				if ( ! empty( $replacement ) ) {
					$results['Creator'] = $replacement;
				} elseif ( ! empty( $results['Producer'] ) ) {
					$results['Creator'] = $results['Producer'];
				}
			}
		}

		if ( ! isset( $results['CreationDate'] ) ) {
			$replacement = self::_nonempty_value( $namespace_arrays, 'xmp', 'CreateDate' );
			if ( ! empty( $replacement ) ) {
				$results['CreationDate'] = $replacement;
			} else {
				$replacement = self::_nonempty_value( $namespace_arrays, 'xap', 'CreateDate' );
				if ( ! empty( $replacement ) ) {
					$results['CreationDate'] = $replacement;
				}
			}
		}

		if ( ! isset( $results['ModDate'] ) ) {
			$replacement = self::_nonempty_value( $namespace_arrays, 'xmp', 'ModifyDate' );
			if ( ! empty( $replacement ) ) {
				$results['ModDate'] = $replacement;
			} else {
				$replacement = self::_nonempty_value( $namespace_arrays, 'xap', 'ModifyDate' );
				if ( ! empty( $replacement ) ) {
					$results['ModDate'] = $replacement;
				}
			}
		}

		if ( ! empty( $xmlns ) ) {
			$results['xmlns'] = $xmlns;
		}

		$results = array_merge( $results, $namespace_arrays );
//error_log( __LINE__ . " mla_fetch_attachment_image_metadata results = " . var_export( $results, true ), 0 );
		return $results;
	}

	/**
	 * UTF-8 replacements for invalid SQL characters
	 *
	 * @since 1.41
	 *
	 * @var	array
	 */
	public static $utf8_chars = array(
		"\xC2\x80", "\xC2\x81", "\xC2\x82", "\xC2\x83", "\xC2\x84", "\xC2\x85", "\xC2\x86", "\xC2\x87", 
		"\xC2\x88", "\xC2\x89", "\xC2\x8A", "\xC2\x8B", "\xC2\x8C", "\xC2\x8D", "\xC2\x8E", "\xC2\x8F", 
		"\xC2\x90", "\xC2\x91", "\xC2\x92", "\xC2\x93", "\xC2\x94", "\xC2\x95", "\xC2\x96", "\xC2\x97", 
		"\xC2\x98", "\xC2\x99", "\xC2\x9A", "\xC2\x9B", "\xC2\x9C", "\xC2\x9D", "\xC2\x9E", "\xC2\x9F", 
		"\xC2\xA0", "\xC2\xA1", "\xC2\xA2", "\xC2\xA3", "\xC2\xA4", "\xC2\xA5", "\xC2\xA6", "\xC2\xA7", 
		"\xC2\xA8", "\xC2\xA9", "\xC2\xAA", "\xC2\xAB", "\xC2\xAC", "\xC2\xAD", "\xC2\xAE", "\xC2\xAF", 
		"\xC2\xB0", "\xC2\xB1", "\xC2\xB2", "\xC2\xB3", "\xC2\xB4", "\xC2\xB5", "\xC2\xB6", "\xC2\xB7", 
		"\xC2\xB8", "\xC2\xB9", "\xC2\xBA", "\xC2\xBB", "\xC2\xBC", "\xC2\xBD", "\xC2\xBE", "\xC2\xBF", 
		"\xC3\x80", "\xC3\x81", "\xC3\x82", "\xC3\x83", "\xC3\x84", "\xC3\x85", "\xC3\x86", "\xC3\x87", 
		"\xC3\x88", "\xC3\x89", "\xC3\x8A", "\xC3\x8B", "\xC3\x8C", "\xC3\x8D", "\xC3\x8E", "\xC3\x8F", 
		"\xC3\x90", "\xC3\x91", "\xC3\x92", "\xC3\x93", "\xC3\x94", "\xC3\x95", "\xC3\x96", "\xC3\x97", 
		"\xC3\x98", "\xC3\x99", "\xC3\x9A", "\xC3\x9B", "\xC3\x9C", "\xC3\x9D", "\xC3\x9E", "\xC3\x9F", 
		"\xC3\xA0", "\xC3\xA1", "\xC3\xA2", "\xC3\xA3", "\xC3\xA4", "\xC3\xA5", "\xC3\xA6", "\xC3\xA7", 
		"\xC3\xA8", "\xC3\xA9", "\xC3\xAA", "\xC3\xAB", "\xC3\xAC", "\xC3\xAD", "\xC3\xAE", "\xC3\xAF", 
		"\xC3\xB0", "\xC3\xB1", "\xC3\xB2", "\xC3\xB3", "\xC3\xB4", "\xC3\xB5", "\xC3\xB6", "\xC3\xB7", 
		"\xC3\xB8", "\xC3\xB9", "\xC3\xBA", "\xC3\xBB", "\xC3\xBC", "\xC3\xBD", "\xC3\xBE", "\xC3\xBF"
	);

	/**
	 * Replace SQL incorrect characters (0x80 - 0xFF) with their UTF-8 equivalents
	 * 
	 * @since 1.41
	 *
	 * @param	string	unencoded string
	 *
	 * @return	string	UTF-8 encoded string
	 */
	private static function _bin_to_utf8( $string ) {
		if ( seems_utf8( $string ) ) {
			return $string;
		}

		if (function_exists('utf8_encode')) {
			return utf8_encode( $string );
		}

		$output = '';
		for ($index = 0; $index < strlen( $string ); $index++ ) {
			$value = ord( $string[ $index ] );
			if ( $value < 0x80 ) {
				$output .= chr( $value );
			} else {
				$output .= self::$utf8_chars[ $value - 0x80 ];
			}
		}

		return $output;
	}

	/**
	 * IPTC Dataset identifiers and names
	 *
	 * This array contains the identifiers and names of Datasets defined in
	 * the "IPTC-NAA Information Interchange Model Version No. 4.1".
	 *
	 * @since 0.90
	 *
	 * @var	array
	 */
	private static $mla_iptc_records = array(
		// Envelope Record
		"1#000" => "Model Version",
		"1#005" => "Destination",
		"1#020" => "File Format",
		"1#022" => "File Format Version",
		"1#030" => "Service Identifier",
		"1#040" => "Envelope Number",
		"1#050" => "Product ID",
		"1#060" => "Envelope Priority",
		"1#070" => "Date Sent",
		"1#080" => "Time Sent",
		"1#090" => "Coded Character Set",
		"1#100" => "UNO",
		"1#120" => "ARM Identifier",
		"1#122" => "ARM Version",

		// Application Record
		"2#000" => "Record Version",
		"2#003" => "Object Type Reference",
		"2#004" => "Object Attribute Reference",
		"2#005" => "Object Name",
		"2#007" => "Edit Status",
		"2#008" => "Editorial Update",
		"2#010" => "Urgency",
		"2#012" => "Subject Reference",
		"2#015" => "Category",
		"2#020" => "Supplemental Category",
		"2#022" => "Fixture Identifier",
		"2#025" => "Keywords",
		"2#026" => "Content Location Code",
		"2#027" => "Content Location Name",
		"2#030" => "Release Date",
		"2#035" => "Release Time",
		"2#037" => "Expiration Date",
		"2#038" => "Expiration Time",
		"2#040" => "Special Instructions",
		"2#042" => "Action Advised",
		"2#045" => "Reference Service",
		"2#047" => "Reference Date",
		"2#050" => "Reference Number",
		"2#055" => "Date Created",
		"2#060" => "Time Created",
		"2#062" => "Digital Creation Date",
		"2#063" => "Digital Creation Time",
		"2#065" => "Originating Program",
		"2#070" => "Program Version",
		"2#075" => "Object Cycle",
		"2#080" => "By-line",
		"2#085" => "By-line Title",
		"2#090" => "City",
		"2#092" => "Sub-location",
		"2#095" => "Province or State",
		"2#100" => "Country or Primary Location Code",
		"2#101" => "Country or Primary Location Name",
		"2#103" => "Original Transmission Reference",
		"2#105" => "Headline",
		"2#110" => "Credit",
		"2#115" => "Source",
		"2#116" => "Copyright Notice",
		"2#118" => "Contact",
		"2#120" => "Caption or Abstract",
		"2#122" => "Caption Writer or Editor",
		"2#125" => "Rasterized Caption",
		"2#130" => "Image Type",
		"2#131" => "Image Orientation",
		"2#135" => "Language Identifier",
		"2#150" => "Audio Type",
		"2#151" => "Audio Sampling Rate",
		"2#152" => "Audio Sampling Resolution",
		"2#153" => "Audio Duration",
		"2#154" => "Audio Outcue",
		"2#200" => "ObjectData Preview File Format",
		"2#201" => "ObjectData Preview File Format Version",
		"2#202" => "ObjectData Preview Data",

		// Pre ObjectData Descriptor Record
		"7#010" => "Size Mode",
		"7#020" => "Max Subfile Size",
		"7#090" => "ObjectData Size Announced",
		"7#095" => "Maximum ObjectData Size",

		// ObjectData Record
		"8#010" => "Subfile",

		// Post ObjectData Descriptor Record
		"9#010" => "Confirmed ObjectData Size"
	);

	/**
	 * IPTC Dataset friendly name/slug and identifiers
	 *
	 * This array contains the sanitized names and identifiers of Datasets defined in
	 * the "IPTC-NAA Information Interchange Model Version No. 4.1".
	 *
	 * @since 0.90
	 *
	 * @var	array
	 */
	public static $mla_iptc_keys = array(
		// Envelope Record
		'model-version' => '1#000',
		'destination' => '1#005',
		'file-format' => '1#020',
		'file-format-version' => '1#022',
		'service-identifier' => '1#030',
		'envelope-number' => '1#040',
		'product-id' => '1#050',
		'envelope-priority' => '1#060',
		'date-sent' => '1#070',
		'time-sent' => '1#080',
		'coded-character-set' => '1#090',
		'uno' => '1#100',
		'arm-identifier' => '1#120',
		'arm-version' => '1#122',

		// Application Record
		'record-version' => '2#000',
		'object-type-reference' => '2#003',
		'object-attribute-reference' => '2#004',
		'object-name' => '2#005',
		'edit-status' => '2#007',
		'editorial-update' => '2#008',
		'urgency' => '2#010',
		'subject-reference' => '2#012',
		'category' => '2#015',
		'supplemental-category' => '2#020',
		'fixture-identifier' => '2#022',
		'keywords' => '2#025',
		'content-location-code' => '2#026',
		'content-location-name' => '2#027',
		'release-date' => '2#030',
		'release-time' => '2#035',
		'expiration-date' => '2#037',
		'expiration-time' => '2#038',
		'special-instructions' => '2#040',
		'action-advised' => '2#042',
		'reference-service' => '2#045',
		'reference-date' => '2#047',
		'reference-number' => '2#050',
		'date-created' => '2#055',
		'time-created' => '2#060',
		'digital-creation-date' => '2#062',
		'digital-creation-time' => '2#063',
		'originating-program' => '2#065',
		'program-version' => '2#070',
		'object-cycle' => '2#075',
		'by-line' => '2#080',
		'by-line-title' => '2#085',
		'city' => '2#090',
		'sub-location' => '2#092',
		'province-or-state' => '2#095',
		'country-or-primary-location-code' => '2#100',
		'country-or-primary-location-name' => '2#101',
		'original-transmission-reference' => '2#103',
		'headline' => '2#105',
		'credit' => '2#110',
		'source' => '2#115',
		'copyright-notice' => '2#116',
		'contact' => '2#118',
		'caption-or-abstract' => '2#120',
		'caption-writer-or-editor' => '2#122',
		'rasterized-caption' => '2#125',
		'image-type' => '2#130',
		'image-orientation' => '2#131',
		'language-identifier' => '2#135',
		'audio-type' => '2#150',
		'audio-sampling-rate' => '2#151',
		'audio-sampling-resolution' => '2#152',
		'audio-duration' => '2#153',
		'audio-outcue' => '2#154',
		'objectdata-preview-file-format' => '2#200',
		'objectdata-preview-file-format-version' => '2#201',
		'objectdata-preview-data' => '2#202',

		// Pre ObjectData Descriptor Record
		'size-mode' => '7#010',
		'max-subfile-size' => '7#020',
		'objectdata-size-announced' => '7#090',
		'maximum-objectdata-size' => '7#095',

		// ObjectData Record
		'subfile' => '8#010',

		// Post ObjectData Descriptor Record
		'confirmed-objectdata-size' => '9#010'
);

	/**
	 * IPTC Dataset descriptions
	 *
	 * This array contains the descriptions of Datasets defined in
	 * the "IPTC-NAA Information Interchange Model Version No. 4.1".
	 *
	 * @since 0.90
	 *
	 * @var	array
	 */
	private static $mla_iptc_descriptions = array(
		// Envelope Record
		"1#000" => "2 octet binary IIM version number",
		"1#005" => "Max 1024 characters of Destination (ISO routing information); repeatable",
		"1#020" => "2 octet binary file format number, see IPTC-NAA V4 Appendix A",
		"1#022" => "2 octet binary file format version number",
		"1#030" => "Max 10 characters of Service Identifier and product",
		"1#040" => "8 Character Envelope Number",
		"1#050" => "Max 32 characters subset of provider's overall service; repeatable",
		"1#060" => "1 numeric character of envelope handling priority (not urgency)",
		"1#070" => "8 numeric characters of Date Sent by service - CCYYMMDD",
		"1#080" => "11 characters of Time Sent by service - HHMMSS±HHMM",
		"1#090" => "Max 32 characters of control functions, etc.",
		"1#100" => "14 to 80 characters of eternal, globally unique identification for objects",
		"1#120" => "2 octet binary Abstract Relationship Model Identifier",
		"1#122" => "2 octet binary Abstract Relationship Model Version",

		// Application Record
		"2#000" => "2 octet binary Information Interchange Model, Part II version number",
		"2#003" => "3 to 67 Characters of Object Type Reference number and optional text",
		"2#004" => "3 to 67 Characters of Object Attribute Reference number and optional text; repeatable",
		"2#005" => "Max 64 characters of the object name or shorthand reference",
		"2#007" => "Max 64 characters of the status of the objectdata",
		"2#008" => "2 numeric characters of the type of update this object provides",
		"2#010" => "1 numeric character of the editorial urgency of content",
		"2#012" => "13 to 236 characters of a structured definition of the subject matter; repeatable",
		"2#015" => "Max 3 characters of the subject of the objectdata, DEPRECATED",
		"2#020" => "Max 32 characters (each) of further refinement of subject, DEPRECATED; repeatable",
		"2#022" => "Max 32 characters identifying recurring, predictable content",
		"2#025" => "Max 64 characters (each) of tags; repeatable",
		"2#026" => "3 characters of ISO3166 country code or IPTC-assigned code; repeatable",
		"2#027" => "Max 64 characters of publishable country/geographical location name; repeatable",
		"2#030" => "8 numeric characters of Release Date - CCYYMMDD",
		"2#035" => "11 characters of Release Time (earliest use) - HHMMSS±HHMM",
		"2#037" => "8 numeric characters of Expiration Date (latest use) - CCYYMDD",
		"2#038" => "11 characters of Expiration Time (latest use) - HHMMSS±HHMM",
		"2#040" => "Max 256 Characters of editorial instructions, e.g., embargoes and warnings",
		"2#042" => "2 numeric characters of type of action this object provides to a previous object",
		"2#045" => "Max 10 characters of the Service ID (1#030) of a prior envelope; repeatable",
		"2#047" => "8 numeric characters of prior envelope Reference Date (1#070) - CCYYMMDD; repeatable",
		"2#050" => "8 characters of prior envelope Reference Number (1#040); repeatable",
		"2#055" => "8 numeric characters of intellectual content Date Created - CCYYMMDD",
		"2#060" => "11 characters of intellectual content Time Created - HHMMSS±HHMM",
		"2#062" => "8 numeric characters of digital representation creation date - CCYYMMDD",
		"2#063" => "11 characters of digital representation creation time - HHMMSS±HHMM",
		"2#065" => "Max 32 characters of the program used to create the objectdata",
		"2#070" => "Program Version - Max 10 characters of the version of the program used to create the objectdata",
		"2#075" => "1 character where a=morning, p=evening, b=both",
		"2#080" => "Max 32 Characters of the name of the objectdata creator, e.g., the writer, photographer; repeatable",
		"2#085" => "Max 32 characters of the title of the objectdata creator; repeatable",
		"2#090" => "Max 32 Characters of the city of objectdata origin",
		"2#092" => "Max 32 Characters of the location within the city of objectdata origin",
		"2#095" => "Max 32 Characters of the objectdata origin Province or State",
		"2#100" => "3 characters of ISO3166 or IPTC-assigned code for Country of objectdata origin",
		"2#101" => "Max 64 characters of publishable country/geographical location name of objectdata origin",
		"2#103" => "Max 32 characters of a code representing the location of original transmission",
		"2#105" => "Max 256 Characters of a publishable entry providing a synopsis of the contents of the objectdata",
		"2#110" => "Max 32 Characters that identifies the provider of the objectdata (Vs the owner/creator)",
		"2#115" => "Max 32 Characters that identifies the original owner of the intellectual content",
		"2#116" => "Max 128 Characters that contains any necessary copyright notice",
		"2#118" => "Max 128 characters that identifies the person or organisation which can provide further background information; repeatable",
		"2#120" => "Max 2000 Characters of a textual description of the objectdata",
		"2#122" => "Max 32 Characters that the identifies the person involved in the writing, editing or correcting the objectdata or caption/abstract; repeatable",
		"2#125" => "7360 binary octets of the rasterized caption - 1 bit per pixel, 460x128-pixel image",
		"2#130" => "2 characters of color composition type and information",
		"2#131" => "1 alphabetic character indicating the image area layout - P=portrait, L=landscape, S=square",
		"2#135" => "2 or 3 aphabetic characters containing the major national language of the object, according to the ISO 639:1988 codes",
		"2#150" => "2 characters identifying monaural/stereo and exact type of audio content",
		"2#151" => "6 numeric characters representing the audio sampling rate in hertz (Hz)",
		"2#152" => "2 numeric characters representing the number of bits in each audio sample",
		"2#153" => "6 numeric characters of the Audio Duration - HHMMSS",
		"2#154" => "Max 64 characters of the content of the end of an audio objectdata",
		"2#200" => "2 octet binary file format of the ObjectData Preview",
		"2#201" => "2 octet binary particular version of the ObjectData Preview File Format",
		"2#202" => "Max 256000 binary octets containing the ObjectData Preview data",

		// Pre ObjectData Descriptor Record
		"7#010" => "1 numeric character - 0=objectdata size not known, 1=objectdata size known at beginning of transfer",
		"7#020" => "4 octet binary maximum subfile dataset(s) size",
		"7#090" => "4 octet binary objectdata size if known at beginning of transfer",
		"7#095" => "4 octet binary largest possible objectdata size",

		// ObjectData Record
		"8#010" => "Subfile DataSet containing the objectdata itself; repeatable",

		// Post ObjectData Descriptor Record
		"9#010" => "4 octet binary total objectdata size"
	);

	/**
	 * IPTC file format identifiers and descriptions
	 *
	 * This array contains the file format identifiers and descriptions defined in
	 * the "IPTC-NAA Information Interchange Model Version No. 4.1" for dataset 1#020.
	 *
	 * @since 0.90
	 *
	 * @var	array
	 */
	private static $mla_iptc_formats = array(
		 0 => "No ObjectData",
		 1 => "IPTC-NAA Digital Newsphoto Parameter Record",
		 2 => "IPTC7901 Recommended Message Format",
		 3 => "Tagged Image File Format (Adobe/Aldus Image data)",
		 4 => "Illustrator (Adobe Graphics data)",
		 5 => "AppleSingle (Apple Computer Inc)",
		 6 => "NAA 89-3 (ANPA 1312)",
		 7 => "MacBinary II",
		 8 => "IPTC Unstructured Character Oriented File Format (UCOFF)",
		 9 => "United Press International ANPA 1312 variant",
		10 => "United Press International Down-Load Message",
		11 => "JPEG File Interchange (JFIF)",
		12 => "Photo-CD Image-Pac (Eastman Kodak)",
		13 => "Microsoft Bit Mapped Graphics File [*.BMP]",
		14 => "Digital Audio File [*.WAV] (Microsoft & Creative Labs)",
		15 => "Audio plus Moving Video [*.AVI] (Microsoft)",
		16 => "PC DOS/Windows Executable Files [*.COM][*.EXE]",
		17 => "Compressed Binary File [*.ZIP] (PKWare Inc)",
		18 => "Audio Interchange File Format AIFF (Apple Computer Inc)",
		19 => "RIFF Wave (Microsoft Corporation)",
		20 => "Freehand (Macromedia/Aldus)",
		21 => "Hypertext Markup Language - HTML (The Internet Society)",
		22 => "MPEG 2 Audio Layer 2 (Musicom), ISO/IEC",
		23 => "MPEG 2 Audio Layer 3, ISO/IEC",
		24 => "Portable Document File (*.PDF) Adobe",
		25 => "News Industry Text Format (NITF)",
		26 => "Tape Archive (*.TAR)",
		27 => "Tidningarnas Telegrambyrå NITF version (TTNITF DTD)",
		28 => "Ritzaus Bureau NITF version (RBNITF DTD)",
		29 => "Corel Draw [*.CDR]"
	);

	/**
	 * IPTC image type identifiers and descriptions
	 *
	 * This array contains the image type identifiers and descriptions defined in
	 * the "IPTC-NAA Information Interchange Model Version No. 4.1" for dataset 2#130, octet 2.
	 *
	 * @since 0.90
	 *
	 * @var	array
	 */
	private static $mla_iptc_image_types = array(
		"M" => "Monochrome",
		"Y" => "Yellow Component",
		"M" => "Magenta Component",
		"C" => "Cyan Component",
		"K" => "Black Component",
		"R" => "Red Component",
		"G" => "Green Component",
		"B" => "Blue Component",
		"T" => "Text Only",
		"F" => "Full colour composite, frame sequential",
		"L" => "Full colour composite, line sequential",
		"P" => "Full colour composite, pixel sequential",
		"S" => "Full colour composite, special interleaving"
	);

	/**
	 * Parse one IPTC metadata field
	 * 
	 * @since 1.41
	 *
	 * @param	string	field name - IPTC Identifier or friendly name/slug
	 * @param	array	metadata array containing iptc, exif, xmp and pdf metadata arrays
	 * @param	string	data option; 'text'|'single'|'export'|'array'|'multi'
	 * @param	boolean	Optional: for option 'multi', retain existing values
	 *
	 * @return	mixed	string/array representation of metadata value or an empty string
	 */
	public static function mla_iptc_metadata_value( $iptc_key, $item_metadata, $option = 'text', $keep_existing = false ) {
		// convert friendly name/slug to identifier
		if ( array_key_exists( $iptc_key, self::$mla_iptc_keys ) ) {
			$iptc_key = self::$mla_iptc_keys[ $iptc_key ];
		}

		if ( 'ALL_IPTC' == $iptc_key ) {
			$clean_data = array();
			if ( isset( $item_metadata['mla_iptc_metadata'] ) && is_array( $item_metadata['mla_iptc_metadata'] ) ) {
				foreach ( $item_metadata['mla_iptc_metadata'] as $key => $value ) {
					if ( is_array( $value ) ) {
						foreach ($value as $text_key => $text )
							$value[ $text_key ] = self::_bin_to_utf8( $text );
	
						$clean_data[ $key ] = 'ARRAY(' . implode( ',', $value ) . ')';
					} elseif ( is_string( $value ) ) {
						$clean_data[ $key ] = self::_bin_to_utf8( substr( $value, 0, 256 ) );
					} else {
						$clean_data[ $key ] = self::_bin_to_utf8( $value );
					}
				}
			}
			
			return var_export( $clean_data, true);
		}

		return self::mla_find_array_element( $iptc_key, $item_metadata['mla_iptc_metadata'], $option, $keep_existing );
	}

	/**
	 * Parse one EXIF metadata field
	 * 
	 * Also handles the special pseudo-values 'ALL_EXIF' and 'ALL_IPTC'.
	 *
	 * @since 1.13
	 *
	 * @param	string	field name
	 * @param	array	metadata array containing iptc, exif, xmp and pdf metadata arrays
	 * @param	string	data option; 'text'|'single'|'export'|'array'|'multi'
	 * @param	boolean	Optional: for option 'multi', retain existing values
	 *
	 * @return	mixed	string/array representation of metadata value or an empty string
	 */
	public static function mla_exif_metadata_value( $exif_key, $item_metadata, $option = 'text', $keep_existing = false ) {
		if ( 'ALL_EXIF' == $exif_key ) {
			$clean_data = array();
			if ( isset( $item_metadata['mla_exif_metadata'] ) && is_array( $item_metadata['mla_exif_metadata'] ) ) {
				foreach ( $item_metadata['mla_exif_metadata'] as $key => $value ) {
					if ( is_array( $value ) ) {
						$clean_data[ $key ] = '(ARRAY)';
					} elseif ( is_string( $value ) ) {
						$clean_data[ $key ] = self::_bin_to_utf8( substr( $value, 0, 256 ) );
					} else {
						$clean_data[ $key ] = $value;
					}
				}
			}

			return var_export( $clean_data, true);
		} elseif ( 'ALL_IPTC' == $exif_key ) {
			$clean_data = array();
			if ( isset( $item_metadata['mla_iptc_metadata'] ) && is_array( $item_metadata['mla_iptc_metadata'] ) ) {
				foreach ( $item_metadata['mla_iptc_metadata'] as $key => $value ) {
					if ( is_array( $value ) ) {
						foreach ($value as $text_key => $text )
							$value[ $text_key ] = self::_bin_to_utf8( $text );
	
						$clean_data[ $key ] = 'ARRAY(' . implode( ',', $value ) . ')';
					} elseif ( is_string( $value ) ) {
						$clean_data[ $key ] = self::_bin_to_utf8( substr( $value, 0, 256 ) );
					} else {
						$clean_data[ $key ] = self::_bin_to_utf8( $value );
					}
				}
			}
			
			return var_export( $clean_data, true);
		}

		return self::mla_find_array_element( $exif_key, $item_metadata['mla_exif_metadata'], $option, $keep_existing );
	}

	/**
	 * Parse one XMP metadata field
	 * 
	 * Also handles the special pseudo-value 'ALL_XMP'.
	 *
	 * @since 2.10
	 *
	 * @param	string	field name
	 * @param	array	XMP metadata array
	 * @param	string	data option; 'text'|'single'|'export'|'array'|'multi'
	 * @param	boolean	Optional: for option 'multi', retain existing values
	 *
	 * @return	mixed	string/array representation of metadata value or an empty string
	 */
	public static function mla_xmp_metadata_value( $xmp_key, $xmp_metadata, $option = 'text', $keep_existing = false ) {
		if ( 'ALL_XMP' == $xmp_key ) {
			$clean_data = array();
			if ( is_array( $xmp_metadata ) ) {
				foreach ( $xmp_metadata as $key => $value ) {
					if ( is_array( $value ) ) {
						$clean_data[ $key ] = '(ARRAY)';
					} elseif ( is_string( $value ) ) {
						$clean_data[ $key ] = self::_bin_to_utf8( substr( $value, 0, 256 ) );
					} else {
						$clean_data[ $key ] = $value;
					}
				}
			}
			
			return var_export( $clean_data, true);
		}

		return self::mla_find_array_element($xmp_key, $xmp_metadata, $option, $keep_existing );
	}

	/**
	 * Parse one ID3 (audio/visual) metadata field
	 * 
	 * Also handles the special pseudo-value 'ALL_ID3'.
	 *
	 * @since 2.13
	 *
	 * @param	string	field name
	 * @param	array	ID3 metadata array
	 * @param	string	data option; 'text'|'single'|'export'|'array'|'multi'
	 * @param	boolean	Optional: for option 'multi', retain existing values
	 *
	 * @return	mixed	string/array representation of metadata value or an empty string
	 */
	public static function mla_id3_metadata_value( $id3_key, $id3_metadata, $option, $keep_existing ) {
		if ( 'ALL_ID3' == $id3_key ) {
			$clean_data = array();
			if ( is_array( $id3_metadata ) ) {
				foreach ( $id3_metadata as $key => $value ) {
					if ( is_array( $value ) ) {
						$clean_data[ $key ] = '(ARRAY)';
					} elseif ( is_string( $value ) ) {
						$clean_data[ $key ] = self::_bin_to_utf8( substr( $value, 0, 256 ) );
					} else {
						$clean_data[ $key ] = $value;
					}
				}
			}
			
			return var_export( $clean_data, true);
		}

		return self::mla_find_array_element($id3_key, $id3_metadata, $option, $keep_existing );
	}

	/**
	 * Parse one PDF metadata field
	 * 
	 * Also handles the special pseudo-value 'ALL_PDF'.
	 *
	 * @since 1.50
	 *
	 * @param	string	field name
	 * @param	string	metadata array containing iptc, exif, xmp and pdf metadata arrays
	 *
	 * @return	mixed	string/array representation of metadata value or an empty string
	 */
	public static function mla_pdf_metadata_value( $pdf_key, $item_metadata ) {
		$text = '';
		if ( array_key_exists( $pdf_key, $item_metadata['mla_pdf_metadata'] ) ) {
			$text = $item_metadata['mla_pdf_metadata'][ $pdf_key ];
			if ( is_array( $text ) ) {
				foreach ($text as $key => $value ) {
					if ( is_array( $value ) ) {
						$text[ $key ] = self::_bin_to_utf8( var_export( $value, true ) );
					} else {
						$text[ $key ] = self::_bin_to_utf8( $value );
					}
				}
			} elseif ( is_string( $text ) ) {
				$text = self::_bin_to_utf8( $text );
			}
		} elseif ( 'ALL_PDF' == $pdf_key ) {
			$clean_data = array();
			if ( isset( $item_metadata['mla_pdf_metadata'] ) && is_array( $item_metadata['mla_pdf_metadata'] ) ) {
				foreach ( $item_metadata['mla_pdf_metadata'] as $key => $value ) {
					if ( is_array( $value ) ) {
						$clean_data[ $key ] = '(ARRAY)';
					} elseif ( is_string( $value ) ) {
						$clean_data[ $key ] = self::_bin_to_utf8( substr( $value, 0, 256 ) );
					} else {
						$clean_data[ $key ] = $value;
					}
				}
			}
			
			$text = var_export( $clean_data, true);
		} // ALL_PDF

		return $text;
	}

	/**
	 * Convert an EXIF GPS rational value to a PHP float value
	 * 
	 * @since 1.50
	 *
	 * @param	array	array( 0 => numerator, 1 => denominator )
	 *
	 * @return	float	numerator/denominator
	 */
	private static function _rational_to_decimal( $rational ) {
		$parts = explode('/', $rational);
		return $parts[0] / ( $parts[1] ? $parts[1] : 1);
	}

	/**
	 * Convert an EXIF rational value to a formatted string
	 * 
	 * @since 2.02
	 *
	 * @param	string	numerator/denominator
	 * @param	string	format for integer values
	 * @param	string	format for fractional values from -1 to +1
	 * @param	string	format for integer.fraction values 
	 *
	 * @return	mixed	formatted value or boolean false if no value available
	 */
	private static function _rational_to_string( $rational, $integer_format, $fraction_format, $mixed_format ) {
		$fragments = array_map( 'intval', explode( '/', $rational ) );
		if ( 1 == count( $fragments ) ) {
			$value = trim( $rational );
			if ( ! empty( $value ) ) {
				return $value;
			}
		} else {
			if ( $fragments[0] ) {
				if ( 1 == $fragments[1] ) {
					return sprintf( $integer_format, $fragments[0] );
				} elseif ( 0 != $fragments[1] ) {
					$value = $fragments[0] / $fragments[1];
						if ( ( -1 <= $value ) && ( 1 >= $value ) ) {
							return sprintf( $fraction_format, $fragments[0], $fragments[1] );
						} else {
							if ( $value == intval( $value ) ) {
								return sprintf( $integer_format, $value );
							}else {
								return sprintf( $mixed_format, $value );
							}
						} // mixed value
				} // fractional or mixed value
			} // non-zero numerator
		} // valid denominator

		return false;
	}

	/**
	 * Passes IPTC/EXIF parse errors between mla_IPTC_EXIF_error_handler
	 * and mla_fetch_attachment_image_metadata
	 *
	 * @since 1.81
	 *
	 * @var	array
	 */
	private static $mla_IPTC_EXIF_errors = array();

	/**
	 * Intercept IPTC and EXIF parse errors
	 * 
	 * @since 1.81
	 *
	 * @param	int		the level of the error raised
	 * @param	string	the error message
	 * @param	string	the filename that the error was raised in
	 * @param	int		the line number the error was raised at
	 *
	 * @return	boolean	true, to bypass PHP error handler
	 */
	public static function mla_IPTC_EXIF_error_handler( $type, $string, $file, $line ) {
//error_log( 'DEBUG: mla_IPTC_EXIF_error_handler $type = ' . var_export( $type, true ), 0 );
//error_log( 'DEBUG: mla_IPTC_EXIF_error_handler $string = ' . var_export( $string, true ), 0 );
//error_log( 'DEBUG: mla_IPTC_EXIF_error_handler $file = ' . var_export( $file, true ), 0 );
//error_log( 'DEBUG: mla_IPTC_EXIF_error_handler $line = ' . var_export( $line, true ), 0 );

		switch ( $type ) {
			case E_ERROR:
				$level = 'E_ERROR';
				break;
			case E_WARNING:
				$level = 'E_WARNING';
				break;
			case E_NOTICE:
				$level = 'E_NOTICE';
				break;
			default:
				$level = 'OTHER';
		}

		$path_info = pathinfo( $file );
		$file_name = $path_info['basename'];
		MLAData::$mla_IPTC_EXIF_errors[] = "{$level} ({$type}) - {$string} [{$file_name} : {$line}]";

		/* Don't execute PHP internal error handler */
		return true;
	}

	/**
	 * Fetch and filter ID3 metadata for an audio or video attachment
	 * 
	 * Adapted from /wp-admin/includes/media.php functions wp_add_id3_tag_data,
	 * wp_read_video_metadata and wp_read_audio_metadata
	 *
	 * @since 2.13
	 *
	 * @param	int		post ID of attachment
	 * @param	string	optional; if $post_id is zero, path to the image file.
	 *
	 * @return	array	Meta data variables, including 'audio' and 'video'
	 */
	public static function mla_fetch_attachment_id3_metadata( $post_id, $path = '' ) {
		static $id3 = NULL;

		if ( 0 != $post_id ) {
			$path = get_attached_file($post_id);
		}

		if ( ! empty( $path ) ) {
			if ( ! class_exists( 'getID3' ) ) {
				require( ABSPATH . WPINC . '/ID3/getid3.php' );
			}

			if ( NULL == $id3 ) {
				$id3 = new getID3();
			}

			$data = $id3->analyze( $path );
		}

		if ( ! empty( $data['filesize'] ) )
			$data['filesize'] = (int) $data['filesize'];
		if ( ! empty( $data['playtime_seconds'] ) )
			$data['length'] = (int) round( $data['playtime_seconds'] );

		// from wp_read_video_metadata
		if ( ! empty( $data['video'] ) ) {
			if ( ! empty( $data['video']['bitrate'] ) )
				$data['bitrate'] = (int) $data['video']['bitrate'];
			if ( ! empty( $data['video']['resolution_x'] ) )
				$data['width'] = (int) $data['video']['resolution_x'];
			if ( ! empty( $data['video']['resolution_y'] ) )
				$data['height'] = (int) $data['video']['resolution_y'];
		}

		// from wp_read_audio_metadata
		if ( ! empty( $data['audio'] ) ) {
			unset( $data['audio']['streams'] );
		}

		// from wp_add_id3_tag_data
		foreach ( array( 'id3v2', 'id3v1' ) as $version ) {
			if ( ! empty( $data[ $version ]['comments'] ) ) {
				foreach ( $data[ $version ]['comments'] as $key => $list ) {
					if ( 'length' !== $key && ! empty( $list ) ) {
						$data[ $key ] = reset( $list );
						// Fix bug in byte stream analysis.
						if ( 'terms_of_use' === $key && 0 === strpos( $metadata[ $key ], 'yright notice.' ) )
							$metadata[ $key ] = 'Cop' . $metadata[$key];
					}
				}
				break;
			}
		}
		unset( $data['id3v2']['comments'] );
		unset( $data['id3v1']['comments'] );

		if ( ! empty( $data['id3v2']['APIC'] ) ) {
			$image = reset( $data['id3v2']['APIC']);
			if ( ! empty( $image['data'] ) ) {
				$data['image'] = array(
					'data' => $image['data'],
					'mime' => $image['image_mime'],
					'width' => $image['image_width'],
					'height' => $image['image_height']
				);
			}

			unset( $data['id3v2']['APIC'] );
		} elseif ( ! empty( $data['comments']['picture'] ) ) {
			$image = reset( $data['comments']['picture'] );
			if ( ! empty( $image['data'] ) ) {
				$data['image'] = array(
					'data' => $image['data'],
					'mime' => $image['image_mime']
				);
			}

			unset( $data['comments']['picture'] );
		}

		$data['post_id'] = $post_id;
		return $data;
	}

	/**
	 * Fetch and filter IPTC and EXIF, XMP or PDF metadata for an image attachment
	 * 
	 * @since 0.90
	 *
	 * @param	int		post ID of attachment
	 * @param	string	optional; if $post_id is zero, path to the image file.
	 *
	 * @return	array	Meta data variables, IPTC and EXIF or PDF
	 */
	public static function mla_fetch_attachment_image_metadata( $post_id, $path = '' ) {
		$results = array(
			'post_id' => $post_id,
			'mla_iptc_metadata' => array(),
			'mla_exif_metadata' => array(),
			'mla_xmp_metadata' => array(),
			'mla_pdf_metadata' => array()
			);

		if ( 0 != $post_id ) {
			$path = get_attached_file($post_id);
		}

		if ( ! empty( $path ) ) {
			if ( 'pdf' == strtolower( pathinfo( $path, PATHINFO_EXTENSION ) ) ) {
				if ( !class_exists( 'MLAPDF' ) ) {
					require_once( MLA_PLUGIN_PATH . 'includes/class-mla-data-pdf.php' );
				}

				$pdf_metadata = MLAPDF::mla_extract_pdf_metadata( $path );
				$results['mla_xmp_metadata'] = $pdf_metadata['xmp'];
				$results['mla_pdf_metadata'] = $pdf_metadata['pdf'];
				return $results;
			}

			$size = getimagesize( $path, $info );

			if ( is_callable( 'iptcparse' ) ) {
				if ( ! empty( $info['APP13'] ) ) {
					//set_error_handler( 'MLAData::mla_IPTC_EXIF_error_handler' );
					$iptc_values = iptcparse( $info['APP13'] );
					//restore_error_handler();

					if ( ! empty( MLAData::$mla_IPTC_EXIF_errors ) ) {
						$results['mla_iptc_errors'] = MLAData::$mla_IPTC_EXIF_errors;
						MLAData::$mla_IPTC_EXIF_errors = array();
						MLACore::mla_debug_add( __LINE__ . __( 'ERROR', 'media-library-assistant' ) . ': ' . '$results[mla_iptc_errors] = ' . var_export( $results['mla_exif_errors'], true ), MLACore::MLA_DEBUG_CATEGORY_ANY );
					}

					if ( ! is_array( $iptc_values ) ) {
						$iptc_values = array();
					}

					foreach ( $iptc_values as $key => $value ) {
						if ( in_array( $key, array( '1#000', '1#020', '1#022', '1#120', '1#122', '2#000', '2#200', '2#201' ) ) ) {
							$value = unpack( 'nbinary', $value[0] );
							$results['mla_iptc_metadata'][ $key ] = (string) $value['binary'];
						} elseif ( 1 == count( $value ) ) {
							$results['mla_iptc_metadata'][ $key ] = $value[0];
						} else {
							$results['mla_iptc_metadata'][ $key ] = $value;
						}
					} // foreach $value
				} // ! empty
			} // iptcparse

			if ( is_callable( 'exif_read_data' ) && in_array( $size[2], array( IMAGETYPE_JPEG, IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM ) ) ) {
				//set_error_handler( 'MLAData::mla_IPTC_EXIF_error_handler' );
				$results['mla_exif_metadata'] = $exif_data = @exif_read_data( $path );
//error_log( __LINE__ . " MLAData::mla_fetch_attachment_image_metadata exif_data = " . var_export( $exif_data, true ), 0 );
				//restore_error_handler();
				if ( ! empty( MLAData::$mla_IPTC_EXIF_errors ) ) {
					$results['mla_exif_errors'] = MLAData::$mla_IPTC_EXIF_errors;
					MLAData::$mla_IPTC_EXIF_errors = array();
					MLACore::mla_debug_add( __LINE__ . __( 'ERROR', 'media-library-assistant' ) . ': ' . '$results[mla_exif_errors] = ' . var_export( $results['mla_exif_errors'], true ), MLACore::MLA_DEBUG_CATEGORY_ANY );
				}
			} // exif_read_data

			$results['mla_xmp_metadata'] = self::mla_parse_xmp_metadata( $path, 0 );
			if ( NULL == $results['mla_xmp_metadata'] ) {
				$results['mla_xmp_metadata'] = array();
			}
				
			// experimental damage repair for Elsie Gilmore (earthnutvt)
			if ( isset( $exif_data['Keywords'] ) && ( '????' == substr( $exif_data['Keywords'], 0, 4 ) ) ) {
				if ( isset( $results['mla_xmp_metadata']['Keywords'] ) ) {
					$exif_data['Keywords'] = $results['mla_xmp_metadata']['Keywords'];
					$results['mla_exif_metadata']['Keywords'] = $results['mla_xmp_metadata']['Keywords'];
				} else {
					unset( $exif_data['Keywords'] );
					unset( $results['mla_exif_metadata']['Keywords'] );
				}
			}
		}

		/*
		 * Expand EXIF Camera-related values:
		 *
		 * ExposureBiasValue
		 * ExposureTime
		 * Flash
		 * FNumber 
		 * FocalLength
		 * ShutterSpeed from ExposureTime
		 */
		$new_data = array();
		if ( isset( $exif_data['FNumber'] ) ) {
			if ( false !== ( $value = self::_rational_to_string( $exif_data['FNumber'], '%1$d', '%1$d/%2$d', '%1$.1f' ) ) ) {
				$new_data['FNumber'] = $value;
			}
		} // FNumber

		if ( isset( $exif_data['ExposureBiasValue'] ) ) {
			$fragments = array_map( 'intval', explode( '/', $exif_data['ExposureBiasValue'] ) );
			if ( ! is_null( $fragments[1] ) ) {
				$numerator = $fragments[0];
				$denominator = $fragments[1];

				// Clean up some common format issues, e.g. 4/6, 2/4
				while ( ( 0 == ( $numerator & 0x1 ) ) && ( 0 == ( $denominator & 0x1 ) ) ) {
					$numerator = ( $numerator >> 1 );
					$denominator = ( $denominator >> 1 );
				}

				// Remove excess precision
				if ( ( $denominator > $numerator) && ( 1000 < $numerator ) && ( 1000 < $denominator ) ) {
					$exif_data['ExposureBiasValue'] = sprintf( '%1$+.3f', ( $numerator/$denominator ) );
				} else {
					$fragments[0] = $numerator;
					$fragments[1] = $denominator;
					$exif_data['ExposureBiasValue'] = $numerator . '/' . $denominator;
				}
			}

			if ( false !== ( $value = self::_rational_to_string( $exif_data['ExposureBiasValue'], '%1$+d', '%1$+d/%2$d', '%1$+.2f' ) ) ) {
				$new_data['ExposureBiasValue'] = $value;
			}
		} // ExposureBiasValue

		if ( isset( $exif_data['Flash'] ) ) {
			$value = ( absint( $exif_data['Flash'] ) );
			if ( $value & 0x1 ) {
				$new_data['Flash'] = __( 'Yes', 'media-library-assistant' );
			} else {
				$new_data['Flash'] = __( 'No', 'media-library-assistant' );
			}
		} // Flash

		if ( isset( $exif_data['FocalLength'] ) ) {
			if ( false !== ( $value = self::_rational_to_string( $exif_data['FocalLength'], '%1$d', '%1$d/%2$d', '%1$.2f' ) ) ) {
				$new_data['FocalLength'] = $value;
			}
		} // FocalLength

		if ( isset( $exif_data['ExposureTime'] ) ) {
			if ( false !== ( $value = self::_rational_to_string( $exif_data['ExposureTime'], '%1$d', '%1$d/%2$d', '%1$.2f' ) ) ) {
				$new_data['ExposureTime'] = $value;
			}
		} // ExposureTime

		/*
		 * ShutterSpeed in "1/" format, from ExposureTime
		 * Special logic for "fractional shutter speed" values 1.3, 1.5, 1.6, 2.5
		 */
		if ( isset( $exif_data['ExposureTime'] ) ) {
			$fragments = array_map( 'intval', explode( '/', $exif_data['ExposureTime'] ) );
			if ( ! is_null( $fragments[1] && $fragments[0] ) ) {
				if ( 1 == $fragments[1] ) {
					$new_data['ShutterSpeed'] = $new_data['ExposureTime'] = sprintf( '%1$d', $fragments[0] );
				} elseif ( 0 != $fragments[1] ) {
					$value = $fragments[0] / $fragments[1];
					if ( ( 0 < $value ) && ( 1 > $value ) ) {
						// Convert to "1/" value for shutter speed
						if ( 1 == $fragments[0] ) {
							$new_data['ShutterSpeed'] = $new_data['ExposureTime'];
						} else {
							$test = (float) number_format( 1.0 / $value, 1, '.', '');
							if ( in_array( $test, array( 1.3, 1.5, 1.6, 2.5 ) ) ) {
								$new_data['ShutterSpeed'] = '1/' . number_format( 1.0 / $value, 1, '.', '' );
							} else {
								$new_data['ShutterSpeed'] = '1/' . number_format( 1.0 / $value, 0, '.', '' );
							}
						}
					} else {
						$new_data['ShutterSpeed'] = $new_data['ExposureTime'] = sprintf( '%1$.2f', $value );
					}
				} // fractional value
			} // valid denominator and non-zero numerator
		} // ShutterSpeed

		if ( isset( $exif_data['UndefinedTag:0xA420'] ) ) {
			$new_data['ImageUniqueID'] = $exif_data['UndefinedTag:0xA420'];
		}

		if ( isset( $exif_data['UndefinedTag:0xA430'] ) ) {
			$new_data['CameraOwnerName'] = $exif_data['UndefinedTag:0xA430'];
		}

		if ( isset( $exif_data['UndefinedTag:0xA431'] ) ) {
			$new_data['BodySerialNumber'] = $exif_data['UndefinedTag:0xA431'];
		}

		if ( isset( $exif_data['UndefinedTag:0xA432'] ) && is_array( $exif_data['UndefinedTag:0xA432'] ) ) {
			$array = $new_data['LensSpecification'] = $exif_data['UndefinedTag:0xA432'];

			if ( isset ( $array[0] ) ) {
				if ( false !== ( $value = self::_rational_to_string( $array[0], '%1$d', '%1$d/%2$d', '%1$.2f' ) ) ) {
					$new_data['LensMinFocalLength'] = $value;
				}
			}

			if ( isset ( $array[1] ) ) {
				if ( false !== ( $value = self::_rational_to_string( $array[1], '%1$d', '%1$d/%2$d', '%1$.2f' ) ) ) {
					$new_data['LensMaxFocalLength'] = $value;
				}
			}

			if ( isset ( $array[2] ) ) {
				if ( false !== ( $value = self::_rational_to_string( $array[2], '%1$d', '%1$d/%2$d', '%1$.1f' ) ) ) {
					$new_data['LensMinFocalLengthFN'] = $value;
				}
			}

			if ( isset ( $array[3] ) ) {
				if ( false !== ( $value = self::_rational_to_string( $array[3], '%1$d', '%1$d/%2$d', '%1$.1f' ) ) ) {
					$new_data['LensMaxFocalLengthFN'] = $value;
				}
			}

		}

		if ( isset( $exif_data['UndefinedTag:0xA433'] ) ) {
			$new_data['LensMake'] = $exif_data['UndefinedTag:0xA433'];
		}

		if ( isset( $exif_data['UndefinedTag:0xA434'] ) ) {
			$new_data['LensModel'] = $exif_data['UndefinedTag:0xA434'];
		}

		if ( isset( $exif_data['UndefinedTag:0xA435'] ) ) {
			$new_data['LensSerialNumber'] = $exif_data['UndefinedTag:0xA435'];
		}

		if ( ! empty( $new_data ) ) {
			$results['mla_exif_metadata']['CAMERA'] = $new_data;
		}

		/*
		 * Expand EXIF GPS values
		 */
		$new_data = array();
		if ( isset( $exif_data['GPSVersion'] ) ) {
			$new_data['Version'] = sprintf( '%1$d.%2$d.%3$d.%4$d', ord( $exif_data['GPSVersion'][0] ), ord( $exif_data['GPSVersion'][1] ), ord( $exif_data['GPSVersion'][2] ), ord( $exif_data['GPSVersion'][3] ) );
		}

		if ( isset( $exif_data['GPSLatitudeRef'] ) ) {
			$new_data['LatitudeRef'] = $exif_data['GPSLatitudeRef'];
			$new_data['LatitudeRefS'] = ( 'N' == $exif_data['GPSLatitudeRef'] ) ? '' : '-';
			$ref = $new_data['LatitudeRef'];
			$refs = $new_data['LatitudeRefS'];
		} else {
			$ref = '';
			$refs = '';
		}

		if ( isset( $exif_data['GPSLatitude'] ) ) {
			$rational = $exif_data['GPSLatitude'];
			$new_data['LatitudeD'] = $degrees = self::_rational_to_decimal( $rational[0] );
			$new_data['LatitudeM'] = $minutes = self::_rational_to_decimal( $rational[1] );
			$new_data['LatitudeS'] = sprintf( '%1$01.4f', $seconds = self::_rational_to_decimal( $rational[2] ) );
			$decimal_minutes = $minutes + ( $seconds / 60 );
			$decimal_degrees = ( $decimal_minutes / 60 );

			$new_data['Latitude'] = sprintf( '%1$dd %2$d\' %3$01.4f" %4$s', $degrees, $minutes, $seconds, $ref );
			$new_data['LatitudeDM'] = sprintf( '%1$d %2$01.4f', $degrees, $decimal_minutes );
			$new_data['LatitudeDD'] = sprintf( '%1$01f', $degrees + $decimal_degrees );
			$new_data['LatitudeMinDec'] = substr( $new_data['LatitudeDM'], strpos( $new_data['LatitudeDM'], ' ' ) + 1 );
			$new_data['LatitudeDegDec'] = substr( $new_data['LatitudeDD'], strpos( $new_data['LatitudeDD'], '.' ) );
			$new_data['LatitudeSDM'] = $refs . $new_data['LatitudeDM'];
			$new_data['LatitudeSDD'] = $refs . $new_data['LatitudeDD'];
			$new_data['LatitudeDM'] = $new_data['LatitudeDM'] . $ref;
			$new_data['LatitudeDD'] = $new_data['LatitudeDD'] . $ref;
		}

		if ( isset( $exif_data['GPSLongitudeRef'] ) ) {
			$new_data['LongitudeRef'] = $exif_data['GPSLongitudeRef'];
			$new_data['LongitudeRefS'] = ( 'E' == $exif_data['GPSLongitudeRef'] ) ? '' : '-';
			$ref = $new_data['LongitudeRef'];
			$refs = $new_data['LongitudeRefS'];
		} else {
			$ref = '';
			$refs = '';
		}

		if ( isset( $exif_data['GPSLongitude'] ) ) {
			$rational = $exif_data['GPSLongitude'];
			$new_data['LongitudeD'] = $degrees = self::_rational_to_decimal( $rational[0] );
			$new_data['LongitudeM'] = $minutes = self::_rational_to_decimal( $rational[1] );
			$new_data['LongitudeS'] = sprintf( '%1$01.4f', $seconds = self::_rational_to_decimal( $rational[2] ) );
			$decimal_minutes = $minutes + ( $seconds / 60 );
			$decimal_degrees = ( $decimal_minutes / 60 );

			$new_data['Longitude'] = sprintf( '%1$dd %2$d\' %3$01.4f" %4$s', $degrees, $minutes, $seconds, $ref );
			$new_data['LongitudeDM'] = sprintf( '%1$d %2$01.4f', $degrees, $decimal_minutes );
			$new_data['LongitudeDD'] = sprintf( '%1$01f', $degrees + $decimal_degrees );
			$new_data['LongitudeMinDec'] = substr( $new_data['LongitudeDM'], strpos( $new_data['LongitudeDM'], ' ' ) + 1 );
			$new_data['LongitudeDegDec'] = substr( $new_data['LongitudeDD'], strpos( $new_data['LongitudeDD'], '.' ) );
			$new_data['LongitudeSDM'] = $refs . $new_data['LongitudeDM'];
			$new_data['LongitudeSDD'] = $refs . $new_data['LongitudeDD'];
			$new_data['LongitudeDM'] = $new_data['LongitudeDM'] . $ref;
			$new_data['LongitudeDD'] = $new_data['LongitudeDD'] . $ref;
		}

		if ( isset( $exif_data['GPSAltitudeRef'] ) ) {
			$new_data['AltitudeRef'] = sprintf( '%1$d', ord( $exif_data['GPSAltitudeRef'][0] ) );
			$new_data['AltitudeRefS'] = ( '0' == $new_data['AltitudeRef'] ) ? '' : '-';
			$refs = $new_data['AltitudeRefS'];
		} else {
			$refs = '';
		}

		if ( isset( $exif_data['GPSAltitude'] ) ) {
			$new_data['Altitude'] = sprintf( '%1$s%2$01.4f', $refs, $meters = self::_rational_to_decimal( $exif_data['GPSAltitude'] ) );
			$new_data['AltitudeFeet'] = sprintf( '%1$s%2$01.2f', $refs, $meters * 3.280839895013 );
		}

		if ( isset( $exif_data['GPSTimeStamp'] ) ) {
			$rational = $exif_data['GPSTimeStamp'];
			$new_data['TimeStampH'] = sprintf( '%1$02d', $hours = self::_rational_to_decimal( $rational[0] ) );
			$new_data['TimeStampM'] = sprintf( '%1$02d', $minutes = self::_rational_to_decimal( $rational[1] ) );
			$new_data['TimeStampS'] = sprintf( '%1$02d', $seconds = self::_rational_to_decimal( $rational[2] ) );
			$new_data['TimeStamp'] = sprintf( '%1$02d:%2$02d:%3$02d', $hours, $minutes, $seconds );
		}

		if ( isset( $exif_data['GPSDateStamp'] ) ) {
			$parts = explode( ':', $exif_data['GPSDateStamp'] );		
			$new_data['DateStampY'] = $parts[0];
			$new_data['DateStampM'] = $parts[1];
			$new_data['DateStampD'] = $parts[2];
			$new_data['DateStamp'] = $exif_data['GPSDateStamp'];
		}

		if ( isset( $exif_data['GPSMapDatum'] ) ) {
			$new_data['MapDatum'] = $exif_data['GPSMapDatum'];
		}

		if ( ! empty( $new_data ) ) {
			$results['mla_exif_metadata']['GPS'] = $new_data;
		}

		/*
		 * Expand EXIF array values - replaced by mla_find_array_element MLA v2.13
		 * /
		foreach ( $results['mla_exif_metadata'] as $exif_key => $exif_value ) {
			if ( is_array( $exif_value ) ) {
				foreach ( $exif_value as $key => $value ) {
					$results['mla_exif_metadata'][ $exif_key . '.' . $key ] = $value;
				}
			} // is_array
		} // */

//error_log( __LINE__ . " mla_fetch_attachment_image_metadata( {$post_id} ) results = " . var_export( $results, true ), 0 );
		return $results;
	}

	/**
	 * Update "meta:" data for a single attachment
	 * 
	 * @since 1.51
	 * 
	 * @param	array	The current wp_attachment_metadata value
	 * @param	array	Field name => value pairs
	 *
	 * @return	string	success/failure message(s); empty string if no changes.
	 */
	public static function mla_update_wp_attachment_metadata( &$current_values, $new_meta ) {
		$message = '';

		foreach( $new_meta as $key => $value ) {
			/*
			 * The "Multi" option has no meaning for attachment_metadata;
			 * convert to a simple array or string
			 */ 
			if ( isset( $value[0x80000000] ) ) {
				unset( $value[0x80000000] );
				unset( $value[0x80000001] );
				unset( $value[0x80000002] );

				if ( 1 == count( $value ) ) {
					foreach ( $value as $single_key => $single_value ) {
						if ( is_integer( $single_key ) ) {
							$value = $single_value;
						}
					}
				} // one-element array
			} // Multi-key value

			$value = sanitize_text_field( $value );
			$old_value = self::mla_find_array_element( $key, $current_values, 'array' );
			if ( ! empty( $old_value ) ) {
				if ( empty( $value ) ) {
					if ( self::_unset_array_element( $key, $current_values ) ) {
						/* translators: 1: meta_key */
						$message .= sprintf( __( 'Deleting %1$s', 'media-library-assistant' ) . '<br>', $key );
					} else {
						/* translators: 1: ERROR tag 2: meta_key */
						$message .= sprintf( __( '%1$s: meta:%2$s not found', 'media-library-assistant' ) . '<br>', __( 'ERROR', 'media-library-assistant' ), $key );
					}

					continue;
				}
			} else { // old_value present
				if ( ! empty( $value ) ) {
					if ( self::_set_array_element( $key, $value, $current_values ) ) {
						/* translators: 1: meta_key 2: meta_value */
						$message .= sprintf( __( 'Adding %1$s = %2$s', 'media-library-assistant' ) . '<br>', $key,
							( is_array( $value ) ) ? var_export( $value, true ) : $value );
					} else {
						/* translators: 1: ERROR tag 2: meta_key */
						$message .= sprintf( __( '%1$s: Adding meta:%2$s; not found', 'media-library-assistant' ) . '<br>', __( 'ERROR', 'media-library-assistant' ), $key );
					}

					continue;
				} elseif ( NULL == $value ) {
					if ( self::_unset_array_element( $key, $current_values ) ) {
						/* translators: 1: meta_key */
						$message .= sprintf( __( 'Deleting Null meta:%1$s', 'media-library-assistant' ) . '<br>', $key );
					}

					continue;
				}
			} // old_value empty

			if ( $old_value !== $value ) {
				if ( self::_set_array_element( $key, $value, $current_values ) ) {
					/* translators: 1: element name 2: old_value 3: new_value */
					$message .= sprintf( __( 'Changing %1$s from "%2$s" to "%3$s"', 'media-library-assistant' ) . '<br>', 'meta:' . $key,
						( is_array( $old_value ) ) ? var_export( $old_value, true ) : $old_value,
						( is_array( $value ) ) ? var_export( $value, true ) : $value );
				} else {
					/* translators: 1: ERROR tag 2: meta_key */
					$message .= sprintf( __( '%1$s: Changing meta:%2$s; not found', 'media-library-assistant' ) . '<br>', __( 'ERROR', 'media-library-assistant' ), $key );
				}
			}
		} // foreach new_meta

		return $message;
	}

	/**
	 * Update custom field and "meta:" data for a single attachment
	 * 
	 * @since 1.40
	 * 
	 * @param	int		The ID of the attachment to be updated
	 * @param	array	Field name => value pairs
	 *
	 * @return	string	success/failure message(s)
	 */
	public static function mla_update_item_postmeta( $post_id, $new_meta ) {
		$post_data = MLAQuery::mla_fetch_attachment_metadata( $post_id );
		$message = '';

		$attachment_meta_values = array();
		foreach ( $new_meta as $meta_key => $meta_value ) {
			if ( 'meta:' == substr( $meta_key, 0, 5 ) ) {
				$meta_key = substr( $meta_key, 5 );
				$attachment_meta_values[ $meta_key ] = $meta_value;
				continue;
			}

			if ( $multi_key = isset( $meta_value[0x80000000] ) ) {
				unset( $meta_value[0x80000000] );
			}

			if ( $keep_existing = isset( $meta_value[0x80000001] ) ) {
				$keep_existing = (boolean) $meta_value[0x80000001];
				unset( $meta_value[0x80000001] );
			}

			if ( $no_null = isset( $meta_value[0x80000002] ) ) {
				$no_null = (boolean) $meta_value[0x80000002];
				unset( $meta_value[0x80000002] );
			}

			if ( isset( $post_data[ 'mla_item_' . $meta_key ] ) ) {
				$old_meta_value = $post_data[ 'mla_item_' . $meta_key ];

				if ( $multi_key && $no_null ) {
					if ( is_string( $old_meta_value ) ) {
						$old_meta_value = trim( $old_meta_value );
					}

					$delete = empty( $old_meta_value );
				} else {
					$delete = NULL === $meta_value;
				}

				if ( $delete) {
					if ( delete_post_meta( $post_id, $meta_key ) ) {
						/* translators: 1: meta_key */
						$message .= sprintf( __( 'Deleting %1$s', 'media-library-assistant' ) . '<br>', $meta_key );
					}

					continue;
				}
			} else {
				if ( NULL !== $meta_value ) {
					if ( $multi_key ) {
						foreach ( $meta_value as $new_value ) {
							if ( add_post_meta( $post_id, $meta_key, $new_value ) ) {
								/* translators: 1: meta_key 2: new_value */
								$message .= sprintf( __( 'Adding %1$s = %2$s', 'media-library-assistant' ) . '<br>', $meta_key, '[' . $new_value . ']' );
							}
						}
					} else {
						if ( add_post_meta( $post_id, $meta_key, $meta_value ) ) {
							/* translators: 1: meta_key 2: meta_value */
							$message .= sprintf( __( 'Adding %1$s = %2$s', 'media-library-assistant' ) . '<br>', $meta_key, $meta_value );
						}
					}
				}

				continue; // no change or message if old and new are both NULL
			} // no old value

			$old_text = ( is_array( $old_meta_value ) ) ? var_export( $old_meta_value, true ) : $old_meta_value;

			/*
			 * Multi-key change from existing values to new values
			 */
			if ( $multi_key ) {
				/*
				 * Test for "no changes"
				 */
				if ( $meta_value == (array) $old_meta_value ) {
					continue;
				}

				if ( ! $keep_existing ) {
					if ( delete_post_meta( $post_id, $meta_key ) ) {
						/* translators: 1: meta_key */
						$message .= sprintf( __( 'Deleting old %1$s values', 'media-library-assistant' ) . '<br>', $meta_key );
					}

					$old_meta_value = array();
				} elseif ( $old_text == $old_meta_value ) { // single value
					$old_meta_value = array( $old_meta_value );
				}

				$updated = 0;
				foreach ( $meta_value as $new_value ) {
					if ( ! in_array( $new_value, $old_meta_value ) ) {
						add_post_meta( $post_id, $meta_key, $new_value );
						$old_meta_value[] = $new_value; // prevent duplicates
						$updated++;
					}
				}

				if ( $updated ) {
					$meta_value = get_post_meta( $post_id, $meta_key );
					if ( is_array( $meta_value ) ) {
						if ( 1 == count( $meta_value ) ) {
							$new_text = $meta_value[0];
						} else {
							$new_text = var_export( $meta_value, true );
						}
					} else {
						$new_text = $meta_value;
					}

					/* translators: 1: meta_key 2: old_value 3: new_value 4: update count*/
					$message .= sprintf( __( 'Changing %1$s from "%2$s" to "%3$s"; %4$d updates', 'media-library-assistant' ) . '<br>', 'meta:' . $meta_key, $old_text, $new_text, $updated );
				}
			} elseif ( $old_meta_value !== $meta_value ) {
				if ( is_array( $old_meta_value ) ) {
					delete_post_meta( $post_id, $meta_key );
				}

				if ( is_array( $meta_value ) ) {
					$new_text = var_export( $meta_value, true );
				} else {
					$new_text = $meta_value;
				}

				if ( update_post_meta( $post_id, $meta_key, $meta_value ) ) {
					/* translators: 1: element name 2: old_value 3: new_value */
					$message .= sprintf( __( 'Changing %1$s from "%2$s" to "%3$s"', 'media-library-assistant' ) . '<br>', 'meta:' . $meta_key, $old_text, $new_text );
				}
			}
		} // foreach $new_meta

		/*
		 * Process the "meta:" updates, if any
		 */
		if ( ! empty( $attachment_meta_values ) ) {
			if ( isset( $post_data['mla_wp_attachment_metadata'] ) ) {
				$current_values = $post_data['mla_wp_attachment_metadata'];
			} else {
				$current_values = array();
			}

			$results = self::mla_update_wp_attachment_metadata( $current_values, $attachment_meta_values );
			if ( ! empty( $results ) ) {
				if ( update_post_meta( $post_id, '_wp_attachment_metadata', $current_values ) ) {
					$message .= $results;
				}
			}
		}

		return $message;
	}

	/**
	 * Update a single item; change the "post" data, taxonomy terms 
	 * and meta data for a single attachment
	 * 
	 * @since 0.1
	 * 
	 * @param	int		The ID of the attachment to be updated
	 * @param	array	Field name => value pairs
	 * @param	array	Optional taxonomy term values, default null
	 * @param	array	Optional taxonomy actions (add, remove, replace), default null
	 *
	 * @return	array	success/failure message and NULL content
	 */
	public static function mla_update_single_item( $post_id, $new_data, $tax_input = NULL, $tax_actions = NULL ) {
		$post_data = self::mla_get_attachment_by_id( $post_id, false );
		if ( !isset( $post_data ) ) {
			return array(
				'message' => __( 'ERROR', 'media-library-assistant' ) . ': ' . __( 'Could not retrieve Attachment.', 'media-library-assistant' ),
				'body' => '' 
			);
		}

		$updates = apply_filters( 'mla_update_single_item', compact( array( 'new_data', 'tax_input', 'tax_actions' ) ), $post_id, $post_data );
		$new_data = isset( $updates['new_data'] ) ? $updates['new_data'] : array();
		$tax_input = isset( $updates['tax_input'] ) ? $updates['tax_input'] : NULL;
		$tax_actions = isset( $updates['tax_actions'] ) ? $updates['tax_actions'] : NULL;

		$message = '';
		$updates = array( 'ID' => $post_id );
		$new_data = stripslashes_deep( $new_data );
		$new_meta = NULL;

		foreach ( $new_data as $key => $value ) {
			switch ( $key ) {
				case 'post_title':
					if ( $value == $post_data[ $key ] ) {
						break;
					}

					/* translators: 1: element name 2: old_value 3: new_value */
					$message .= sprintf( __( 'Changing %1$s from "%2$s" to "%3$s"', 'media-library-assistant' ) . '<br>', __( 'Title', 'media-library-assistant' ), esc_attr( $post_data[ $key ] ), esc_attr( $value ) );
					$updates[ $key ] = $value;
					break;
				case 'post_name':
					if ( $value == $post_data[ $key ] ) {
						break;
					}

					$value = sanitize_title( $value );

					/*
					 * Make sure new slug is unique
					 */
					$args = array(
						'name' => $value,
						'post_type' => 'attachment',
						'post_status' => 'inherit',
						'showposts' => 1 
					);
					$my_posts = get_posts( $args );

					if ( $my_posts ) {
						/* translators: 1: ERROR tag 2: old_value */
						$message .= sprintf( __( '%1$s: Could not change Name/Slug "%2$s"; name already exists', 'media-library-assistant' ) . '<br>', __( 'ERROR', 'media-library-assistant' ), $value );
					} else {
						/* translators: 1: element name 2: old_value 3: new_value */
						$message .= sprintf( __( 'Changing %1$s from "%2$s" to "%3$s"', 'media-library-assistant' ) . '<br>', __( 'Name/Slug', 'media-library-assistant' ), esc_attr( $post_data[ $key ] ), esc_attr( $value ) );
						$updates[ $key ] = $value;
					}
					break;
				/*
				 * bulk_image_alt requires a separate key because some attachment types
				 * should not get a value, e.g., text or PDF documents
				 */
				case 'bulk_image_alt':
					if ( 'image/' !== substr( $post_data[ 'post_mime_type' ], 0, 6 ) ) {
						break;
					}
					// fallthru
				case 'image_alt':
					$key = 'mla_wp_attachment_image_alt';
					if ( !isset( $post_data[ $key ] ) ) {
						$post_data[ $key ] = NULL;
					}

					if ( $value == $post_data[ $key ] ) {
						break;
					}

					if ( empty( $value ) ) {
						if ( delete_post_meta( $post_id, '_wp_attachment_image_alt' ) ) {
							/* translators: 1: old_value */
							$message .= sprintf( __( 'Deleting ALT Text, was "%1$s"', 'media-library-assistant' ) . '<br>', esc_attr( $post_data[ $key ] ) );
						} else {
							/* translators: 1: ERROR tag 2: old_value */
							$message .= sprintf( __( '%1$s: Could not delete ALT Text, remains "%2$s"', 'media-library-assistant' ) . '<br>', __( 'ERROR', 'media-library-assistant' ), esc_attr( $post_data[ $key ] ) );
						}
					} else {
						/*
						 * ALT Text isn't supposed to have multiple values, but it happens.
						 * Delete multiple values and start over.
						 */
						if ( is_array( $post_data[ $key ] ) ) {
							delete_post_meta( $post_id, '_wp_attachment_image_alt' );
						}

						if ( update_post_meta( $post_id, '_wp_attachment_image_alt', $value ) ) {
							/* translators: 1: element name 2: old_value 3: new_value */
							$message .= sprintf( __( 'Changing %1$s from "%2$s" to "%3$s"', 'media-library-assistant' ) . '<br>', __( 'ALT Text', 'media-library-assistant' ), esc_attr( $post_data[ $key ] ), esc_attr( $value ) );
						} else {
							/* translators: 1: ERROR tag 2: old_value 3: new_value */
							$message .= sprintf( __( '%1$s: Could not change ALT Text from "%2$s" to "%3$s"', 'media-library-assistant' ) . '<br>', __( 'ERROR', 'media-library-assistant' ), esc_attr( $post_data[ $key ] ), esc_attr( $value ) );
						}
					}
					break;
				case 'post_excerpt':
					if ( $value == $post_data[ $key ] ) {
						break;
					}

					/* translators: 1: element name 2: old_value 3: new_value */
					$message .= sprintf( __( 'Changing %1$s from "%2$s" to "%3$s"', 'media-library-assistant' ) . '<br>', __( 'Caption', 'media-library-assistant' ), esc_attr( $post_data[ $key ] ), esc_attr( $value ) );
					$updates[ $key ] = $value;
					break;
				case 'post_content':
					if ( $value == $post_data[ $key ] ) {
						break;
					}

					/* translators: 1: element name 2: old_value 3: new_value */
					$message .= sprintf( __( 'Changing %1$s from "%2$s" to "%3$s"', 'media-library-assistant' ) . '<br>', __( 'Description', 'media-library-assistant' ), esc_textarea( $post_data[ $key ] ), esc_textarea( $value ) );
					$updates[ $key ] = $value;
					break;
				case 'post_parent':
					if ( $value == $post_data[ $key ] ) {
						break;
					}

					$value = absint( $value );

					/* translators: 1: element name 2: old_value 3: new_value */
					$message .= sprintf( __( 'Changing %1$s from "%2$s" to "%3$s"', 'media-library-assistant' ) . '<br>', __( 'Parent', 'media-library-assistant' ), $post_data[ $key ], $value );
					$updates[ $key ] = $value;
					break;
				case 'menu_order':
					if ( $value == $post_data[ $key ] ) {
						break;
					}

					$value = absint( $value );

					/* translators: 1: element name 2: old_value 3: new_value */
					$message .= sprintf( __( 'Changing %1$s from "%2$s" to "%3$s"', 'media-library-assistant' ) . '<br>', __( 'Menu Order', 'media-library-assistant' ), $post_data[ $key ], $value );
					$updates[ $key ] = $value;
					break;
				case 'post_author':
					if ( $value == $post_data[ $key ] ) {
						break;
					}

					$value = absint( $value );

					$from_user = get_userdata( $post_data[ $key ] );
					$to_user = get_userdata( $value );
					/* translators: 1: element name 2: old_value 3: new_value */
					$message .= sprintf( __( 'Changing %1$s from "%2$s" to "%3$s"', 'media-library-assistant' ) . '<br>', __( 'Author', 'media-library-assistant' ), $from_user->display_name, $to_user->display_name );
					$updates[ $key ] = $value;
					break;
				case 'comment_status':
					if ( $value == $post_data[ $key ] ) {
						break;
					}

					/* translators: 1: element name 2: old_value 3: new_value */
					$message .= sprintf( __( 'Changing %1$s from "%2$s" to "%3$s"', 'media-library-assistant' ) . '<br>', __( 'Comments', 'media-library-assistant' ), esc_attr( $post_data[ $key ] ), esc_attr( $value ) );
					$updates[ $key ] = $value;
					break;
				case 'ping_status':
					if ( $value == $post_data[ $key ] ) {
						break;
					}

					/* translators: 1: element name 2: old_value 3: new_value */
					$message .= sprintf( __( 'Changing %1$s from "%2$s" to "%3$s"', 'media-library-assistant' ) . '<br>', __( 'Pings', 'media-library-assistant' ), esc_attr( $post_data[ $key ] ), esc_attr( $value ) );
					$updates[ $key ] = $value;
					break;
				case 'taxonomy_updates':
					$tax_input = $value['inputs'];
					$tax_actions = $value['actions'];
					break;
				case 'custom_updates':
					$new_meta = $value;
					break;
				default:
					// Ignore anything else
			} // switch $key
		} // foreach $new_data

		if ( ! empty( $tax_input ) ) {
			foreach ( $tax_input as $taxonomy => $tags ) {
				if ( ! empty( $tax_actions ) ) {
					$tax_action = $tax_actions[ $taxonomy ];
				} else {
					$tax_action = 'replace';
				}

				$taxonomy_obj = get_taxonomy( $taxonomy );

				if ( current_user_can( $taxonomy_obj->cap->assign_terms ) ) {
					if ( is_array( $tags ) ) // array of int = hierarchical, comma-delimited string = non-hierarchical.
						$tags = array_filter( $tags );

					switch ( $tax_action ) {
						case 'add':
							if ( ! empty( $tags ) ) {
								$action_name = __( 'Adding', 'media-library-assistant' );
								$result = wp_set_post_terms( $post_id, $tags, $taxonomy, true );
							}
							break;
						case 'remove':
							$action_name = __( 'Removing', 'media-library-assistant' );
							$tags = self::_remove_terms( $post_id, $tags, $taxonomy_obj );
							$result = wp_set_post_terms( $post_id, $tags, $taxonomy );

							if ( empty( $tags ) ) {
								$result = true;
							}
							break;
						case 'replace':
							$action_name = __( 'Replacing', 'media-library-assistant' );
							$result = wp_set_post_terms( $post_id, $tags, $taxonomy );

							if ( empty( $tags ) ) {
								$result = true;
							}
							break;
						default:
							$action_name = __( 'Ignoring', 'media-library-assistant' );
							$result = NULL;
							// ignore anything else
					}

					/*
					 * Definitive results check would use:
					 * do_action( 'set_object_terms', $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids );
					 * in /wp_includes/taxonomy.php function wp_set_object_terms()
					 */
					if ( ! empty( $result ) ) {
						delete_transient( MLA_OPTION_PREFIX . 't_term_counts_' . $taxonomy );
						/* translators: 1: action_name, 2: taxonomy */
						$message .= sprintf( __( '%1$s "%2$s" terms', 'media-library-assistant' ) . '<br>', $action_name, $taxonomy );
					}
				} else { // current_user_can
					/* translators: 1: taxonomy */
					$message .= sprintf( __( 'You cannot assign "%1$s" terms', 'media-library-assistant' ) . '<br>', $taxonomy );
				}
			} // foreach $tax_input
		} // ! empty $tax_input

		if ( is_array( $new_meta ) ) {
			$message .= self::mla_update_item_postmeta( $post_id, $new_meta );
		}

		if ( empty( $message ) ) {
			return array(
				/* translators: 1: post ID */
				'message' => sprintf( __( 'Item %1$d, no changes detected.', 'media-library-assistant' ), $post_id ),
				'body' => '' 
			);
		} else {
			// invalidate the cached item
			self::mla_get_attachment_by_id( -1 );
			MLAQuery::mla_fetch_attachment_parent_data( -1 );
			MLAQuery::mla_fetch_attachment_metadata( -1 );
			MLAQuery::mla_fetch_attachment_references( -1, 0 );

			// See if anything else has changed
			if ( 1 <  count( $updates ) ) {
				$result = wp_update_post( $updates );
			} else {
				$result = $post_id;
			}

			do_action( 'mla_updated_single_item', $post_id, $result );

			if ( $result ) {
				/* translators: 1: post ID */
				$final_message = sprintf( __( 'Item %1$d updated.', 'media-library-assistant' ), $post_id );
				/*
				 * Uncomment this for debugging.
				 */
				// $final_message .= '<br>' . $message;
				//error_log( 'DEBUG: mla_update_single_item message = ' . var_export( $message, true ), 0 );

				return array(
					'message' => $final_message,
					'body' => '' 
				);
			} else {
				return array(
					/* translators: 1: ERROR tag 2: post ID */
					'message' => sprintf( __( '%1$s: Item %2$d update failed.', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $post_id ),
					'body' => '' 
				);
			}
		}
	}

	/**
	 * Remove terms from an attachment's assignments
	 * 
	 * @since 0.40
	 * 
	 * @param	integer	The ID of the attachment to be updated
	 * @param	array	The term ids (integer array) or names (string array) to remove
	 * @param	object	The taxonomy object
	 *
	 * @return	array	Term ids/names of the surviving terms
	 */
	private static function _remove_terms( $post_id, $terms, $taxonomy_obj ) {
		$taxonomy = $taxonomy_obj->name;
		$hierarchical = $taxonomy_obj->hierarchical;

		/*
		 * Get the current terms for the terms_after check
		 */
		$current_terms = get_object_term_cache( $post_id, $taxonomy );
		if ( false === $current_terms ) {
			$current_terms = wp_get_object_terms( $post_id, $taxonomy );
			wp_cache_add( $post_id, $current_terms, $taxonomy . '_relationships' );
		}

		$terms_before = array();
		foreach( $current_terms as $term ) {
			$terms_before[ $term->term_id ] = $term->name;
		}

		$terms_after = array();
		if ( $hierarchical ) {
			$terms = array_map( 'intval', $terms );
			$terms = array_unique( $terms );

			foreach( $terms_before as $index => $term ) {
				if ( ! in_array( $index, $terms ) ) {
					$terms_after[] = $index;
				}
			}
		} else {
			// WordPress encodes special characters, e.g., "&" as HTML entities in term names
			array_map( '_wp_specialchars', $terms );
			foreach( $terms_before as $index => $term ) {
				if ( ! in_array( $term, $terms ) ) {
					$terms_after[] = $term;
				}
			}
		}

		return $terms_after;
	}

	/**
	 * Format printable version of binary data
	 * 
	 * @since 0.90
	 * 
	 * @param	string	Binary data
	 * @param	integer	Bytes to format, default = 0 (all bytes)
	 * @param	intger	Bytes to format on each line
	 * @param	integer	offset of initial byte, or -1 to suppress printing offset information
	 *
	 * @return	string	Printable representation of $data
	 */
	public static function mla_hex_dump( $data, $limit = 0, $bytes_per_row = 16, $offset = -1 ) {
		if ( 0 == $limit ) {
			$limit = strlen( $data );
		}

		$position = 0;
		$output = "\r\n";
		$print_offset = ( 0 <= $offset );

		if ( $print_offset ) {
			$print_length = $bytes_per_row + 5;
		} else {
			$print_length = $bytes_per_row;
		}

		while ( $position < $limit ) {
			$row_length = strlen( substr( $data, $position ) );

			if ( 0 == $row_length ) {
				break;
			}

			if ( $row_length > ( $limit - $position ) ) {
				$row_length = $limit - $position;
			}

			if ( $row_length > $bytes_per_row ) {
				$row_length = $bytes_per_row;
			}

			$row_data = substr( $data, $position, $row_length );

			if ( $print_offset ) {
				$print_string = sprintf( '%04X ', $position + $offset );
			} else {
				$print_string = '';
			}

			$hex_string = '';
			for ( $index = 0; $index < $row_length; $index++ ) {
				$char = ord( substr( $row_data, $index, 1 ) );
				if ( ( 31 < $char ) && ( 127 > $char ) ) {
					$print_string .= chr($char);
				} else {
					$print_string .= '.';
				}

				$hex_string .= ' ' . bin2hex( chr($char) );
			} // for

			$output .= str_pad( $print_string, $print_length, ' ', STR_PAD_RIGHT ) . $hex_string . "\r\n";
			$position += $row_length;
		} // while

		return $output;
	}
} // class MLAData
?>
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
	 * Provides a unique suffix for the ALT Text "Search Media" SQL View
	 *
	 * The SQL View is used to filter the Media/Assistant submenu table by
	 * ALT Text with the Search Media text box.
	 *
	 * @since 0.40
	 */
	const MLA_ALT_TEXT_VIEW_SUFFIX = 'alt_text_view';

	/**
	 * Provides a unique name for the ALT Text "Search Media" SQL View
	 *
	 * @since 0.40
	 *
	 * @var	array
	 */
	private static $mla_alt_text_view = NULL;

	/**
	 * Provides a unique suffix for the custom field "orderby" SQL View
	 *
	 * The SQL View is used to sort the Media/Assistant submenu table on
	 * ALT Text and custom field columns.
	 *
	 * @since 2.15
	 */
	const MLA_ORDERBY_VIEW_SUFFIX = 'orderby_view';

	/**
	 * Provides a unique name for the custom field "orderby" SQL View
	 *
	 * @since 2.15
	 *
	 * @var	array
	 */
	private static $mla_orderby_view = NULL;

	/**
	 * Provides a unique suffix for the "Table View custom:" SQL View
	 *
	 * The SQL View is used to filter the Media/Assistant submenu table on
	 * custom field Table Views.
	 *
	 * @since 2.15
	 */
	const MLA_TABLE_VIEW_CUSTOM_SUFFIX = 'table_view_custom';

	/**
	 * Provides a unique name for the "Table View custom:" SQL View
	 *
	 * @since 2.15
	 *
	 * @var	array
	 */
	private static $mla_table_view_custom = NULL;

	/**
	 * WordPress version test for $wpdb->esc_like() Vs esc_sql()
	 *
	 * @since 2.13
	 *
	 * @var	boolean
	 */
	private static $wp_4dot0_plus = true;

	/**
	 * Initialization function, similar to __construct()
	 *
	 * @since 0.1
	 */
	public static function initialize() {
		global $table_prefix;

		self::$mla_alt_text_view = $table_prefix . MLA_OPTION_PREFIX . self::MLA_ALT_TEXT_VIEW_SUFFIX;
		self::$mla_orderby_view = $table_prefix . MLA_OPTION_PREFIX . self::MLA_ORDERBY_VIEW_SUFFIX;
		self::$mla_table_view_custom = $table_prefix . MLA_OPTION_PREFIX . self::MLA_TABLE_VIEW_CUSTOM_SUFFIX;
		self::$wp_4dot0_plus = version_compare( get_bloginfo('version'), '4.0', '>=' );

		add_action( 'save_post', 'MLAData::mla_save_post_action', 10, 1);
		add_action( 'edit_attachment', 'MLAData::mla_save_post_action', 10, 1);
		add_action( 'add_attachment', 'MLAData::mla_save_post_action', 10, 1);
	}

	/**
	 * Load an HTML template from a file
	 *
	 * Loads a template to a string or a multi-part template to an array.
	 * Multi-part templates are divided by comments of the form <!-- template="key" -->,
	 * where "key" becomes the key part of the array.
	 *
	 * @since 0.1
	 *
	 * @param	string 	Complete path and/or name of the template file, option name or the raw template
	 * @param	string 	Optional type of template source; 'path', 'file' (default), 'option', 'string'
	 *
	 * @return	string|array|false|NULL
	 *			string for files that do not contain template divider comments,
	 *			array for files containing template divider comments,
	 *			false if file or option does not exist,
	 *			NULL if file could not be loaded.
	 */
	public static function mla_load_template( $source, $type = 'file' ) {
		switch ( $type ) {
			case 'file':
				/*
				 * Look in three places, in this order:
				 * 1) Custom templates
				 * 2) Language-specific templates
				 * 3) Standard templates
				 */
				$text_domain = 'media-library-assistant';
				$locale = apply_filters( 'mla_plugin_locale', get_locale(), $text_domain );
				$path = trailingslashit( WP_LANG_DIR ) . $text_domain . '/tpls/' . $locale . '/' . $source;
				if ( file_exists( $path ) ) {
					$source = $path;
				} else {
					$path = MLA_PLUGIN_PATH . 'languages/tpls/' . $locale . '/' . $source;
					if ( file_exists( $path ) ) {
						$source = $path;
					} else {
						$source = MLA_PLUGIN_PATH . 'tpls/' . $source;
					}
				}
				// fallthru
			case 'path':
				if ( !file_exists( $source ) ) {
					return false;
				}

				$template = file_get_contents( $source, true );
				if ( $template == false ) {
					/* translators: 1: ERROR tag 2: path and file name */
					error_log( sprintf( _x( '%1$s: mla_load_template file "%2$s" not found.', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), var_export( $source, true ) ), 0 );
					return NULL;
				}
				break;
			case 'option':
				$template = MLAOptions::mla_get_option( $source );
				if ( $template == false ) {
					return false;
				}
				break;
			case 'string':
				$template = $source;
				if ( empty( $template ) ) {
					return false;
				}
				break;
			default:
				/* translators: 1: ERROR tag 2: path and file name 3: source type, e.g., file, option, string */
				error_log( sprintf( _x( '%1$s: mla_load_template file "%2$s" bad source type "%3$s".', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $source, $type ), 0 );
				return NULL;
		}

		$match_count = preg_match_all( '#\<!-- template=".+" --\>#', $template, $matches, PREG_OFFSET_CAPTURE );

		if ( ( $match_count == false ) || ( $match_count == 0 ) ) {
			return $template;
		}

		$matches = array_reverse( $matches[0] );

		$template_array = array();
		$current_offset = strlen( $template );
		foreach ( $matches as $key => $value ) {
			$template_key = preg_split( '#"#', $value[0] );
			$template_key = $template_key[1];
			$template_value = substr( $template, $value[1] + strlen( $value[0] ), $current_offset - ( $value[1] + strlen( $value[0] ) ) );
			/*
			 * Trim exactly one newline sequence from the start of the value
			 */
			if ( 0 === strpos( $template_value, "\r\n" ) ) {
				$offset = 2;
			} elseif ( 0 === strpos( $template_value, "\n\r" ) ) {
				$offset = 2;
			} elseif ( 0 === strpos( $template_value, "\n" ) ) {
				$offset = 1;
			} elseif ( 0 === strpos( $template_value, "\r" ) ) {
				$offset = 1;
			} else {
				$offset = 0;
			}

			$template_value = substr( $template_value, $offset );

			/*
			 * Trim exactly one newline sequence from the end of the value
			 */
			$length = strlen( $template_value );
			if ( $length > 2) {
				$postfix = substr( $template_value, ($length - 2), 2 );
			} else {
				$postfix = $template_value;
			}

			if ( 0 === strpos( $postfix, "\r\n" ) ) {
				$length -= 2;
			} elseif ( 0 === strpos( $postfix, "\n\r" ) ) {
				$length -= 2;
			} elseif ( 0 === strpos( $postfix, "\n" ) ) {
				$length -= 1;
			} elseif ( 0 === strpos( $postfix, "\r" ) ) {
				$length -= 1;
			}

			$template_array[ $template_key ] = substr( $template_value, 0, $length );
			$current_offset = $value[1];
		} // foreach $matches

		return $template_array;
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
					error_log( sprintf( _x( '%1$s: _find_template_substring no template end delimiter, tail = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), substr( $tpl, $offset ) ), 0 );
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
					error_log( sprintf( _x( '%1$s: mla_parse_array_template no template end delimiter, tail = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), substr( $tpl, $offset ) ), 0 );
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
					error_log( sprintf( _x( '%1$s: mla_parse_template no end delimiter, tail = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), substr( $tpl, $offset ) ), 0 );
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
					error_log( sprintf( _x( '%1$s: _find_test_substring no end delimiter, tail = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), substr( $tpl, $nest ) ), 0 );
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
					error_log( sprintf( _x( '%1$s: _evaluate_template_array_node unknown type "%2$s".', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $node ), 0 );
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
				error_log( sprintf( _x( '%1$s: _evaluate_template_node unknown type "%2$s".', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $node ), 0 );
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
							$meta_values = self::mla_fetch_attachment_metadata( $post_id );
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

					if ( MLAOptions::mla_is_data_source( $candidate ) ) {
						$data_value = array(
							'data_source' => $candidate,
							'keep_existing' => false,
							'format' => 'raw',
							'option' => $value['option'] ); // single, export, text for array values, e.g., alt_text

						$markup_values[ $key ] = MLAOptions::mla_get_data_source( $post_id, 'single_attachment_mapping', $data_value );
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
					error_log( sprintf( _x( '%1$s: mla_get_template_placeholders no template-end delimiter dump = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), self::mla_hex_dump( substr( $tpl, $template_offset, 128 ), 128, 16 ) ), 0 );
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
	 * Cache the results of mla_count_list_table_items for reuse in mla_query_list_table_items
	 *
	 * @since 1.40
	 *
	 * @var	array
	 */
	private static $mla_list_table_items = NULL;

	/**
	 * Get the total number of attachment posts
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
		if ( NULL !== $offset && NULL !== $count ) {
			$request = self::_prepare_list_table_query( $request, $offset, $count );
			$request = apply_filters( 'mla_list_table_query_final_terms', $request );

			self::$mla_list_table_items = apply_filters( 'mla_list_table_query_custom_items', NULL, $request );
			if ( is_null( self::$mla_list_table_items ) ) {
				self::$mla_list_table_items = self::_execute_list_table_query( $request );
			}

			return self::$mla_list_table_items->found_posts;
		}

		$request = self::_prepare_list_table_query( $request );
		$request = apply_filters( 'mla_list_table_query_final_terms', $request );

		$results = apply_filters( 'mla_list_table_query_custom_items', NULL, $request );
		if ( is_null( $results ) ) {
			$results = self::_execute_list_table_query( $request );
		}

		self::$mla_list_table_items = NULL;

		return $results->found_posts;
	}

	/**
	 * Retrieve attachment objects for list table display
	 *
	 * Supports prepare_items in class-mla-list-table.php.
	 * Modeled after wp_edit_attachments_query in wp-admin/post.php
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
		if ( NULL == self::$mla_list_table_items ) {
			$request = self::_prepare_list_table_query( $request, $offset, $count );
			$request = apply_filters( 'mla_list_table_query_final_terms', $request );

			self::$mla_list_table_items = apply_filters( 'mla_list_table_query_custom_items', NULL, $request );
			if ( is_null( self::$mla_list_table_items ) ) {
				self::$mla_list_table_items = self::_execute_list_table_query( $request );
			}
		}

		$attachments = self::$mla_list_table_items->posts;
		foreach ( $attachments as $index => $attachment ) {
			/*
			 * Add parent data
			 */
			$parent_data = self::mla_fetch_attachment_parent_data( $attachment->post_parent );
			foreach ( $parent_data as $parent_key => $parent_value ) {
				$attachments[ $index ]->$parent_key = $parent_value;
			}

			/*
			 * Add meta data
			 */
			$meta_data = self::mla_fetch_attachment_metadata( $attachment->ID );
			foreach ( $meta_data as $meta_key => $meta_value ) {
				$attachments[ $index ]->$meta_key = $meta_value;
			}
		}

		/*
		 * Add references
		 */
		self::mla_attachment_array_fetch_references( $attachments );

		return $attachments;
	}

	/**
	 * Retrieve attachment objects for the WordPress Media Manager
	 *
	 * Supports month-year and taxonomy-term filters as well as the enhanced search box
	 *
	 * @since 1.20
	 *
	 * @param	array	query parameters from Media Manager
	 * @param	int		number of rows to skip over to reach desired page
	 * @param	int		number of rows on each page
	 *
	 * @return	object	WP_Query object with query results
	 */
	public static function mla_query_media_modal_items( $request, $offset, $count ) {
		$request = self::_prepare_list_table_query( $request, $offset, $count );
		$request = apply_filters( 'mla_media_modal_query_final_terms', $request );

		$results = apply_filters( 'mla_media_modal_query_custom_items', NULL, $request );
		return is_null( $results ) ? self::_execute_list_table_query( $request ) : $results;
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
	 * Sanitize and expand query arguments from request variables
	 *
	 * Prepare the arguments for WP_Query.
	 * Modeled after wp_edit_attachments_query in wp-admin/post.php
	 *
	 * @since 0.1
	 *
	 * @param	array	query parameters from web page, usually found in $_REQUEST
	 * @param	int		Optional number of rows (default 0) to skip over to reach desired page
	 * @param	int		Optional number of rows on each page (0 = all rows, default)
	 *
	 * @return	array	revised arguments suitable for WP_Query
	 */
	private static function _prepare_list_table_query( $raw_request, $offset = 0, $count = 0 ) {
		/*
		 * Go through the $raw_request, take only the arguments that are used in the query and
		 * sanitize or validate them.
		 */
		if ( ! is_array( $raw_request ) ) {
			/* translators: 1: ERROR tag 2: function name 3: non-array value */
			error_log( sprintf( _x( '%1$s: %2$s non-array "%3$s"', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), 'MLAData::_prepare_list_table_query', var_export( $raw_request, true ) ), 0 );
			return null;
		}

		/*
		 * Make sure the current orderby choice still exists or revert to default.
		 */
		$default_orderby = array_merge( array( 'none' => array('none',false) ), MLA_List_Table::mla_get_sortable_columns( ) );
		$current_orderby = MLAOptions::mla_get_option( MLAOptions::MLA_DEFAULT_ORDERBY );
		$found_current = false;
		foreach ( $default_orderby as $key => $value ) {
			if ( $current_orderby == $value[0] ) {
				$found_current = true;
				break;
			}
		}

		if ( $found_current ) {
			/*
			 * Custom fields can have HTML reserved characters, which are encoded by
			 * mla_get_sortable_columns, so a separate, unencoded list is required.
			 */
			$default_orderby = MLAOptions::mla_custom_field_support( 'custom_sortable_columns' );
			foreach ( $default_orderby as $sort_key => $sort_value ) {
				if ( $current_orderby == $sort_key ) {
					$current_orderby = 'c_' . $sort_value[0];
					break;
				}
			} // foreach
		} else {
			MLAOptions::mla_delete_option( MLAOptions::MLA_DEFAULT_ORDERBY );
			$current_orderby = MLAOptions::mla_get_option( MLAOptions::MLA_DEFAULT_ORDERBY );
		}

		$clean_request = array (
			'm' => 0,
			'orderby' => $current_orderby,
			'order' => MLAOptions::mla_get_option( MLAOptions::MLA_DEFAULT_ORDER ),
			'post_type' => 'attachment',
			'post_status' => 'inherit',
			'mla_search_connector' => 'AND',
			'mla_search_fields' => array()
		);

		foreach ( $raw_request as $key => $value ) {
			switch ( $key ) {
				/*
				 * 'sentence' and 'exact' modify the keyword search ('s')
				 * Their value is not important, only their presence.
				 */
				case 'sentence':
				case 'exact':
				case 'mla-tax':
				case 'mla-term':
					$clean_request[ $key ] = sanitize_key( $value );
					break;
				case 'orderby':
					if ( in_array( $value, array( 'none', 'post__in' ) ) ) {
						$clean_request[ $key ] = $value;
					} else {
						$orderby = NULL;
						/*
						 * Custom fields can have HTML reserved characters, which are encoded by
						 * mla_get_sortable_columns, so a separate, unencoded list is required.
						 */
						$sortable_columns = MLAOptions::mla_custom_field_support( 'custom_sortable_columns' );
						foreach ($sortable_columns as $sort_key => $sort_value ) {
							if ( $value == $sort_key ) {
								$orderby = 'c_' . $sort_value[0];
								break;
							}
						} // foreach

						if ( NULL === $orderby ) {
							$sortable_columns = MLA_List_Table::mla_get_sortable_columns();
							foreach ($sortable_columns as $sort_key => $sort_value ) {
								if ( $value == $sort_value[0] ) {
									$orderby = $value;
									break;
								}
							} // foreach
						}

						if ( NULL !== $orderby ) {
							$clean_request[ $key ] = $orderby;
						}
					}

					break;
				/*
				 * ids allows hooks to supply a persistent list of items
				 */
				case 'ids':
					if ( is_array( $value ) ) {
						$clean_request[ 'post__in' ] = $value;
					} else {
						$clean_request[ 'post__in' ] = array_map( 'absint', explode( ',', $value ) );
					}
					break;
				/*
				 * post__in and post__not_in are used in the Media Modal Ajax queries
				 */
				case 'post__in':
				case 'post__not_in':
				case 'post_mime_type':
					$clean_request[ $key ] = $value;
					break;
				case 'parent':
				case 'post_parent':
					$clean_request[ 'post_parent' ] = absint( $value );
					break;
				/*
				 * ['m'] - filter by year and month of post, e.g., 201204
				 */
				case 'author':
				case 'm':
					$clean_request[ $key ] = absint( $value );
					break;
				/*
				 * ['mla_filter_term'] - filter by category or tag ID; -1 allowed
				 */
				case 'mla_filter_term':
					$clean_request[ $key ] = intval( $value );
					break;
				case 'order':
					switch ( $value = strtoupper ($value ) ) {
						case 'ASC':
						case 'DESC':
							$clean_request[ $key ] = $value;
							break;
						default:
							$clean_request[ $key ] = 'ASC';
					}
					break;
				case 'detached':
					if ( ( '0' == $value ) || ( '1' == $value ) ) {
						$clean_request['detached'] = $value;
					}

					break;
				case 'status':
					if ( 'trash' == $value ) {
						$clean_request['post_status'] = 'trash';
					}

					break;
				/*
				 * ['s'] - Search Media by one or more keywords
				 * ['mla_search_connector'], ['mla_search_fields'] - Search Media options
				 */
				case 's':
					switch ( substr( $value, 0, 3 ) ) {
						case '>|<':
							$clean_request['debug'] = 'console';
							break;
						case '<|>':
							$clean_request['debug'] = 'log';
							break;
					}

					if ( isset( $clean_request['debug'] ) ) {
						$value = substr( $value, 3 );
					}

					$value = stripslashes( trim( $value ) );

					if ( ! empty( $value ) ) {
						$clean_request[ $key ] = $value;
					}

					break;
				case 'mla_terms_search':
					if ( ! empty( $value['phrases'] ) && ! empty( $value['taxonomies'] ) ) {
						$value['phrases'] = stripslashes( trim( $value['phrases'] ) );
						if ( ! empty( $value['phrases'] ) ) {
							$clean_request[ $key ] = $value;
						}
					}
					break;
				case 'mla_search_connector':
				case 'mla_search_fields':
					$clean_request[ $key ] = $value;
					break;
				case 'mla-metakey':
				case 'mla-metavalue':
					$clean_request[ $key ] = stripslashes( $value );
					break;
				case 'meta_query':
					if ( ! empty( $value ) ) {
						if ( is_array( $value ) ) {
							$clean_request[ $key ] = $value;
						} else {
							$clean_request[ $key ] = unserialize( stripslashes( $value ) );
							unset( $clean_request[ $key ]['slug'] );
						} // not array
					}

					break;
				default:
					// ignore anything else in $_REQUEST
			} // switch $key
		} // foreach $raw_request

		/*
		 * Pass query and search parameters to the filters for _execute_list_table_query
		 */
		self::$query_parameters = array( 'use_alt_text_view' => false, 'use_postmeta_view' => false, 'use_orderby_view' => false, 'orderby' => $clean_request['orderby'], 'order' => $clean_request['order'] );
		self::$query_parameters['detached'] = isset( $clean_request['detached'] ) ? $clean_request['detached'] : NULL;
		self::$search_parameters = array( 'debug' => 'none' );

		/*
		 * Matching a meta_value to NULL requires a LEFT JOIN to a view and a special WHERE clause
		 * Matching a wildcard pattern requires mainpulating the WHERE clause, too
		 */
		if ( isset( $clean_request['meta_query']['key'] ) ) {
			self::$query_parameters['use_postmeta_view'] = true;
			self::$query_parameters['postmeta_key'] = $clean_request['meta_query']['key'];
			self::$query_parameters['postmeta_value'] = NULL;
			unset( $clean_request['meta_query'] );
		} elseif ( isset( $clean_request['meta_query']['patterns'] ) ) {
			self::$query_parameters['patterns'] = $clean_request['meta_query']['patterns'];
			unset( $clean_request['meta_query']['patterns'] );
		}

		if ( isset( $clean_request['debug'] ) ) {
			self::$query_parameters['debug'] = $clean_request['debug'];
			self::$search_parameters['debug'] = $clean_request['debug'];
			MLA::mla_debug_mode( $clean_request['debug'] );
			unset( $clean_request['debug'] );
		}

		/*
		 * We must patch the WHERE clause if there are leading spaces in the meta_value
		 */
		if ( isset( $clean_request['mla-metavalue'] ) && ( 0 < strlen( $clean_request['mla-metavalue'] ) ) && ( ' ' == $clean_request['mla-metavalue'][0] ) ) {
			self::$query_parameters['mla-metavalue'] = $clean_request['mla-metavalue'];
		}

		/*
		 * We will handle "Terms Search" in the mla_query_posts_search_filter.
		 */
		if ( isset( $clean_request['mla_terms_search'] ) ) {
			self::$search_parameters['mla_terms_search'] = $clean_request['mla_terms_search'];

			/*
			 * The Terms Search overrides any terms-based keyword search for now; too complicated.
			 */
			if ( isset( $clean_request['mla_search_fields'] ) ) {
				foreach ( $clean_request['mla_search_fields'] as $index => $field ) {
					if ( 'terms' == $field ) {
						unset ( $clean_request['mla_search_fields'][ $index ] );
					}
				}
			}
		}

		/*
		 * We will handle keyword search in the mla_query_posts_search_filter.
		 */
		if ( isset( $clean_request['s'] ) ) {
			self::$search_parameters['s'] = $clean_request['s'];
			self::$search_parameters['mla_search_fields'] = apply_filters( 'mla_list_table_search_filter_fields', $clean_request['mla_search_fields'], array( 'content', 'title', 'excerpt', 'alt-text', 'name', 'terms' ) );
			self::$search_parameters['mla_search_connector'] = $clean_request['mla_search_connector'];
			self::$search_parameters['sentence'] = isset( $clean_request['sentence'] );
			self::$search_parameters['exact'] = isset( $clean_request['exact'] );

			if ( in_array( 'alt-text', self::$search_parameters['mla_search_fields'] ) ) {
				self::$query_parameters['use_alt_text_view'] = true;
			}

			if ( in_array( 'terms', self::$search_parameters['mla_search_fields'] ) ) {
				self::$search_parameters['mla_search_taxonomies'] = MLAOptions::mla_supported_taxonomies( 'term-search' );
			}

			unset( $clean_request['s'] );
			unset( $clean_request['mla_search_connector'] );
			unset( $clean_request['mla_search_fields'] );
			unset( $clean_request['sentence'] );
			unset( $clean_request['exact'] );
		}

		/*
		 * We have to handle custom field/post_meta values here
		 * because they need a JOIN clause supplied by WP_Query
		 */
		if ( 'c_' == substr( $clean_request['orderby'], 0, 2 ) ) {
			$option_value = MLAOptions::mla_custom_field_option_value( $clean_request['orderby'] );
			if ( isset( $option_value['name'] ) ) {
				self::$query_parameters['use_orderby_view'] = true;
				self::$query_parameters['postmeta_key'] = $option_value['name'];

				if ( isset($clean_request['orderby']) ) {
					unset($clean_request['orderby']);
				}

				if ( isset($clean_request['order']) ) {
					unset($clean_request['order']);
				}
			}
		} else { // custom field
			switch ( self::$query_parameters['orderby'] ) {
				/*
				 * '_wp_attachment_image_alt' is special; we'll handle it in the JOIN and ORDERBY filters
				 */
				case '_wp_attachment_image_alt':
					self::$query_parameters['use_orderby_view'] = true;
					if ( isset($clean_request['orderby']) ) {
						unset($clean_request['orderby']);
					}

					if ( isset($clean_request['order']) ) {
						unset($clean_request['order']);
					}

					break;
				case '_wp_attached_file':
					$clean_request['meta_key'] = '_wp_attached_file';
					$clean_request['orderby'] = 'meta_value';
					$clean_request['order'] = self::$query_parameters['order'];
					break;
			} // switch $orderby
		}

		/*
		 * Ignore incoming paged value; use offset and count instead
		 */
		if ( ( (int) $count ) > 0 ) {
			$clean_request['offset'] = $offset;
			$clean_request['posts_per_page'] = $count;
		} elseif ( ( (int) $count ) == -1 ) {
			$clean_request['posts_per_page'] = $count;
		}

		/*
		 * ['mla_filter_term'] - filter by taxonomy
		 *
		 * cat =  0 is "All Categories", i.e., no filtering
		 * cat = -1 is "No Categories"
		 */
		if ( isset( $clean_request['mla_filter_term'] ) ) {
			if ( $clean_request['mla_filter_term'] != 0 ) {
				$tax_filter = MLAOptions::mla_taxonomy_support('', 'filter');
				if ( $clean_request['mla_filter_term'] == -1 ) {
					$term_list = get_terms( $tax_filter, array(
						'fields' => 'ids',
						'hide_empty' => false
					) );
					$clean_request['tax_query'] = array(
						array(
							'taxonomy' => $tax_filter,
							'field' => 'id',
							'terms' => $term_list,
							'operator' => 'NOT IN' 
						) 
					);
				} else { // mla_filter_term == -1
					$clean_request['tax_query'] = array(
						array(
							'taxonomy' => $tax_filter,
							'field' => 'id',
							'terms' => array(
								(int) $clean_request['mla_filter_term']
							),
							'include_children' => ( 'checked' == MLAOptions::mla_get_option( MLAOptions::MLA_TAXONOMY_FILTER_INCLUDE_CHILDREN ) )
						) 
					);
				} // mla_filter_term != -1
			} // mla_filter_term != 0

			unset( $clean_request['mla_filter_term'] );
		} // isset mla_filter_term

		if ( isset( $clean_request['mla-tax'] ) && isset( $clean_request['mla-term'] )) {
			$clean_request['tax_query'] = array(
				array(
					'taxonomy' => $clean_request['mla-tax'],
					'field' => 'slug',
					'terms' => $clean_request['mla-term'],
					'include_children' => false 
				) 
			);

			unset( $clean_request['mla-tax'] );
			unset( $clean_request['mla-term'] );
		} // isset mla_tax

		if ( isset( $clean_request['mla-metakey'] ) && isset( $clean_request['mla-metavalue'] ) ) {
			$clean_request['meta_key'] = $clean_request['mla-metakey'];
			$clean_request['meta_value'] = $clean_request['mla-metavalue'];

			unset( $clean_request['mla-metakey'] );
			unset( $clean_request['mla-metavalue'] );
		} // isset mla_tax

		return $clean_request;
	}

	/**
	 * Add filters, run query, remove filters
	 *
	 * @since 0.30
	 *
	 * @param	array	query parameters from web page, usually found in $_REQUEST
	 *
	 * @return	object	WP_Query object with query results
	 */
	private static function _execute_list_table_query( $request ) {
		global $wpdb;

		/*
		 * ALT Text searches, custom field Table Views and custom field sorts are
		 * special; we have to use an SQL VIEW to build an intermediate table and
		 * modify the JOIN to include posts with no value for the metadata field.
		 */
		if ( self::$query_parameters['use_alt_text_view'] ) {
			$alt_text_view_name = self::$mla_alt_text_view;
			$key_name = '_wp_attachment_image_alt';
			$table_name = $wpdb->postmeta;

			$result = $wpdb->query(
					"
					CREATE OR REPLACE VIEW {$alt_text_view_name} AS
					SELECT post_id, meta_value
					FROM {$table_name}
					WHERE {$table_name}.meta_key = '{$key_name}'
					"
			);
		}

		if ( self::$query_parameters['use_postmeta_view'] ) {
			$postmeta_view_name = self::$mla_table_view_custom;
			$key_name = self::$query_parameters['postmeta_key'];
			$table_name = $wpdb->postmeta;

			$result = $wpdb->query(
					"
					CREATE OR REPLACE VIEW {$postmeta_view_name} AS
					SELECT post_id, meta_value
					FROM {$table_name}
					WHERE {$table_name}.meta_key = '{$key_name}'
					"
			);
		}

		if ( self::$query_parameters['use_orderby_view'] ) {
			$orderby_view_name = self::$mla_orderby_view;
			$key_name = self::$query_parameters['postmeta_key'];
			$table_name = $wpdb->postmeta;

			$result = $wpdb->query(
					"
					CREATE OR REPLACE VIEW {$orderby_view_name} AS
					SELECT post_id, meta_value
					FROM {$table_name}
					WHERE {$table_name}.meta_key = '{$key_name}'
					"
			);
		}

		add_filter( 'posts_search', 'MLAData::mla_query_posts_search_filter', 10, 2 ); // $search, &$this
		add_filter( 'posts_where', 'MLAData::mla_query_posts_where_filter' );
		add_filter( 'posts_join', 'MLAData::mla_query_posts_join_filter' );
		add_filter( 'posts_groupby', 'MLAData::mla_query_posts_groupby_filter' );
		add_filter( 'posts_orderby', 'MLAData::mla_query_posts_orderby_filter' );

		/*
		 * Disable Relevanssi - A Better Search, v3.2 by Mikko Saari 
		 * relevanssi_prevent_default_request( $request, $query )
		 * apply_filters('relevanssi_admin_search_ok', $admin_search_ok, $query );
		 */
		if ( function_exists( 'relevanssi_prevent_default_request' ) ) {
			add_filter( 'relevanssi_admin_search_ok', 'MLAData::mla_query_relevanssi_admin_search_ok_filter' );
		}

		if ( isset( self::$query_parameters['debug'] ) ) {
			global $wp_filter;
			$debug_array = array( 'posts_search' => $wp_filter['posts_search'], 'posts_join' => $wp_filter['posts_join'], 'posts_where' => $wp_filter['posts_where'], 'posts_orderby' => $wp_filter['posts_orderby'] );

			/* translators: 1: DEBUG tag 2: query filter details */
			MLA::mla_debug_add( sprintf( _x( '%1$s: _execute_list_table_query $wp_filter = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'DEBUG', 'media-library-assistant' ), var_export( $debug_array, true ) ) );

			add_filter( 'posts_clauses', 'MLAData::mla_query_posts_clauses_filter', 0x7FFFFFFF, 1 );
			add_filter( 'posts_clauses_request', 'MLAData::mla_query_posts_clauses_request_filter', 0x7FFFFFFF, 1 );
		} // debug

		$results = new WP_Query( $request );

		if ( isset( self::$query_parameters['debug'] ) ) {
			remove_filter( 'posts_clauses', 'MLAData::mla_query_posts_clauses_filter', 0x7FFFFFFF );
			remove_filter( 'posts_clauses_request', 'MLAData::mla_query_posts_clauses_request_filter', 0x7FFFFFFF );

			$debug_array = array( 'request' => $request, 'query_parameters' => self::$query_parameters, 'post_count' => $results->post_count, 'found_posts' => $results->found_posts );

			/* translators: 1: DEBUG tag 2: query details */
			MLA::mla_debug_add( sprintf( _x( '%1$s: _execute_list_table_query WP_Query = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'DEBUG', 'media-library-assistant' ), var_export( $debug_array, true ) ) );
			/* translators: 1: DEBUG tag 2: SQL statement */
			MLA::mla_debug_add( sprintf( _x( '%1$s: _execute_list_table_query SQL_request = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'DEBUG', 'media-library-assistant' ), var_export( $results->request, true ) ) );
		} // debug

		if ( function_exists( 'relevanssi_prevent_default_request' ) ) {
			remove_filter( 'relevanssi_admin_search_ok', 'MLAData::mla_query_relevanssi_admin_search_ok_filter' );
		}

		remove_filter( 'posts_orderby', 'MLAData::mla_query_posts_orderby_filter' );
		remove_filter( 'posts_groupby', 'MLAData::mla_query_posts_groupby_filter' );
		remove_filter( 'posts_join', 'MLAData::mla_query_posts_join_filter' );
		remove_filter( 'posts_where', 'MLAData::mla_query_posts_where_filter' );
		remove_filter( 'posts_search', 'MLAData::mla_query_posts_search_filter' );

		if ( self::$query_parameters['use_alt_text_view'] ) {
			$result = $wpdb->query( "DROP VIEW {$alt_text_view_name}" );
		}

		if ( self::$query_parameters['use_postmeta_view'] ) {
			$result = $wpdb->query( "DROP VIEW {$postmeta_view_name}" );
		}

		if ( self::$query_parameters['use_orderby_view'] ) {
			$result = $wpdb->query( "DROP VIEW {$orderby_view_name}" );
		}

		return $results;
	}

	/**
	 * Detects wildcard searches, i.e., containing an asterisk outside quotes
	 * 
	 * Defined as public because it's a callback from array_map().
	 *
	 * @since 2.13
	 *
	 * @param	string	search string
	 *
	 * @return	boolean	true if wildcard
	 */
	private static function _wildcard_search_string( $search_string ) {
		preg_match_all('/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $search_string, $matches);

		if ( is_array( $matches ) ) {
			foreach ( $matches[0] as $term ) {
				if ( '"' == substr( $term, 0, 1) ) {
					continue;
				}

				if ( false !== strpos( $term, '*' ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Replaces a WordPress function deprecated in v3.7
	 * 
	 * Defined as public because it's a callback from array_map().
	 *
	 * @since 1.51
	 *
	 * @param	string	search term before modification
	 *
	 * @return	string	cleaned up search term
	 */
	public static function mla_search_terms_tidy( $term ) {
		return trim( $term, "\"'\n\r " );
	}

	/**
	 * Isolates keyword match results to word boundaries
	 * 
	 * Eliminates matches such as "man" in "woman".
	 *
	 * @since 2.11
	 *
	 * @param	string	the quoted phrase (without enclosing quotes)
	 * @param	string	the entire term
	 *
	 * @return	boolean	$needle is a word match within $haystack
	 */
	private static function _match_quoted_phrase( $needle, $haystack ) {
		$haystack = strtolower( html_entity_decode( $haystack ) );
		$needle = strtolower( html_entity_decode( $needle ) );

		// Escape the PCRE meta-characters
		$safe_needle = '';
		for ( $index = 0; $index < strlen( $needle ); $index++ ) {
			$chr = $needle[ $index ];
			if ( false !== strpos( '\\^$.[]()?*+{}/', $chr ) ) {
				$safe_needle .= '\\';
			}
			$safe_needle .= $chr;
		}

		$pattern = '/^' . $safe_needle . '$|^' . $safe_needle . '\s+|\s+' . $safe_needle . '\s+|\s+' . $safe_needle . '$/';
		$match_count = preg_match_all($pattern, $haystack, $matches);
		return 0 < $match_count;
	}

	/**
	 * Adds a keyword search to the WHERE clause, if required
	 * 
	 * Defined as public because it's a filter.
	 *
	 * @since 0.60
	 *
	 * @param	string	query clause before modification
	 * @param	object	WP_Query object
	 *
	 * @return	string	query clause after keyword search addition
	 */
	public static function mla_query_posts_search_filter( $search_string, &$query_object ) {
		global $wpdb;

		$numeric_clause = '';
		$search_clause = '';
		$tax_clause = '';
		$tax_connector = 'AND';
		$tax_index = 0;

		/*
		 * Process the Terms Search arguments, if present.
		 */
		if ( isset( self::$search_parameters['mla_terms_search']['phrases'] ) ) {
			$terms_search_parameters = self::$search_parameters['mla_terms_search'];
			$terms = array_map( 'trim', explode( ',', $terms_search_parameters['phrases'] ) );
			if ( 1 < count( $terms ) ) {
				$terms_connector = '(';			
			} else {
				$terms_connector = '';			
			}

			foreach ( $terms as $term ) {
				preg_match_all('/".*?("|$)|\'.*?(\'|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $term, $matches);
				$phrases = array_map('MLAData::mla_search_terms_tidy', $matches[0]);

				/*
				 * Find the quoted phrases for a word-boundary check
				 */
				$quoted = array();
				foreach ( $phrases as $index => $phrase ) {
					$quoted[ $index ] = ( '"' == $matches[1][$index] ) || ( "'" == $matches[2][$index] );
				}

				$tax_terms = array();
				$tax_counts = array();
				foreach ( $phrases as $index => $phrase ) {
					if ( isset( $terms_search_parameters['exact'] ) ) {
						$the_terms = array();
						foreach( $terms_search_parameters['taxonomies'] as $taxonomy ) {
							// WordPress encodes special characters, e.g., "&" as HTML entities in term names
							$the_term = get_term_by( 'name', _wp_specialchars( $phrase ), $taxonomy );
							if ( false !== $the_term ) {
								$the_terms[] = $the_term;
							}
						}
					} else {
						$is_wildcard_search = ( ! $quoted[ $index ] ) && self::_wildcard_search_string( $phrase );

						if ( $is_wildcard_search ) {
							add_filter( 'terms_clauses', 'MLAData::mla_query_terms_clauses_filter', 0x7FFFFFFF, 3 );
						}

						// WordPress encodes special characters, e.g., "&" as HTML entities in term names
						$the_terms = get_terms( $terms_search_parameters['taxonomies'], array( 'name__like' => _wp_specialchars( $phrase ), 'fields' => 'all', 'hide_empty' => false ) );

						if ( $is_wildcard_search ) {
							remove_filter( 'terms_clauses', 'MLAData::mla_query_terms_clauses_filter', 0x7FFFFFFF );
						}

						// Invalid taxonomy will return WP_Error object
						if ( ! is_array( $the_terms ) ) {
							$the_terms = array();
						}

						if ( $quoted[ $index ] ) {
							foreach ( $the_terms as $term_index => $the_term ) {
								if ( ! self::_match_quoted_phrase( $phrase, $the_term->name ) ) {
									unset( $the_terms[ $term_index ]);
								}
							}
						} // quoted phrase
					} // not exact

					foreach( $the_terms as $the_term ) {
						$tax_terms[ $the_term->taxonomy ][ $the_term->term_id ] = (integer) $the_term->term_taxonomy_id;

						if ( isset( $tax_counts[ $the_term->taxonomy ][ $the_term->term_id ] ) ) {
							$tax_counts[ $the_term->taxonomy ][ $the_term->term_id ]++;
						} else {
							$tax_counts[ $the_term->taxonomy ][ $the_term->term_id ] = 1;
						}
					}
				} // foreach phrase

				/*
				 * For the AND connector, a taxonomy term must have all of the search terms within it
				 */
				if ( 'AND' == $terms_search_parameters['radio_phrases'] ) {
					$search_term_count = count( $phrases );
					foreach ($tax_terms as $taxonomy => $term_ids ) {
						foreach ( $term_ids as $term_id => $term_taxonomy_id ) {
							if ( $search_term_count != $tax_counts[ $taxonomy ][ $term_id ] ) {
								unset( $term_ids[ $term_id ] );
							}
						}

						if ( empty( $term_ids ) ) {
							unset( $tax_terms[ $taxonomy ] );
						} else {
							$tax_terms[ $taxonomy ] = $term_ids;
						}
					} // foreach taxonomy
				} // AND (i.e., All phrases)

				if ( ! empty( $tax_terms ) ) {
					$inner_connector = '';

					$tax_clause .= $terms_connector;
					foreach( $tax_terms as $tax_term ) {
						if ( 'AND' == $terms_search_parameters['radio_terms'] ) {
							$prefix = 'mlatt' . $tax_index++;
						} else {
							$prefix = 'mlatt0';
							$tax_index = 1; // only one JOIN needed for the "Any Term" case
						}

						$tax_clause .= sprintf( '%1$s %2$s.term_taxonomy_id IN (%3$s)', $inner_connector, $prefix, implode( ',', $tax_term ) );
						$inner_connector = ' OR';
					} // foreach tax_term

					$terms_connector = ' ) ' . $terms_search_parameters['radio_terms'] . ' (';
				} // tax_terms present
			} // foreach term

			if ( 1 < count( $terms ) && ! empty( $tax_clause ) ) {
				$tax_clause .= ')';
			}

			if ( empty( $tax_clause ) ) {
				$tax_clause = '1=0';
			} else {
				self::$search_parameters['tax_terms_count'] = $tax_index;
			};
		} // isset mla_terms_search

		/*
		 * Process the keyword search argument, if present.
		 */
		if ( isset( self::$search_parameters['s'] ) ) {

			// WordPress v3.7 says: there are no line breaks in <input /> fields
			$keyword_string = stripslashes( str_replace( array( "\r", "\n" ), '', self::$search_parameters['s'] ) );
			$is_wildcard_search = self::_wildcard_search_string( $keyword_string );

			if ( $is_wildcard_search || self::$search_parameters['sentence'] || self::$search_parameters['exact'] ) {
				$keyword_array = array( $keyword_string );
			} else {
				// v3.6.1 was '/".*?("|$)|((?<=[\r\n\t ",+])|^)[^\r\n\t ",+]+/'
				preg_match_all('/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $keyword_string, $matches);
				$keyword_array = array_map( 'MLAData::mla_search_terms_tidy', $matches[0]);
				$numeric_array = array_filter( $keyword_array, 'is_numeric' );

				/*
				 * If all the "keywords" are numeric, interpret it/them as the ID(s) of a specific attachment
				 * or the ID(s) of a parent post/page; add it/them to the regular text-based search.
				 */
				if ( count( $keyword_array ) == count( $numeric_array ) ) {
					$numeric_array = implode( ',', $numeric_array );
					$numeric_clause = '( ( ' . $wpdb->posts . '.ID IN (' . $numeric_array . ') ) OR ( ' . $wpdb->posts . '.post_parent IN (' . $numeric_array . ') ) ) OR ';

				}
			}

			$fields = self::$search_parameters['mla_search_fields'];
			$allow_terms_search = in_array( 'terms', $fields ) && ( ! $is_wildcard_search );
			$percent = self::$search_parameters['exact'] ? '' : '%';
			$connector = '';

			if ( empty( $fields ) ) {
				$search_clause = '1=0';
			} else {
				$tax_terms = array();
				$tax_counts = array();
				foreach ( $keyword_array as $term ) {
					if ( $is_wildcard_search ) {
						/*
						 * Escape any % in the source string
						 */
						if ( self::$wp_4dot0_plus ) {
							$sql_term = $wpdb->esc_like( $term );
							$sql_term = $wpdb->prepare( '%s', $sql_term );
						} else {
							$sql_term = "'" . esc_sql( like_escape( $term ) ) . "'";
						}

						/*
						 * Convert wildcard * to SQL %
						 */
						$sql_term = str_replace( '*', '%', $sql_term );
					} else {
						if ( self::$wp_4dot0_plus ) {
							$sql_term = $percent . $wpdb->esc_like( $term ) . $percent;
							$sql_term = $wpdb->prepare( '%s', $sql_term );
						} else {
							$sql_term = "'" . $percent . esc_sql( like_escape( $term ) ) . $percent . "'";
						}
					}

					$inner_connector = '';
					$inner_clause = '';

					if ( in_array( 'content', $fields ) ) {
						$inner_clause .= "{$inner_connector}({$wpdb->posts}.post_content LIKE {$sql_term})";
						$inner_connector = ' OR ';
					}

					if ( in_array( 'title', $fields ) ) {
						$inner_clause .= "{$inner_connector}({$wpdb->posts}.post_title LIKE {$sql_term})";
						$inner_connector = ' OR ';
					}

					if ( in_array( 'excerpt', $fields ) ) {
						$inner_clause .= "{$inner_connector}({$wpdb->posts}.post_excerpt LIKE {$sql_term})";
						$inner_connector = ' OR ';
					}

					if ( in_array( 'alt-text', $fields ) ) {
						$view_name = self::$mla_alt_text_view;
						$inner_clause .= "{$inner_connector}({$view_name}.meta_value LIKE {$sql_term})";
						$inner_connector = ' OR ';
					}

					if ( in_array( 'name', $fields ) ) {
						$inner_clause .= "{$inner_connector}({$wpdb->posts}.post_name LIKE {$sql_term})";
					}

					$inner_clause = apply_filters( 'mla_list_table_search_filter_inner_clause', $inner_clause, $inner_connector, $wpdb->posts, $sql_term );

					if ( ! empty($inner_clause) ) {
						$search_clause .= "{$connector}({$inner_clause})";
						$connector = ' ' . self::$search_parameters['mla_search_connector'] . ' ';
					}

					/*
					 * Convert search term text to term_taxonomy_id value(s),
					 * separated by taxonomy.
					 */
					if ( $allow_terms_search ) {
						// WordPress encodes special characters, e.g., "&" as HTML entities in term names
						$the_terms = get_terms( self::$search_parameters['mla_search_taxonomies'], array( 'name__like' => _wp_specialchars( $term ), 'fields' => 'all', 'hide_empty' => false ) );
						// Invalid taxonomy will return WP_Error object
						if ( ! is_array( $the_terms ) ) {
							$the_terms = array();
						}

						foreach( $the_terms as $the_term ) {
							$tax_terms[ $the_term->taxonomy ][ $the_term->term_id ] = (integer) $the_term->term_taxonomy_id;

							if ( isset( $tax_counts[ $the_term->taxonomy ][ $the_term->term_id ] ) ) {
								$tax_counts[ $the_term->taxonomy ][ $the_term->term_id ]++;
							} else {
								$tax_counts[ $the_term->taxonomy ][ $the_term->term_id ] = 1;
							}
						}
					} // in_array terms
				} // foreach term

				if ( $allow_terms_search ) {
					/*
					 * For the AND connector, a taxonomy term must have all of the search terms within it
					 */
					if ( 'AND' == self::$search_parameters['mla_search_connector'] ) {
						$search_term_count = count( $keyword_array );
						foreach ($tax_terms as $taxonomy => $term_ids ) {
							foreach ( $term_ids as $term_id => $term_taxonomy_id ) {
								if ( $search_term_count != $tax_counts[ $taxonomy ][ $term_id ] ) {
									unset( $term_ids[ $term_id ] );
								}
							}

							if ( empty( $term_ids ) ) {
								unset( $tax_terms[ $taxonomy ] );
							} else {
								$tax_terms[ $taxonomy ] = $term_ids;
							}
						} // foreach taxonomy
					} // AND connector

					if ( empty( $tax_terms ) ) {
						/*
						 * If "Terms" is the only field and no terms are present,
						 * the search must fail.
						 */
						if ( ( 1 == count( $fields ) ) && ( 'terms' == array_shift( $fields ) ) ) {
							$tax_clause = '1=0';
						}
					} else {
						$tax_index = 0;
						$inner_connector = '';

						foreach( $tax_terms as $tax_term ) {
							$prefix = 'mlatt' . $tax_index++;
							$tax_clause .= sprintf( '%1$s %2$s.term_taxonomy_id IN (%3$s)', $inner_connector, $prefix, implode( ',', $tax_term ) );
							$inner_connector = ' OR';
						} // foreach tax_term

						self::$search_parameters['tax_terms_count'] = $tax_index;
						$tax_connector = 'OR';
					} // tax_terms present
				} // terms in fields
			} // fields not empty
		} // isset 's'

		if ( ! empty( $tax_clause ) && ! empty( $search_clause ) ) {
			$tax_clause = " {$tax_connector} ({$tax_clause} )";
		}

		if ( ! empty( $search_clause ) || ! empty( $tax_clause ) ) {
			$search_clause = " AND ( {$numeric_clause}{$search_clause}{$tax_clause} ) ";

			if ( ! is_user_logged_in() ) {
				$search_clause .= " AND ( {$wpdb->posts}.post_password = '' ) ";
			}
		}

		if ( 'none' != self::$search_parameters['debug'] ) {
			$debug_array['search_string'] = $search_string;
			$debug_array['search_parameters'] = self::$search_parameters;
			$debug_array['search_clause'] = $search_clause;

			if ( 'shortcode' == self::$search_parameters['debug'] ) {
				MLA::mla_debug_add( '<strong>mla_debug posts_search filter</strong> = ' . var_export( $debug_array, true ) );
			} else {
				/* translators: 1: DEBUG tag 2: search filter details */
				MLA::mla_debug_add( sprintf( _x( '%1$s: mla_query_posts_search_filter = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'DEBUG', 'media-library-assistant' ), var_export( $debug_array, true ) ) );
			}
		} // debug

		return $search_clause;
	}

	/**
	 * Adds/modifies the WHERE clause for meta values, LIKE patterns and detached items
	 * 
	 * Modeled after _edit_attachments_query_helper in wp-admin/post.php.
	 * Defined as public because it's a filter.
	 *
	 * @since 0.1
	 *
	 * @param	string	query clause before modification
	 *
	 * @return	string	query clause after modification
	 */
	public static function mla_query_posts_where_filter( $where_clause ) {
		global $wpdb;

		if ( isset( self::$query_parameters['debug'] ) ) {
			$debug_array = array( 'where_string' => $where_clause );
		}

		/*
		 * WordPress filters meta_value thru trim() - which we must reverse
		 */
		if ( isset( self::$query_parameters['mla-metavalue'] ) ) {
			if ( is_array( self::$query_parameters['mla-metavalue'] ) ) {
				foreach ( self::$query_parameters['mla-metavalue'] as $pattern => $replacement ) {
					$where_clause = preg_replace( '/(^.*meta_value AS CHAR\) = \')(' . $pattern . '[^\']*)/m', '${1}' . $replacement, $where_clause );
				}
			} else {
				$where_clause = preg_replace( '/(^.*meta_value AS CHAR\) = \')([^\']*)/m', '${1}' . self::$query_parameters['mla-metavalue'], $where_clause );
			}
		}

		/*
		 * Matching a NULL meta value 
		 */
		if ( array_key_exists( 'postmeta_value', self::$query_parameters ) && NULL == self::$query_parameters['postmeta_value'] ) {
			$where_clause .= ' AND ' . self::$mla_table_view_custom . '.meta_value IS NULL';
		}

		/*
		 * WordPress modifies the LIKE clause - which we must reverse
		 */
		if ( isset( self::$query_parameters['patterns'] ) ) {
			foreach ( self::$query_parameters['patterns'] as $pattern ) {
				$pattern = str_replace( '_', '\\\\_', $pattern );
				$match_clause = '%' . str_replace( '%', '\\\\%', $pattern ) . '%';
				$where_clause = str_replace( "LIKE '{$match_clause}'", "LIKE '{$pattern}'", $where_clause );
			}
		}

		/*
		 * Unattached items require some help
		 */
		if ( isset( self::$query_parameters['detached'] ) ) {
			if ( '1' == self::$query_parameters['detached'] ) {
				$where_clause .= sprintf( ' AND %1$s.post_parent < 1', $wpdb->posts );
			} elseif ( '0' == self::$query_parameters['detached'] ) {
				$where_clause .= sprintf( ' AND %1$s.post_parent > 0', $wpdb->posts );
			}
		}

		if ( isset( self::$query_parameters['debug'] ) ) {
			$debug_array['where_clause'] = $where_clause;

			/* translators: 1: DEBUG tag 2: where filter details */
			MLA::mla_debug_add( sprintf( _x( '%1$s: mla_query_posts_where_filter = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'DEBUG', 'media-library-assistant' ), var_export( $debug_array, true ) ) );
		} // debug

		return $where_clause;
	}

	/**
	 * Adds a JOIN clause, if required, to handle sorting/searching on custom fields or ALT Text
	 * 
	 * Defined as public because it's a filter.
	 *
	 * @since 0.30
	 *
	 * @param	string	query clause before modification
	 *
	 * @return	string	query clause after "LEFT JOIN view ON post_id" item modification
	 */
	public static function mla_query_posts_join_filter( $join_clause ) {
		global $wpdb;

		if ( isset( self::$query_parameters['debug'] ) ) {
			$debug_array = array( 'join_string' => $join_clause );
		}

		/*
		 * ALT Text searches, custom field Table Views and custom field sorts are
		 * special; we have to use an SQL VIEW to build an intermediate table and
		 * modify the JOIN to include posts with no value for this metadata field.
		 */
		if ( self::$query_parameters['use_alt_text_view'] ) {
			$join_clause .= sprintf( ' LEFT JOIN %1$s ON (%2$s.ID = %1$s.post_id)', self::$mla_alt_text_view, $wpdb->posts );
		}

		if ( self::$query_parameters['use_postmeta_view'] ) {
			$join_clause .= sprintf( ' LEFT JOIN %1$s ON (%2$s.ID = %1$s.post_id)', self::$mla_table_view_custom, $wpdb->posts );
		}

		if ( self::$query_parameters['use_orderby_view'] ) {
			$join_clause .= sprintf( ' LEFT JOIN %1$s ON (%2$s.ID = %1$s.post_id)', self::$mla_orderby_view, $wpdb->posts );
		}

		/*
		 * custom field sorts are special; we have to use an SQL VIEW to
		 * build an intermediate table and modify the JOIN to include posts with
		 * no value for this metadata field.
		 */
		if ( isset( self::$query_parameters['orderby'] ) ) {
			if ( ( 'c_' == substr( self::$query_parameters['orderby'], 0, 2 ) ) || ( '_wp_attachment_image_alt' == self::$query_parameters['orderby'] ) ) {
				$orderby = self::$mla_orderby_view . '.meta_value';
			}
		}

		if ( isset( self::$search_parameters['tax_terms_count'] ) ) {
			$tax_index = 0;
			$tax_clause = '';

			while ( $tax_index < self::$search_parameters['tax_terms_count'] ) {
				$prefix = 'mlatt' . $tax_index++;
				$tax_clause .= sprintf( ' LEFT JOIN %1$s AS %2$s ON (%3$s.ID = %2$s.object_id)', $wpdb->term_relationships, $prefix, $wpdb->posts );
			}

			$join_clause .= $tax_clause;
		}

		if ( isset( self::$query_parameters['debug'] ) ) {
			$debug_array['join_clause'] = $join_clause;

			/* translators: 1: DEBUG tag 2: join filter details */
			MLA::mla_debug_add( sprintf( _x( '%1$s: mla_query_posts_join_filter = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'DEBUG', 'media-library-assistant' ), var_export( $debug_array, true ) ) );
		} // debug

		return $join_clause;
	}

	/**
	 * Adds a GROUPBY clause, if required
	 * 
	 * Taxonomy text queries and postmeta queries can return multiple results for the same ID.
	 * Defined as public because it's a filter.
	 *
	 * @since 1.90
	 *
	 * @param	string	query clause before modification
	 *
	 * @return	string	updated query clause
	 */
	public static function mla_query_posts_groupby_filter( $groupby_clause ) {
		global $wpdb;

//		if ( ( isset( self::$query_parameters['use_postmeta_view'] ) && self::$query_parameters['use_postmeta_view'] ) || ( isset( self::$query_parameters['use_alt_text_view'] ) && self::$query_parameters['use_alt_text_view'] ) || isset( self::$search_parameters['tax_terms_count'] ) ) {
		if ( ( ! empty( self::$query_parameters['use_postmeta_view'] ) ) || ( ! empty( self::$query_parameters['use_alt_text_view'] ) ) || ( ! empty( self::$query_parameters['use_orderby_view'] ) ) || isset( self::$search_parameters['tax_terms_count'] ) ) {
			$groupby_clause = "{$wpdb->posts}.ID";
		}

		return $groupby_clause;
	}

	/**
	 * Adds a ORDERBY clause, if required
	 * 
	 * Expands the range of sort options because the logic in WP_Query is limited.
	 * Defined as public because it's a filter.
	 *
	 * @since 0.30
	 *
	 * @param	string	query clause before modification
	 *
	 * @return	string	updated query clause
	 */
	public static function mla_query_posts_orderby_filter( $orderby_clause ) {
		global $wpdb;

		if ( isset( self::$query_parameters['debug'] ) ) {
			$debug_array = array( 'orderby_string' => $orderby_clause );
		}

		if ( isset( self::$query_parameters['orderby'] ) ) {
			if ( 'c_' == substr( self::$query_parameters['orderby'], 0, 2 ) ) {
				$orderby = self::$mla_orderby_view . '.meta_value';
			} /* custom field sort */ else { 
				switch ( self::$query_parameters['orderby'] ) {
					case 'none':
						$orderby = '';
						$orderby_clause = '';
						break;
					/*
					 * post__in is passed from Media Manager Modal Window
					 */
					case 'post__in':
						return $orderby_clause;
					/*
					 * There are two columns defined that end up sorting on post_title,
					 * so we can't use the database column to identify the column but
					 * we actually sort on the database column.
					 */
					case 'title_name':
						$orderby = $wpdb->posts . '.post_title';
						break;
					/*
					 * The _wp_attached_file meta data value is present for all attachments, and the
					 * sorting on the meta data value is handled by WP_Query
					 */
					case '_wp_attached_file':
						$orderby = '';
						break;
					/*
					 * The _wp_attachment_image_alt value is only present for images, so we have to
					 * use the view we prepared to get attachments with no meta data value
					 */
					case '_wp_attachment_image_alt':
						$orderby = self::$mla_orderby_view . '.meta_value';
						break;
					default:
						$orderby = $wpdb->posts . '.' . self::$query_parameters['orderby'];
				} // $query_parameters['orderby']
			}

			if ( ! empty( $orderby ) ) {
				$orderby_clause = $orderby . ' ' . self::$query_parameters['order'];
			}
		} // isset

		if ( isset( self::$query_parameters['debug'] ) ) {
			$debug_array['orderby_clause'] = $orderby_clause;

			/* translators: 1: DEBUG tag 2: orderby details details */
			MLA::mla_debug_add( sprintf( _x( '%1$s: mla_query_posts_orderby_filter = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'DEBUG', 'media-library-assistant' ), var_export( $debug_array, true ) ) );
		} // debug

		return $orderby_clause;
	}

	/**
	 * Disable Relevanssi - A Better Search, v3.2 by Mikko Saari
	 * Defined as public because it's a filter.
	 *
	 * @since 1.80
	 *
	 * @param	boolean	Default setting
	 *
	 * @return	boolean	Updated setting
	 */
	public static function mla_query_relevanssi_admin_search_ok_filter( $admin_search_ok ) {
		return false;
	}

	/**
	 * Filters all clauses for get_terms queries
	 * 
	 * Defined as public because it's a filter.
	 *
	 * @since 2.13
	 *
	 * @param array $pieces     Terms query SQL clauses.
	 * @param array $taxonomies An array of taxonomies.
	 * @param array $args       An array of terms query arguments.
	 */
	public static function mla_query_terms_clauses_filter( $pieces, $taxonomies, $args ) {
		global $wpdb;

		if ( empty( $args['name__like'] ) ) {
			return $pieces;
		}

		$term = $args['name__like'];

		/*
		 * Escape any % in the source string
		 */
		if ( self::$wp_4dot0_plus ) {
			$sql_term = $wpdb->esc_like( $term );
			$sql_term = $wpdb->prepare( '%s', $sql_term );
		} else {
			$sql_term = "'" . esc_sql( like_escape( $term ) ) . "'";
		}

		/*
		 * Convert wildcard * to SQL %
		 */
		$sql_term = str_replace( '*', '%', $sql_term );

		/*
		 * Replace the LIKE pattern in the WHERE clause
		 */
		$match_clause = '%' . str_replace( '%', '\\\\%', $term ) . '%';
		$pieces['where'] = str_replace( "LIKE '{$match_clause}'", "LIKE {$sql_term}", $pieces['where'] );

		return $pieces;
	}

	/**
	 * Filters all clauses for shortcode queries, pre caching plugins
	 * 
	 * This is for debug purposes only.
	 * Defined as public because it's a filter.
	 *
	 * @since 1.80
	 *
	 * @param	array	query clauses before modification
	 *
	 * @return	array	query clauses after modification (none)
	 */
	public static function mla_query_posts_clauses_filter( $pieces ) {
		/* translators: 1: DEBUG tag 2: SQL clauses */
		MLA::mla_debug_add( sprintf( _x( '%1$s: mla_query_posts_clauses_filter = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'DEBUG', 'media-library-assistant' ), var_export( $pieces, true ) ) );

		return $pieces;
	}

	/**
	 * Filters all clauses for shortcode queries, post caching plugins
	 * 
	 * This is for debug purposes only.
	 * Defined as public because it's a filter.
	 *
	 * @since 1.80
	 *
	 * @param	array	query clauses before modification
	 *
	 * @return	array	query clauses after modification (none)
	 */
	public static function mla_query_posts_clauses_request_filter( $pieces ) {
		/* translators: 1: DEBUG tag 2: SQL clauses */
		MLA::mla_debug_add( sprintf( _x( '%1$s: mla_query_posts_clauses_request_filter = "%2$s".', 'error_log', 'media-library-assistant' ), __( 'DEBUG', 'media-library-assistant' ), var_export( $pieces, true ) ) );

		return $pieces;
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
			error_log( sprintf( _x( '%1$s: mla_get_attachment_by_id(%2$d) not found.', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $post_id ), 0 );
			return NULL;
		}

		if ( $item->post_type != 'attachment' ) {
			/* translators: 1: ERROR tag 2: post ID 3: post_type */
			error_log( sprintf( _x( '%1$s: mla_get_attachment_by_id(%2$d) wrong post_type "%3$s".', 'error_log', 'media-library-assistant' ), __( 'ERROR', 'media-library-assistant' ), $post_id, $item->post_type ), 0 );
			return NULL;
		}

		$post_data = (array) $item;
		$post = $item;
		setup_postdata( $item );

		/*
		 * Add parent data
		 */
		$post_data = array_merge( $post_data, self::mla_fetch_attachment_parent_data( $post_data['post_parent'] ) );

		/*
		 * Add meta data
		 */
		$post_data = array_merge( $post_data, self::mla_fetch_attachment_metadata( $post_id ) );

		/*
		 * Add references, if requested, or "empty" references array
		 */
		$post_data['mla_references'] = self::mla_fetch_attachment_references( $post_id, $post_data['post_parent'], $add_references );

		$save_id = $post_id;
		return $post_data;
	}

	/**
	 * Returns information about an attachment's parent, if found
	 *
	 * @since 0.1
	 *
	 * @param	int		post ID of attachment's parent, if any
	 *
	 * @return	array	Parent information; post_date, post_title and post_type
	 */
	public static function mla_fetch_attachment_parent_data( $parent_id ) {
		static $save_id = -1, $parent_data;

		if ( $save_id == $parent_id ) {
			return $parent_data;
		} elseif ( $parent_id == -1 ) {
			$save_id = -1;
			return NULL;
		}

		$parent_data = array();
		if ( $parent_id ) {
			$parent = get_post( $parent_id );

			if ( isset( $parent->post_name ) ) {
				$parent_data['parent_name'] = $parent->post_name;
			}

			if ( isset( $parent->post_type ) ) {
				$parent_data['parent_type'] = $parent->post_type;
			}

			if ( isset( $parent->post_title ) ) {
				$parent_data['parent_title'] = $parent->post_title;
			}

			if ( isset( $parent->post_date ) ) {
				$parent_data['parent_date'] = $parent->post_date;
			}

			if ( isset( $parent->post_status ) ) {
				$parent_data['parent_status'] = $parent->post_status;
			}
		}

		$save_id = $parent_id;
		return $parent_data;
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
	 * Fetch and filter meta data for an attachment
	 * 
	 * Returns a filtered array of a post's meta data. Internal values beginning with '_'
	 * are stripped out or converted to an 'mla_' equivalent. 
	 *
	 * @since 0.1
	 *
	 * @param	int		post ID of attachment
	 *
	 * @return	array	Meta data variables
	 */
	public static function mla_fetch_attachment_metadata( $post_id ) {
		static $save_id = -1, $results;

		if ( $save_id == $post_id ) {
			return $results;
		} elseif ( $post_id == -1 ) {
			$save_id = -1;
			return NULL;
		}

		$attached_file = NULL;
		$results = array();
		$post_meta = get_metadata( 'post', $post_id );
		if ( is_array( $post_meta ) ) {
			foreach ( $post_meta as $post_meta_key => $post_meta_value ) {
				if ( empty( $post_meta_key ) ) {
					continue;
				}

				if ( '_' == $post_meta_key{0} ) {
					if ( stripos( $post_meta_key, '_wp_attached_file' ) === 0 ) {
						$key = 'mla_wp_attached_file';
						$attached_file = $post_meta_value[0];
					} elseif ( stripos( $post_meta_key, '_wp_attachment_metadata' ) === 0 ) {
						$key = 'mla_wp_attachment_metadata';
					} elseif ( stripos( $post_meta_key, '_wp_attachment_image_alt' ) === 0 ) {
						$key = 'mla_wp_attachment_image_alt';
					} else {
						continue;
					}
				} else {
					if ( stripos( $post_meta_key, 'mla_' ) === 0 ) {
						$key = $post_meta_key;
					} else {
						$key = 'mla_item_' . $post_meta_key;
					}
				}

				/*
				 * At this point, every value is an array; one element per instance of the key.
				 * We'll test anyway, just to be sure, then convert single-instance values to a scalar.
				 * Metadata array values are serialized for storage in the database.
				 */
				if ( is_array( $post_meta_value ) ) {
					if ( count( $post_meta_value ) == 1 ) {
						$post_meta_value = maybe_unserialize( $post_meta_value[0] );
					} else {
						foreach ( $post_meta_value as $single_key => $single_value ) {
							$post_meta_value[ $single_key ] = maybe_unserialize( $single_value );
						}
					}
				}

				$results[ $key ] = $post_meta_value;
			} // foreach $post_meta

			if ( ! empty( $attached_file ) ) {
				$last_slash = strrpos( $attached_file, '/' );
				if ( false === $last_slash ) {
					$results['mla_wp_attached_path'] = '';
					$results['mla_wp_attached_filename'] = $attached_file;
				} else {
					$results['mla_wp_attached_path'] = substr( $attached_file, 0, $last_slash + 1 );
					$results['mla_wp_attached_filename'] = substr( $attached_file, $last_slash + 1 );
				}
			} // $attached_file
		} // is_array($post_meta)

		$save_id = $post_id;
		return $results;
	}

	/**
	 * Find Featured Image and inserted image/link references to an attachment
	 * 
	 * Searches all post and page content to see if the attachment is used 
	 * as a Featured Image or inserted in the post as an image or link.
	 *
	 * @since 0.1
	 *
	 * @param	int	post ID of attachment
	 * @param	int	post ID of attachment's parent, if any
	 * @param	boolean	True to compute references, false to return empty values
	 *
	 * @return	array	Reference information; see $references array comments
	 */
	public static function mla_fetch_attachment_references( $ID, $parent, $add_references = true ) {
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
		$parent_data = self::mla_fetch_attachment_parent_data( $parent );
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
		if ('checked' == MLAOptions::mla_get_option( MLAOptions::MLA_EXCLUDE_REVISIONS ) ) {
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
		if ( MLAOptions::$process_featured_in ) {
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
		if ( MLAOptions::$process_inserted_in ) {
			$reference_tests++;

			if ( NULL == $inserted_in_option ) {
				$inserted_in_option = MLAOptions::mla_get_option( MLAOptions::MLA_INSERTED_IN_TUNING );
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

					if ( self::$wp_4dot0_plus ) {
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

					if ( self::$wp_4dot0_plus ) {
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
		if ( MLAOptions::$process_mla_gallery_in ) {
			$reference_tests++;
			if ( self::_build_mla_galleries( MLAOptions::MLA_MLA_GALLERY_IN_TUNING, self::$mla_galleries, '[mla_gallery', $exclude_revisions ) ) {
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
		if ( MLAOptions::$process_gallery_in ) {
			$reference_tests++;
			if ( self::_build_mla_galleries( MLAOptions::MLA_GALLERY_IN_TUNING, self::$galleries, '[gallery', $exclude_revisions ) ) {
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
	 * Searches all post and page content to see if the attachmenta are used 
	 * as a Featured Image or inserted in the post as an image or link.
	 *
	 * @since 1.94
	 *
	 * @param	array	WP_Post objects, passed by reference
	 *
	 * @return	void	updates WP_Post objects with new mla_references property
	 */
	public static function mla_attachment_array_fetch_references( &$attachments ) {
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

		$inserted_in_option = MLAOptions::mla_get_option( MLAOptions::MLA_INSERTED_IN_TUNING );
		$initial_references['inserted_option'] = $inserted_in_option;

		/*
		 * Make sure there's work to do; otherwise initialize the attachment data and return
		 */
		if ( false == ( MLAOptions::$process_featured_in || MLAOptions::$process_inserted_in || MLAOptions::$process_gallery_in || MLAOptions::$process_mla_gallery_in ) ) {
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

		if ('checked' == MLAOptions::mla_get_option( MLAOptions::MLA_EXCLUDE_REVISIONS ) ) {
			$exclude_revisions = " AND (p.post_type <> 'revision')";
		} else {
			$exclude_revisions = '';
		}

		$features = array();
		if ( MLAOptions::$process_featured_in && ! empty( $attachment_ids ) ) {
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

		if ( MLAOptions::$process_inserted_in ) {
			$query_parameters = array();
			$query = array();
			$query[] = "SELECT ID, post_type, post_status, post_title, CONVERT(`post_content` USING utf8 ) AS POST_CONTENT FROM {$wpdb->posts} WHERE ( %s=%s";
			// for empty file name array
			$query_parameters[] = '1';
			$query_parameters[] = '0';

			foreach ( $files as $file ) {
				foreach ( $file['files'] as $base_name => $file_data ) {
					$query[] = 'OR ( POST_CONTENT LIKE %s)';

					if ( self::$wp_4dot0_plus ) {
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

		if ( MLAOptions::$process_mla_gallery_in ) {
			$have_mla_galleries = self::_build_mla_galleries( MLAOptions::MLA_MLA_GALLERY_IN_TUNING, self::$mla_galleries, '[mla_gallery', $exclude_revisions );
		} else {
			$have_mla_galleries = false;
		}

		if ( MLAOptions::$process_gallery_in ) {
			$have_galleries = self::_build_mla_galleries( MLAOptions::MLA_GALLERY_IN_TUNING, self::$galleries, '[gallery', $exclude_revisions );
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
			if ( MLAOptions::$process_featured_in ) {
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
			if ( MLAOptions::$process_inserted_in ) {
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
			if ( MLAOptions::$process_mla_gallery_in ) {
				$reference_tests++;
				if ( self::_build_mla_galleries( MLAOptions::MLA_MLA_GALLERY_IN_TUNING, self::$mla_galleries, '[mla_gallery', $exclude_revisions ) ) {
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
			if ( MLAOptions::$process_gallery_in ) {
				$reference_tests++;
				if ( self::_build_mla_galleries( MLAOptions::MLA_GALLERY_IN_TUNING, self::$galleries, '[gallery', $exclude_revisions ) ) {
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
	private static $galleries = null;

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
	private static $mla_galleries = null;

	/**
	 * Invalidates the $mla_galleries or $galleries array and cached values
	 *
	 * @since 1.00
	 *
	 * @param	string name of the gallery's cache/option variable
	 *
	 * @return	void
	 */
	public static function mla_flush_mla_galleries( $option_name ) {
		delete_transient( MLA_OPTION_PREFIX . 't_' . $option_name );

		switch ( $option_name ) {
			case MLAOptions::MLA_GALLERY_IN_TUNING:
				self::$galleries = null;
				break;
			case MLAOptions::MLA_MLA_GALLERY_IN_TUNING:
				self::$mla_galleries = null;
				break;
			default:
				//	ignore everything else
		} // switch
	}

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
		self::mla_flush_mla_galleries( MLAOptions::MLA_GALLERY_IN_TUNING );
		self::mla_flush_mla_galleries( MLAOptions::MLA_MLA_GALLERY_IN_TUNING );
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

		$option_value = MLAOptions::mla_get_option( $option_name );
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
		 * $galleries_array is null, so build the array
		 */
		$galleries_array = array();

		if ( $exclude_revisions ) {
			$exclude_revisions = "(post_type <> 'revision') AND ";
		} else {
			$exclude_revisions = '';
		}

		if ( self::$wp_4dot0_plus ) {
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
		$chunksize = 16384;			
		$xmp_chunk = file_get_contents( $file_name, true, NULL, $file_offset, $chunksize );

		/*
		 * If necessary and possible, advance the $xmp_chunk through the file until it contains the start tag
		 */
		if ( false === ( $start_tag = strpos( $xmp_chunk, '<x:xmpmeta' ) ) && ( $chunksize == strlen( $xmp_chunk ) ) ) {
			$new_offset = $file_offset + ( $chunksize - 16 );
			$xmp_chunk = file_get_contents( $file_name, true, NULL, $new_offset, $chunksize );
			while ( false === ( $start_tag = strpos( $xmp_chunk, '<x:xmpmeta' ) ) && ( $chunksize == strlen( $xmp_chunk ) ) ) {
				$new_offset = $new_offset + ( $chunksize - 16 );
				$xmp_chunk = file_get_contents( $file_name, true, NULL, $new_offset, $chunksize );
			} // while not found
		} else { // if not found
			$new_offset = $file_offset;
		}

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
		$xmp_values = array();
		$xml_parser = xml_parser_create('UTF-8');
		if ( xml_parser_set_option( $xml_parser, XML_OPTION_SKIP_WHITE, 0 ) && xml_parser_set_option( $xml_parser, XML_OPTION_CASE_FOLDING, 0 ) ) {
			if (xml_parse_into_struct( $xml_parser, $xmp_string, $xmp_values ) == 0) {
				error_log( __( 'ERROR', 'media-library-assistant' ) . ': ' . _x( 'mla_parse_xmp_metadata xml_parse_into_struct failed.', 'error_log', 'media-library-assistant' ), 0 );
			}
		} else {
			error_log( __( 'ERROR', 'media-library-assistant' ) . ': ' . _x( 'mla_parse_xmp_metadata set option failed.', 'error_log', 'media-library-assistant' ), 0 );
		}

		xml_parser_free($xml_parser);

		if ( empty( $xmp_values ) ) {
			return NULL;
		}

		$levels = array();
		$current_level = 0;
		$results = array();
		$xmlns = array();
		foreach ( $xmp_values as $value ) {
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
							$node_attributes[ $att_tag ] = $att_value;
						}
					}
				}
			} // attributes

			switch ( $value['type'] ) {
				case 'open':
					$levels[ ++$current_level ] = array( 'key' => $value['tag'], 'values' => $node_attributes );
					break;
				case 'close':
					if ( 0 < --$current_level ) {
						$top_level = array_pop( $levels );
						if ( 'rdf:li' == $top_level['key'] ) {
							$levels[ $current_level ]['values'][] = $top_level['values'];
						} else {
							$levels[ $current_level ]['values'][ $top_level['key'] ] = $top_level['values'];
						}
					}
					break;
				case 'complete':
					if ( 'x-default' != $language ) {
						break;
					}

					$complete_value = NULL;
					if ( isset( $value['attributes'] ) ) {
						unset( $value['attributes']['xml:lang'] );

						if ( ! empty( $value['attributes'] ) ) {
							$complete_value = array();
							foreach ( $value['attributes'] as $attr_key => $attr_value ) {
								$complete_value[ $attr_key ] = self::_bin_to_utf8( $attr_value );
							}
						}
					}

					if ( empty( $complete_value ) ) {
						if ( isset( $value['value'] ) ) {
							$complete_value = self::_bin_to_utf8( $value['value'] );
						} else {
							$complete_value = '';
						}
					}

					if ( 'rdf:li' == $value['tag'] ) {
						$levels[ $current_level ]['values'][] = $complete_value;
					} else {
						$levels[ $current_level ]['values'][ $value['tag'] ] = $complete_value;
					}
			} // switch on type
		} // foreach value

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
			if ( isset( $namespace_arrays['dc'] ) && isset( $namespace_arrays['dc']['title'] ) ) {
				if ( is_array( $namespace_arrays['dc']['title'] ) ) {
					$results['Title'] = @implode( ',', $namespace_arrays['dc']['title'] );
				} else {
					$results['Title'] = (string) $namespace_arrays['dc']['title'];
				}
			}
		}

		if ( ! isset( $results['Author'] ) ) {
			if ( isset( $namespace_arrays['dc'] ) && isset( $namespace_arrays['dc']['creator'] ) ) {
				if ( is_array( $namespace_arrays['dc']['creator'] ) ) {
					$results['Author'] = @implode( ',', $namespace_arrays['dc']['creator'] );
				} else {
					$results['Author'] = (string) $namespace_arrays['dc']['creator'];
				}
			}
		}

		if ( ! isset( $results['Subject'] ) ) {
			if ( isset( $namespace_arrays['dc'] ) && isset( $namespace_arrays['dc']['description'] ) ) {
				if ( is_array( $namespace_arrays['dc']['description'] ) ) {
					$results['Subject'] = @implode( ',', $namespace_arrays['dc']['description'] );
				} else {
					$results['Subject'] = (string) $namespace_arrays['dc']['description'];
				}
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
					$term = trim( $term );
					if ( ! empty( $term ) ) {
						$keywords[ $term ] = $term;
					}
				}
			} elseif ( is_string( $namespace_arrays['dc']['subject'] ) ) {
				$term = trim ( $namespace_arrays['dc']['subject'] );
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
			if ( isset( $namespace_arrays['xmp'] ) && isset( $namespace_arrays['xmp']['CreatorTool'] ) ) {
				$results['Creator'] = $namespace_arrays['xmp']['CreatorTool'];
			} elseif ( isset( $namespace_arrays['xap'] ) && isset( $namespace_arrays['xap']['CreatorTool'] ) ) {
				$results['Creator'] = $namespace_arrays['xap']['CreatorTool'];
			} elseif ( ! empty( $results['Producer'] ) ) {
				$results['Creator'] = $results['Producer'];
			}
		}

		if ( ! isset( $results['CreationDate'] ) ) {
			if ( isset( $namespace_arrays['xmp'] ) && isset( $namespace_arrays['xmp']['CreateDate'] ) ) {
				$results['CreationDate'] = $namespace_arrays['xmp']['CreateDate'];
			} elseif ( isset( $namespace_arrays['xap'] ) && isset( $namespace_arrays['xap']['CreateDate'] ) ) {
				$results['CreationDate'] = $namespace_arrays['xap']['CreateDate'];
			}
		}

		if ( ! isset( $results['ModDate'] ) ) {
			if ( isset( $namespace_arrays['xmp'] ) && isset( $namespace_arrays['xmp']['ModifyDate'] ) ) {
				$results['ModDate'] = $namespace_arrays['xmp']['ModifyDate'];
			} elseif ( isset( $namespace_arrays['xap'] ) && isset( $namespace_arrays['xap']['ModifyDate'] ) ) {
				$results['ModDate'] = $namespace_arrays['xap']['ModifyDate'];
			}
		}

		if ( ! empty( $xmlns ) ) {
			$results['xmlns'] = $xmlns;
		}

		$results = array_merge( $results, $namespace_arrays );
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
			foreach ( $item_metadata['mla_exif_metadata'] as $key => $value ) {
				if ( is_array( $value ) ) {
					$clean_data[ $key ] = '(ARRAY)';
				} elseif ( is_string( $value ) ) {
					$clean_data[ $key ] = self::_bin_to_utf8( substr( $value, 0, 256 ) );
				} else {
					$clean_data[ $key ] = $value;
				}
			}

			return var_export( $clean_data, true);
		} elseif ( 'ALL_IPTC' == $exif_key ) {
			$clean_data = array();
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
			foreach ( $xmp_metadata as $key => $value ) {
				if ( is_array( $value ) ) {
					$clean_data[ $key ] = '(ARRAY)';
				} elseif ( is_string( $value ) ) {
					$clean_data[ $key ] = self::_bin_to_utf8( substr( $value, 0, 256 ) );
				} else {
					$clean_data[ $key ] = $value;
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
			foreach ( $id3_metadata as $key => $value ) {
				if ( is_array( $value ) ) {
					$clean_data[ $key ] = '(ARRAY)';
				} elseif ( is_string( $value ) ) {
					$clean_data[ $key ] = self::_bin_to_utf8( substr( $value, 0, 256 ) );
				} else {
					$clean_data[ $key ] = $value;
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
			foreach ( $item_metadata['mla_pdf_metadata'] as $key => $value ) {
				if ( is_array( $value ) ) {
					$clean_data[ $key ] = '(ARRAY)';
				} elseif ( is_string( $value ) ) {
					$clean_data[ $key ] = self::_bin_to_utf8( substr( $value, 0, 256 ) );
				} else {
					$clean_data[ $key ] = $value;
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
						error_log( __( 'ERROR', 'media-library-assistant' ) . ': ' . '$results[mla_iptc_errors] = ' . var_export( $results['mla_exif_errors'], true ), 0 );
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
				//restore_error_handler();
				if ( ! empty( MLAData::$mla_IPTC_EXIF_errors ) ) {
					$results['mla_exif_errors'] = MLAData::$mla_IPTC_EXIF_errors;
					MLAData::$mla_IPTC_EXIF_errors = array();
					error_log( __( 'ERROR', 'media-library-assistant' ) . ': ' . '$results[mla_exif_errors] = ' . var_export( $results['mla_exif_errors'], true ), 0 );
				}
			} // exif_read_data

			$results['mla_xmp_metadata'] = self::mla_parse_xmp_metadata( $path, 0 );
			if ( NULL == $results['mla_xmp_metadata'] ) {
				$results['mla_xmp_metadata'] = array();
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
		$post_data = self::mla_fetch_attachment_metadata( $post_id );
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
					if ( empty( $post_data[ 'mla_wp_attachment_metadata' ] ) ) {
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
			self::mla_fetch_attachment_parent_data( -1 );
			self::mla_fetch_attachment_metadata( -1 );
			self::mla_fetch_attachment_references( -1, 0 );

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
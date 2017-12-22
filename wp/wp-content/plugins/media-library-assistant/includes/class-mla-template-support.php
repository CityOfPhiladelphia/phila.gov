<?php
/**
 * Media Library Assistant Custom Style/Markup Template handler(s).
 *
 * @package Media Library Assistant
 * @since 2.30
 */

/**
 * Class MLA (Media Library Assistant) Custom Style/Markup Template Support provides functions that
 * define, import and export custom style and markup templates for MLA shortcodes.
 *
 * @package Media Library Assistant
 * @since 2.30
 */
class MLATemplate_Support {
	/**
	 * $mla_template_definitions defines the structure of the style and markup templates
	 * and the labels, etc. required to render them in the Settings/Shortcodes tab.
	 *
	 * The array must be populated at runtime in MLATemplate_Support::mla_localize_template_definitions();
	 * localization calls cannot be placed in the "public static" array definition itself.
	 *
	 * @since 2.30
	 * @access public
	 * @var	array $mla_template_definitions {
	 *     Definitions by type. Key $$type is 'markup' or 'style'.
	 *
	 *     @type array $$type {
	 *         Definitions by shortcode. Key $$shortcode_slug is 'gallery', 'tag-cloud' or 'term-list'
	 *
	 *         @type array $$shortcode_slug {
	 *             Templates by name. Key $$template_name is the template name/slug.
	 *
	 *             @type string $label Label for the shortcode.
	 *             @type array $default_names Names of the default templates.
	 *             @type array $sections {
	 *                 Template section definitions. Key $$section_name is the section name/slug.
	 *
	 *                 @type array $$section_name {
	 *                     Definitions by section.
	 *
	 *                     @type string $label Label for the section textbox.
	 *                     @type integer $rows Number of rows for the section textbox.
	 *                     @type string $help Help text displayed below the textbox.
	 *                     @type integer $order Where the section appears in the template.
	 *                 }
	 *             }
	 *        }
	 *    }
	 * }
	 */
	 
	public static $mla_template_definitions = array ();

	/**
	 * Localize $mla_option_definitions array.
	 *
	 * Localization must be done at runtime; these calls cannot be placed in the
	 * "public static" array definition itself. Called from MLATest::initialize.
	 *
	 * @since 2.30
	 *
	 * @return null
	 */
	public static function mla_localize_template_definitions() {
		self::$mla_template_definitions = array (
			'style' => array(
				'gallery' => array(
					'label' => _x( 'Gallery', 'table_view_singular', 'media_library-assistant' ),
					'default_names' => array( 'default' ),
					'sections' => array(
						'description' => array(
							'label' => __( 'Description', 'media-library-assistant' ),
							'rows' => 3,
							'help' => __( 'Notes for the Shortcodes tab submenu table.', 'media-library-assistant' ),
							'order' => 0,
						),
						'styles' => array(
							'label' => __( 'Styles', 'media-library-assistant' ),
							'rows' => 10,
							'help' => __( 'List of substitution parameters, e.g., [+selector+], on Documentation tab.', 'media-library-assistant' ),
							'order' => 1,
						),
					),
				),
				'tag-cloud' => array(
					'label' => _x( 'Tag Cloud', 'table_view_singular', 'media_library-assistant' ),
					'default_names' => array( 'tag-cloud' ),
					'sections' => array(
						'description' => array(
							'label' => __( 'Description', 'media-library-assistant' ),
							'rows' => 3,
							'help' => __( 'Notes for the Shortcodes tab submenu table.', 'media-library-assistant' ),
							'order' => 0,
						),
						'styles' => array(
							'label' => __( 'Styles', 'media-library-assistant' ),
							'rows' => 10,
							'help' => __( 'List of substitution parameters, e.g., [+selector+], on Documentation tab.', 'media-library-assistant' ),
							'order' => 1,
						),
					),
				),
				'term-list' => array(
					'label' => _x( 'Term List', 'table_view_singular', 'media_library-assistant' ),
					'default_names' => array( 'term-list' ),
					'sections' => array(
						'description' => array(
							'label' => __( 'Description', 'media-library-assistant' ),
							'rows' => 3,
							'help' => __( 'Notes for the Shortcodes tab submenu table.', 'media-library-assistant' ),
							'order' => 0,
						),
						'styles' => array(
							'label' => __( 'Styles', 'media-library-assistant' ),
							'rows' => 10,
							'help' => __( 'List of substitution parameters, e.g., [+selector+], on Documentation tab.', 'media-library-assistant' ),
							'order' => 1,
						),
					),
				),
			),
			'markup' => array(
				'gallery' => array(
					'label' => _x( 'Gallery', 'table_view_singular', 'media_library-assistant' ),
					'default_names' => array( 'default' ),
					'sections' => array(
						'description' => array(
							'label' => __( 'Description', 'media-library-assistant' ),
							'rows' => 3,
							'help' => __( 'Notes for the Shortcodes tab submenu table.', 'media-library-assistant' ),
							'order' => 0,
						),
						'arguments' => array(
							'label' => __( 'Arguments', 'media-library-assistant' ),
							'rows' => 3,
							'help' => __( 'Default shortcode parameter values.', 'media-library-assistant' ),
							'order' => 2,
						),
						'row-open' => array(
							'label' => __( 'Row', 'media-library-assistant' ) . '&nbsp;' . __( 'Open', 'media-library-assistant' ),
							'rows' => 3,
							'help' => __( 'Markup for the beginning of each row in the gallery.', 'media-library-assistant' ),
							'order' => 4,
						),
						'open' => array(
							'label' => __( 'Open', 'media-library-assistant' ),
							'rows' => 3,
							'help' => __( 'Markup for the beginning of the gallery. List of parameters, e.g., [+selector+], on Documentation tab.', 'media-library-assistant' ),
							'order' => 3,
						),
						'item' => array(
							'label' => __( 'Item', 'media-library-assistant' ),
							'rows' => 6,
							'help' => __( 'Markup for each item/cell of the gallery.', 'media-library-assistant' ),
							'order' => 5,
						),
						'row-close' => array(
							'label' => __( 'Row', 'media-library-assistant' ) . '&nbsp;' . __( 'Close', 'media-library-assistant' ),
							'rows' => 3,
							'help' => __( 'Markup for the end of each row in the gallery.', 'media-library-assistant' ),
							'order' => 9,
						),
						'close' => array(
							'label' => __( 'Close', 'media-library-assistant' ),
							'rows' => 3,
							'help' => __( 'Markup for the end of the gallery.', 'media-library-assistant' ),
							'order' => 10,
						),
					),
				),
				'tag-cloud' => array(
					'label' => _x( 'Tag Cloud', 'table_view_singular', 'media_library-assistant' ),
					'default_names' => array( 'tag-cloud', 'tag-cloud-ul', 'tag-cloud-dl' ),
					'sections' => array(
						'description' => array(
							'label' => __( 'Description', 'media-library-assistant' ),
							'rows' => 3,
							'help' => __( 'Notes for the Shortcodes tab submenu table.', 'media-library-assistant' ),
							'order' => 0,
						),
						'arguments' => array(
							'label' => __( 'Arguments', 'media-library-assistant' ),
							'rows' => 3,
							'help' =>  __( 'Default shortcode parameter values.', 'media-library-assistant' ),
							'order' => 2,
						),
						'row-open' => array(
							'label' => __( 'Row', 'media-library-assistant' ) . '&nbsp;' . __( 'Open', 'media-library-assistant' ),
							'rows' => 3,
							'help' => __( 'Markup for the beginning of each row in the cloud; grid format only.', 'media-library-assistant' ),
							'order' => 4,
						),
						'open' => array(
							'label' => __( 'Open', 'media-library-assistant' ),
							'rows' => 3,
							'help' => __( 'Markup for the beginning of the cloud. List of parameters, e.g., [+selector+], on Documentation tab.', 'media-library-assistant' ),
							'order' => 3,
						),
						'item' => array(
							'label' => __( 'Item', 'media-library-assistant' ),
							'rows' => 6,
							'help' => __( 'Markup for each item/cell of the cloud.', 'media-library-assistant' ),
							'order' => 5,
						),
						'row-close' => array(
							'label' => __( 'Row', 'media-library-assistant' ) . '&nbsp;' . __( 'Close', 'media-library-assistant' ),
							'rows' => 3,
							'help' => __( 'Markup for the end of each row in the cloud; grid format only.', 'media-library-assistant' ),
							'order' => 9,
						),
						'close' => array(
							'label' => __( 'Close', 'media-library-assistant' ),
							'rows' => 3,
							'help' => __( 'Markup for the end of the cloud.', 'media-library-assistant' ),
							'order' => 10,
						),
					),
				),
				'term-list' => array(
					'label' => _x( 'Term List', 'table_view_singular', 'media_library-assistant' ),
					'default_names' => array( 'term-list-ul', 'term-list-dl', 'term-list-dropdown', 'term-list-checklist' ),
					'sections' => array(
						'description' => array(
							'label' => __( 'Description', 'media-library-assistant' ),
							'rows' => 3,
							'help' => __( 'Notes for the Shortcodes tab submenu table.', 'media-library-assistant' ),
							'order' => 0,
						),
						'arguments' => array(
							'label' => __( 'Arguments', 'media-library-assistant' ),
							'rows' => 3,
							'help' =>  __( 'Default shortcode parameter values.', 'media-library-assistant' ),
							'order' => 1,
						),
						'child-open' => array(
							'label' => __( 'Child', 'media-library-assistant' ) . '&nbsp;' . __( 'Open', 'media-library-assistant' ),
							'rows' => 3,
							'help' => __( 'Markup for the beginning of each level in the hierarchy; list format only.', 'media-library-assistant' ),
							'order' => 6,
						),
						'child-item' => array(
							'label' => __( 'Child', 'media-library-assistant' ) . '&nbsp;' . __( 'Item', 'media-library-assistant' ),
							'rows' => 6,
							'help' => __( 'Markup for each lower-level item in the hierarchy; list format only.', 'media-library-assistant' ),
							'order' => 7,
						),
						'child-close' => array(
							'label' => __( 'Child', 'media-library-assistant' ) . '&nbsp;' . __( 'Close', 'media-library-assistant' ),
							'rows' => 3,
							'help' => __( 'Markup for the end of each level in the hierarchy; list format only.', 'media-library-assistant' ),
							'order' => 8,
						),
						'open' => array(
							'label' => __( 'Open', 'media-library-assistant' ),
							'rows' => 3,
							'help' => __( 'Markup for the beginning of the list. List of parameters, e.g., [+selector+], on Documentation tab.', 'media-library-assistant' ),
							'order' => 3,
						),
						'item' => array(
							'label' => __( 'Item', 'media-library-assistant' ),
							'rows' => 6,
							'help' => __( 'Markup for each item/cell in the list.', 'media-library-assistant' ),
							'order' => 5,
						),
						'close' => array(
							'label' => __( 'Close', 'media-library-assistant' ),
							'rows' => 3,
							'help' => __( 'Markup for the end of the list.', 'media-library-assistant' ),
							'order' => 10,
						),
					),
				),
			),
		);
//error_log( __LINE__ . ' mla_localize_template_definitions MLATemplate_Support::$mla_template_definitions = ' . var_export( MLATemplate_Support::$mla_template_definitions, true ), 0 );
	}
	
	/**
	 * Style and Markup templates.
	 *
	 * @since 2.30
	 * @access private
	 * @var	array $mla_custom_templates {
	 *     Templates by type. Key $$type is 'markup' or 'style'.
	 *
	 *     @type array $$type {
	 *         Templates by shortcode. Key $$shortcode_slug is 'gallery', 'tag-cloud' or 'term-list'
	 *
	 *         @type array $$shortcode_slug {
	 *             Templates by name. Key $$template_name is the template name/slug, which must be unique within type.
	 *
	 *             @type array $$template_name {
	 *                 Template content by section. Key $$section_name is the section name/slug.
	 *
	 *                 @type string $$section_name HTML markup/CSS styles for the template section.
	 *             }
	 *        }
	 *    }
	 * }
	 */
	private static $mla_custom_templates = NULL;

	/**
	 * Load style and markup templates to $mla_custom_templates.
	 *
	 * @since 2.30
	 *
	 * @return null
	 */
	public static function mla_load_custom_templates() {
		if ( empty( MLATemplate_Support::$mla_template_definitions ) ) {
			MLATemplate_Support::mla_localize_template_definitions();
		}

		MLATemplate_Support::$mla_custom_templates = NULL;
		$default_templates = MLACore::mla_load_template( 'mla-custom-templates.tpl' );

		// Load the default templates
		if ( is_null( $default_templates ) ) {
			MLACore::mla_debug_add( '<strong>mla_debug mla_load_custom_templates()</strong> ' . __( 'error loading tpls/mla-custom-templates.tpl', 'media-library-assistant' ), MLACore::MLA_DEBUG_CATEGORY_ANY );
			return;
		} elseif ( !$default_templates ) {
			MLACore::mla_debug_add( '<strong>mla_debug mla_load_custom_templates()</strong> ' . __( 'tpls/mla-custom-templates.tpl not found', 'media-library-assistant' ), MLACore::MLA_DEBUG_CATEGORY_ANY );
			return;
		}

		// Record explicit shortcode assignments, extract the style template description "section"
		$mla_shortcode_slugs = array();
		$mla_descriptions = array();
		foreach ( $default_templates as $key => $value ) {
			$mla_shortcode_slug = NULL;
			$mla_description = NULL;
			
			$match_count = preg_match( '#\<!-- mla_shortcode_slug="(.+)" --\>[\r\n]*#', $value, $matches, PREG_OFFSET_CAPTURE );
			if ( $match_count == 0 ) {
				$match_count = preg_match( '#mla_shortcode_slug="(.+)"[ \r\n]*#', $value, $matches, PREG_OFFSET_CAPTURE );
			}

			if ( $match_count > 0 ) {
				$mla_shortcode_slug = $matches[ 1 ][ 0 ];
				$value = substr_replace( $value, '', $matches[ 0 ][ 1 ], strlen( $matches[ 0 ][ 0 ] ) );
			}

			if ( !empty( $mla_shortcode_slug ) ) {
				$tail = strrpos( $key, '-style' );
				if ( ! ( false === $tail ) ) {
					$mla_shortcode_slugs['style'][ substr( $key, 0, $tail ) ] = $mla_shortcode_slug;
				} else {
					$tail = strrpos( $key, '-arguments-markup' );
					if ( ! ( false === $tail ) ) {
						$mla_shortcode_slugs['markup'][ substr( $key, 0, $tail ) ] = $mla_shortcode_slug;
					}
				}
			}

			$match_count = preg_match( '#\<!-- mla_description="(.+)" --\>[\r\n]*#', $value, $matches, PREG_OFFSET_CAPTURE );
			if ( $match_count > 0 ) {
				$mla_description = $matches[ 1 ][ 0 ];
				$value = substr_replace( $value, '', $matches[ 0 ][ 1 ], strlen( $matches[ 0 ][ 0 ] ) );
			}

			if ( empty( $value ) ) {
				unset( $default_templates[ $key ] );
			} else {
				$default_templates[ $key ] = $value;
//error_log( __LINE__ . " replace default template {$key}, {$mla_shortcode_slug}, {$mla_description} value = " . MLAData::mla_hex_dump( $value ), 0 );
			}

			if ( !empty( $mla_shortcode_slug ) ) {
				$tail = strrpos( $key, '-style' );
				if ( ! ( false === $tail ) ) {
					$mla_shortcode_slugs['style'][ substr( $key, 0, $tail ) ] = $mla_shortcode_slug;
				} else {
					$tail = strrpos( $key, '-arguments-markup' );
					if ( ! ( false === $tail ) ) {
						$mla_shortcode_slugs['markup'][ substr( $key, 0, $tail ) ] = $mla_shortcode_slug;
					}
				}
			}

			if ( !empty( $mla_description ) ) {
				$tail = strrpos( $key, '-style' );
				if ( ! ( false === $tail ) ) {
					$mla_descriptions['style'][ substr( $key, 0, $tail ) ] = $mla_description;
				}
			}
		}
		
		// Find the shortcode and template type for array indices
		foreach ( $default_templates as $key => $value ) {
			$tail = strrpos( $key, '-style' );
			if ( ! ( false === $tail ) ) {
				// If we can't find the shortcode; assume it's ]mla_gallery]				
				$shortcode = 'gallery';
				$name = substr( $key, 0, $tail );

				if ( isset( $mla_shortcode_slugs['style'][ $name ] ) ) {
					// Assign to the declared shortcode
					$shortcode = $mla_shortcode_slugs['style'][ $name ];
				} else {
					// Guess at the shortcode
					foreach( MLATemplate_Support::$mla_template_definitions['style'] as $slug => $definition ) {
						if ( isset( $definition['default_names'] ) && in_array( $name, $definition['default_names'] ) ) {
							$shortcode = $slug;
							break;
						}
					}
				}

				if ( isset( $mla_descriptions['style'][ $name ] ) ) {
					MLATemplate_Support::$mla_custom_templates['style'][ $shortcode ][ $name ]['description'] = $mla_descriptions['style'][ $name ];
				}
				
				MLATemplate_Support::$mla_custom_templates['style'][ $shortcode ][ $name ]['styles'] = $value;
				continue;
			}

			$tail = strrpos( $key, '-markup' );
			if ( ! ( false === $tail ) ) {
				// If we can't find the shortcode; assume it's mla_gallery				
				$shortcode = 'gallery';
				$name = substr( $key, 0, $tail );
				
				// Look for explicit assignment
				foreach( $mla_shortcode_slugs['markup'] as $root_name => $mla_shortcode_slug ) {
					$root = strpos( $name, $root_name );
					if ( 0 === $root ) {
						$section_name = substr( $name, strlen( $root_name ) + 1 );
						// Assign to the declared shortcode
						MLATemplate_Support::$mla_custom_templates['markup'][ $mla_shortcode_slug ][ $root_name ][ $section_name ] = $value;
						$name = NULL;
						break;
					}
				}

				if ( $name ) {
					// Guess at the shortcode
					foreach( MLATemplate_Support::$mla_template_definitions['markup'] as $slug => $definition ) {
						if ( isset( $definition['default_names'] ) ) {
							foreach( $definition['default_names'] as $default_name ) {
								$root = strpos( $name, $default_name );
								if ( 0 === $root ) {
									foreach( $definition['sections'] as $section_name => $section_value ) {
										$tail = strrpos( $name, '-' . $section_name );
										if ( ! ( false === $tail ) ) {
											$name = substr( $name, 0, $tail );
											MLATemplate_Support::$mla_custom_templates['markup'][ $slug ][ $name ][ $section_name ] = $value;
										}
									}
									
									$name = NULL;
									break;
								} // matched the default name
							} // foreach default name
						}
					} // foreach shortcode
				} // Guess the shortcode

				// Can't find the shortcode; assume it's [mla_gallery]
				if ( $name ) {
					foreach( MLATemplate_Support::$mla_template_definitions['markup']['gallery']['sections'] as $section_name => $section_value ) {
						$tail = strrpos( $name, '-' . $section_name );
						if ( ! ( false === $tail ) ) {
							$name = substr( $name, 0, $tail );
							MLATemplate_Support::$mla_custom_templates['markup']['gallery'][ $name ][ $section_name ] = $value;
						}
					}
				}
			} // default markup template
		} // foreach default template

		// Add user-defined Style templates
		$templates = MLACore::mla_get_option( 'style_templates' );
		if ( is_array(	$templates ) ) {
			foreach ( $templates as $name => $value ) {
				// If we can't find the shortcode; assume it's [mla_gallery]				
				$shortcode = 'gallery';

				// Extract the description "section"
				$mla_description = NULL;
				$match_count = preg_match( '#\<!-- mla_description="(.+)" --\>[\r\n]*#', $value, $matches, PREG_OFFSET_CAPTURE );
				if ( $match_count > 0 ) {
					$mla_description = $matches[ 1 ][ 0 ];
					$value = substr_replace( $value, '', $matches[ 0 ][ 1 ], strlen( $matches[ 0 ][ 0 ] ) );
				}

				// Check for explicit shortcode assignment
				$match_count = preg_match( '#\<!-- mla_shortcode_slug="(.+)" --\>[\r\n]*#', $value, $matches, PREG_OFFSET_CAPTURE );
				if ( $match_count > 0 ) {
					$value = substr_replace( $value, '', $matches[ 0 ][ 1 ], strlen( $matches[ 0 ][ 0 ] ) );
					$shortcode = $matches[ 1 ][ 0 ];
				} else {
					// Guess from content
					foreach( MLATemplate_Support::$mla_template_definitions['style'] as $slug => $definition ) {
						if ( false !== strpos( $value, '.' . $slug ) ) {
							$shortcode = $slug;
							break;
						}
					}
				}
				
				if ( !empty( $mla_description ) ) {
					MLATemplate_Support::$mla_custom_templates['style'][ $shortcode ][ $name ]['description'] = $mla_description;
				}
				
				MLATemplate_Support::$mla_custom_templates['style'][ $shortcode ][ $name ]['styles'] = $value;
			} // foreach $templates
		} // is_array

		// Add user-defined Markup templates
		$templates = MLACore::mla_get_option( 'markup_templates' );
		if ( is_array(	$templates ) ) {
			foreach ( $templates as $name => $value ) {
				// Check for explicit assignment
				if ( isset( $value['arguments'] ) ) {
					$match_count = preg_match( '#mla_shortcode_slug="(.+)"[ \r\n]*#', $value['arguments'], $matches, PREG_OFFSET_CAPTURE );
				} else {
					$match_count = 0;
				}
	
				if ( $match_count > 0 ) {
					$value['arguments'] = substr_replace( $value['arguments'], '', $matches[ 0 ][ 1 ], strlen( $matches[ 0 ][ 0 ] ) );
					if ( empty( $value['arguments'] ) ) {
						unset( $value['arguments'] );
					}
					
					MLATemplate_Support::$mla_custom_templates['markup'][ $matches[ 1 ][ 0 ] ][ $name ] = $value;
					continue;
				}

				// Guess from content
				$full_text = ''; // for guessing shortcode name
				foreach( $value as $section_name => $section_value ) {
					$full_text .= $section_value;
				}
				
				foreach( MLATemplate_Support::$mla_template_definitions['markup'] as $slug => $definition ) {
					if ( preg_match( '#class=[\'\"]*.*' . $slug . '#', $full_text, $matches ) ) {
						MLATemplate_Support::$mla_custom_templates['markup'][ $slug ][ $name ] = $value;
						$name = NULL;
						break;
					}
				}
				
				if ( $name ) {
					MLATemplate_Support::$mla_custom_templates['markup']['gallery'][ $name ] = $value;
				}
			} // foreach $templates
		} // is_array
//error_log( __LINE__ . ' mla_load_custom_templates MLATemplate_Support::$mla_custom_templates = ' . var_export( MLATemplate_Support::$mla_custom_templates, true ), 0 );
	}

	/**
	 * Fetch style or markup template from $mla_templates.
	 *
	 * @since 2.30
	 *
	 * @param string $key Template name.
	 * @param string $shortcode Optional. Shortcode slug; 'gallery', 'tag-cloud' or 'term-list'. Default 'gallery'.
	 * @param string $type Optional. Template type; 'style' or 'markup'. Default 'style'.
	 * @param string $section Optional. Template section. Default '[not supplied]'.
	 * @return string Requested template section, if it exists.
	 * @return boolean false if template section not found,
	 *                 true if section='[exists]' and template exists.
	 * @return null If no templates exist.
	 */
	public static function mla_fetch_custom_template( $key, $shortcode = 'gallery', $type = 'style', $section = '[not supplied]' ) {
//MLACore::mla_debug_add( "<strong>mla_fetch_custom_template( {$key}, {$shortcode}, {$type}, {$section} )</strong> " . __( 'calling parameters', 'media-library-assistant' ), MLACore::MLA_DEBUG_CATEGORY_ANY );
		if ( ! is_array( MLATemplate_Support::$mla_custom_templates ) ) {
			MLACore::mla_debug_add( '<strong>mla_fetch_custom_template()</strong> ' . __( 'no templates exist', 'media-library-assistant' ), MLACore::MLA_DEBUG_CATEGORY_ANY );
			return NULL;
		}
//error_log( ' mla_fetch_custom_template mla_custom_templates = ' . var_export( MLATemplate_Support::$mla_custom_templates, true ), 0 );

		if ( array_key_exists( $type, MLATemplate_Support::$mla_custom_templates ) ) {
			if ( array_key_exists( $shortcode, MLATemplate_Support::$mla_custom_templates[ $type ] ) ) {
				if ( array_key_exists( $key, MLATemplate_Support::$mla_custom_templates[ $type ][ $shortcode ] ) ) {
					if ( '[exists]' == $section ) {
						return true;
					}
					
					if ( array_key_exists( $section, MLATemplate_Support::$mla_custom_templates[ $type ][ $shortcode ][ $key ] ) ) {
						return MLATemplate_Support::$mla_custom_templates[ $type ][ $shortcode ][ $key ][ $section ];
					} elseif ( 'style' == $type && '[not supplied]' == $section ) {
						return MLATemplate_Support::$mla_custom_templates['style'][ $shortcode ][ $key ]['styles'];
					}

					// No error - not every section is required
					return false;
				} elseif ( '[exists]' == $section ) {
					return false;
				}
			}
		}

		MLACore::mla_debug_add( "<strong>mla_fetch_custom_template( {$key}, {$shortcode}, {$type}, {$section} )</strong> " . __( 'not found', 'media-library-assistant' ), MLACore::MLA_DEBUG_CATEGORY_ANY );
		return false;
	}

	/**
	 * Get ALL style templates from $mla_custom_templates, including default(s).
	 *
	 * @since 2.30
	 *
 	 * @param string $shortcode Optional. Shortcode to which the template(s) apply. Default ''.
	 * @return array|null Array ( name => value ) for all style templates or null if no templates.
	 */
	public static function mla_get_style_templates( $shortcode = '' ) {
		if ( ! is_array( MLATemplate_Support::$mla_custom_templates ) ) {
			MLACore::mla_debug_add( '<strong>mla_debug mla_get_style_templates()</strong> ' . __( 'no templates exist', 'media-library-assistant' ) );
			return NULL;
		}

		if ( !empty( $shortcode ) ) {
			if ( array_key_exists( $shortcode, MLATemplate_Support::$mla_custom_templates['style'] ) ) {
				return MLATemplate_Support::$mla_custom_templates['style'][ $shortcode ];
			}
			
			return NULL;
		}
		
		$templates = array();
		foreach ( MLATemplate_Support::$mla_custom_templates['style'] as $shortcode => $value ) {
			$templates = array_merge( $templates, $value );
		} // foreach

		return $templates;
	}

	/**
	 * Put user-defined style templates to $mla_custom_templates and database
	 *
	 * @since 2.30
	 *
	 * @param array	$templates Array ( name => value ) for all user-defined style templates.
	 * @return boolean true if success, false if failure.
	 */
	public static function mla_put_style_templates( $templates ) {
		$new_templates = array();
		foreach ( $templates as $name => $sections ) {
			$styles = $sections['styles'];
			
			// Embed description in the styles for backward compatibility
			if ( isset( $sections['description'] ) ) {
				$styles = sprintf( "<!-- mla_description=\"%1\$s\" -->\r\n%2\$s", $sections['description'], $styles );
			}
			
			$new_templates[ $name ] = $styles;
		}
		
		if ( MLACore::mla_update_option( 'style_templates', $new_templates ) ) {
			MLATemplate_Support::mla_load_custom_templates();
			return true;
		}

		return false;
	}

	/**
	 * Get ALL markup templates from $mla_custom_templates, including default(s).
	 *
	 * @since 2.30
	 *
 	 * @param string $shortcode Optional. Shortcode to which the template(s) apply. Default 'gallery'.
	 * @return array|null Array ( name => value ) for all markup templates or null if no templates.
	 */
	public static function mla_get_markup_templates( $shortcode = '' ) {
		if ( ! is_array( MLATemplate_Support::$mla_custom_templates ) ) {
			MLACore::mla_debug_add( '<strong>mla_debug mla_get_markup_templates()</strong> ' . __( 'no templates exist', 'media-library-assistant' ) );
			return NULL;
		}

		if ( !empty( $shortcode ) ) {
			if ( array_key_exists( $shortcode, MLATemplate_Support::$mla_custom_templates['markup'] ) ) {
				return MLATemplate_Support::$mla_custom_templates['markup'][ $shortcode ];
			}
			
			return NULL;
		}
		
		$templates = array();
		foreach ( MLATemplate_Support::$mla_custom_templates['markup'] as $shortcode => $value ) {
			$templates = array_merge( $templates, $value );
		} // foreach

		return $templates;
	}

	/**
	 * Put user-defined markup templates to $mla_custom_templates and database
	 *
	 * @since 2.30
	 *
	 * @param array	$templates Array ( name => value ) for all user-defined markup templates.
	 * @return boolean true if success, false if failure.
	 */
	public static function mla_put_markup_templates( $templates ) {
		if ( MLACore::mla_update_option( 'markup_templates', $templates ) ) {
			MLATemplate_Support::mla_load_custom_templates();
			return true;
		}

		return false;
	}
} // Class MLATemplate_Support

MLATemplate_Support::mla_load_custom_templates();
?>
<?php
/**
 * Support functions for the SMC Settings page.
 *
 * @package   Smart_Media_Categories_Admin
 * @author    David Lingren <dlingren@comcast.net>
 * @license   GPL-2.0+
 * @link      @TODO http://example.com
 * @copyright 2014 David Lingren
 */

/**
 * This support class provides functions to manage the SMC options
 *
 * In the current version all of the support functions are static, and there is
 * no need to create a new instance of the class.
 *
 * @package Smart_Media_Categories_Admin
 * @author  David Lingren <dlingren@comcast.net>
 */
class SMC_Settings_Support {
	/**
	 * Option definitions
	 *
	 * Defined NULL and initialized at runtime for I18N support.
	 *
	 * @since    1.0.6
	 *
	 * @var      array
	 */
	protected static $option_definitions = NULL;

	/**
	 * Initialize the $option_definitions array
	 *
	 * @since    1.0.6
	 *
	 * @return	void
	 */
	public static function initialize() {
		SMC_Settings_Support::$option_definitions = array(
			'smc_automatic_options' => array(
				'post_types' => array(
					'type' => 'text',
					'size' => 40,
					'default' => 'post',
					'id' => 'smc_automatic_post_types',
					'title' => __( 'Sync Post Types', 'smart-media-categories' ),
					'description' => __( "Which Post Type(s) are subject to these rules. Separate multiple types by commas.", 'smart-media-categories' ),
				),
				'scroll_threshold' => array(
					'type' => 'text',
					'size' => 2,
					'default' => '10',
					'id' => 'smc_automatic_scroll_threshold',
					'title' => __( 'Scroll Threshold', 'smart-media-categories' ),
					'description' => __( "The number of children required to activate column scrolling.", 'smart-media-categories' ),
				),
				'scroll_height' => array(
					'type' => 'text',
					'size' => 5,
					'default' => '150px',
					'id' => 'smc_automatic_scroll_height',
					'title' => __( 'Scroll Height', 'smart-media-categories' ),
					'description' => __( "Maximum height of scrolling Children column cell.", 'smart-media-categories' ),
				),
				'upload_item' => array(
					'type' => 'checkbox',
					'default' => '1',
					'id' => 'smc_automatic_upload_item',
					'title' => __( 'Upload Item', 'smart-media-categories' ),
					'description' => __( "When an item is uploaded to a <strong>Sync Post Type</strong> (any post_type in the Sync Post Types list above), it will inherit the parent's terms.", 'smart-media-categories' ),
				),
				'attach_orphan' => array(
					'type' => 'checkbox',
					'default' => '1',
					'id' => 'smc_automatic_attach_orphan',
					'title' => __( 'Attach Orphan', 'smart-media-categories' ),
					'description' => __( "When an orphan is attached to a <strong>Sync Post Type</strong>, it will inherit the parent's terms.", 'smart-media-categories' ),
				),
				'insert_orphan' => array(
					'type' => 'checkbox',
					'default' => '1',
					'id' => 'smc_automatic_insert_orphan',
					'title' => __( 'Insert Orphan', 'smart-media-categories' ),
					'description' => __( "Inserting an orphan in a <strong>Sync Post Type</strong> will attach it to the <strong>Sync Post Type</strong> and assign its terms.", 'smart-media-categories' ),
				),
				'insert_attached' => array(
					'type' => 'checkbox',
					'default' => '1',
					'id' => 'smc_automatic_insert_attached',
					'title' => __( 'Insert Attached', 'smart-media-categories' ),
					'description' => __( "Inserting an item already attached to a different <strong>Sync Post Type</strong> (or Page or Custom Post Type) will change the item's post_parent, delete its terms and assign the terms assigned to the new parent <strong>Sync Post Type</strong>.", 'smart-media-categories' ),
				),
				'update_post_terms' => array(
					'type' => 'checkbox',
					'default' => '1',
					'id' => 'smc_automatic_update_post_terms',
					'title' => __( 'Update Post', 'smart-media-categories' ),
					'description' => __( "When a <strong>Sync Post Type's</strong> terms are updated, the <strong>Sync Post Type's</strong> children inherit the current terms of the parent.", 'smart-media-categories' ),
				),
				'set_feature' => array(
					'type' => 'checkbox',
					'default' => '1',
					'id' => 'smc_automatic_set_feature',
					'title' => __( 'Set Feature', 'smart-media-categories' ),
					'description' => __( "When an orphan is set as a Featured Image of a <strong>Sync Post Type</strong> it is attached to the <strong>Sync Post Type</strong> and inherits the <strong>Sync Post Type's</strong> terms.", 'smart-media-categories' ),
				),
				'reattach_feature' => array(
					'type' => 'checkbox',
					'default' => '1',
					'id' => 'smc_automatic_reattach_feature',
					'title' => __( 'Reattach Feature', 'smart-media-categories' ),
					'description' => __( "If the item was previously attached to a different <strong>Sync Post Type</strong>, Page or Custom Post Type, it is detached from the previous parent, reattached to the current <strong>Sync Post Type</strong> and inherits the current parent's terms.", 'smart-media-categories' ),
				),
				'remove_feature' => array(
					'type' => 'checkbox',
					'default' => '1',
					'id' => 'smc_automatic_remove_feature',
					'title' => __( 'Remove Old Feature', 'smart-media-categories' ),
					'description' => __( "If the item was the Featured Image of a different <strong>Sync Post Type</strong>, it is removed as the Featured Image of that <strong>Sync Post Type</strong>. ", 'smart-media-categories' ),
				),
				'reattach_item' => array(
					'type' => 'checkbox',
					'default' => '1',
					'id' => 'smc_automatic_reattach_item',
					'title' => __( 'Reattach Item', 'smart-media-categories' ),
					'description' => __( "When an items new parent is a <strong>Sync Post Type</strong>, it will inherit the parent's terms.", 'smart-media-categories' ),
				),
			),
			'smc_manual_options' => array(
			),
		);
	}
	
	/**
	 * Find the SMC Settings page active tab
	 *
	 * @since    1.0.6
	 *
	 * @param	string	Optional; 'smc_automatic_options', 'smc_manual_options'
	 *
	 * @return	string	Active tab value	
	 */
	public static function get_active_tab( $active_tab = NULL ) {
		if ( isset( $_REQUEST[ 'smc_settings_tab' ] ) ) {
			$active_tab = $_REQUEST[ 'smc_settings_tab' ];
		} elseif ( is_null( $active_tab ) ) {
			$active_tab = 'smc_automatic_options';
		} elseif ( ! in_array( $active_tab, array( 'smc_automatic_options', 'smc_manual_options' ) ) ) {
			$active_tab = 'smc_automatic_options';
		}
	
		return $active_tab;
	}
	
	/**
	 * Find taxonomies subjet to SMC rules
	 *
	 * @since    1.0.9
	 *
	 * @return	array	Taxonomies subject to the SMC rules, if any
	 */
	public static function smc_taxonomies() {
		static $smc_taxonomies = NULL;
		
		if ( is_null( $smc_taxonomies ) ) {
			$smc_types = array_map( 'trim', explode( ',', self::get_option('post_types') ) );
//error_log( __LINE__ . ' SMC_Settings_Support::smc_taxonomies $smc_types = ' . var_export( $smc_types, true ), 0 );
			
			$smc_taxonomies = array();
			foreach ( $smc_types as $smc_type ) {
				$taxonomies = get_object_taxonomies( $smc_type, 'names' );
//error_log( __LINE__ . " SMC_Settings_Support::smc_taxonomies( {$smc_type} ) taxonomies = " . var_export( $taxonomies, true ), 0 );
				foreach ( $taxonomies as $tax_name ) {
					// Index on name to avoid duplicates
					$smc_taxonomies[ $tax_name ] = $tax_name;
				}
			}

//error_log( __LINE__ . ' SMC_Settings_Support::smc_taxonomies $smc_taxonomies = ' . var_export( $smc_taxonomies, true ), 0 );
		}
		
		return $smc_taxonomies;
	}
	
	/**
	 * Find Post Types subjet to SMC rules
	 *
	 * @since    1.0.9
	 *
	 * @return	array	Post Types subject to the SMC rules, if any
	 */
	public static function smc_post_types() {
		static $smc_types = NULL;
		
		if ( is_null( $smc_types ) ) {
			$smc_types = array_map( 'trim', explode( ',', self::get_option('post_types') ) );
//error_log( __LINE__ . ' SMC_Settings_Support::smc_post_types $smc_types = ' . var_export( $smc_types, true ), 0 );
		}
		
		return $smc_types;
	}
	
	/**
	 * See if a Post Type is subjet to SMC rules
	 *
	 * @since    1.0.9
	 *
	 * @param	string	$post_type Post Type slug
	 *
	 * @return	boolean	True if the Post Type is subject to the SMC rules
	 */
	public static function is_smc_post_type( $post_type ) {
		static $smc_types = NULL;
		
		if ( is_null( $smc_types ) ) {
			$smc_types = array_map( 'trim', explode( ',', self::get_option('post_types') ) );
//error_log( __LINE__ . ' SMC_Settings_Support::is_smc_post_type $smc_types = ' . var_export( $smc_types, true ), 0 );
		}
		
		return in_array( $post_type, $smc_types );
	}
	
	/**
	 * Find an SMC option by slug
	 *
	 * @since    1.0.6
	 *
	 * @param	string	Option slug
	 *
	 * @return	string	Option value	
	 */
	public static function get_option( $slug ) {
		$group = NULL;
		$definition = NULL;
		
		foreach ( SMC_Settings_Support::$option_definitions as $key => $value ) {
			if ( array_key_exists( $slug, $value ) ) {
				$group = $key;
				$definition = $value[ $slug ];
				break;
			}
		}

		if ( is_null( $definition ) ) {
			return NULL;		
		}
		
		$options = get_option( $group );
		if ( is_array( $options ) && isset( $options[ $slug ] ) ) {
			return $options[ $slug ];
		}
		
		return $definition['default'];
	}
	
	/**
	 * Render the SMC Settings page, section and options
	 *
	 * @since    1.0.6
	 *
	 * @param	string	Settings page slug, as used in add_options_page()
	 * @param	string	Optional; 'smc_automatic_options', 'smc_manual_options'
	 *
	 * @return	void	
	 */
	public static function initialize_settings_page( $page_slug, $active_tab = NULL ) {
		$active_tab = SMC_Settings_Support::get_active_tab( $active_tab );
		
		if ( 'smc_automatic_options' == $active_tab ) {
			add_settings_section( // $id, $title, $callback, $page
				'smc_automatic_options_section',
				__( 'Automatic Actions', 'smart-media-categories' ),
				'SMC_Settings_Support::render_automatic_options_section',
				$page_slug
			);
			
			foreach( SMC_Settings_Support::$option_definitions['smc_automatic_options'] as $key => $value ) {
				add_settings_field(	// $id, $title, $callback, $page, $section, $args
					$value['id'],
					$value['title'],
					'SMC_Settings_Support::render_automatic_options',
					$page_slug,
					'smc_automatic_options_section',
					array_merge( $value, array( 'slug' => $key ) )
				);
			}
		} // smc_automatic_options
	
		if ( 'smc_manual_options' == $active_tab ) {
			add_settings_section( // $id, $title, $callback, $page
				'smc_manual_options_section',
				__( 'Manual Actions', 'smart-media-categories' ),
				'SMC_Settings_Support::render_manual_options_section',
				$page_slug
			);
		} // smc_manual_options
	}
	
	/**
	 * Render the SMC Settings page Automatic tab section-level content
	 *
	 * @since    1.0.6
	 *
	 * @param	string	Optional; 'smc_automatic_options', 'smc_manual_options'
	 *
	 * @return	void	echoes HTML markup for the section description
	 */
	public static function render_automatic_options_section() {
		echo '<p>' . __( "You can find some User Interface notes and more information about the rules in this PDF document: ", 'smart-media-categories' ) . "<a href=\"http://fairtradejudaica.org/wp-content/uploads/Smart-Media-Categories-v07.pdf\" target=\"_blank\">Smart-Media-Categories-v07.pdf</a></p>\n";
		echo '<p>' . __( "Check the box of each automatic rule you want to apply.", 'smart-media-categories' ) . "</p>\n";
	}
	
	/**
	 * Render the SMC Settings page Automatic tab section-level content
	 *
	 * @since    1.0.6
	 *
	 * @param	string	Optional; 'smc_automatic_options', 'smc_manual_options'
	 *
	 * @return	void	echoes HTML markup for the section description
	 */
	public static function render_manual_options_section() {
		echo '<p><strong>' . __( "There are no Manual Settings in this version of the plugin.", 'smart-media-categories' ) . "</strong></p>\n";
	}
	
	/**
	 * Render the Automatic tab Upload Item setting
	 *
	 * @since    1.0.6
	 *
	 * @param	array	( [0] => explaination of this setting )
	 *
	 * @return	void	echoes HTML markup for the setting
	 */
	public static function render_automatic_options( $args ) {
	//error_log( __LINE__ . ' SMC_Settings_Support::render_automatic_options $args = ' . var_export( $args, true ), 0 );
		
		$slug = $args['slug'];
		$value = SMC_Settings_Support::get_option( $slug );
		if ( NULL === $value ) {
			echo __( 'Option not found: ', 'smart-media-categories' ) . $slug;
			return;
		}
		
		switch ( $args['type'] ) {
			case 'checkbox':
				$html = '<input name="smc_automatic_options[' . $slug . ']" id="' . $args['id'] . '" type="checkbox" ' . checked( 1, $value, false ) . " value=\"1\"/>\n";
				$html .= '<label for="smc_automatic_' . $slug . '"> '  . $args['description'] . "</label>\n";
				break;
			case 'text':
				$html = '<input name="smc_automatic_options[' . $slug . ']" id="' . $args['id'] . '" type="text" size=' . $args['size'] . ' value="' . $value . "\"/>\n";
				$html .= '<br><label for="smc_automatic_' . $slug . '"> '  . $args['description'] . "</label>\n";
				break;
			default:
				$html = '';
		}
		
		echo $html;
	}
	
	/**
	 * Validate the SMC Settings page Automatic options
	 *
	 * @since    1.0.6
	 *
	 * @param	array	Option values as entered
	 *
	 * @return	void
	 */
	public static function validate_automatic_options( $input ) {
	//error_log( __LINE__ ."' SMC_Settings_Support::validate_automatic_options input = " . var_export( $input, true ), 0 );
		$output = array();
		$updates = 0;
		
		foreach( SMC_Settings_Support::$option_definitions['smc_automatic_options'] as $key => $value ) {
	//error_log( __LINE__ ."' SMC_Settings_Support::validate_automatic_options {$key} = " . var_export( $value, true ), 0 );
	
			$update = NULL;
			switch ( $value['type'] ) {
				case 'checkbox':
					if ( isset( $input[ $key ] ) && absint( $input[ $key ] ) ) {
						$update = '1';
					} else  {
						$update = '0';
					}
					break;
				case 'text':
					if ( isset( $input[ $key ] ) ) {
						$update = trim( $input[ $key ] );
					}
					break;
				default:
					break;
			}
			
			if ( !is_null( $update ) ) {
				$output[ $key ] = $update;
				if ( $output[ $key ] != SMC_Settings_Support::get_option(  $key ) ) {
					$updates++;
					add_settings_error( // $setting, $code, $message, $type
						$key,
						$key,
						$value['title'] . __( ' updated', 'smart-media-categories' ),
						'updated'
					);

				}
			}
		}

		if ( 0 == $updates ) {
			add_settings_error( // $setting, $code, $message, $type
				'smc_automatic_options',
				'smc_automatic_options',
				__( 'No updates', 'smart-media-categories' ),
				'updated'
			);
		}
		
	//error_log( __LINE__ ."' SMC_Settings_Support::validate_automatic_options output = " . var_export( $output, true ), 0 );
		update_option( 'smc_automatic_options', $output );
	}
	
	/**
	 * Render the SMC Settings page, section and options
	 *
	 * @since    1.0.6
	 *
	 * @param	string	Settings page slug
	 * @param	string	Optional; 'smc_automatic_options', 'smc_manual_options'
	 *
	 * @return	void	echoes HTML markup for the settings page
	 */
	public static function render_settings_page( $page, $active_tab = NULL ) {
		$active_tab = SMC_Settings_Support::get_active_tab( $active_tab );
		?>
		
		<h2 class="nav-tab-wrapper">
			<a href="?page=smart-media-categories&smc_settings_tab=smc_automatic_options" class="nav-tab <?php echo $active_tab == 'smc_automatic_options' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Automatic', 'smart-media-categories' ); ?></a>
			<a href="?page=smart-media-categories&smc_settings_tab=smc_manual_options" class="nav-tab <?php echo $active_tab == 'smc_manual_options' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Manual', 'smart-media-categories' ); ?></a>
		</h2>
	
		<form method="post" action="options-general.php?page=smart-media-categories&smc_settings_tab=<?php echo $active_tab ?>">
		<?php
		settings_fields( $page );
		do_settings_sections( $page );
		submit_button();
		?>
		</form>
	
	<?php }
}
?>
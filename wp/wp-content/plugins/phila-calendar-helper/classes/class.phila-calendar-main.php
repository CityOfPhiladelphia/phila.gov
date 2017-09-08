<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Phila_Calendar_Main{
	var $ids_being_changed = array();

	public function __construct() {
		global $discard;
		$discard = array();

		/**
		 * Register Phila Calendar post type action
		 */
		add_action( 'init', array( $this, 'register_phila_calendar_post_type' ), 5 );

		// Set Phila calendar actions for save, trash and before_delete_post
		$this->set_actions();

		/**
		* With this actions we replicate the Simple Calendar "status" on its related Phila Calendar
		*/
		add_action( 'transition_post_status',  array( $this, 'on_all_status_transitions' ), 12, 3 );
		add_action( 'simcal_process_settings_meta', array( $this, 'on_save_simple_calendar' ), 11, 1 );

		// Validate if masters are created and other stuff for this plugin to works correctly
		add_action( 'admin_init', array( $this, 'phila_calendar_validations' ) );

		/**
		 * FILTER SIMPLE CALENDAR
		 */
		add_filter( 'parse_query', array( $this, 'phila_admin_posts_filter' ) );
		add_action( 'restrict_manage_posts', array( $this, 'phila_admin_posts_filter_restrict_manage_posts' ) );

		if( get_option( "phc_force_simple_calendar_admin", false ) ) {
			add_filter( 'register_post_type_args', array( $this, 'modify_wordpress_post_types' ), 20, 2 );
		}
	}

	/**
	 * This is a filter function where we modify the calendar post_type to be managed only by the "Administrator" Role.
	 *
	 * @param $args post type arguments
	 * @param $post_type post type name
	 * @return Array 
	 */
	public function modify_wordpress_post_types( $args = array(), $post_type ){
		if( $post_type === "calendar" ) {
			$args['capabilities'] = array(
				'edit_post'          => 'manage_options',
				'read_post'          => 'manage_options',
				'delete_post'        => 'manage_options',
				'edit_posts'         => 'manage_options',
				'edit_others_posts'  => 'manage_options',
				'delete_posts'       => 'manage_options',
				'publish_posts'      => 'manage_options',
				'read_private_posts' => 'manage_options'
			);
		}

		return $args;
	}
	

	public function phila_admin_posts_filter( $wp_query ) {
		global $pagenow;

		if( is_admin() && 'edit.php' === $pagenow 
			&& 'calendar' === $this->get_current_post_type() ) {

			global $wpdb;
			$IDs = $wpdb->get_col( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}posts WHERE post_name = %s OR post_name = %s",
				'master-list-calendar', 'master-grid-calendar' ) );
			
			if( is_array( $IDs ) ) {
				if( isset( $_GET['show'] ) && $_GET['show'] === 'masters' ) {
					$wp_query->query_vars['post__in'] = $IDs;
				}else{
					$wp_query->query_vars['post__not_in'] = $IDs;
				}
			}
		}
	
		return $wp_query;
	}

	public function phila_admin_posts_filter_restrict_manage_posts() {
		global $pagenow;
		if( is_admin() && 'edit.php' === $pagenow 
			&& 'calendar' === $this->get_current_post_type() ) {
		?>
		<select name="show" id="show">
		<?php
			$fields = array(
				'no-masters' => 'No masters',
				'masters' => 'Masters'
			);
			$current = isset( $_GET['show'] ) ? $_GET['show'] : 'no-masters';
			foreach ( $fields as $key => $field ) {
				printf
					(
						'<option value="%s"%s>%s</option>',
						$key,
						$key == $current? ' selected="selected"':'',
						$field
					);
			}
		?>
		</select>
		<?php
		}
	}

	/**
	 * Set Phila Calendar Save, Trash and Before Delete Post actions
	 *
	 * @return void
	 */
	private function set_actions(){
		add_action( 'save_post_' . PHILA_CALENDAR_POST_TYPE, array( $this, 'save_phila_calendar_meta' ), 10, 2 );
	}

	/**
	 * Unset Phila Calendar Save, Trash and Before Delete Post actions
	 *
	 * @return void
	 */
	private function unset_actions(){
		remove_action( 'save_post_' . PHILA_CALENDAR_POST_TYPE, array( $this, 'save_phila_calendar_meta' ), 10, 2 );
	}

	/**
	 * Gets and Object and trasnform it to Array
	 *
	 * @param Object $object
	 * @return void
	 */
	public static function get_object_array( $object ) {
		$object_arr = get_object_vars( $object );
		$object_arr = wp_slash($object_arr);
		return $object_arr;
	}

	/**
	 * This function is executed when a Simple Calendar post has a status update, looks for the related Phila Calendar Post
	 * and updates it with the new status change.
	 *
	 * @param String $new_status
	 * @param String $old_status
	 * @param Object $post post, beign updated
	 * @return void
	 */
	public function on_all_status_transitions( $new_status, $old_status, $post ) {
		if( $post->post_type != 'calendar' && $post->post_type != PHILA_CALENDAR_POST_TYPE ) return;

		if ( $new_status !== $old_status ) {

			if( empty( $this->ids_being_changed ) ) {
				if ( isset( $_REQUEST['ids'] ) ) {
					$this->ids_being_changed = array_map( 'intval', explode( ',', $_REQUEST['ids'] ) );
				} elseif ( !empty( $_REQUEST['post'] ) ) {
					$this->ids_being_changed = array_map('intval', $_REQUEST['post']);
				}
			}

			if( empty( $this->ids_being_changed ) ) {
				$this->ids_being_changed = array();
			}

			$related_posts = $this->get_simple_calendar_related_id( $post->ID );
			$related_posts_ids = array_values( ( array ) $related_posts );

			if( ( $key = array_search( $post->ID, $related_posts_ids ) ) !== false) {
				unset( $related_posts_ids[ $key ] );
			}

			if( is_array( $related_posts_ids ) && ! empty( $related_posts_ids ) ){
				$this->unset_actions();

				global $wpdb;

				foreach( $related_posts_ids as $myid ) {
					if( ! in_array( $myid, $this->ids_being_changed ) ) {
						log_me( array( "post" => $post->ID, 'new_status' => $new_status ) );
						$wpdb->update( $wpdb->posts, array( 'post_status' => $new_status ), array( 'ID' => $myid ) );
						clean_post_cache( $myid );
					}
				}

				$this->set_actions();
			}
			
		}
	}

	/**
	 * This function upates a Simple Calendar Post when the master calendar is updated
	 *
	 * @param Object $master master post object
	 * @return void
	 */
	public function update_calendars_by_master( $master ) {
		if( is_object( $master ) ) {
			$master = $this->get_object_array( $master );
		}
		$calendar_view = get_post_meta( $master['ID'], '_calendar_view', true );

		if( ! empty( $calendar_view ) && is_array( $calendar_view ) ) {

			$key = key( $calendar_view );
			$value = $calendar_view[ $key ];

			// We get only PUBLISHED posts related to the master one.
			$query = new WP_Query( array(
				'post__not_in' => array( $master["ID"] ),
				'post_status' => 'publish',
				'post_type' => 'calendar',
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key'  => '_calendar_view',
						'value' => '"' . $key . '"',
						'compare' => 'LIKE'
					),
					array(
						'key'  => '_calendar_view',
						'value' => '"' . $value . '"',
						'compare' => 'LIKE'
					)
				)
			) );

			if( ! empty( $query->posts ) ) {
				foreach( $query->posts as $post ) {
					$postarr = $this->get_object_array( $post );
					$this->update_post_from_master_post( $postarr, $master );
				}
			}
		}
	}

	/**
	 * This function updates the Phila Calendar Google ID when its related post on Simple Calendar is Updated
	 *
	 * @param [type] $post_id
	 * @return void
	 */
	public function on_save_simple_calendar( $post_id ) {

		$to_update_ids = $this->get_simple_calendar_related_id( $post_id );
		if( is_array( $to_update_ids ) ){
			$calendar_id = get_post_meta( $post_id, '_google_calendar_id', true );
			if( ! empty( $calendar_id ) ) {
				if( is_numeric( $to_update_ids['phila'] ) ) {
					update_post_meta( $to_update_ids['phila'], "_phila_calendar_google_calendar_id",  $calendar_id );
				}
				if( is_numeric( $to_update_ids['list'] ) && $post_id != $to_update_ids['list']  ) {
					update_post_meta( $to_update_ids['list'], "_google_calendar_id", $calendar_id );
				}
				if( is_numeric( $to_update_ids['grid'] ) && $post_id != $to_update_ids['grid'] ) {
					update_post_meta( $to_update_ids['grid'], "_google_calendar_id", $calendar_id );
				}
			}
		}

		/**
		 * We check if it is a master post and update public sub-posts related to it.
		 */
		global $master_list, $master_grid;
		if( ( isset( $master_list ) && $master_list[ 'ID' ] !== $post_id ) 
			&& ( isset( $master_grid ) && $master_grid[ 'ID' ] !== $post_id ) ) {
			return false;
		}

		 $post = get_post( $post_id );

		 if( "calendar" !== $post->post_type ) {
			 return false;
		 }
		 
		 if( "master-list-calendar" == $post->post_name 
			|| "master-grid-calendar" == $post->post_name ) {
				$this->update_calendars_by_master( $post );
		 }
	}

	/**
	* This function gets the current post type in admin
	* Via @DomeicF
	* Github https://gist.github.com/DomenicF
	*/
	public static function get_current_post_type() {
		global $post, $typenow, $current_screen;
		//we have a post so we can just get the post type from that
		if ( $post && $post->post_type ) {
			return $post->post_type;
		}
		//check the global $typenow - set in admin.php
		elseif ( $typenow ) {
			return $typenow;
		}
		//check the global $current_screen object - set in sceen.php
		elseif ( $current_screen && $current_screen->post_type ) {
			return $current_screen->post_type;
		}
		//check the post_type querystring
		elseif ( isset( $_REQUEST['post_type'] ) ) {
			return sanitize_key( $_REQUEST['post_type'] );
		}
		//lastly check if post ID is in query string
		elseif ( isset( $_REQUEST['post'] ) ) {
			return get_post_type( $_REQUEST['post'] );
		}
		//we do not know the post type!
		return null;
	}

	/**
	 * This function is executed on init action to register the new Phila Calendar post type.
	 *
	 * @return void
	 */
	public function register_phila_calendar_post_type() {

		if( ! post_type_exists( PHILA_CALENDAR_POST_TYPE ) ) {
			// Register post_type
			$labels = array(
				'name'               => _x( 'Philly Calendars', 'post type general name', PHILA_CALENDAR_DOMAIN),
				'singular_name'      => _x( 'Philly Calendar', 'post type singular name', PHILA_CALENDAR_DOMAIN ),
				'menu_name'          => _x( 'Philly Calendars', 'admin menu', PHILA_CALENDAR_DOMAIN ),
				'name_admin_bar'     => _x( 'Philly Calendar', 'add new on admin bar', PHILA_CALENDAR_DOMAIN ),
				'add_new'            => _x( 'Add New', 'Philly Calendar', PHILA_CALENDAR_DOMAIN ),
				'add_new_item'       => __( 'Add New Philly Calendar', PHILA_CALENDAR_DOMAIN ),
				'new_item'           => __( 'New Philly Calendar', PHILA_CALENDAR_DOMAIN ),
				'edit_item'          => __( 'Edit Philly Calendar', PHILA_CALENDAR_DOMAIN ),
				'view_item'          => __( 'View Philly Calendar', PHILA_CALENDAR_DOMAIN ),
				'all_items'          => __( 'All Philly Calendar', PHILA_CALENDAR_DOMAIN ),
				'search_items'       => __( 'Search Philly Calendar', PHILA_CALENDAR_DOMAIN ),
				'parent_item_colon'  => __( 'Parent Philly Calendar:', PHILA_CALENDAR_DOMAIN ),
				'not_found'          => __( 'No Philly Calendar found.', PHILA_CALENDAR_DOMAIN ),
				'not_found_in_trash' => __( 'No Philly Calendar found in Trash.', PHILA_CALENDAR_DOMAIN )
			);

			$args = array(
				'labels'             => $labels,
				'description'        => __( 'Google calendars for phila.gov.', PHILA_CALENDAR_DOMAIN ),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => 'phila-calendar' ),
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'revisions'          => false,
				'menu_position'      => null,
				'menu_icon'          => 'dashicons-calendar',
				'register_meta_box_cb' => array($this, 'phila_calendar_add_metaboxes'),
				'supports'           => array( 'title', 'author' )
			);

			register_post_type( PHILA_CALENDAR_POST_TYPE, $args );
		}
	}

	/**
	 * This function adds the needed metaboxes to the new Phila Calendar post type
	 *
	 * @param Object $post
	 * @return void
	 */
	public function phila_calendar_add_metaboxes( $post ) {
		add_meta_box(
			'phila_calendar_configuration',
			__( 'Google Calendar', PHILA_CALENDAR_DOMAIN ),
			array( $this, 'render_meta_box_content' ),
			$post->post_type,
			'normal',
			'high'
		);
	}

	/**
	 * This function render the metaboxes on Phila Calendar (HTML content)
	 *
	 * @param  $post
	 * @return void
	 */
	public function render_meta_box_content( $post ) {
		if ( $post->post_type == PHILA_CALENDAR_POST_TYPE ) {
			wp_enqueue_script('jquery');
			wp_enqueue_script( 'phila_calendar_script', PHILA_CALENDAR_URL . 'js/phila-calendar.js' );
		}

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'phila_calendar_google_input_box', 'phila_calendar_google_input_nonce' );
 
		// Use get_post_meta to retrieve an existing value from the database.
		$calendar_id = $this->get_phila_calendar_meta_id( $post->ID );
		
		// Display the form, using the current value.
		?>
		<label for="phila_calendar_google_calendar_id">
			<?php _e( 'Google Calendar ID:', PHILA_CALENDAR_DOMAIN ); ?>
		</label>
		<input type="text" id="phila_calendar_google_calendar_id" name="phila_calendar_google_calendar_id" value="<?php echo esc_attr( $calendar_id ); ?>" style="width: 100%;" required />
		
		<!-- This funcionality is going to be added in the future -->
		<div style="display: none; visibility: hidden; opacity: 0; height: 0; width: 0;">
			<p>Calendar Style</p>
			<label>
				List
				<input type="radio" name="phila_calendar_style" value="list" checked>
			</label>
			<label>
				Grid
				<input type="radio" name="phila_calendar_style" value="grid">
			</label>
		</div>

		<p>
			Please enter your Google calendar ID, e.g., <i>r4vm98aemlik83vqmobdd677mk@group.calendar.google.com</i><br>
			The detailed instructions are <a href="https://docs.simplecalendar.io/find-google-calendar-id/" target="_blank">here</a>, Via <a href="https://docs.simplecalendar.io/" target="_blank">Simple Calendar documentation<a>
		</p>
		<?php
	}

	/**
	 * Get the value of _phila_calendar_google_calendar_id metadata
	 *
	 * @param int $post_id
	 * @return void
	 */
	public function get_phila_calendar_meta_id( $post_id ) {
		$calendar_id = get_post_meta( $post_id, "_phila_calendar_google_calendar_id", true );
		return ( ! empty( $calendar_id ) ) ? base64_decode( $calendar_id ) : "";
	}

	/**
	 * With a given Phila Calendar post ID, we look of the Simple Calendar related post and return its ID
	 *
	 * @param int $post_id
	 * @return void
	 */
	public function get_simple_calendar_related_id( $post_id ) {
		if( ! is_numeric( $post_id ) )
		{
			return false;
		}else{
			$calendar_ids = get_post_meta( $post_id, "_phila_calendar_calendar_id", true );

			if( ! is_array( $calendar_ids ) )
			{
				return false;
			}else{
				return $calendar_ids;
			}
		}

		return null;
	}

	/**
	 * This functions creates a new Simple Calendar Post from its master, but with some Phila Calendar values
	 * Author, Title, Status and Name
	 *
	 * @param string $layout
	 * @param Object $post_related
	 * @return void
	 */
	public function clone_master_calendar( $layout = 'list', $post_related ) {
		
		if( ! isset($post_related) || ! is_a( $post_related, 'WP_Post' ) ) {
			return null;
		}

		global $master_grid, $master_list;

		$master = null;
		if( 'list' === $layout && ! empty( $master_list ) ) {
			$master = $master_list;
		}

		if( 'grid' === $layout && ! empty( $master_grid ) ) {
			$master = $master_grid;
		}

		if( empty( $master ) ) {
			$master = self::get_master_calendar( $layout );
		}

		if ( ! empty( $master ) ) {

			$post_id = $master["ID"];
			if ( 'calendar' == $master["post_type"] ) {
				$master["post_author"] = $post_related->post_author;
				$master["post_title"]  = $post_related->post_title;
				$master["post_status"] = $post_related->post_status;
				$master["post_name"]   = $post_related->post_name . '-' . $layout;

				unset( $master["ID"] );
				unset( $master["guid"] );
				unset( $master["comment_count"] );

				$duplicate_id = wp_insert_post( $master );
				
				$taxonomies = get_object_taxonomies( $master["post_type"] );
				foreach ( $taxonomies as $taxonomy ) {
					$terms = wp_get_post_terms( $post_id, $taxonomy, array( 'fields' => 'names' ) );
					wp_set_object_terms( $duplicate_id, $terms, $taxonomy );
				}

				$custom_fields = get_post_custom( $post_id );
				foreach ( $custom_fields as $key => $value ) {
					add_post_meta( $duplicate_id, $key, maybe_unserialize( $value[0] ) );
				}

				return $duplicate_id;
			}

			return null;
		}

		return null;
	}

	/**
	 * This function updates a Simple Calendar Post from the Master Calendar Post
	 *
	 * @param Array/Object $postarr representing the Post Object or Array we are going to update
	 * @param Mix $master Rrepresenting the Master Post we are using for the update
	 * @return int ID del post or Null if an error occurs
	 */
	function update_post_from_master_post( $postarr, $master ) {
		if( ! is_object( $postarr ) && ! is_array( $postarr ) ) {
			return null;
		}

		if( ! is_object( $master ) && ! is_array( $master ) ) {
			return null;
		}

		if( is_object( $postarr ) ){
			$postarr = self::get_object_array( $postarr );
		}

		if( is_object( $master ) ){
			$master = self::get_object_array( $master );
		}

		$postarr['post_content'] = $master['post_content'];

		$post_id = wp_update_post( $postarr, true );
		if (is_wp_error($post_id)) {
			$errors = $post_id->get_error_messages();
			log_me( array( 'Object' => $this, 'message' => $errors, 'Post to update' => $postarr, 'From post' => $master ) );
			return null;
		}

		$taxonomies = get_object_taxonomies( $master["post_type"] );
		foreach ( $taxonomies as $taxonomy ) {
			wp_set_object_terms( $post_id, null, $taxonomy );
			
			$terms = wp_get_post_terms( $master["ID"], $taxonomy, array( 'fields' => 'names' ) );
			wp_set_object_terms( $post_id, $terms, $taxonomy );
		}

		$custom_fields = get_post_custom( $master["ID"] );

		foreach ( $custom_fields as $key => $value ) {
			if( $key !== '_google_calendar_id' ) {
				update_post_meta( $post_id, $key, maybe_unserialize( $value[0] ) );
			}
		}
		
		return $post_id;
	}

	/**
	 * This functions takes an existing Simple Calendar Post from a Simple Calendar Master Post
	 * If it gets a "from_post" value, it should be an object and it means we have to update the post_author, 
	 * post_title, post_name, post_date and post_date_gmt from this given post.
	 * 
	 * @param object $post
	 * @param string $layout
	 * @param object $from_post
	 * @return void
	 */
	function update_post_from_master( $post, $layout = 'list', $from_post = null ) {
		if( ! is_a( $post, 'WP_Post' ) || empty( $post ) ) {
			return null;
		}

		global $master_grid, $master_list;

		$master = null;
		if( 'list' === $layout && ! empty( $master_list ) ) {
			$master = $master_list;
		}

		if( 'grid' === $layout && ! empty( $master_grid ) ) {
			$master = $master_grid;
		}

		if( empty( $master ) ) {
			$master = self::get_master_calendar( $layout );
		}
		
		if ( ! empty( $master ) ) {
			$postarr = array('ID' => $post->ID);
			
			if( is_a( $from_post, 'WP_Post' ) ) {
				$postarr['post_author']    = $from_post->post_author;
				$postarr['post_title']     = $from_post->post_title;
				$postarr['post_name']      = $from_post->post_name . '-' . $layout;
				$postarr['post_status']    = $from_post->post_status;
				$postarr['post_date']      = $from_post->post_date;
				$postarr['post_date_gmt']  = $from_post->post_date_gmt;
			}

			return $this->update_post_from_master_post( $postarr, $master );
		}

		return null;
	}

	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save_phila_calendar_meta( $post_id, $post ) {
		/*
		* In production code, $slug should be set only once in the plugin,
		* preferably as a class property, rather than in each function that needs it.
		*/
		$post_type = get_post_type( $post_id );

		// If this isn't a 'Phila Calendar' post, don't update it.
		if ( PHILA_CALENDAR_POST_TYPE != $post_type ) {
			return null;
		};

		/*
		 * If this is an autosave, our form has not been submitted,
		 * so we don't want to do anything.
		 */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
 
		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */
		 
		 $_is_quick_edit = ( isset( $_POST['_inline_edit'] ) ) ? true : false;
		 
		 if( ! $_is_quick_edit ) {

			// Check if our nonce is set.
			if ( ! isset( $_POST['phila_calendar_google_input_nonce'] ) ) {
				return $post_id;
			}
	
			$nonce = $_POST['phila_calendar_google_input_nonce'];
	
			// Verify that the nonce is valid.
			if ( ! wp_verify_nonce( $nonce, 'phila_calendar_google_input_box' ) ) {
				return $post_id;
			}

		 } else {

			if ( ! wp_verify_nonce( $_POST[ '_inline_edit' ], 'inlineeditnonce' ) ) {
				return $post_id;
			}

		 }
 
		// Check the user's permissions.
		if ( ! current_user_can( 'edit_posts', $post_id ) ) {
			return $post_id;
		}
 
		/* OK, it's safe for us to save the data now. */

		// Sanitize the user input.
		if( ! $_is_quick_edit ) {
			$google_calendar_id = sanitize_text_field( $_POST['phila_calendar_google_calendar_id'] );
		}else{
			$google_calendar_id = $this->get_phila_calendar_meta_id( $post_id );
		}

		if( empty( $google_calendar_id ) ) {
			$message = __( "Google Calendar ID for post : $post_id can't be empty" , PHILA_CALENDAR_DOMAIN );
			jp_notices_add_error($message);

			return $post_id;
		}
		

		$calendar_ids    = $this->get_simple_calendar_related_id( $post_id );
		$cloned_post_ids = array();
		
		$this->unset_actions();
		$attempt         = '';
		if( ! is_array( $calendar_ids ) ) {
			//There is not calendar related post, then I have to create it.
			$cloned_post_ids['list'] = $this->clone_master_calendar( 'list', $post );
			$cloned_post_ids['grid'] = $this->clone_master_calendar( 'grid', $post );
			$attempt = 'Clone. meta doesn\'t Exists';
		}else{
			$cloned_post_list = get_post( $calendar_ids['list'] );
			$do_list = false;
			if( ! is_a( $cloned_post_list, 'WP_Post' ) ) {
				$do_list = true;
			}elseif( $cloned_post_list->post_type !== 'calendar' ){
				$do_list = true;
			}else{
				//Meta ID exists, the post exists and it is the correct post type (Simple Calendar)
				$cloned_post_ids['list'] = $this->update_post_from_master( get_post( $calendar_ids['list'] ), 'list', $post );
				simcal_delete_feed_transients($cloned_post_ids['list']);
				$attempt = 'Update. meta exists, post exists, it is correct post type';
			}

			$cloned_post_grid = get_post( $calendar_ids['grid'] );
			$do_grid = false;
			if( ! is_a( $cloned_post_grid, 'WP_Post' ) ) {
				$do_grid = true;
			}elseif( $cloned_post_grid->post_type !== 'calendar' ){
				$do_grid = true;
			}else{
				//Meta ID exists, the post exists and it is the correct post type (Simple Calendar)
				$cloned_post_ids['grid'] = $this->update_post_from_master( get_post( $calendar_ids['grid'] ), 'grid', $post );
				simcal_delete_feed_transients($cloned_post_ids['grid']);
				$attempt = 'Update. meta exists, post exists, it is correct post type';
			}
		   
			if( $do_list ) {
				$attempt = 'Update. meta exists, post doesn\'t exists or is a wrong type, LIST';
				$cloned_post_ids['list'] = $this->clone_master_calendar( 'list', $post );
			}

			if( $do_list ) {
				$attempt = 'Update. meta exists, post doesn\'t exists or is a wrong type, GRID';
				$cloned_post_ids['grid'] = $this->clone_master_calendar( 'grid', $post );
			}
		}

		if( ! is_numeric( $cloned_post_ids['list'] ) || ! is_numeric( $cloned_post_ids['grid'] ) )
		{
			$message = __( 'ATTENTION! We couldn\'t create the calendar relation with Simple Calendar', PHILA_CALENDAR_DOMAIN ) . '<br>' . __( 'Please try again doing click on "Publish" if error persists please contact your web administrator.', PHILA_CALENDAR_DOMAIN );
			jp_notices_add_error($message);
			
			// Web log the error for future purposes
			log_me( array( 'Object' => $this, 'message' => 'There was an error duplicating calendar master from Simple Calendar', 'Phila Calendar Post' => $post, 'Attempt' => $attempt ) );
		}else{
			$cloned_post_ids['phila'] = $post_id;
			update_post_meta( $cloned_post_ids['list'], '_google_calendar_id', base64_encode( $google_calendar_id ) );
			update_post_meta( $cloned_post_ids['grid'], '_google_calendar_id', base64_encode( $google_calendar_id ) );


			update_post_meta( $cloned_post_ids['list'], "_phila_calendar_calendar_id", $cloned_post_ids );
			update_post_meta( $cloned_post_ids['grid'], "_phila_calendar_calendar_id", $cloned_post_ids );
			update_post_meta( $post_id, "_phila_calendar_calendar_id", $cloned_post_ids );
		}

		// Update the meta field.
		if( ! $_is_quick_edit ) {
			update_post_meta( $post_id, "_phila_calendar_google_calendar_id", base64_encode( $google_calendar_id ) );
		}
		$this->set_actions();
	}

	/**
	 * Get a required master Simple Calendar object
	 *
	 * @param string $layout
	 * @return void
	 */
	public static function get_master_calendar( $layout = 'list' ) {
		$slug = 'master-list-calendar';
		if( 'grid' == $layout ) {
			$slug = 'master-grid-calendar';
		}

		$args = array(
			'name'        => $slug,
			'post_type'   => 'calendar',
			'post_status' => 'publish',
			'numberposts' => 1
		);

		$post = get_posts($args);
		if( isset( $post[0] ) ) {
			return self::get_object_array( $post[0] );
		}else{
			return false;
		}
	}

	/**
	 * We run some validations when plugin is loaded, we neet to know that master posts are created
	 *
	 * @return void
	 */
	function phila_calendar_validations() {
		if( self::get_current_post_type() !== PHILA_CALENDAR_POST_TYPE ) {
			return false;
		};

		global $master_grid, $master_list;
		$master_list = self::get_master_calendar( 'list' );
		$master_grid = self::get_master_calendar( 'grid' );

		if( empty( $master_list ) ) {
			add_action( 'admin_notices', function() {
				echo '<div class="error"><p>' .
						__( 'ATTENTION! please tell your web administrator that "Simple Calendar Master List is missing!", this plugin only works if the calendar master list page is created.', PHILA_CALENDAR_DOMAIN ) .
						'<br>' .
						__( 'We encourage you not to keep using this service until the missing resources are created.', PHILA_CALENDAR_DOMAIN ) .
						'</p></div>';
			} );
		}

		if( empty( $master_grid ) ) {
			add_action( 'admin_notices', function() {
				echo '<div class="error"><p>' .
						__( 'ATTENTION! please tell your web administrator that "Simple Calendar Master Grid is missing!", this plugin only works if the calendar master grid page is created.', PHILA_CALENDAR_DOMAIN ) .
						'<br>' .
						__( 'We encourage you not to keep using this service until the missing resources are created.', PHILA_CALENDAR_DOMAIN ) .
						'</p></div>';
			} );
		}
	}
}
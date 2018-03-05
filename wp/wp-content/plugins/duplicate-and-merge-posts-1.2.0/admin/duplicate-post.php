<?php

/* Usage

require_once 'includes/duplicate-post.php';

// Initialize ( optinaly pass array of options )
DuplicatePost::_init(array(
	"duplicate_post_title_prefix" => "Clone :: "
));

// To duplicate a post
DuplicatePost::_duplicate_post(753);

// To save back changes to original post, and delete clone
DuplicatePost::_save_to_original(2928);

*/

class DuplicatePost{

	// singleton instance
	private static $instance;

	private $options;
	private $default_options = array(
		"duplicate_post_copyexcerpt" => true,
		"duplicate_post_copyattachments" => false,
		"duplicate_post_copychildren" => false,
		"duplicate_post_copystatus" => false,
		"duplicate_post_taxonomies_blacklist" => array(),
		"duplicate_post_show_row" => true,
		"duplicate_post_show_adminbar" => true,
		"duplicate_post_show_submitbox" => true,
		"duplicate_post_copydate" => false,
		"duplicate_post_blacklist" => "",
		"duplicate_post_title_prefix" => "Copy of: ",
		"duplicate_post_title_suffix" => "",
		"duplicate_post_roles" => array("administrator"),
		"duplicate_post_global_admins" => false,
		"duplicate_post_add_nofollow_noindex" => true
	);

	private function __construct(){ }

	private function init( $options_to_set=null ){
		if($options_to_set == null) $options_to_set = array();

		$this->options = array_merge( $this->default_options, $options_to_set );
		$this->add_filters();
		$this->add_actions();
	}

	// getInstance method
	public static function getInstance() {
		if(!self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	function get_option( $option_name ){
		return isset($this->options[$option_name]) ?$this->options[$option_name] :"";
	}


	/**
	 * Test if the user is allowed to copy posts
	 */
	static function duplicate_post_is_current_user_allowed_to_copy() {
		global $post;
			$qo = get_queried_object();
			$allowed = false;
			$settings = get_option('dem_main_settings');
			$current_user = wp_get_current_user();

			if($qo){
				$authorID = isset($qo->post_author) ? $qo->post_author : '';
				$postType = isset($qo->post_type) ? $qo->post_type : '';
			} else {
				if(isset($post)){
					$authorID = $post->post_author;
					$postType = $post->post_type;
				} else {
					$authorID = 0;
				}
			}

			$current_roles = $current_user->roles;

			foreach ($current_roles as $key => $role) {
				//echo $value;
				if(isset($settings['edit_access'])) {
					if(in_array($role, $settings['edit_access'])){
					$allowed = true;
					//echo "YES". $value;
					}
				}

			}

			/* by default post author can copy but not merge back */
			if($current_user->ID == $authorID){
				$allowed = true;
			}

			/* Exclude post types from settings */
			if(isset($settings['exclude_post_types'])) {
				if(is_array($settings['exclude_post_types'])){
					if(in_array($postType, $settings['exclude_post_types'])){
						$allowed = false;
					}
				}
			}


		return apply_filters( "duplicate_post_is_allowed", $allowed );
	}

	/**
	 * Test if the user can merge back
	 */
	static function duplicate_post_is_current_user_allowed_to_merge_back() {
		$allowed = false;
		$settings = get_option('dem_main_settings');
			$current_user = wp_get_current_user();
			$current_roles = $current_user->roles;
			foreach ($current_roles as $key => $role) {
				//echo $value;
				if(in_array($role, $settings['merge_access'])){

					$allowed = true;
					//echo "YES". $value;
				}
			}

		return apply_filters( "duplicate_post_is_allowed_merge_back", $allowed );
	}


	public static function _init( $options_to_set=null ){
		if(!self::$instance) {
			self::getInstance()->init( $options_to_set );
		}else{
			throw new Exception('Already initialized');
		}
	}
	public static function _duplicate_post( $post_id ){
		if(self::$instance) {
			self::getInstance()->duplicate_post($post_id);
		}else{
			throw new Exception('Not Initialized');
		}
	}
	public static function _save_to_original( $post_id ){
		if(self::$instance) {
			self::getInstance()->save_to_original($post_id);
		}else{
			throw new Exception('Not Initialized');
		}
	}


	function add_nofollow_noindex_to_clones(){
		if(!$this->get_option("duplicate_post_add_nofollow_noindex")) return;

		$qo = get_queried_object();
		$id = isset($qo->ID) ?$qo->ID :false;

		if($id){
			$original_post_id = get_post_meta($id, '_dp_original', true);

			if($original_post_id){
				?>

				<!-- NOFOLLOW, NOINDEX -->
				<meta name="robots" content="noindex,nofollow">

				<?php
			}
		}

	}

	// Template tag
	/**
	 * Retrieve duplicate post link for post.
	 *
	 *
	 * @param int $id Optional. Post ID.
	 * @param string $context Optional, default to display. How to write the '&', defaults to '&amp;'.
	 * @param string $draft Optional, default to true
	 * @return string
	 */
	static function duplicate_post_get_clone_post_link( $id = 0, $context = 'display', $draft = true ) {
		if ( !DuplicatePost::duplicate_post_is_current_user_allowed_to_copy() )
		return;

		if ( !$post = get_post( $id ) )
		return;

		if ($draft)
		$action_name = "duplicate_post_save_as_new_post_draft";
		else
		$action_name = "duplicate_post_save_as_new_post";



		if ( 'display' == $context )
		$action = '?action='.$action_name.'&amp;post='.$post->ID;
		else
		$action = '?action='.$action_name.'&post='.$post->ID;

		$post_type_object = get_post_type_object( $post->post_type );
		if ( !$post_type_object )
		return;

		return apply_filters( 'duplicate_post_get_clone_post_link', admin_url( "admin.php". $action ), $post->ID, $context );
	}
	/**
	 * Display duplicate post link for post.
	 *
	 * @param string $link Optional. Anchor text.
	 * @param string $before Optional. Display before edit link.
	 * @param string $after Optional. Display after edit link.
	 * @param int $id Optional. Post ID.
	 */
	static function duplicate_post_clone_post_link( $link = null, $before = '', $after = '', $id = 0 ) {
		if ( !$post = get_post( $id ) )
		return;

		if ( !$url = DuplicatePost::duplicate_post_get_clone_post_link( $post->ID ) )
		return;

		if ( null === $link )
		$link = __('Copy to a new draft', 'dem');

		$post_type_obj = get_post_type_object( $post->post_type );
		$link = '<a class="post-clone-link button" style="margin-bottom:5px;width:100%;text-align:center;" href="' . esc_attr( $url ) . '" title="'
		. esc_attr(__("Submit an update to this doc", 'dem'))
		.'">' . $link . '</a>';
		echo $before . apply_filters( 'duplicate_post_clone_post_link', $link, $post->ID ) . $after;
	}



	public function remove_default_post_actions_for_cloned_docs() {
		global $pagenow ;

		if ( $pagenow  == 'post.php' && isset( $_GET['action'] ) && $_GET['action'] == 'edit' && isset( $_GET['post'] ) ) {

			$original_post_id = get_post_meta($_GET['post'], '_dp_original', true);
			if($original_post_id){
				?>

				<style type="text/css">
					#duplicate-action, #delete-action, #publishing-action{ display:none; }
				</style>

				<script type="text/javascript">
					jQuery(document).ready(function($){
						$("#duplicate-action, #delete-action, #publishing-action").remove();
					});
				</script>

				<?php
			}
		}
	}

	public function add_cloned_doc_action_buttons(){
		global $pagenow, $post;

		if ( $pagenow  == 'post.php' && isset( $_GET['action'] ) && $_GET['action'] == 'edit' && isset( $_GET['post'] ) ) {
			$submitted_count = get_post_meta($post->ID,"_dp_submited", true);
			$original_post_id = get_post_meta($_GET['post'], '_dp_original', true);


			if($original_post_id){
				$label = "Update";
				$allow_submit_for_review = apply_filters( 'duplicate_post_allow_submit_for_review', DuplicatePost::duplicate_post_is_current_user_allowed_to_copy(), $original_post_id );
				$allow_merge_back = DuplicatePost::duplicate_post_is_current_user_allowed_to_merge_back();

				$merge_label = "Merge into original ".get_post_type_object(get_post_type())->labels->singular_name  . "";
				$submit_label = "Submit update for review";

			$settings = get_option('dem_main_settings');
			$current_user = wp_get_current_user();
			$current_roles = $current_user->roles;
			foreach ($current_roles as $key => $value) {
				//echo $value;
				if(in_array($value, $settings['merge_access'])){
					$allowed = true;
					//echo "YES". $value;
				}
			}

				?>
				<div id="publishing-action-update">
						<input name="original_publish" type="hidden" id="original_publish" value="Update">
							<a class="button" href="<?php echo esc_url( get_permalink( $original_post_id ) ); ?>">Go to original <?php echo get_post_type_object(get_post_type())->labels->singular_name; ?></a>

						<?php if ($allow_submit_for_review ): ?>
							<?php $class = "";

							if($submitted_count > 0 ){
								$class=" waiting";
								$submit_label = "Submit for another review";
							?>
							<span id="update-waiting">Update submitted, awaiting approval</span>
							<?php } ?>
							<?php wp_nonce_field( basename( __FILE__ ), 'dem_nonce' );

							$value = get_post_meta($post->ID, 'dem_notify_emails', true);?>

							<textarea name="dem_notify_emails" id="dem_notify_emails" type="textarea" size="20" placeholder="Enter email address(es) of users to review changes. One email per line." class="align-left" value="<?php echo esc_attr( get_post_meta( $post->ID, 'dem_notify_emails', true ) ); ?>" size="30" /><?php echo esc_attr( get_post_meta( $post->ID, 'dem_notify_emails', true ) ); ?></textarea>

							<input name="submit_for_review" type="submit" class="button button-primary button-large<?php echo $class;?>" id="submit_for_review" value="<?php echo $submit_label; ?>">

						<?php endif ?>

						<?php if ($allow_merge_back): ?>
							<input name="merge_back" type="submit" class="button button-primary button-large" id="merge_back" value="<?php echo $merge_label; ?>">

							<input name="save_as_new" type="submit" class="button save_as_new button-primary button-large" id="save_as_new" value="Save as new <?php echo get_post_type_object(get_post_type())->labels->singular_name;?>">
							<input name="save_as_new_id" type="hidden" id="save_as_new_id" value="<?php echo esc_attr( $original_post_id );?>">
						<?php endif ?>
				</div>
				<style type="text/css">
				#publishing-action-update .align-left{
					text-align: left;
				}
					#update-waitng{
						text-align:center; display:block;
					}
					#publishing-action-update >*{
						margin-bottom:5px;
						width:100%;
						text-align: center;
					}
					.button.button-primary.button-large.save_as_new {
						-webkit-box-shadow: none;
						box-shadow: none;
						color: #fff;
						text-decoration: none;
						}
					.button.button-primary.button-large.waiting {
						-webkit-box-shadow: none;
						box-shadow: none;
						color: #fff;
						text-decoration: none;
					}
					#merge_back{
						background: rgb(0, 144, 0);
						border-color: rgb(0, 144, 0);
					}
					#save_as_new{
						background: #5E757E;
						border-color: #5E757E;
					}
				</style>
			<script type="text/javascript">
				jQuery(document).ready(function($){
						$("#merge_back, #submit_for_review").on('click', function(){
							if(acf && acf.validation){
							acf.validation.$trigger = $(this);
						}
					});
				});
			</script>
				<?php
			}
		}
	}

	/* Save the meta box's post metadata. */
	public function dem_save_email ( $post_id, $post ) {

		/* Verify the nonce before proceeding. */
		if ( !isset( $_POST['dem_nonce'] ) || !wp_verify_nonce( $_POST['dem_nonce'], basename( __FILE__ ) ) )
			return $post_id;

		/* Get the post type object. */
		$post_type = get_post_type_object( $post->post_type );

		/* Check if the current user has permission to edit the post. */
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
			return $post_id;

		/* Get the posted data and sanitize it  */
		$new_meta_value = ( isset( $_POST['dem_notify_emails'] ) ? wp_kses_post( $_POST['dem_notify_emails'] ) : '' );

		/* Get the meta key. */
		$meta_key = 'dem_notify_emails';

		/* Get the meta value of the custom field key. */
		$meta_value = get_post_meta( $post_id, $meta_key, true );

		/* If a new meta value was added and there was no previous value, add it. */
		if ( $new_meta_value && '' == $meta_value )
			add_post_meta( $post_id, $meta_key, $new_meta_value, true );

		/* If the new meta value does not match the old value, update it. */
		elseif ( $new_meta_value && $new_meta_value != $meta_value )
			update_post_meta( $post_id, $meta_key, $new_meta_value );

		/* If there is no new meta value but an old value exists, delete it. */
		elseif ( '' == $new_meta_value && $meta_value )
			delete_post_meta( $post_id, $meta_key, $meta_value );
	}


	public function cloned_post_save( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;
 // $new_post_id = get_post_meta($post_id, '_dp_original_backup', true);
 // $original_post_id = get_post_meta($post_id, '_dp_original', true);
 // echo $post_id;
 // echo $new_post_id;
 // echo "<br>xxx";
 // echo $original_post_id;
 // exit;
		/* Save as new Post */
		if(isset($_POST["save_as_new"])) {
			//echo $_POST["save_as_new"];
			$original_post_id = get_post_meta($post_id, '_dp_original', true);
			if(!$original_post_id){
				$original_post_id = absint( $_POST['save_as_new_id'] );
			}
			update_post_meta($post_id, "_dp_original_backup", $original_post_id);
			/* then delete */
			delete_post_meta($post_id, '_dp_original');
		}
		if(isset($_POST['revert_back_to_cloned'])) {
			$o_post_id = get_post_meta($post_id, '_dp_original_backup', true);

			// Original post ID from meta should be an integer.
			if ( 0 !== absint( $o_post_id ) ) {
				update_post_meta($post_id, "_dp_original", $o_post_id);
			}
			//echo 'yes' . $original_post_id;
		}
		if(isset($_POST['unlink_post_forever'])) {
			delete_post_meta($post_id, '_dp_original_backup');
			//echo 'yes' . $original_post_id;
		}
		//exit;
		// Only allow if its a merge back or submit for review
		if(!isset($_POST["merge_back"]) && !isset($_POST["submit_for_review"]))
			return;

		$original_post_id = get_post_meta($post_id, '_dp_original', true);

		$allow_submit_for_review = DuplicatePost::duplicate_post_is_current_user_allowed_to_copy();
		$allow_merge_back = DuplicatePost::duplicate_post_is_current_user_allowed_to_merge_back();

		if($original_post_id){

			if(isset($_POST["merge_back"])){
				if( $allow_merge_back ){

					delete_post_meta($_POST['post_ID'], '_dp_original_backup');
					// Unset the merge_back to prevent infinite loop after calling _save_to_original()

					unset($_POST["merge_back"]);

					DuplicatePost::_save_to_original( $post_id );
			$redirect_url = add_query_arg( array( 'post' => absint( $original_post_id ), 'action' => 'edit' ), admin_url( 'post.php' ) );
					wp_redirect( $redirect_url );
					die();

				}
			}else if(isset($_POST["submit_for_review"])){

				if( $allow_submit_for_review ){
					//echo 'save'; exit;

			// Any non numeric value will be 0.
			$submitted_count = absint( get_post_meta($post_id,"_dp_submited", true) );
			$submitted_count++;

					// Only notify users the first time it is submitted
					//if( get_post_meta($post_id,"_dp_submited", true) != "yes" ){

						update_post_meta($post_id, "_dp_submited", $submitted_count);

						global $current_user;
						get_currentuserinfo();

						//----------  // Notify local admins, if none, notify global when an update is submitted //  ----------//
					if ( get_post_meta( $post_id, 'dem_notify_emails' ) != null ) {
						$global_admin_emails = get_post_meta( $post_id, 'dem_notify_emails' );
					} elseif ($this->get_option('duplicate_post_global_admins') != false){
						$global_admin_emails = $this->get_option('duplicate_post_global_admins');
					}else{
						$global_admin_emails = explode("\n",get_field("global_admin_emails","options"));
					}
					if(count($global_admin_emails) == 0){
						$global_admin_emails = get_bloginfo('admin_email');
					}
						$post = get_post($original_post_id);
						$new_post = get_post($post_id);

						$message = implode( array(
						"<strong>".$current_user->display_name . "</strong> has requested changes to: ",
						"<a href='".get_permalink($post->ID)."'>'".$post->post_title."'</a> ",
						"<br><br> To review and approve the change, follow this link: ",
						"<a href='".get_permalink($new_post->ID)."'>'".$new_post->post_title."'</a>.<br /><br /> <i>Note: You must be logged in to the website in order to view changes.</i>"
					) );

						$message = apply_filters("duplicate_post_notification_message", $message, $post, $new_post, $current_user );

						if(!$message == false){
								$from_name = get_option( 'blogname' , '');
					$from_email = get_option( 'admin_email' );
					$headers	= "From: " . $from_name . " <" . $from_email . ">\n";
					$headers .= 'Content-type: text/html';
					/* todo add filters here */
							wp_mail( $global_admin_emails, "[Post Update] - There is a new update pending review", $message, $headers );

					}

					//}
				}
			}

		}
	}




	public function register_my_custom_submenu_page() {
		add_submenu_page( '', 'Show Differences', 'Show Differences', 'read', 'show-diff', array($this,'show_diff_callback') );
	}

	/* Side by side difference page content */
	public function show_diff_callback() {

		$allow_merge_back = DuplicatePost::duplicate_post_is_current_user_allowed_to_merge_back();

		echo '<div class="wrap" id="show_diff_wrapper">';
			echo '<div id="icon-tools" class="icon32"></div><h2>Edit Differences</h2>';

		if(!isset($_GET["post"])){
			echo "<div id='message' class='error'><p>Post ID is required</p></div>";
			return;
		}

		$post_id = absint( $_GET["post"] );
		$post = get_post($post_id);
		if(!$post){
			echo "<div id='message' class='error'><p>Post does not exist or is already merged back</p></div>";
			return;
		}
		if($post->post_status == "trash"){
			echo "<div id='message' class='error'><p>Post already been merged back</p></div>";
			return;
		}

		$original_post_id = get_post_meta($post_id, '_dp_original', true);

		if(!$original_post_id){
			echo "<div id='message' class='error'><p>This post does not have an original post id</p></div>";
			return;
		}

		$merge_back_link = admin_url()."?action=duplicate_post_save_to_original&post=".$post_id;
		echo "<div class='show_diff_actions'>";
			echo "<a class='button' href='".get_edit_post_link($original_post_id)."'>View original" . get_post_type_object(get_post_type())->labels->singular_name . "</a>";
			echo "<a class='button' href='".get_edit_post_link($post_id)."'>View duplicated" . get_post_type_object(get_post_type())->labels->singular_name  ."</a>";
			if($allow_merge_back){
				 echo "<a class='button button-primary' href='". esc_url( $merge_back_link ) ."'>Merge back to Original " . get_post_type_object(get_post_type())->labels->singular_name  ."</a>";
			}

		echo "</div>";

		echo $this->get_side_by_side_diff($post_id, $original_post_id);

		echo '</div>';
		?>
			<style type="text/css">
				#show_diff_wrapper{
					position:relative;
				}
				#show_diff_wrapper h2{
					margin-bottom:20px;
				}
				#show_diff_wrapper .show_diff_actions{
					position:absolute;
					top:10px;
					right:0px;
				}
				#show_diff_wrapper .show_diff_actions a{
					margin-right:10px;
				}
				#show_diff_wrapper .show_diff_actions a.button-primary{
					margin-right:0px;
				}
			</style>
		<?php
	}


	/* Generate and return, side by side difference html */
	private function get_side_by_side_diff( $post_id, $original_post_id){
		ob_start();

		$post = get_post($post_id);
		$original_post = get_post($original_post_id);

		$fields = array(
		array("type"=>"wp", "name"=>"title"),
		array("type"=>"wp", "name"=>"content")
		);

		// Get all acf field groups
		/*$field_groups = get_posts(array("post_type"=>"acf", "posts_per_page"=>-1));
		foreach($field_groups as $field_group){
			$fields = array_merge($fields, $this->get_acf_field_for_diff( $field_group->ID ) );
		}*/

		// Get ACF Fields of the original post and add them to fields array
		$acf_meta = get_post_custom( $original_post_id );
		$acf_field_keys = array();
		foreach ( $acf_meta as $key => $val ) {
				if ( preg_match( "/^field_/", $val[0] ) ) {
					if (function_exists('get_field_object')) {
					$acf_field = get_field_object( $val[0] );
					if($acf_field["type"] == "tab" || in_array($acf_field["key"], $acf_field_keys) ) continue;

					$acf_field_keys[] = $acf_field["key"];
					$fields[] = array("type"=>"acf", "name"=>$acf_field["key"]);
				}
				}
		}

		// Get post taxonomies
		$fields = array_merge($fields, $this->get_post_taxonomies_for_diff( $post ) );

		// Include two sample files for comparison
		echo "<div class='all_differences'>";
		echo "<table width='100%' class='Differences diff_header'><tr>
			<th></th>
			<td>Old: <a href='".get_edit_post_link($original_post)."'>".$original_post->post_title."</a></td>
			<th></th>
			<td>New: <a href='".get_edit_post_link($post)."'>".$post->post_title."</a></td>
		</tr></table>";
		foreach($fields as $field){

		$a = $this->get_string_value( $field ,$original_post_id, $original_post );
		$b = $this->get_string_value( $field ,$post_id         , $post );
		$field["label"] = ucfirst($field["label"]);

		$diff_html = wp_text_diff( $a, $b );

		if($diff_html != ""){
			?>
			<h3 class="diff_field_title"><?php echo $field["label"]; ?></h3>
			<?php
			echo $diff_html;
		}
		}
		echo "</div>";

		?>
		<style type="text/css">
		.ChangeReplace, .Differences.DifferencesSideBySide {max-width: 100%;}
		.all_differences{
			background:white;
			padding:15px;
			box-shadow: 0 1px 3px rgba(0,0,0,.1);
		}
		.Differences {
			width:100%;
		}
		.Differences td{
			width:46%;
		}
		.Differences .Left{
		}
		.Differences .ChangeInsert .Right{
			background:#e9ffe9;
		}
		.Differences .ChangeInsert .Right ins{
			background:#afa;
		}
		.Differences .ChangeDelete .Left{
			background:#ffe9e9;
		}
		.Differences .ChangeDelete .Left del{
			background:#faa;
		}
		.Differences .ChangeReplace .Right{
			background:#e9ffe9;
		}
		.Differences .ChangeReplace .Right ins{
			background:#afa;
		}
		.Differences .ChangeReplace .Left{
			background:#ffe9e9;
		}
		.Differences .ChangeReplace .Left del{
			background:#faa;
		}
		.Differences tr td{
			padding:5px 5px;
		}
		.Differences th:first-child{
			display:none;
		}
		.Differences th{
			text-indent:-100000;
			width:4%;
			color:white;
			font-size:0px;
		}
		.diff_field_title{
			margin-top:20px;
			padding-bottom:5px;
			border-bottom:1px solid rgba(0,0,0,0.1);
		}
		.diff_header{
			padding-top:20px;
			text-align: center;
			font-size:18px;
		}
		</style>
		<?php

		return ob_get_clean();
	}

	/* Get formated acf fields that belong to a field group */
	private function get_acf_field_for_diff( $field_group ){
		$acf_fields = $this->my_acf_get_fields_in_group( $field_group );
		$fields = array();
		foreach($acf_fields as $acf_field){
			if($acf_field["type"] == "tab") continue;
			$fields[] = array("type"=>"acf", "name"=>$acf_field["key"]);
		}
		return $fields;
	}

	/* Get formated taxonomies of a certain post */
	private function get_post_taxonomies_for_diff($post){
		$fields = array();
		$taxonomies = get_object_taxonomies($post);
		foreach($taxonomies as $taxonomy){
			$fields[] = array("type"=>"taxonomy", "name"=>$taxonomy );
		}
		return $fields;
	}

	/* Get value of a field as a string */
	private function get_string_value( &$obj , $post_id, $post, $acf_field_value=null ){
		$type = strtolower($obj["type"]);
		$name = strtolower($obj["name"]);

		switch( $type ){
			case "acf":
				if (function_exists('get_field_object')) {
					$field_obj = get_field_object( $name );
					$acf_field_type = $field_obj["type"];
					$acf_field_key = $field_obj["key"];

					if($acf_field_value == null){
						$field_value = get_field( $acf_field_key, $post_id );
						if(!$field_value) return "";
					}else{
						$field_value = $acf_field_value;
					}

					$field_tab = $this->is_in_tab( $field_obj );
					$obj["label"] = ( $field_tab != null? $field_tab["label"].": " :"" ) .  $field_obj["label"];

					if( in_array( $acf_field_type, array("text", "wysiwyg", "wp_wysiwyg") ) ){
						return $field_value;

					}else if( $acf_field_type == "checkbox" ){
						foreach($field_value as $key=>$field_value_item){
							$field_value[$key] = "• ".$field_obj["choices"][$field_value_item];
						}
						return implode("\n", $field_value);

					}else if( $acf_field_type == "relationship" ){
						foreach($field_value as $key=>$field_value_item){
							$field_value[$key] = "• ".$field_value_item->post_title."  ( ".get_permalink($field_value_item->ID)." )";
						}
						return implode("\n", $field_value);

					}else if( $acf_field_type == "repeater" ){

						foreach($field_value as $key=>$field_value_item){
							$field_row_val = "";
							foreach($field_obj["sub_fields"] as $skey=>$sub_field){
								$field_obj_ = array("type"=>"acf", "name"=>$sub_field["key"]);
								$sub_field_value = $this->get_string_value( $field_obj_ , $post_id, $post , $field_value_item[$sub_field["name"]] );
								$field_row_val .= $sub_field["label"]. ": ".$sub_field_value. " , ";
							}
							$field_value[$key] = "• ".$field_row_val;
						}

						return implode("\n", $field_value);

					}else if( $acf_field_type == "image" ){
						return $field_value;
					}
			}
				break;

			case "wp":
				$obj["label"] = $name;
				if($name == "content"){
					return $post->post_content;
				}
				break;
			case "taxonomy":
				$tax_obj = get_taxonomy($name);
				$obj["label"] = $tax_obj->labels->singular_name;
				if($obj["label"] == "") $obj["label"] = $tax_obj->name;
				if($obj["label"] == "") $obj["label"] = $name;

				$terms = array();
				$wp_terms = wp_get_post_terms($post_id, $name);
				foreach($wp_terms as $wp_term){
					$terms[] = "• ".$wp_term->name;
				}
				return implode("\n", $terms);

		}
	}

	/* Check if a field is in tab, if so return the tab field object */
	private function is_in_tab( $field_obj ){

		$tab = null;
		$acf_fields = $this->my_acf_get_fields_in_group( $field_obj["field_group"] );

		foreach($acf_fields as $key=>$acf_field){
			if($acf_field["key"] == $field_obj["key"]){
				return $tab;
			}
			if($acf_field["type"] == "tab"){
				$tab = $acf_field;
			}
		}

	}

	/* Get ACF Fields that belong to certain ACF Fields Group */
	private function my_acf_get_fields_in_group( $group_id ) {
			global $acf_group_fields;
			if(!$acf_group_fields) $acf_group_fields = array();

			// Check if we already got these fields. If so just return them
			if(isset($acf_group_fields[$group_id])) return $acf_group_fields[$group_id];

			$acf_meta = get_post_custom( $group_id );
			$acf_fields = array();

			if(!$acf_meta) return array();

			foreach ( $acf_meta as $key => $val ) {
					if ( preg_match( "/^field_/", $key ) ) {
							$acf_fields[$key] = unserialize($val[0]);
					}
			}

			// Order fields.
			usort($acf_fields, function($a, $b){
				if($a["order_no"] == $b["order_no"]) return 0;
				return $a["order_no"] > $b["order_no"] ? 1 : -1;
			});

			$acf_group_fields[$group_id] = $acf_fields;

			return $acf_fields;
	}


	function rule_match_post( $match, $rule, $options ){
		// validation
		if( !$options['post_id'] ){
			return $match;
		}

		$original_post_id = get_post_meta($options['post_id'], '_dp_original', true);
		if($original_post_id){

					if($rule['operator'] == "=="){
						$match = ( $original_post_id == $rule['value'] );
					}
					elseif($rule['operator'] == "!="){
						$match = ( $original_post_id != $rule['value'] );
					}

		}

		return $match;
	}

	function add_filters(){
		if ($this->get_option('duplicate_post_show_row') == 1){
			add_filter('post_row_actions', array($this,'duplicate_post_make_duplicate_link_row'),10,2);
			add_filter('page_row_actions', array($this,'duplicate_post_make_duplicate_link_row'),10,2);
		}

		add_filter('acf/location/rule_match/page', array($this, 'rule_match_post'), 15, 3);
		add_filter('acf/location/rule_match/post', array($this, 'rule_match_post'), 15, 3);
	}
	function add_actions(){

		/**
		 * Add a button in the post/page edit screen to create a clone
		 */
		if ($this->get_option('duplicate_post_show_submitbox') == 1){
			add_action( 'post_submitbox_start', array($this,'duplicate_post_add_duplicate_post_button') );
		}

		// if ($this->get_option('duplicate_post_show_adminbar') == 1){
		// 	add_action( 'wp_before_admin_bar_render', array($this,'duplicate_post_admin_bar_render') );
		// }

		add_action('admin_action_duplicate_post_save_as_new_post', array($this,'duplicate_post_save_as_new_post'));
		add_action('admin_action_duplicate_post_save_as_new_post_draft', array($this,'duplicate_post_save_as_new_post_draft'));
		add_action('admin_action_duplicate_post_save_to_original', array($this,'duplicate_post_save_to_original'));
		// Using our action hooks to copy taxonomies
		add_action('dp_duplicate_post', array($this,'duplicate_post_copy_post_taxonomies') , 10, 2);
		add_action('dp_duplicate_page', array($this,'duplicate_post_copy_post_taxonomies'), 10, 2);
		// Using our action hooks to copy meta fields
		add_action('dp_duplicate_post', array($this,'duplicate_post_copy_post_meta_info'), 10, 2);
		add_action('dp_duplicate_page', array($this,'duplicate_post_copy_post_meta_info'), 10, 2);
		// Using our action hooks to copy attachments
		add_action('dp_duplicate_post', array($this,'duplicate_post_copy_children'), 10, 2);
		add_action('dp_duplicate_page', array($this,'duplicate_post_copy_children'), 10, 2);

		add_action('admin_menu', array($this,'register_my_custom_submenu_page') );

		add_filter('admin_head', array($this,'remove_default_post_actions_for_cloned_docs') );
		add_action('post_submitbox_start', array($this,'add_cloned_doc_action_buttons') );
		add_action( 'save_post', array($this,'cloned_post_save') );

		add_action('wp_head', array($this,'add_nofollow_noindex_to_clones') );

		add_action( 'save_post', array($this,'dem_save_email'), 10, 2 );

	}


	function duplicate_post_make_duplicate_link_row($actions, $post) {
		if (DuplicatePost::duplicate_post_is_current_user_allowed_to_copy()) {
			$actions['clone'] = '<a href="'.DuplicatePost::duplicate_post_get_clone_post_link( $post->ID , 'display', false).'" title="'
			. esc_attr(__("Duplicate this item", 'dem'))
			. '">' .  __('Duplicate', 'dem') . '</a>';
			/*$actions['edit_as_new_draft'] = '<a href="'. DuplicatePost::duplicate_post_get_clone_post_link( $post->ID ) .'" title="'
			. esc_attr(__('Copy to a new draft', 'dem'))
			. '">' .  __('New Draft', 'dem') . '</a>';*/
		}
		return $actions;
	}

	function duplicate_post_add_duplicate_post_button() {
		$allowed = DuplicatePost::duplicate_post_is_current_user_allowed_to_copy();

		/*global $post;
			$qo = get_queried_object();
			$allowed = false;
			$settings = get_option('dem_main_settings');
			$current_user = wp_get_current_user();

			if($qo){
				$authorID = $qo->post_author;
			} else {
				if(isset($post)){
					$authorID = $post->post_author;
				} else {
					$authorID = 0;
				}
			}*/

		if ( isset( $_GET['post'] ) && $allowed) {
			$backup = get_post_meta($_GET['post'], '_dp_original_backup', true);
			?>
				<div id="duplicate-action">
					<a style="width: 100%;text-align: center;margin-bottom: 10px;" class="submitduplicate duplication button" href="<?php echo DuplicatePost::duplicate_post_get_clone_post_link( $_GET['post'] ) ?>">
						<?php _e('Duplicate and Edit', 'dem'); ?>
					</a>
					<?php if($backup){ ?>
						<input name="revert_back_to_cloned" type="submit" class="button revert_back_to_cloned button-primary button-large" id="revert_back_to_cloned" value="Ooops. Re-link to Original Post">
						<input name="unlink_post_forever" type="submit" class="button button-primary button-large" id="unlink_post_permanently" title="This will unlink the post from the original. There is no undo after clicking this button" value="Unlink from Original Post Forever">
						<style type="text/css">
						#revert_back_to_cloned, #unlink_post_permanently {

							background: #00529B;
							border:#00529B;
							width: 100%;
							margin-bottom: 10px;
							color: white;
							box-shadow: none;
							-webkit-box-shadow: none;
						}
						#unlink_post_permanently {
							background: rgb(214, 8, 8);
							border:rgb(214, 8, 8);
						}
						</style>
					<?php } ?>
				</div>
			<?php
		}
	}

	function duplicate_post_save_to_original(){
		if ( isset( $_GET['post'] ) ){
			$original_post_id = absint( get_post_meta( $_GET["post"], '_dp_original', true) );
			if($original_post_id){
				$this->save_to_original( $_GET["post"] );

						wp_redirect( admin_url("post.php")."?post={$original_post_id}&action=edit" );
						die();
				}
		}
	}

	function duplicate_post_admin_bar_render() {
		global $wp_admin_bar;
		$current_object = get_queried_object();
		if ( empty($current_object) )
		return;
		if ( ! empty( $current_object->post_type )
		&& ( $post_type_object = get_post_type_object( $current_object->post_type ) )
		&& DuplicatePost::duplicate_post_is_current_user_allowed_to_copy()
		&& ( $post_type_object->show_ui || 'attachment' == $current_object->post_type ) )
		{
			$wp_admin_bar->add_menu( array(
				'parent' => '',
						'id' => 'new_draft',
						'title' => __("Duplicate & Edit this " . ucfirst ($current_object->post_type), 'dem'),
						'href' => DuplicatePost::duplicate_post_get_clone_post_link( $current_object->ID )
			) );
		}
	}

	/*
	 * This function calls the creation of a new copy of the selected post (as a draft)
	 * then redirects to the edit post screen
	 */
	function duplicate_post_save_as_new_post_draft(){
		$this->duplicate_post_save_as_new_post('draft');
	}

	/*
	 * This function calls the creation of a new copy of the selected post (by default preserving the original publish status)
	 * then redirects to the post list
	 */
	function duplicate_post_save_as_new_post($status = ''){
		if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'duplicate_post_save_as_new_post' == $_REQUEST['action'] ) ) ) {
			wp_die(__('No post to duplicate has been supplied!', 'dem'));
		}

		// Get the original post
		$id = (isset($_GET['post']) ? $_GET['post'] : $_POST['post']);
		$post = get_post($id);

		// Copy the post and insert it
		if (isset($post) && $post!=null) {
			$new_id = $this->duplicate_post_create_duplicate($post, $status);

			if ($status == ''){
				// Redirect to the post list screen
				wp_redirect( admin_url( 'edit.php?post_type='.$post->post_type) );
			} else {
				// Redirect to the edit screen for the new draft post
				wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_id ) );
			}
			exit;

		} else {
			$post_type_obj = get_post_type_object( $post->post_type );
			wp_die(esc_attr(__('Copy creation failed, could not find original:', 'dem')) . ' ' . htmlspecialchars($id));
		}
	}

	/**
	 * Get the currently registered user
	 */
	function duplicate_post_get_current_user() {
		if (function_exists('wp_get_current_user')) {
			return wp_get_current_user();
		} else if (function_exists('get_currentuserinfo')) {
			global $userdata;
			get_currentuserinfo();
			return $userdata;
		} else {
			$user_login = $_COOKIE[USER_COOKIE];
			$sql = $wpdb->prepare("SELECT * FROM $wpdb->users WHERE user_login=%s", $user_login);
			$current_user = $wpdb->get_results($sql);
			return $current_user;
		}
	}


	/**
	 * Copy the taxonomies of a post to another post
	 */
	function duplicate_post_copy_post_taxonomies($new_id, $post) {
		global $wpdb;
		if (isset($wpdb->terms)) {
			// Clear default category (added by wp_insert_post)
			wp_set_object_terms( $new_id, NULL, 'category' );

			$post_taxonomies = get_object_taxonomies($post->post_type);
			$taxonomies_blacklist = $this->get_option('duplicate_post_taxonomies_blacklist');
			if ($taxonomies_blacklist == "") $taxonomies_blacklist = array();
			$taxonomies = array_diff($post_taxonomies, $taxonomies_blacklist);
			foreach ($taxonomies as $taxonomy) {
				$post_terms = wp_get_object_terms($post->ID, $taxonomy, array( 'orderby' => 'term_order' ));
				$terms = array();
				for ($i=0; $i<count($post_terms); $i++) {
					$terms[] = $post_terms[$i]->slug;
				}
				wp_set_object_terms($new_id, $terms, $taxonomy);
			}
		}
	}


	/**
	 * Copy the meta information of a post to another post
	 */
	function duplicate_post_copy_post_meta_info($new_id, $post) {
		$post_meta_keys = get_post_custom_keys($post->ID);

		if (empty($post_meta_keys)) return;
		$meta_blacklist = explode(",",$this->get_option('duplicate_post_blacklist'));
		if ($meta_blacklist == "") $meta_blacklist = array();
		$meta_keys = array_diff($post_meta_keys, $meta_blacklist);

		foreach ($meta_keys as $meta_key) {
			if( in_array($meta_key, array("_dp_original","_dp_submited")) ) continue;

			$meta_values = get_post_custom_values($meta_key, $post->ID);
			foreach ($meta_values as $meta_value) {
				$meta_value = maybe_unserialize($meta_value);
				update_post_meta($new_id, $meta_key, $meta_value);
			}
		}
	}


	/**
	 * Copy the attachments
	 * It simply copies the table entries, actual file won't be duplicated
	 */
	function duplicate_post_copy_children($new_id, $post){
		$copy_attachments = $this->get_option('duplicate_post_copyattachments');
		$copy_children = $this->get_option('duplicate_post_copychildren');

		// get children
		$children = get_posts(array( 'post_type' => 'any', 'numberposts' => -1, 'post_status' => 'any', 'post_parent' => $post->ID ));
		// clone old attachments
		foreach($children as $child){
			if ($copy_attachments == 0 && $child->post_type == 'attachment') continue;
			if ($copy_children == 0 && $child->post_type != 'attachment') continue;
			$this->duplicate_post_create_duplicate($child, '', $new_id);
		}
	}


	/**
	 * Create a duplicate from a post
	 */
	function duplicate_post_create_duplicate($post_to_dup, $status = '', $parent_id = '', $to_post_id='') {

		// We don't want to clone revisions
		if ($post_to_dup->post_type == 'revision') return;

		if($to_post_id != ''){
			$to_post = get_post($to_post_id);
			if(!$to_post) return;
		}

		if ($post_to_dup->post_type != 'attachment'){
			$prefix = $this->get_option('duplicate_post_title_prefix');
			$suffix = $this->get_option('duplicate_post_title_suffix');
			if (!empty($prefix)) $prefix.= " ";
			if (!empty($suffix)) $suffix = " ".$suffix;
			if ($this->get_option('duplicate_post_copystatus') == 0) $status = 'draft';
		}
		$new_post_author = $this->duplicate_post_get_current_user();

		$new_post = array(
		'menu_order' => $post_to_dup->menu_order,
		'comment_status' => $post_to_dup->comment_status,
		'ping_status' => $post_to_dup->ping_status,
		'post_author' => $new_post_author->ID,
		'post_content' => $post_to_dup->post_content,
		'post_excerpt' => ($this->get_option('duplicate_post_copyexcerpt') == '1') ? $post_to_dup->post_excerpt : "",
		'post_mime_type' => $post_to_dup->post_mime_type,
		'post_parent' => $new_post_parent = empty($parent_id)? $post_to_dup->post_parent : $parent_id,
		'post_password' => $post_to_dup->post_password,
		'post_status' => $new_post_status = (empty($status))? $post_to_dup->post_status: $status,
		'post_title' => $prefix.$post_to_dup->post_title.$suffix,
		'post_type' => $post_to_dup->post_type,
		);

		if($to_post_id != ''){
			$new_post["post_title"] 	= $to_post->post_title;
			$new_post["post_status"] 	= $to_post->post_status;
			$new_post["ID"] 			= $to_post_id;
			$new_post["post_author"] 	= $to_post->post_author;
		}

								if($this->get_option('duplicate_post_copydate') == 1 && empty( $to_post_id ) ){
												$new_post['post_date'] = $new_post_date =  $post_to_dup->post_date ;
												$new_post['post_date_gmt'] = get_gmt_from_date($new_post_date);
								} else if( ! empty( $to_post_id ) ) {
												// We are merging back, do not override publish date
												$new_post['post_date'] = $new_post_date =  $to_post->post_date ;
												$new_post['post_date_gmt'] = get_gmt_from_date($new_post_date);
								}

		$new_post_id = wp_insert_post($new_post);
		delete_post_meta($new_post_id, '_dp_original_backup');
		// If the copy is published or scheduled, we have to set a proper slug.
		if($to_post_id == ''){
			if ($new_post_status == 'publish' || $new_post_status == 'future'){
				$post_to_dup_name = wp_unique_post_slug($post_to_dup->post_name, $new_post_id, $new_post_status, $post_to_dup->post_type, $new_post_parent);

				$new_post = array();
				$new_post['ID'] = $new_post_id;
				$new_post['post_name'] = $post_to_dup_name;

				// Update the post into the database
				wp_update_post( $new_post );
			}
		}

		// If you have written a plugin which uses non-WP database tables to save
		// information about a post you can hook this action to dupe that data.
		if ($post_to_dup->post_type == 'page' || (function_exists('is_post_type_hierarchical') && is_post_type_hierarchical( $post_to_dup->post_type )))
		do_action( 'dp_duplicate_page', $new_post_id, $post_to_dup );
		else
		do_action( 'dp_duplicate_post', $new_post_id, $post_to_dup );

		if($to_post_id == ''){
			delete_post_meta($new_post_id, '_dp_original');
			add_post_meta($new_post_id, '_dp_original', $post_to_dup->ID);
		}else{
			wp_delete_post($post_to_dup->ID);
		}

		return $new_post_id;
	}


	function duplicate_post( $post_id ){
		$this->duplicate_post_create_duplicate(get_post($post_id));
	}

	function save_to_original( $post_id ){
		$original_post_id = get_post_meta($post_id, '_dp_original', true);
		if($original_post_id){
			if($post = get_post($post_id)){
				$this->duplicate_post_create_duplicate(get_post($post_id),'','',$original_post_id);
			}else{
				return new WP_Error( 'broke', __( "This posts original does not exist", 'dem' ) );
			}
		}else{
			return new WP_Error( 'broke', __( "This post doesn't have an original post id", 'dem' ) );
		}
	}


}

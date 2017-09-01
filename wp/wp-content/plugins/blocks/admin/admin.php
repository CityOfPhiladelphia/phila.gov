<?php

require_once wpcmsb_PLUGIN_DIR . '/admin/includes/admin-functions.php';
require_once wpcmsb_PLUGIN_DIR . '/admin/includes/help-tabs.php';
// require_once wpcmsb_PLUGIN_DIR . '/admin/includes/tag-generator.php';

add_action( 'admin_menu', 'wpcmsb_admin_menu', 8 );

//wpcmsb

function wpcmsb_admin_menu() {
	add_menu_page( __( 'Block', 'cms-block' ),
		__( 'Blocks', 'cms-block' ),
		'wpcmsb_read_cms_blocks', 'block',
		'wpcmsb_admin_management_page', 'dashicons-screenoptions' );

	$edit = add_submenu_page( 'block',
		__( 'Edit Block', 'cms-block' ),
		__( 'All Blocks', 'cms-block' ),
		'wpcmsb_read_cms_blocks', 'block',
		'wpcmsb_admin_management_page' );

	add_action( 'load-' . $edit, 'wpcmsb_load_cms_block_admin' );

	$addnew = add_submenu_page( 'block',
		__( 'Add New Block', 'cms-block' ),
		__( 'Add New Block', 'cms-block' ),
		'wpcmsb_edit_cms_blocks', 'block-new',
		'wpcmsb_admin_add_new_page' );

	add_action( 'load-' . $addnew, 'wpcmsb_load_cms_block_admin' );

	$integration = wpcmsb_Integration::get_instance();

	if ( $integration->service_exists() ) {
		$integration = add_submenu_page( 'block',
			__( 'Integration with Other Services', 'cms-block' ),
			__( 'Integration', 'cms-block' ),
			'wpcmsb_edit_cms_blocks', 'wpcmsb-integration',
			'wpcmsb_admin_integration_page' );

		add_action( 'load-' . $integration, 'wpcmsb_load_integration_page' );
	}
}

add_filter( 'set-screen-option', 'wpcmsb_set_screen_options', 10, 3 );

function wpcmsb_set_screen_options( $result, $option, $value ) {
	$wpcmsb_screens = array(
		'cfseven_cms_blocks_per_page' );

	if ( in_array( $option, $wpcmsb_screens ) )
		$result = $value;

	return $result;
}

function wpcmsb_load_cms_block_admin() {
	global $plugin_page;

	$action = wpcmsb_current_action();

	if ( 'save' == $action ) {
		$id = $_POST['post_ID'];
		check_admin_referer( 'wpcmsb-save-cms-block_' . $id );

		if ( ! current_user_can( 'wpcmsb_edit_cms_block', $id ) )
			wp_die( __( 'You are not allowed to edit this item.', 'cms-block' ) );

		$id = wpcmsb_save_cms_block( $id );

		$query = array(
			'message' => ( -1 == $_POST['post_ID'] ) ? 'created' : 'saved',
			'post' => $id,
			'active-tab' => isset( $_POST['active-tab'] ) ? (int) $_POST['active-tab'] : 0 );

		$redirect_to = add_query_arg( $query, menu_page_url( 'block', false ) );
		wp_safe_redirect( $redirect_to );
		exit();
	}

	if ( 'copy' == $action ) {
		$id = empty( $_POST['post_ID'] )
			? absint( $_REQUEST['post'] )
			: absint( $_POST['post_ID'] );

		check_admin_referer( 'wpcmsb-copy-cms-block_' . $id );

		if ( ! current_user_can( 'wpcmsb_edit_cms_block', $id ) )
			wp_die( __( 'You are not allowed to edit this item.', 'cms-block' ) );

		$query = array();

		if ( $cms_block = wpcmsb_cms_block( $id ) ) {
			$new_cms_block = $cms_block->copy();
			$new_cms_block->save();

			$query['post'] = $new_cms_block->id();
			$query['message'] = 'created';
		}

		$redirect_to = add_query_arg( $query, menu_page_url( 'block', false ) );

		wp_safe_redirect( $redirect_to );
		exit();
	}

	if ( 'delete' == $action ) {
		if ( ! empty( $_POST['post_ID'] ) )
			check_admin_referer( 'wpcmsb-delete-cms-block_' . $_POST['post_ID'] );
		elseif ( ! is_array( $_REQUEST['post'] ) )
			check_admin_referer( 'wpcmsb-delete-cms-block_' . $_REQUEST['post'] );
		else
			check_admin_referer( 'bulk-posts' );

		$posts = empty( $_POST['post_ID'] )
			? (array) $_REQUEST['post']
			: (array) $_POST['post_ID'];

		$deleted = 0;

		foreach ( $posts as $post ) {
			$post = wpcmsb_cmsblock::get_instance( $post );

			if ( empty( $post ) )
				continue;

			if ( ! current_user_can( 'wpcmsb_delete_cms_block', $post->id() ) )
				wp_die( __( 'You are not allowed to delete this item.', 'cms-block' ) );

			if ( ! $post->delete() )
				wp_die( __( 'Error in deleting.', 'cms-block' ) );

			$deleted += 1;
		}

		$query = array();

		if ( ! empty( $deleted ) )
			$query['message'] = 'deleted';

		$redirect_to = add_query_arg( $query, menu_page_url( 'block', false ) );

		wp_safe_redirect( $redirect_to );
		exit();
	}

	$_GET['post'] = isset( $_GET['post'] ) ? $_GET['post'] : '';

	$post = null;

	if ( 'block-new' == $plugin_page && isset( $_GET['locale'] ) ) {
		$post = wpcmsb_cmsblock::get_template( array(
			'locale' => $_GET['locale'] ) );
		$post = null;
	} elseif ( ! empty( $_GET['post'] ) ) {
		$post = wpcmsb_cmsblock::get_instance( $_GET['post'] );
	}

	$current_screen = get_current_screen();

	$help_tabs = new wpcmsb_Help_Tabs( $current_screen );

	if ( $post && current_user_can( 'wpcmsb_edit_cms_block', $post->id() ) ) {
		$help_tabs->set_help_tabs( 'edit' );

	} else if ( 'block-new' == $plugin_page ) {
		$help_tabs->set_help_tabs( 'add_new' );

	} else {
		$help_tabs->set_help_tabs( 'list' );

		if ( ! class_exists( 'wpcmsb_cms_block_List_Table' ) ) {
			require_once wpcmsb_PLUGIN_DIR . '/admin/includes/class-cms-blocks-list-table.php';
		}

		add_filter( 'manage_' . $current_screen->id . '_columns',
			array( 'wpcmsb_cms_block_List_Table', 'define_columns' ) );

		add_screen_option( 'per_page', array(
			'label' => __( 'Block', 'cms-block' ),
			'default' => 20,
			'option' => 'cfseven_cms_blocks_per_page' ) );
	}
}

add_action( 'admin_enqueue_scripts', 'wpcmsb_admin_enqueue_scripts' );

function wpcmsb_admin_enqueue_scripts( $hook_suffix ) {
	if ( false === strpos( $hook_suffix, 'block' ) ) {
		return;
	}

	wp_enqueue_style( 'cms-block-admin',
		wpcmsb_plugin_url( 'admin/css/styles.css' ),
		array(), wpcmsb_VERSION, 'all' );

	if ( wpcmsb_is_rtl() ) {
		wp_enqueue_style( 'cms-block-admin-rtl',
			wpcmsb_plugin_url( 'admin/css/styles-rtl.css' ),
			array(), wpcmsb_VERSION, 'all' );
	}


	wp_localize_script( 'wpcmsb-admin', '_wpcmsb', array(
		'pluginUrl' => wpcmsb_plugin_url(),
		'saveAlert' => __( "The changes you made will be lost if you navigate away from this page.", 'cms-block' ),
		'activeTab' => isset( $_GET['active-tab'] ) ? (int) $_GET['active-tab'] : 0 ) );

	add_thickbox();

}

function wpcmsb_admin_management_page() {
	if ( $post = wpcmsb_get_current_cms_block() ) {
		$post_id = $post->initial() ? -1 : $post->id();

		require_once wpcmsb_PLUGIN_DIR . '/admin/includes/editor.php';
		require_once wpcmsb_PLUGIN_DIR . '/admin/edit-cms-block.php';
		return;
	}

	$list_table = new wpcmsb_cms_block_List_Table();
	$list_table->prepare_items();

?>
<div class="wrap">

<h2><?php
	echo esc_html( __( 'Blocks', 'cms-block' ) );

	if ( current_user_can( 'wpcmsb_edit_cms_blocks' ) ) {
		echo ' <a href="' . esc_url( menu_page_url( 'block-new', false ) ) . '" class="add-new-h2">' . esc_html( __( 'Add New', 'cms-block' ) ) . '</a>';
	}

	if ( ! empty( $_REQUEST['s'] ) ) {
		echo sprintf( '<span class="subtitle">'
			. __( 'Search results for &#8220;%s&#8221;', 'cms-block' )
			. '</span>', esc_html( $_REQUEST['s'] ) );
	}
?></h2>

<?php do_action( 'wpcmsb_admin_notices' ); ?>

<form method="get" action="">
	<input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
	<?php $list_table->search_box( __( 'Search Block', 'cms-block' ), 'wpcmsb-contact' ); ?>
	<?php $list_table->display(); ?>
</form>

</div>
<?php
}

function wpcmsb_admin_add_new_page() {
	if ( $post = wpcmsb_get_current_cms_block() ) {
		$post_id = -1;

		require_once wpcmsb_PLUGIN_DIR . '/admin/includes/editor.php';
		require_once wpcmsb_PLUGIN_DIR . '/admin/edit-cms-block.php';
		return;
	}

	$available_locales = wpcmsb_l10n();
	$default_locale = get_locale();

	if ( ! isset( $available_locales[$default_locale] ) ) {
		$default_locale = 'en_US';
	}

?>
<div class="wrap">

<h2><?php echo esc_html( __( 'Add New Block', 'cms-block' ) ); ?></h2>

<?php do_action( 'wpcmsb_admin_notices' ); ?>

<p><a href="<?php echo esc_url( add_query_arg( array( 'locale' => $default_locale ), menu_page_url( 'block-new', false ) ) ); ?>" class="button button-primary" /><?php echo esc_html( __( 'Add New', 'cms-block' ) ); ?></a></p>

<?php unset( $available_locales[$default_locale] ); ?>
<form action="" method="get">
<input type="hidden" name="page" value="block-new" />
</form>
</div>
<?php
}

function wpcmsb_load_integration_page() {
	$integration = wpcmsb_Integration::get_instance();

	if ( isset( $_REQUEST['service'] )
	&& $integration->service_exists( $_REQUEST['service'] ) ) {
		$service = $integration->get_service( $_REQUEST['service'] );
		$service->load( wpcmsb_current_action() );
	}

	$help_tabs = new wpcmsb_Help_Tabs( get_current_screen() );
	$help_tabs->set_help_tabs( 'integration' );
}

function wpcmsb_admin_integration_page() {
	$integration = wpcmsb_Integration::get_instance();

?>
<div class="wrap">

<h2><?php echo esc_html( __( 'Integration with Other Services', 'cms-block' ) ); ?></h2>

<?php do_action( 'wpcmsb_admin_notices' ); ?>

<?php
	if ( isset( $_REQUEST['service'] )
	&& $service = $integration->get_service( $_REQUEST['service'] ) ) {
		$service->admin_notice();
		$integration->list_services( array( 'include' => $_REQUEST['service'] ) );
	} else {
		$integration->list_services();
	}
?>

</div>
<?php
}

/* Misc */

add_action( 'wpcmsb_admin_notices', 'wpcmsb_admin_updated_message' );

function wpcmsb_admin_updated_message() {
	if ( empty( $_REQUEST['message'] ) )
		return;

	if ( 'created' == $_REQUEST['message'] )
		$updated_message = esc_html( __( 'Block created.', 'cms-block' ) );
	elseif ( 'saved' == $_REQUEST['message'] )
		$updated_message = esc_html( __( 'Block saved.', 'cms-block' ) );
	elseif ( 'deleted' == $_REQUEST['message'] )
		$updated_message = esc_html( __( 'Block deleted.', 'cms-block' ) );

	if ( empty( $updated_message ) )
		return;

?>
<div id="message" class="updated"><p><?php echo $updated_message; ?></p></div>
<?php
}

add_filter( 'plugin_action_links', 'wpcmsb_plugin_action_links', 10, 2 );

function wpcmsb_plugin_action_links( $links, $file ) {
	if ( $file != wpcmsb_PLUGIN_BASENAME )
		return $links;

	$settings_link = '<a href="' . menu_page_url( 'block', false ) . '">'
		. esc_html( __( 'Settings', 'cms-block' ) ) . '</a>';

	array_unshift( $links, $settings_link );

	return $links;
}

add_action( 'admin_notices', 'wpcmsb_old_wp_version_error', 9 );

function wpcmsb_old_wp_version_error() {
	global $plugin_page;

	if ( 'block' != substr( $plugin_page, 0, 5 ) ) {
		return;
	}

	$wp_version = get_bloginfo( 'version' );

	if ( ! version_compare( $wp_version, wpcmsb_REQUIRED_WP_VERSION, '<' ) )
		return;

?>
<div class="error">
<p><?php echo sprintf( __( '<strong>Blocks %1$s requires WordPress %2$s or higher.</strong> Please <a href="%3$s">update WordPress</a> first.', 'cms-block' ), wpcmsb_VERSION, wpcmsb_REQUIRED_WP_VERSION, admin_url( 'update-core.php' ) ); ?></p>
</div>
<?php
}

//add_action( 'wpcmsb_admin_notices', 'wpcmsb_welcome_panel', 2 );

function wpcmsb_welcome_panel() {
	global $plugin_page;

	if ( 'block' != $plugin_page || ! empty( $_GET['post'] ) ) {
		return;
	}

	$classes = 'welcome-panel';

	$vers = (array) get_user_meta( get_current_user_id(),
		'wpcmsb_hide_welcome_panel_on', true );

	if ( wpcmsb_version_grep( wpcmsb_version( 'only_major=1' ), $vers ) ) {
		$classes .= ' hidden';
	}

?>
<div id="welcome-panel" class="<?php echo esc_attr( $classes ); ?>">
	<?php wp_nonce_field( 'wpcmsb-welcome-panel-nonce', 'welcomepanelnonce', false ); ?>
	<a class="welcome-panel-close" href="<?php echo esc_url( menu_page_url( 'block', false ) ); ?>"><?php echo esc_html( __( 'Dismiss', 'cms-block' ) ); ?></a>

	<div class="welcome-panel-content">
		<div class="welcome-panel-column-container">
			<div class="welcome-panel-column">
				<h4><?php echo esc_html( __( 'Blocks Needs Your Support', 'cms-block' ) ); ?></h4>
				<p class="message"><?php echo esc_html( __( "It is hard to continue development and support for this plugin without contributions from users like you. If you enjoy using Blocks and find it useful, please consider making a donation.", 'cms-block' ) ); ?></p>
				<p><?php echo wpcmsb_link( __( 'http://renzojohnson.com/contributions/blocks', 'cms-block' ), __( 'Donate', 'cms-block' ), array( 'class' => 'button button-primary' ) ); ?></p>
			</div>

			<div class="welcome-panel-column">
				<h4><?php echo esc_html( __( 'Get Started', 'cms-block' ) ); ?></h4>
				<ul>
					<li><?php echo wpcmsb_link( __( 'http://renzojohnson.com/contributions/blocks', 'cms-block' ), __( 'Getting Started with CMS Block', 'cms-block' ) ); ?></li>
					<li><?php echo wpcmsb_link( __( 'http://renzojohnson.com/contributions/blocks', 'cms-block' ), __( 'Admin Screen', 'cms-block' ) ); ?></li>
					<li><?php echo wpcmsb_link( __( 'http://renzojohnson.com/contributions/blocks', 'cms-block' ), __( 'How Tags Work', 'cms-block' ) ); ?></li>
				</ul>
			</div>

			<div class="welcome-panel-column">
				<h4><?php echo esc_html( __( 'Did You Know?', 'cms-block' ) ); ?></h4>
				<ul>
					<li><?php echo wpcmsb_link( __( 'http://renzojohnson.com/contributions/blocks', 'cms-block' ), __( 'Spam Filtering with Akismet', 'cms-block' ) ); ?></li>
					<li><?php echo wpcmsb_link( __( 'http://renzojohnson.com/contributions/blocks', 'cms-block' ), __( 'Save Messages with Flamingo', 'cms-block' ) ); ?></li>
					<li><?php echo wpcmsb_link( __( 'http://renzojohnson.com/contributions/blocks', 'cms-block' ), __( 'Selectable Recipient with Pipes', 'cms-block' ) ); ?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<?php
}

add_action( 'wp_ajax_wpcmsb-update-welcome-panel', 'wpcmsb_admin_ajax_welcome_panel' );

function wpcmsb_admin_ajax_welcome_panel() {
	check_ajax_referer( 'wpcmsb-welcome-panel-nonce', 'welcomepanelnonce' );

	$vers = get_user_meta( get_current_user_id(),
		'wpcmsb_hide_welcome_panel_on', true );

	if ( empty( $vers ) || ! is_array( $vers ) ) {
		$vers = array();
	}

	if ( empty( $_POST['visible'] ) ) {
		$vers[] = wpcmsb_VERSION;
	}

	$vers = array_unique( $vers );

	update_user_meta( get_current_user_id(), 'wpcmsb_hide_welcome_panel_on', $vers );

	wp_die( 1 );
}

add_action( 'wpcmsb_admin_notices', 'wpcmsb_not_allowed_to_edit' );

function wpcmsb_not_allowed_to_edit() {
	if ( ! $cms_block = wpcmsb_get_current_cms_block() ) {
		return;
	}

	$post_id = $cms_block->id();

	if ( current_user_can( 'wpcmsb_edit_cms_block', $post_id ) ) {
		return;
	}

	$message = __( "You are not allowed to edit this Block.",
		'cms-block' );

	echo sprintf( '<div class="notice notice-warning"><p>%s</p></div>',
		esc_html( $message ) );
}

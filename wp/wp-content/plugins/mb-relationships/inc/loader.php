<?php
/**
 * Plugin loader.
 *
 * @package    Meta Box
 * @subpackage MB Relationships
 */

/**
 * The loader class.
 */
class MBR_Loader {

	/**
	 * Plugin activation.
	 */
	public function activate() {
		$this->create_table();
	}

	/**
	 * Initialization.
	 */
	public function init() {
		if ( ! defined( 'RWMB_VER' ) ) {
			return;
		}

		$this->load_files();

		/**
		 * If plugin is embed in another plugin, the table is not created during activation.
		 * Thus, we have to create it while initializing.
		 */
		$this->create_table();

		$obj_factory = new MBR_Object_Factory();
		$rel_factory = new MBR_Relationship_Factory( $obj_factory );

		$storage_handler = new MBR_Storage_Handler( $rel_factory );
		$storage_handler->init();

		$normalizer = new MBR_Query_Normalizer( $rel_factory );
		$post_query = new MBR_Query_Post( $normalizer );
		$post_query->init();
		$term_query = new MBR_Query_Term( $normalizer );
		$term_query->init();
		$user_query = new MBR_Query_User( $normalizer );
		$user_query->init();

		MB_Relationships_API::set_relationship_factory( $rel_factory );
		MB_Relationships_API::set_post_query( $post_query );
		MB_Relationships_API::set_term_query( $term_query );
		MB_Relationships_API::set_user_query( $user_query );

		$shortcodes = new MBR_Shortcodes( $rel_factory, $obj_factory );
		$shortcodes->init();

		// All registration code goes here.
		do_action( 'mb_relationships_init' );
	}

	/**
	 * Create relationships table.
	 */
	protected function create_table() {
		require __DIR__ . '/database/table.php';

		$table = new MBR_Table();
		$is_table_created = get_option( 'mbr_table_created' );
		if ( ! $is_table_created ) {
			$table->create();
			update_option( 'mbr_table_created', 1 );
		}
	}

	/**
	 * Load plugin files.
	 */
	protected function load_files() {
		require __DIR__ . '/database/storage.php';
		require __DIR__ . '/database/storage-handler.php';

		require __DIR__ . '/object/interface.php';
		require __DIR__ . '/object/post.php';
		require __DIR__ . '/object/term.php';
		require __DIR__ . '/object/user.php';
		require __DIR__ . '/object/factory.php';

		require __DIR__ . '/query/query.php';
		require __DIR__ . '/query/normalizer.php';
		require __DIR__ . '/query/post.php';
		require __DIR__ . '/query/term.php';
		require __DIR__ . '/query/user.php';

		require __DIR__ . '/relationship/factory.php';
		require __DIR__ . '/relationship/relationship.php';
		require __DIR__ . '/relationship/admin-columns.php';
		require __DIR__ . '/relationship/meta-boxes.php';

		require __DIR__ . '/api.php';
		require __DIR__ . '/shortcodes.php';
	}
}

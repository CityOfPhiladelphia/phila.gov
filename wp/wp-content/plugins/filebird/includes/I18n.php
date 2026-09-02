<?php
namespace FileBird;

defined( 'ABSPATH' ) || exit;
/**
 * I18n Logic
 */
class I18n {
	protected static $instance = null;

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
	}

	public static function loadPluginTextdomain() {
		if ( function_exists( 'determine_locale' ) ) {
			$locale = determine_locale();
		} else {
			$locale = is_admin() ? get_user_locale() : get_locale();
		}
		unload_textdomain( 'filebird' );
		load_textdomain( 'filebird', NJFB_PLUGIN_PATH . '/i18n/languages/filebird-' . $locale . '.mo' );
		load_plugin_textdomain( 'filebird', false, NJFB_PLUGIN_PATH . '/i18n/languages/' );
	}

	public static function getTranslation() {
		$translation = array(
			'noMedia'                         => __( 'No media files found.', 'filebird' ),
			'new_folder'                      => __( 'New Folder', 'filebird' ),
			'delete'                          => __( 'Delete', 'filebird' ),
			'folders'                         => __( 'Folders', 'filebird' ),
			'ok'                              => __( 'Ok', 'filebird' ),
			'cancel'                          => __( 'Cancel', 'filebird' ),
			'cut'                             => __( 'Cut', 'filebird' ),
			'paste'                           => __( 'Paste', 'filebird' ),
			'download'                        => __( 'Download', 'filebird' ),
			'loading'                         => __( 'Loading', 'filebird' ),
			'generate_download'               => __( 'Generating download link...', 'filebird' ),
			'move_done'                       => __( 'Successfully moved', 'filebird' ),
			'move_error'                      => __( 'Unsuccessfully moved', 'filebird' ),
			'delete_done'                     => __( 'Successfully deleted!', 'filebird' ),
			'delete_error'                    => __( "Can't delete!", 'filebird' ),
			'save'                            => __( 'Save', 'filebird' ),
			'folder'                          => __( 'Folder', 'filebird' ),
			'folder_name_placeholder'         => __( 'Folder name...', 'filebird' ),
			'folders'                         => __( 'Folders', 'filebird' ),
			'enter_folder_name_placeholder'   => __( 'Enter folder name...', 'filebird' ),
			'are_you_sure_delete'             => __( 'Are you sure you want to delete', 'filebird' ),
			'are_you_sure'                    => __( 'Are you sure?', 'filebird' ),
			'all_files_will_move'             => __( 'Those files will be moved to <strong>Uncategorized</strong> folder.', 'filebird' ),
			'editing_warning'                 => __( 'You are editing another folder! Please complete the task first!', 'filebird' ),
			'sort_folders'                    => __( 'Sort Folders', 'filebird' ),
			'delete_folder'                   => __( 'Delete Folder', 'filebird' ),
			'sort_files'                      => __( 'Sort Files', 'filebird' ),
			'bulk_select'                     => __( 'Bulk Select', 'filebird' ),
			'all_files'                       => __( 'All Files', 'filebird' ),
			'uncategorized'                   => __( 'Uncategorized', 'filebird' ),
			'previous_folder_selected'        => __( 'Previous folder selected', 'filebird' ),
			'rename'                          => __( 'Rename', 'filebird' ),
			'are_you_sure_delete_this_folder' => __( 'Are you sure you want to delete this folder? Those files will be moved to <strong>Uncategorized</strong> folder.', 'filebird' ),
			'sort_ascending'                  => __( 'Sort Ascending', 'filebird' ),
			'sort_descending'                 => __( 'Sort Descending', 'filebird' ),
			'reset'                           => __( 'Reset', 'filebird' ),
			'by_name'                         => __( 'By Name', 'filebird' ),
			'name_ascending'                  => __( 'Name Ascending', 'filebird' ),
			'name_descending'                 => __( 'Name Descending', 'filebird' ),
			'by_date'                         => __( 'By Date', 'filebird' ),
			'date_ascending'                  => __( 'Date Ascending', 'filebird' ),
			'date_descending'                 => __( 'Date Descending', 'filebird' ),
			'by_modified'                     => __( 'By Modified', 'filebird' ),
			'modified_ascending'              => __( 'Modified Ascending', 'filebird' ),
			'modified_descending'             => __( 'Modified Descending', 'filebird' ),
			'by_author'                       => __( 'By Author', 'filebird' ),
			'by_file_name'                    => __( 'By File Name', 'filebird' ),
			'author_ascending'                => __( 'Author Ascending', 'filebird' ),
			'author_descending'               => __( 'Author Descending', 'filebird' ),
			'skip_and_deactivate'             => __( 'Skip & Deactivate', 'filebird' ),
			'deactivate'                      => __( 'Deactivate', 'filebird' ),
			'thank_you_so_much'               => __( 'Thank you so much!', 'filebird' ),
			'feedback'                        => array(
				'filebird_pro'           => __( 'I\'ve purchased FileBird Pro.', 'filebird' ),
				'no_features'            => __( 'It doesn\'t have the features I\'m looking for.', 'filebird' ),
				'not_working'            => __( 'Not work with my theme or other plugins.', 'filebird' ),
				'found_better_plugin'    => __( 'Found another plugin that works better.', 'filebird' ),
				'not_know_using'         => __( 'Don\'t know how to use it.', 'filebird' ),
				'temporary_deactivation' => __( 'This is just temporary, I will use it again.', 'filebird' ),
				'other'                  => __( 'Other', 'filebird' ),
			),
			'which_features'                  => __( 'Which features please?', 'filebird' ),
			'found_better_plugin_placeholder' => __( 'Please tell us which one', 'filebird' ),
			'not_know_using_document'         => __( 'Please read FileBird documentation <a target="_blank" href="https://ninjateam.gitbook.io/filebird/">here</a> or <a target="_blank" href="https://ninjateam.org/support/">chat with us</a> if you need help', 'filebird' ),
			'not_working_support'             => __( 'Please <a target="_blank" href="https://ninjateam.org/support/">ask for support here</a>, we will fix it for you.', 'filebird' ),
			'other_placeholder'               => __( 'Please share your thoughts...', 'filebird' ),
			'quick_feedback'                  => __( 'Want a better FileBird?', 'filebird' ),
			'deactivate_sadly'                => __( 'Sorry to see you walk away, please share why you want to deactivate FileBird?', 'filebird' ),
			'folder_limit_reached'            => __( 'Folder Limit Reached', 'filebird' ),
			'limit_folder'                    => __(
				'<p>FileBird Lite version supports up to 10 folders.<br>Please upgrade to have unlimited folders and other premium features!</p>
        <ul class="fbv-in_feature">
          <li>Unlimited Folders</li>
          <li>Sort Files / Folders</li>
          <li>Compatible with Premium Page Builders <span id="fbv-pagebuilder" class="njn-i"><svg viewBox="0 0 192 512"><path fill="currentColor" d="M20 424.229h20V279.771H20c-11.046 0-20-8.954-20-20V212c0-11.046 8.954-20 20-20h112c11.046 0 20 8.954 20 20v212.229h20c11.046 0 20 8.954 20 20V492c0 11.046-8.954 20-20 20H20c-11.046 0-20-8.954-20-20v-47.771c0-11.046 8.954-20 20-20zM96 0C56.235 0 24 32.235 24 72s32.235 72 72 72 72-32.235 72-72S135.764 0 96 0z"></path></svg></span></li>
          <li>Get Fast Updates</li>
          <li>Premium Technical Support</li>
          <li>One-time Payment</li>
          <li>30-day Refund Guarantee</li>
        </ul>',
				'filebird'
			),
			'pagebuilder_support'             => __( 'Including Divi, Fusion, Thrive Architect, WPBakery...', 'filebird' ),
			'upgrade_to_pro'                  => __( 'Upgrade to FileBird Pro now', 'filebird' ),
			'success'                         => __( 'Success.', 'filebird' ),
			'filebird_db_updated'             => __( 'Congratulations. Successfully imported!', 'filebird' ),
			'go_to_media'                     => __( 'Go To Media', 'filebird' ),
			'update_noti_title'               => __( 'FileBird 4 Update Required', 'filebird' ),
			'update_noti_desc'                => __( 'You\'re using the new FileBird 4. Please import database to view your folders correctly.', 'filebird' ),
			'update_noti_btn'                 => __( 'Import now', 'filebird' ),
			'import_failed'                   => __( 'Import failed. Please try again or <a href="https://ninjateam.org/support" target="_blank">contact our support</a>.', 'filebird' ),
			'purchase_code_missing'           => __( 'Please enter your Purchase Code.', 'filebird' ),
			'envato_token_missing'            => __( 'Please enter your Personal Access Token or get one.', 'filebird' ),
			'envato_invalid_license'          => __( 'Can not active your License, please try again.', 'filebird' ),
			'settings'                        => __( 'Settings', 'filebird' ),
			'fb_settings'                     => __( 'FileBird Settings', 'filebird' ),
			'select_default_startup_folder'   => __( 'Select a default startup folder:', 'filebird' ),
			'set_setting_success'             => __( 'Settings saved', 'filebird' ),
			'set_setting_fail'                => __( 'Failed to save settings. Please try again!', 'filebird' ),
			'unlock_new_features_title'       => __( 'Unlock new features', 'filebird' ),
			'unlock_new_features_desc'        => __( 'To use FileBird folders with your current page builder/plugin, please upgrade to PRO version.', 'filebird' ),
			'do_more_with_filebird_title'     => __( 'Do more with FileBird PRO', 'filebird' ),
			'do_more_with_filebird_desc'      => __( 'You\'re using a third party plugin, which is supported in FileBird PRO. Please upgrade to browse files faster and get more done.', 'filebird' ),
			'go_pro'                          => __( 'Go Pro', 'filebird' ),
			'view_details'                    => __( 'View details.', 'filebird' ),
			'turn_off_for_7_days'             => __( 'Turn off for 7 days', 'filebird' ),
			'collapse'                        => __( 'Collapse', 'filebird' ),
			'expand'                          => __( 'Expand', 'filebird' ),
			'uploaded'                        => __( 'Uploaded', 'filebird' ),
			'lessThanAMin'                    => __( 'Less than a min', 'filebird' ),
			'totalSize'                       => __( 'Total size', 'filebird' ),
			'move'                            => __( 'Move', 'filebird' ),
			'item'                            => __( 'item', 'filebird' ),
			'items'                           => __( 'items', 'filebird' ),
			'import_folder_to_filebird'       => __( 'Import folders to FileBird', 'filebird' ),
			'go_to_import'                    => __( 'Go to import', 'filebird' ),
			'no_thanks'                       => __( 'No, thanks', 'filebird' ),
			'import_some_folders'             => __( 'You have some folders created by other media plugins. Would you like to import them?', 'filebird' ),
			'default_tree_view'               => __( 'Default Tree View', 'filebird' ),
			'flat_tree_view'                  => __( 'Flat Tree View', 'filebird' ),
			'imported'                        => __( 'Imported!', 'filebird' ),
			'please_try_again'                => __( 'Please try again.', 'filebird' ),
			'successfully_exported'           => __( 'Successfully exported!', 'filebird' ),
			'successfully_imported'           => __( 'Successfully imported!', 'filebird' ),
			'active_to_use_feature'           => __( 'Please activate FileBird license to use this feature.', 'filebird' ),
			'by_size'                         => __( 'By Size', 'filebird' ),
			'size_ascending'                  => __( 'Size Ascending', 'filebird' ),
			'size_descending'                 => __( 'Size Descending', 'filebird' ),
			'PRO'                             => __( 'PRO', 'filebird' ),
			'change_color'                    => __( 'Change Color', 'filebird' ),
			'processing'                      => __( 'Processing...', 'filebird' ),
			'all_folders'                     => __( 'All folders', 'filebird' ),
			'common_folders'                  => __( 'Common folders', 'filebird' ),
			'all_folders_description'         => __( 'This option imports the common folder tree and user-based folder trees.', 'filebird' ),
			'common_folders_description'      => __( 'This option imports only the common folder tree.', 'filebird' ),
			'user_folders_description'        => __( 'This option imports the folder tree created by', 'filebird' ),
			'add_your_first_folder'             => __( 'Add your first folder', 'filebird' ),
			'add_your_first_folder_description' => __( 'You don\'t have any folder. Add folder to easily manage your files.', 'filebird' ),
			'add_folder'                        => __( 'Add Folder', 'filebird' ),
			'contact_support'                   => __( 'Contact Support', 'filebird' ),
		);
		return $translation;
	}
}

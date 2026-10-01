<?php
/**
 * Core Rollback
 *
 * @author  Andy Fragen
 * @license MIT
 * @link    https://github.com/afragen/core-rollback
 * @package core-rollback
 */

namespace Fragen\Rollback;

/*
 * Exit if called directly.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Settings
 */
class Settings {
	/**
	 * Load hooks.
	 *
	 * @return void
	 */
	public function load_hooks() {
		add_action( 'admin_init', [ $this, 'add_settings' ] );
		add_action( is_multisite() ? 'network_admin_menu' : 'admin_menu', [ $this, 'add_plugin_menu' ] );
		add_action( 'admin_init', [ $this, 'update_settings' ] );
		add_action( 'network_admin_edit_rollback', [ $this, 'update_settings' ] );
	}

	/**
	 * Add/register setttings.
	 *
	 * @return void
	 */
	public function add_settings() {
		register_setting( 'rollback_settings', 'rollback_settings' );
		add_settings_section(
			'core_rollback',
			null,
			[ $this, 'print_core_rollback' ],
			'rollback'
		);
		add_settings_field(
			'core_versions',
			__( 'Core Releases', 'core-rollback' ),
			[ $this, 'version_dropdown' ],
			'rollback',
			'core_rollback',
			[ 'core' => new Core() ]
		);
	}

	/**
	 * Print settings section blurb.
	 *
	 * @return void
	 */
	public function print_core_rollback() {
		global $wp_version;

		echo '<div class="notice notice-warning fade">';
		echo '<p>' . wp_kses_post( __( '<strong>WARNING:</strong> Downgrading WordPress Core may leave your site in an unusable state.', 'core-rollback' ) ) . '</p>';
		echo '</div>';
		echo '<div class="notice notice-info fade">';
		echo '<p>' . wp_kses_post( __( '<strong>INFO:</strong> Depending upon your current PHP version, some versions of WordPress core will not be available for rollback.', 'core-rollback' ) ) . '</p>';
		echo '</div>';

		esc_html_e( 'Rollback to latest release or any outdated, secure release version of WordPress Core.', 'core-rollback' );

		if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
			echo '<div class="notice notice-info fade is-dismissible">';
			echo '<p>' . wp_kses_post( __( '<strong>INFO:</strong> Your site is currently using a block theme. Block themes require at least WordPress 5.9.', 'core-rollback' ) ) . '</p>';
			echo '</div>';
		}

		echo '<ol>';
		echo( '<li>' . esc_html__( 'Select the rollback version from the dropdown menu.', 'core-rollback' ) . '</li>' );
		echo( '<li>' . esc_html__( 'Click the `Rollback` button. You will be automatically re-directed to the `update-core.php` page.', 'core-rollback' ) . '</li>' );
		echo( '<li>' . esc_html__( 'Use the `Re-install` button to rollback to the selected version.', 'core-rollback' ) . '</li>' );
		echo '</ol>';
	}

	/**
	 * Add options page.
	 */
	public function add_plugin_menu() {
		$parent     = is_multisite() ? 'settings.php' : 'tools.php';
		$capability = is_multisite() ? 'manage_network_options' : 'manage_options';

		add_submenu_page(
			$parent,
			esc_html__( 'Rollback Core', 'core-rollback' ),
			esc_html_x( 'Rollback Core', 'Menu item', 'core-rollback' ),
			$capability,
			'rollback',
			[ $this, 'create_admin_page' ]
		);
	}

	/**
	 * Options page callback.
	 */
	public function create_admin_page() {
		$action      = is_multisite() ? 'edit.php?action=rollback' : 'options.php';
		$form_action = 'update-core.php?action=do-core-reinstall';
		$submit      = __( 'Rollback', 'core-rollback' );
		$disabled    = ! empty( $this->filter_if_block_themes() ) ? '' : 'disabled';

		echo '<div class="wrap">';
		echo '<h2>' . esc_html__( 'Rollback Core', 'core-rollback' ) . '</h2>';
		echo '<form method="post" action="' . esc_attr( $form_action ) . '" name="upgrade" class="upgrade">';
		settings_fields( 'rollback_settings' );
		do_settings_sections( 'rollback' );
		submit_button( $submit, $disabled );
		echo '</form>';
		echo '</div>';
	}

	/**
	 * Make version dropdown.
	 *
	 * @param  array $args Array of args, [ 'core' => new Core() ].
	 *
	 * @return void
	 */
	public function version_dropdown( $args ) {
		$items = array_keys( $args['core']::$core_versions );

		// Filter out WP versions with deprecations and current PHP version.
		$items = array_filter(
			$items,
			function ( $item ) {
				if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
					return version_compare( $item, '5.9', '>=' );
				}
				if ( version_compare( phpversion(), '8.0', '<' ) ) {
					return version_compare( $item, '4.0', '>=' );
				} else {
					return version_compare( $item, '5.3', '>=' );
				}
			}
		);

		if ( ! empty( $this->filter_if_block_themes() ) ) {
			echo "<select id='core_dropdown' name='versions[core_dropdown]'>";
			foreach ( $items as $item ) {
				echo '<option value="' . esc_attr( $item ) . '">' . esc_attr( $item ) . '</option>';
			}
			echo '</select>';
		}
	}

	/**
	 * Return array of core versions compatible with block themes.
	 *
	 * @return array
	 */
	private function filter_if_block_themes() {
		$core  = new Core();
		$items = array_keys( $core::$core_versions );
		if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
			$items = array_filter(
				$items,
				function ( $item ) {
					return version_compare( $item, '5.9', '>=' );
				}
			);
		}
		return $items;
	}

	/**
	 * Update single site and network settings.
	 * Used when plugin is network activated to save settings.
	 *
	 * @link http://wordpress.stackexchange.com/questions/64968/settings-api-in-multisite-missing-update-message
	 * @link http://benohead.com/wordpress-network-wide-plugin-settings/
	 */
	public function update_settings() {
		// Exit if improper privileges.
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ), 'rollback_settings-options' ) ) {
			return;
		}

		if ( ! isset( $_POST['_wp_http_referer'] ) ) {
			return false;
		}

		if ( isset( $_POST['option_page'] ) &&
			'rollback_settings' === sanitize_title_with_dashes( wp_unslash( $_POST['option_page'] ) )
		) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$options = isset( $_POST['versions'] ) ? wp_unslash( $_POST['versions'] ) : [];
			// phpcs:enable

			set_site_transient( '_core_rollback', $options, 15 );
			wp_safe_redirect( \network_admin_url( 'update-core.php?force-check=1' ) );
			exit;
		}
	}
}

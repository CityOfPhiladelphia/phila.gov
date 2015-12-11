<?php

class wfUpdateCheck {

	private $needs_core_update = false;
	private $core_update_version = 0;
	private $plugin_updates = array();
	private $theme_updates = array();

	public function __construct() {

	}

	/**
	 * @return bool
	 */
	public function needsAnyUpdates() {
		return $this->needsCoreUpdate() || count($this->getPluginUpdates()) > 0 || count($this->getThemeUpdates()) > 0;
	}

	/**
	 * Check for any core, plugin or theme updates.
	 *
	 * @return $this
	 */
	public function checkAllUpdates() {
		return $this->checkCoreUpdates()
			->checkPluginUpdates()
			->checkThemeUpdates();
	}

	/**
	 * Check if there is an update to the WordPress core.
	 *
	 * @return $this
	 */
	public function checkCoreUpdates() {
		$this->needs_core_update = false;

		if (!function_exists('wp_version_check')) {
			require_once(ABSPATH . WPINC . '/update.php');
		}
		if (!function_exists('get_preferred_from_update_core')) {
			require_once(ABSPATH . 'wp-admin/includes/update.php');
		}

		wp_version_check(); // Check for Core updates
		$update = get_preferred_from_update_core();

		if (isset($update->response) && $update->response == 'upgrade') {
			$this->needs_core_update = true;
			$this->core_update_version = $update->current;
		}

		return $this;
	}

	/**
	 * Check if any plugins need an update.
	 *
	 * @return $this
	 */
	public function checkPluginUpdates() {
		$this->plugin_updates = array();

		if (!function_exists('wp_update_plugins')) {
			require_once(ABSPATH . WPINC . '/update.php');
		}

		wp_update_plugins(); // Check for Plugin updates
		$update_plugins = get_site_transient('update_plugins');

		if ($update_plugins && !empty($update_plugins->response)) {
			foreach ($update_plugins->response as $plugin => $vals) {
				if (!function_exists('get_plugin_data')) {
					require_once ABSPATH . '/wp-admin/includes/plugin.php';
				}
				$pluginFile = wfUtils::getPluginBaseDir() . $plugin;
				$data = get_plugin_data($pluginFile);
				$data['pluginFile'] = $pluginFile;
				$data['newVersion'] = $vals->new_version;
				$this->plugin_updates[] = $data;
			}
		}

		return $this;
	}

	/**
	 * Check if any themes need an update.
	 *
	 * @return $this
	 */
	public function checkThemeUpdates() {
		$this->theme_updates = array();

		if (!function_exists('wp_update_themes')) {
			require_once(ABSPATH . WPINC . '/update.php');
		}

		wp_update_themes();  // Check for Theme updates
		$update_themes = get_site_transient('update_themes');

		if ($update_themes && (!empty($update_themes->response))) {
			if (!function_exists('wp_get_themes')) {
				require_once ABSPATH . '/wp-includes/theme.php';
			}
			$themes = wp_get_themes();
			foreach ($update_themes->response as $theme => $vals) {
				foreach ($themes as $name => $themeData) {
					if (strtolower($name) == $theme) {
						$this->theme_updates[] = array(
							'newVersion' => $vals['new_version'],
							'package'    => $vals['package'],
							'URL'        => $vals['url'],
							'Name'       => $themeData['Name'],
							'name'       => $themeData['Name'],
							'version'    => $themeData['Version']
						);
					}
				}
			}
		}
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function needsCoreUpdate() {
		return $this->needs_core_update;
	}

	/**
	 * @return int
	 */
	public function getCoreUpdateVersion() {
		return $this->core_update_version;
	}

	/**
	 * @return array
	 */
	public function getPluginUpdates() {
		return $this->plugin_updates;
	}

	/**
	 * @return array
	 */
	public function getThemeUpdates() {
		return $this->theme_updates;
	}
}

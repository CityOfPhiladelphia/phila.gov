<?php
namespace FileBird\Page;

defined( 'ABSPATH' ) || exit;
/**
 * Settings Page
 */
class Settings {
	protected static $instance = null;
	private $pageId            = null;
	public $settingPageSuffix  = '';

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
			self::$instance->doHooks();
		}

		return self::$instance;
	}

	private function doHooks() {
		add_filter( 'plugin_action_links_' . NJFB_PLUGIN_BASE_NAME, array( $this, 'addActionLinks' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );

		add_action( 'admin_init', array( $this, 'registerSettings' ) );
		add_action( 'admin_footer', array( $this, 'adminFooter' ) );
		add_action( 'admin_menu', array( $this, 'settingsMenu' ) );
	}

	public function settingsMenu() {
		$filebirdIcon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCI+PHBhdGggZD0iTTEwLDRINEMyLjg5LDQgMiw0Ljg5IDIsNlYxOEEyLDIgMCAwLDAgNCwyMEgyMEEyLDIgMCAwLDAgMjIsMThWOEMyMiw2Ljg5IDIxLjEsNiAyMCw2SDEyTDEwLDRaIiBmaWxsPSIjYTdhYWFkIi8+PC9zdmc+';

		add_menu_page( 'FileBird', 'FileBird', 'manage_options', 'filebird-settings', null, $filebirdIcon );

		$this->settingPageSuffix = add_submenu_page( 'filebird-settings', __( 'FileBird Settings', 'filebird' ), __( 'FileBird Settings', 'filebird' ), 'manage_options', 'filebird-settings', array( $this, 'settingsPage' ), 0 );
		add_submenu_page(
			'filebird-settings',
			'',
			'<span>' . __( 'Go Pro', 'filebird' ) . '</span>',
			'manage_options',
			'go_filebird_pro',
			array( $this, 'goProRedirects' ),
			100
		);
	}

	public function adminFooter() {
		?>
	<style>
	body.admin-color-fresh #adminmenu #toplevel_page_filebird-settings a[href="admin.php?page=go_filebird_pro"] {
		color: #00BC28;
		font-weight: bold;
		position: relative;
		}

	body.admin-color-fresh #adminmenu #toplevel_page_filebird-settings a[href="admin.php?page=go_filebird_pro"]::after {
		content: '';
		position: absolute;
		width: 4px;
		top: 0;
		bottom: 0;
		left: 0;
		background: green;
		}
	</style>
	<script>
	jQuery(document).ready(function() {
		jQuery('#toplevel_page_filebird-settings a[href="admin.php?page=go_filebird_pro"]').click(function(event) {
			event.preventDefault()
			window.open('https://1.envato.market/GoPro-FileBird-Premium', '_blank')
		})
	})
	</script>
		<?php
	}

	public function goProRedirects() {
		if ( empty( $_GET['page'] ) ) {
			return;
		}

		if ( 'go_filebird_pro' === $_GET['page'] ) {
			// wp_redirect( 'https://1.envato.market/GoPro-FileBird-Premium' );
			?>
				<script>window.location.href = "https://1.envato.market/GoPro-FileBird-Premium"</script>
			<?php
		}
	}

	public function settingsPage() {
		include_once NJFB_PLUGIN_PATH . 'views/pages/html-settings.php';
	}

	public function plugin_row_meta( $links, $file ) {
		if ( strpos( $file, 'filebird.php' ) !== false ) {
			$new_links = array(
				'doc' => '<a href="https://ninjateam.gitbook.io/filebird/" target="_blank">' . __( 'Documentation', 'filebird' ) . '</a>',
			);

			$links = array_merge( $links, $new_links );
		}

		return $links;
	}

	public function addActionLinks( $links ) {
		$settingsLinks = array(
			'<a href="' . admin_url( 'admin.php?page=' . $this->getPageId() ) . '">' . esc_html__( 'Settings', 'filebird' ) . '</a>',
		);

		$links[] = '<a target="_blank" href="https://1.envato.market/FileBirdPro" style="color: #43B854; font-weight: bold">' . __( 'Go Pro', 'filebird' ) . '</a>';
		return array_merge( $settingsLinks, $links );
	}

	public function getPageId() {
		if ( null == $this->pageId ) {
			$this->pageId = NJFB_PREFIX . '-settings';
		}

		return $this->pageId;
	}
	public function registerSettings() {
		$settings = array(
			'njt_fbv_folder_per_user',
			'njt_fbv_allow_svg_upload',
			'njt_fbv_default_folder',
		);
		foreach ( $settings as $k => $v ) {
			register_setting( 'njt_fbv', $v );
		}
	}
}

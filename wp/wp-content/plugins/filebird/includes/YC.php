<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'YayCommerce' ) ) {
	class YayCommerce {
		private $autoInstallUrl = '';
		private $nonce          = '';
		public function __construct() {
			if ( ! function_exists( 'WC' ) || defined( 'YAYMAIL_VERSION' ) ) {
				return;
			}

			$noti_sale = get_option( 'yaymail_noti_sale' );
			if ( ! empty( $noti_sale ) ) {
				return;
			}

			if ( function_exists( 'current_user_can' ) && current_user_can( 'install_plugins' ) ) {
				$this->nonce          = wp_create_nonce( 'install-plugin_yaymail' );
				$this->autoInstallUrl = self_admin_url( 'update.php?action=install-plugin&plugin=yaymail&_wpnonce=' . $this->nonce );
			} else {
				$this->autoInstallUrl = admin_url( 'plugin-install.php?s=yaymail&tab=search&type=term' );
			}

			add_action( 'admin_init', array( $this, 'init' ) );
		}

		public function init() {
			add_action( 'admin_notices', array( $this, 'notification' ) );
			add_action( 'wp_ajax_njt_yaycommerce_dismiss', array( $this, 'ajax_dismiss_plugin' ) );
		}

		public function ajax_dismiss_plugin() {
			check_ajax_referer( 'install-plugin_yaymail', 'nonce', true );
			update_option( 'yaymail_noti_sale', 1 );
			wp_send_json_success();
		}

		public function notification() {
			if ( function_exists( 'get_current_screen' ) ) {
				$screen = get_current_screen();
				if ( ! in_array( $screen->id, array( 'woocommerce_page_wc-settings', 'woocommerce_page_wc-addons' ) ) ) {
					return;
				}
			} else {
				return;
			}

			wp_enqueue_script( 'yaycommerce', NJFB_PLUGIN_URL . 'assets/js/yc.js', array(), NJFB_VERSION, true );
			wp_localize_script(
				'yaycommerce',
				'yaycommerce',
				array(
					'nonce' => $this->nonce,
				)
			);

			?>
				<div class="notice notice-info is-dismissible" id="njt-yc">
					<div class="njt-yc-wrapper">
					<h3><?php esc_html_e( 'Email Customizer for WooCommerce', 'filebird' ); ?></h3>
					<p style="margin: 17px 0"><?php esc_html_e( 'YayMail helps you easily customize your WooCommerce emails with email builder. Try it today!', 'filebird' ); ?></p>
					<p>
						<a href="<?php echo esc_url( $this->autoInstallUrl ); ?>" aria-label="More information about YayMail" data-title="YayMail" class="button button-primary"><?php esc_html_e( 'Install for Free', 'filebird' ); ?></a>
						<a href="javascript:;" id="njt-yc-noti-dismiss"><?php esc_html_e( 'No, Thanks', 'filebird' ); ?></a>
					</p>
					</div>
				</div>
				<style>
					#njt-yc-noti-dismiss{
						margin-left: 10px;
						text-decoration: none;
					}

					.njt-yc-wrapper{
						padding: 5px 0 10px;
					}
				</style>
			<?php
		}
	}

	new YayCommerce();
}

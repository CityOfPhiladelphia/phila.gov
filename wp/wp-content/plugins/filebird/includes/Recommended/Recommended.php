<?php
defined( 'ABSPATH' ) || exit;
require_once ABSPATH . 'wp-admin/includes/plugin-install.php';

if ( ! class_exists( 'YayRecommended' ) ) {
	class YayRecommended {

		public $pluginPrefix      = '';
		public $subMenuSlug       = '';
		public $recommendedPlugin = array();

		public function __construct( $pluginPrefix ) {
			$this->pluginPrefix      = $pluginPrefix;
			$this->recommendedPlugin = $this->get_recommended_plugins();
			$this->doHooks();
		}

		public function doHooks() {
			add_action(
				'init',
				function() {
					add_action( 'admin_menu', array( $this, 'admin_menu' ) );
					add_action( 'admin_footer', array( $this, 'add_global_script_styles' ) );
					add_action( 'wp_ajax_yay_recommended_get_plugin_data', array( $this, 'yay_recommended_get_plugin_data' ) );
					add_action( 'wp_ajax_yay_recommended_activate_plugin', array( $this, 'yay_recommended_activate_plugin' ) );
					add_action( 'wp_ajax_yay_recommended_upgrade_plugin', array( $this, 'yay_recommended_upgrade_plugin' ) );
				}
			);
		}

		private function get_recommended_plugins() {
			$recommendedPlugins = array(
				'filebird'          => array(
					'slug'              => 'filebird',
					'name'              => __( 'FileBird - WordPress Media Library Folders & File Manager', 'filebird' ),
					'short_description' => __( 'Organize thousands of WordPress media files in folders / categories at ease.', 'filebird' ),
					'icon'              => 'https://ps.w.org/filebird/assets/icon-128x128.gif?rev=2299145',
					'download_link'     => 'https://downloads.wordpress.org/plugin/filebird.zip',
					'type'              => array( 'featured' ),
					'version'           => 0,
				),
				'yaymail'           => array(
					'slug'              => 'yaymail',
					'name'              => __( 'YayMail - WooCommerce Email Customizer', 'filebird' ),
					'short_description' => __( 'Customize WooCommerce email templates with live preview & drag and drop email builder.', 'filebird' ),
					'icon'              => 'https://ps.w.org/yaymail/assets/icon-256x256.gif?rev=2599198',
					'download_link'     => 'https://downloads.wordpress.org/plugin/yaymail.zip',
					'type'              => array( 'featured', 'woocommerce' ),
					'version'           => 0,
				),
				'yaycurrency'       => array(
					'slug'              => 'yaycurrency',
					'name'              => __( 'YayCurrency - WooCommerce Multi-Currency Switcher', 'filebird' ),
					'short_description' => __( 'WooCommerce Multi-Currency made easy, powerful, and flexible.', 'filebird' ),
					'icon'              => 'https://ps.w.org/yaycurrency/assets/icon-256x256.png?rev=2550570',
					'download_link'     => 'https://downloads.wordpress.org/plugin/yaycurrency.zip',
					'type'              => array( 'featured', 'woocommerce' ),
					'version'           => 0,
				),
				'yayswatches'       => array(
					'slug'              => 'yayswatches',
					'name'              => __( 'YaySwatches - Variation Swatches for WooCommerce', 'filebird' ),
					'short_description' => __( 'Optimize your variable product showcase with color swatches, image swatches, custom images, buttons, and more!', 'filebird' ),
					'icon'              => 'https://ps.w.org/yayswatches/assets/icon-256x256.png?rev=2757155',
					'download_link'     => 'https://downloads.wordpress.org/plugin/yayswatches.zip',
					'type'              => array( 'woocommerce' ),
					'version'           => 0,
				),
				'yayextra'          => array(
					'slug'              => 'yayextra',
					'name'              => __( 'YayExtra - WooCommerce Extra Product Options', 'filebird' ),
					'short_description' => __( 'Add WooCommerce product options like personal engraving, print-on-demand items, gifts, custom canvas prints, and personalized products.', 'filebird' ),
					'icon'              => 'https://ps.w.org/yayextra/assets/icon-256x256.png?rev=2776349',
					'download_link'     => 'https://downloads.wordpress.org/plugin/yayextra.zip',
					'type'              => array( 'woocommerce' ),
					'version'           => 0,
				),
				'yaypricing'        => array(
					'slug'              => 'yaypricing',
					'name'              => __( 'YayPricing - WooCommerce Dynamic Pricing & Discounts', 'filebird' ),
					'short_description' => __( 'Offer automatic pricing and discounts to design a powerful marketing strategy for your WooCommerce store.', 'filebird' ),
					'icon'              => 'https://yaycommerce.com/wp-content/uploads/2022/11/yaypricing-256x256-1.png',
					'download_link'     => 'https://yaycommerce.com/yaypricing-woocommerce-dynamic-pricing-and-discounts/',
					'type'              => array( 'woocommerce' ),
					'version'           => 0,
				),
				'yaysmtp'           => array(
					'slug'              => 'yaysmtp',
					'name'              => __( 'YaySMTP - Simple WP SMTP Mail', 'filebird' ),
					'short_description' => __( 'Send WordPress emails successfully with WP Mail SMTP via your favorite Mailer.', 'filebird' ),
					'icon'              => 'https://ps.w.org/yaysmtp/assets/icon-256x256.png?rev=2437984',
					'download_link'     => 'https://downloads.wordpress.org/plugin/yaysmtp.zip',
					'type'              => array( 'featured', 'marketing' ),
					'version'           => 0,
				),
				'wp-whatsapp'       => array(
					'slug'              => 'wp-whatsapp',
					'name'              => __( 'WP Chat App', 'filebird' ),
					'short_description' => __( 'Integrate WhatsApp experience directly into your WordPress website.', 'filebird' ),
					'icon'              => 'https://ps.w.org/wp-whatsapp/assets/icon-256x256.png?rev=2725670',
					'download_link'     => 'https://downloads.wordpress.org/plugin/wp-whatsapp.zip',
					'type'              => array( 'featured' ),
					'version'           => 0,
				),
				'filester'          => array(
					'slug'              => 'filester',
					'name'              => __( 'Filester - File Manager Pro', 'filebird' ),
					'short_description' => __( 'Best WordPress file manager without FTP access. Clean design. No need to upgrade because this…', 'filebird' ),
					'icon'              => 'https://ps.w.org/filester/assets/icon-256x256.gif?rev=2305540',
					'download_link'     => 'https://downloads.wordpress.org/plugin/filester.zip',
					'type'              => array( 'management' ),
					'version'           => 0,
				),
				'cf7-multi-step'    => array(
					'slug'              => 'cf7-multi-step',
					'name'              => __( 'Multi Step for Contact Form 7', 'filebird' ),
					'short_description' => __( 'Break your looooooong form into user-friendly steps.', 'filebird' ),
					'icon'              => 'https://ps.w.org/cf7-multi-step/assets/icon-256x256.png?rev=1994366',
					'download_link'     => 'https://downloads.wordpress.org/plugin/cf7-multi-step.zip',
					'type'              => array( 'management' ),
					'version'           => 0,
				),
				'cf7-database'      => array(
					'slug'              => 'cf7-database',
					'name'              => __( 'Database for Contact Form 7', 'filebird' ),
					'short_description' => __( 'Automatically save all data submitted via Contact Form 7 to your database.', 'filebird' ),
					'icon'              => 'https://ps.w.org/cf7-database/assets/icon-128x128.png?rev=1614091',
					'download_link'     => 'https://downloads.wordpress.org/plugin/cf7-database.zip',
					'type'              => array( 'management' ),
					'version'           => 0,
				),
				'wp-duplicate-page' => array(
					'slug'              => 'wp-duplicate-page',
					'name'              => __( 'WP Duplicate Page', 'filebird' ),
					'short_description' => __( 'Clone WordPress page, post, custom post types.', 'filebird' ),
					'icon'              => 'https://ps.w.org/wp-duplicate-page/assets/icon-256x256.gif?rev=2432962',
					'download_link'     => 'https://downloads.wordpress.org/plugin/wp-duplicate-page.zip',
					'type'              => array( 'management' ),
					'version'           => 0,
				),
				'notibar'           => array(
					'slug'              => 'notibar',
					'name'              => __( 'Notibar - Notification Bar for WordPress', 'filebird' ),
					'short_description' => __( 'Customizer for sticky header, notification bar, alert, promo code, marketing campaign, top banner.', 'filebird' ),
					'icon'              => 'https://ps.w.org/notibar/assets/icon-256x256.png?rev=2387855',
					'download_link'     => 'https://downloads.wordpress.org/plugin/notibar.zip',
					'type'              => array( 'marketing' ),
					'version'           => 0,
				),
			);
			return $recommendedPlugins;
		}

		public function admin_menu() {
			$this->subMenuSlug = add_submenu_page( 'nta_whatsapp', __( 'Recommended Plugins', 'filebird' ), __( 'Recommended Plugins', 'filebird' ), 'manage_options', 'nta_whatsapp_recommended_plugins', array( $this, 'recommended_plugins_view' ) );
		}

		public function recommended_plugins_view() {
			if ( function_exists( 'WC' ) ) {
				$featuredTab = '<li class="plugin-install-tab plugin-install-featured" data-tab="featured"><a href="#" >' . esc_html__( 'Featured', 'filebird' ) . '</a> </li>';
				$wooTab      = '<li class="plugin-install-tab plugin-install-woocommerce" data-tab="woocommerce"><a href="#" class="current" aria-current="page">' . esc_html__( 'WooCommerce', 'filebird' ) . '</a> </li>';
			} else {
				$featuredTab = '<li class="plugin-install-tab plugin-install-featured" data-tab="featured"><a href="#" class="current" aria-current="page">' . esc_html__( 'Featured', 'filebird' ) . '</a> </li>';
				$wooTab      = '<li class="plugin-install-tab plugin-install-woocommerce" data-tab="woocommerce"><a href="#" >' . esc_html__( 'WooCommerce', 'filebird' ) . '</a> </li>';
			}
			?>
			<style>
				.yay-recommended-plugins-layout {
					margin-top: 20px;
				}
				.wrap .notice, .wrap .error {
					display: none !important;
				}
				.yay-recommended-plugins-layout-header {
					background: #fff;
					box-sizing: border-box;
					padding: 0;
					z-index: 1001;
				}
				
				.yay-recommended-plugins-header{
					display: flex;
					flex-wrap: wrap;
					justify-content: space-between;
					align-items: center;
					position: relative;
					box-sizing: border-box;
					margin: 12px 0 25px;
					padding: 0 10px;
					width: 100%;
					box-shadow: 0 1px 1px rgb(0 0 0 / 4%);
					border: 1px solid #c3c4c7;
					background: #fff;
					color: #50575e;
					font-size: 13px;
				}
				.yay-recommended-plugins-header-title {
					font-size: 1.2em;
					margin-left: 8px;
				}
				.yay-recommended-plugins-layout .plugin-card .desc, .plugin-card .name {
					margin-right: 0;
				}
				.yay-recommended-plugins-layout .plugin-card-bottom {
					display: flex;
					justify-content: space-between;
					align-items: center;
				}
				.yay-recommended-plugins-layout .plugin-action-buttons,
				.yay-recommended-plugins-layout .plugin-action-buttons li,
				.plugin-card .column-rating, .plugin-card .column-updated {
					margin-bottom: 0;
				}
				.yay-recommended-plugins-layout .loading-process {
					pointer-events: none;
				}
				.yay-recommended-plugins-layout .column-rating {
					min-height: 30px;
					line-height: 30px;
				}
				.yay-recommended-plugins-layout .plugin-status-inactive {
					color: #ff4d4f;
				}
				.yay-recommended-plugins-layout .plugin-status-active {
					color: #52c41a;
				}
				.yay-recommended-plugins-layout .plugin-status-not-install {
					color: #1d2327;
				}
				@media screen and (max-width: 1100px) and (min-width: 782px), (max-width: 480px) {
					.yay-recommended-plugins-layout .plugin-card .column-compatibility, 
					.yay-recommended-plugins-layout .plugin-card .column-updated {
						width: calc(100% - 220px);
					}
					.yay-recommended-plugins-layout .plugin-action-buttons li .button,
					.yay-recommended-plugins-layout .plugin-action-buttons {
						margin: 0;
					}
				}
			</style>
			<div class="wrap">
				<div class="yay-recommended-plugins-layout">
					<div class="yay-recommended-plugins-layout-header">
						<div class="wp-filter yay-recommended-plugins-header">
							<h2 class="yay-recommended-plugins-header-title"><?php esc_attr_e( 'Recommended Plugins', 'filebird' ); ?></h2>
							<ul class="filter-links">
								<?php
								 echo wp_kses_post( $featuredTab );
								?>
								<li class="plugin-install-tab plugin-install-all" data-tab="all"><a href="#"><?php esc_html_e( 'All', 'filebird' ); ?></a></li>
								<?php
								 echo wp_kses_post( $wooTab );
								?>
								<li class="plugin-install-tab plugin-install-management" data-tab="management"><a href="#"><?php esc_html_e( 'Management', 'filebird' ); ?></a> </li>
								<li class="plugin-install-tab plugin-install-marketing" data-tab="marketing"><a href="#"><?php esc_html_e( 'Marketing', 'filebird' ); ?></a></li>
							</ul>
						</div>
					</div>
					<div class="wp-list-table widefat plugin-install">
						<div id="the-list"></div>
					</div>
				</div>
			</div>
			<?php
		}

		public function add_global_script_styles() {
			$screen = get_current_screen();
			if ( $screen->base !== $this->subMenuSlug ) {
				return;
			}
			$activeWC = function_exists( 'WC' );
			wp_enqueue_script( 'plugin-install' );
			wp_enqueue_script( 'thickbox' );
			wp_enqueue_style( 'thickbox' );
			wp_register_script( "{$this->pluginPrefix}-yayrecommended", plugin_dir_url( __FILE__ ) . '/assets/js/recommended.js', array( 'jquery' ), '1.0', true );
			wp_localize_script(
				"{$this->pluginPrefix}-yayrecommended",
				'yayRecommended',
				array(
					'nonce'      => wp_create_nonce( 'yay_recommended_nonce' ),
					'admin_ajax' => admin_url( 'admin-ajax.php' ),
					'woo_active' => $activeWC,
				)
			);
			wp_enqueue_script( "{$this->pluginPrefix}-yayrecommended" );
		}

		public function yay_recommended_get_plugin_data() {
			try {
				if ( isset( $_POST['tab'] ) ) {
					$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
					if ( ! wp_verify_nonce( $nonce, 'yay_recommended_nonce' ) ) {
						wp_send_json_error( array( 'mess' => __( 'Nonce is invalid', 'filebird' ) ) );
					}
					$tab                = sanitize_text_field( $_POST['tab'] );
					$recommendedPlugins = array();
					$recommendedData    = apply_filters( 'yay_recommended_plugins_excluded', $this->recommendedPlugin );
					foreach ( $recommendedData as $key => $plugin ) {
						if ( in_array( $tab, $plugin['type'] ) || 'all' === $tab ) {
							$recommendedPlugins[ $key ] = $plugin;
						}
					}
					ob_start();
					$path = plugin_dir_path( __FILE__ ) . '/views/content.php';
					include $path;
					$html = ob_get_contents();
					ob_end_clean();
					wp_send_json_success(
						array(
							'mess' => __( 'Get data success', 'filebird' ),
							'html' => $html,
						)
					);
				}
			} catch ( \Exception $ex ) {
				wp_send_json_error(
					array(
						'mess' => __( 'Error exception.', 'filebird' ),
						array(
							'error' => $ex,
						),
					)
				);
			} catch ( \Error $ex ) {
				wp_send_json_error(
					array(
						'mess' => __( 'Error.', 'filebird' ),
						array(
							'error' => $ex,
						),
					)
				);
			}
		}

		public function yay_recommended_activate_plugin() {
			try {
				if ( isset( $_POST['file'] ) ) {
					$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
					if ( ! wp_verify_nonce( $nonce, 'yay_recommended_nonce' ) ) {
						wp_send_json_error( array( 'mess' => __( 'Nonce is invalid', 'filebird' ) ) );
					}
					$file   = sanitize_text_field( $_POST['file'] );
					$result = activate_plugin( $file );

					if ( is_wp_error( $result ) ) {
						wp_send_json_error(
							array(
								'mess' => $result->get_error_message(),
							)
						);
					}
					wp_send_json_success(
						array(
							'mess' => __( 'Activate success', 'filebird' ),
						)
					);
				}
			} catch ( \Exception $ex ) {
				wp_send_json_error(
					array(
						'mess' => __( 'Error exception.', 'filebird' ),
						array(
							'error' => $ex,
						),
					)
				);
			} catch ( \Error $ex ) {
				wp_send_json_error(
					array(
						'mess' => __( 'Error.', 'filebird' ),
						array(
							'error' => $ex,
						),
					)
				);
			}
		}

		public function yay_recommended_upgrade_plugin() {
			try {
				require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
				require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
				require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';
				require_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';
				if ( isset( $_POST['plugin'] ) ) {
					$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
					if ( ! wp_verify_nonce( $nonce, 'yay_recommended_nonce' ) ) {
						wp_send_json_error( array( 'mess' => __( 'Nonce is invalid', 'filebird' ) ) );
					}
					$plugin   = sanitize_text_field( $_POST['plugin'] );
					$type     = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : 'install';
					$skin     = new \WP_Ajax_Upgrader_Skin();
					$upgrader = new \Plugin_Upgrader( $skin );
					if ( 'install' === $type ) {
						$result = $upgrader->install( $plugin );
						if ( is_wp_error( $result ) ) {
							wp_send_json_error(
								array(
									'mess' => $result->get_error_message(),
								)
							);
						}
						$args       = array(
							'slug'   => $upgrader->result['destination_name'],
							'fields' => array(
								'short_description' => true,
								'icons'             => true,
								'banners'           => false,
								'added'             => false,
								'reviews'           => false,
								'sections'          => false,
								'requires'          => false,
								'rating'            => false,
								'ratings'           => false,
								'downloaded'        => false,
								'last_updated'      => false,
								'added'             => false,
								'tags'              => false,
								'compatibility'     => false,
								'homepage'          => false,
								'donate_link'       => false,
							),
						);
						$pluginData = plugins_api( 'plugin_information', $args );
						if ( $pluginData && ! is_wp_error( $pluginData ) ) {
							$installStatus = install_plugin_install_status( $pluginData );
							$activePlugin  = activate_plugin( $installStatus['file'] );
							if ( is_wp_error( $activePlugin ) ) {
								wp_send_json_error(
									array(
										'mess' => $activePlugin->get_error_message(),
									)
								);
							} else {
								wp_send_json_success(
									array(
										'mess' => __( 'Install success', 'filebird' ),
									)
								);
							}
						} else {
							wp_send_json_error(
								array(
									'mess' => 'Error',
								)
							);
						}
					} else {
						$is_active = is_plugin_active( $plugin );
						$result    = $upgrader->upgrade( $plugin );
						if ( is_wp_error( $result ) ) {
							wp_send_json_error(
								array(
									'mess' => $result->get_error_message(),
								)
							);
						} else {
							activate_plugin( $plugin );
							wp_send_json_success(
								array(
									'mess'   => __( 'Update success', 'filebird' ),
									'active' => $is_active,
								)
							);
						}
					}
				}
			} catch ( \Exception $ex ) {
				wp_send_json_error(
					array(
						'mess' => __( 'Error exception.', 'filebird' ),
						array(
							'error' => $ex,
						),
					)
				);
			} catch ( \Error $ex ) {
				wp_send_json_error(
					array(
						'mess' => __( 'Error.', 'filebird' ),
						array(
							'error' => $ex,
						),
					)
				);
			}
		}

		public function check_pro_version_exists( $pluginDetail ) {
			$existProVer = false;
			$allPlugin   = get_plugins();
			if ( 'filebird' === $pluginDetail['slug'] ) {
				$existProVer = array_key_exists( 'filebird-pro/filebird.php', $allPlugin ) === true ? 'filebird-pro/filebird.php' : false;
			}
			if ( 'yaymail' === $pluginDetail['slug'] ) {
				if ( array_key_exists( 'yaymail-pro/yaymail.php', $allPlugin ) ) {
					$existProVer = 'yaymail-pro/yaymail.php';
				} elseif ( array_key_exists( 'email-customizer-for-woocommerce/yaymail.php', $allPlugin ) ) {
					$existProVer = 'email-customizer-for-woocommerce/yaymail.php';
				}
			}
			if ( 'yaycurrency' === $pluginDetail['slug'] ) {
				if ( array_key_exists( 'yaycurrency-pro/yay-currency.php', $allPlugin ) ) {
					$existProVer = 'yaycurrency-pro/yay-currency.php';
				} elseif ( array_key_exists( 'multi-currency-switcher/yay-currency.php', $allPlugin ) ) {
					$existProVer = 'multi-currency-switcher/yay-currency.php';
				}
			}
			if ( 'yaysmtp' === $pluginDetail['slug'] ) {
				$existProVer = array_key_exists( 'yaysmtp-pro/yay-smtp.php', $allPlugin ) === true ? 'yaysmtp-pro/yay-smtp.php' : false;
			}
			if ( 'yayswatches' === $pluginDetail['slug'] ) {
				$existProVer = array_key_exists( 'yayswatches-pro/yay-swatches.php', $allPlugin ) === true ? 'yayswatches-pro/yay-swatches.php' : false;
			}
			if ( 'yayextra' === $pluginDetail['slug'] ) {
				$existProVer = array_key_exists( 'yayextra-pro/yayextra.php', $allPlugin ) === true ? 'yayextra-pro/yayextra.php' : false;
			}
			if ( 'yaypricing' === $pluginDetail['slug'] ) {
				$existProVer = array_key_exists( 'yaypricing-pro/yaypricing.php', $allPlugin ) === true ? 'yaypricing-pro/yaypricing.php' : false;
			}
			if ( 'cf7-multi-step' === $pluginDetail['slug'] ) {
				$existProVer = array_key_exists( 'contact-form-7-multi-step-pro/contact-form-7-multi-step.php', $allPlugin ) === true ? 'contact-form-7-multi-step-pro/contact-form-7-multi-step.php' : false;
			}
			if ( 'cf7-database' === $pluginDetail['slug'] ) {
				$existProVer = array_key_exists( 'contact-form-7-database-pro/cf7-database.php', $allPlugin ) === true ? 'contact-form-7-database-pro/cf7-database.php' : false;
			}
			if ( 'wp-whatsapp' === $pluginDetail['slug'] ) {
				$existProVer = array_key_exists( 'whatsapp-for-wordpress/whatsapp.php', $allPlugin ) === true ? 'whatsapp-for-wordpress/whatsapp.php' : false;
			}
			return $existProVer;
		}
	}
}

if ( ! class_exists( 'FileBirdRecommended' ) ) {
	class FileBirdRecommended extends YayRecommended {

		public function __construct( $pluginPrefix ) {
			parent::__construct( $pluginPrefix );
			add_filter( 'yay_recommended_plugins_excluded', array( $this, 'exclude_recommended_plugins' ), 10, 1 );
		}

		public function exclude_recommended_plugins( $plugins ) {
			if ( array_key_exists( 'filebird', $plugins ) ) {
				unset( $plugins['filebird'] );
			}
			return $plugins;
		}

		public function admin_menu() {
			$this->subMenuSlug = add_submenu_page( 'filebird-settings', __( 'Recommended Plugins', 'filebird' ), __( 'Recommended', 'filebird' ), 'manage_options', 'filebird-settings-recommended-plugins', array( $this, 'recommended_plugins_view' ), 1 );
		}
	}

	new FileBirdRecommended( 'filebird' );
}

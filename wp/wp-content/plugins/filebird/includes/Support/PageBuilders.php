<?php
namespace FileBird\Support;

use FileBird\Controller\Folder;

defined( 'ABSPATH' ) || exit;

class PageBuilders {
	protected $folderController;

	public function __construct() {
		$this->folderController = Folder::getInstance();
		add_action( 'init', array( $this, 'prepareRegister' ) );
	}

	public function prepareRegister() {
		// Compatible for Elementor
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$this->registerForElementor();
		}
		// Compatible for WPBakery - Work normally

		// Compatible for Beaver Builder
		if ( class_exists( 'FLBuilderLoader' ) ) {
			$this->registerForBeaver();
		}

		// Brizy Builder
		if ( class_exists( 'Brizy_Editor' ) ) {
			$this->registerForBrizy();
		}

		// Cornerstone
		if ( class_exists( 'Cornerstone_Plugin' ) ) {
			$this->registerCornerstone();
		}

		// Compatible for Divi
		if ( class_exists( 'ET_Builder_Element' ) ) {
			$this->registerForDivi();
		}

		// Compatible for Thrive
		if ( defined( 'TVE_IN_ARCHITECT' ) || class_exists( 'Thrive_Quiz_Builder' ) ) {
			$this->registerForThrive();
		}

		// Fusion Builder
		if ( class_exists( 'Fusion_Builder_Front' ) ) {
			$this->registerForFusion();
		}

		// Avada Theme
		if ( ! class_exists( 'Fusion_Builder_Front' ) && defined( 'AVADA_VERSION' ) ) {
			$this->registerAvada();
		}

		// Oxygen Builder
		if ( defined( 'CT_VERSION' ) ) {
			$this->registerOxygenBuilder();
		}

		// Tatsu Builder
		if ( defined( 'TATSU_VERSION' ) ) {
			$this->registerTatsuBuilder();
		}

		// Dokan plugin
		if ( defined( 'DOKAN_PLUGIN_VERSION' ) ) {
			$this->registerForDokan();
		}

		// Themify
		if ( defined( 'THEMIFY_VERSION' ) && class_exists( 'Themify_Builder_Model' ) ) {
			$this->registerThemify();
		}

		// Bricks
		if ( defined( 'BRICKS_VERSION' ) ) {
			$this->registerBricksBuilder();
		}

		// BeTheme
		if ( defined( 'MFN_THEME_VERSION' ) ) {
			$this->registerBeBuilder();
		}

		// LearnPress
		if ( class_exists( 'LP_Addon_Frontend_Editor_Preload' ) ) {
			$this->registerLearnPress();
		}

		// Break Dance Builder
		if ( defined( '__BREAKDANCE_VERSION' ) ) {
			$this->registerBreakDance();
		}

		// YooTheme
		if ( class_exists( 'YOOtheme\Builder' ) ) {
			$this->registerYooTheme();
		}

		// Zion Builder
		if ( class_exists( 'ZionBuilder\Plugin' ) || function_exists( 'znb_kallyas_integration' ) ) {
			$this->registerZionBuilder();
		}
	}

	public function enqueueScripts( $is_enqueue_media = false, $is_enqueue_footer = false ) {
		if ( $is_enqueue_media ) {
			wp_enqueue_media();

        }

		if ( $is_enqueue_footer ) {
			add_action(
                'wp_footer',
                function() {
					$this->folderController->enqueueAdminScripts( 'pagebuilders' );
				}
            );
		}

		$this->folderController->enqueueAdminScripts( 'pagebuilders' );
	}

	public function registerForElementor() {
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'enqueueScripts' ) );
	}

	public function registerForBeaver() {
		add_action(
            'fl_before_sortable_enqueue',
            function() {
				$this->enqueueScripts( false, true );
			}
		);
	}

	public function registerForBrizy() {
		add_action( 'brizy_editor_enqueue_scripts', array( $this, 'enqueueScripts' ) );
	}

	public function registerCornerstone() {
		add_action( 'cornerstone_before_wp_editor', array( $this, 'enqueueScripts' ) );
	}

	public function registerForDivi() {
		add_action(
			'et_fb_enqueue_assets',
			function() {
				$this->enqueueScripts();
			}
		);
	}

	public function registerForThrive() {
		add_action( 'tcb_main_frame_enqueue', array( $this, 'enqueueScripts' ) );
	}

	public function registerForFusion() {
		add_action( 'fusion_builder_enqueue_live_scripts', array( $this, 'enqueueScripts' ) );
	}

	public function registerOxygenBuilder() {
		add_action( 'oxygen_enqueue_ui_scripts', array( $this, 'enqueueScripts' ) );
	}

	public function registerTatsuBuilder() {
		add_action( 'tatsu_builder_footer', array( $this, 'enqueueScripts' ) );
	}

	public function registerForDokan() {
		add_action(
			'dokan_enqueue_scripts',
			function() {
				if ( function_exists( 'dokan_is_seller_dashboard' ) ) {
					if ( ( dokan_is_seller_dashboard() || ( get_query_var( 'edit' ) && is_singular( 'product' ) ) ) || apply_filters( 'dokan_forced_load_scripts', false ) ) {
						$this->enqueueScripts();
					}
				}
			}
		);
	}

	public function registerThemify() {
		add_action(
             'wp_ajax_tb_load_editor',
            function() {
				$this->enqueueScripts( true );
			},
            9
		);
	}

	public function registerBricksBuilder() {
		if ( function_exists( 'bricks_is_builder' ) && \bricks_is_builder() ) {
			add_action( 'bricks_after_footer', array( $this, 'enqueueScripts' ) );
		}
	}

	public function registerAvada() {
		add_action( 'fusion_enqueue_live_scripts', array( $this, 'enqueueScripts' ) );
	}

	public function registerBeBuilder() {
		add_action(
             'admin_enqueue_scripts',
            function() {
				if ( ! wp_script_is( 'mfn-vbscripts', 'enqueued' ) ) {
					return;
				}

				$this->enqueueScripts();
			},
            PHP_INT_MAX
        );

		add_action(
             'wp_enqueue_scripts',
            function() {
				if ( ! wp_script_is( 'mfn-vbscripts', 'enqueued' ) ) {
					return;
				}

				$this->enqueueScripts();
			},
            PHP_INT_MAX
        );
	}

	public function registerLearnPress() {
		add_action( 'learnpress/addons/frontend_editor/enqueue_scripts', array( $this, 'enqueueScripts' ) );
	}

	public function registerBreakDance() {
		if ( isset( $_GET['breakdance_wpuiforbuilder_media'] ) && $_GET['breakdance_wpuiforbuilder_media'] ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueueScripts' ), 9 );
		}
	}

	public function registerYooTheme() {
		add_action( 'admin_print_footer_scripts-yootheme_customizer', array( $this, 'enqueueScripts' ) );
	}

	public function registerZionBuilder() {
		if ( class_exists( 'ZionBuilder\Plugin' ) ) {
			add_action( 'zionbuilder/editor/before_scripts', array( $this, 'enqueueScripts' ) );
		}

		if ( function_exists( 'znb_kallyas_integration' ) ) {
			add_action( 'znpb_editor_after_load_scripts', array( $this, 'enqueueScripts' ) );
		}
	}
}
<?php

namespace FileBird\Support;

defined( 'ABSPATH' ) || exit;

final class SupportController {
    public function __construct() {
        $this->init();
	}

    public function get_plugin_support() {
        $plugins = apply_filters(
            'fbv_support',
            array(
				'WPML',
				'Polylang',
                'DocumentGallery',
                'ACF',
                'PageBuilders',
            )
        );

        return $plugins;
    }

    public function init() {
        $plugins = $this->get_plugin_support();

        foreach ( $plugins as $plugin ) {
            $plugin_class = __NAMESPACE__ . "\\{$plugin}";
            new $plugin_class();
        }
    }
}
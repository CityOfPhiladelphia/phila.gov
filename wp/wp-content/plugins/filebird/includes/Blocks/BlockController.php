<?php

namespace FileBird\Blocks;

defined( 'ABSPATH' ) || exit;

final class BlockController {
    public function __construct() {
        if ( function_exists( 'register_block_type' ) ) {
            add_action( 'init', array( $this, 'register_blocks' ) );
        }
	}

    public function get_blocks() {
        $blocks = apply_filters(
            'fbv_blocks',
            array()
        );

        return $blocks;
    }

    public function register_blocks() {
        $blocks = $this->get_blocks();

        foreach ( $blocks as $block ) {
            $block_class = __NAMESPACE__ . "\\{$block}";
            new $block_class();
        }
    }
}
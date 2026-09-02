<?php

namespace FileBird\Blocks;

abstract class AbstractBlock {

    protected $namespace = 'filebird';

    protected $block_name = '';

    public function __construct() {
        $this->init();
    }

    abstract protected function enqueue_frontend_assets();

    protected function get_block_type() {
        return $this->namespace . '/' . $this->block_name;
    }

    protected function get_block_attributes() {
        return array();
    }

    protected function get_editor_dependencies() {
        return array();
    }

    protected function register_block_editor_script() {
        wp_register_script(
            $this->namespace . '-' . $this->block_name . '-js',
            NJFB_PLUGIN_URL . 'blocks/' . $this->block_name . '/dist/index.js',
            $this->get_editor_dependencies(),
            NJFB_VERSION,
            true
        );
        wp_register_style(
            $this->namespace . '-' . $this->block_name . '-css',
            NJFB_PLUGIN_URL . 'blocks/' . $this->block_name . '/dist/index.css',
            $this->get_editor_dependencies(),
            NJFB_VERSION
        );
    }

    public function render_callback( $attributes = array(), $content = '' ) {
        if ( ! is_admin() ) {
            $this->enqueue_frontend_assets();
        }
        return $this->render( $attributes, $content );
    }

    protected function render( $attributes, $content ) {

    }

    protected function get_render_callback() {
        return array( $this, 'render_callback' );
    }

    protected function register_block_type() {
        $this->register_block_editor_script();
        register_block_type(
            $this->get_block_type(),
            array(
                'editor_script'   => $this->namespace . '-' . $this->block_name . '-js',
                'editor_style'    => $this->namespace . '-' . $this->block_name . '-css',
                // 'style'           => $this->namespace . '-' . $this->block_name,
                'render_callback' => $this->get_render_callback(),
                'attributes'      => $this->get_block_attributes(),
			)
        );
    }

    protected function init() {
        $this->register_block_type();
    }
}
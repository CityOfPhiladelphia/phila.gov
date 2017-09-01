<?php

class wpcmsb_TagGenerator {

	private static $instance;

	private $panels = array();

	private function __construct() {}

	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function add( $id, $title, $callback, $options = array() ) {
		$id = trim( $id );

		if ( '' === $id || ! wpcmsb_is_name( $id ) ) {
			return false;
		}

		$this->panels[$id] = array(
			'title' => $title,
			'content' => 'tag-generator-panel-' . $id,
			'options' => $options,
			'callback' => $callback );

		return true;
	}

	public function print_buttons() {
		echo '<span id="tag-generator-list">';

		foreach ( (array) $this->panels as $panel ) {
			echo sprintf(
				'<a href="#TB_inline?width=900&height=500&inlineId=%1$s" class="thickbox button" title="%2$s">%3$s</a>',
				esc_attr( $panel['content'] ),
				esc_attr( sprintf(
					__( 'Form-tag Generator: %s', 'cms-block' ),
					$panel['title'] ) ),
				esc_html( $panel['title'] ) );
		}

		echo '</span>';
	}

	public function print_panels( wpcmsb_cmsblock $cms_block ) {
		foreach ( (array) $this->panels as $id => $panel ) {
			$callback = $panel['callback'];

			$options = wp_parse_args( $panel['options'], array() );
			$options = array_merge( $options, array(
				'id' => $id,
				'title' => $panel['title'],
				'content' => $panel['content'] ) );

			if ( is_callable( $callback ) ) {
				echo sprintf( '<div id="%s" class="hidden">',
					esc_attr( $options['content'] ) );
				echo sprintf(
					'<form action="" class="tag-generator-panel" data-id="%s">',
					$options['id'] );

				call_user_func( $callback, $cms_block, $options );

				echo '</form></div>';
			}
		}
	}

}

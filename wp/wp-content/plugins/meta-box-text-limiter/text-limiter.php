<?php
/**
 * Plugin Name: Meta Box Text Limiter
 * Plugin URI:  https://metabox.io/plugins/meta-box-text-limiter/
 * Description: Limit number of characters or words entered for text and textarea fields.
 * Version:     1.1.3
 * Author:      MetaBox.io
 * Author URI:  https://metabox.io
 *
 * @package    Meta Box
 * @subpackage Meta Box Text Limiter
 */

if ( ! class_exists( 'MB_Text_Limiter' ) ) {
	class MB_Text_Limiter {
		/**
		 * List of supported fields.
		 *
		 * @var array
		 */
		protected $types = array( 'text', 'textarea' );

		public function init() {
			add_action( 'rwmb_before', array( $this, 'register' ) );

			// Change the output of fields with limit.
			add_filter( 'rwmb_get_value', array( $this, 'get_value' ), 10, 2 );
			add_filter( 'rwmb_the_value', array( $this, 'get_value' ), 10, 2 );

			add_action( 'rwmb_enqueue_scripts', array( $this, 'enqueue' ) );
		}

		/**
		 * Register hook to change the output of text/textarea fields.
		 */
		public function register() {
			foreach ( $this->types as $type ) {
				add_filter( "rwmb_{$type}_html", array( $this, 'show' ), 10, 2 );
			}
		}

		/**
		 * Change the output of text/textarea fields.
		 *
		 * @param string $output HTML output of the field.
		 * @param array  $field  Field parameter.
		 *
		 * @return string
		 */
		public function show( $output, $field ) {
			if ( ! isset( $field['limit'] ) || ! is_numeric( $field['limit'] ) || ! $field['limit'] > 0 ) {
				return $output;
			}

			$type = isset( $field['limit_type'] ) ? $field['limit_type'] : 'character';
			$text = 'word' === $type ? __( 'Word Count', 'text-limiter' ) : __( 'Character Count', 'text-limiter' );

			return $output . '
				<div class="text-limiter" data-limit-type="' . esc_attr( $type ) . '">
					<span>' . esc_html( $text ) . ':
						<span class="counter">0</span>/<span class="maximum">' . esc_html( $field['limit'] ) . '</span>
					</span>
				</div>';
		}

		/**
		 * Filters the value of a field
		 *
		 * @see rwmb_get_field() in meta-box/inc/functions.php for explenation
		 *
		 * @param string $value Field value.
		 * @param array  $field Field parameters.
		 *
		 * @return string
		 */
		public function get_value( $value, $field ) {
			if ( empty( $field ) ) {
				return $value;
			}

			if ( ! in_array( $field['type'], $this->types, true ) || empty( $field['limit'] ) || ! is_numeric( $field['limit'] ) ) {
				return $value;
			}

			$type = isset( $field['limit_type'] ) ? $field['limit_type'] : 'character';
			if ( 'character' === $type ) {
				return function_exists( 'mb_substr' ) ? mb_substr( $value, 0, $field['limit'] ) : substr( $value, 0, $field['limit'] );
			}

			$value = preg_split( '/\s+/', $value, - 1, PREG_SPLIT_NO_EMPTY );
			$value = implode( ' ', array_slice( $value, 0, $field['limit'] ) );

			return $value;
		}

		public function enqueue() {
			// Use helper function to get correct URL to current folder, which can be used in themes/plugins.
			list( , $url ) = RWMB_Loader::get_path( dirname( __FILE__ ) );

			wp_enqueue_style( 'text-limiter', $url . 'text-limiter.css' );
			wp_enqueue_script( 'text-limiter', $url . 'text-limiter.js', array( 'jquery' ), '', true );
		}
	}

	$text_limiter = new MB_Text_Limiter();
	$text_limiter->init();
}

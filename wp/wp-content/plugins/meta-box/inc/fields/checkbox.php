<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'RWMB_Checkbox_Field' ) )
{
	class RWMB_Checkbox_Field extends RWMB_Field
	{
		/**
		 * Enqueue scripts and styles
		 *
		 * @return void
		 */
		static function admin_enqueue_scripts()
		{
			wp_enqueue_style( 'rwmb-checkbox', RWMB_CSS_URL . 'checkbox.css', array(), RWMB_VER );
		}

		/**
		 * Get field HTML
		 *
		 * @param mixed $meta
		 * @param array $field
		 *
		 * @return string
		 */
		static function html( $meta, $field )
		{
			return sprintf(
				'<input type="checkbox" class="rwmb-checkbox" name="%s" id="%s" value="1" %s>',
				$field['field_name'],
				$field['id'],
				checked( ! empty( $meta ), 1, false )
			);
		}

		/**
		 * Set the value of checkbox to 1 or 0 instead of 'checked' and empty string
		 * This prevents using default value once the checkbox has been unchecked
		 *
		 * @link https://github.com/rilwis/meta-box/issues/6
		 *
		 * @param mixed $new
		 * @param mixed $old
		 * @param int   $post_id
		 * @param array $field
		 *
		 * @return int
		 */
		static function value( $new, $old, $post_id, $field )
		{
			return empty( $new ) ? 0 : 1;
		}

		/**
		 * Output the field value
		 * Display 'Yes' or 'No' instead of '1' and '0'
		 *
		 * Note: we don't echo the field value directly. We return the output HTML of field, which will be used in
		 * rwmb_the_field function later.
		 *
		 * @use self::get_value()
		 * @see rwmb_the_field()
		 *
		 * @param  array    $field   Field parameters
		 * @param  array    $args    Additional arguments. Rarely used. See specific fields for details
		 * @param  int|null $post_id Post ID. null for current post. Optional.
		 *
		 * @return string HTML output of the field
		 */
		static function the_value( $field, $args = array(), $post_id = null )
		{
			$value = self::get_value( $field, $args, $post_id );

			return $value ? __( 'Yes', 'meta-box' ) : __( 'No', 'meta-box' );
		}
	}
}

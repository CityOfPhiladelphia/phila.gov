<?php
/**
 * This class defines a custom "phone" field type for Meta Box class in the following format: (###) ###-####
 *
 * @package Meta Box
 * @see http://metabox.io/?post_type=docs&p=390
 */
if ( class_exists( 'RWMB_Field' ) )
{
	class RWMB_Phone_Field extends RWMB_Field
	{
		/**
		 * Get field HTML
		 *
		 * @param mixed $meta
		 * @param array $field
		 *
		 * @return string
		 */
		static public function html( $meta, $field )
		{
      $phone_label_types = [ 'area', 'phone-co-code', 'phone-subscriber-number' ];

      if(!is_array($meta)){
        $meta = [];
        foreach ($phone_label_types as $label) {
          $meta[$label] = '';
        }
      }

      foreach ($phone_label_types as $label) {
        if( !array_key_exists($label ,$meta)){
          $meta[$label] = '';
        }
      }

			return sprintf(
				'(<input type="tel" name="%1$s[area]" id="%2$s_area" value="%3$s" size="3" class="rwmb-phone-area">)<input type="tel" name="%1$s[phone-co-code]" id="%2$s_phone-co-code" value="%4$s" size="3" class="rwmb-phone-co-code">-<input type="tel" name="%1$s[phone-subscriber-number]" id="%2$s_phone-subscriber-number" value="%5$s" size="4" class="rwmb-phone-subscriber-number">',
				$field['field_name'],
				$field['id'],
				$meta['area'],
        $meta['phone-co-code'],
        $meta['phone-subscriber-number']
			);
		}
	}
}
<?php
/**
*
* Shortcode for presenting content with a vertical rule
* @param @atts - quote - text to be displayed in pullquote
*                attribution - source of the original quote
*
* @package phila-gov_customization
*/

add_shortcode( 'vr', 'phila_vertical_rule' );

function phila_vertical_rule( $a, $content = null ) {
  return '<div class="vr">' . $content . '</div>';
}

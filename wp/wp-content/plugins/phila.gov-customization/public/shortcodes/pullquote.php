<?php
/**
*
* Shortcode for displaying pullquotes
* @param @atts - quote - text to be displayed in pullquote
*                attribution - source of the original quote
*
* @package phila-gov_customization
*/
function pullquote_shortcode($atts){
  $a = shortcode_atts( array(
    'quote' => '',
    'attribution' => '',
  ), $atts);

  $output = '';

  if ( $a['quote'] != '' ){
    $output .= '<div class="pullquote">';
    $output .= '<div class="quote-icon">&ldquo;</div>';
    $output .= '<p>' . $a['quote'] . '</p>';
    if($a['attribution'] != '') {
      $output .= '<span class="attribution"> - ' . $a['attribution'] . ' - </span>';
    }
    $output .= '</div>';

    return $output;
  } else {
    return;
  }
}

add_action( 'init', 'register_pullquote_shortcode' );

function register_pullquote_shortcode(){
   add_shortcode( 'pullquote', 'pullquote_shortcode' );
}

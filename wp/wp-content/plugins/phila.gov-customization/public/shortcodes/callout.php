<?php
/**
*
* Shortcode for displaying callouts
* @param @atts - summary - text to be displayed in callout
*                type - use 'important' to add important class
*
* @package phila-gov_customization
*/
function callout_shortcode($atts){
  $a = shortcode_atts( array(
    'summary' => '',
    'type' => '',
    'inline' => 'true',
  ), $atts);

  $output = '';

  if ( $a['summary'] != '' ){
    $output .= '<div class="callout';

    if($a['type'] == 'important') {
      $output .= ' ' . $a['type'] . ' ';
    }

    if($a['inline'] == 'true') {
      $output .= 'mtl">';
    } else  {
        $output .= 'mbn">';
    }

    $output .= '<p>' . $a['summary'] . '</p>';
    $output .= '</div>';

    return $output;
  } else {
    return;
  }
}

add_action( 'init', 'register_callout_shortcode' );

function register_callout_shortcode(){
   add_shortcode( 'callout', 'callout_shortcode' );
}

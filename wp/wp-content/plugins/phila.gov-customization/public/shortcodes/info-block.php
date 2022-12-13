<?php
/**
*
* Shortcode for displaying info-blocks
* @param @atts - summary - text to be displayed in info-blocks
*
* @package phila-gov_customization
*/
function info_block_shortcode($a, $content=null){

  $a = shortcode_atts( array(
    'paragraph-text' => false,
  ), $a);

  $output = '';

  if ( $content != '' ){
    $output .= '<div class="row">';
    $output .= '<div class="columns">';
    $output .= '<div class="panel info info-block mbl">';
    $output .= $a['paragraph-text'] === 'true' ? '<div class="paragraph">' : '<p>';

    $output .=  apply_filters('the_content', $content );
    $output .= $a['paragraph-text'] === 'true' ? '</div>' : '</p>';


    $output .= '</div>';
    $output .= '</div>';
    $output .= '</div>';


    return $output;
  } else {
    return;
  }
}

add_action( 'init', 'register_info_block_shortcode' );

function register_info_block_shortcode(){
  add_shortcode( 'info-block', 'info_block_shortcode' );
}

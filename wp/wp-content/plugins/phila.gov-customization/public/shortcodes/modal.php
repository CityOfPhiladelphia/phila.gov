<?php
/**
*
* Shortcode to pop content in a modal
* @param @atts - id - unique id for modal, in case of mutiple
*  button-text - clickable button text
*
* @package phila-gov_customization
*/
function modal_shortcode( $atts, $content = null ){

  $a = shortcode_atts( array(
    'id' => 'modal',
    'button-text' => 'Please add button-text attribute.',
  ), $atts);

  $output = '';

  if ( $content != '' ){
    $output .= '<a data-open="' . $a['id'] . '" class="button">';
    $output .= $a['button-text'];
    $output .= '</a>';
    $output .= '<div class="reveal center" id="' . $a['id'] . '" data-reveal data-deep-link="true">';
      $output .= $content;
    $output .= '<button class="close-button bg-white" data-close aria-label="Close modal" type="button">
     <span aria-hidden="true">&times;</span>
   </button>
  </div>';


    return $output;
  } else {
    return;
  }
}

add_action( 'init', 'register_modal_shortcode' );

function register_modal_shortcode(){
   add_shortcode( 'modal', 'modal_shortcode' );
}

<?php
/**
*
* Shortcode for parsing dates / times and applying some of our standards
* @param @atts -
*
* @package phila-gov_customization
*/
function standard_date_time_shortcode( $atts, $content = null ){

  $content = do_shortcode( $content );

  if ( $content != '') {
    $content = str_replace(array('Sep','12:00 am','12:00 pm','am','pm',':00'),array('Sept','midnight','noon','a.m.','p.m.',''), $content );
  }

  return $content;
}

add_action( 'init', 'register_standard_date_time_shortcode' );

function register_standard_date_time_shortcode(){
  add_shortcode( 'standard_date_time', 'standard_date_time_shortcode' );
}

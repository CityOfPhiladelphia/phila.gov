<?php
/**
*
* Shortcode for displaying filtered press releases
* @param @atts - conditionally add table wrapper, default false
*
* @package phila-gov_customization
*/

function longform_content_footnote_shortcode($atts, $content = null) {

  $a = shortcode_atts( array(
    'id' => 0,
  ), $atts );

  return '<i data-id='.$a['id'].' ref="footnote" data-content="'.$content.'" class="pls fas fa-space-station-moon" />';

  wp_reset_postdata();

}
add_action( 'init', 'register_longform_content_footnote_shortcode' );

function register_longform_content_footnote_shortcode(){
  add_shortcode( 'longform-footnote', 'longform_content_footnote_shortcode' );
}

<?php
/**
*
* Shortcode for displaying filtered press releases
* @param @atts - conditionally add table wrapper, default false
*
* @package phila-gov_customization
*/

function longform_content_footnote_shortcode($atts) {

  $a = shortcode_atts( array(
    'id' => 0,
  ), $atts );

  $content = 'link copied';
  $trigger = 'click';
  // return '<i v-tooltip="{ "content": "link copied!", "trigger": "click" }" class="pls fas fa-space-station-moon" />';
  return '<i class="pls fas fa-space-station-moon has-tooltip" data-original-title="null"></i>';

  wp_reset_postdata();

}
add_action( 'init', 'register_longform_content_footnote_shortcode' );

function register_longform_content_footnote_shortcode(){
  add_shortcode( 'longform-footnote', 'longform_content_footnote_shortcode' );
}

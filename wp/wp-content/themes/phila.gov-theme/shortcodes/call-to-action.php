<?php
/**
*
* Shortcode for displaying filtered press releases
* @param @atts - conditionally add table wrapper, default false
*
* @package phila-gov_customization
*/

function call_to_action_shortcode($atts) {

  $a = shortcode_atts( array(
    'title' => '',
    'description' => '',
    'link_text' => '',
    'url' => '',
    'external' => '',
    'is_survey' => '',
    'is_modal' => '',
  ), $atts );

  include( locate_template( 'partials/posts/call-to-action.php' ) );

  wp_reset_postdata();

}
add_action( 'init', 'register_call_to_action_shortcode' );

function register_call_to_action_shortcode(){
  add_shortcode( 'call-to-action', 'call_to_action_shortcode' );
}

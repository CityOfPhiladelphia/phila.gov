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
    'title' => 'title',
    'description' => 'description',
    'link_text' => 'link_text',
    'url' => 'url',
    'external' => 0,
    'is_survey' => 0,
    'is_modal' => 0,
    'modal_icon' => '',
  ), $atts );

  ob_start();
  include( locate_template( 'partials/posts/call-to-action.php' ) );
  $content = ob_get_clean();
  return $content;

}
add_action( 'init', 'register_call_to_action_shortcode' );

function register_call_to_action_shortcode(){
  add_shortcode( 'call-to-action', 'call_to_action_shortcode' );
}

<?php
/**
*
* Shortcode for displaying filtered press releases
* @param @atts - conditionally add table wrapper, default false
*
* @package phila-gov_customization
*/

function trashday_alerts_shortcode($atts) {

  $a = shortcode_atts( array(
    'is_in_table' => 0,
    'icon_text' => 0,
    'icon_padding' => 0,
  ), $atts );

  include( locate_template( 'partials/posts/trashday-alerts.php' ) );

  wp_reset_postdata();

}
add_action( 'init', 'register_trashday_shortcode' );

function register_trashday_shortcode(){
  add_shortcode( 'trashday-alerts', 'trashday_alerts_shortcode' );
}

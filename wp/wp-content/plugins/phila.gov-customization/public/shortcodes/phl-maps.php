<?php
/**
* @since 0.22.0
*
* Shortcode for displaying maps from http://phl.maps.arcgis.com/
* @param @atts - map_id: the 'webmap' value in a phl.maps.arcgis.com address
*                width: optional. accepts 'full' for full-width display
*
* @package phila-gov_customization
*/

add_action( 'init', 'register_phl_maps_shortcode' );

function phl_maps_shortcode($atts) {
  $a = shortcode_atts( array(
   'map_id' => '',
   'width'  => 'default',
  ), $atts );

  if ( $a['map_id'] != '' ){
    $output = '<div class="embed-container"><iframe width="500" height="400" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" title="Construction" src="//phl.maps.arcgis.com/apps/Embed/index.html?webmap=' . $a['map_id'] . '&amp;center=-75.1634622,39.9526239&amp;zoom=true&amp;extent=-75.2279,39.9466,-75.149,39.9812&amp;scale=true&amp;disable_scroll=true&amp;theme=light" class="phl-maps-' . $a['width'] . '-width"></iframe></div>';
    return $output;
  }
}

function register_phl_maps_shortcode(){
   add_shortcode( 'phl-maps', 'phl_maps_shortcode' );
}

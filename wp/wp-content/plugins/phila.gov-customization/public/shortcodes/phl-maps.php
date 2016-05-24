<?php
/**
*
* Shortcode for displaying maps from http://phl.maps.arcgis.com/
* @param @atts - mapid: the 'webmap' value in a phl.maps.arcgis.com address
*                center: optional. the coordinates used to center the map (defaults to City Hall)
*                level: optional. zoom level of the map
*                width: optional. accepts 'full' for full-width display
*
* @package phila-gov_customization
*/

add_action( 'init', 'register_phl_maps_shortcode' );

function phl_maps_shortcode($atts) {
  $a = shortcode_atts( array(
   'mapid' => '',
   'center' => '-75.1634622,39.9526239',
   'level' => '2',
   'width'  => 'default',
  ), $atts );

  if ( $a['mapid'] != '' ){
    $output = '<div class="embed-container"><iframe width="500" height="400" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" title="Construction" src="//phl.maps.arcgis.com/apps/Embed/index.html?webmap=' . $a['mapid'] . '&amp;center=' . $a['center'] . '&amp;level=' . $a['level'] . '&amp;zoom=true&amp;extent=-75.2279,39.9466,-75.149,39.9812&amp;scale=true&amp;disable_scroll=true&amp;theme=light&amp;logoimage=//cityofphiladelphia.github.io/patterns/images/city-of-philadelphia-mobile.png&amp;logolink=//phl.maps.arcgis.com/home/webmap/viewer.html?webmap=' . $a['mapid'] . '" class="phl-maps-' . $a['width'] . '-width"></iframe></div>';
    return $output;
  }
}

function register_phl_maps_shortcode(){
   add_shortcode( 'phl-maps', 'phl_maps_shortcode' );
}

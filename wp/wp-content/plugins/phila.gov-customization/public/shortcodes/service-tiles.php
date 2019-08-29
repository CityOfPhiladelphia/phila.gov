<?php
/**
*
* Shortcode for displaying service tiles in body copy
* @param @atts - ids - ID of service pages to be displayed
*
* @package phila-gov_customization
*/
function service_tile_shortcode($atts){
  $a = shortcode_atts( array(
    'ids' => '',
  ), $atts);

  $output = '';

  if ( $a['ids'] != '' ){
    $ids = explode(',', $a['ids']);
    $output .= '<div class="row equal-height fat-gutter">';
        foreach( $ids  as $id ) :
          if ( get_post_type(trim($id)) !== 'service_page' ) {
            $output .= '<div class="row columns end phila-placeholder">Please use the ID of a service page.</div>';
            return $output;
          }
          $output .= '<div class="' . (count($ids) == 1  ? 'columns end mbl">' : 'medium-8 columns end mbl">');
          $output .= '<a class="card sub-topic equal';
          $output .= '" href="' . get_the_permalink($id) . '">';
          $output .=   '<div class="content-block"><h3>'. get_the_title($id) . '</h3>';
          $output .=   '<p>' . rwmb_meta( 'phila_meta_desc', array() ,$id) . '</p>';
          $output .=  '</div>
            </a>
          </div>';
        endforeach;
        $output .= '</div>';

    return $output;
  } else {
    return;
  }
}

add_action( 'init', 'register_service_tile_shortcode' );

function register_service_tile_shortcode(){
  add_shortcode( 'service', 'service_tile_shortcode' );
}

<?php
/**
*
* Shortcode for displaying program tiles in body copy
* @param @atts - id - ID of program page to be displayed
*
* @package phila-gov_customization
*/
function program_tile_shortcode($atts){
  $a = shortcode_atts( array(
    'id' => '',
  ), $atts);

  $output = '';

  if ( $a['id'] != '' ){
    $ids = explode(',', $a['id']);
    $output .= '<div class="row">
    <div class="columns">
      <div class="row fat-gutter">';
        foreach( $ids  as $id ) :
          if ( phila_get_selected_template($id) !== 'prog_landing_page' && phila_get_selected_template($id) !== 'prog_off_site'  ) {
            $output .= '<div class="columns medium-8 end phila-placeholder">Please use the ID of a program homepage.</div>';
            return $output;
          }
          $output .= '<div class="' . (count($ids) == 1  ? 'columns end mbl">' : 'medium-8 columns end mbl">');
          $output .= '<a class="card program-card small' . (count($ids) == 1  ? ' vertical' : '' );
          $output .= '" href="' . get_the_permalink($id) . '">';
          $img = rwmb_meta( 'prog_header_img', $args = array( 'size' => 'medium', 'limit' => 1 ), $id );
          $img = reset( $img );
          $output .=  '<img src="' . $img['url'] . '" alt="' . $img['alt'] . '">';
          $output .=   '<div class="content-block"><h4 class="h3">'. get_the_title($id) . '</h4>';
          $output .=   '<p>' . rwmb_meta( 'phila_meta_desc', array() ,$id) . '</p>';
          $output .=  '</div>
            </a>
          </div>';
        endforeach;
        $output .= '</div>
    </div>
  </div>';

    return $output;
  } else {
    return;
  }
}

add_action( 'init', 'register_program_tile_shortcode' );

function register_program_tile_shortcode(){
  add_shortcode( 'program', 'program_tile_shortcode' );
}

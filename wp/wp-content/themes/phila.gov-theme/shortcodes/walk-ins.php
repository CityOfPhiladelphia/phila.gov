<?php
/**
*
* Shortcode for displaying walk-ins component
* @param @atts - none
*
* @package phila-gov_customization
*/
function walk_ins_shortcode($atts, $content=null){
  $post_type = get_post_type();

  $allowed_html = [
    'a'      => [
        'href'  => [],
        'title' => [],
        'class' => [],
      ],
    'br'     => [],
    'em'     => [],
    'strong' => [],
    'class' => [],
    'p' => [],
  ];

  $output = '';

  if ( $content != '' ){
    if ($post_type === 'service_page'){
      $output .= '<div class="walk-ins grid-x mvl">';
      $output .= '<div class="cell small-3 medium-3 walk-ins-icon">';
    }else{
      $output .= '<div class="walk-ins grid-x mvxl">';
      $output .= '<div class="cell small-3 medium-2 walk-ins-icon">';
    }
    
    $output .= '<i class="fas fa-street-view fa-3x"></i></div>';
    if ($post_type === 'service_page'){
      $output .= '<div class="cell small-21 medium-21 walk-ins-copy">';
    }else{
    $output .= '<div class="cell small-21 medium-22 walk-ins-copy">';
    }

    $output .=  wp_kses($content, $allowed_html) ;
    $output .= '</div></div></div>';

    return $output;
  } else {
    return;
  }
}

add_action( 'init', 'register_walk_ins_shortcode' );

function register_walk_ins_shortcode(){
  add_shortcode( 'walk-ins', 'walk_ins_shortcode' );
}

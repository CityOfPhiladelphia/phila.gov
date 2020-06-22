<?php
/**
*
* Shortcode for displaying walkins component
* @param @atts - none
*
* @package phila-gov_customization
*/
function walkins_shortcode($atts, $content=null){

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
  ];

  $output = '';

  if ( $content != '' ){
    $output .= '<div class="walkins grid-x mtxl">
    <div class="cell small-3 medium-2 walkins-icon"><i class="fas fa-street-view fa-3x"></i></div>
    <div class="cell small-21 medium-22 walkins-copy">';
    $output .= '<p>' . wp_kses($content, $allowed_html). '</p>';
    $output .= '</div></div>';

    return $output;
  } else {
    return;
  }
}

add_action( 'init', 'register_walkins_shortcode' );

function register_walkins_shortcode(){
  add_shortcode( 'walkins', 'walkins_shortcode' );
}

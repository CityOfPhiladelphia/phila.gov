<?php
/**
*
* Shortcode for displaying callouts
* @param @atts - summary - text to be displayed in callout
*                type - use 'important' to add important class
*
* @package phila-gov_customization
*/
function callout_shortcode($atts, $content=null){
  $a = shortcode_atts( array(
    'type' => '',
    'inline' => 'true',
  ), $atts);

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
		'ol' => [],
    'ul' => [],
    'li' => [],
    'p' => [],
  ];

  $output = '';

  if ( $content != '' ){
    $output .= '<div class="callout';

    $output .= $a['type'] == 'important' ? ' ' . $a['type'] . ' ' : ' ';

    $output .= $a['inline'] == 'true' ? 'mtl">' : 'mbn">';

    $output .= wp_kses($content, $allowed_html);
    $output .= '</div>';

    return $output;
  } else {
    return;
  }
}

add_action( 'init', 'register_callout_shortcode' );

function register_callout_shortcode(){
  add_shortcode( 'callout', 'callout_shortcode' );
}

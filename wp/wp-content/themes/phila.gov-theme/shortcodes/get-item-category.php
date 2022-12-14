<?php
/**
*
* Shortcode for easy display of an item's category, or categories
* [display_category]
*
* @package phila-gov_customization
*/
function category_display( $atts ) {
  global $post;

  $categories = get_the_category();

  return phila_get_current_department_name($categories);
}

add_action( 'init', 'register_category_display' );

function register_category_display(){
   add_shortcode( 'display_category', 'category_display' );
}

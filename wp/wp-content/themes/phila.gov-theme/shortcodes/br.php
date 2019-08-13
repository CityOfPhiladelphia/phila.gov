<?php 
/* 
 * Allow line breaks in WYSIWYG, useful when <br> tag needs to be added inside other markup or another shortcode
 * [br]
 */


function phila_add_linebreak() {
  return '<br>';
}

add_action( 'init', 'register_br_shortcode' );

function register_br_shortcode(){
  add_shortcode('br', 'phila_add_linebreak' );
}

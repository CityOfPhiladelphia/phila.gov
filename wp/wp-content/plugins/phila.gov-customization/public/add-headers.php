<?php

//Add Last-Modified back to headers
// Hooked into template_redirect so this doesn't run when logged in
add_action( 'template_redirect', 'phila_last_modified' );

function phila_last_modified() {
  global $post;
  if( isset( $post ) && is_single() ){
    $timestamp = strtotime( $post->post_date_gmt );
    if ( isset( $post->post_modified ) ) {
      $timestamp = strtotime( $post->post_modified );
      $date = date( "D, d M Y H:i:s", $timestamp );
      header( "Last-Modified: " . $date . " GMT" );
    }
  }
}

add_action('template_redirect', 'phila_allow_gzip');

function phila_allow_gzip(){
  header("Accept-Encoding: gzip");
}

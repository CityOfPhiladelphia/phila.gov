<?php

add_action( 'after_wp_tiny_mce', 'custom_after_wp_tiny_mce' );

function custom_after_wp_tiny_mce() {
    printf( '<script type="text/javascript" src="%s"></script>',  plugins_url('js/tiny-mce.js', __FILE__) );
}

/**
 * Add in a core button that's disabled by default
 */
add_filter( 'mce_buttons_2', 'phila_mce_2_addons' );

function phila_mce_2_addons( $buttons ) {
  $buttons[] = 'superscript';
  $buttons[] = 'subscript';

  return $buttons;
}

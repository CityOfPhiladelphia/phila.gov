<?php

add_action( 'after_wp_tiny_mce', 'custom_after_wp_tiny_mce' );

function custom_after_wp_tiny_mce() {
    printf( '<script type="text/javascript" src="%s"></script>',  plugins_url('js/tiny-mce.js', __FILE__) );
}

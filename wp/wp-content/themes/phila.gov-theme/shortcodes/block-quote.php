<?php
/**
*
* Shortcode for displaying a block quote that takes in text parameter
* @param @atts - 'text'
*
* @package phila-gov_customization
*/

function block_quote_shortcode($atts, $content = null) {

    $a = shortcode_atts( array(
        'text' => $content, // Use the content between shortcode tags as the default
    ), $atts );

    ob_start();
    include( locate_template( 'partials/posts/block-quote.php' ) );
    $output = ob_get_clean();

    return $output;
}
add_action( 'init', 'register_block_quote_shortcode' );

function register_block_quote_shortcode(){
    add_shortcode( 'block-quote', 'block_quote_shortcode' );
}

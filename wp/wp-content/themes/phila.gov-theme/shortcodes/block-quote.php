<?php
/**
*
* Shortcode for displaying filtered press releases
* @param @atts - conditionally add table wrapper, default false
*
* @package phila-gov_customization
*/

function block_quote_shortcode($atts) {

$a = shortcode_atts( array(
    'text' => 'text',
), $atts );

ob_start();
    include( locate_template( 'partials/posts/block-quote.php' ) );
    $content = ob_get_clean();
    return $content;

}
add_action( 'init', 'register_block_quote_shortcode' );

function register_block_quote_shortcode(){
    add_shortcode( 'block-quote', 'block_quote_shortcode' );
}

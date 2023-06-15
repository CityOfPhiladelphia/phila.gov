<?php
/**
*
* Shortcode that creates an iframe to host an embedded form
* @param @atts - 'url', 'height'
*
* @package phila-gov_customization
*/

function embedded_form_shortcode($atts) {
    // Extract the parameters passed to the shortcode
    $params = shortcode_atts(array(
        'url' => '',
        'height' => '300px',
    ), $atts);

    // Sanitize the URL parameter
    $url = esc_url($params['url']);

    // Sanitize the height parameter
    $height = esc_attr($params['height']);

    // Generate the iframe HTML code
    $iframe = '<iframe src="' . $url . '" style="border: none; height: ' . $height . '; width: 100%;"></iframe>';

    return $iframe;
}

add_action( 'init', 'register_embedded_form_shortcode' );

function register_embedded_form_shortcode(){
    add_shortcode( 'embedded-form', 'embedded_form_shortcode' );
}


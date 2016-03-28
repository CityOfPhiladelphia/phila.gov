<?php

/* Filter the press release link to include URL */


add_filter( 'post_type_link', 'phila_press_release_link' , 11, 2 );

function phila_press_release_link( $post_link, $id = 0 ) {

    $post = get_post( $id );

    if ( is_wp_error( $post ) || 'press_release' != $post->post_type || empty( $post->post_name ) )
        return $post_link;

    // Get the genre:
    $terms = get_the_terms( $post->ID, 'category' );

    if( is_wp_error( $terms ) || !$terms ) {
        $cat = 'uncategorised';
    } else {
        $cat_obj = array_pop($terms);
        $cat = $cat_obj->slug;
    }

    return home_url( user_trailingslashit( "press-releases/$cat/$post->post_name" ) );
}

/**
* @since 0.5.11
*
* Filter the post_type_link to add the category into url
*
* @package phila-gov_customization
*/

add_filter( 'post_type_link', 'phila_news_link' , 10, 2 );
function phila_news_link( $post_link, $id = 0 ) {

    $post = get_post( $id );

    if ( is_wp_error( $post ) || 'news_post' != $post->post_type || empty( $post->post_name ) )
        return $post_link;

    // Get the genre:
    $terms = get_the_terms( $post->ID, 'category' );

    if( is_wp_error( $terms ) || !$terms ) {
        $cat = 'uncategorised';
    } else {
        $cat_obj = array_pop($terms);
        $cat = $cat_obj->slug;
    }

    return home_url( user_trailingslashit( "news/$cat/$post->post_name" ) );
}

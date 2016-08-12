<?php
$children = get_pages( 'child_of=' . $post->ID . '&sort_column=menu_order&post_type=' . get_post_type() );
$content = $post->post_content;

$has_parent = get_post_ancestors( $post );

if ( $children && empty( $content ) ) {
    $firstchild = $children[0];
    wp_redirect( get_permalink( $firstchild->ID ) );
    exit;
}
get_header();
?>

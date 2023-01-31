<?php 
$series_posts = rwmb_meta('phila_post_picker');
$label = 'post';
$label_arr = phila_get_post_label($label);

foreach( $series_posts as $collection_post_id ) {
  global $post;
  $post = get_post( $collection_post_id, OBJECT );
  setup_postdata( $post );
  include( locate_template( 'partials/posts/series/series-content-card.php' ) );
}
?>
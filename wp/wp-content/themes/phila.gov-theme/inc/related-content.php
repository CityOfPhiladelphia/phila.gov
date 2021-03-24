<?php
/**
 * Add custom breadcrumb support
 *
 */
function get_related_content( $post_id ) {
  $post_type = get_post_type( $post_id );
  $related_post_type = array( $post_type );
  $category = get_the_category( $post_id );
  $cat_ids = array();
  $posts = [];
  foreach( $category as $cat ){
    array_push( $cat_ids, $cat->cat_ID );
  }
  $cat_id_string = implode( ', ', $cat_ids );
  $template_type = phila_get_selected_template( $post_id );

  $related_content_args = array(
    'post_type' => $related_post_type,
    'category__and' => array($cat_id_string),
    'posts_per_page'  => 4,
    'post__not_in'  => array($post_id),
    // 'meta_query' => array(
    //   array(
    //     'key'     => 'phila_template_select',
    //     'value'   => $template_type,
    //     'compare' => '=',
    //   ),
    // ),
  );

  $related_posts = new WP_Query( $related_content_args );

  if ( $related_posts->have_posts() ) {
    while ( $related_posts->have_posts() ) : $related_posts->the_post();
      $translated_posts = rwmb_meta( 'phila_v2_translated_content' );
      if( $translated_posts != null ) {
        foreach ($translated_posts as $key => $value) {
          $post['title'] = $value['phila_custom_wysiwyg']['phila_wysiwyg_title'];
          $post['link'] = get_permalink().'?language='.$value['translated_language'];
          $post['template'] = phila_get_selected_template( get_the_ID() );
          $post['featured_image'] = wp_get_attachment_url( get_post_thumbnail_id(), 'thumbnail' );
          $post['published_date'] = rwmb_meta( 'phila_press_release_date') ? rwmb_meta( 'phila_press_release_date') : get_the_date();
          $post['language'] = $value['translated_language'];

          array_push( $posts, $post );
          $post = null;
        }
      } else {
        $post = null;
        $post['title'] = get_the_title();
        $post['link'] = get_permalink();
        $post['template'] = phila_get_selected_template( get_the_ID() );
        $post['featured_image'] = wp_get_attachment_url( get_post_thumbnail_id(), 'thumbnail' );
        $post['published_date'] =  rwmb_meta( 'phila_press_release_date') ? rwmb_meta( 'phila_press_release_date') : get_the_date();
        $post['language'] = rwmb_meta( 'phila_select_language');
        array_push( $posts, $post );
      }
    endwhile;
  }
  return $posts;

}
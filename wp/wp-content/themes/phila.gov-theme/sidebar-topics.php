<?php

/*
 *
 *  Topics List Sidebar
 *
 */

 ?>
<div id="secondary" class="widget-area" role="complementary">
  <div class="filter">
    <h3><?php printf( __( 'Filter by Topic', 'phila-gov' )); ?> </h3>
      <?php
            $args = array(
          	'sort_order' => 'asc',
          	'sort_column' => 'post_title',
          	'post_type' => 'news_post'
          );
          $pages = get_posts($args);

          $news_topics = array();

          foreach ($pages as $page) {
            $news_topics[] = $page->ID;
          }

        $topic_terms = wp_get_object_terms( $news_topics,  'topics' );
           if ( ! empty( $topic_terms ) ) {
           	if ( ! is_wp_error( $topic_terms ) ) {
           		echo '<ul>';
           			foreach( $topic_terms as $term ) {
                   if ( $term->parent == 0 )  {
           				    echo '<li><a href="/news/topics/' . $term->slug . '" class="item">' . esc_html( $term->name ) . '</a></li>';
                  }
           			}
           		echo '</ul>';
           	}
          }
      ?>
  </div><!-- .related -->
</div><!-- #secondary -->

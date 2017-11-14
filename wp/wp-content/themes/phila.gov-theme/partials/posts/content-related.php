<?php
/* Related content
*  at the bottom of posts, etc.
* $related_content_args should be passed by the file this is included in.
*/
?>

<?php $related_posts = new WP_Query( $related_content_args ); ?>
<?php $count = -1;?>
<?php $label_arr = phila_get_post_label(phila_get_selected_template($post->ID)); ?>

<?php if ( $related_posts->have_posts() ) : ?>
    <h2><?php _e( 'Related content' ); ?></h2>
      <?php while ( $related_posts->have_posts() ) : $related_posts->the_post(); ?>
        <?php $count++; ?>
        <?php if ( isset( $is_press_release ) ):  ?>
          <div class="cell medium-12">
            <?php include( locate_template( 'partials/posts/content-card.php' ) ); ?>
            </div>
          <?php else :  ?>
            <?php include( locate_template( 'partials/posts/content-list-image.php') ); ?>
         <?php endif; ?>
      <?php endwhile; ?>
  <?php wp_reset_postdata(); ?>
<?php endif; ?>

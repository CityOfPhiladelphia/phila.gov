<?php
/* Related content
*  at the bottom of posts, etc.
* $related_content_args should be passed by the file this is included in.
*/
?>

<?php $related_posts = new WP_Query( $related_content_args ); ?>
<?php $count = -1;?>
<?php if ( $related_posts->have_posts() ) : ?>
  <div class="grid-container">
    <h2><?php _e( 'Related' ); ?>
    <?php echo strtolower($post_obj->labels->name); ?></h2>
  </div>
  <div class="grid-container">
    <div class="grid-x grid-margin-x">
      <?php while ( $related_posts->have_posts() ) : $related_posts->the_post(); ?>
        <?php $count++; ?>
        <?php if ( isset( $is_press_release ) ):  ?>
          <div class="cell medium-12">
            <?php include( locate_template( 'partials/content-card.php' ) ); ?>
            </div>
          <?php else :  ?>
          <div class="grid-container">
            <?php get_template_part( 'partials/content', 'list-featured-image' ); ?>
          </div>

         <?php endif; ?>
      <?php endwhile; ?>
  </div>

</div>
  <?php wp_reset_postdata(); ?>
<?php endif; ?>

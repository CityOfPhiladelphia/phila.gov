<?php
/*
 * Posts & announcements mash up
 *
*/
?>

<?php $posts_announcements  = array(
  'posts_per_page' => 1,
  'post_type' => array( 'announcements' ),
  'order' => 'asc',
  'orderby' => 'post_date',

); ?>

<?php $label = 'announcement'; ?>
<?php $count = 0; ?>

<div class="grid-container">
  <div class="grid-x grid-margin-x">
    <?php $posts = new WP_Query( $posts_announcements ); ?>

    <?php if ( $posts->have_posts() ) : ?>
      <div class="cell medium-8">
        <?php while ( $posts->have_posts() ) : $posts->the_post(); ?>
          <?php $post_type = get_post_type(); ?>
          <?php $post_obj = get_post_type_object( $post_type ); ?>
          <?php $count++; ?>

          <?php include( locate_template( 'partials/posts/content-card.php' ) ); ?>

        <?php endwhile; ?>
      </div>
    <?php endif; ?>
    <?php wp_reset_postdata(); ?>

  </div>
</div>

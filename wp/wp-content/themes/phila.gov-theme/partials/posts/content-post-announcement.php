<?php
/*
 * Posts & announcements mash up
 *
*/
?>

<?php $posts_announcements  = array(
  'posts_per_page' => 1,
  'post_type' => array('post', 'announcements'),
  'order' => 'asc',
  'orderby' => 'post_date',

); ?>

<?php $label = 'featured'; ?>

<header class="row columns mtl">
  <h2>The latest from departments</h2>
</header>
<div class="grid-container">
  <div class="grid-x grid-margin-x">
    <?php $posts = new WP_Query( $posts_announcements ); ?>

    <?php if ( $posts->have_posts() ) : ?>
      whaever
        <div class="cell medium-16">
          <?php while ( $posts->have_posts() ) : $posts->the_post(); ?>
            <?php $post_type = get_post_type(); ?>

            <?php $post_obj = get_post_type_object( $post_type ); ?>

            <?php include( locate_template( 'partials/posts/content-list-featured-image.php' ) ); ?>

          <?php endwhile; ?>
        </div>
    <?php endif; ?>
    <?php wp_reset_postdata(); ?>

  </div>
</div>

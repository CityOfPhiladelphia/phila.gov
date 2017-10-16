<?php
/*
* Announcements rendering
*/
?>

<?php $announcements  = array(
  'posts_per_page' => 4,
  'post_type' => array( 'announcement' ),
  'order' => 'desc',
  'orderby' => 'post_date',
); ?>

<?php $label = 'announcement'; ?>
<?php $announcements = new WP_Query( $announcements )?>
<?php $count = $announcements->post_count ?>
<div class="grid-container">
  <div class="grid-x grid-margin-x">
    <?php if ( $announcements->have_posts() ) : ?>
      <?php while ( $announcements->have_posts() ) : $announcements->the_post(); ?>
        <?php $post_type = get_post_type(); ?>
        <?php $post_obj = get_post_type_object( $post_type ); ?>
            <div class="cell medium-<?php echo phila_grid_column_counter( $count ) ?> align-self-stretch">
              <?php include( locate_template( 'partials/posts/content-card.php' ) ); ?>
          </div>
        <?php endwhile; ?>
      <?php endif; ?>
    <?php wp_reset_postdata(); ?>

  </div>
</div>

<?php
/*
 * Posts
 *
*/
?>

<?php $posts_args = array(
  'posts_per_page' => 3,
  'post_type' => array( 'post' ),
  'order' => 'desc',
  'orderby' => 'post_date',
  'meta_query'  => array(
    'relation'  => 'AND',
    array(
      'key' => 'phila_template_select',
      'value' => 'post',
      'compare' => '=',
    ),
    array(
      'key' => 'phila_is_feature',
      'value' => '0',
      'compare' => '=',
    ),
  ),
); ?>

<?php $count = 0; ?>
<?php $posts = new WP_Query($posts_args) ?>

<div class="grid-container">
  <div class="grid-x grid-margin-x">
    <?php if ( $posts->have_posts() ) : ?>
        <?php while ( $posts->have_posts() ) : $posts->the_post(); ?>
          <?php $post_type = get_post_type(); ?>
          <?php $post_obj = get_post_type_object( $post_type ); ?>
          <?php $count++; ?>
              <?php $label = 'post'; ?>
              <div class="cell medium-16">
              <?php include( locate_template( 'partials/posts/content-card-image.php' ) ); ?>
            </div>

        <?php endwhile; ?>
      </div>
    <?php endif; ?>
    <?php wp_reset_postdata(); ?>

  </div>
</div>

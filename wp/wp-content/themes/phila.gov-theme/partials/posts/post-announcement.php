<?php
/*
 * Posts & announcements mash up
 *
*/
?>

<?php $announcements_args  = array(
  'posts_per_page' => 2,
  'post_type' => array( 'announcements' ),
  'order' => 'asc',
  'orderby' => 'post_date',

); ?>

<?php $posts_args  = array(
  'posts_per_page' => 2,
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

<?php $phila_posts_args  = array(
  'posts_per_page' => 1,
  'post_type' => array( 'phila_post' ),
  'order' => 'desc',
  'orderby' => 'post_date',
); ?>

<?php
//special handling for old press release CPT
  $announcements = new WP_Query( $announcements_args );
  $posts = new WP_Query( $posts_args );
  $phila_posts = new WP_Query( $phila_posts_args );
  $result = new WP_Query();
  $result->posts = array_merge( $announcements->posts, $posts->posts, $phila_posts->posts );
  $result->post_count = count( $result->posts );
?>

<?php $count = 0; ?>

<div class="grid-container">
  <div class="grid-x grid-margin-x">
    <?php if ( $result->have_posts() ) : ?>
        <?php while ( $result->have_posts() ) : $result->the_post(); ?>
          <?php $post_type = get_post_type(); ?>
          <?php $post_obj = get_post_type_object( $post_type ); ?>
          <?php $count++; ?>
          <?php if ($count <= 2 ): ?>
            <?php if ($post_type == 'announcements') : ?>
              <div class="cell medium-8">
                <?php $label = 'announcement'; ?>
                <?php include( locate_template( 'partials/posts/content-card.php' ) ); ?>
            </div>
            <?php else: ?>
              <?php $label = 'post'; ?>
              <div class="cell medium-16">
              <?php include( locate_template( 'partials/posts/content-card-image.php' ) ); ?>
            </div>
            <?php endif; ?>
          <?php endif; ?>

        <?php endwhile; ?>
      </div>
    <?php endif; ?>
    <?php wp_reset_postdata(); ?>

  </div>
</div>

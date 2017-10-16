<?php
/*
 * Display a grid of 3 posts
 *
*/
?>

<?php $posts_args  = array(
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

<?php $phila_posts_args  = array(
  'posts_per_page' => 3,
  'post_type' => array( 'phila_post' ),
  'order' => 'desc',
  'orderby' => 'post_date',
); ?>

<?php
  $posts = new WP_Query( $posts_args );
  $phila_posts = new WP_Query( $phila_posts_args );
  $result = new WP_Query();
  $result->posts = array_merge( $posts->posts, $phila_posts->posts );
  $result->post_count = count( $result->posts );
?>

<?php $count = 0; ?>

<div class="grid-container mbm">
  <div class="grid-x grid-margin-x align-stretch">
    <?php if ( $result->have_posts() ) : ?>
      <?php while ( $result->have_posts() ) : $result->the_post(); ?>
        <?php $post_type = get_post_type(); ?>
        <?php $post_obj = get_post_type_object( $post_type ); ?>
        <?php $count++; ?>
        <?php if ($count <= 3 ): ?>
          <?php $label = 'post'; ?>
          <?php if ($count == 1 ): ?>
            <div class="cell medium-16 align-self-stretch">
              <?php include( locate_template( 'partials/posts/content-card-image.php' ) ); ?>
          <?php elseif($count ==2):?>
            <div class="cell medium-8 align-self-stretch">
              <?php include( locate_template( 'partials/posts/content-card-image.php' ) ); ?>
          <?php else : ?>
          </div>
        </div>
          <div class="grid-container">
            <div class="grid-x grid-margin-x">
              <div class="cell medium-24">
                <?php include( locate_template( 'partials/content-list-featured-image.php' ) ); ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php endwhile; ?>
      </div>
    <?php endif; ?>
    <?php wp_reset_postdata(); ?>

  </div>
</div>

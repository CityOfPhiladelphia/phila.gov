<?php
/*
 * Featured content block
 *
 *
*/
?>

<?php $main_feature_args  = array(
  'posts_per_page' => 1,
  'post_type' => array('post'),
  'order' => 'asc',
  'orderby' => 'post_date',
  'meta_key' => 'phila_is_feature',
  'meta_value' => '1',
  'ignore_sticky_posts' => 1
); ?>

<?php $feature_args  = array(
  'posts_per_page' => 3,
  'offset'  => 1,
  'post_type' => array('post'),
  'order' => 'asc',
  'orderby' => 'post_date',
  'meta_key' => 'phila_is_feature',
  'meta_value' => '1'
); ?>

<?php $label = 'featured'; ?>

<header class="row columns mtl">
  <h2 id="featured">Featured</h2>
</header>
<div class="grid-container">
  <div class="grid-x grid-margin-x">
    <?php $main_feature = new WP_Query( $main_feature_args ); ?>

    <?php if ( $main_feature->have_posts() ) : ?>
        <div class="cell medium-16">
          <?php while ( $main_feature->have_posts() ) : $main_feature->the_post(); ?>
            <?php $post_type = get_post_type(); ?>

            <?php $post_obj = get_post_type_object( $post_type ); ?>

            <?php include( locate_template( 'partials/posts/content-card-image.php' ) ); ?>

          <?php endwhile; ?>
        </div>
    <?php endif; ?>
    <?php wp_reset_postdata(); ?>


    <?php $feature = new WP_Query( $feature_args ); ?>

    <?php if ( $feature->have_posts() ) : ?>
        <div class="cell medium-8">
          <?php while ( $feature->have_posts() ) : $feature->the_post(); ?>
            <?php $post_type = get_post_type(); ?>

            <?php $post_obj = get_post_type_object( $post_type ); ?>

            <?php include( locate_template( 'partials/posts/content-card.php' ) ); ?>
          <?php endwhile; ?>
        </div>
    <?php endif; ?>
    <?php wp_reset_postdata(); ?>
  </div>
</div>

<?php
/*
 * Featured content block
 *
 * TODO: Remove fallback for old news_post content type
*/
?>

<?php $main_feature_args  = array(
  'posts_per_page' => 1,
  'post_type' => array('post', 'news_post'),
  'order' => 'desc',
  'orderby' => 'date',
  'meta_query'  => array(
    'relation'  => 'OR',
    array(
      'key' => 'phila_show_on_home',
      'value' => '1',
      'compare' => '=',
    ),
    array(
      'relation'  => 'AND',
      array(
        'key' => 'phila_is_feature',
        'value' => '1',
        'compare' => '=',
      ),
      array(
        'key' => '_thumbnail_id',
        'compare' => 'EXISTS'
      ),
    ),
  ),
  'ignore_sticky_posts' => 1, // We have to ignore sticky, otherwise we might show more than one post
); ?>

<?php $label = 'featured'; ?>
<?php $main_feature_id = ''; ?>
<div class="featured-grid">

  <div class="grid-container">
    <div class="grid-x grid-margin-x">
      <?php $main_feature = new WP_Query( $main_feature_args ); ?>

      <?php if ( $main_feature->have_posts() ) : ?>
        <div class="cell medium-16 feature-main align-self-stretch">
          <?php while ( $main_feature->have_posts() ) : $main_feature->the_post(); ?>
            <?php $post_type = get_post_type(); ?>

            <?php $post_obj = get_post_type_object( $post_type ); ?>
            <?php $main_feature_id = get_the_ID();?>

            <?php include( locate_template( 'partials/posts/content-card-image.php' ) ); ?>

          <?php endwhile; ?>
        </div>
      <?php endif; ?>

      <?php wp_reset_postdata(); ?>

      <?php $feature_args  = array(
        'posts_per_page' => 3,
        'post_type' => array('post', 'news_post'),
        'order' => 'desc',
        'orderby' => 'date',
        'post__not_in' => array( $main_feature_id ),
        'ignore_sticky_posts' => 1,
        'meta_query'  => array(
          'relation'  => 'OR',
          array(
            'key' => 'phila_show_on_home',
            'value' => '1',
            'compare' => '=',
          ),
          array(
            'key' => 'phila_is_feature',
            'value' => '1',
            'compare' => '=',
          ),
        ),
      ); ?>
      <?php $count = 0; ?>
      <?php $feature = new WP_Query( $feature_args ); ?>

      <?php if ( $feature->have_posts() ) : ?>
        <div class="cell medium-8 feature-more flex-container flex-dir-column">
          <?php while ( $feature->have_posts() ) : $feature->the_post(); ?>
            <?php $post_type = get_post_type(); ?>

            <?php $post_obj = get_post_type_object( $post_type ); ?>
            <?php $count++;?>
            <?php include( locate_template( 'partials/posts/content-card.php' ) ); ?>

          <?php endwhile; ?>
            <?php $see_all = array(
              'URL' => '/the-latest/archives/?template=featured',
              'content_type' => $label,
              'nice_name' => $label,
            ); ?>
        <?php include( locate_template( 'partials/content-see-all.php' ) ); ?>

        </div>
      <?php endif; ?>
      <?php wp_reset_postdata(); ?>
    </div>
  </div>
</div>

<?php
/*
 * Display a grid of 3 posts
 *
*/
?>
<?php $post_categories = isset($category) ? $category : ''; ?>

<?php

if ( ( $post_categories == '' ) ) {
  $post_meta_query = array(
    'key' => 'phila_is_feature',
    'value' => '0',
    'compare' => '=',
  );
}else{
  $post_meta_query = array();
}

$posts_args  = array(
  'posts_per_page' => 3,
  'post_type' => array( 'post' ),
  'order' => 'desc',
  'orderby' => 'post_date',
  'cat' => $post_categories,
  'meta_query'  => array(
    'relation'  => 'AND',
    array(
      'key' => 'phila_template_select',
      'value' => 'post',
      'compare' => '=',
    ),
    $post_meta_query
  )
);

$phila_posts_args  = array(
  'posts_per_page' => 3,
  'post_type' => array( 'phila_post' ),
  'order' => 'desc',
  'orderby' => 'post_date',
  'cat' => $post_categories,
); ?>

<?php
  $posts = new WP_Query( $posts_args );
  $phila_posts = new WP_Query( $phila_posts_args );
  $result = new WP_Query();
  $result->posts = array_merge( $posts->posts, $phila_posts->posts );
  $result->post_count = count( $result->posts );
?>

<?php $count = 0; ?>
<?php $label = 'post'; ?>
<div class="post-grid">
  <div class="grid-container mbm">
    <div class="grid-x grid-margin-x align-stretch">
      <?php if ( $result->have_posts() ) : ?>
        <?php while ( $result->have_posts() ) : $result->the_post(); ?>
          <?php $post_type = get_post_type(); ?>
          <?php $post_obj = get_post_type_object( $post_type ); ?>
          <?php $count++; ?>
          <?php if ($count <= 3 ): ?>
            <?php if ($count == 1 ): ?>
              <div class="cell medium-16 align-self-stretch post-<?php echo $count ?>">
              <?php include( locate_template( 'partials/posts/content-card-image.php' ) ); ?>
            <?php elseif( $count == 2 ):?>
              <div class="cell medium-8 align-self-stretch post-<?php echo $count ?>">
              <?php include( locate_template( 'partials/posts/content-card-image.php' ) ); ?>
            <?php else : ?>
              </div>
            </div>
            <div class="grid-container">
              <div class="grid-x grid-margin-x">
                <div class="cell medium-24 post-<?php echo $count ?>">
                  <?php include( locate_template( 'partials/posts/content-list-image.php' ) ); ?>
                </div>
              </div>
            <?php endif; ?>
          </div>
          <?php endif; ?>

        <?php endwhile; ?>
        <div class="grid-container">
          <?php $see_all = array(
            'URL' => '/the-latest/archive?template=post',
            'content_type' => $label,
            'nice_name' => 'posts'
          );
          ?>
          <?php if( !empty( $post_categories ) ) :
            $see_all_URL = array(
              'URL' => '/the-latest/archive?template=post&category=' . $post_categories[0],
            );
            $see_all = array_replace($see_all, $see_all_URL );
            endif;?>
          <?php include( locate_template( 'partials/content-see-all.php' ) ); ?>
        </div>
      <?php endif; ?>
      <?php wp_reset_postdata(); ?>
</div>

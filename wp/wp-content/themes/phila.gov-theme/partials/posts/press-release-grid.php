<?php
/*
 * Press release grid
*/
?>
<?php $press_categories = isset( $category ) ? $category : '';?>
<?php $press_tag = isset( $tag ) ? $tag : '';?>

<?php $press_release_args  = array(
  'posts_per_page' => 4,
  'post_type' => array( 'press_release' ),
  'order' => 'desc',
  'orderby' => 'post_date',
  'cat' => $press_categories,
); ?>

<?php

//if categories aren't set then, this is the latest, don't show featured
if ( empty( $press_categories ) ) {
  $press_meta_query  = array(
    'key' => 'phila_is_feature',
    'value' => '0',
    'compare' => '=',
  );
}else{
  $press_meta_query = array();
}

if( !empty($tag) ) {
  $press_release_template_args  = array(
    'posts_per_page' => 4,
    'post_type' => array( 'post' ),
    'orderby' => 'post_date',
    'tag_id'  => (int) $tag,
    'ignore_sticky_posts' => 1,
    'meta_query'  => array(
      'relation'=> 'AND',
        array(
          'key' => 'phila_template_select',
          'value' => 'press_release',
          'compare' => '=',
      ),
    )
  );

}else{
  $press_release_template_args  = array(
    'posts_per_page' => 4,
    'post_type' => array( 'post' ),
    'order' => 'desc',
    'orderby' => 'post_date',
    'ignore_sticky_posts' => 1,
    'cat' => $press_categories,
    'meta_query'  => array(
      'relation'=> 'AND',
        array(
          'key' => 'phila_template_select',
          'value' => 'press_release',
          'compare' => '=',
        ),
      $press_meta_query
    ),
  );
}
?>


<?php
//special handling for old press release CPT
  $old_press = new WP_Query( $press_release_args );
  $new_press = new WP_Query( $press_release_template_args );
  $result = new WP_Query();
  $result->posts = array_merge( $new_press->posts, $old_press->posts );
  $result->post_count = count( $result->posts );
?>

<?php $label = 'press_release' ?>
<?php $count = 0; ?>

<div class="press-grid<?= ( is_page_template() ) ? "" : ' mbxl mtxl' ?>">
  <div class="grid-container">
  <?php if ( $result->have_posts() ) : ?>
    <?php if (!is_page_template('templates/the-latest.php')): ?>
      <h2>Press releases</h2>
    <?php endif; ?>
      <?php while ( $result->have_posts() ) : $result->the_post(); ?>
        <?php $post_type = get_post_type(); ?>
        <?php $post_obj = get_post_type_object( $post_type ); ?>
        <?php $count++; ?>
        <?php if ($count == 1) :?>
          <div class="grid-x grid-margin-x grid-full-height">
        <?php elseif( $count == 3 ) : ?>
          <div class="grid-x grid-margin-x grid-full-height mtm">
        <?php endif;?>
          <?php
          if ($count <= 4) : ?>
            <div class="cell medium-12 align-self-stretch">
              <?php include( locate_template( 'partials/posts/content-card.php' ) ); ?>
              <?php if ($count == 4) : ?>
                <?php $see_all = array(
                  'URL' => '/the-latest/archives/?template=press_release',
                  'content_type' => 'press_release',
                  'nice_name' => 'Press releases',
                  'is_full' => true
                );
                ?>
                <?php include( locate_template( 'partials/content-see-all.php' ) ); ?>
              <?php endif;?>
              <?php if ($count == 2 || $count == 4) :?>
              </div>
            <?php endif;?>
          </div>
        <?php endif;?>

        <?php endwhile; ?>
      <?php endif; ?>
    <?php wp_reset_postdata(); ?>
  </div>
</div>

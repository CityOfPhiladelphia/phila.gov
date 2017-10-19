<?php
/* Press release grid
*/
?>
<?php $press_categories = isset( $category ) ? $category : '';?>

<?php $press_release_args  = array(
  'posts_per_page' => 4,
  'post_type' => array( 'press_release' ),
  'order' => 'desc',
  'orderby' => 'post_date',
  'cat' => $press_categories,
); ?>

<?php

//if this is a department homepage (i.e. categories are set, show all press releases, not just featured ones)
if ( ( $press_categories == '' ) ) {
  $press_meta_query  = array(
    'key' => 'phila_is_feature',
    'value' => '0',
    'compare' => '=',
  );
}else{
  $press_meta_query = array();
}
 $press_release_template_args  = array(
  'posts_per_page' => 4,
  'post_type' => array( 'post' ),
  'order' => 'desc',
  'orderby' => 'post_date',
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
?>


<?php
//special handling for old press release CPT
  $old_press = new WP_Query( $press_release_args );
  $new_press = new WP_Query( $press_release_template_args );
  $result = new WP_Query();
  $result->posts = array_merge( $new_press->posts, $old_press->posts );
  $result->post_count = count( $result->posts );
?>

<?php $label = 'press_release'; ?>
<?php $count = 0; ?>
<div class="press-grid">

  <div class="grid-container">
    <?php if ( $result->have_posts() ) : ?>
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
                <?php $see_all_content_type = $label; ?>
                <?php $is_full = true; ?>
                <?php $see_all_URL = 'archive?template=press_release'?>
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

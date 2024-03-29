<?php
/*
 * Press release grid
*/
?>
<?php $press_categories = isset( $category ) ? $category : '';?>
<?php if ($press_categories) : ?>
  <?php foreach ($press_categories as $category ) {
    $current_cat = get_category($category);
    $slang_name = html_entity_decode(trim(phila_get_owner_typography( $current_cat )));
  }
  ?>
  <?php else: 
    $current_cat = null;
    $slang_name = '';?>
<?php endif; ?>

<?php $press_tag = isset( $tag ) ? $tag : '';?>

<?php

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
    'cat' => (int) implode( $press_categories ),
    'meta_query'  => array(
      'relation'=> 'AND',
        array(
          'key' => 'phila_template_select',
          'value' => 'press_release',
          'compare' => '=',
        ),
    ),
  );
}
?>

<?php 
  $user_selected_template = phila_get_selected_template(); 
  $post_type_parent = get_post_type($post->ID);
?>

<?php
//special handling for old press release CPT
  $result = new WP_Query( $press_release_template_args );
  $result->post_count = count( $result->posts );
?>

<?php $label = 'press_release' ?>
<?php $count = 0; ?>

<div class="press-grid<?php echo ( is_page_template() ) ? "" : ' mbxl mtxl' ?>">
  <div class="grid-container">
  <?php if ( $result->have_posts() ) : ?>
    <?php include( locate_template( 'partials/posts/press-release-translated-langs-see-all.php' ) ); ?>
      <?php while ( $result->have_posts() ) : $result->the_post(); ?>
        <?php $post_type = get_post_type(); ?>
        <?php $post_obj = get_post_type_object( $post_type ); ?>
        <?php $count++; ?>
        <?php if( $user_selected_template == 'custom_content' || $post_type_parent == 'guides' ): ?>
          <?php if ($count <= 4) : ?>
            <div class="cell align-self-stretch">
              <?php include( locate_template( 'partials/posts/custom-content-icon.php' ) ); ?>
            </div>
          <?php endif; ?>
          <?php if( $count == 4 ) : ?>
            <div class="cell align-self-stretch">
              <?php include( locate_template( 'partials/posts/press-release-grid-view-all.php' ) ); ?>
            </div>
          <?php endif; ?>
        <?php else: ?>
          <?php if ($count == 1) :?>
            <div class="grid-x grid-margin-x grid-full-height">
          <?php elseif( $count == 3 ) : ?>
            <div class="grid-x grid-margin-x grid-full-height">
          <?php endif;?>
          <?php if ($count <= 4) : ?>
            <?php if ($user_selected_template == 'custom_content' || $post_type_parent == 'guides'): ?>
              <div class="cell align-self-stretch">
                <?php include( locate_template( 'partials/posts/custom-content-icon.php' ) ); ?>
            <?php else: ?>
              <div class="cell medium-12 align-self-stretch">
                <?php include( locate_template( 'partials/posts/content-card.php' ) ); ?>
            <?php endif; ?>
            <?php include( locate_template( 'partials/posts/press-release-grid-view-all.php' ) ); ?>
            <?php if ( $count == 2 || $count == 4) :?>
              </div>
            <?php endif;?>
            </div>
          <?php endif;?>
        <?php endif;?>
      <?php endwhile; ?>
  <?php endif; ?>
  <?php wp_reset_postdata(); ?>
  </div>
</div>

<?php
/*
 * Action guide grid
 *
*/
?>
<?php $action_args  = array(
  'posts_per_page' => 5,
  'post_type' => array('post'),
  'order' => 'desc',
  'orderby' => 'date',
  'ignore_sticky_posts' => 1,
  'meta_query'  => array(
    array(
      'key' => 'phila_template_select',
      'value' => 'action_guide',
      'compare' => '=',
    ),
  ),
); ?>
<?php $count = 0; ?>
<?php $label = 'action_guide'; ?>

<?php $action = new WP_Query( $action_args ); ?>
<div class="grid-container grid--action_guide">
  <div class="grid-x grid-margin-x">
    <?php if ( $action->have_posts() ) : ?>
      <div class="cell medium-8 feature-more flex-container flex-dir-column">
        <?php while ( $action->have_posts() ) : $action->the_post(); ?>
          <?php $post_type = get_post_type(); ?>

          <?php $post_obj = get_post_type_object( $post_type ); ?>
          <?php $count++;?>
          <?php include( locate_template( 'partials/posts/content-card.php' ) ); ?>

        <?php endwhile; ?>
        <?php if ($count == 5) : ?>
          <?php $see_all = array(
            'URL' => 'archive?template=action_guide',
            'content_type' => $label,
            'nice_name' => $label,
        ); ?>
          <?php include( locate_template( 'partials/content-see-all.php' ) ); ?>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php wp_reset_postdata(); ?>

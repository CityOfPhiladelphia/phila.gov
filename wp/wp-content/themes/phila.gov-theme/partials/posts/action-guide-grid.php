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
  <?php if (!is_page_template('templates/the-latest.php')): ?>
    <h2>More action guides</h2>
  <?php endif; ?>
  <div class="grid-x grid-margin-x">
    <?php if ( $action->have_posts() ) : ?>
        <?php while ( $action->have_posts() ) : $action->the_post(); ?>
          <div class="cell medium-8 flex-container flex-dir-column mbm hide-for-small-only">
            <?php $post_type = get_post_type(); ?>

            <?php $post_obj = get_post_type_object( $post_type ); ?>
            <?php $count++;?>
            <?php include( locate_template( 'partials/posts/content-card.php' ) ); ?>
          </div>

        <?php endwhile; ?>
        <?php if ( $count >= 5 ) : ?>
          <div class="cell medium-8 flex-container flex-dir-column mbm">
            <div class="see-all-card bg-punk-pink white pal flex-child-auto">
              <h1>City of Philadelphia Action Guides</h1>
              <div class="description">
                Get the facts on complex issues and then take action.
              </div>
            </div>
          <?php $see_all = array(
            'URL' => '/the-latest/archives/?template=action_guide',
            'content_type' => $label,
            'nice_name' => 'Action guides',
          );?>
          <?php include( locate_template( 'partials/content-see-all.php' ) ); ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
  </div>
</div>
<?php wp_reset_postdata(); ?>

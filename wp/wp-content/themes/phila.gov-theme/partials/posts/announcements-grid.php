<?php
/*
* Announcements rendering
*/
?>
<?php $ann_categories = isset( $category ) ? $category : '';?>
<?php $ann_tag = isset( $tag ) ? $tag : '';?>
<?php $current_time = current_time('U'); ?>
<?php isset($home_filter) ? $home_filter : $home_filter = array(); ?>

<?php if( !empty( $ann_tag ) && $ann_tag != 'is_front_page' && $ann_tag != 'is_single' ) {
  $announcement_args  = array(
    'posts_per_page' => 4,
    'post_type' => array( 'announcement' ),
    'order' => 'desc',
    'orderby' => 'post_date',
    'tag__in'  => $ann_tag,
    'meta_query'  => array(
      'relation'  => 'AND',
      array(
        'key' => 'phila_announce_end_date',
        'value'   => $current_time,
        'compare' => '>=',
      ),
      $home_filter
    ),
  );

  }else if( $ann_categories != '' ){
    $announcement_args = array(
    'posts_per_page' => 4,
    'post_type' => array( 'announcement' ),
    'order' => 'desc',
    'orderby' => 'post_date',
    'cat' => $ann_categories,
    'meta_query'  => array(
      'relation'  => 'AND',
      array(
        'key' => 'phila_announce_end_date',
        'value'   => $current_time,
        'compare' => '>=',
      ),
      $home_filter
    ),
  );
  } else {
    $announcement_args = array(
      'posts_per_page' => 4,
      'post_type' => array( 'announcement' ),
      'order' => 'desc',
      'orderby' => 'post_date',
      'meta_query'  => array(
        'relation'  => 'AND',
        array(
          'key' => 'phila_announce_end_date',
          'value'   => $current_time,
          'compare' => '>=',
        ),
        $home_filter
      ),
    );
};
?>
<?php $label = 'announcement'; ?>

<?php $user_selected_template = phila_get_selected_template(); ?>

<?php $announcements = new WP_Query( $announcement_args )?>
<?php $count = $announcements->post_count ?>
  <?php if ( $announcements->have_posts() ) : ?>
    <?php $total = $announcements->post_count; ?>
    <div class="grid-container announcement-grid mbxl">
    <?php if ( is_single() || is_home() ) { ?>
      <h2>Announcements</h2>
    <?php } ?>
    <div class="grid-x grid-margin-x">
    <?php while ( $announcements->have_posts() ) : $announcements->the_post(); ?>
        <?php $post_type = get_post_type(); ?>
        <?php $cats = get_the_category($post->ID); ?>
        <?php $post_obj = get_post_type_object( $post_type ); ?>
            <div class="cell medium-<?php echo phila_grid_column_counter( $count ) ?> align-self-stretch">
              <?php if ($user_selected_template == 'custom_content'): ?>
                <?php include( locate_template( 'partials/posts/custom-content-card.php' ) ); ?>
              <?php else: ?>
                <?php include( locate_template( 'partials/posts/content-card.php' ) ); ?>
              <?php endif; ?>
          </div>
          <div id="announcement-<?php the_ID(); ?>" class="reveal reveal--<?php echo $label_arr['label']?>" data-reveal data-deep-link="true" data-options="closeOnClick:false; closeOnEsc:false;">
            <button class="close-button" data-close aria-label="Close modal" type="button">
              <span aria-hidden="true">&times;</span>
            </button>
            <div class="post-label post-label--<?php echo $label_arr['label']?>">
              <i class="<?php echo $label_arr['icon'] ?> fa-lg" aria-hidden="true"></i> <span><?php echo $label_arr['nice']; ?></span>
            </div>
            <header class="mvm">
              <h1><?php echo get_the_title(); ?></h1>
            </header>
            <?php the_content(); ?>
            <div class="post-meta mbm reveal-footer">
              <span class="date-published">Announced on: <time datetime="<?php echo get_post_time('Y-m-d'); ?>"><?php echo get_the_date();?></time></span>
              <span class="departments"><?php echo phila_get_current_department_name( $cats, $byline = false, $break_tags = false ); ?></span>
            </div>
          </div>

        <?php endwhile; ?>
      </div>
    </div>
  <?php endif; ?>
<?php wp_reset_postdata(); ?>

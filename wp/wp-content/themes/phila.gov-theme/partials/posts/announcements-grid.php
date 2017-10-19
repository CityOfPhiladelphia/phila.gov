<?php
/*
* Announcements rendering
*/
?>
<?php $ann_categories = isset( $category ) ? $category : '';?>

<?php $announcements  = array(
  'posts_per_page' => 4,
  'post_type' => array( 'announcement' ),
  'order' => 'desc',
  'orderby' => 'post_date',
  'cat' => $ann_categories
); ?>

<?php $label = 'announcement'; ?>
<?php $announcements = new WP_Query( $announcements )?>
<?php $count = $announcements->post_count ?>
<div class="grid-container">
  <div class="grid-x grid-margin-x">
    <?php if ( $announcements->have_posts() ) : ?>
      <?php while ( $announcements->have_posts() ) : $announcements->the_post(); ?>
        <?php $post_type = get_post_type(); ?>
        <?php $post_obj = get_post_type_object( $post_type ); ?>
            <div class="cell medium-<?php echo phila_grid_column_counter( $count ) ?> align-self-stretch">
              <?php include( locate_template( 'partials/posts/content-card.php' ) ); ?>
          </div>
          <div id="announcement-<?php the_ID(); ?>" class="reveal reveal--<?php echo $label?>" data-reveal>
            <button class="close-button" data-close aria-label="Close modal" type="button">
              <span aria-hidden="true">&times;</span>
            </button>
            <div class="post-label post-label--<?php echo $label?>">
              <i class="fa fa-<?php echo $icon ?> fa-lg" aria-hidden="true"></i> <span><?php echo $label_nice; ?></span>
            </div>
            <header class="mvm">
              <h1><?php echo get_the_title(); ?></h1>
            </header>
            <?php echo get_the_content(); ?>
            <div class="post-meta mtxl">
              <span class="date-published">Announced on: <time datetime="<?php echo get_post_time('Y-m-d'); ?>"><?php echo get_the_date();?></time></span>
              <span class="departments"><?php echo phila_get_current_department_name( $categories, $byline = false, $break_tags = false ); ?></span>
            </div>
          </div>
        <?php endwhile; ?>
      <?php endif; ?>
    <?php wp_reset_postdata(); ?>

  </div>
</div>

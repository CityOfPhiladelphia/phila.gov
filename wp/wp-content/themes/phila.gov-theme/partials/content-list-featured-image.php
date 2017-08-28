<?php
/**
 * The template used for displaying a featured image, description and link
 *
 * @package phila-gov
 */
?>
<?php $categories = get_the_category($post->ID); ?>
<?php $desc = phila_get_item_meta_desc( ); ?>


<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <a href="<?php echo the_permalink(); ?>" class="card card--list grid-x">
    <?php if ( has_post_thumbnail() ) : ?>
      <div class="cell medium-7">
        <?php echo phila_get_thumbnails(); ?>
      </div>
    <?php endif; ?>
    <div class="content-block cell medium-17 grid-x">
      <div class="cell align-self-top">
        <header>
          <h1><?php echo get_the_title(); ?></h1>
        </header>
        <p><?php echo $desc ?></p>
      </div>
      <div class="cell align-self-bottom">
        <div class="post-meta">
          <span class="date-published"><?php echo get_the_date();?></span>
          <span class="departments"><?php echo phila_get_current_department_name( $categories, $byline = false, $break_tags = false, $name_list = true ); ?></span>
        </div>
      </div>
    </div>
  </a>
</article>

<?php
/**
 * The template used for displaying a featured image, description and link
 *
 * @package phila-gov
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('news-item row'); ?>>
  <?php if ( has_post_thumbnail() ) {
    $thumb_active = true;  ?>
    <div class="logo columns medium-7">
      <?php the_post_thumbnail('home-thumb'); ?>
    </div>
  <?php } ?>

  <div class="medium-17 columns">
  	<header class="entry-header small-text">
      <?php
        $categories = get_the_category($post->ID);
        ?>
      <span class="entry-date"><strong><?php echo get_the_date(); ?></strong> | </span>
      <span class="category">
        <?php echo phila_get_current_department_name( $categories, $break_tags = false ); ?>
      </span>
        <a href="<?php echo the_permalink(); ?>"><?php the_title('<h2 class="h4">', '</h2>' ); ?></a>
  	</header><!-- .entry-header -->
    <?php $desc = phila_get_item_meta_desc(); ?>
      <p class="description"><?php echo $desc ?> </p>
  </div>
</article><!-- #post-## -->
<hr>

<?php
/**
 * The content of a single post
 * @package phila-gov
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <div class="row">
    <header class="entry-header small-24 columns mbs">
      <?php the_title( '<h1 class="entry-title contrast">', '</h1>' ); ?>
      <?php $posted_on_values = phila_get_posted_on(); ?>
      <span class="small-text"><?php echo $posted_on_values['time_string'];?>
        <?php $category = get_the_category() ?>
        <?php echo phila_get_current_department_name( $category, true ); ?>
      </span>
    </header><!-- .entry-header -->
  </div>
  <div class="row">
    <div data-swiftype-index='true' class="entry-content medium-24 columns">
      <?php
      if ( has_post_thumbnail() ) : ?>
      <div class="phila-thumb float-left mrm mvm">
        <?php echo phila_get_thumbnails(); ?>
      </div>
    <?php endif; ?>

      <?php the_content(); ?>
    </div><!-- .entry-content -->
  </div><!-- .row -->
</article><!-- #post-## -->

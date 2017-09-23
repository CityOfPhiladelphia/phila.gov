<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package phila-gov
 */
?>

<article id="post-<?php the_ID(); ?>">
  <div class="row">
    <header class="small-24 columns">
      <?php the_title( '<h1 class="contrast">', '</h1>' ); ?>
    </header>
  </div>
  <div class="row">
    <div data-swiftype-index='true' class="entry-content columns">
        <?php get_template_part('partials/content', 'default');?>
    </div><!-- .entry-content -->
  </div>
</article><!-- #post-## -->

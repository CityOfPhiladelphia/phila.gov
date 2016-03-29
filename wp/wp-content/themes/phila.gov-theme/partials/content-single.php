<?php
/**
 * The content of a single post
 * @package phila-gov
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <div class="row">
    <header class="entry-header small-24 columns">
      <?php the_title( '<h1 class="entry-title contrast">', '</h1>' ); ?>
    </header><!-- .entry-header -->
  </div>
  <div class="row">
    <div data-swiftype-index='true' class="entry-content medium-18 columns">
      <?php the_content(); ?>
    </div><!-- .entry-content -->
  </div><!-- .row -->
</article><!-- #post-## -->

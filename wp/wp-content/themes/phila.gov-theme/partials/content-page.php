<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package phila-gov
 */
?>

<article id="post-<?php the_ID(); ?>">
  <div class="row">
    <header class="entry-header small-24 columns">
      <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
    </header><!-- .entry-header -->
  </div>
  <div class="row">
      <div data-swiftype-index='true' class="entry-content medium-18 columns">
          <?php the_content(); ?>
      </div><!-- .entry-content -->
  </div>
  <?php get_template_part( 'partials/content', 'modified' ) ?>
</article><!-- #post-## -->

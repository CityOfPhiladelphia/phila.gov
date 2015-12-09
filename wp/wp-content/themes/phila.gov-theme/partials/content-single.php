<?php
/**
 * The content of a single post
 * @package phila-gov
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <div class="row">
    <header class="entry-header small-24 columns">
      <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
      <div class="entry-meta small-text">
        <p><?php phila_gov_posted_on(); ?></p>
  		</div><!-- .entry-meta -->
    </header><!-- .entry-header -->
  </div>
  <div class="row">
    <div data-swiftype-index='true' class="entry-content medium-18 columns">
      <?php the_content(); ?>
      <footer class="entry-footer small-text">
        <p><?php phila_gov_entry_footer(); ?></p>
      </footer><!-- .entry-footer -->
    </div><!-- .entry-content -->
    <?php // get_sidebar(); ?>
  </div><!-- .row -->
  <?php get_template_part( 'partials/content', 'modified' ) ?>
</article><!-- #post-## -->

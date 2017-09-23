<?php
/**
 * Template Name: The latest
 * Description: Custom Page template for the
 * @package phila-gov
 */

  get_header();
?>

<div id="primary" class="content-area">
  <main id="main" class="site-main">
    <div class="row">
      <header class="small-24 columns">
        <?php the_title( '<h1 class="contrast">', '</h1>' ); ?>
      </header>
    </div>

  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>

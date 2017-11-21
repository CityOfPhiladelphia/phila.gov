<?php
/**
 * The template for displaying document archives.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package phila-gov
 */

get_header(); ?>

<section id="primary" class="content-area archive">
  <div class="row">
    <header class="columns">
      <h1 class="contrast"><?php get_the_archive_title(); ?></h1>
    </header>
  </div>
  <div class="row">
    <main class="site-main medium-20 columns medium-centered">
      <div id="pubs-forms"></div>
    </main>
  </div>
</section><!-- #primary -->
<?php get_footer(); ?>

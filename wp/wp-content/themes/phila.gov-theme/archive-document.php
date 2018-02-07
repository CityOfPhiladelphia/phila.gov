<?php
/**
 * The template for displaying archive pages.
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
      <main id="main" class="site-main small-20 columns medium-centered">
        <div id="publication-search">
          <div class="center"><i class="fa fa-spinner fa-spin fa-3x"></i></div>
        </div>
    </main><!-- #main -->
  </div>
</section><!-- #primary -->
<?php get_footer(); ?>

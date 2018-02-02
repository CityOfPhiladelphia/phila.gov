<?php
/**
 * Template Name: The latest archives
 * Description: Custom Page template for The latest archive page
 * @package phila-gov
 */

get_header(); ?>

<div class="row">
  <header class="columns">
    <h1 class="contrast">
      <?php echo get_the_title(); ?>
    </h1>
  </header>
</div>

<section id="archive-page" class="content-area archive">

  <div class="row">
    <main id="main" class="site-main medium-20 columns medium-centered">

      <div id="archive-results">
        <div class="center"><i class="fa fa-spinner fa-spin fa-3x"></i></div>
      </div>

      <?php phila_gov_paging_nav(); ?>

    </main><!-- #main -->
  </div>
</section><!-- #primary -->
<?php get_footer(); ?>

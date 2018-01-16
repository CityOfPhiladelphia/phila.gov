<?php
/**
 * Program and initiatives landing page
 *
 * @package phila-gov
 */

get_header(); ?>

<div id="primary" class="programs-landing content-area">
  <main id="main" class="site-main">
    <div class="grid-container">
      <div class="grid-x grid-x-margin">
        <header class="small-24 cell">
          <?php printf(__('<h1 class="contrast ptm">Programs & initiatives </h1>', 'phila-gov') ); ?>
        </header>
      </div>
    </div>
    <div class="grid-container">
      <div id="programs-initiatives-landing"></div>
    </div>
  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>

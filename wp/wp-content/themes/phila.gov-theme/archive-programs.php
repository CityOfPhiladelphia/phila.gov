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
      <p class="panel info info-block">Not all of the City's programs and initiatives are listed here yet. If you don't see what you're looking for, <a href="https://cse.google.com/cse?oe=utf8&ie=utf8&source=uds&start=0&cx=003474906032785030072:utbav7zeaky&hl=en&q=programs#gsc.tab=0&gsc.q=programs&gsc.page=1">search our classic site</a>. </p>
      <div id="programs-initiatives-landing">
        <div class="center"><i class="fa fa-spinner fa-spin fa-3x"></i></div>
      </div>
    </div>
  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>

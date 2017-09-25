<?php
/**
 * Template Name: The latest
 * Description: Custom Page template for the
 * @package phila-gov
 */

  get_header();
?>

<div id="primary" class="the-latest content-area">
  <main id="main" class="site-main">
    <header>
      <div class="grid-container">
        <?php the_title( '<h1 class="contrast">', '</h1>' );  ?>
      </div>
      <div data-sticky-container class="bg-white">
        <nav class="sticky sticky--in-page center bg-white" data-sticky data-top-anchor="global-sticky-nav:bottom" style="width:100%">
          <div class="grid-container">
            <ul class="inline-list grid-x grid-margin-x">
              <li class="featured auto cell">
                <a href="#featured">
                  <i class="fa fa-3x fa-newspaper-o" aria-hidden="true"></i>
                  <div>Featured</div>
                </a>
              </li>
              <li class="posts auto cell">
                <a href="#posts">
                  <i class="fa fa-3x fa-bullhorn" aria-hidden="true"></i>
                  <div>Posts</div>
                </a>
              </li>
              <li class="press-releases auto cell">
                <a href="#press-releases">
                  <i class="fa fa-3x fa-file-text-o" aria-hidden="true"></i>
                <div>Press releases</div>
              </a>
              </li>
              <li class="events auto cell">
                <a href="#events">
                  <i class="fa fa-3x fa-calendar-check-o" aria-hidden="true"></i>
                  <div>Events</div>
                </a>
              </li>
            </ul>
          </div>
        </nav>
      </div>
    </header>
  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>

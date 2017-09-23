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
    <div class="row">
      <header class="small-24 columns">
        <?php the_title( '<h1 class="contrast">', '</h1>' ); ?>
        <div class="data-sticky-container">
          <nav class="sticky sticky--in-page centered" data-sticky data-top-anchor="global-sticky-nav:bottom">
            <ul>
              <li class="featured">
                <a href="#featured">
                  <i class="fa fa-newspaper-o" aria-hidden="true"></i>
                  <span>Featured</span>
                </a>
              </li>
              <li class="posts">
                <a href="#posts">
                  <i class="fa fa-bullhorn" aria-hidden="true"></i>
                  <span>Posts</span>
              </a>
              </li>
              <li class="press-releases">
                <a href="#press-releases">
                  <i class="fa fa-file-text-o" aria-hidden="true"></i>
                <span>Press releases</span>
              </a>
              </li>
              <li class="events">
                <a href="#events">
                  <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                  <span>Events</span>
                </a>
              </li>
            </ul>
          </nav>
        </div>
      </header>
    </div>

  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>

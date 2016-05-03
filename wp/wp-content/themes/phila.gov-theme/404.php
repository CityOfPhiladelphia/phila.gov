<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package phila-gov
 */

get_header(); ?>

<div id="primary" class="content-area">
  <main id="main" class="site-main">
    <div class="row">
      <div class="small-24 columns">
        <section class="error-404 not-found">
          <header>
            <h1 class="contrast"><?php _e( 'Sorry, the page you requested was not found.', 'phila-gov' ); ?></h1>
          </header><!-- .page-header -->
          <div class="page-content">
            <p><?php _e( 'It looks like nothing was found at this location.', 'phila-gov' ); ?></p>
            <div class="find">
              <h2 class="h1 mtn">What can we help you find?</h2>
              <?php get_search_form(); ?>
            </div>
          </div>
        </section><!-- .error-404 -->
      </div>
    </div>
  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>

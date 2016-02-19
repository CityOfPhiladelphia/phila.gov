<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package phila-gov
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
      <div class="row">
        <div class="small-24 columns">
          <section class="error-404 not-found">
            <header>
                <h1 class="contrast ptm"><?php _e( 'Sorry, the page you requested was not found.', 'phila-gov' ); ?></h1>
            </header><!-- .page-header -->
            <div class="page-content">
                <p><?php _e( 'It looks like nothing was found at this location.', 'phila-gov' ); ?></p>
            	<div class="find">
                <h1>What can we help you find?</h1>
                <?php get_search_form(); ?>
            	</div>
            </div>
          </div>
          <div class="medium-8 small-24 columns"></div>
        </section><!-- .error-404 -->
        </div>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>

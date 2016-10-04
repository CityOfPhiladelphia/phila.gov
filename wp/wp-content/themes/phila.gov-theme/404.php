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
        <section>
          <header>
            <h1 class="contrast"><?php _e( 'Sorry, the page you requested was not found.', 'phila-gov' ); ?></h1>
          </header><!-- .page-header -->
        </div>
      </div>
      <div class="row">
        <div class="small-24 columns">
          <div class="page-content error-404 not-found">
            <p><?php _e( 'It looks like nothing was found at this location.', 'phila-gov' ); ?></p>
            <div class="find">
              <h2 class="h1 mtn">What can we help you find?</h2>
              <form role="search" method="get" class="search" id="search-form" action="<?php echo home_url( '/search' ); ?>">
                <label for="search-field"><span class="screen-reader-text"><?php echo _x( 'Search for:', 'label' ) ?></span></label>
                  <input type="text" class="search-field" placeholder="<?php echo esc_attr_x( 'Search', 'placeholder' ) ?>" value="<?php echo get_search_query() ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label' ) ?>" id="search-field"/>
                  <input type="submit" class="search-submit" value="<?php echo esc_attr_x( 'Search', 'submit button' ) ?>">
              </form>
            </div>
          </div>
        </section><!-- .error-404 -->
      </div>
    </div>
  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>

<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package phila-gov
 */

get_header(); ?>

<div id="primary" class="content-area">
  <main id="main" class="site-main">
    <div class="row center">
      <div class="small-24 columns">
        <section>
          <header>
            <h1>404 Not found</h1>
            <h2><?php _e( 'The content you\'re looking for isn\'t here.', 'phila-gov' ); ?></h2>
          </header><!-- .page-header -->
        </div>
      </div>
      <div class="row">
        <div class="medium-centered medium-12 small-24 columns">
          <p>Here are some options: <a href="/trashday">find your trash day</a>, <a href="https://secure.phila.gov/PaymentCenter/AccountLookup/">pay a bill</a>, <a href="http://www.phila.gov/personnel/JobOpps.html">explore City jobs</a> or <a href="/property">search for a property</a>.</p>
          <div class="page-content not-found">
            <div class="find mbl">
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

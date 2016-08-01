<?php
/**
 *
 * @package phila-gov
 */

get_header(); ?>

<section id="primary" class="content-area">
  <main id="main" class="site-main search-page">
    <div class="search-head">
      <div class="row">
        <header class="medium-8 columns">
          <h1><?php printf( __( 'Search results for:', 'phila-gov' )); ?></h1>
        </header><!-- .page-header -->
      </div>
      <div class="row">
        <div class="search-site medium-16 columns">
          <form role="search" method="get" class="search" action="<?php echo home_url( '/search' ); ?>">
            <label for="st-search-input"><span class="screen-reader-text"><?php echo _x( 'Search for:', 'label' ) ?></span></label>
            <input type="text" class="search-field" placeholder="<?php echo esc_attr_x( 'Search alpha.phila.gov', 'placeholder' ) ?>" value="<?php echo get_search_query() ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label' ) ?>" id="st-search-input"/>
            <input type="submit" class="search-submit" value="<?php echo esc_attr_x( 'Search', 'submit button' ) ?>">
          </form>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="medium-16 columns">
        <a class="property-link" id="property-link">
          <i class="fa fa-arrow-circle-right"></i>
          <p>We have found real estate property related to your search.</p>
        </a>
      </div>
    </div>
    <div class="row">
      <p class="medium-16 columns result-count" id="result-count"></p>
    </div>
    <div class="row">
      <div class="medium-16 columns search-results-list" id="st-results-container"></div>
    </div>
  </main><!-- #main -->
</section><!-- #primary -->

<?php get_footer(); ?>

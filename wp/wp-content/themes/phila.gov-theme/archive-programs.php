<?php
/**
 * Program and initiatives landing page
 *
 * @package phila-gov
 */

get_header(); ?>

<div id="primary" class="content-area">
  <main id="main" class="site-main">
    <div class="grid-container">
      <div class="grid-x grid-x-margin">
        <header class="small-24 cell">
          <?php printf(__('<h1 class="contrast ptm">Programs & initiatives </h1>', 'phila-gov') ); ?>
        </header>
      </div>
    </div>

    <div class="grid-container">
      <div class="grid-x grid-margin-x">
        <div class="small-24 medium-8 cell">
          <section>
            <div class="panel phm">
              <h3>Search within Programs</h3>
              <form role="search" method="get" class="search" action="<?php echo home_url( '/search' ); ?>">
                <label for="st-search-input"><span class="screen-reader-text"><?php echo _x( 'Search for:', 'label' ) ?></span></label>
                <input type="text" class="search-field" placeholder="<?php echo esc_attr_x( 'Search', 'placeholder' ) ?>" value="<?php echo get_search_query() ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label' ) ?>" id="st-search-input"/ autocomplete="off">
                <input type="submit" class="search-submit" value="<?php echo esc_attr_x( 'Search', 'submit button' ) ?>">
              </form>
            </div>
            <h4>Filter by audience</h4>
            <h4>Filter by category</h4>
          </section>
        </div>
        <div class="small-24 medium-16 cell program-results">
          <div class="grid-x grid-margin-x grid-padding-x">
            <div class="medium-12">
            </div>
            <div class="medium-12">
            </div>
           </div>
        </div>
      </div>
    </div>

  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>

<?php
/**
 *
 * @package phila-gov
 */

get_header(); ?>

<section id="primary" class="content-area">
  <main id="main" class="site-main search-page">
    <div class="grid-container">
      <div class="grid-x">
        <div class="cell small-24">
          <form role="search" method="get" class="search" action="<?php echo home_url( '/search' ); ?>">
            <label for="st-search-input"><span class="screen-reader-text"><?php echo _x( 'Search for:', 'label' ) ?></span></label>
            <input type="text" class="search-field" placeholder="<?php echo esc_attr_x( 'Search', 'placeholder' ) ?>" value="<?php echo get_search_query() ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label' ) ?>" id="st-search-input"/ autocomplete="off">
            <input type="submit" class="search-submit" value="<?php echo esc_attr_x( 'Search', 'submit button' ) ?>">
          </form>
        </div>
      </div>
    </div>
    <div class="grid-container">
      <div class="grid-x grid-margin-x">
        <div class="cell medium-8">
          <div class="accordion" data-accordion data-allow-all-closed="true"  data-multi-expand="true">
            <div class="accordion-item is-active" data-accordion-item>
              <a href="#" class="h4 accordion-title mbn">Filter by type</a>
              <div class="accordion-content" data-tab-content>
                <div id="content-types">
                  <fieldset>
                    <div>
                      <input type="radio" id="services" data-type="service_page" class="content-type" name="content_type">
                        <label for="services">Services</label>
                    </div>
                    <div>
                      <input type="radio" id="programs" data-type="programs" class="content-type" name="content_type">
                      <label for="programs">Programs & initiatives</label>
                    </div>
                    <div>
                      <input type="radio" id="news-events" data-type="post" class="content-type" name="content_type">
                      <label for="news-events">News & events</label>
                    </div>
                    <div>
                      <input type="radio" id="publications" data-type="documents" class="content-type" name="content_type">
                      <label for="publications">Publications & forms</label>
                    </div>
                    <div>
                      <input type="radio" id="departments" data-type="department_page" class="content-type" name="content_type">
                      <label for="departments">Departments</label>
                    </div>
                    <a href="#" class="mtm clear-all button" data-name="content_type">Clear all filters</a>
                  </fieldset>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="medium-16 cell">
          <div id="property-link" class="callout">
            We have found real estate property related to your search.
            <a href="#">View results using the property application <i class="fas fa-arrow-circle-right"></i></a>
          </div>
          <div id="legacy-content" class="callout">
            <span class="label mrm bg-dark-gray">Legacy</span>
            Content marked "legacy" has not been moved to our new platform.
          </div>
          <div id="result-count" class="pvm"></div>
          <div id="st-results-container">Enter search terms in the area above. <i class="fas fa-loading spin"></i></div>
        </div>
      </div>
    </div>
  </main><!-- #main -->
</section><!-- #primary -->

<?php get_footer(); ?>

<?php
/**
 * The search form
 *
 * @package phila-gov
 */
?>
<div class="search-pane global-nav">
  <div class="dropdown-pane site-search-dropdown" data-dropdown data-v-offset="0" data-hover-pane="true" data-trap-focus="true" data-close-on-click="true" data-auto-focus="true" id="search-dropdown">
    <div class="row columns expanded bg-ghost-gray">
      <div class="row arrow">
        <div class="medium-12 columns small-centered">
          <div class="paxl">
            <form role="search" method="get" class="search" id="search-form" action="<?php echo home_url( '/search' ); ?>">
              <label for="search-field"><span class="screen-reader-text"><?php echo _x( 'Search for:', 'label' ) ?></span></label>
                <input type="text" class="search-field swiftype" placeholder="<?php echo esc_attr_x( 'Search', 'placeholder' ) ?>" value="<?php echo get_search_query() ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label' ) ?>" id="search-field"/>
                <input type="submit" class="search-submit" value="<?php echo esc_attr_x( 'Search', 'submit button' ) ?>">
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

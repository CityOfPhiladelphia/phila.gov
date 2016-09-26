<?php
/**
 * The search form
 *
 * @package phila-gov
 */
?>
<div class="search-pane">
  <div class="dropdown-pane mega-menu-dropdown" data-dropdown data-v-offset="0" data-hover-pane="true" data-trap-focus="true" data-close-on-click="true" id="search-dropdown">
    <div class="row columns expanded bg-ghost-gray">
      <div class="row">
        <div class="medium-12 columns small-centered">
          <div class="paxl">
            <form role="search" method="get" class="search" action="<?php echo home_url( '/search' ); ?>">
              <label for="st-search-input"><span class="screen-reader-text"><?php echo _x( 'Search:', 'label' ) ?></span></label>
              <input type="text" class="search-field" placeholder="<?php echo esc_attr_x( 'Search', 'placeholder' ) ?>" value="<?php echo get_search_query() ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label' ) ?>" id="st-search-input"/>
              <input type="submit" class="search-submit" value="<?php echo esc_attr_x( 'Search', 'submit button' ) ?>">
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

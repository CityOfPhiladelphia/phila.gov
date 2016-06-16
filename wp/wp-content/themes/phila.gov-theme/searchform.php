<?php
/**
 * The search form
 *
 * @package phila-gov
 */
?>


<form role="search" method="get" class="search" id="search-form" action="<?php echo home_url( '/search' ); ?>">
  <label for="search-field"><span class="screen-reader-text"><?php echo _x( 'Search for:', 'label' ) ?></span></label>
    <input type="text" class="search-field" placeholder="<?php echo esc_attr_x( 'Search alpha.phila.gov', 'placeholder' ) ?>" value="<?php echo get_search_query() ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label' ) ?>" id="search-field"/>
    <i class="fa fa-search search-icon show-for-small-only" aria-hidden="true"></i>
    <input type="submit" class="search-submit" value="<?php echo esc_attr_x( 'Search', 'submit button' ) ?>">
</form>

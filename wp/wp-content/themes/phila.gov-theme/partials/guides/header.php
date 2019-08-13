<?php
  /*
   * Guides header
  */
?>
<div data-sticky-container data-check-every="0">
  <header id="guide-hero" data-sticky data-top-anchor="global-sticky-nav:bottom" data-margin-top="0">
    <div class="hero-full">
      <div class="grid-x">
        <div class="cell bg-ben-franklin-blue white hero-full--container">
          <div class="grid-x grid-container">
            <div class="hero-full--title align-self-bottom">
              <h1 class="guide-name">
              <?php 
                if($post->post_parent) {
                  $parent_title = get_the_title($post->post_parent);
                    echo $parent_title;
                  }
                else {
                  echo get_the_title($post->ID);
                } 
              ?>
              </h1>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
</div>
<div id="breadcrumbs" class="grid-container pan">
  <div class="grid-x">
    <div class="cell medium-18 mtm">
      <?php if ( phila_get_selected_template($post->ID) !== 'guide_landing_page' ): ?>
        <?php get_template_part( 'partials/breadcrumbs' ); ?>
      <?php endif; ?>
    </div>
    <div class="cell medium-6 text-right">
      <?php get_template_part('partials/posts/social-media') ?>
    </div>
  </div>
</div>

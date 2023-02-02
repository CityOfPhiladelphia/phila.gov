<article id="post-<?php the_ID(); ?>" <?php post_class('post img-floats series-archive'); ?>>
  <header class="post-header grid-container">
    <div class="cell ptl pbs"></div>
    <div class="grid-x grid-padding-x align-bottom">
      <div class="cell medium-24 post-title">
        <?php the_title( '<h1 style="display:inline">', '</h1>' ); ?>
      </div>
      <div class="border-bottom-fat"></div>
    </div>
  </header>

  <div class="grid-container post-content grid-x grid-padding-x mvm">
    <?php the_content(); ?>
  </div>
  <div class="grid-container post-content grid-x grid-padding-x mvl pan">
    <?php include(locate_template ('partials/posts/series/series-post-grid.php') ); ?>
  </div>

  <hr class="margin-auto"/>
</article>
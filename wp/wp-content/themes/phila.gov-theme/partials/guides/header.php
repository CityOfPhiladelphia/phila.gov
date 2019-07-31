<?php
  /*
   * Guides header
  */
?>
<header>
  <div class="hero-full">
    <div class="grid-x">
      <div class="cell bg-ben-franklin-blue white hero-full--container">
        <div class="grid-x grid-container align-right">
          <div class="hero-full--title mvl">
            <h1><?php echo the_title() ?></h1>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>
<?php if ( phila_get_selected_template($post->ID) != 'guide_landing_page' ): ?>
  <?php get_template_part( 'partials/breadcrumbs' ); ?>
<?php endif; ?>
<?php get_template_part('partials/posts/social-media') ?>

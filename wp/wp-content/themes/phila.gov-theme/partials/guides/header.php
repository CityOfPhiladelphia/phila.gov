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
            <h1><?php echo the_title(); ?></h1>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>
<div class="grid-container">
  <div class="grid-x grid-padding-x">
    <div class="cell medium-18">
      <?php if ( phila_get_selected_template($post->ID) !== 'guide_landing_page' ): ?>
        <?php get_template_part( 'partials/breadcrumbs' ); ?>
      <?php endif; ?>
    </div>
    <div class="cell medium-6 text-right">
      <?php get_template_part('partials/posts/social-media') ?>
    </div>
  </div>
</div>
<div class="grid-container">
  <div class="grid-x grid-padding-x">
    <div class="cell">
      <?php $guide_icon = rwmb_meta('guide_page_icon'); ?>
      <?php $landing_title = rwmb_meta('guide_landing_page_title'); var_dump($landing_title)?>
      <h2>
        <?php echo !empty( $guide_icon )  ? '<i class="' . $guide_icon . '"></i>' : '' ?>
        <?php if ( phila_get_selected_template($post->ID) === 'guide_landing_page' ): ?>
          <?php echo  !empty($landing_title) ? $landing_title : 'Overview' ?>
        <?php else: ?>
          <?php the_title(); ?>
        <?php endif;?>
      </h2>
    </div>
  </div>
</div>
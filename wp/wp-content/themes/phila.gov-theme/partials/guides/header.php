<?php
/*
   * Guides header
  */
?>
<div>
  <header id="guide-hero">
    <div class="hero-full">
      <div class="grid-x">
        <div class="cell bg-ben-franklin-blue white hero-full--container">
          <div class="grid-x grid-container">
            <div class="hero-full--title align-self-bottom">
              <h1 class="guide-name">
                <?php
                if ($post->post_parent) : ?>
                  <?php $parent_title = get_the_title($post->post_parent); ?>
                  <a href="<?php echo the_permalink($post->post_parent) ?>"><?php echo $parent_title; ?></a>
                <?php else : ?>
                  <a href="<?php echo the_permalink($post->ID) ?>"><?php echo get_the_title($post->ID); ?></a>
                <?php endif; ?>
              </h1>
            </div>
          </div>
        </div> 
      </div>
    </div>
  </header>
</div>
<?php if (phila_get_selected_template($post->ID) !== 'guide_landing_page') : ?>
  <div class="page-title-button hide-for-medium" id="page-title-button">
    <h1> <?php echo get_the_title($post) ?> </h1> <div class="caret-icon"> <i class="fas fa-chevron-down"></i>  </div>
  </div>
<?php endif; ?>
<div id="breadcrumbs" class="grid-container pan">
  <div class="grid-x">
    <div class="cell medium-18 mtm">
      <?php if (phila_get_selected_template($post->ID) !== 'guide_landing_page') : ?>
        <?php get_template_part('partials/breadcrumbs'); ?>
      <?php endif; ?>
    </div>
    <div class="cell medium-6 text-right">
      <?php get_template_part('partials/posts/social-media') ?>
    </div>
  </div>
</div>
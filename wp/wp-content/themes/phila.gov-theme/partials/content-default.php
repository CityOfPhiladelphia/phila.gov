<?php
/*
 *
 * Default Page or Service Template
 *
 */
 ?>
  <?php get_template_part( 'partials/content', 'custom-markup-before-wysiwyg' ); ?>
<div class="row">
  <div class="columns">
    <?php the_content(); ?>
  </div>
</div>
  <?php get_template_part( 'partials/content', 'custom-markup-after-wysiwyg' ); ?>
  <?php $heading_groups = rwmb_meta( 'phila_heading_groups' ); ?>
  <?php include(locate_template('partials/content-heading-groups.php')); ?>
  <?php get_template_part( 'partials/content', 'additional' ); ?>
  <?php include(locate_template('partials/content-prereq-row.php')); ?>

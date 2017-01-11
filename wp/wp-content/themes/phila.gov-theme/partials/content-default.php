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
  <?php get_template_part( 'partials/content', 'heading-groups' ); ?>
  <?php get_template_part( 'partials/content', 'additional' ); ?>

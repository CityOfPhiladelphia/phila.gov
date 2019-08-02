<?php 
/*
* Subpage partial
*/
?>
<section>
  <div class="grid-container">
    <div class="grid-x grid-padding-x">
      <div class="cell page-title">
        <?php $guide_icon = rwmb_meta('guide_page_icon'); ?>
        <?php $landing_title = rwmb_meta('guide_landing_page_title');?>
        <h1>
          <?php echo !empty( $guide_icon )  ? '<i class="' . $guide_icon . '"></i>' : '' ?>
          <?php if ( phila_get_selected_template($post->ID) === 'guide_landing_page' ): ?>
            <?php echo  !empty($landing_title) ? $landing_title : 'Overview' ?>
          <?php else: ?>
            <?php the_title(); ?>
          <?php endif;?>
        </h1>
      </div>
    </div>
  </div>

  <?php get_template_part( 'partials/content', 'custom-markup-before-wysiwyg' ); ?>
      
    <?php if( !empty( get_the_content() ) ) : ?>
      <div class="grid-container">
        <div class="grid-x grid-x-padding">
          <div class="cell">
            <div class="intro-text"><?php the_content(); ?></div>
          </div>
        </div>
      </div>
    <?php endif; ?>

  <?php get_template_part( 'partials/content', 'custom-markup-after-wysiwyg' ); ?>

</section>
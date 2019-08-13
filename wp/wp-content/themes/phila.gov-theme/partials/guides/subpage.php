<?php 
/*
* Subpage partial 
* phila_template_select = guide_sub_page
* Required vars - $heading_groups - 
*/
?>
<?php 
?>
<div class="grid-container mbxxl">
  <div class="grid-x grid-padding-x">
    <div class="cell medium-6 mtl" data-sticky-container>
      <?php include(locate_template('partials/guides/side-nav.php')); ?>
    </div>
    <div class="cell medium-1"></div>
    <div class="cell medium-17 guide-content">
      <section>
        <div class="page-title">
          <?php $guide_icon = rwmb_meta('guide_page_icon'); ?>
          <?php $landing_title = rwmb_meta('guide_landing_page_title');?>
          <h1 id="<?php echo sanitize_title_with_dashes(get_the_title())?>" data-magellan-target="<?php echo sanitize_title_with_dashes(get_the_title())?>">
            <span><?php echo !empty( $guide_icon )  ? '<i class="' . $guide_icon . '"></i>' : '' ?></span>
            <span><?php the_title(); ?></span>
          </h1>
        </div>

        <?php get_template_part( 'partials/content', 'custom-markup-before-wysiwyg' ); ?>
            
          <?php if( !empty( get_the_content() ) ) : ?>
            <div class="intro-text"><?php the_content(); ?></div>
          <?php endif; ?>

        <?php get_template_part( 'partials/content', 'custom-markup-after-wysiwyg' ); ?>

      <?php 
        $heading_groups = rwmb_meta( 'phila_heading_groups' );
        $heading_content = phila_extract_clonable_wysiwyg( $heading_groups ); ?>

      <?php include(locate_template('partials/content-heading-groups.php')); ?>

      </section>
    </div>
  </div>
</div>
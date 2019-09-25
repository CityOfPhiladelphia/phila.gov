<?php 
/*
* Subpage partial 
* phila_template_select = guide_sub_page
* Required vars - $heading_groups - 
*/
?>
<?php 
$current_template = phila_get_selected_template();
?>
<div class="grid-container mbxxl mobile-no-bottom-margin">
  <div class="grid-x grid-padding-x">
    <div class="cell medium-6 mtl nav-container" data-sticky-container>
      <?php include(locate_template('partials/guides/side-nav.php')); ?>
    </div>
    <div class="cell medium-1"></div>
    <div class="cell medium-17 guide-content">
      <article>
        <section>
          <div class="page-title-group">
            <?php $landing_title = rwmb_meta('guide_landing_page_title');?>
            <h1 id="<?php echo sanitize_title_with_dashes(get_the_title())?>" data-magellan-target="<?php echo sanitize_title_with_dashes(get_the_title())?>" class="<?php echo ($current_template === 'guide_resource_page' ? 'hide-for-print' : '')?>">
              <span class="title"><?php the_title(); ?></span>
            </h1>
          </div>

          <?php get_template_part( 'partials/content', 'custom-markup-before-wysiwyg' ); ?>
              
            <?php if( !empty( get_the_content() ) ) : ?>
              <div class="intro-text <?php echo ($current_template === 'guide_resource_page' ? 'hide-for-print' : '')?>"><?php the_content(); ?></div>
            <?php endif; ?>

          <?php get_template_part( 'partials/content', 'custom-markup-after-wysiwyg' ); ?>

          <?php 
          switch ($current_template): 
            case('guide_resource_page'):
              include(locate_template('partials/guides/resource-list.php'));
              break;
            default:
              $heading_groups = rwmb_meta( 'phila_heading_groups' );
              $heading_content = phila_extract_clonable_wysiwyg( $heading_groups, $array_key = 'phila_wywiwyg_alt_heading' );
              include(locate_template('partials/content-heading-groups.php'));
          endswitch;
          
          include(locate_template('partials/guides/footer.php')); 
          ?>

        </section>
      </article>
    </div>
  </div>
</div>

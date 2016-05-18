<?php
/**
 * The template used for displaying service pages
 *
 * @package phila-gov
 */

get_header(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class('service'); ?>>
  <div class="row">
    <header class="entry-header small-24 columns">
      <?php the_title( '<h1 class="entry-title contrast">', '</h1>' ); ?>
    </header><!-- .entry-header -->
  </div>

  <?php while ( have_posts() ) : the_post();
   if (function_exists('rwmb_meta')) {
     $service_url = rwmb_meta( 'phila_service_url', $args = array('type' => 'url'));
     $service_name = rwmb_meta( 'phila_service_detail', $args = array('type' => 'textarea'));
     $service_desc = rwmb_meta( 'phila_service_desc', $args = array('type' => 'textarea'));
     $service_button_text =  rwmb_meta( 'phila_service_button_text', $args = array('type' => 'text'));
     $service_before_start =  rwmb_meta( 'phila_service_before_start', $args = array('type' => 'text'));
     $related_content = rwmb_meta( 'phila_service_related_items', $args = array('type' => 'textarea'));
   }
  ?>
  <div class="row">
    <div class="small-24 columns">
      <p class="description"><?php echo $service_desc;?></p>
    </div>
  </div>
  <div class="row columns equal-height">
    <?php
      if ( !$service_before_start == '' ): ?>
        <div class="intro small-24 medium-16 intro-item columns pal equal">
          <div class="row">
            <div class="medium-2 columns show-for-medium">
              <i class="fa fa-flag fa-2x"></i>
            </div>
            <div class="medium-22 small-24 columns">
              <h2 class="h4 mtn">Before you start</h2>
              <?php echo $service_before_start; ?>
            </div>
          </div>
        </div>
        <div class="intro small-24 medium-8 intro-item columns center equal">
          <div class="valign mvm mvn-mu">
            <div class="valign-cell">

      <?php else: //$service_before_start ?>
          <div class="intro small-24 medium-24 intro-item columns center pam">
            <div class="valign mvm mvn-mu">
              <div class="valign-cell">
        <?php endif; //$service_before_start ?>

        <?php if (!$service_url == ''):?>
          <a data-swiftype-index="false" class="button prxl plxl" href="<?php echo $service_url;?>">
            <?php echo ( ( $service_button_text == '')  ? 'Start Now' :  $service_button_text );?>
            <span class="accessible"> External link</span>
          </a>
        <?php endif;?>

        <?php if (!$service_name == ''):?>
          <span data-swiftype-index="false" class="small-text">On the <?php echo $service_name;?> website</span>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
  <div class="row mtm mtl-mu">
    <div data-swiftype-index='true' class="entry-content small-24 medium-17 large-17 columns">
        <?php the_content(); ?>
        <?php endwhile; // end of the loop. ?>
    </div><!-- .entry-content -->
    <?php if (!$related_content == ''):?>
      <aside id="secondary" class="related widget-area small-24 medium-6 large-6 columns mtl mtn-mu">
        <h3 class="alternate mtm mtn-mu">Related Topics</h3>
        <?php echo $related_content; ?>
      </aside>
    <?php endif; ?>
  </div>
</article><!-- #post-## -->

<?php get_footer(); ?>

<?php
/**
 * The template used for displaying service pages
 *
 * @package phila-gov
 */

get_header(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class('service'); ?>>
  <div class="row">
    <div class="small-24 columns">
  		<header class="entry-header">
  			<?php the_title( '<h1 class="entry-title contrast h3">', '</h1>' ); ?>
  		</header><!-- .entry-header -->
    </div>
  </div>
  <?php while ( have_posts() ) : the_post();
   if (function_exists('rwmb_meta')) {
     $service_url = rwmb_meta( 'phila_service_url', $args = array('type' => 'url'));
     $service_name = rwmb_meta( 'phila_service_detail', $args = array('type' => 'textrea'));
     $service_desc = rwmb_meta( 'phila_service_desc', $args = array('type' => 'textarea'));
     $service_button_text =  rwmb_meta( 'phila_service_button_text', $args = array('type' => 'text'));
     $service_before_start =  rwmb_meta( 'phila_service_before_start', $args = array('type' => 'text'));
     $related_content = rwmb_meta( 'phila_service_related_items', $args = array('type' => 'textarea'));
   }
  ?>
  <div class="row">
    <div class="column">
      <p class="description"><?php echo $service_desc;?></p>
    </div>
  </div>
  <div class="row intro">
    <?php if ( !$service_before_start == '' ): ?>
      <div class="intro-item small-24 columns">
        <div class="row collapse">
          <div class="medium-7 columns before-start-left">
            <h3><i class="fa fa-flag fa-3x bell-yellow"></i> Before you start</h3>
          </div>
          <div class="medium-15 columns before-start-right">
            <?php echo $service_before_start; ?>
          </div>
        </div>
      </div>
    <?php endif; ?>
    <div class="intro-item small-24 columns">
      <div class="row collapse">
        <div class="columns center">
          <?php if (!$service_url == ''):?>
            <a data-swiftype-index="false" class="button" href="<?php echo $service_url;?>">
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

  <div class="row top-margin">
  <div data-swiftype-index='true' class="entry-content small-24 medium-17 columns">
    <?php the_content(); ?>
    <?php endwhile; // end of the loop. ?>
    <?php get_template_part( 'partials/content', 'modified' ) ?>
  </div><!-- .entry-content -->
  <?php if (!$related_content == ''):?>
      <aside id="secondary" class="related widget-area small-24 medium-6 columns" role="complementary">
        <h3 class="alternate">Related Topics</h3>
          <?php echo $related_content; ?>
      </aside>
  <?php endif; ?>
</div>

</article><!-- #post-## -->

<?php get_footer(); ?>

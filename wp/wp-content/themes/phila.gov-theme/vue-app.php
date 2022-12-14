<?php
/**
 * Template Name: Vue App
 * Template used for vue apps
 * @package phila-gov
 */

  get_header();
  $app_id = rwmb_meta('phila_vue_app_id');
?>
<div id="primary" class="content-area">
  <main id="main" class="site-main">
    <?php while ( have_posts() ) : the_post();
      get_template_part( 'templates/default', 'page' );
      endwhile; // end of the loop. ?>
    <div id="<?php echo empty($app_id) ? 'vue-app' : $app_id ?>"></div>
  </main><!-- #main -->
</div><!-- #primary -->
<?php get_footer(); ?>

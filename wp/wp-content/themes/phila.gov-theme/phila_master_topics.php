<?php
/**
 * Template Name: Master Topics List
 *
 * @package phila-gov
 */

get_header(); ?>

  <div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
      <div class="row">
              <header class="small-24 columns">
                  <?php echo the_title(); ?>
              </header>
      </div>
      <div class="row">
            <div class="small-24 columns">
                    <?php get_master_topics(); ?>
            </div>
      </div>
    </main><!-- #main -->
  </div><!-- #primary -->

<?php get_footer(); ?>

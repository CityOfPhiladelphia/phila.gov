<?php
/**
 * Archive for Department List
 *
 * @package phila-gov
 */

get_header(); ?>

<div id="primary" class="content-area departments">
  <main id="main" class="site-main" role="main">
    <div class="row">
      <header class="small-24 columns">
        <?php post_type_archive_title('<h1 class="contrast ptm">All City ', '</h1>'); ?>
      </header>
    </div>
    <div class="row">
      <div class="small-24 columns results mbm">
        <?php get_template_part( 'partials/content', 'finder' ); ?>
          <ul class="list no-bullet"><!-- ul for sortable listness -->
            <?php  if ( have_posts() ) : ?>
                <?php the_title(); ?>

                <?php while ( have_posts() ) : the_post(); ?>

                <?php get_template_part( 'partials/content', 'list' ); ?>

                <?php endwhile; ?>
                <?php else : ?>

              <?php endif;
              wp_reset_query();
              ?>

            </ul>
          </div>
        </div>
    </div> <!-- .row -->
  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>

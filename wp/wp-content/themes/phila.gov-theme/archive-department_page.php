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
        <h1><?php  _e( 'All City Departments ', 'phila-gov' ); ?></h1>
      </header>
    </div>
    <div class="row">
      <div class="small-24 columns results">
        <?php get_template_part( 'partials/content', 'finder' ); ?>
          <ul class="list no-bullet"><!-- ul for sortable listness -->
            <?php
              $department_listing = new WP_Query(array(
                'post_type' => 'department_page',
                'posts_per_page' => -1,
                'orderby' => 'title',
                'order'=> 'asc',
                'post_parent' => 0,
                )
              );

              if ( $department_listing->have_posts() ) : ?>

                <?php while ( $department_listing->have_posts() ) : $department_listing->the_post(); ?>

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

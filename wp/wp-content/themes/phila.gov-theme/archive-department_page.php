<?php
/**
 * Archive for Department List
 *
 * @package phila-gov
 */

get_header(); ?>

<div id="primary" class="content-area departments">
  <main id="main" class="site-main">
    <div class="row">
      <header class="small-24 columns">
        <?php printf(__('<h1 class="contrast ptm">City Government Directory</h1>', 'phila-gov') ); ?>
      </header>
    </div>
    <div class="row">
      <div class="small-24 columns results mbm">
        <div id="filter-list">
          <form>
            <input class="search" type="text" placeholder="Filter results...">
          </form>
            <?php
            $args = phila_get_department_homepage_list();
            $department_list = new WP_Query( $args );

            if ( $department_list->have_posts() ) : ?>
              <ul class="list no-bullet">
              <?php while ( $department_list->have_posts() ) : $department_list->the_post(); ?>
                <li>
                  <?php
                    //NOTE: The content-department class is used for Google Analytics and should not be removed.
                  ?><a href="<?php echo get_permalink(); ?>" class="content-department item"><?php echo the_title(); ?></a>
                  <p class="item-desc"><?php echo phila_return_dept_meta(); ?> </p>
                </li>

              <?php endwhile; ?>

              </ul>
            <?php endif; ?>

            <?php wp_reset_query(); ?>
          </div>
        </div>
    </div> <!-- .row -->
  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>

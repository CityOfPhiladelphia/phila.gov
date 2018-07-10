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
        <?php printf(__('<h1 class="contrast ptm">City government directory</h1>', 'phila-gov') ); ?>
      </header>
    </div>
    <div class="row">
      <div class="small-24 columns results mbm">
        <div id="filter-list" class="city-government-directory">
          <form class="search mbxl">
            <input class="search-field" type="text" placeholder="Begin typing to filter results by title, keyword, or acronym...">
            <input type="submit" class="search-submit" value="Search">
          </form>
            <?php
            $args = phila_get_department_homepage_list();
            $department_list = new WP_Query( $args );

            if ( $department_list->have_posts() ) : ?>
            <div class="row list-heading">
              <div class="columns small-6">Name</div>
              <div class="columns small-10 mu-phl">Description</div>
              <div class="columns small-8">Connect</div>
            </div>
            <hr class="strong" />
            <div class="list">
              <?php while ( $department_list->have_posts() ) : $department_list->the_post(); ?>
                <div class="row pvm">
                  <div class="columns small-6">
                  <?php
                    //NOTE: The content-department class is used for Google Analytics and should not be removed.
                  ?><a href="<?php echo get_permalink(); ?>" class="content-department item"><?php echo the_title(); ?>
                    <?php $acronym = rwmb_meta( 'phila_department_acronym' );?> <?php echo !empty($acronym) ? '(' . $acronym . ')' : ''; ?>
                    </a>
                  </div>
                  <div class="columns small-10 mu-phl">
                    <p class="item-desc"><?php echo phila_get_item_meta_desc( ); ?>
                    <span class="hidden"><?php echo  rwmb_meta('phila_department_keywords')?></span>               </p>
                  </div>
                  <?php get_template_part( 'partials/departments/v2/content', 'connect' ); ?>
                </div>
                <hr />
              <?php endwhile; ?>
            </div>
            <?php endif; ?>
            <?php wp_reset_query(); ?>
        </div>
      </div>
    </div> <!-- .row -->
  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>

<?php
/**
 * This is the template that displays all service pages by default.
 *
 * @package phila-gov
 */
 get_header(); ?>

<div id="primary" class="content-area">
  <main id="main" class="site-main">

    <?php while ( have_posts() ) : the_post(); ?>
    <?php $user_selected_template = phila_get_selected_template(); ?>

    <article id="post-<?php the_ID(); ?>">
    <div class="row">
      <header class="entry-header small-24 columns">
        <h1 class="contrast"><?php echo get_the_title(); ?></h1>
        </header>
      </div>
      <div class="row">
        <div class="medium-24 columns">
          <div data-swiftype-index='true' data-swiftype-name="body" data-swiftype-type="text" class="entry-content">
            <?php if ($user_selected_template == 'tax_detail') : ?>
              <?php get_template_part('partials/taxes/content', 'tax-detail');?>
            <?php else : ?>
              <?php the_content(); ?>
            <?php endif; ?>
            </div>
          </div>
        </div>
    </article><!-- #post-## -->


  <?php  endwhile; // end of the loop. ?>

  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>

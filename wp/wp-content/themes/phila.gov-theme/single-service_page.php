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
            <!-- Service Stub  -->
            <?php elseif ($user_selected_template == 'service_stub') : ?>
              <?php if ( null !== rwmb_meta( 'phila_stub_source' ) ) : ?>
                <?php $stub_source = rwmb_meta( 'phila_stub_source' );?>
                <?php $post_id = intval( $stub_source );?>

                <?php $stub_args = array(
                  'p' => $post_id,
                  'post_type' => 'service_page'
                ); ?>
                <?php $stub_post = new WP_Query($stub_args); ?>
                <?php if ( $stub_post->have_posts() ): ?>
                  <?php while ( $stub_post->have_posts() ) : ?>
                    <?php $stub_post->the_post(); ?>
                    <?php $source_template =  rwmb_meta( 'phila_template_select'); ?>
                    <?php if ($source_template == 'default') :?>
                      <?php the_content(); ?>
                    <?php elseif ($source_template == 'tax_detail') : ?>
                      <?php get_template_part('partials/taxes/content', 'tax-detail'); ?>
                    <?php endif; ?>
                  <?php endwhile; ?>
                <?php endif; ?>
                <?php wp_reset_query(); ?>
              <?php endif; ?>
              <!-- END Service Stub -->

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

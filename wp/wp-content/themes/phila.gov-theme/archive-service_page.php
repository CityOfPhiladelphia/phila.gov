<?php
/**
 * Archive for Service Directory
 *
 * @package phila-gov
 */

get_header(); ?>

<div id="primary" class="content-area services">
  <main id="main" class="site-main">
    <div class="row">
      <header class="small-24 columns">
        <?php printf(__('<h1 class="contrast ptm">Service Directory</h1>', 'phila-gov') ); ?>
      </header>
    </div>
    <div class="row">
      <div class="medium-8 columns">
        <?php printf(__('<h2 class="h4">Filter by Service Categories</h2>', 'phila-gov') ); ?>
        <?php $terms = get_terms(
          array(
            'taxonomy' => 'service_type',
            'hide_empty' => true,
            )
        );?>
        <form>
        <?php foreach ( $terms as $term ) : ?>
          <div>
            <input type="checkbox" name="<?php echo $term->slug ?>" value="<?php echo $term->slug ?>" id="<?php echo $term->slug ?>"><label for="<?php echo $term->slug ?>"><?php echo $term->name ?></label>
          </div>
        <?php endforeach; ?>
        </form>
      </div>
      <div class="medium-16 columns results mbm">
        <?php
          if ( have_posts() ) : ?>
          <?php while ( have_posts() ) : the_post(); ?>
            <?php echo the_title('<h2>', '</h2>'); ?>
            <?php echo phila_get_item_meta_desc( $blog_info = false ); ?>
          <?php endwhile; ?>

        <?php endif; ?>

        <?php wp_reset_query(); ?>
      </div>
    </div> <!-- .row -->
  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>

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
        <?php $args = array(
            'post_type'  => 'service_page',
            'posts_per_page'  => -1,
            'order' => 'ASC',
            'orderby' => 'title',
            'meta_query' => array(
              array(
                'key'     => 'phila_template_select',
                'value'   => 'topic_page',
                'compare' => 'NOT IN',
              ),
            ),
          );
          $service_pages = new WP_Query( $args );
        ?>
          <?php if ( $service_pages->have_posts() ) : ?>
          <?php while ( $service_pages->have_posts() ) : $service_pages->the_post(); ?>

            <?php $terms = wp_get_post_terms( $post->ID, 'service_type' ); ?>
            <?php $page_terms = array(); ?>
            <?php foreach ( $terms as $term ) : ?>
              <?php array_push($page_terms, $term->slug); ?>
            <?php endforeach; ?>

              <div class="service" data-service="<?php echo implode( ' ', $page_terms ) ?>">
                <a href="<?php echo get_permalink();?>"><?php echo the_title(); ?></a>
                <p class="hide-for-small-only"><?php echo phila_get_item_meta_desc( $blog_info = false ); ?></p>
              </div>
          <?php endwhile; ?>

        <?php endif; ?>

        <?php wp_reset_query(); ?>
      </div>
    </div> <!-- .row -->
  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>

<?php
/*
 *
 * Topic Page Template
 *
 */
 ?>
<?php if (!empty(phila_get_item_meta_desc( $bloginfo = false ) ) ) : ?>
  <section class="row mbl">
    <div class="medium-24 columns">
      <?php echo phila_get_item_meta_desc( $bloginfo = false ); ?>
    </div>
  </section>
<?php endif; ?>
<?php $topic_args = array(
  'post_type'      => 'service_page',
  'posts_per_page' => -1,
  'post_parent'    => $post->ID,
  'orderby' => 'menu_order title',
  'order' => 'ASC'

); ?>
<?php $counter = 0; ?>

<?php $topic_children = new WP_Query( $topic_args ); ?>
<?php if ( $topic_children->have_posts() ): ?>
  <div class="row grid-x fat-gutter">

  <?php while ( $topic_children->have_posts() ) : ?>
    <?php $topic_children->the_post(); ?>
    <?php $counter++; ?>
      <div class="flex-container auto small-24 medium-12 column">
        <a href="<?php the_permalink(); ?>" class="card sub-topic">
          <div class="content-block">
            <h3><?php the_title(); ?></h3>
            <?php echo phila_get_item_meta_desc( $bloginfo = false ); ?>
          </div>
        </a>
      </div>
  <?php endwhile;?>
  </div>
<?php endif; ?>
<?php wp_reset_query(); ?>
<?php get_template_part( 'partials/content', 'additional' ); ?>
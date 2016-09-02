<?php
/*
 *
 * Topic Page Template
 *
 */
 ?>
<section class="row">
<div class="medium-24">
  <?php the_content(); ?>
</div>
</section>
<?php $topic_args = array(
  'post_type'      => 'service_page',
  'posts_per_page' => -1,
  'post_parent'    => $post->ID,
  'order'          => 'ASC',
  'orderby'        => 'menu_order'
); ?>

<?php $topic_children = new WP_Query( $topic_args ); ?>
<?php if ( $topic_children->have_posts() ): ?>
  <?php // TODO: count to three and close the row ?>
  <?php while ( $topic_children->have_posts() ) : ?>
    <?php $topic_children->the_post(); ?>
      <div class="small-24 medium-8 column">
        <a href="<?php the_permalink(); ?>" class="card sub-topic equal">
          <div class="content-block">
            <h3><?php the_title(); ?></h3>
            <?php echo phila_get_item_meta_desc(); ?>
          </div>
        </a>
      </div>
  <?php endwhile;?>
<?php endif; ?>
<?php wp_reset_query(); ?>

<?php
  /*
  * Guides side nav template
  */

  
  global $post;
  $direct_parent = $post->post_parent;
  $args = array(
    'post_type'      => 'guides',
    'posts_per_page' => -1,
    'post_parent'    => $direct_parent,
    'order'          => 'ASC',
    'orderby'        => 'menu_order',
  );
  $parent = new WP_Query( $args );

  if ( $parent->have_posts() ) : ?>

  <section>
    <?php while ( $parent->have_posts() ) : $parent->the_post(); ?>
    <?php $guide_icon = rwmb_meta('guide_page_icon'); ?>
    <?php $heading_groups = rwmb_meta( 'phila_heading_groups' ); ?>

    <?php $sub_heads = phila_extract_clonable_wysiwyg( $heading_groups ); ?>
      <nav class="sticky-side-nav">
        <ul>
          <li><a href="<?php the_permalink()?>">
            <span class="icon"><?php echo !empty( $guide_icon )  ? '<i class="' . $guide_icon . '"></i>' : '' ?></span>
            <span class="title"><?php the_title(); ?></span>
          </a>
          <ul>
            <?php foreach($sub_heads as $sub_head) : ?>
              <li><a href="<?php the_permalink()?>#<?php echo sanitize_title_with_dashes($sub_head['phila_wysiwyg_heading']) ?>"><?php echo $sub_head['phila_wysiwyg_heading'] ?></a></li>
            <?php endforeach?>
            </ul>
          </li>
        </ul>
      </nav>
    <?php endwhile; ?>
  </section>
  
<?php endif; wp_reset_query(); ?>

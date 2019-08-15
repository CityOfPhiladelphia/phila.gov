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
  $this_post = $post->ID;

  
  if ( $parent->have_posts() ) : ?>
    <section>
      <div class="sticky-container" data-sticky-container>
        <div class="sticky-side-nav" data-sticky data-top-anchor="breadcrumbs:bottom" data-btm-anchor="global-footer" data-margin-top="7" id="guides-nav">
        <nav class="mbxxl">

        <?php while ( $parent->have_posts() ) : $parent->the_post(); ?>
        <?php $guide_icon = rwmb_meta('guide_page_icon'); ?>
        <?php $heading_groups = rwmb_meta( 'phila_heading_groups' ); ?>

        <?php $sub_heads = phila_extract_clonable_wysiwyg( $heading_groups, $array_key = 'phila_wywiwyg_alt_heading' ); ?>
          <ul class="no-bullet" data-magellan data-offset="300" data-threshold="100">
            <li>
              <a href="<?php echo ($this_post !== $post->ID) ? get_the_permalink() : '#' . sanitize_title_with_dashes(get_the_title()); ?>">
                <span class="icon"><?php echo !empty( $guide_icon )  ? '<i class="' . $guide_icon . ' fa-fw"></i>' : '' ?></span>
                <span class="title"><?php the_title(); ?></span>
              </a>
              <ul class="no-bullet">
                <?php foreach($sub_heads as $sub_head) : ?>
                  <li><a href="<?php echo ($this_post !== $post->ID) ? get_the_permalink() : '' ?>#<?php echo isset( $sub_head['phila_heading_alt']) ? sanitize_title_with_dashes($sub_head['phila_heading_alt']) : sanitize_title_with_dashes($sub_head['phila_wysiwyg_heading']) ?>"><?php echo isset( $sub_head['phila_heading_alt']) ? $sub_head['phila_heading_alt'] : $sub_head['phila_wysiwyg_heading'] ?></a></li>
                <?php endforeach?>
              </ul>
            </li>
          </ul>
        <?php endwhile; ?>
        </nav>
      </div>
    </section>
    
  <?php endif; ?>
<?php wp_reset_query(); ?>

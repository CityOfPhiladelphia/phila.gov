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
        <div class="sticky-side-nav" data-sticky  data-top-anchor="breadcrumbs:bottom" data-margin-top="5" data-btm-anchor="global-footer" id="guides-nav">
        <nav>
        <ul class="no-bullet hide-for-medium">
          <li id="home-nav">

            <a class="title-link" href="<?php echo get_the_permalink( $direct_parent ) ?> ">
            <i class="fas fa-home"></i>
              Overview
            </a>
            <div id="nav-menu-caret"> <i class="fas fa-times fa-2x"></i>  </div>
        </li>
        </ul>

        <?php while ( $parent->have_posts() ) : $parent->the_post(); ?>
        <?php $guide_icon = rwmb_meta('guide_page_icon'); ?>
        <?php $heading_groups = rwmb_meta( 'phila_heading_groups' ); ?>
     

        <?php $sub_heads = phila_extract_clonable_wysiwyg( $heading_groups, $array_key = 'phila_wywiwyg_alt_heading' ); ?>
          <ul class="no-bullet">
            <li>
              <a href="<?php echo get_the_permalink()?>" class="<?php echo ($this_post !== $post->ID) ? '' : 'is-active' ?> title-link">
                <span class="icon"><?php echo !empty( $guide_icon )  ? '<i class="' . $guide_icon . ' fa-fw"></i>' : '' ?></span>
                <span class="title"><?php the_title(); ?></span>
              </a>
              <ul class="no-bullet">
                <?php foreach($sub_heads as $sub_head) : ?>
                  <li><a class="nav-subheader" href="<?php echo ($this_post !== $post->ID) ? get_the_permalink() : '' ?>#<?php echo isset( $sub_head['phila_heading_alt']) ? sanitize_title_with_dashes($sub_head['phila_heading_alt']) : sanitize_title_with_dashes($sub_head['phila_wysiwyg_heading']) ?>"><?php echo isset( $sub_head['phila_heading_alt']) ? $sub_head['phila_heading_alt'] : $sub_head['phila_wysiwyg_heading'] ?></a></li>
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


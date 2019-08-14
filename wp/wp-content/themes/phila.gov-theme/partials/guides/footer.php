<?php 
/*
 *  Guides footer
 * 
 */
?>

<footer class="guides-footer mtxl">
  <?php
    $args = array(
      'child_of' => $post->post_parent,
      'parent'  => $post->post_parent,
      'sort_column' => 'menu_order',
      'sort_order'=> 'asc',
      'post_type' => 'guides'
    );
    $pagelist = get_pages($args);
    $post_parent = $post->post_parent;
    $pages = array();

    foreach ($pagelist as $page) {
      $pages[] += $page->ID;
    }

    $current = array_search($post->ID, $pages);
    $previous = isset( $pages[$current-1] ) ? $pages[$current-1] : -1 ;
    $next = isset($pages[$current+1]) ? $pages[$current+1] : null;
  ?>

  <nav>
    <div class="grid-x">
      <?php if (!empty($previous)) :?>
        <div class="cell medium-12">

          <?php if ($previous == -1) : ?>
            <?php $landing_title = rwmb_meta('guide_landing_page_title', $post_parent);?>
            <a href="<?php echo get_permalink($post_parent); ?>" title="<?php !empty($landing_title) ? $landing_title : 'Overview' ?>"><i class="fas fa-arrow-left"></i> <?php echo !empty($landing_title) ? $landing_title : 'Overview' ?></a>

            <?php else: ?>
              <a href="<?php echo get_permalink($previous); ?>" title="<?php echo get_the_title($previous); ?>"><i class="fas fa-arrow-left"></i> <?php echo get_the_title($previous)?></a>
            <?php endif;?>
            </div>

          <?php endif;?>
        <?php if (!empty($next)) : ?>
        <div class="cell medium-12 text-right">
          <a href="<?php echo get_permalink($next); ?>" title="<?php echo get_the_title($next); ?>"><?php echo get_the_title($next); ?> <i class="fas fa-arrow-right"></i></a>
        </div>
    <?php endif; ?>
    </div>
  </nav>
</footer>
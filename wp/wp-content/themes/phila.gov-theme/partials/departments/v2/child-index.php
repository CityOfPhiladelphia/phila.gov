<?php
/*
 * Partial for rendering all child pages under this menu item.
 *
 */
?>

<div class="row">
  <div class="columns">
    <div class="one-quarter-layout">

      <?php
      $args = array(
        'post_parent' => $post->ID,
        'post_type'   => 'any',
        'numberposts' => -1,
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'post_status' => array('publish', 'private', 'draft')
      );
      $children = get_children( $args );

      $last_key = phila_util_is_last_in_array( (array) $children );

      foreach ($children as $key => $child) : ?>

      <div class="row one-quarter-row mvl">
        <div class="medium-6 columns">
          <h3 id="<?php echo sanitize_title_with_dashes($child->post_title)?>"><?php echo $child->post_title ?></h3>
        </div>
          <div class="medium-18 columns pbxl">
            <?php echo rwmb_meta('phila_meta_desc', '', $child->ID)?>
            <a href="<?php echo get_permalink($child->ID) ?>">Learn more <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
        <?php if ($last_key != $key) : ?>
        <hr class="mhn"/>
      <?php endif ?>
      <?php endforeach;?>
    </div>
  </div>
</div>

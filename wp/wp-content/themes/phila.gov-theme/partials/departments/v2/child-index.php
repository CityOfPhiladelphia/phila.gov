<?php
/*
 * Partial for rendering all child pages under this menu item.
 *
 */
?>
<?php   get_template_part( 'partials/breadcrumbs' ); ?>
<article>

  <div data-swiftype-index='true' class="entry-content">

    <header class="row">
      <div class="columns">
        <?php the_title( '<h2 class="sub-page-title contrast">', '</h2>' ); ?>
      </div>
    </header>

    <div class="row">
      <div class="columns">
        <?php
        $args = array(
        	'post_parent' => $post->ID,
        	'post_type'   => 'any',
        	'numberposts' => -1,
        	'post_status' => 'any'
        );
        $children = get_children( $args );
        foreach ($children as $child) : ?>
          <a href="<?php echo get_permalink($child->ID) ?>"><?php echo $child->post_title ?></a> - <?php echo rwmb_meta('phila_meta_desc', '', $child->ID)?>
          <hr />
        <?php endforeach;?>
      </div>
    </div> <!-- .row -->
  </div>
</article>

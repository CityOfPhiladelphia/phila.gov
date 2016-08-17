<?php
/*
 *
 * Partial for rendering Featured Programs and Initiatives
 *
 */
?>
<!-- Display Featured Programs and Initiatives -->
<section class="mvl">
  <div class="row">
    <div class="columns">
      <h2 class="contrast"> Core Initiatives </h2>
    </div>
  </div>
  <section class="row equal-height mbl">
    <?php
      //FIXME: Need to count featured pages and set appropriate column widths
      foreach ($featured as $key => $value):
    ?>
      <article class="large-8 medium-24 columns featured-content programs equal">
        <?php
          if ( null !== rwmb_meta( 'phila_p_i_images', $arg ='type=textarea', $post_id = intval($featured[$key]) )):
            $featured_post = get_post( $featured[$key] );
            $featured_item =  rwmb_meta( 'phila_p_i_images', $arg ='type=textarea', $post_id = intval($featured[$key]) );
            $featured_image = isset( $featured_item['phila_p_i_featured'] ) ? $featured_item['phila_p_i_featured'] : '';
            $short_description = isset( $featured_item['phila_short_feat_desc'] ) ? $featured_item['phila_short_feat_desc'] : '';
            $long_description = isset( $featured_item['phila_long_feat_desc'] ) ? $featured_item['phila_long_feat_desc'] : '';
          endif;
        ?>
          <div class="featured-thumbnail">
            <img src="<?php echo $featured_image;?>" alt="" class="mrm">
          </div>
          <div>
            <header>
              <a href="<?php echo get_permalink($featured[$key]); ?>"><h4 class="h6"><?php echo $featured_post->post_title; ?></h4></a>
            </header>
            <p><?php echo $short_description;?></p>
          </div>
        </article>
    <?php endforeach;?>
  </section>
</section>

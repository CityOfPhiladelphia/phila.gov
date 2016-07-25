<?php
/**
 * The template used for displaying Department Subpages
 *
 * @package phila-gov
 */
?>
<?php
  // MetaBox variables
  $page_rows = rwmb_meta('phila_row');
?>
<div>
<?php
    foreach ($page_rows as $key => $value):
      $current_row = $page_rows[$key];?>
      
      <!-- Grid Row -->
      <?php if ( isset( $current_row['phila_grid_options'] ) && $current_row['phila_grid_options'] == 'phila_grid_options_full'):
          // Begin full width row
          $current_row_option = $current_row['phila_full_options']['phila_full_options_select'];

          if ($current_row_option == 'phila_feature_p_i'): ?>
            <!-- Display Featured Programs and Initiatives -->
            <div class="row mvm">
              <div class="columns">
                <h2 class="contrast"> Core Initiatives </h2>
              </div>
            </div>
            <section class="row equal-height mbl">
              <?php
                $featured = $current_row['phila_full_options']['phila_feature_p_i']['phila_p_i'];
                foreach ($featured as $key => $value):
              ?>

                <article class="large-24 columns featured-content equal">
                  <?php
                    if ( null !== rwmb_meta( 'phila_p_i_images', $arg ='type=textarea', $post_id = intval($featured[$key]) )):
                      $featured_post = get_post( $featured[$key] );
                      $featured_item =  rwmb_meta( 'phila_p_i_images', $arg ='type=textarea', $post_id = intval($featured[$key]) );
                      $featured_image = isset( $featured_item['phila_p_i_featured'] ) ? $featured_item['phila_p_i_featured'] : '';
                      $short_description = isset( $featured_item['phila_short_feat_desc'] ) ? $featured_item['phila_short_feat_desc'] : '';
                      $long_description = isset( $featured_item['phila_long_feat_desc'] ) ? $featured_item['phila_long_feat_desc'] : '';
                    endif;
                  ?>
                    <img src="<?php echo $featured_image;?>" alt="" class="mrm mbxxl-mu">
                    <header>
                      <a href="<?php echo get_permalink($featured[$key]); ?>"><h3><?php echo $featured_post->post_title; ?></h3></a>
                    </header>
                    <p><?php echo $long_description;?></p>
                  </article>

              <?php endforeach;?>
            </section>
          <?php endif; ?>
      <?php endif; ?>
    <!-- Grid Row -->
    <?php endforeach; ?>
</div>

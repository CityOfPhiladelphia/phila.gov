<?php
/**
 * The template used for displaying Department Homepage
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
        <?php if ( $current_row['phila_grid_options'] == 'phila_grid_options_full'):
            $current_row_option = $current_row['phila_full_options']['phila_full_options_select'];

            if ( $current_row_option == 'phila_blog_posts'): ?>
              <!-- Blog Content -->
                <section class="row mvl">
                  <?php echo do_shortcode('[recent-posts posts="3"]'); ?>
                </section>

            <?php elseif ( $current_row_option == 'phila_full_width_calendar'):
              //TODO: verify that these vars exist before setting... offload to function?
              $cal_id = $current_row['phila_full_options']['phila_full_width_calendar']['phila_full_width_calendar_id'];
              $cal_url = $current_row['phila_full_options']['phila_full_width_calendar']['phila_full_width_calendar_url'];?>

              <!-- Full Width Calendar -->
              <section class="row">
                <div class="columns">
                  <h2>Events</h2>
                </div>
              </div>
              <div class="row expanded calendar-row mbm ptm">
                <div class="medium-centered large-16 columns">
                  <?php echo do_shortcode('[calendar id="' . $cal_id . '"]'); ?>
                </div>
              </section>

            <?php elseif ($current_row_option == 'phila_feature_p_i'): ?>
              <!-- Display Featured Programs and Initiatives -->
              <section class="mvl">
                <div class="row">
                  <div class="columns">
                    <h2 class="contrast"> Core Initiatives </h2>
                  </div>
                </div>
                <section class="row equal-height mbl">
                  <?php
                    $featured = $current_row['phila_full_options']['phila_feature_p_i']['phila_p_i']['phila_p_i_items'];
                    foreach ($featured as $key => $value):
                  ?>
                    <article class="large-8 medium-24 columns featured-content programs equal">
                      <?php
                      //FIXME: This needs to be reworked a bit...
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
            <?php elseif ($current_row_option == 'phila_get_involved'): ?>
              <!-- Display Multi Call to Action as Get Involved -->
              <?php
                $phila_dept_homepage_cta = $current_row['phila_full_options']['phila_call_to_action_multi']['phila_call_to_action_section'];
                include(locate_template('partials/departments/content-get-involved.php'));
              ?>

            <?php elseif ( $current_row_option == 'phila_full_width_press_releases'): ?>
              <!-- Press Releases -->
                <section class="row mvl">
                  <?php echo do_shortcode('[press-releases posts=5]');?>
                </section>

            <?php elseif ($current_row_option == 'phila_resource_list'): ?>
              <!-- Display Multi Call to Action as Resource List -->
              <?php
                $phila_dept_homepage_cta = $current_row['phila_full_options']['phila_call_to_action_multi']['phila_call_to_action_section'];
                include(locate_template('partials/departments/content-call-to-action-multi.php'));
              ?>

            <?php elseif ( $current_row_option == 'phila_custom_text'): ?>

              <?php $custom_text = $current_row['phila_full_options']['phila_custom_text']; ?>
                <section class="row mvl">
                  <div class="large-24 columns">
                    <h2 class="contrast"><?php echo($custom_text['phila_custom_text_title']); ?></h2>
                    <div>
                      <?php echo($custom_text['phila_custom_text_content']); ?>
                    </div>
                    <?php if ( $custom_text == '' ) :?>
                      <div class="placeholder">
                        Please enter content.
                      </div>
                    <?php endif; ?>
                  </div>
                </section>
          <?php endif; ?>
        <?php elseif ($current_row['phila_grid_options'] == 'phila_grid_options_thirds'):
          $current_row_option_one = $current_row['phila_two_thirds_options'] ['phila_two_thirds_col'];
          $current_row_option_two = $current_row['phila_two_thirds_options'] ['phila_one_third_col']; ?>

          <section class="row mvl">
          <?php if ( $current_row_option_one['phila_two_thirds_col_option'] == 'phila_blog_posts'): ?>
            <!-- Blog Content -->
            <div class="large-18 columns">
              <div class="row">
                <?php echo do_shortcode('[recent-posts posts="3"]'); ?>
              </div>
            </div>
          <?php elseif ( $current_row_option_one['phila_two_thirds_col_option'] == 'phila_custom_text'):?>

            <?php $custom_text = $current_row_option_one['phila_custom_text']; ?>

              <div class="large-18 columns">
                <h2 class="contrast"><?php echo($custom_text['phila_custom_text_title']); ?></h2>
                <div>
                  <?php echo($custom_text['phila_custom_text_content']); ?>
                </div>
                <?php if ( $custom_text == '' ) :?>
                  <div class="placeholder">
                    Please enter content.
                  </div>
                <?php endif; ?>
              </div>

            <?php endif;?>

            <?php if ( $current_row_option_two['phila_one_third_col_option'] == 'phila_connect_panel'):
              $connect_panel = $current_row_option_two['phila_connect_panel'];

              // Set Connect Panel vars
              $connect_vars = phila_connect_panel($connect_panel);
              // TODO: get_template_part vs. include: can't pass arbitrary vars to templates
              // get_template_part( 'partials/departments/content', 'connect' );
              include(locate_template('partials/departments/content-connect.php'));

            endif; ?>
          </section>
        <?php endif; ?>
      <!-- Grid Row -->
      <?php endforeach; ?>
</div>

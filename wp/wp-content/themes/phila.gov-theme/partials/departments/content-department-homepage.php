<?php
/**
 * The template used for displaying Department Homepage
 *
 * @package phila-gov
 */
?>
<?php
  // MetaBox variables
  $dept_homepage_rows = rwmb_meta('phila_row');
  // print_r($dept_homepage_rows);
?>

  <div>
  <?php
      foreach ($dept_homepage_rows as $key => $value):
        $current_row = $dept_homepage_rows[$key];?>
        <!-- Grid Row -->
        <?php if ( $current_row['phila_grid_options'] == 'phila_grid_options_full'):
            $current_row_option = $current_row['phila_full_options']['phila_full_options_select'];

            if ( $current_row_option == 'phila_blog_posts'): ?>
              <!-- Blog Content -->
              <div class="large-24 columns">
                <div class="row">
                  <?php echo do_shortcode('[recent-posts posts="3"]'); ?>
                </div>
              </div>

            <?php elseif ( $current_row_option == 'phila_full_width_calendar'):
              //TODO: verify that these vars exist before setting... offload to function?
              $cal_id = $current_row['phila_full_options']['phila_full_width_calendar']['phila_full_width_calendar_id'];
              $cal_url = $current_row['phila_full_options']['phila_full_width_calendar']['phila_full_width_calendar_url'];?>

              <!-- Full Width Calendar -->
              <div class="row">
                <div class="columns">
                  <h2>Events</h2>
                </div>
              </div>
              <div class="row expanded calendar-row mbm ptm">
                <div class="medium-centered large-16 columns">
                  <?php echo do_shortcode('[calendar id="' . $cal_id . '"]'); ?>
                </div>
              </div>

            <?php elseif ($current_row_option == 'phila_get_involved'): ?>
              <!-- Display Multi Call to Action as Get Involved -->
              <?php
                $phila_dept_homepage_cta = $current_row['phila_full_options']['phila_call_to_action_multi']['phila_call_to_action_section'];
                include(locate_template('partials/departments/content-get-involved.php'));
              ?>

            <?php elseif ( $current_row_option == 'phila_full_width_press_releases'): ?>
              <!-- Press Releases -->
              <div class="large-24 columns">
                <div class="row">
                  <?php echo do_shortcode('[press-releases posts=5]');?>
                </div>
              </div>

            <?php elseif ($current_row_option == 'phila_resource_list'): ?>
              <!-- Display Multi Call to Action as Resource List -->
              <?php
                $phila_dept_homepage_cta = $current_row['phila_full_options']['phila_call_to_action_multi']['phila_call_to_action_section'];
                include(locate_template('partials/departments/content-call-to-action-multi.php'));
              ?>

            <?php elseif ( $current_row_option == 'phila_custom_text'): ?>

              <?php $custom_text = $current_row['phila_full_options']['phila_custom_text']; ?>
                <div class="row">
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
                </div>
          <?php endif; ?>
        <?php elseif ($current_row['phila_grid_options'] == 'phila_grid_options_thirds'):
          $current_row_option_one = $current_row['phila_two_thirds_options'] ['phila_two_thirds_col'];
          $current_row_option_two = $current_row['phila_two_thirds_options'] ['phila_one_third_col']; ?>

          <section class="mvl">
            <div class="row">
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

            </div>
          </section>
        <?php endif; ?>
      <!-- Grid Row -->
      <?php endforeach; ?>
</div>

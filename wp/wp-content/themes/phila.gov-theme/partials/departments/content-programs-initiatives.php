<?php
/**
 * The template used for displaying Program and Initiative pages
 *
 * @package phila-gov
 */
?>
<?php
  // MetaBox variables
  $p_i_page_rows = rwmb_meta('phila_row');
  // print_r($dept_homepage_rows);
?>
<div>
<?php
    foreach ($p_i_page_rows as $key => $value):
      $current_row = $p_i_page_rows[$key];?>
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
          <?php elseif ( $current_row_option == 'phila_callout'):
            $callout_type = $current_row['phila_full_options']['phila_callout']['phila_callout_type'];
            $callout_text = $current_row['phila_full_options']['phila_callout']['phila_callout_text'];
          ?>
            <div class="row">
              <div class="large-24 column">
                  <?php echo do_shortcode('[callout summary="' . $callout_text . '" type="' . $callout_type . '"]'); ?>
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

      <?php elseif ($current_row['phila_grid_options'] == 'phila_grid_options_half'):
        $current_row_option_one = $current_row['phila_half_options'] ['phila_half_col_1'];
        $current_row_option_two = $current_row['phila_half_options'] ['phila_half_col_2']; ?>
        <section class="mvl">
          <div class="row">
            <?php if ( $current_row_option_one['phila_half_col_1_option'] == 'phila_custom_text'):?>
              <?php $custom_text = $current_row_option_one['phila_custom_text']; ?>

                <div class="large-12 columns">
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
            <?php elseif ( $current_row_option_one['phila_half_col_1_option'] == 'phila_pullquote'):?>
              <div class="large-12 columns">
                <?php echo do_shortcode('[pullquote quote="To meaningfully change the outcomes of our schools, the single most important investment we can make is in pre-K." attribution="Mayor Jim Kenney"]'); ?>
              </div>
            <?php endif; ?>


            <?php if ( $current_row_option_two['phila_half_col_2_option'] == 'phila_custom_text'):?>
              <?php $custom_text = $current_row_option_one['phila_custom_text']; ?>

                <div class="large-12 columns">
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
            <?php elseif ( $current_row_option_two['phila_half_col_2_option'] == 'phila_pullquote'):?>
              <?php $pullquote = $current_row_option_two['phila_pullquote'];?>
              <div class="large-12 columns pullquote-wrapper">
                <?php echo do_shortcode('[pullquote quote="' . $pullquote['phila_quote'] . '" attribution="' . $pullquote['phila_attribution'] . '"]'); ?>
              </div>
            <?php endif; ?>



          </div>
        </section>
      <?php elseif ($current_row['phila_grid_options'] == 'phila_grid_options_thirds'):
        $current_row_option_one = $current_row['phila_two_thirds_options'] ['phila_two_thirds_col'];
        $current_row_option_two = $current_row['phila_two_thirds_options'] ['phila_one_third_col']; ?>

        <section class="mvl">
          <div class="row">
        <?php if ( $current_row_option_one['phila_two_thirds_col_option'] == 'phila_two_thirds_column_blog_posts'): ?>
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

          <?php if ( $current_row_option_two['phila_one_third_col_option'] == 'phila_one_third_column_connect'):
            $connect_panel = $current_row_option_two['phila_connect_panel'];

            // Set Connect Panel vars
            $connect_vars = phila_connect_panel($connect_panel);
            // TODO: get_template_part vs. include: can't pass arbitrary vars to templates
            // get_template_part( 'partials/departments/content', 'connect' );
            include(locate_template('partials/departments/content-connect.php'));

          endif; ?>

          </div>
        </div>
      <?php endif; ?>
    <!-- Grid Row -->
    <?php endforeach; ?>
</div>

<?php
/**
 * The template used for displaying Department Homepage
 *
 * @package phila-gov
 */
?>
<?php
  // MetaBox variables
  $dept_homepage_rows = rwmb_meta('phila_two_column_row');
  print_r($dept_homepage_rows);
?>

  <div>
  <?php
      foreach ($dept_homepage_rows as $key => $value):
        $current_row = $dept_homepage_rows[$key];?>
        <!-- Grid Row -->
        <?php if ( $current_row['phila_grid_options'] == 'phila_grid_options_full'):
            $current_row_option = $current_row['phila_full_options']['phila_full_options_select'];

            if ( $current_row_option == 'phila_full_width_calendar'):
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

            <?php elseif ( $current_row_option == 'phila_full_width_press_releases'):
              echo 'press release';
            endif;

        elseif ($current_row['phila_grid_options'] == 'phila_grid_options_thirds'):
          $current_row_option_one = $current_row['phila_two_thirds_options'] ['phila_two_thirds_col'];
          $current_row_option_two = $current_row['phila_two_thirds_options'] ['phila_one_third_col']; ?>

          <section class="mvl">
            <div class="row">
          <?php if ( $current_row_option_one == 'phila_two_thirds_column_blog_posts'): ?>
            <!-- Blog Content -->
            <div class="large-18 columns">
              <div class="row">
                <?php echo do_shortcode('[recent-posts posts="3"]'); ?>
              </div>
            </div>
          <?php elseif ( $current_row_option_one == 'phila_full_width_press_releases'):
              echo 'press release';
            endif;?>

            <?php if ( $current_row_option_two['phila_one_third_col_option'] == 'phila_one_third_column_connect'):
              $connect_panel = $current_row_option_two['phila_connect_panel'];

              // Set Connect Panel vars
              $connect_vars = phila_connect_panel($connect_panel);
              // TODO: get_template_part vs. include....
              include(locate_template('partials/departments/content-connect.php'));

            endif; ?>

            </div>
          </div>
        <?php endif; ?>
      <!-- Grid Row -->
      <?php endforeach; ?>
</div>

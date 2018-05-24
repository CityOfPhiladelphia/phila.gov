<?php
/**
 * The template used for displaying Department Homepage
 *
 * @package phila-gov
 */
?>
<?php
  // set category vars for news/blogs
  $category = get_the_category();
  $category_slug = $category[0]->slug;

  // MetaBox variables
  $page_rows = rwmb_meta('phila_row');
?>

  <div>
  <?php
      foreach ($page_rows as $key => $value):
        $current_row = $page_rows[$key];?>
        <!-- Grid Row -->
        <?php if ( ( isset( $current_row['phila_grid_options'] ) && $current_row['phila_grid_options'] == 'phila_grid_options_full' ) &&  isset( $current_row['phila_full_options']['phila_full_options_select'] ) ):
        $current_row_option = $current_row['phila_full_options']['phila_full_options_select'];
        if ( $current_row_option == 'phila_blog_posts'): ?>

        <!-- Blog Content 1-->
          <section class="mvl">
            <?php include(locate_template('partials/departments/phila_full_row_blog.php'));?>
          </section>

            <?php elseif ( $current_row_option == 'phila_full_width_calendar'):
              $cal_id = isset( $current_row['phila_full_options']['phila_full_width_calendar']['phila_full_width_calendar_id'] ) ? $current_row['phila_full_options']['phila_full_width_calendar']['phila_full_width_calendar_id'] : '' ;
              $cal_url = isset( $current_row['phila_full_options']['phila_full_width_calendar']['phila_full_width_calendar_url'] ) ? $current_row['phila_full_options']['phila_full_width_calendar']['phila_full_width_calendar_url'] : '';?>

              <?php if ( !empty( $cal_id ) ):?>
                <!-- Full Width Calendar -->
                <section class="expanded">
                  <div class="row">
                    <div class="columns">
                      <h2 id="events" class="mbn">Events</h2>
                    </div>
                  </div>
                  <div class="row calendar-row">
                    <div class="columns">
                      <?php echo do_shortcode('[calendar id="' . $cal_id . '"]'); ?>
                    </div>
                  </div>
                  <?php if ( !empty( $cal_url ) ):?>
                    <div class="row">
                      <div class="columns">
                        <?php $see_all = array(
                          'URL' => $cal_url,
                          'content_type' => 'events',
                          'nice_name' => 'events'
                        ); ?>
                        <?php include( locate_template( 'partials/content-see-all.php' ) ); ?>
                      </div>
                    </div>
                  <?php endif; ?>
                </section>
              <?php endif;?>
            <?php elseif ($current_row_option == 'phila_feature_p_i'): ?>
              <?php
                $featured = $current_row['phila_full_options']['phila_feature_p_i']['phila_p_i']['phila_p_i_items'];
                if ( !empty( $featured ) ):
                  include(locate_template('partials/departments/content-featured-pages.php'));
                endif;
              ?>
            <?php elseif ($current_row_option == 'phila_get_involved'): ?>
              <?php if ( !isset( $current_row['phila_full_options']['phila_call_to_action_multi']['phila_call_to_action_section'] ) ):
                $phila_dept_homepage_cta = $current_row['phila_full_options']['phila_call_to_action_multi']['phila_call_to_action_section'];
                  include(locate_template('partials/departments/content-get-involved.php'));
                endif; ?>
            <?php elseif ( $current_row_option == 'phila_full_width_press_releases'): ?>
              <!-- Press Releases -->
                <div class="row mvl">
                  <?php echo do_shortcode('[press-releases posts=5]');?>
                </div>
            <?php elseif ($current_row_option == 'phila_resource_list'): ?>
              <!-- Display Multi Call to Action as Resource List -->
              <?php if ( isset( $current_row['phila_full_options']['phila_call_to_action_multi']['phila_call_to_action_section'] ) ):
                $phila_dept_homepage_cta = $current_row['phila_full_options']['phila_call_to_action_multi']['phila_call_to_action_section'];
                include(locate_template('partials/departments/content-call-to-action-multi.php'));
              endif; ?>

            <?php elseif ( $current_row_option == 'phila_custom_text'): ?>
              <?php if ( isset( $current_row['phila_full_options']['phila_custom_text'] ) ):
                  $custom_text = $current_row['phila_full_options']['phila_custom_text']; ?>
                  <!-- Display Custom Textarea -->
                  <section class="row mvl">
                    <div class="large-24 columns">
                      <?php include(locate_template('partials/departments/content-custom-text.php'));?>
                    </div>
                  </section>
              <?php endif; ?>

          <?php endif; ?>
        <?php elseif ( ( isset( $current_row['phila_grid_options'] ) && $current_row['phila_grid_options'] == 'phila_grid_options_thirds') && ( isset( $current_row['phila_two_thirds_options']['phila_two_thirds_col'] ) && isset( $current_row['phila_two_thirds_options']['phila_one_third_col'] ) ) ):

          $current_row_option_one = $current_row['phila_two_thirds_options'] ['phila_two_thirds_col'];

          $current_row_option_two = $current_row['phila_two_thirds_options'] ['phila_one_third_col']; ?>

          <section class="row mvl">

          <?php if ( $current_row_option_one['phila_two_thirds_col_option'] == 'phila_blog_posts'): ?>
            <!-- Blog Content 2-->
            <div class="large-16 columns">
              <div class="row">
                <?php include(locate_template('partials/departments/phila_full_row_blog.php'));?>
              </div>
            </div>

          <?php elseif ( $current_row_option_one['phila_two_thirds_col_option'] == 'phila_custom_text'):?>
            <?php if ( isset( $current_row_option_one['phila_custom_text'] ) ):
              $custom_text = $current_row_option_one['phila_custom_text']; ?>
              <div class="large-16 columns">
                <?php include(locate_template('partials/departments/content-custom-text.php'));?>
              </div>
            <?php endif;?>

          <?php endif;?>

          <?php if ( $current_row_option_two['phila_one_third_col_option'] == 'phila_connect_panel'):?>
              <?php if ( isset( $current_row_option_two['phila_connect_panel'] ) ):
                $connect_panel = $current_row_option_two['phila_connect_panel'];
                $connect_vars = phila_connect_panel($connect_panel);
                include(locate_template('partials/departments/content-connect.php'));
              endif; ?>
            <?php endif; ?>

            <?php if ( isset( $current_row_option_two['phila_custom_text'] ) ):

              $custom_text = $current_row_option_two['phila_custom_text']; ?>

              <div class="large-8 columns">

                <?php include(locate_template('partials/departments/content-custom-text.php'));?>
              </div>
          <?php endif;?>

          </section>
        <?php endif; ?>
      <!-- Grid Row -->
      <?php endforeach; ?>
</div>

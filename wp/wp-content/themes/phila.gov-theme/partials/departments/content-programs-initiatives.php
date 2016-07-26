<?php
/**
 * The template used for displaying Program and Initiative pages
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

          if ( $current_row_option == 'phila_blog_posts'):
            $blog_category = isset( $current_row['phila_full_options']['phila_blog_options']['phila_category'] ) ? $current_row['phila_full_options']['phila_blog_options']['phila_category'] : '';
        ?>
            <!-- Blog Content -->
            <div class="large-24 columns">
              <div class="row">
                <?php echo do_shortcode('[recent-posts posts="3" category="' . $blog_category . '"]'); ?>
              </div>
            </div>

          <?php elseif ( $current_row_option == 'phila_full_width_calendar'): ?>
            <!-- Full Width Calendar -->
            <?php
              //TODO: verify that these vars exist before setting... offload to function?
              $cal_id = $current_row['phila_full_options']['phila_full_width_calendar']['phila_full_width_calendar_id'];
              $cal_url = $current_row['phila_full_options']['phila_full_width_calendar']['phila_full_width_calendar_url'];
            ?>
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

          <?php elseif ( $current_row_option == 'phila_callout'): ?>
            <!-- Display Callout -->
            <?php
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
            <!-- Display Custom Text -->
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
          <?php elseif ( $current_row_option == 'phila_list_items'): ?>
            <?php
              $list_items = isset( $current_row['phila_full_options']['phila_list_items'] ) ? $current_row['phila_full_options']['phila_list_items'] : '';
              include(locate_template('partials/departments/content-list-items.php'));
            ?>
          <?php endif; ?>

      <?php elseif ( isset( $current_row['phila_grid_options'] ) && $current_row['phila_grid_options'] == 'phila_grid_options_half'):

        // Begin 1/2 x 1/2 row
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
              <?php $pullquote = $current_row_option_two['phila_pullquote'];?>
              <div class="large-12 columns">
                <?php echo do_shortcode('[pullquote quote="' . $pullquote['phila_quote'] . '" attribution="' . $pullquote['phila_attribution'] . '" inline=false]'); ?>
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
              <div class="large-12 columns">
                <?php echo do_shortcode('[pullquote quote="' . $pullquote['phila_quote'] . '" attribution="' . $pullquote['phila_attribution'] . '" inline=false]'); ?>
              </div>
            <?php endif; ?>



          </div>
        </section>
      <?php elseif ( isset( $current_row['phila_grid_options'] ) && $current_row['phila_grid_options'] == 'phila_grid_options_thirds' ):

        // Begin 2/3 x 1/3 row
        $current_row_option_one = $current_row['phila_two_thirds_options']['phila_two_thirds_col'];
        $current_row_option_two = $current_row['phila_two_thirds_options']['phila_one_third_col']; ?>

        <section class="mvl">
          <div class="row">
            <?php
              if ( $current_row_option_one['phila_two_thirds_col_option'] == 'phila_blog_posts'):
                $blog_category = isset( $current_row_option_one['phila_blog_options']['phila_category'] ) ? $current_row_option_one['phila_blog_options']['phila_category'] : '';
              ?>
              <!-- Blog Content -->
              <div class="large-18 columns">
                <div class="row">
                  <?php echo do_shortcode('[recent-posts posts="3" category="' . $blog_category . '"]'); ?>
                </div>
              </div>
            <?php elseif ( $current_row_option_one['phila_two_thirds_col_option'] == 'phila_custom_text'):?>
              <!-- Custom Text -->
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
            <?php elseif ( $current_row_option_one['phila_two_thirds_col_option'] == 'phila_custom_text_multi'):?>
              <?php
              // TODO: Move this block to partials/content-custom-text-multi.php
                $custom_text = $current_row_option_one['phila_custom_text_multi'];
                $custom_text_title = $custom_text['phila_custom_row_title'];
                $custom_text_group = $custom_text['phila_custom_text_group'];
              ?>
              <div class="large-18 columns custom-text-multi">
                <h2 class="contrast"><?php echo($custom_text['phila_custom_row_title']); ?></h2>
              <?php if ( is_array( $custom_text_group ) ):?>
                <?php $item_count = count($custom_text_group); ?>
                <?php $columns = phila_grid_column_counter( $item_count ); ?>
                <div class="row <?php if( $item_count > 1 ) echo 'equal-height';?> ">
                  <?php foreach ($custom_text_group as $key => $value):?>
                    <div class="large-<?php echo $columns ?> columns">

                      <?php if ( isset( $custom_text_group[$key]['phila_custom_text_title'] ) && $custom_text_group[$key]['phila_custom_text_title'] != '') : ?>
                        <h3><?php echo $custom_text_group[$key]['phila_custom_text_title']; ?></h3>
                      <?php endif;?>

                      <?php if ( isset( $custom_text_group[$key]['phila_custom_text_content'] ) && $custom_text_group[$key]['phila_custom_text_content'] != '') : ?>
                        <p><?php echo $custom_text_group[$key]['phila_custom_text_content']; ?></p>
                      <?php else :?>
                        <div class="placeholder">
                          Please enter content.
                        </div>
                      <?php endif;?>

                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
            <?php endif;?>

              <?php
                if ( $current_row_option_two['phila_one_third_col_option'] == 'phila_connect_panel'):
                $connect_panel = $current_row_option_two['phila_connect_panel'];

                // Set Connect Panel vars
                $connect_vars = phila_connect_panel($connect_panel);
                include(locate_template('partials/departments/content-connect.php'));

                elseif ( $current_row_option_two['phila_one_third_col_option'] == 'phila_custom_feature'):
                  $feature_panel = isset( $current_row_option_two['phila_custom_feature'] ) ? $current_row_option_two['phila_custom_feature'] : '';
                  $feature_title = isset( $feature_panel['phila_feature_title'] ) ? $feature_panel['phila_feature_title'] : '';
                  $feature_image = isset( $feature_panel['phila_feature_image'] ) ? $feature_panel['phila_feature_image'] : '';
                  $feature_text = isset( $feature_panel['phila_feature_text'] ) ? $feature_panel['phila_feature_text'] : '';
                  $feature_url = isset( $feature_panel['phila_feature_url'] ) ? $feature_panel['phila_feature_url'] : '';
              ?>
              <div class="large-6 columns">
                <h2 class="contrast"><?php echo $feature_title;?></h2>
                <?php if( $feature_url != '' ): ?>
                <a href="<?php echo $feature_url;?>" class="card action-panel">
                <?php endif; ?>
                  <div class="panel">
                    <img src="<?php echo $feature_image;?>" alt="" class="mbm">
                    <span class="details"><?php echo $feature_text;?></span>
                  </div>
                <?php if( $feature_url != '' ): ?>
                </a>
                <?php endif; ?>
              </div>
              <?php endif; ?>
          </div>
        </section>
      <?php endif; ?>
    <!-- Grid Row -->
    <?php endforeach; ?>
</div>

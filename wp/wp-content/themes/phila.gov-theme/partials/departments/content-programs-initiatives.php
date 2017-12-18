<?php
/**
 * The template used for displaying Program and Initiative pages
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
    <?php if ( ( isset( $current_row['phila_grid_options'] ) && $current_row['phila_grid_options'] == 'phila_grid_options_full' ) &&
    isset( $current_row['phila_full_options']['phila_full_options_select'] ) ):

      // Begin full width row
      $current_row_option = $current_row['phila_full_options']['phila_full_options_select'];

      if ( $current_row_option == 'phila_blog_posts'):?>

        <!-- Blog Content -->
        <div class="mvl">
          <?php $blog_cat_override = isset( $current_row['phila_full_options']['phila_get_post_cats']) ? $current_row['phila_full_options']['phila_get_post_cats'] : ''; ?>
          <?php include( locate_template( 'partials/departments/phila_full_row_blog.php' ) ); ?>
        </div>
        <?php elseif ( $current_row_option == 'phila_full_width_calendar'):
          $cal_id = isset( $current_row['phila_full_options']['phila_full_width_calendar']['phila_full_width_calendar_id'] ) ? $current_row['phila_full_options']['phila_full_width_calendar']['phila_full_width_calendar_id'] : '';
          $cal_url = isset( $current_row['phila_full_options']['phila_full_width_calendar']['phila_full_width_calendar_url'] ) ? $current_row['phila_full_options']['phila_full_width_calendar']['phila_full_width_calendar_url'] : ''; ?>

          <?php if ( !empty( $cal_id ) ):?>
            <!-- Full Width Calendar -->
            <section class="expanded">
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

        <?php elseif ( $current_row_option == 'phila_callout'):
          $callout_type = isset( $current_row['phila_full_options']['phila_callout']['phila_callout_type'] ) ? $current_row['phila_full_options']['phila_callout']['phila_callout_type'] : '';
          $callout_text = isset( $current_row['phila_full_options']['phila_callout']['phila_callout_text'] ) ? $current_row['phila_full_options']['phila_callout']['phila_callout_text'] : ''; ?>

          <?php if ( !empty( $callout_text ) ): ?>
            <!-- Display Callout -->
            <section class="row mvm">
              <div class="large-24 column">
                  <?php echo do_shortcode('[callout type="' . $callout_type . '" inline="false"]' . $callout_text . '[/callout]'); ?>
              </div>
            </section>
          <?php endif;?>

        <?php elseif ($current_row_option == 'phila_get_involved'): ?>
          <?php if ( isset( $current_row['phila_full_options']['phila_call_to_action_multi']['phila_call_to_action_section'] ) ):
            $phila_dept_homepage_cta = $current_row['phila_full_options']['phila_call_to_action_multi']['phila_call_to_action_section'];
              include(locate_template('partials/departments/content-get-involved.php'));
          endif; ?>

        <?php elseif ( $current_row_option == 'phila_full_width_press_releases'): ?>
          <!-- Press Releases -->
            <section class="row mvl">
              <?php echo do_shortcode('[press-releases posts=5]');?>
            </section>
            <?php $see_all = array(
              'URL' => '/press-releases/' . $category_slug ,
              'content_type' => 'press_release',
              'nice_name' => 'Press releases'
            ); ?>
            <?php include( locate_template( 'partials/content-see-all.php' ) ); ?>

          <?php elseif ($current_row_option == 'phila_resource_list'): ?>
            <?php if ( isset( $current_row['phila_full_options']['phila_call_to_action_multi']['phila_call_to_action_section'] ) ):
                $phila_dept_homepage_cta = $current_row['phila_full_options']['phila_call_to_action_multi']['phila_call_to_action_section'];
                include(locate_template('partials/departments/phila_call_to_action_multi.php'));
            endif; ?>

          <?php elseif ( $current_row_option == 'phila_custom_text'): ?>
            <?php if ( isset( $current_row['phila_full_options']['phila_custom_text'] ) ):
              $custom_text = $current_row['phila_full_options']['phila_custom_text'];?>
              <!-- Display Custom Text -->
              <section class="row mvl">
                <div class="large-24 column">
                  <?php include(locate_template('partials/departments/content-custom-text.php'));?>
                </div>
              </section>

            <?php endif; ?>
          <?php elseif ( $current_row_option == 'phila_list_items'): ?>
            <?php
              $list_items = isset( $current_row['phila_full_options']['phila_list_items'] ) ? $current_row['phila_full_options']['phila_list_items'] : '';
              include(locate_template('partials/departments/content-list-items.php'));
            ?>
        <?php elseif ( $current_row_option == 'phila_full_cta'): ?>
          <?php if ( isset( $current_row['phila_full_options']['phila_full_width_cta'] ) ):
            $cta = $current_row['phila_full_options']['phila_full_width_cta'];

            $link = phila_cta_full_display( $cta );

           include(locate_template('partials/departments/v2/full-width-call-to-action.php')); ?>
         <?php endif; ?>
      <?php endif;  /*end full row */?>

      <?php elseif ( ( isset( $current_row['phila_grid_options'] ) && $current_row['phila_grid_options'] == 'phila_grid_options_half') && ( isset( $current_row['phila_half_options']['phila_half_col_1'] ) && isset( $current_row['phila_half_options']['phila_half_col_2'] ) ) ):

        // Begin 1/2 x 1/2 row
        $current_row_option_one = $current_row['phila_half_options']['phila_half_col_1'];
        $current_row_option_two = $current_row['phila_half_options']['phila_half_col_2']; ?>

        <section class="row mvl">
            <?php if ( $current_row_option_one['phila_half_col_1_option'] == 'phila_custom_text'):?>

              <?php if ( isset( $current_row_option_one['phila_custom_text'] ) ):
                $custom_text = $current_row_option_one['phila_custom_text']; ?>
                <div class="large-12 columns">
                  <?php include(locate_template('partials/departments/content-custom-text.php'));?>
                </div>
              <?php endif;?>

            <?php elseif ( $current_row_option_one['phila_half_col_1_option'] == 'phila_pullquote'):
              $pullquote = isset ($current_row_option_one['phila_pullquote'] ) ? $current_row_option_one['phila_pullquote'] : '';
              $quote = isset( $pullquote['phila_quote'] ) ? $pullquote['phila_quote'] : '';
              $attribution = isset( $pullquote['phila_attribution'] ) ? $pullquote['phila_attribution'] : '';

              if ( !empty( $quote ) ): ?>
                <div class="large-12 columns">
                  <?php echo do_shortcode('[pullquote quote="' . $quote . '" attribution="' . $attribution . '" inline="false"]'); ?>
                </div>
              <?php endif; ?>
            <?php endif; ?>

            <?php if ( $current_row_option_two['phila_half_col_2_option'] == 'phila_custom_text'):?>
                <?php if ( isset( $current_row_option_two['phila_custom_text'] ) ):
                  $custom_text = $current_row_option_two['phila_custom_text'];
                  var_dump($custom_text); ?>
                  <div class="large-12 columns">
                    <?php include(locate_template('partials/departments/content-custom-text.php'));?>
                  </div>
                <?php endif;?>

            <?php elseif ( $current_row_option_two['phila_half_col_2_option'] == 'phila_pullquote'):
              $pullquote = isset ($current_row_option_two['phila_pullquote'] ) ? $current_row_option_two['phila_pullquote'] : '';
              $quote = isset( $pullquote['phila_quote'] ) ? $pullquote['phila_quote'] : '';
              $attribution = isset( $pullquote['phila_attribution'] ) ? $pullquote['phila_attribution'] : '';

              if ( !empty( $quote ) ): ?>
                <div class="large-12 columns">
                  <?php echo do_shortcode('[pullquote quote="' . $quote . '" attribution="' . $attribution . '" inline="false"]'); ?>
                </div>
              <?php endif; ?>
            <?php endif; ?>

        </section>
      <?php elseif ( (isset( $current_row['phila_grid_options'] ) && $current_row['phila_grid_options'] == 'phila_grid_options_thirds' ) && ( isset($current_row['phila_two_thirds_options']['phila_two_thirds_col'] ) && isset( $current_row['phila_two_thirds_options']['phila_one_third_col'] ) ) ):

        // Begin 2/3 x 1/3 row
        $current_row_option_one = $current_row['phila_two_thirds_options']['phila_two_thirds_col'];
        $current_row_option_two = $current_row['phila_two_thirds_options']['phila_one_third_col']; ?>

        <section class="row mvl">
            <?php
              if ( $current_row_option_one['phila_two_thirds_col_option'] == 'phila_blog_posts'):
                $blog_cat_override = isset( $current_row['phila_two_thirds_col_option']['phila_get_post_cats']) ? $current_row['phila_two_thirds_col_option']['phila_get_post_cats'] : '';
                include(locate_template('partials/departments/phila_full_row_blog.php'));?>
            <?php elseif ( $current_row_option_one['phila_two_thirds_col_option'] == 'phila_custom_text'):?>
                <?php if ( isset( $current_row_option_one['phila_custom_text'] ) ):
                  $custom_text = $current_row_option_one['phila_custom_text']; ?>
                  <div class="large-16 columns">
                    <?php include(locate_template('partials/departments/content-custom-text.php'));?>
                  </div>
                <?php endif;?>

            <?php elseif ( $current_row_option_one['phila_two_thirds_col_option'] == 'phila_custom_text_multi'):?>
              <?php if ( isset( $current_row_option_one['phila_custom_text_multi'] ) ):
                $custom_text = $current_row_option_one['phila_custom_text_multi'];
                include(locate_template('partials/departments/content-custom-text-multi.php'));
              endif; ?>

            <?php endif;?>

            <?php if ( $current_row_option_two['phila_one_third_col_option'] == 'phila_connect_panel'):?>
                <?php if ( isset( $current_row_option_two['phila_connect_panel'] ) ):

                  $connect_panel = $current_row_option_two['phila_connect_panel'];

                  $connect_vars = phila_connect_panel($connect_panel);

                  include(locate_template('partials/departments/v2/content-connect.php'));

                endif; ?>

            <?php elseif ( $current_row_option_two['phila_one_third_col_option'] == 'phila_custom_text'):?>
                <?php if ( isset( $current_row_option_two['phila_custom_text'] ) ):
                  $custom_text = $current_row_option_two['phila_custom_text']; ?>
                  <div class="large-8 columns">
                    <?php include(locate_template('partials/departments/content-custom-text.php'));?>
                  </div>
              <?php endif;?>

            <?php elseif ( $current_row_option_two['phila_one_third_col_option'] == 'phila_custom_feature'):?>
              <?php if ( isset( $current_row_option_two['phila_custom_feature'] ) ):
                $feature_panel = $current_row_option_two['phila_custom_feature'];
                $feature_title = isset( $feature_panel['phila_feature_title'] ) ? $feature_panel['phila_feature_title'] : '';
                $feature_image = isset( $feature_panel['phila_feature_image'] ) ? $feature_panel['phila_feature_image'] : '';
                $feature_text = isset( $feature_panel['phila_feature_text'] ) ? $feature_panel['phila_feature_text'] : '';
                $feature_url = isset( $feature_panel['phila_feature_url'] ) ? $feature_panel['phila_feature_url'] : '';
                $feature_url_text = isset( $feature_panel['phila_feature_url_text'] ) ? $feature_panel['phila_feature_url_text'] : '';
              ?>
              <div class="large-8 columns">
                <h2 class="contrast"><?php echo $feature_title;?></h2>
                <?php if( $feature_url != '' ): ?>
                <a href="<?php echo $feature_url;?>" class="card action-panel mbn">
                <?php endif; ?>
                <?php if( $feature_image != '' ): ?>
                  <img src="<?php echo $feature_image;?>" alt="">
                <?php endif; ?>
                  <div class="panel">
                    <?php if( $feature_url_text != '' ): ?>
                      <header class="">
                        <span class="external"><?php echo $feature_url_text;?></span>
                      </header>
                      <hr class="mll mrl">
                    <?php endif; ?>
                    <span class="details"><?php echo $feature_text;?></span>
                  </div>
                <?php if( $feature_url != '' ): ?>
                </a>
                <?php endif; ?>
              </div>
              <?php endif; ?>
            <?php endif; ?>
        </section>
      <?php endif; ?>
    <!-- Grid Row -->
    <?php endforeach; ?>
</div>

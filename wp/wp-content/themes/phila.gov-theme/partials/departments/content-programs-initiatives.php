<?php
/**
 * The template used for displaying Program and Initiative pages
 *
 * @package phila-gov
 */
?>
<?php
  // set category vars for blogs/staff
  $category = get_the_category();
  $category_slug = !phila_util_is_array_empty($category) ? $category[0]->slug : '';

  // MetaBox variables
  $page_rows = rwmb_meta('phila_row');
?>
<?php if (!phila_util_is_array_empty($page_rows)): ?>
  <!-- Program and initiatives -->
  <section>
  <?php
    foreach ($page_rows as $key => $value):
      $current_row = $page_rows[$key];?>
      <?php if ( ( isset( $current_row['phila_grid_options'] ) && $current_row['phila_grid_options'] == 'phila_grid_options_full' ) &&
      isset( $current_row['phila_full_options']['phila_full_options_select'] ) ):

        // Begin full width row
        $current_row_option = $current_row['phila_full_options']['phila_full_options_select'];

        if ( $current_row_option == 'phila_blog_posts'):?>
          <!-- Blog Content -->
          <div class="mvl">
            <?php $blog_cat_override = isset( $current_row['phila_full_options']['phila_get_post_cats']['phila_post_category']) ? $current_row['phila_full_options']['phila_get_post_cats']['phila_post_category'] : '';
            ?>
            <?php $blog_tag_override = isset( $current_row['phila_full_options']['phila_get_post_cats']['tag']) ? $current_row['phila_full_options']['phila_get_post_cats']['tag'] : '';
            echo 'tag test';
            var_dump($blog_tag_override);
            echo 'tag test override';
            ?>
            <?php $blog_see_all = isset( $current_row['phila_full_options']['phila_get_post_cats']['override_url']) ? $current_row['phila_full_options']['phila_get_post_cats']['override_url'] : ''; ?>
            <?php include( locate_template( 'partials/departments/phila_full_row_blog.php' ) ); ?>
          </div>
          <!-- /Blog Content -->

          <?php elseif ( $current_row_option == 'phila_full_width_calendar'):
            $cal_id = isset( $current_row['phila_full_options']['phila_full_width_calendar']['phila_full_width_calendar_id'] ) ? $current_row['phila_full_options']['phila_full_width_calendar']['phila_full_width_calendar_id'] : '';

            $cal_category = isset( $current_row['phila_full_options']['phila_full_width_calendar']['phila_calendar_owner'] ) ? $current_row['phila_full_options']['phila_full_width_calendar']['phila_calendar_owner'] : ''; ?>

            <?php $calendar_see_all = isset( $current_row['phila_full_options']['phila_full_width_calendar']['override_url'] ) ? $current_row['phila_full_options']['phila_full_width_calendar']['override_url'] : ''; ?>
            <!-- Calendar -->
            <?php include( locate_template( 'partials/departments/v2/calendar.php' ) ); ?>
            <!-- /Calendar -->

          <?php elseif ( $current_row_option == 'phila_callout'):
            $callout_text = isset( $current_row['phila_full_options']['phila_callout']['phila_callout_text'] ) ? $current_row['phila_full_options']['phila_callout']['phila_callout_text'] : ''; ?>

            <?php if ( !empty( $callout_text ) ): ?>
              <!-- Callout -->
              <section class="row mvxl">
                <div class="large-24 column">
                    <?php echo do_shortcode('[callout type="default" inline="false"]' . $callout_text . '[/callout]'); ?>
                </div>
              </section>
              <!-- /Callout -->
            <?php endif;?>

          <?php elseif ($current_row_option == 'phila_get_involved'): ?>
            <?php if ( isset( $current_row['phila_full_options']['phila_call_to_action_multi']['phila_call_to_action_section'] ) ): ?>
              <!-- Get involved -->
              <?php $phila_dept_homepage_cta = $current_row['phila_full_options']['phila_call_to_action_multi']['phila_call_to_action_section'];
                include(locate_template('partials/departments/content-get-involved.php'));
              endif; ?>
            <!-- /Get involved -->
          <?php elseif ( $current_row_option == 'phila_full_width_press_releases'): ?>
            <?php $press_cat_override = isset( $current_row['phila_full_options']['full_width_press_releases']['phila_press_release_category']) ? $current_row['phila_full_options']['full_width_press_releases']['phila_press_release_category'][0] : '';
            ?>
            <?php $press_tag_override = isset( $current_row['phila_full_options']['full_width_press_releases']['tag']) ? $current_row['phila_full_options']['full_width_press_releases']['tag'] : '';
            ?>
            <?php $blog_see_all = isset( $current_row['phila_full_options']['full_width_press_releases']['override_url']) ? $current_row['phila_full_options']['full_width_press_releases']['override_url'] : ''; ?>
              <!-- Press Releases -->
              <section class="row mvl">
                <?php echo do_shortcode('[press-releases posts=5 see_all="' . $blog_see_all . '" tag="'. $press_tag_override .'" category="' . $press_cat_override . '"]');?>
              </section>
              <!-- /Press Releases -->
            <?php elseif ($current_row_option == 'phila_resource_list'): ?>
              <?php if ( isset( $current_row['phila_full_options']['phila_call_to_action_multi']['phila_call_to_action_section'] ) ):
                  $phila_dept_homepage_cta = $current_row['phila_full_options']['phila_call_to_action_multi']['phila_call_to_action_section'];
                  ?>
                  <!-- Call to action multi -->
                  <div class="mvl"><?php

                  include(locate_template('partials/departments/phila_call_to_action_multi.php')); ?>
                </div>
                <!-- /Call to action multi -->
                <?php endif; ?>

            <?php elseif ( $current_row_option == 'phila_custom_text'): ?>
              <?php if ( isset( $current_row['phila_full_options']['phila_custom_text'] ) ):
                $custom_text = $current_row['phila_full_options']['phila_custom_text'];?>
                <!-- Custom Text -->
                <section class="row mvl">
                  <div class="large-24 column">
                    <?php include(locate_template('partials/departments/content-custom-text.php'));?>
                  </div>
                </section>
                <!-- /Custom Text -->
              <?php endif; ?>

            <?php elseif ( $current_row_option == 'phila_list_items'): ?>
              <?php
                $list_items = isset( $current_row['phila_full_options']['phila_list_items'] ) ? $current_row['phila_full_options']['phila_list_items'] : '';
                ?>
                <!-- List items -->
                <?php include(locate_template('partials/departments/content-list-items.php')); ?>
              <!-- /List items -->

          <?php elseif ( $current_row_option == 'phila_full_cta'): ?>
            <?php if ( isset( $current_row['phila_full_options']['phila_full_width_cta'] ) ):
              $cta = $current_row['phila_full_options']['phila_full_width_cta'];

              $link = phila_cta_full_display( $cta ); ?>
              <!-- Full width Call to Action-->
              <div class="mvl">
                <?php include(locate_template('partials/departments/v2/full-width-call-to-action.php')); ?>
              </div>
              <!-- /Full width Call to Action-->

          <?php endif; ?>

        <?php elseif ( $current_row_option == 'phila_custom_text_multi'):?>
          <?php if ( isset( $current_row['phila_full_options']['phila_custom_text_multi_full'] ) ):
            $custom_text = $current_row['phila_full_options']['phila_custom_text_multi_full'];
            $multi_full_row = true;
            ?>
            <!-- Custom text multi-->
              <?php include(locate_template('partials/departments/content-custom-text-multi.php')); ?>
            <!-- /Custom text multi -->

          <?php endif; ?>

          <?php elseif ( $current_row_option == 'phila_image_list'):?>

            <?php if ( isset( $current_row['phila_full_options']['phila_image_list'] ) ):

              $image_list = $current_row['phila_full_options']['phila_image_list'];

              $image_list_vars = phila_image_list($image_list);
              ?>
              <!-- Image List-->
              <?php include(locate_template('partials/programs/image-list.php')); ?>
              <!-- /Image List-->

            <?php endif; ?>

          <?php elseif ( $current_row_option == 'phila_registration'):?>
            <?php if ( isset( $current_row['phila_full_options']['phila_registration'] ) ):

              $registration = $current_row['phila_full_options']['phila_registration']; ?>
              <!--Registration-->
              <?php include(locate_template('partials/global/registration.php')); ?>
              <!--/Registration-->

        <?php endif; ?>
        
        <?php elseif ( $current_row_option == 'phila_vue_app'): ?>
          <?php if ( isset( $current_row['phila_full_options']['phila_vue_template'] ) ):

            $app_id = $current_row['phila_full_options']['phila_vue_template']['phila_vue_app_id'];

            $vuejs_js_ids = $current_row['phila_full_options']['phila_vue_template']['phila-vue-app-js'];
            $vuejs_css_ids = $current_row['phila_full_options']['phila_vue_template']['phila-vue-app-css'];

            $app_title = $current_row['phila_full_options']['vue_app_title'];
              if (is_array($vuejs_js_ids)) {
                $count = 1;
                foreach($vuejs_js_ids as $url) {
                  $handle = $post->post_name . '-vue-app-js-url-' . $count . $app_id;
                  wp_enqueue_script($handle, $url['phila_vue_app_js_url'], array(), null, true );
                  $count++;
                }
              }

              if (is_array($vuejs_css_ids)) {
                $count = 1;
                foreach($vuejs_css_ids as $url) {
                  $handle = $post->post_name . '-vue-app-css-url-' . $count . $app_id;
                  wp_enqueue_style($handle, $url['phila_vue_app_css_url']);
                  $count++;
                }
              }
              
              ?>
            <!--Vuejs-->
            <?php if (!empty($app_title) ): ?>
              <div class="grid-container">
                <div class="grid-x">
                  <div class="cell small-24">
                    <h2 class="contrast"><?php echo $app_title ?> </h2>
                    </div>
                </div>
              </div>
            <?php endif; ?>
            <div class="grid-container">
              <div class="grid-x">
                <div class="cell small-24">
                <div id="<?php echo empty($app_id) ? 'vue-app' : $app_id ?>"></div>
                </div>
              </div>
            </div>
            <!--/Vuejs-->

        <?php endif; ?>
        
          <?php elseif ( $current_row_option == 'phila_stepped_content'):?>
            <?php if ( isset( $current_row['phila_full_options']['phila_full_options_select'] ) ): ?>
              <!-- Heading groups -->
              <div class="grid-container">
                <?php
                $heading_groups = $current_row['phila_full_options']['phila_heading_groups'];
                include(locate_template('partials/content-heading-groups.php')); ?>
              </div>
              <!-- /Heading groups -->

            <?php endif;?>

          <?php elseif ( $current_row_option == 'phila_location_list'):?>
            <?php if ( isset( $current_row['phila_full_options']['phila_location_list'] ) ): ?>
              <!-- Location list -->
              <?php
              $location_list = $current_row['phila_full_options']['phila_location_list'];
              $location_list_title = $current_row['phila_full_options']['phila_location_list']['row_title'];
              include(locate_template('partials/programs/location-list.php')); ?>
              <!-- /Location list -->
            <?php endif;?>

          <?php elseif ( $current_row_option == 'phila_programs'):?>
            <?php if ( isset( $current_row['phila_full_options']['phila_programs'] ) ): ?>
              <?php
              $cards = $current_row['phila_full_options']['phila_programs']['phila_select_programs'];?>
              <!-- Program cards-->
              <?php include(locate_template('partials/departments/v2/homepage_programs.php')); ?>
              <!-- Program cards-->
            <?php endif;?>

          <?php elseif ( $current_row_option == 'phila_board_commission'):?>
            <?php if ( isset( $current_row['phila_full_options']['commission_members'] ) ): ?>
              <?php
              $section_title = $current_row['phila_full_options']['commission_members']['section_title'];
              $table_cell_title = $current_row['phila_full_options']['commission_members']['table_head_title'];
              $members = $current_row['phila_full_options']['commission_members']['phila_commission_members'];
              ?>
              <!-- Boards/Commission Members -->
              <?php include(locate_template('partials/departments/v2/board_commission_member_list.php')); ?>
              <!-- /Boards/Commission Members -->
            <?php endif;?>
            <?php elseif ( $current_row_option == 'phila_staff_table'):?>
            <?php if ( isset( $current_row['phila_full_options']['phila_staff_directory_listing'] ) ): ?>
              <!-- Staff listing -->
              <?php include(locate_template('partials/departments/phila_staff_directory_listing.php')); ?>
              <!-- /Staff listing -->
            <?php endif;?>

            <?php elseif ( $current_row_option == 'phila_photo_callout'):?>
            <?php if ( isset( $current_row['phila_full_options']['photo_callout'] ) ): ?>
              <!-- Photo call out -->
              <?php include(locate_template('partials/departments/v2/photo_callout.php')); ?>
              <!-- /Photo call out -->
            <?php endif;?>

            <?php elseif ( $current_row_option == 'phila_faq'):?>
            <?php if ( isset( $current_row['phila_full_options']['faq'] ) ): ?>
              <!-- FAQ -->
              <?php include(locate_template('partials/departments/v2/faq.php')); ?>
              <!-- /FAQ -->
            <?php endif;?>

            <?php elseif ( $current_row_option == 'phila_content_heading_group'):
            
            $wysiwyg_heading = isset( $current_row['phila_full_options']['phila_content_heading_group']['phila_wysiwyg_heading'] ) ? $current_row['phila_full_options']['phila_content_heading_group']['phila_wysiwyg_heading'] : '';
            $wysiwyg_content = isset( $current_row['phila_full_options']['phila_content_heading_group']['phila_unique_wysiwyg_content'] ) ? $current_row['phila_full_options']['phila_content_heading_group']['phila_unique_wysiwyg_content'] : '';

            if ( !empty( $wysiwyg_heading ) || !empty( $wysiwyg_content ) ) : ?>
              <!-- Heading Group -->
              <?php include(locate_template('partials/content-single-heading-group.php')); ?>
              <!-- /Heading Group -->
            <?php endif;?>

          <?php elseif ( $current_row_option == 'phila_prereq'):
          
            $accordion_group = isset( $current_row['phila_full_options']['phila_prereq']['accordion_group'] ) ? $current_row['phila_full_options']['phila_prereq']['accordion_group'] : '';
            $requirements_prereq_title = isset( $current_row['phila_full_options']['phila_prereq']['accordion_row_title'] ) ? $current_row['phila_full_options']['phila_prereq']['accordion_row_title'] : '';

            if ( !empty( $accordion_group ) || !empty( $requirements_prereq_title ) ) : ?>
              <!-- Prereq Row -->
              <?php include(locate_template('partials/content-custom-prereq-row.php')); ?>
              <!-- /Prereq Row -->
            <?php endif;?>

          <?php elseif ( $current_row_option == 'phila_content_additional_content'):

            $additional_content = isset( $current_row['phila_full_options']['phila_content_additional_content']['phila_additional_content'] ) ? $current_row['phila_full_options']['phila_content_additional_content']['phila_additional_content'] : '';

            if ( !empty( $additional_content ) ) : ?>
              <!-- Additional Content -->
              <?php include(locate_template('partials/content-custom-additional.php')); ?>
              <!-- /Additional Content -->
            <?php endif;?>

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
                $custom_text = $current_row_option_two['phila_custom_text'];?>
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
              if ( $current_row_option_one['phila_two_thirds_col_option'] == 'phila_custom_text'):?>
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

                  include(locate_template('partials/departments/v2/content-connect.php'));

                endif; ?>

            <?php elseif ( $current_row_option_two['phila_one_third_col_option'] == 'phila_custom_text'):?>
                <?php if ( isset( $current_row_option_two['phila_custom_text'] ) ):
                  $custom_text = $current_row_option_two['phila_custom_text']; ?>
                  <div class="large-8 columns">
                    <?php include(locate_template('partials/departments/content-custom-text.php'));?>
                  </div>
              <?php endif;?>

            <?php endif; ?>
        </section>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
<!-- /Program and initiatives -->
<?php endif; ?>

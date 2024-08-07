<?php
/**
 * The template used for displaying the full width row content 
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
<!-- Page content -->
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
            <?php $blog_cat_override = isset( $current_row['phila_full_options']['phila_get_post_cats']['phila_post_category']) ? $current_row['phila_full_options']['phila_get_post_cats']['phila_post_category'] : ''; ?>
            <?php $blog_tag_override = isset( $current_row['phila_full_options']['phila_get_post_cats']['tag']) ? $current_row['phila_full_options']['phila_get_post_cats']['tag'] : ''; ?>
            <?php $blog_see_all = isset( $current_row['phila_full_options']['phila_get_post_cats']['override_url']) ? $current_row['phila_full_options']['phila_get_post_cats']['override_url'] : ''; ?>
            <?php include( locate_template( 'partials/departments/phila_full_row_blog.php' ) ); ?>
          </div>
          <!-- /Blog Content -->

          <?php elseif ( $current_row_option === 'phila_announcements' ): ?>
          <!-- Announcement Content -->
          <?php $ann_cat_override = isset( $current_row['phila_full_options']['phila_announcements_group']['phila_ann_category']) ? $current_row['phila_full_options']['phila_announcements_group']['phila_ann_category'] : ''; ?>
            <?php $ann_tag_override = isset( $current_row['phila_full_options']['phila_announcements_group']['phila_ann_tag']) ? $current_row['phila_full_options']['phila_announcements_group']['phila_ann_tag'] : ''; ?>
            <?php include( locate_template( 'partials/global/phila_full_row_announcements.php' ) ); ?>
          <!-- /Announcement Content -->
          <?php elseif ( $current_row_option == 'phila_full_width_calendar'):
            $cal_id = isset( $current_row['phila_full_options']['phila_full_width_calendar']['phila_full_width_calendar_id'] ) ? $current_row['phila_full_options']['phila_full_width_calendar']['phila_full_width_calendar_id'] : null;
            $spotlight_id = isset( $current_row['phila_full_options']['phila_full_width_calendar']['phila_event_spotlight'] ) ? $current_row['phila_full_options']['phila_full_width_calendar']['phila_event_spotlight'] : '';
            $display_spotlight = isset( $current_row['phila_full_options']['phila_full_width_calendar']['phila_active_event_spotlight'] ) ? $current_row['phila_full_options']['phila_full_width_calendar']['phila_active_event_spotlight'] : null;
            $cal_owner_id = isset( $current_row['phila_full_options']['phila_full_width_calendar']['phila_calendar_owner'] ) ? $current_row['phila_full_options']['phila_full_width_calendar']['phila_calendar_owner'] : ''; 
            ?>

            <?php $calendar_see_all = isset( $current_row['phila_full_options']['phila_full_width_calendar']['override_url'] ) ? $current_row['phila_full_options']['phila_full_width_calendar']['override_url'] : ''; ?>
            <?php $owner = get_the_terms( get_the_id(), 'category' )[0]; ?>
            <?php $cal_category = get_the_category_by_ID($cal_owner_id); ?>
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

          <?php elseif ($current_row_option == 'phila_cost_callout'): 
            $cost_callout = $current_row['phila_full_options']['phila_cost_component'];
          ?>
          <div class="cost">
          <section>
            <h3 id="cost" class="black bg-ghost-gray phm-mu mtl mbm"><?php echo $cost_callout['phila_heading'] ?></h3>
              <div class="grid-x grid-margin-x">
                <?php $count = count($cost_callout['service_cost_callout']['cost_callout']) ?>
                <?php foreach ( $cost_callout['service_cost_callout']['cost_callout'] as $callout ): ?>
                  <div class="medium-<?php echo phila_grid_column_counter($count)?> cell align-self-stretch panel info">
                    <div class="center heading">
                      <div class="title pvxs"> <?php echo $callout['heading'] ?></div>
                      <span class="symbol">
                        $<span class="large-text"><?php echo $callout['amount']; ?></span>
                      </span>
                        <?php if ( isset($callout['description'] ) ) : ?>
                          <div class="pam">
                            <?php echo apply_filters( 'the_content', $callout['description']) ?>
                          </div>
                        <?php endif; ?>
                    </div>
                  </div>
                <?php endforeach; ?>
                <div class="phm-mu"><?php echo $cost_callout['phila_additional_wysiwyg'] ?></div>
                </div>
            <div class="phm-mu"><?php echo apply_filters( 'the_content', $cost) ?></div>
            <?php if ( !empty($is_modal) && !empty( $modal_link_text ) ) : ?>
              <div class="reveal reveal--announcement" id="<?php echo sanitize_title_with_dashes($modal_link_text)?>" data-reveal aria-labelledby="<?php echo sanitize_title_with_dashes($modal_link_text)?>">
                <button class="close-button" data-close aria-label="Close modal" type="button">
                  <span aria-hidden="true">&times;</span>
                </button>
                <div class="mtl"><?php echo do_shortcode($modal_content) ?></div>
              </div>
              <div class="phm-mu"><button class="link" data-open="<?php echo sanitize_title_with_dashes($modal_link_text)?>"><i class="fas fa-info-circle"></i> <?php echo $modal_link_text ?></button></div>
            <?php endif ?>
          </section>
        </div>

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

          <?php elseif ( $current_row_option == 'phila_service_updates'):?>
          <?php if ( isset( $current_row['phila_full_options']['phila_service_update_page'] ) ):
            $service_update_page = $current_row['phila_full_options']['phila_service_update_page'];
            ?>
            <!-- service update page -->
              <?php include(locate_template('partials/global/page-service-updates.php')); ?>
            <!-- /service update page -->

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

            $app_title = isset($current_row['phila_full_options']['vue_app_title']) ? $current_row['phila_full_options']['vue_app_title'] : '';
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
            <?php include(locate_template( 'partials/vue-apps/app-container.php' ) ); ?>

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

            <?php elseif ( $current_row_option == 'phila_homepage_timeline'):?>
            <!-- Homepage timeline -->
              <?php
              $timeline_page = $current_row['phila_full_options']['phila_timeline_picker'];
              $limit = $current_row['phila_full_options']['phila_timeline_picker']['homepage_timeline_item_count'];
              include(locate_template('partials/timeline_stub.php')); ?>
            <!-- /Homepage timeline -->

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
              $title = isset ($current_row['phila_full_options']['phila_programs']['phila_custom_text_title']) ? $current_row['phila_full_options']['phila_programs']['phila_custom_text_title'] : 'Our programs';
              $cards = isset($current_row['phila_full_options']['phila_programs']['phila_select_programs']) ? $current_row['phila_full_options']['phila_programs']['phila_select_programs'] : '';
              $all_programs = isset($current_row['phila_full_options']['phila_programs']['phila_v2_programs_link']) ? $current_row['phila_full_options']['phila_programs']['phila_v2_programs_link'] : '';
              ?>
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
              <?php include(locate_template('partials/departments/v2/member_list.php')); ?>
              <!-- /Boards/Commission Members -->
            <?php endif;?>
            <?php elseif ( $current_row_option == 'phila_staff_table'):?>
            <?php if ( isset( $current_row['phila_full_options']['phila_staff_directory_listing'] ) ): 
              $repeating_override = $current_row['phila_full_options']['phila_staff_directory_listing']['phila_get_staff_cats']['phila_staff_category'];
              ?>
              <!-- Staff listing -->
              <?php include(locate_template('partials/departments/phila_staff_directory_listing.php')); ?>
              <!-- /Staff listing -->
            <?php endif;?>

            <?php elseif ( $current_row_option == 'phila_photo_callout'): ?>
            <?php if ( isset( $current_row['phila_full_options']['phila_photo_callout']) || isset( $current_row['phila_full_options']['phila_full_options_select'] )): 
              ?>
              <!-- Photo call out -->
              <?php include(locate_template('partials/departments/v2/photo-callout.php')); ?>
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

            <?php elseif ( $current_row_option == 'phila_heading_one_quarter_select'):
            
            $wysiwyg_heading = isset( $current_row['phila_full_options']['phila_heading_one_quarter']['phila_custom_wysiwyg']['phila_wysiwyg_title'] ) ? $current_row['phila_full_options']['phila_heading_one_quarter']['phila_custom_wysiwyg']['phila_wysiwyg_title'] : '';
            $wysiwyg_content = isset( $current_row['phila_full_options']['phila_heading_one_quarter']['phila_custom_wysiwyg']['phila_wysiwyg_content'] ) ? $current_row['phila_full_options']['phila_heading_one_quarter']['phila_custom_wysiwyg']['phila_wysiwyg_content'] : '';

            if ( !empty( $wysiwyg_heading ) || !empty( $wysiwyg_content ) ) : ?>
              <!-- 1/4 Heading Group -->
              <?php include(locate_template('partials/content-one-quarter-simple.php')); ?>
              <!-- 1/4 Heading Group -->
            <?php endif;?>


          <?php elseif ( $current_row_option == 'phila_prereq'):
          
            $accordion_group = isset( $current_row['phila_full_options']['phila_prereq']['accordion_group'] ) ? $current_row['phila_full_options']['phila_prereq']['accordion_group'] : '';
            $requirements_prereq_title = isset( $current_row['phila_full_options']['phila_prereq']['accordion_row_title'] ) ? $current_row['phila_full_options']['phila_prereq']['accordion_row_title'] : '';  
            $override_icon = isset( $current_row['phila_full_options']['phila_prereq']['phila_v2_icon'] ) ? $current_row['phila_full_options']['phila_prereq']['phila_v2_icon'] : '';
            $use_icon = true;
            $is_full_width = false;
            if ( !empty( $accordion_group ) || !empty( $requirements_prereq_title ) ) : ?>
              <!-- Prereq Row -->
              <?php include(locate_template('partials/global/accordion.php')); ?>
              <!-- /Prereq Row -->
            <?php endif;?>

          <?php elseif ( $current_row_option == 'phila_content_additional_content'):

            $additional_content = isset( $current_row['phila_full_options']['phila_content_additional_content']['phila_additional_content'] ) ? $current_row['phila_full_options']['phila_content_additional_content']['phila_additional_content'] : '';

            if ( !empty( $additional_content ) ) : ?>
              <!-- Additional Content -->
              <?php include(locate_template('partials/content-custom-additional.php')); ?>
              <!-- /Additional Content -->
            <?php endif;?>
          <?php elseif ( $current_row_option == 'phila_modal'): 
            $phila_modal = isset( $current_row['phila_full_options']['phila_modal'] ) ? $current_row['phila_full_options']['phila_modal'] : '';

            if ( !empty( $phila_modal ) ) :  ?>
            <!-- Phila Modal -->
            <?php include(locate_template('partials/services/content-phila-modal.php')); ?>
            <!-- /Phila Modal -->
            <?php endif;?>

        <?php endif;  /*end full row */?>
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
</section>
<!-- /Page content -->
<?php endif; ?>

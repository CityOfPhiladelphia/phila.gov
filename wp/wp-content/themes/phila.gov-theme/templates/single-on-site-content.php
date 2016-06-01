<?php
/*
*
* Template part
* for displaying on-site department content
*
*/
?>
<div class="row">
  <div class="columns">
    <?php the_title( '<h2 class="sub-page-title">', '</h2>' ); ?>
  </div>
</div>

<div data-swiftype-index='true' class="entry-content">
  <?php if (function_exists('rwmb_meta')): ?>
    <?php // Set custom markup vars
          $append_before_wysiwyg = rwmb_meta( 'phila_append_before_wysiwyg', $args = array('type' => 'textarea'));
          $append_after_wysiwyg = rwmb_meta( 'phila_append_after_wysiwyg', $args = array('type' => 'textarea'));
          // Set hero-header vars
          $hero_header_image = rwmb_meta( 'phila_hero_header_image', $args = array('type' => 'file_input'));
          $hero_header_alt_text = rwmb_meta( 'phila_hero_header_image_alt_text', $args = array('type' => 'text'));
          $hero_header_credit = rwmb_meta( 'phila_hero_header_image_credit', $args = array('type' => 'text'));
          $hero_header_title = rwmb_meta( 'phila_hero_header_title', $args = array('type' => 'text'));
          $hero_header_body_copy = rwmb_meta( 'phila_hero_header_body_copy', $args = array('type' => 'textarea'));
          $hero_header_call_to_action_button_url = rwmb_meta( 'phila_hero_header_call_to_action_button_url', $args = array('type' => 'URL'));
          $hero_header_call_to_action_button_text = rwmb_meta( 'phila_hero_header_call_to_action_button_text', $args = array('type' => 'text'));
          // Set module row vars
          $row_one_col_one_module = rwmb_meta( 'module_row_1_col_1');

          if (!empty($row_one_col_one_module)){
            $row_one_col_one_type = isset( $row_one_col_one_module['phila_module_row_1_col_1_type'] ) ? $row_one_col_one_module['phila_module_row_1_col_1_type'] : '';
            if ( $row_one_col_one_type == 'phila_module_row_1_col_1_blog_posts' ){
              $row_one_col_one_post_style = $row_one_col_one_module['module_row_1_col_1_options']['phila_module_row_1_col_1_post_style'];
            } else {
              $row_one_col_one_text_title = isset( $row_one_col_one_module['module_row_1_col_1_options']['phila_module_row_1_col_1_texttitle'] ) ? $row_one_col_one_module['module_row_1_col_1_options']['phila_module_row_1_col_1_texttitle'] : '';
              $row_one_col_one_textarea = isset( $row_one_col_one_module['module_row_1_col_1_options']['phila_module_row_1_col_1_textarea'] ) ? $row_one_col_one_module['module_row_1_col_1_options']['phila_module_row_1_col_1_textarea'] : '';
            }
          }
          $row_one_col_two_module = rwmb_meta( 'module_row_1_col_2');
          if (!empty($row_one_col_two_module)){
            $row_one_col_two_type = $row_one_col_two_module['phila_module_row_1_col_2_type'];
            if ( $row_one_col_two_type == 'phila_module_row_1_col_2_blog_posts' ){
              $row_one_col_two_post_style = 'phila_module_row_1_col_2_post_style_cards';
            } else {
              $row_one_col_two_text_title = $row_one_col_two_module['module_row_1_col_2_options']['phila_module_row_1_col_2_texttitle'];
              $row_one_col_two_textarea = $row_one_col_two_module['module_row_1_col_2_options']['phila_module_row_1_col_2_textarea'];
            }
          }
          $row_two_col_one_module = rwmb_meta( 'module_row_2_col_1');
          if (!empty($row_two_col_one_module)){
            $row_two_col_one_type = $row_two_col_one_module['phila_module_row_2_col_1_type'];
            if ( $row_two_col_one_type == 'phila_module_row_2_col_1_calendar' ){
              $row_two_col_one_cal_id = $row_two_col_one_module['module_row_2_col_1_options']['phila_module_row_2_col_1_cal_id'];
              $row_two_col_one_cal_url = $row_two_col_one_module['module_row_2_col_1_options']['phila_module_row_2_col_1_cal_url'];
            }
          }
          $row_two_col_two_module = rwmb_meta( 'module_row_2_col_2');
          if (!empty($row_two_col_two_module)){
            $row_two_col_two_type = $row_two_col_two_module['phila_module_row_2_col_2_type'];
            if ( $row_two_col_two_type == 'phila_module_row_2_col_2_calendar' ){
              $row_two_col_two_cal_id = $row_two_col_two_module['module_row_2_col_2_options']['phila_module_row_2_col_2_cal_id'];
              $row_two_col_two_cal_url = $row_two_col_two_module['module_row_2_col_2_options']['phila_module_row_2_col_2_cal_url'];
            }
          }

          //set template selection var
          $user_selected_template = rwmb_meta( 'phila_template_select');

    ?>
    <!-- If Custom Markup append_before_wysiwyg is present print it -->
    <?php if (!$append_before_wysiwyg == ''):?>
      <div class="row before-wysiwyg">
        <div class="small-24 columns">
          <?php echo $append_before_wysiwyg; ?>
        </div>
      </div>
    <?php endif; ?>
    <!-- Hero-Header MetaBox Modules -->
    <?php if (!$hero_header_image == ''): ?>
    <div class="row mtm">
      <div class="small-24 columns">
        <section class="department-header">
          <img id="header-image" class="size-full wp-image-4069" src="<?php echo $hero_header_image; ?>" alt="<?php echo $hero_header_alt_text;?>" width="975" height="431" />
          <?php if (!$hero_header_credit == ''): ?>
            <div class="photo-credit small-text">
              <span><i class="fa fa-camera" aria-hidden="true"></i> Photo by <?php echo $hero_header_credit; ?></span>
            </div>
          <?php endif; ?>
        <?php if (!$hero_header_title == ''): ?>
          <div class="intro row">
            <div class="column">
              <h1><?php echo $hero_header_title; ?></h1>
              <?php if (!$hero_header_body_copy == ''): ?>
                <p><?php echo $hero_header_body_copy; ?></p>
              <?php endif; ?>
              <?php if (!$hero_header_call_to_action_button_url == ''): ?>
                <p><a href="<?php echo $hero_header_call_to_action_button_url; ?>" class="button alternate no-margin"><?php echo $hero_header_call_to_action_button_text; ?></a></p>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
        </section>
      </div>
    </div>
    <?php endif; ?>
  <?php endif; ?>
  <!-- Begin Row One MetaBox Modules -->
  <?php if ( ( !empty( $row_one_col_one_module['phila_module_row_1_col_1_type'] ) ) && ( !empty( $row_one_col_two_module['phila_module_row_1_col_2_type'] ) ) ): ?>
    <section class="department-module-row-one mvl">
      <div class="row equal-height">
        <!-- Begin Column One -->
        <?php if ( $row_one_col_one_type  == 'phila_module_row_1_col_1_blog_posts' ): ?>
          <div class="large-18 columns">
            <div class="row">
              <?php if ($row_one_col_one_post_style == 'phila_module_row_1_col_1_post_style_list'):?>
                <!-- TURN SHORTCODE STRING INTO VAR -->
                <?php echo do_shortcode('[recent-posts list posts="3"]'); ?>
              <?php else: ?>
                <?php echo do_shortcode('[recent-posts posts="3"]'); ?>
              <?php endif;?>
            </div>
          </div>
        <?php elseif ( $row_one_col_one_type  == 'phila_module_row_1_col_1_custom_text' ): ?>
          <div class="large-18 columns">
            <h2 class="alternate"><?php echo($row_one_col_one_text_title); ?></h2>
            <div>
              <?php echo($row_one_col_one_textarea); ?>
            </div>
          </div>
        <?php endif; ?>
        <!-- End Column One -->
        <!-- Begin Column Two -->
        <?php if ( $row_one_col_two_type  == 'phila_module_row_1_col_2_blog_posts' ): ?>
          <div class="large-6 columns">
            <div class="row">
              <?php echo do_shortcode('[recent-posts posts="1"]'); ?>
            </div>
          </div>
        <?php elseif ( $row_one_col_two_type  == 'phila_module_row_1_col_2_custom_text' ): ?>
          <div class="large-6 columns">
            <h2 class="alternate"><?php echo($row_one_col_two_text_title); ?></h2>
            <div class="panel no-margin">
              <div>
                <?php echo($row_one_col_two_textarea); ?>
              </div>
            </div>
          </div>
        <?php endif; ?>
        <!-- End Column Two -->
      </div>
    </section>
  <?php endif; ?>
  <!-- End Row One MetaBox Modules -->

  <!-- WYSIWYG content -->
  <?php if( get_the_content() != '' ) : ?>
  <section class="wysiwyg-content">
    <div class="row">
      <div class="small-24 columns">
        <?php echo the_content();?>
      </div>
    </div>
  </section>
  <?php endif; ?>
  <!-- End WYSIWYG content -->

  <?php if( !empty($user_selected_template) ) : ?>
  <!-- Begin Template Display -->
  <section class="apply-template">
    <?php if ($user_selected_template == 'resource_list') : ?>
      <?php get_template_part( 'partials/resource', 'list' ); ?>
  <?php endif; ?>
  </section>
  <!-- End Template Display -->
  <?php endif; ?>

   <!-- Begin Row Two MetaBox Modules -->
   <?php if ( ( !empty( $row_two_col_one_module['phila_module_row_2_col_1_type'] ) ) && (!empty( $row_two_col_two_module['phila_module_row_2_col_2_type'] ) ) ): ?>
   <section class="department-module-row-two mvl">
     <div class="row">
       <?php if ( $row_two_col_one_type  == 'phila_module_row_2_col_1_calendar' ): ?>
         <div class="medium-12 columns">
           <h2 class="alternate">Calendar</h2>
           <div class="event-box">
             <?php echo do_shortcode('[calendar id="' . $row_two_col_one_cal_id .'"]'); ?>
           </div>
           <?php if ($row_two_col_one_cal_url):?>
             <a class="float-right see-all-right" href="<?php echo $row_two_col_one_cal_url; ?>">All Events</a>
           <?php endif; ?>
         </div>
       <?php elseif ( $row_two_col_one_type  == 'phila_module_row_2_col_1_press_release' ): ?>
           <div class="medium-12 columns">
             <div class="row">
             <?php echo do_shortcode('[press-releases posts=5]');?>
             </div>
           </div>
         <?php endif; ?>
         <?php if ( $row_two_col_two_type  == 'phila_module_row_2_col_2_calendar' ): ?>
           <div class="medium-12 columns">
             <h2 class="alternate">Calendar</h2>
             <div class="event-box">
               <?php echo do_shortcode('[calendar id="' . $row_two_col_two_cal_id .'"]'); ?>
             </div>
             <?php if ($row_two_col_one_cal_url):?>
               <a class="float-right see-all-right" href="<?php echo $row_two_col_two_cal_url; ?>">All Events</a>
             <?php endif; ?>
          </div>
         <?php elseif ( $row_two_col_two_type  == 'phila_module_row_2_col_2_press_release' ): ?>
           <div class="medium-12 columns">
             <div class="row">
               <?php echo do_shortcode('[press-releases posts=5]');?>
             </div>
           </div>
         <?php endif; ?>
     </div>
   </section>
   <?php endif; ?>
   <!-- End Row Two MetaBox Modules -->

   <!-- If Custom Markup append_after_wysiwyg is present print it -->
  <?php if (!$append_after_wysiwyg == ''):?>
    <div class="row after-wysiwyg">
      <div class="small-24 columns">
        <?php echo $append_after_wysiwyg; ?>
      </div>
    </div>
  <?php endif; ?>
</div> <!-- End .entry-content -->

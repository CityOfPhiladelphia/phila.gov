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
          $row_one_col_two_action_panel = rwmb_meta('module_row_1_col_2_call_to_action_panel');

          if (!empty($row_one_col_two_module)){
            $row_one_col_two_type = isset( $row_one_col_two_module['phila_module_row_1_col_2_type'] ) ? $row_one_col_two_module['phila_module_row_1_col_2_type'] : '';

            if ( $row_one_col_two_type == 'phila_module_row_1_col_2_blog_posts' ){
              $row_one_col_two_post_style = 'phila_module_row_1_col_2_post_style_cards';
            } elseif( $row_one_col_two_type == 'phila_module_row_1_col_2_custom_text' ) {

              $row_one_col_two_text_title = $row_one_col_two_module['module_row_1_col_2_options']['phila_module_row_1_col_2_texttitle'];

              $row_one_col_two_textarea = $row_one_col_two_module['module_row_1_col_2_options']['phila_module_row_1_col_2_textarea'];

            }else{
              $row_one_col_two_action_panel_title = isset( $row_one_col_two_action_panel['phila_action_section_title'] ) ? $row_one_col_two_action_panel['phila_action_section_title'] : '' ;

              $row_one_col_two_action_panel_summary = isset( $row_one_col_two_action_panel['phila_action_panel_summary'] ) ? $row_one_col_two_action_panel['phila_action_panel_summary'] : '';

              $row_one_col_two_action_panel_cta_text = isset( $row_one_col_two_action_panel['phila_action_panel_cta_text'] ) ? $row_one_col_two_action_panel['phila_action_panel_cta_text'] : '';

              $row_one_col_two_action_panel_link = isset( $row_one_col_two_action_panel['phila_action_panel_link'] ) ? $row_one_col_two_action_panel['phila_action_panel_link'] : '';

              $row_one_col_two_action_panel_link_loc  = isset(  $row_one_col_two_action_panel['phila_action_panel_link_loc'] ) ? $row_one_col_two_action_panel['phila_action_panel_link_loc'] : '';

              $row_one_col_two_action_panel_fa_circle  = isset( $row_one_col_two_action_panel['phila_action_panel_fa_circle'] ) ? $row_one_col_two_action_panel['phila_action_panel_fa_circle'] : '' ;

              $row_one_col_two_action_panel_fa = isset( $row_one_col_two_action_panel['phila_action_panel_fa'] ) ? $row_one_col_two_action_panel['phila_action_panel_fa'] : '';
            }
          }

          //set row 2 vars
          $row_two_column_selection = rwmb_meta('phila_module_row_2_column_selection');

          $row_two_col_one_module = rwmb_meta( 'module_row_2_col_1');

          if (!empty($row_two_column_selection)) {

            $module_row_two_full_cal_col = rwmb_meta( 'phila_module_row_two_full_cal_col');

            if ( $row_two_column_selection == 'phila_module_row_2_full_column' ){
              $row_two_full_col_cal_id = $module_row_two_full_cal_col['phila_module_row_2_full_col_cal_id'];
              $row_two_full_col_cal_url = $module_row_two_full_cal_col['phila_module_row_2_full_col_cal_url'];
            }

            if ( $row_two_column_selection == 'phila_module_row_2_2_column' ){

              if (!empty($row_two_col_one_module)){

                $row_two_col_one_type = isset($row_two_col_one_module['phila_module_row_2_col_1_type']) ? $row_two_col_one_module['phila_module_row_2_col_1_type'] : '';

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
            }
          }

        //set template selection var
        $user_selected_template = rwmb_meta( 'phila_template_select');

    ?>
    <?php if (!$append_before_wysiwyg == ''):?>
      <!-- If Custom Markup append_before_wysiwyg is present print it -->
      <div class="row before-wysiwyg">
        <div class="small-24 columns">
          <?php echo $append_before_wysiwyg; ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!$hero_header_image == ''): ?>
    <!-- Hero-Header MetaBox Modules -->
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
  <?php if ( ( !empty( $row_one_col_one_module['phila_module_row_1_col_1_type'] ) ) && ( !empty( $row_one_col_two_module['phila_module_row_1_col_2_type'] ) ) ): ?>
    <!-- Begin Row One MetaBox Modules -->
    <section class="department-module-row-one mvl">
      <div class="row equal-height">
        <?php if ( $row_one_col_one_type  == 'phila_module_row_1_col_1_blog_posts' ): ?>
          <!-- Begin Column One -->
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
            <h2 class="contrast"><?php echo($row_one_col_one_text_title); ?></h2>
            <div>
              <?php echo($row_one_col_one_textarea); ?>
            </div>
          </div>
          <!-- End Column One -->
        <?php endif; ?>
        <?php if ( $row_one_col_two_type  == 'phila_module_row_1_col_2_blog_posts' ): ?>
          <!-- Begin Column Two -->
          <div class="large-6 columns">
            <div class="row">
              <?php echo do_shortcode('[recent-posts posts="1"]'); ?>
            </div>
          </div>
        <?php elseif ( $row_one_col_two_type  == 'phila_module_row_1_col_2_custom_text' ): ?>
          <div class="large-6 columns">
            <h2 class="contrast"><?php echo($row_one_col_two_text_title); ?></h2>
            <div class="panel no-margin">
              <div>
                <?php echo($row_one_col_two_textarea); ?>
              </div>
            </div>
          </div>
        <?php elseif (!$row_one_col_two_action_panel_summary == ''): ?>
          <div class="large-6 columns">
            <h2 class="contrast"><?php echo $row_one_col_two_action_panel_title; ?></h2>
              <?php if (!$row_one_col_two_action_panel_link == ''): ?>
                <a href="<?php echo $action_panel_link; ?>"  class="action-panel">
                  <div class="panel">
                    <header>
                      <?php if ($row_one_col_two_action_panel_fa_circle): ?>
                        <div>
                          <span class="fa-stack fa-4x center" aria-hidden="true">
                          <i class="fa fa-circle fa-stack-2x"></i>
                          <i class="fa <?php echo $row_one_col_two_action_panel_fa; ?> fa-stack-1x fa-inverse"></i>
                        </span>
                      </div>
                      <?php else: ?>
                        <div>
                          <span><i class="fa <?php echo $row_one_col_two_action_panel_fa; ?> fa-7x" aria-hidden="true"></i></span>
                        </div>
                      <?php endif; ?>
                        <?php if (!$row_one_col_two_action_panel_cta_text == ''): ?>
                          <span class="center <?php if ($row_one_col_two_action_panel_link_loc) echo 'external';?>"><?php echo $row_one_col_two_action_panel_cta_text; ?></span>
                        <?php endif; ?>
                    </header>
                    <hr class="mll mrl">
                      <span class="details"><?php echo $row_one_col_two_action_panel_summary; ?></span>
                  </div>
                </a>
              <?php endif; ?>
            </div>
          <!-- End Column Two -->
        <?php endif; ?>
      </div>
    </section>
    <!-- End Row One MetaBox Modules -->
  <?php endif; ?>

  <?php if( get_the_content() != '' ) : ?>
  <!-- WYSIWYG content -->
  <section class="wysiwyg-content">
    <div class="row">
      <div class="small-24 columns">
        <?php echo the_content();?>
      </div>
    </div>
  </section>
  <!-- End WYSIWYG content -->
  <?php endif; ?>

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
  <?php if ( !empty( $row_two_full_col_cal_id ) ) : ?>
    <div class="row">
      <div class="columns">
        <h2>Calendar</h2>
      </div>
    </div>

    <div class="row expanded calendar-row mbm ptm">
      <div class="medium-centered large-16 columns">
        <?php echo do_shortcode('[calendar id="' . $row_two_full_col_cal_id . '"]'); ?>
      </div>
    </div>
    <?php if ($row_two_full_col_cal_url):?>
      <div class="row">
        <div class="columns">
          <a class="float-right see-all-right" href="<?php echo $row_two_full_col_cal_url; ?>">All Events</a>
          </div>
      </div>
    <?php endif; ?>
   <?php endif; ?>

   <?php if ( ( !empty( $row_two_col_one_module['phila_module_row_2_col_1_type'] ) ) && (!empty( $row_two_col_two_module['phila_module_row_2_col_2_type'] ) ) ): ?>
   <section class="department-module-row-two mvl">
     <div class="row">
       <?php if ( $row_two_col_one_type  == 'phila_module_row_2_col_1_calendar' ): ?>
         <div class="medium-12 columns">
           <h2 class="contrast">Calendar</h2>
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
             <h2 class="contrast">Calendar</h2>
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
   <!-- End Row Two MetaBox Modules -->
   <?php endif; ?>

  <?php if (!$append_after_wysiwyg == ''):?>
  <!-- If Custom Markup append_after_wysiwyg is present print it -->
    <div class="row after-wysiwyg">
      <div class="small-24 columns">
        <?php echo $append_after_wysiwyg; ?>
      </div>
    </div>
  <?php endif; ?>
</div> <!-- End .entry-content -->

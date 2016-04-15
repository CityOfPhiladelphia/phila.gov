<?php
/*
*
* Template part
* for displaying on-site department content
*
*/
?>

<div class="small-24 columns">
  <?php the_title( '<h2 class="entry-title">', '</h2>' ); ?>
</div>


<div class="small-24 columns">
  <div class="row">
    <div data-swiftype-index='true' class="entry-content small-24 columns">
      <?php if (function_exists('rwmb_meta')): ?>
        <?php // Set custom markup vars
              $append_before_wysiwyg = rwmb_meta( 'phila_append_before_wysiwyg', $args = array('type' => 'textarea'));
              $append_after_wysiwyg = rwmb_meta( 'phila_append_after_wysiwyg', $args = array('type' => 'textarea'));
              // Set hero-header vars
              $hero_header_image = rwmb_meta( 'phila_hero_header_image', $args = array('type' => 'file_input'));
              $hero_header_alt_text = rwmb_meta( 'phila_hero_header_image_alt_text', $args = array('type' => 'text'));
              $hero_header_title = rwmb_meta( 'phila_hero_header_title', $args = array('type' => 'text'));
              $hero_header_body_copy = rwmb_meta( 'phila_hero_header_body_copy', $args = array('type' => 'textarea'));
              $hero_header_call_to_action_button_url = rwmb_meta( 'phila_hero_header_call_to_action_button_url', $args = array('type' => 'URL'));
              $hero_header_call_to_action_button_text = rwmb_meta( 'phila_hero_header_call_to_action_button_text', $args = array('type' => 'text'));
              // Set module row vars
              $row_one_col_one_module = rwmb_meta( 'module_row_1_col_1');
              if (!empty($row_one_col_one_module)){
                $row_one_col_one_type = $row_one_col_one_module['phila_module_row_1_col_1_type'];
                $row_one_col_one_post_style = $row_one_col_one_module['module_row_1_col_1_options']['phila_module_row_1_col_1_post_style'];
                $row_one_col_one_text_title = $row_one_col_one_module['module_row_1_col_1_options']['phila_module_row_1_col_1_texttitle'];
                $row_one_col_one_textarea = $row_one_col_one_module['module_row_1_col_1_options']['phila_module_row_1_col_1_textarea'];
              }
              $row_one_col_two_module = rwmb_meta( 'module_row_1_col_2');
              if (!empty($row_one_col_two_module)){
                $row_one_col_two_type = $row_one_col_two_module['phila_module_row_1_col_2_type'];
                $row_one_col_two_post_style = $row_one_col_two_module['module_row_1_col_2_options']['phila_module_row_1_col_2_post_style'];
                $row_one_col_two_text_title = $row_one_col_two_module['module_row_1_col_2_options']['phila_module_row_1_col_2_texttitle'];
                $row_one_col_two_textarea = $row_one_col_two_module['module_row_1_col_2_options']['phila_module_row_1_col_2_textarea'];
              }
        ?>
        <!-- If Custom Markup append_before_wysiwyg is present print it -->
        <?php if (!$append_before_wysiwyg == ''):?>
          <?php echo $append_before_wysiwyg; ?>
        <?php endif; ?>
        <!-- Hero-Header MetaBox Modules -->
        <?php if (!$hero_header_image == ''): ?>
            <section class="department-header">
              <img id="header-image" class="size-full wp-image-4069" src="<?php echo $hero_header_image; ?>" alt="<?php echo $hero_header_alt_text;?>" width="975" height="431" />
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
        <?php endif; ?>
      <?php endif; ?>
      <!-- Begin Row One MetaBox Modules -->
      <?php if ( ( !empty($row_one_col_one_module ) ) && (!empty($row_one_col_one_module ) ) ): ?>
        <section>
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

       <?php echo the_content();?>

       <!-- If Custom Markup append_after_wysiwyg is present print it -->
       <?php if (!$append_after_wysiwyg == ''):?>
         <?php echo $append_after_wysiwyg; ?>
       <?php endif; ?>

       <!-- If JotForm Embed is present print it -->
       <?php if (function_exists('rwmb_meta')) {
         $jotform = rwmb_meta( 'phila_jotform_embed', $args = array('type' => 'textarea'));
         if ($jotform != ''){
           echo $jotform;
         }
       }
       ?>
     </div>
  </div>
</div>

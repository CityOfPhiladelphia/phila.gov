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
        <?php $append_before_wysiwyg = rwmb_meta( 'phila_append_before_wysiwyg', $args = array('type' => 'textarea'));
              $append_after_wysiwyg = rwmb_meta( 'phila_append_after_wysiwyg', $args = array('type' => 'textarea'));

              $hero_header_image = rwmb_meta( 'phila_hero_header_image', $args = array('type' => 'file_input'));
              $hero_header_alt_text = rwmb_meta( 'phila_hero_header_image_alt_text', $args = array('type' => 'text'));
              $hero_header_title = rwmb_meta( 'phila_hero_header_title', $args = array('type' => 'text'));
              $hero_header_body_copy = rwmb_meta( 'phila_hero_header_body_copy', $args = array('type' => 'textarea'));
              $hero_header_call_to_action_button_url = rwmb_meta( 'phila_hero_header_call_to_action_button_url', $args = array('type' => 'URL'));
              $hero_header_call_to_action_button_text = rwmb_meta( 'phila_hero_header_call_to_action_button_text', $args = array('type' => 'text'));

              $row_one_col_one_module = rwmb_meta( 'module_row_1_col_1');
              // foreach ( $row_one_col_one_module as $module_meta ):
              //   $row_one_col_one_module_type = ();
              //   $row_one_col_one_module_option = ();
              // endforeach;
              $row_one_col_two_module = rwmb_meta( 'module_row_1_col_2');

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
      <!-- Row One MetaBox Modules -->
      <?php if ( ( !$row_one_col_one_module[phila_module_row_1_col_1_type]  == '') && (!$row_one_col_two_module[phila_module_row_1_col_2_type]  == '' ) ): ?>
        <section>
          <div class="row equal-height">
            <?php if ( $row_one_col_one_module[phila_module_row_1_col_1_type]  == 'phila_module_row_1_col_1_blog_posts' ): ?>
              <div class="large-18 columns">
                <div class="row">
                  <?php if ($row_one_col_one_module[module_row_1_col_1_options][phila_module_row_1_col_1_post_style] == 'phila_module_row_1_col_1_post_style_list'):?>
                    <?php echo do_shortcode('[recent-posts list posts="3"]'); ?>
                  <?php else: ?>
                    <?php echo do_shortcode('[recent-posts posts="3"]'); ?>
                  <?php endif;?>
                </div>
              </div>
            <?php else: ?>
              <div class="large-18 columns">
                <h2 class="alternate"><?php echo($row_one_col_one_module[module_row_1_col_1_options][phila_module_row_1_col_1_texttitle]); ?></h2>
                <?php echo($row_one_col_one_module[module_row_1_col_1_options][phila_module_row_1_col_1_textarea]); ?>
              </div>
            <?php endif; ?>

            <div class="large-6 columns">
              <?php if ( $row_one_col_two_module[phila_module_row_1_col_2_type]  == 'phila_module_row_1_col_2_blog_posts' ): ?>
                <div class="row">
                  <?php echo do_shortcode('[recent-posts posts="1"]'); ?>
                </div>
              <?php else: ?>
                <h2 class="alternate"><?php echo($row_one_col_two_module[module_row_1_col_2_options][phila_module_row_1_col_2_texttitle]); ?></h2>
                  <div class="panel no-margin">
                    <div>
                      <?php echo($row_one_col_two_module[module_row_1_col_2_options][phila_module_row_1_col_2_textarea]); ?>
                    </div>
                  </div>
              <?php endif; ?>
            </div>
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

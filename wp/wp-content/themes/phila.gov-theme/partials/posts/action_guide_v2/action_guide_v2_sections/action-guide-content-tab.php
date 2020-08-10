<?php
/**
 * The template used for displaying Action Guide V2 content
 *
 * @package phila-gov
 */
?>
<?php
  // MetaBox variables
  $tabs = rwmb_meta('phila_tabbed_content');
?>
<?php if (!phila_util_is_array_empty($tabs)): ?>
  <!-- Program and initiatives -->
  <section>
  <?php
    foreach ($tabs as $key => $value){
      $current_tab = $tabs[$key];?>
      <?php echo $current_tab['tab_label']; ?>
      <i class="<?php echo $current_tab['tab_icon']; ?> fa-2x" aria-hidden="true"></i>
      <?php
    foreach ($current_tab['phila_row'] as $key => $value){
      $current_row = $current_tab['phila_row'][$key]['phila_tabbed_options'];?>
        <?php if ( isset( $current_row['phila_tabbed_select'] ) ){

          // Begin full width row
          $current_row_option = $current_row['phila_tabbed_select'];
          if ( $current_row_option == 'phila_metabox_tabbed_single_title'):?>
            <!-- Single Title -->
            <div class="grid-x grid-margin-x mvl">
              <div class="medium-24 cell pbm">
                <div class="mbl">
                  <?php if( isset($current_row[$current_row_option]['phila_single_title'])): ?>
                    <div class="mbl">
                      <h4 class="h3 black bg-ghost-gray phm-mu mtn mbm"><?php echo $current_row[$current_row_option]['phila_single_title']; ?></h4>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <!-- /Single Title -->
          <?php elseif ( $current_row_option == 'phila_metabox_tabbed_single_wysiwyg'):?>
            <!-- Staff listing -->
              <?php include(locate_template('partials/departments/phila_staff_directory_listing.php')); ?>
            <!-- /Staff listing -->
          <?php elseif ( $current_row_option == 'phila_metabox_tabbed_repeater_wysiwyg'):?>
            <!-- Repeater wyswiyg -->
              <?php if( isset($current_row[$current_row_option]['step_repeater_wysiwyg'])){ ?>
                <?php $step_content = phila_loop_clonable_metabox( $current_row[$current_row_option]['step_repeater_wysiwyg'] ); ?>
                <div class="grid-x grid-margin-x mvl">
                  <div class="medium-24 cell pbxl">
                    <?php foreach( $step_content as $content ) :?>
                      <div class="mbl">
                        <?php if( isset($content['phila_custom_wysiwyg']['phila_wysiwyg_title'] )): ?>
                          <h4 id="<?php echo $content['url'];?>" class="h3 black bg-ghost-gray phm-mu mtn mbm"><?php echo $content['phila_custom_wysiwyg']['phila_wysiwyg_title']; ?></h4>
                        <?php endif;?>
                        <?php if( isset($content['phila_custom_wysiwyg']['phila_wysiwyg_content'] )): ?>
                          <div class="phm">
                            <?php echo apply_filters( 'the_content', $content['phila_custom_wysiwyg']['phila_wysiwyg_content']) ?>
                          </div>
                        <?php endif;?>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php } ?>
            <!-- /Repeater wyswiyg -->
          <?php elseif ( $current_row_option == 'phila_metabox_tabbed_stepped_content'):?>
            <!-- Staff listing -->
              <?php include(locate_template('partials/departments/phila_staff_directory_listing.php')); ?>
            <!-- /Staff listing -->
          <?php endif;?>
          <?php } // if row isset ?>
      <?php } // row content ?>
    <?php } // tab content ?>
  </div>
<!-- /Program and initiatives -->
<?php endif; ?>
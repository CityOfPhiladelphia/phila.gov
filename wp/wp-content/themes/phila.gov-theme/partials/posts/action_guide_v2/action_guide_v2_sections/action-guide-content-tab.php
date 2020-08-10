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


  <!-- Tabs -->
  <div class="grid-container action-guide-v2-tabs mtxl">
    <div class="grid-x grid-margin-x mvl one-quarter-row">
    <?php
    foreach ($tabs as $key => $value){
      $current_tab = $tabs[$key];
      $tab_id = $key+1;
    ?>
      <div class="cell medium-8 step-label bg-dark-ben-franklin white <?php echo ($tab_id == 1) ? 'active' : '' ?>" id="step-<?php echo $tab_id?>-label">
        <div class="bg-dark-ben-franklin active-bar"></div>
        <?php if( isset($current_tab['tab_icon'])) :?>
          <i class="<?php echo $current_tab['tab_icon'] ?> fa-2x" aria-hidden="true"></i>
        <?php endif; ?>
        <?php if( isset($current_tab['tab_label'])) :?>
          <div class="label-copy"><?php echo $current_tab['tab_label'];?></div>
        <?php endif; ?>
      </div>
    <?php }?>
    </div>
  </div>
  <!-- /Tabs -->

  <section>
  <?php
    foreach ($tabs as $key => $value){
      $current_tab = $tabs[$key]; 
      $tab_id = $key+1;
  ?>
      <div class="content-action_guide action-guide-v2 <?php echo ($tab_id == 1) ? 'active' : '' ?>" id="tab-<?php echo $tab_id?>-content">
      <?php
        foreach ($current_tab['phila_row'] as $key => $value){
        $current_row = $current_tab['phila_row'][$key]['phila_tabbed_options'];?>
        <?php if ( isset( $current_row['phila_tabbed_select'] ) ){

          // Begin full width row
          $current_row_option = $current_row['phila_tabbed_select'];
          if ( $current_row_option == 'phila_metabox_tabbed_single_title'):?>
            <!-- Single title -->
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
            <!-- /Single title -->
          <?php elseif ( $current_row_option == 'phila_metabox_tabbed_single_wysiwyg'):?>
            <!-- Single wywiwyg -->
              <div class="grid-x grid-margin-x mvl">
                <div class="medium-24 cell pbxl">
                    <div class="mbl">
                      <?php if( isset($current_row[$current_row_option]['step_wysiwyg'])): ?>
                        <div>
                          <?php echo apply_filters( 'the_content', $current_row[$current_row_option]['step_wysiwyg']) ?>
                        </div>
                      <?php endif; ?>
                    </div>
                </div>
              </div>
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
              <div class="grid-x grid-margin-x mvl">
                <div class="medium-24 cell pbm">
                  <?php if( isset($current_row[$current_row_option]['phila_stepped_content']) && isset($current_row[$current_row_option]['phila_stepped_content']['phila_ordered_content'])): ?>
                    <?php $steps = $current_row[$current_row_option]['phila_stepped_content']['phila_ordered_content']; ?>
                    <div class="mbl">
                      <div class="phm">
                        <?php include( locate_template( 'partials/stepped-content.php' ) ); ?>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            <!-- /Staff listing -->
          <?php endif;?>
          <?php } // if row isset ?>
      <?php } // row content ?>
      </div>
    <?php } // tab content ?>
  </div>
<!-- /Program and initiatives -->
<?php endif; ?>
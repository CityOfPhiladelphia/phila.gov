<?php
/**
 * The template used for displaying Action Guide V2 content on mobile
 *
 * @package phila-gov
 */
?>

<section>
  <?php
  $tab_count = count($tabs);
  foreach ($tabs as $tab_key => $value){
    $current_tab = $tabs[$tab_key]; 
    $tab_id = $tab_key+1;
  ?>
<!-- Tabbed content -->
  <div class="content-action_guide" id="tab-<?php echo $tab_id?>-content-mobile">


    <?php if( isset($current_tab['tab_label'])):?>
      <h2 class="label-copy"><?php echo $current_tab['tab_label'];?></h2>
    <?php endif; ?>
    <div class="grid-x grid-margin-x">
      <div class="medium-24 cell pbs">
        <ul class="accordion phn" data-accordion data-multi-expand="true" data-allow-all-closed="true">
          <?php foreach ($current_tab['phila_row'] as $row_key => $value){
            $current_row = $current_tab['phila_row'][$row_key]['phila_tabbed_options']; 
            if ( isset( $current_row['phila_tabbed_select'] ) ){
              $current_row_option = $current_row['phila_tabbed_select'];
              if ( $current_row_option == 'phila_metabox_tabbed_repeater_wysiwyg'):
                include(locate_template('partials/posts/action_guide_v2/components/mobile/repeater-wysiwyg-mobile.php')); 
              elseif ( $current_row_option == 'phila_metabox_tabbed_stepped_content'):
                include(locate_template('partials/posts/action_guide_v2/components/mobile/stepped-content-mobile.php')); 
              elseif ( $current_row_option == 'phila_metabox_tabbed_timeline_content'):
                include(locate_template('partials/posts/action_guide_v2/components/mobile/timeline-content-mobile.php')); 
              endif;
            ?>
            <?php } //end phila_tabbed_select isset ?>
          <?php } //end phila_row foreach ?>
        </ul>
      </div>
    </div>

  </div>
  <!-- /Tabbed content -->
  <?php } ?>
</section>
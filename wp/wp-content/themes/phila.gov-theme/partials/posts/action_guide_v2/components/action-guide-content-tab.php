<?php
/**
 * The template used for displaying Action Guide V2 content
 *
 * @package phila-gov
 */
?>
  <!-- Tabbed content -->
  <section>
  <?php
    $tab_count = count($tabs);
    foreach ($tabs as $tab_key => $value){
      $current_tab = $tabs[$tab_key]; 
      $tab_id = $tab_key+1;
  ?>
      <div class="content-action_guide action-guide-v2 <?php echo ($tab_id == 1) ? 'active' : '' ?>" id="tab-<?php echo $tab_id?>-content">
      <?php
        foreach ($current_tab['phila_row'] as $row_key => $value){
          $current_row = $current_tab['phila_row'][$row_key]['phila_tabbed_options'];
          if ( isset( $current_row['phila_tabbed_select'] ) ){
            $current_row_option = $current_row['phila_tabbed_select'];
            if ( $current_row_option == 'phila_metabox_tabbed_single_wysiwyg'):
              include(locate_template('partials/posts/action_guide_v2/components/single-wysiwyg.php')); 
            elseif ( $current_row_option == 'phila_metabox_tabbed_repeater_wysiwyg'):
              include(locate_template('partials/posts/action_guide_v2/components/repeater-wysiwyg.php')); 
            elseif ( $current_row_option == 'phila_metabox_tabbed_stepped_content'):
              include(locate_template('partials/posts/action_guide_v2/components/stepped-content.php')); 
            endif;
          } // if row isset 
        } // row content 
        include(locate_template('partials/posts/action_guide_v2/components/tab-pagination.php')); ?>
      </div>
    <?php } // tab content ?>
  </section>
<!-- /Tabbed content -->
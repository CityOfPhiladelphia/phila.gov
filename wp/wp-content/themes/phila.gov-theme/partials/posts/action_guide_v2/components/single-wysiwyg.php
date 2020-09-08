<!-- Single wywiwyg -->
<div class="grid-x grid-margin-x mvl">
  <div class="medium-24 cell pbs">
      <div class="mbl">
        <?php if( isset($current_row[$current_row_option]['phila_wysiwyg_content'])): ?>
          <div>
            <?php echo apply_filters( 'the_content', $current_row[$current_row_option]['phila_wysiwyg_content']) ?>
          </div>
        <?php endif; ?>
        <!-- Anchor links -->
        <?php if( isset($current_row[$current_row_option]['include_anchor_links']) && $current_row[$current_row_option]['include_anchor_links'] == true): ?>
          <ul class="no-bullet mbn pln">
            <?php foreach ($current_tab['phila_row'] as $row_key => $value):?>
              <?php $current_row = $current_tab['phila_row'][$row_key]['phila_tabbed_options']; ?>
              <?php $current_row_option = $current_row['phila_tabbed_select']; ?>
              <?php if ( $current_row_option == 'phila_metabox_tabbed_repeater_wysiwyg'):?>

                <?php $step_content = phila_loop_clonable_metabox( $current_row[$current_row_option]['step_repeater_wysiwyg'] ); ?>
                <?php foreach( $step_content as $content ) :?>
                  <?php if( isset($content['phila_custom_wysiwyg']['phila_wysiwyg_title'] )): ?>
                    <?php $current_row_id = strtolower(str_replace(' ', '-', $content['phila_custom_wysiwyg']['phila_wysiwyg_title']));?>
                    <li class="pvs-mu phl-mu phs">
                      <a href="<?php echo '#'.$current_row_id;?>" class="anchor underline">- <?php echo $content['phila_custom_wysiwyg']['phila_wysiwyg_title']; ?></a>
                    </li>
                  <?php endif;?>
                <?php endforeach; ?>
              <?php elseif ( $current_row_option == 'phila_metabox_tabbed_stepped_content'):?>

                <?php if( isset($current_row[$current_row_option]['phila_stepped_content']['phila_wysiwyg_title'] )): ?>
                  <?php $current_row_id = strtolower(str_replace(' ', '-', $current_row[$current_row_option]['phila_stepped_content']['phila_wysiwyg_title']));?>
                  <li class="pvs-mu phl-mu phs">
                    <a href="<?php echo '#'.$current_row_id;?>" class="anchor underline">- <?php echo $current_row[$current_row_option]['phila_stepped_content']['phila_wysiwyg_title']; ?></a>
                  </li>
                <?php endif;?>
              <?php elseif ( $current_row_option == 'phila_metabox_tabbed_timeline_content'):?>
                
                <?php if( isset($current_row[$current_row_option]['phila_timeline_content']['phila_wysiwyg_title'] )): ?>
                  <?php $current_row_id = strtolower(str_replace(' ', '-', $current_row[$current_row_option]['phila_timeline_content']['phila_wysiwyg_title']));?>
                  <li class="pvs-mu phl-mu phs">
                    <a href="<?php echo '#'.$current_row_id;?>" class="anchor underline">- <?php echo $current_row[$current_row_option]['phila_timeline_content']['phila_wysiwyg_title']; ?></a>
                  </li>
                <?php endif;?>  
              <?php endif;?>  
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
        <!-- /Anchor links -->
      </div>
  </div>
</div>
<!-- /Single wywiwyg -->
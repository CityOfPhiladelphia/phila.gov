<!-- Repeater wyswiyg -->
<?php if( isset($current_row[$current_row_option]['step_repeater_wysiwyg'])){ ?>
  <?php $step_content = phila_loop_clonable_metabox( $current_row[$current_row_option]['step_repeater_wysiwyg'] ); ?>
  <div class="grid-x grid-margin-x mvl">
    <div class="medium-24 cell pbxl">
      <?php foreach( $step_content as $content ) :?>
        <div class="mbl">
          <?php if( isset($content['phila_custom_wysiwyg']['phila_wysiwyg_title'] )): ?>
            <?php $current_row_id = strtolower(str_replace(' ', '-', $content['phila_custom_wysiwyg']['phila_wysiwyg_title']));?>
            <h4 id="<?php echo $current_row_id;?>" class="h3 black bg-ghost-gray phm-mu mtn mbm"><?php echo $content['phila_custom_wysiwyg']['phila_wysiwyg_title']; ?></h4>
          <?php endif;?>
          <?php if( isset($content['phila_custom_wysiwyg']['phila_wysiwyg_content'] )): ?>
            <div class="plm">
              <?php echo apply_filters( 'the_content', $content['phila_custom_wysiwyg']['phila_wysiwyg_content']) ?>
            </div>
          <?php endif;?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php } ?>
<!-- /Repeater wyswiyg -->
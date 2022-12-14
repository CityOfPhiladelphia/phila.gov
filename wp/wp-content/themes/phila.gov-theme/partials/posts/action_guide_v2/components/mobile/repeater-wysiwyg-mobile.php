<!-- Repeater wyswiyg mobile -->
<?php if( isset($current_row[$current_row_option]['step_repeater_wysiwyg'])){ ?>
  <?php $step_content = phila_loop_clonable_metabox( $current_row[$current_row_option]['step_repeater_wysiwyg'] ); ?>
  <?php foreach( $step_content as $content ) :?>
    <li class="mbs accordion-item" data-accordion-item>
    <?php if( isset($content['phila_custom_wysiwyg']['phila_wysiwyg_title'] )): ?>
      <a href="#" class="accordion-title"><?php echo $content['phila_custom_wysiwyg']['phila_wysiwyg_title']; ?></a>
    <?php endif;?>
    <?php if( isset($content['phila_custom_wysiwyg']['phila_wysiwyg_content'] )): ?>
      <div class="phm accordion-content" data-tab-content>
        <?php echo apply_filters( 'the_content', $content['phila_custom_wysiwyg']['phila_wysiwyg_content']) ?>
      </div>
    <?php endif;?>
    </li>
  <?php endforeach; ?>
<?php } ?>
<!-- /Repeater wyswiyg mobile -->
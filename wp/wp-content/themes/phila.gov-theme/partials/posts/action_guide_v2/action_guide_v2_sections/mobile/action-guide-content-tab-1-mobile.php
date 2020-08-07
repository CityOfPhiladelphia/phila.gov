<!-- Tab 1 -->
<div class="content-action_guide action-guide-v2 active" id="tab-1-content-mobile">


  <?php if( rwmb_meta('step_1_label') != null) :?>
    <h2 class="label-copy"><?php echo rwmb_meta( 'step_1_label' );?></h2>
  <?php endif; ?>

  <div class="grid-x grid-margin-x mvl">
    <div class="medium-24 cell pbl">
      <ul class="accordion phn" data-accordion data-multi-expand="true" data-allow-all-closed="true">
      <?php foreach( $step_1_content as $content ) :?>
          <li class="mbl accordion-item" data-accordion-item>
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
      </ul>
    </div>
  </div>

</div>
<!-- /Tab 1 -->
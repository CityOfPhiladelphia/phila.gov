<!-- Tab 2 -->
<div class="content-action_guide action-guide-v2 active" id="tab-2-content-mobile">


  <?php if( rwmb_meta('step_2_label') != null) :?>
    <h2 class="label-copy"><?php echo rwmb_meta( 'step_2_label' );?></h2>
  <?php endif; ?>

  <div class="grid-x grid-margin-x">
    <div class="medium-24 cell pbs">
      <ul class="accordion phn" data-accordion data-multi-expand="true" data-allow-all-closed="true">
        
        <?php if( isset($step_2_content_before_steps['phila_custom_wysiwyg']['phila_wysiwyg_title'] )): ?>
          <li class="mbs accordion-item" data-accordion-item>
            <a href="#" class="accordion-title"><?php echo $step_2_content_before_steps['phila_custom_wysiwyg']['phila_wysiwyg_title']; ?></a>
            <div class="phm accordion-content" data-tab-content>
              <?php echo apply_filters( 'the_content', $step_2_content_before_steps['phila_custom_wysiwyg']['phila_wysiwyg_content']) ?>
              <div class="grid-x grid-margin-x mvl">
                <div class="medium-24 cell pbm">
                  <?php if( isset($phila_stepped_content_step_2)): ?>
                    <?php $steps = $phila_stepped_content_step_2; ?>
                    <div class="mbl">
                      <div class="phm">
                        <?php include( locate_template( 'partials/stepped-content.php' ) ); ?>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </li>
        <?php endif;?>

        <?php foreach( $step_2_content_after_steps as $content ) :?>
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

      </ul>
    </div>
  </div>

</div>
<!-- /Tab 2 -->
<?php
  /* Required vars:
    $is_full_width - Boolean - Use old style with gray bg and full-width click, or new style with expand/collapse
    $accordion_title - string 
    $accordion_group - array
    Optional 
    $use_icon = Boolean - determine if icon should be displayed when using non-full width template
  */
?>
<?php if ( $is_full_width === true) : ?>
<section class="mvl">
  <div class="grid-container">
    <h2 id="#<?php echo phila_format_uri($accordion_title)?>"><?php echo isset($accordion_title) ? $accordion_title : '' ?></h2>
    <div class="accordion" data-accordion data-multi-expand="true">
      <?php foreach ($accordion_group as $accordion ) : ?>
        <div class="accordion-item" data-accordion-item>
          <a href="#" class="accordion-title"><?php echo $accordion['phila_custom_wysiwyg']['phila_wysiwyg_title'] ?></a>
          <div class="accordion-content" data-tab-content>
          <?php echo apply_filters('the_content', $accordion['phila_custom_wysiwyg']['phila_wysiwyg_content']); ?>
          </div>
        </div>
      <?php endforeach;?>
    </div>
  </div>
</section>
<?php else: ?>
  <?php foreach ( $accordion_group as $ag_key => $accordion ) : ?>
  <?php reset($accordion_group) ?>
  <div class="icon-expand-container result">
    <div class="icon-expand-title grid-x">
      <?php if ($use_icon === true ) :?>
        <?php if ( isset( $accordion['phila_custom_wysiwyg']['phila_accordion_icon'] ) ) { ?>
            <div class="cell shrink mrm mtxs"><i class="<?php echo $accordion['phila_custom_wysiwyg']['phila_accordion_icon'] ?> fa-2x"></i></div> 
        <?php } else if (isset($custom_icon ) && $custom_icon === true ) { ?>          
            <div class="cell shrink mrm mtxs"><i class="<?php echo rwmb_meta('phila_v2_icon') ?> fa-2x"></i></div> 
        <?php } else { ?>
          <div class="cell shrink mrm mtxs"><i class="fas fa-tasks fa-2x"></i></div> 
        <?php } ?>
      <?php endif; ?>
      <div class="cell auto">
        <?php echo apply_filters( 'the_content', $accordion['phila_custom_wysiwyg']['phila_wysiwyg_title'] ); ?>
      </div>
    </div>
    <a href="#" data-toggle="icon-expand" class="icon-expand-link" id="<?php echo sanitize_title_with_dashes($accordion['phila_custom_wysiwyg']['phila_wysiwyg_title']) . '-control' ?>"> More + </a>
    <div class="icon-expand-content mvm " aria-controls="<?php echo sanitize_title_with_dashes($accordion['phila_custom_wysiwyg']['phila_wysiwyg_title']) . '-control' ?>" aria-expanded="false">
      <?php echo apply_filters( 'the_content', $accordion['phila_custom_wysiwyg']['phila_wysiwyg_content']); ?>
    </div>
  </div>
  <?php end( $accordion_group) ; ?>
  <?php if ($ag_key != key($accordion_group) ) :?>
    <hr class="icon-expand-hr">
  <?php endif ?>
  <?php endforeach;?>
<?php endif; ?>
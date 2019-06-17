<?php
  /* Required vars:
    $accordion_title - string
    $accordion_group - array
    Optional:
    $is_icon_template - Boolean
  */
?>
<?php if (!isset($is_icon_template)) : ?>
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
<section class="mvl">
  <div class="phm-mu">
    <?php foreach ($accordion_group as $accordion ) : ?>
    <div class="icon-expand-container">
      <div class="icon-expand-title grid-x">
        <div class="cell shrink mrm mtxs"><i class="fas fa-tasks fa-2x"></i></div>
        <div class="cell auto">
          <?php echo $accordion['phila_custom_wysiwyg']['phila_wysiwyg_title'] ?>
        </div>
        </div>
      <a href="#" data-toggle="icon-expand" class="icon-expand-link" id="<?php echo sanitize_title_with_dashes($accordion['phila_custom_wysiwyg']['phila_wysiwyg_title']) . '-control' ?>"> More + </a>
      <div class="icon-expand-content mvm " aria-controls="<?php echo sanitize_title_with_dashes($accordion['phila_custom_wysiwyg']['phila_wysiwyg_title']) . '-control' ?>" aria-expanded="false">
        <?php echo apply_filters( 'the_content', $accordion['phila_custom_wysiwyg']['phila_wysiwyg_content']); ?>
      </div>
    </div>
    <hr class="mhn">
    <?php endforeach;?>
  </div>
</section>
<?php endif; ?>

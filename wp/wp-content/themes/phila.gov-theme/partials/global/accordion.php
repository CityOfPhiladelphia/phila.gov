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
  <div class="grid-container">
    <?php foreach ($accordion_group as $accordion ) : ?>
      <?php echo $accordion['phila_custom_wysiwyg']['phila_wysiwyg_title'] ?>

      <div class="expandable" aria-controls="<?php echo sanitize_title_with_dashes($accordion['phila_custom_wysiwyg']['phila_wysiwyg_title']) . '-control' ?>" aria-expanded="false">
        <?php echo apply_filters( 'the_content', $accordion['phila_custom_wysiwyg']['phila_wysiwyg_content']); ?>
      </div>
      <a href="#" data-toggle="expandable" class="float-right" id="<?php echo sanitize_title_with_dashes($accordion['phila_custom_wysiwyg']['phila_wysiwyg_title']) . '-control' ?>"> More + </a>
      
    <?php endforeach;?>
  </div>
</section>
<?php endif; ?>

<?php
  /* Required vars:
    $accordion_title - string
    $accordion_group - array
  */
?>
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

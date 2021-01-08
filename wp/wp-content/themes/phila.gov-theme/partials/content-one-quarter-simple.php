<?php
/*
 *
 * Partial for one quarter page heading on a deptartment page - in the page content 
 */
?>

<div class="one-quarter-layout">
  <div class="row one-quarter-row mvxl">
    <div class="medium-6 columns">
      <h3 id="<?php echo sanitize_title_with_dashes($wysiwyg_heading, null, 'save') ?>"><?php echo $wysiwyg_heading; ?></h3>
    </div>
    <div class="medium-18 columns pbxl">
      <?php if ((!empty($wysiwyg_content))) : ?>
        <?php echo apply_filters('the_content', $wysiwyg_content); ?>
      <?php endif ?>
    </div>
    </section>
  </div>
</div>
<hr class="margin-auto">
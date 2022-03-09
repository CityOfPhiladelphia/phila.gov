<?php
/*
 *
 * Partial for single heading group
 */
?>

<?php $is_address = isset($current_row['phila_full_options']['phila_content_heading_group']['phila_address_select']) ? $current_row['phila_full_options']['phila_content_heading_group']['phila_address_select'] : ''; 
?>
<?php $stepped_select = isset($current_row['phila_full_options']['phila_content_heading_group']['phila_stepped_select']) ? $current_row['phila_full_options']['phila_content_heading_group']['phila_stepped_select'] : '';
?>
<?php $contact_content = isset($current_row['phila_full_options']['phila_content_heading_group']['phila_std_address']) ? $current_row['phila_full_options']['phila_content_heading_group']['phila_std_address'] : ''; 

$source_template =  rwmb_meta( 'phila_template_select'); 
?>
<?php if ($source_template !== 'custom_content') : ?>
  <div class="grid-container">
<?php endif; ?>
<div class="grid-x">
  <div class="cell">
    <section>
      <?php if ($wysiwyg_heading != '') : ?>
        <h3 class="black bg-ghost-gray phm-mu mbm" id="<?php echo sanitize_title_with_dashes($wysiwyg_heading, null, 'save') ?>"><?php echo $wysiwyg_heading; ?></h3>
      <?php endif; ?>
      <div class="phm-mu">
        <?php if ((!empty($wysiwyg_content) || (!empty($is_address)))) : ?>
          <?php echo apply_filters('the_content', $wysiwyg_content); ?>
          <?php include(locate_template('partials/global/contact-information.php')); ?>
        <?php endif ?>
        <?php if (!empty($stepped_select)) :
          $steps = phila_extract_stepped_content($current_row['phila_full_options']['phila_content_heading_group']['phila_stepped_content']);?>
          <div class="phm-mu">
            <?php include(locate_template('partials/stepped-content.php')); ?>
          </div>
          <?php endif; ?>
      </div>
    </section>
  </div>
</div>
<?php if ($source_template !== 'custom_content') : ?>
</div>
<?php endif; ?>
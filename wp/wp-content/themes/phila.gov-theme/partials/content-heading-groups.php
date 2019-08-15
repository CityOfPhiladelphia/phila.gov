<?php
/*
 *
 * Partial for heading groups
 * Required params: $heading_groups
 */
?>
<?php
  if ( !isset( $heading_content ) )  {
    $heading_content = phila_extract_clonable_wysiwyg( $heading_groups );
  }

  if ( !empty($heading_content) ) : ?>
  <?php foreach ( $heading_content as $content ): ?>

  <div class="grid-x mvl">
    <div class="cell">
      <section>
        <?php $wysiwyg_heading = isset($content['phila_wysiwyg_heading']) ? $content['phila_wysiwyg_heading'] : '';?>
        <?php $wysiwyg_alt_heading = isset($content['phila_heading_alt']) ? $content['phila_heading_alt'] : '';?>
        <?php if (phila_get_selected_template() === 'prog_landing_page'): ?>
          <h2 class="contrast" id="<?php echo sanitize_title_with_dashes($wysiwyg_heading, null, 'save')?>"><?php echo $wysiwyg_heading; ?></h3>
        <?php elseif (phila_get_selected_template() === 'guide_sub_page') : ?>

          <h2 id="<?php echo !empty($wysiwyg_alt_heading) ? sanitize_title_with_dashes($wysiwyg_alt_heading, null, 'save') : sanitize_title_with_dashes($wysiwyg_heading, null, 'save')?>" data-magellan-target="<?php echo !empty($wysiwyg_alt_heading) ? sanitize_title_with_dashes($wysiwyg_alt_heading, null, 'save') : sanitize_title_with_dashes($wysiwyg_heading, null, 'save')?>"><?php echo $wysiwyg_heading; ?></h2>
        <?php else : ?>
          <?php if ( $wysiwyg_heading != '' ): ?>
            <h3 class="black bg-ghost-gray phm-mu mbm" id="<?php echo sanitize_title_with_dashes($wysiwyg_heading, null, 'save')?>"><?php echo $wysiwyg_heading; ?></h3>
          <?php endif; ?>
      <?php endif; ?>
      <div class="<?php echo (phila_get_selected_template() === 'prog_landing_page') || (phila_get_selected_template() === 'guide_sub_page') ? '' : 'phm-mu'; ?>">
        <?php $wysiwyg_content = isset( $content['phila_unique_wysiwyg_content'] ) ? $content['phila_unique_wysiwyg_content'] : ''; ?>
        <?php $is_address = isset( $content['phila_address_select'] ) ? $content['phila_address_select'] : ''; ?>

        <?php if ( (!empty($wysiwyg_content) || (!empty($is_address) ) ) ) : ?>
          <?php echo apply_filters( 'the_content', $wysiwyg_content ) ;?> 

          <?php $contact_content = isset($content['phila_std_address']) ? $content['phila_std_address'] : ''; ?>

          <?php include( locate_template( 'partials/global/contact-information.php' ) );?>

        <?php endif ?>
        <?php if ( !empty($content['phila_stepped_select']) ) :?>
          <?php $steps = phila_extract_stepped_content($content['phila_stepped_content']);?>
          <div class="phm-mu">
            <?php include( locate_template( 'partials/stepped-content.php' ) );?>
          </div>
        <?php endif;?>
      </div>

      </section>
    </div>
  </div>
<?php endforeach; ?>
<?php endif; ?>

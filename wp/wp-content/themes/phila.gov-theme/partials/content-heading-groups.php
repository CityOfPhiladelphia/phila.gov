<?php
/*
 *
 * Partial for heading groups
 * Required params: $heading_groups
 */
?>
<?php

  $heading_content = phila_extract_clonable_wysiwyg( $heading_groups );
  if ( !empty($heading_content) ) : ?>
  <?php foreach ( $heading_content as $content ): ?>

  <div class="row mvl">
    <div class="columns">
      <section>
        <?php $wysiwyg_heading = isset($content['phila_wysiwyg_heading']) ? $content['phila_wysiwyg_heading'] : '';?>
        <?php if (phila_get_selected_template() === 'prog_landing_page'): ?>
          <h2 class="contrast" id="<?php echo sanitize_title_with_dashes($wysiwyg_heading, null, 'save')?>"><?php echo $wysiwyg_heading; ?></h3>
        <?php else : ?>
        <?php if ( $wysiwyg_heading != '' ): ?>
          <h3 class="black bg-ghost-gray phm-mu mbm" id="<?php echo sanitize_title_with_dashes($wysiwyg_heading, null, 'save')?>"><?php echo $wysiwyg_heading; ?></h3>
        <?php endif; ?>
      <?php endif; ?>
      <div class="<?php echo phila_get_selected_template() == 'prog_landing_page' ? '' : 'phm-mu'; ?>">
        <?php $wysiwyg_content = isset( $content['phila_unique_wysiwyg_content'] ) ? $content['phila_unique_wysiwyg_content'] : ''; ?>
        <?php $is_address = isset( $content['phila_address_select'] ) ? $content['phila_address_select'] : ''; ?>

        <?php if ( (!empty($wysiwyg_content) || (!empty($is_address) ) ) ) : ?>
          <?php echo apply_filters( 'the_content', $wysiwyg_content ) ;?> 

          <?php $contact_content = $content['phila_std_address']; ?>

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

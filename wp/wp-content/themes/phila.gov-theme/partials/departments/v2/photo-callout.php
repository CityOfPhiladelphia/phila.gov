<?php

if (phila_get_selected_template() === 'homepage_v2') {
    $toggle = rwmb_meta('phila_v2_photo_callout_block__image_toggle');
    $sub_header = rwmb_meta('phila_v2_photo_callout_block__txt-sub-header');
    $header = rwmb_meta('phila_v2_photo_callout_block__txt-header');
    $description = rwmb_meta('phila_v2_photo-callout-block__desc');
    $button_link = rwmb_meta('phila_v2_photo_callout_block__link');
    $button_text = rwmb_meta('phila_v2_photo-callout-block__txt-btn-label');
    $icon = rwmb_meta('phila_v2_photo-callout-block__txt-icon');
    $get_photo = rwmb_meta('phila_v2_photo_callout_block__photo');
    $photo = reset($get_photo)['full_url'];
    $alt = reset($get_photo)['alt'];
} else if ( phila_get_selected_template() === 'collection_page_v2' ) {
    $photo_callout = isset($current_row['phila_callout_group']['phila_callout_v2']) ? $current_row['phila_callout_group']['phila_callout_v2'] : '';
    $header = $photo_callout['large_title'];
    $sub_header = $photo_callout['small_title'];
    $description = $photo_callout['description'];
    $button_link = $photo_callout['button_url'];
    $button_text = $photo_callout['button_text'];
    $icon = $photo_callout['button_icon'];
} else {
    $photo_callout = isset($current_row['phila_full_options']['photo_callout']) ? $current_row['phila_full_options']['photo_callout'] : '';
    $toggle = isset($photo_callout['phila_v2_photo_callout_block__image_toggle']) ? $photo_callout['phila_v2_photo_callout_block__image_toggle'] : '';
    $header = $photo_callout['phila_v2_photo_callout_block__txt-header'];
    $sub_header = $photo_callout['phila_v2_photo_callout_block__txt-sub-header'];
    $description = $photo_callout['phila_v2_photo-callout-block__desc'];
    $button_link = $photo_callout['phila_v2_photo_callout_block__link'];
    $button_text = $photo_callout['phila_v2_photo-callout-block__txt-btn-label'];
    $icon = $photo_callout['phila_v2_photo-callout-block__txt-icon'];
    $photo = wp_get_attachment_url( $photo_callout['phila_v2_photo_callout_block__photo'][0]);
}

?>

<?php if (!empty($header)): ?>

<section class="row">
  <div class="grid-container columns">
    <div class="mvl grid-x large-padding-collapse medium-padding-collapse small-padding-collapse small-margin-collapse align-center photo-callout-block ">
        <?php if (phila_get_selected_template() === 'collection_page_v2') : ?>
            <div class="photo-callout-block__txt medium-24 cell">
                <div class="grid align-center-middle grid-x">
                    <div class="cell small-22 large-18 ">
                        <h4 class="h5 photo-callout-block__txt-sub-header"><?php echo $sub_header ?></h4>
                        <h2 class="h2 photo-callout-block__txt-header"><?php echo $header ?></h2>
                        <p class="photo-callout-block__desc"><?php echo $description ?></p>
                        <?php if($button_link): ?>
                        <a href="<?php echo $button_link ?>" class="photo-callout-block__txt-btn button icon">
                            <div class="valign">
                                <?php if ( !empty( $icon ) ) : ?>
                                    <i class="<?php echo $icon ?> valign-cell fa-3x" aria-hidden="true"></i>
                                <?php endif; ?>
                                <div class="button-label valign-cell"><?php echo $button_text ?></div>
                            </div>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php elseif ((empty( $toggle ) || !isset( $toggle )) && empty($toggle)) : ?>
            <div class="photo-callout-block__txt large-20 medium-20 small-20 cell callout-photo-toggle-false">
              <div class="grid align-center-middle grid-x">
                  <div class="cell small-22 large-18 ">
                      <div class="valign">
                          <?php if ( !empty( $icon ) ) : ?>
                              <i class="<?php echo $icon ?> valign-cell fa-3x" aria-hidden="true"></i>
                          <?php endif; ?>
                          <h2 id="<?php echo sanitize_title_with_dashes( $header); ?>" class="h2 photo-callout-block__txt-header callout-photo-toggle-false"><?php echo $header ?></h2>
                          <?php if($button_link): ?>
                              <a href="<?php echo $button_link ?>" class="photo-callout-block__txt-btn button icon callout-photo-toggle-false">
                                  <div class="button-label valign-cell"><?php echo $button_text ?></div>
                              </a>
                          <?php endif; ?>
                      </div>
                  </div>
              </div>
            </div>

        <?php elseif (!empty($toggle)  || !empty($photo) ) : ?>
          <div class="photo-callout-block__img large-14 medium-12 small-20 cell">
              <img src="<?php echo $photo ?>" alt="" class="float-center">
          </div>

          <div class="photo-callout-block__txt large-10 medium-12 small-20 cell">
            <div class="grid align-center-middle grid-x">
                <div class="cell small-22 large-18 ">
                    <h4 class="h5 photo-callout-block__txt-sub-header"><?php echo $sub_header ?></h4>
                    <h2 class="h2 photo-callout-block__txt-header"><?php echo $header ?></h2>
                    <p class="photo-callout-block__desc"><?php echo $description ?></p>
                    <?php if($button_link): ?>
                    <a href="<?php echo $button_link ?>" class="photo-callout-block__txt-btn button icon">
                        <div class="valign">
                            <?php if ( !empty( $icon ) ) : ?>
                                <i class="<?php echo $icon ?> valign-cell fa-3x" aria-hidden="true"></i>
                            <?php endif; ?>
                            <div class="button-label valign-cell"><?php echo $button_text ?></div>
                        </div>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
          </div>

      <?php endif; ?>
</section>
<?php endif; ?>
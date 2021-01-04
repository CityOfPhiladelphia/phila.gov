

<?php

if (phila_get_selected_template() === 'homepage_v2') {
    $toggle = rwmb_meta('phila_v2_photo_callout_block__image_toggle');
    $subHeader = rwmb_meta('phila_v2_photo_callout_block__txt-sub-header');
    $header = rwmb_meta('phila_v2_photo_callout_block__txt-header');
    $description = rwmb_meta('phila_v2_photo-callout-block__desc');
    $btnLink = rwmb_meta('phila_v2_photo_callout_block__link');
    $btnTxt = rwmb_meta('phila_v2_photo-callout-block__txt-btn-label');
    $btnIcon = rwmb_meta('phila_v2_photo_callout_block__icon');
    $icon = rwmb_meta('phila_v2_photo-callout-block__txt-icon');
    $get_photo = rwmb_meta('phila_v2_photo_callout_block__photo');
    $photo = reset($get_photo)['full_url'];
    $alt = reset($get_photo)['alt'];

  }else{
    $photo_callout = isset($current_row['phila_full_options']['photo_callout']) ? $current_row['phila_full_options']['photo_callout'] : '';
    $toggle = isset($photo_callout['phila_v2_photo_callout_block__image_toggle']) ? $photo_callout['phila_v2_photo_callout_block__image_toggle'] : '';
    $header = $photo_callout['phila_v2_photo_callout_block__txt-header'];
    $subHeader = $photo_callout['phila_v2_photo_callout_block__txt-sub-header'];    
    $description = $photo_callout['phila_v2_photo-callout-block__desc'];
    $btnLink = $photo_callout['phila_v2_photo_callout_block__link'];
    $btnTxt = $photo_callout['phila_v2_photo-callout-block__txt-btn-label'];
    $icon = $photo_callout['phila_v2_photo-callout-block__txt-icon'];
    $photo = wp_get_attachment_url( $photo_callout['phila_v2_photo_callout_block__photo'][0]);
  }

?>

<?php if (!empty($header)): ?>

<section class="row">
  <div class="grid-container columns">
    <div class="mvl grid-x large-padding-collapse medium-padding-collapse small-padding-collapse small-margin-collapse align-center photo-callout-block ">
        <?php if ((empty( $photo_callout['phila_v2_photo_callout_block__image_toggle'] ) || !isset( $photo_callout['phila_v2_photo_callout_block__photo'] )) && empty($toggle)) : ?>
            <div class="photo-callout-block__txt large-20 medium-20 small-20 cell callout-photo-toggle-false">
              <div class="grid align-center-middle grid-x grid-padding-x">
                  <div class="cell small-22 large-18 ">
                      <div class="valign">
                          <?php if ( !empty( $icon ) ) : ?>
                              <i class="<?php echo $icon ?> valign-cell fa-3x" aria-hidden="true"></i>
                          <?php endif; ?>
                          <h2 class="h2 photo-callout-block__txt-header callout-photo-toggle-false"><?php echo $header ?></h2>
                          <?php if($btnLink): ?>
                              <a href="<?php echo $btnLink ?>" class="photo-callout-block__txt-btn button icon callout-photo-toggle-false">
                                  <div class="button-label valign-cell"><?php echo $btnTxt ?></div>
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
            <div class="grid align-center-middle grid-x grid-padding-x">
                <div class="cell small-22 large-18 ">
                    <h4 class="h5 photo-callout-block__txt-sub-header"><?php echo $subHeader ?></h4>
                    <h2 class="h2 photo-callout-block__txt-header"><?php echo $header ?></h2>
                    <p class="photo-callout-block__desc"><?php echo $description ?></p>
                    <?php if($btnLink): ?>
                    <a href="<?php echo $btnLink ?>" class="photo-callout-block__txt-btn button icon">
                        <div class="valign">
                            <?php if ( !empty( $icon ) ) : ?>
                                <i class="<?php echo $icon ?> valign-cell fa-3x" aria-hidden="true"></i>
                            <?php endif; ?>
                            <div class="button-label valign-cell"><?php echo $btnTxt ?></div>
                        </div>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
          </div>

      <?php endif; ?>
</section>
<?php endif; ?>


<?php
    $photo_callout = $current_row['phila_full_options']['photo_callout'];
    $header = $photo_callout['phila_v2_photo_callout_block__txt-header'];
    $subHeader = $photo_callout['phila_v2_photo_callout_block__txt-sub-header'];    
    $description = $photo_callout['phila_v2_photo-callout-block__desc'];
    $btnLink = $photo_callout['phila_v2_photo_callout_block__link'];
    $btnTxt = $photo_callout['phila_v2_photo-callout-block__txt-btn-label'];

?>

<section class="row">
    <div class="grid-container columns">
    <div class="mvl grid-x large-padding-collapse medium-padding-collapse small-padding-collapse small-margin-collapse align-center photo-callout-block ">
        <?php if (empty( $photo_callout['phila_v2_photo_callout_block__image_toggle'] ) || !isset( $photo_callout['phila_v2_photo_callout_block__photo'] )) : ?>

            <div class="photo-callout-block__txt large-20 medium-12 small-20 cell callout-photo-toggle-false">
                    <div class="grid align-center-middle grid-x grid-padding-x">
                        <div class="cell small-22 large-18 ">
                            <div class="valign">        
                                <i class="far fa-map valign-cell fa-3x"></i>
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

        <?php elseif (!empty( $photo_callout['phila_v2_photo_callout_block__image_toggle'] )) : ?>

            <div class="photo-callout-block__img large-14 medium-12 small-20 cell">
                <img src="<?php echo wp_get_attachment_url( $photo_callout['phila_v2_photo_callout_block__photo'][0] ); ?>" alt="" class="float-center">
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
                                    <i class="far fa-map valign-cell"></i>
                                    <div class="button-label valign-cell"><?php echo $btnTxt ?></div>
                                </div>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
            </div>

        <?php endif; ?>
</section>

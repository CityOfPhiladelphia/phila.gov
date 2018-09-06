<?php if (rwmb_meta( 'phila_v2_photo_callout_block__photo' )): ?>

<?php
    $imagePath = rwmb_meta('phila_v2_photo_callout_block__photo', array('size' => 'medium', 'limit' => 1))[0]['url'];
    $subHeader = rwmb_meta('phila_v2_photo_callout_block__txt-sub-header');
    $header = rwmb_meta('phila_v2_photo_callout_block__txt-header');
    $description = rwmb_meta('phila_v2_photo-callout-block__desc');
    $btnLink = rwmb_meta('phila_v2_photo_callout_block__link');
    $btnTxt = rwmb_meta('phila_v2_photo-callout-block__txt-btn-label');
?>


<section class="row">
    <div class="grid-container columns">
    <div class="mvl grid-x large-padding-collapse medium-padding-collapse small-padding-collapse small-margin-collapse align-center photo-callout-block ">

        <div class="photo-callout-block__img large-14 medium-12 small-20 cell">
            <img src="<?php echo $imagePath ?>" alt="" class="float-center">
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
                            <i class="fa fa-map-o valign-cell"></i>
                            <div class="button-label valign-cell"><?php echo $btnTxt ?></div>
                          </div>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
        </div>


</section>
<?php endif; ?>

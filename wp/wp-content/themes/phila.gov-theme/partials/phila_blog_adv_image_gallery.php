<?php
/*
Partial for Advanced Blog Posts Image Gallery Component
*/
?>
<div class="mvxl">
    <h2> <?php echo $title ?></h2>
    <p><?php echo $description ?></p>
    <div class="grid-x medium-16 medium-centered align-middle">
        <div class="slideshow-container">
            <?php foreach ($images as $key => $image) {
                $media_credit = get_post_meta($image['phila_images'][0])['phila_media_credit'][0];
                $media_caption = get_post($image['phila_images'][0])->post_excerpt;
                $image_url =  wp_get_attachment_url($image['phila_images'][0]);
                $image_key = $image['phila_images'][0] + $key; ?>
                <div class="mySlides<?php if ($key == 0) {
                                        echo " active";
                                    } ?>">
                    <img src="<?php echo $image_url ?>" class="lightbox-link lightbox-link--feature" data-open="phila-lightbox-ig-<?php echo $image_key; ?>">
                    <div id="phila-lightbox-ig-<?php echo $image_key; ?>" data-reveal class="lb-ig reveal reveal--auto center" data-image-url="<?php echo $image_url ?>" data-media-credit="<?php echo $media_credit ?>" data-media-caption="<?php echo $media_caption ?>" data-key="<?php echo $image_key ?>"></div>
                    <button class="prev"><i class="image-gallery-arrows fa-solid fa-arrow-left"></i></button>
                    <button class="next"><i class="image-gallery-arrows fa-solid fa-arrow-right"></i></button>
                    <?php if ($media_credit != null || $media_caption != null) { ?>
                        <div class="text">
                            <?php if ($media_credit != null) { ?>
                                Photo by: <?php echo $media_credit ?>
                                <br>
                            <?php } ?>
                            <?php if ($media_caption != null) { ?>
                                <?php echo $media_caption ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="dots">
                <?php
                for ($i = 0; $i < count($images); $i++) {
                    $isActive = ($i === 0) ? 'active' : '';
                ?>
                    <button data-slide="<?php echo $i + 1; ?>"><span class="dot <?php echo $isActive; ?>"></span></button>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>
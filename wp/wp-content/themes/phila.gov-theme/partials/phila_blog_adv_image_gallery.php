<?php
/*
Partial for Advanced Blog Posts Image Gallery Component
*/
?>
<h2> <?php echo $title ?></h2>
<p><?php echo $description ?></p>
<div class="slideshow-container">
    <?php
    foreach ($images as $key => $image) {
        $media_credit = get_post_meta($image['phila_images'][0])['phila_media_credit'][0];
        $media_caption = get_post($image['phila_images'][0])->post_excerpt;
    ?>

        <div class="mySlides<?php if($key == 0) { echo " active"; } ?>">
            <img src="<?php echo wp_get_attachment_url($image['phila_images'][0]) ?>" class = "lightbox-link lightbox-link--feature" data-open="phila-lightbox-feature">
            <a class="prev"><i class="image-gallery-arrows fa-solid fa-arrow-left"></i></a>
            <a class="next"><i class="image-gallery-arrows fa-solid fa-arrow-right"></i></a>
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
        <span class="dot <?php echo $isActive; ?>" data-slide="<?php echo $i + 1; ?>"></span>
    <?php
    }
    ?>
</div>
</div>
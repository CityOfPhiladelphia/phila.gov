<section class="row ">
    <div class="columns">
        <h2 class="contrast"><?= rwmb_meta('phila_v2_linked_image_grid__header'); ?></h2>
    </div>
    <div class="columns">
        <div class="grid-container phila-linked-image-grid">
        <div class="grid-x grid-margin-x align-spaced">
        <?php
            $group_values = rwmb_meta( 'phila_v2_linked_image_grid' );
            if ( ! empty( $group_values ) ):
        ?>
            <?php
                foreach ( $group_values as $group_value ):
                    $image_id  = isset( $group_value['phila_v2_linked_image_grid__image'][0]) ? $group_value['phila_v2_linked_image_grid__image'][0] : null;
                    $image     = RWMB_Image_Field::file_info( $image_id, array( 'size' => 'full' ) );
                    $imageUrl  = $image ? $image['full_url'] : '';
                    $linkTitle = $group_value['phila_v2_linked_image_grid__link']['link_text'];
                    $linkURL   = $group_value['phila_v2_linked_image_grid__link']['link_url'];
                    $linkIsExternal = isset($group_value['phila_v2_linked_image_grid__link']['is_external']) ? $group_value['phila_v2_linked_image_grid__link']['is_external'] : false;
                    if($linkURL):
             ?>

                    <div class="cell large-7 medium-7 small-18 phila-linked-image-grid__item">
                        <div class="columns">
                            <a class="phila-linked-image-grid__item-photo" href="<?= $linkURL ?>" target="_blank">
                                <?php if($imageUrl): ?> <img src="<?= $imageUrl ?>" alt=""><?php endif; ?>
                            </a>
                            <a href="<?= $linkURL ?>" target="_blank" class="phila-linked-image-grid__item-title <?= $linkIsExternal ? 'external' : ''  ?>"><?= $linkTitle ?></a>
                        </div>
                    </div>
                <?php endif; ?>

            <?php endforeach; ?>
        <?php endif; ?>
        </div>
        </div>
    </div>

</section>

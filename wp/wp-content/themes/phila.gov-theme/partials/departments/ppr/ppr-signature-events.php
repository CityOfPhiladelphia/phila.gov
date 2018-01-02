<section class="row ppr-signature-events">
    <div class="columns">
        <h2 class="contrast">Parks &amp; Rec's signature events</h2>
    </div>
    <div class="columns">
        <div class="grid-container">
        <div class="grid-x grid-margin-x align-spaced">
        <?php
            $group_values = rwmb_meta( 'phila_v2_ppr_signature_events' );
            if ( ! empty( $group_values ) ) {
                foreach ( $group_values as $group_value ) {
                    $image_id = isset( $group_value['phila_v2_ppr_sig_event__photo'][0]) ? $group_value['phila_v2_ppr_sig_event__photo'][0] : null;
                    $image    = RWMB_Image_Field::file_info( $image_id, array( 'size' => 'full' ) );
                    $imageUrl = $image ? $image['full_url'] : '';

                    phila_get_template_part('partials/departments/ppr/ppr-signature-event',
                        array(
                            'title'     => isset( $group_value['phila_v2_ppr_sig_event__header'] ) ? $group_value['phila_v2_ppr_sig_event__header'] : '',
                            'photo_url' => $imageUrl,
                            'link_url'  => isset( $group_value['phila_v2_ppr_sig_event__link'] ) ? $group_value['phila_v2_ppr_sig_event__link'] : ''
                        )
                    );
                }
            }
         ?>
        </div>
        </div>
    </div>

</section>



<?php
    $faq = $current_row['phila_full_options']['faq'];

    // $header = $faq['phila_v2_faq__txt-header'];

?>

<?php
/*
 *
 *  1/4 Heading Group Layout
 *
 */
?>
<?php
    $faq = $current_row['phila_full_options']['faq'];
    $accordion_group = $faq['accordion_group'];

    $faq_group = $faq['accordion_row'];
    $faq_search = $faq['accordion_search'];
    $heading_groups = $faq['phila_heading_groups'];
    $heading_content = phila_extract_clonable_wysiwyg( $heading_groups );
?>
<?php if ( !empty( $faq_search ) ): ?>
    <div class="row">
        <div class="small-24 columns results mbm">
        <div id="a-z-filter-list" class="faq-list">
        <form class="search mvl">
            <input class="search-field" type="text" placeholder="Begin typing to filter results...">
            <input type="submit" class="search-submit" value="Search">
        </form>
<?php endif;?>
<?php if ( !empty( $faq_group ) ): ?>
    <div id="a-z-list">
        <?php foreach ($faq_group as $faq_key => $faq): ?>
        <?php reset($faq_group);?>
        <!--1/4 Content-->
        <section class="a-z-group">
        <div class="one-quarter-layout">
            <div class="row one-quarter-row mvl">
            <div class="medium-6 columns item">
                <h3 id="<?php echo sanitize_title_with_dashes($faq['accordion_row_title']) ?>" class="phm-mu mtl mbm"><?php echo $faq['accordion_row_title'] ?></h3>
            </div>
        <?php
            $accordion_title = '';
            $accordion_group = $faq['accordion_group'];
            $is_full_width = false;
            $use_icon = false; ?>
            <div class="medium-18 columns pbxl mvl phm-mu list">
                <?php include(locate_template('partials/global/accordion.php')); ?>
            </div>
        <?php end( $faq_group ) ; ?>
        <?php if ($faq_key != key($faq_group) ) :?>
            <hr class="icon-expand-hr">
        <?php endif ?>
            </div>
        </section>

    <?php endforeach; ?>
    </div>
<?php endif; ?>
<div class="not-found h3" style="display:none">No results found for that search.</div>
<?php if ( !empty( $faq_search ) ): ?>
</div>
<?php endif; ?>

<?php if ( !empty($heading_content) ) : ?>
    <?php $last_key = phila_util_is_last_in_array( (array) $heading_content ); ?>

    <div class="one-quarter-layout">
    <?php foreach ( $heading_content as $key => $content ): ?>
        <div class="row one-quarter-row mvxl">
            <div class="medium-6 columns">
                <h3 id="<?php echo sanitize_title_with_dashes($content['phila_wysiwyg_heading'], null, 'save')?>"><?php echo $content['phila_wysiwyg_heading']; ?></h3>
            </div>

            <div class="medium-18 columns pbxl">
            <?php if ( isset($content['phila_expand_collapse'] ) ) : ?>
                <div class="expandable" aria-controls="<?php echo $content['phila_wysiwyg_heading'] . '-control' ?>" aria-expanded="false">
            <?php endif ?>
                <?php $wysiwyg_content = isset( $content['phila_unique_wysiwyg_content'] ) ? $content['phila_unique_wysiwyg_content'] : ''; ?>
                <?php $is_contact_info = isset( $content['phila_address_select'] ) ? $content['phila_address_select'] : ''; ?>
                <?php if ( (!empty($wysiwyg_content)  ) ) : ?>
                    <?php echo apply_filters( 'the_content', $wysiwyg_content ) ;?>
                <?php endif; ?>

                <?php if ( isset($content['phila_expand_collapse'] ) ) : ?>
                    </div><a href="#" data-toggle="expandable" class="float-right" id="<?php echo $content['phila_wysiwyg_heading'] . '-control' ?>"> More + </a>
                <?php endif; ?>

            <?php if (!empty($is_contact_info) ) : ?>
            <?php
            $address_1 = isset( $content['phila_std_address']['address_group']['phila_std_address_st_1'] ) ? $content['phila_std_address']['address_group']['phila_std_address_st_1'] : '';

            $address_2 = isset( $content['phila_std_address']['address_group']['phila_std_address_st_2'] ) ? $content['phila_std_address']['address_group']['phila_std_address_st_2'] : '';

            $city = isset( $content['phila_std_address']['address_group']['phila_std_address_city'] ) ? $content['phila_std_address']['address_group']['phila_std_address_city'] : '';

            $state = isset( $content['phila_std_address']['address_group']['phila_std_address_state'] ) ? $content['phila_std_address']['address_group']['phila_std_address_state'] : '';

            $zip = isset( $content['phila_std_address']['address_group']['phila_std_address_zip'] ) ? $content['phila_std_address']['address_group']['phila_std_address_zip'] : '';

            $phone = array(
                'area' => isset( $content['phila_std_address']['address_group']['phila_std_address_phone']['area'] ) ? $content['phila_std_address']['address_group']['phila_std_address_phone']['area'] : '',

                'co-code' => isset( $content['phila_std_address']['address_group']['phila_std_address_phone']['phone-co-code'] ) ? $content['phila_std_address']['address_group']['phila_std_address_phone']['phone-co-code'] : '',

                'subscriber-number' => isset( $content['phila_std_address']['address_group']['phila_std_address_phone']['phone-subscriber-number'] ) ? $content['phila_std_address']['address_group']['phila_std_address_phone']['phone-subscriber-number']  : '',
            );
            $email = isset( $content['phila_std_address']['phila_connect_general']['phila_connect_email'] ) ? $content['phila_std_address']['phila_connect_general']['phila_connect_email'] : '';

            $email_desc = isset( $content['phila_std_address']['phila_connect_general']['phila_connect_email_exp'] ) ? $content['phila_std_address']['phila_connect_general']['phila_connect_email_exp'] : '';

            $fax = isset( $content['phila_std_address']['phila_connect_general']['phila_connect_fax'] ) ? $content['phila_std_address']['phila_connect_general']['phila_connect_fax'] : '';

            $facebook = isset( $content['phila_std_address']['phila_connect_general']['phila_connect_social']['phila_connect_social_facebook'] ) ? $content['phila_std_address']['phila_connect_general']['phila_connect_social']['phila_connect_social_facebook'] : '';

            $twitter = isset( $content['phila_std_address']['phila_connect_general']['phila_connect_social']['phila_connect_social_twitter'] ) ? $content['phila_std_address']['phila_connect_general']['phila_connect_social']['phila_connect_social_twitter'] : '';

            $instagram = isset( $content['phila_std_address']['phila_connect_general']['phila_connect_social']['phila_connect_social_instagram'] ) ? $content['phila_std_address']['phila_connect_general']['phila_connect_social']['phila_connect_social_instagram'] : '';

            $youtube = isset( $content['phila_std_address']['phila_connect_general']['phila_connect_social']['phila_connect_social_youtube'] ) ? $content['phila_std_address']['phila_connect_general']['phila_connect_social']['phila_connect_social_youtube'] : '';

            $flickr = isset( $content['phila_std_address']['phila_connect_general']['phila_connect_social']['phila_connect_social_flickr'] ) ? $content['phila_std_address']['phila_connect_general']['phila_connect_social']['phila_connect_social_flickr'] : '';

            ?>
            <?php if ( !empty($address_1) || !empty($phone)) : ?>
            <div class="vcard">
                <?php if ( !empty($address_1) ) : ?>
                    <div class="pbm">
                        <span class="street-address"><?php echo $address_1; ?></span><br>
                        <?php if ( !empty($address_2) ) : ?>
                        <span class="street-address"><?php echo $address_2; ?></span><br>
                        <?php endif; ?>
                        <span class="locality"><?php echo $city; ?></span>, <span class="region" title="Pennsylvania"><?php echo $state; ?></span>
                        <span class="postal-code"><?php echo $zip; ?></span>
                <?php endif; ?>
                    </div>
            <?php endif; ?>
            <?php if ( !empty($phone) ) : ?>
                <div class="tel pbm">
                    <abbr class="type" title="voice"></abbr>
                    <div class="accessible">
                        <span class="type">Work</span> Phone:
                    </div>
                <?php $area = ( $phone['area'] != '' ) ? '(' . $phone['area'] . ') ' : '';

                    $co_code = ( $phone['co-code'] != '' ) ? $phone['co-code'] : '';

                    $subscriber_number = ( $phone['subscriber-number'] != '' ) ? '-' . $phone['subscriber-number'] : '';

                    $full_phone = $area . $co_code . $subscriber_number; ?>

                    <a href="tel:<?php echo preg_replace('/[^A-Za-z0-9]/', '', $full_phone); ?>" class="value"><?php echo $full_phone; ?></a>
                </div>
                <?php endif;?>
                <?php if ( !empty( $email ) ) : ?>
                    <div class="pbm"><a href="mailto:<?php echo $email?>"><?php echo $email ?></a> <?php echo ( $email_desc ) ? $email_desc : '' ?></div>
                <?php endif;?>
                <div class="ptxs">
                    <?php if ( !empty( $facebook ) ) : ?>
                        <span class="pvxs">
                            <a href="<?php echo $facebook ?>" class="phs" data-analytics="social">
                                <i class="fab fa-facebook fa-2x" title="Facebook" aria-hidden="true"></i>
                                <span class="show-for-sr">Facebook</span>
                            </a>
                        </span>
                    <?php endif; ?>
                    <?php if ( !empty( $twitter) ) : ?>
                        <span class="pvxs">
                            <a href="<?php echo $twitter; ?>" class="phs" data-analytics="social">
                                <i class="fab fa-twitter fa-2x" title="Twitter" aria-hidden="true"></i>
                                <span class="show-for-sr">Twitter</span>
                            </a>
                        </span>
                    <?php endif; ?>
                    <?php if ( !empty( $instagram) ) : ?>
                        <span class="pvxs">
                            <a href="<?php echo $instagram; ?>" class="phs" data-analytics="social">
                                <i class="fab fa-instagram fa-2x" title="Instagram" aria-hidden="true"></i>
                                <span class="show-for-sr">Instagram</span>
                            </a>
                        </span>
                    <?php endif; ?>
                    <?php if ( !empty( $youtube ) ) : ?>
                        <span class="pvxs">
                            <a href="<?php echo $youtube ?>" class="phs" data-analytics="social">
                                <i class="fab fa-youtube fa-2x" title="YouTube" aria-hidden="true"></i>
                                <span class="show-for-sr">Youtube channel</span>
                            </a>
                        </span>
                    <?php endif; ?>
                    <?php if ( !empty( $flickr ) ) : ?>
                        <span class="pvxs">
                            <a href="<?php echo $flickr; ?>" class="phs" data-analytics="social">
                                <i class="fab fa-flickr fa-2x" title="Flickr" aria-hidden="true"></i>
                                <span class="show-for-sr">Flickr stream</span>
                            </a>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif;?>
            <?php if ( !empty($content['phila_stepped_select']) ) :?>

                <?php $steps = phila_extract_stepped_content($content['phila_stepped_content']);

                include( locate_template( 'partials/stepped-content.php' ) ); ?>

            <?php endif;?>
            </div>
        </div>
        <?php if ($last_key != $key) : ?>
            <hr class="margin-auto"/>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
<!--1/4 Content-->
<?php endif; ?>

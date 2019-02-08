<?php
/*
 *
 *  1/4 Heading Group Layout
 *
 */
?>
<?php

  $heading_groups = rwmb_meta( 'phila_heading_groups' );
  $heading_content = phila_extract_clonable_wysiwyg( $heading_groups );
?>

<?php if ( !empty($heading_content) ) : ?>
  <?php $last_key = phila_util_is_last_in_array( (array) $heading_content ); ?>

  <div class="one-quarter-layout">
    <?php foreach ( $heading_content as $key => $content ): ?>
      <div class="row one-quarter-row mvl">
        <div class="medium-6 columns">

          <?php
            $heading_link_set = isset($content['phila_heading_link']) && $content['phila_heading_link'] !== '';
            $heading_link_new_tab = (isset($content['phila_heading_link_new_tab']) && $content['phila_heading_link_new_tab']) ? '_blank' : null;
            $heading_has_image = isset($content['phila_heading_image_selected']) && $content['phila_heading_image_selected'];
          ?>

          <?php
            // conditionally linked header wysiwyg content
            if ( isset( $content['phila_wysiwyg_heading'] ) &&  !$heading_has_image ):
          ?>

            <?php if($heading_link_set): ?>
              <a href="<?php echo $content['phila_heading_link'] ?>" target="<?=$heading_link_new_tab?>" >
                <h3><?php echo $content['phila_wysiwyg_heading']; ?></h3>
            <?php else: ?>
              <h3 id="<?php echo sanitize_title_with_dashes($content['phila_wysiwyg_heading'], null, 'save')?>"><?php echo $content['phila_wysiwyg_heading']; ?></h3>
            <?php endif;  ?>


          <?php elseif($heading_has_image ): ?>


              <?php
                // conditionally linked header image
                if($heading_link_set):
              ?>
                <a href="<?php echo $content['phila_heading_link'] ?>" target="<?=$heading_link_new_tab?>">
                  <img src="<?php echo $content['phila_heading_image']; ?> " alt="">
                </a>
              <?php else: ?>
                <img src="<?php echo $content['phila_heading_image']; ?> " alt="">
              <?php endif;  ?>


          <?php endif; //END if conditionally linked header wysiwyg content?>
        </div>

        <div class="medium-18 columns pbxl">
          <?php $wysiwyg_content = isset( $content['phila_unique_wysiwyg_content'] ) ? $content['phila_unique_wysiwyg_content'] : ''; ?>
          <?php $is_contact_info = isset( $content['phila_address_select'] ) ? $content['phila_address_select'] : ''; ?>
          <?php if ( (!empty($wysiwyg_content)  ) ) : ?>
            <?php echo apply_filters( 'the_content', $wysiwyg_content ) ;?>
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
<?php endif; ?>

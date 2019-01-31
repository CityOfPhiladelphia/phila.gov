<?php
/*
 *
 * Partial for heading groups
 * Required params: $heading_groups
 */
?>
<?php

  $heading_content = phila_extract_clonable_wysiwyg( $heading_groups );
  if ( !empty($heading_content) ) : ?>
  <?php foreach ( $heading_content as $content ): ?>

  <div class="row mvl">
    <div class="columns">
      <section>
        <?php $wysiwyg_heading = isset($content['phila_wysiwyg_heading']) ? $content['phila_wysiwyg_heading'] : '';?>
        <?php if (phila_get_selected_template() === 'prog_landing_page'): ?>
          <h2 class="contrast" id="<?php echo sanitize_title_with_dashes($wysiwyg_heading, null, 'save')?>"><?php echo $wysiwyg_heading; ?></h3>
        <?php else : ?>
        <?php if ( $wysiwyg_heading != '' ): ?>
          <h3 class="black bg-ghost-gray phm-mu mbm" id="<?php echo sanitize_title_with_dashes($wysiwyg_heading, null, 'save')?>"><?php echo $wysiwyg_heading; ?></h3>
        <?php endif; ?>
      <?php endif; ?>
      <div class="<?php echo phila_get_selected_template() == 'prog_landing_page' ? '' : 'phm-mu'; ?>">
        <?php $wysiwyg_content = isset( $content['phila_unique_wysiwyg_content'] ) ? $content['phila_unique_wysiwyg_content'] : ''; ?>
        <?php $is_address = isset( $content['phila_address_select'] ) ? $content['phila_address_select'] : ''; ?>

        <?php if ( (!empty($wysiwyg_content) || (!empty($is_address) ) ) ) : ?>
          <?php echo apply_filters( 'the_content', $wysiwyg_content ) ;?>
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
            <?php if ( !empty($is_address) ) : ?>
              <?php if ( !empty($address_1) || !empty($phone)) : ?>
              <div class="vcard mbm">
                <?php if ( !empty($address_1) ) : ?>
                  <span class="street-address"><?php echo $address_1; ?></span><br>
                  <?php if ( !empty($address_2) ) : ?>
                    <span class="street-address"><?php echo $address_2; ?></span><br>
                  <?php endif; //address_2 ?>
                  <span class="locality"><?php echo $city; ?></span>, <span class="region" title="Pennsylvania"><?php echo $state; ?></span>
                  <span class="postal-code"><?php echo $zip; ?></span>
                <?php endif //address_1?>
                <?php if ( !empty($phone) ) : ?>
                  <div class="tel">
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
                  <?php endif; //phone ?>
                </div>
              <?php endif; // address_1 || phone ?>
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
              <?php if ( !empty( $twitter ) ) : ?>
                <span class="pvxs">
                  <a href="<?php echo $twitter; ?>" class="phs" data-analytics="social">
                    <i class="fab fa-twitter fa-2x" title="Twitter" aria-hidden="true"></i>
                    <span class="show-for-sr">Twitter</span>
                  </a>
                </span>
              <?php endif; ?>
              <?php if ( !empty( $instagram ) ) : ?>
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
              <?php if ( !empty( $flickr ) )  : ?>
                <span class="pvxs">
                  <a href="<?php echo $flickr; ?>" class="phs" data-analytics="social">
                  <i class="fab fa-flickr fa-2x" title="Flickr" aria-hidden="true"></i>
                    <span class="show-for-sr">Flickr stream</span>
                  </a>
                </span>
              <?php endif; ?>
            </div>
            <?php endif;?>

            <?php if ( !empty($content['phila_stepped_select']) ) :?>
              <?php $steps =    phila_extract_stepped_content($content['phila_stepped_content']);?>
              <div class="phm-mu">
                <?php include( locate_template( 'partials/stepped-content.php' ) );?>
              </div>
            <?php endif;?>
          </div>
          <?php endif;?>

        </section>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

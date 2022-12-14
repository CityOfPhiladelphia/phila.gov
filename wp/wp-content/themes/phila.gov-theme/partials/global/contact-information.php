<?php
/* 
*
* Standard output for contact iformation within body copy
* $contact_content - Required - Array of phila_std_address['address_group']
* $is_address - Required - String - 'phila_address_select'
*/

$address_1 = isset( $contact_content['address_group']['phila_std_address_st_1'] ) ? $contact_content['address_group']['phila_std_address_st_1'] : '';

$address_2 = isset( $contact_content['address_group']['phila_std_address_st_2'] ) ? $contact_content['address_group']['phila_std_address_st_2'] : '';

$city = isset( $contact_content['address_group']['phila_std_address_city'] ) ? $contact_content['address_group']['phila_std_address_city'] : '';

$state = isset( $contact_content['address_group']['phila_std_address_state'] ) ? $contact_content['address_group']['phila_std_address_state'] : '';

$zip = isset( $contact_content['address_group']['phila_std_address_zip'] ) ? $contact_content['address_group']['phila_std_address_zip'] : '';

$phone = array(
  'area' => isset( $contact_content['address_group']['phila_std_address_phone']['area'] ) ? $contact_content['address_group']['phila_std_address_phone']['area'] : '',

  'co-code' => isset( $contact_content['address_group']['phila_std_address_phone']['phone-co-code'] ) ? $contact_content['address_group']['phila_std_address_phone']['phone-co-code'] : '',

'subscriber-number' => isset( $contact_content['address_group']['phila_std_address_phone']['phone-subscriber-number'] ) ? $contact_content['address_group']['phila_std_address_phone']['phone-subscriber-number']  : '',
);
$email = isset( $contact_content['phila_connect_general']['phila_connect_email'] ) ? $contact_content['phila_connect_general']['phila_connect_email'] : '';

$email_desc = isset( $contact_content['phila_connect_general']['phila_connect_email_exp'] ) ? $contact_content['phila_connect_general']['phila_connect_email_exp'] : '';

$fax = isset( $contact_content['phila_connect_general']['phila_connect_fax'] ) ? $contact_content['phila_connect_general']['phila_connect_fax'] : '';

$facebook = isset( $contact_content['phila_connect_general']['phila_connect_social']['phila_connect_social_facebook'] ) ? $contact_content['phila_connect_general']['phila_connect_social']['phila_connect_social_facebook'] : '';

$twitter = isset( $contact_content['phila_connect_general']['phila_connect_social']['phila_connect_social_twitter'] ) ? $contact_content['phila_connect_general']['phila_connect_social']['phila_connect_social_twitter'] : '';

$instagram = isset( $contact_content['phila_connect_general']['phila_connect_social']['phila_connect_social_instagram'] ) ? $contact_content['phila_connect_general']['phila_connect_social']['phila_connect_social_instagram'] : '';

$youtube = isset( $contact_content['phila_connect_general']['phila_connect_social']['phila_connect_social_youtube'] ) ? $contact_content['phila_connect_general']['phila_connect_social']['phila_connect_social_youtube'] : '';

$flickr = isset( $contact_content['phila_connect_general']['phila_connect_social']['phila_connect_social_flickr'] ) ? $contact_content['phila_connect_general']['phila_connect_social']['phila_connect_social_flickr'] : '';
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

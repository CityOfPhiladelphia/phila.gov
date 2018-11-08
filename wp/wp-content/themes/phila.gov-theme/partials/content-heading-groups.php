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

  <div class="row">
    <div class="columns">
      <section>
        <?php $wysiwyg_heading = isset($content['phila_wysiwyg_heading']) ? $content['phila_wysiwyg_heading'] : '';?>
        <?php if (phila_get_selected_template() === 'prog_landing_page'): ?>
          <h2 class="contrast" id="<?php echo sanitize_title_with_dashes($wysiwyg_heading, null, 'save')?>"><?php echo $wysiwyg_heading; ?></h3>
        <?php else : ?>
        <?php if ( $wysiwyg_heading != '' ): ?>
          <h3 class="black bg-ghost-gray phm-mu mtl mbm" id="<?php echo sanitize_title_with_dashes($wysiwyg_heading, null, 'save')?>"><?php echo $wysiwyg_heading; ?></h3>
        <?php endif; ?>
      <?php endif; ?>
      <div class="phm-mu">
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
            ?>

            <?php if ( !empty($address_1) || !empty($phone)) : ?>
            <div class="vcard mbm">
              <?php if ( !empty($address_1) ) : ?>
                <span class="street-address"><?php echo $address_1; ?></span><br>
                <?php if ( !empty($address_2) ) : ?>
                  <span class="street-address"><?php echo $address_2; ?></span><br>
                <?php endif; ?>
                <span class="locality"><?php echo $city; ?></span>, <span class="region" title="Pennsylvania"><?php echo $state; ?></span>
                <span class="postal-code"><?php echo $zip; ?></span>
               <?php endif ?>
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
                <?php endif;?>
            </div>
            <?php endif;?>
            <?php endif;?>
            <?php if ( !empty($content['phila_stepped_select']) ) :?>
              <?php $steps =    phila_extract_stepped_content($content['phila_stepped_content']);?>
              <div class="phm-mu">
                <?php include( locate_template( 'partials/stepped-content.php' ) );?>
              </div>
            <?php endif;?>
          </div>
        </section>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

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
  <div class="one-quarter-layout bdr-dark-gray">

    <?php foreach ( $heading_content as $content ): ?>


      <div class="row one-quarter-row mvl">

        <div class="medium-6 columns">

          <?php
            $heading_link_set     =  isset($content['phila_heading_link']) && $content['phila_heading_link'] !== '';
            $heading_link_new_tab = (isset($content['phila_heading_link_new_tab']) && $content['phila_heading_link_new_tab']) ? '_blank' : null;
            $heading_has_image    = isset($content['phila_heading_image_selected']) && $content['phila_heading_image_selected'];
          ?>

          <?php
            // conditionally linked header wysiwyg content
            if ( isset( $content['phila_wysiwyg_heading'] ) &&  !$heading_has_image ):
          ?>

            <?php if($heading_link_set): ?>
              <a href="<?= $content['phila_heading_link'] ?>" target="<?=$heading_link_new_tab?>" >
                <h3><?php echo $content['phila_wysiwyg_heading']; ?></h3>
            <?php else: ?>
              <h3 id="<?= sanitize_title_with_dashes($content['phila_wysiwyg_heading'], null, 'save')?>"><?php echo $content['phila_wysiwyg_heading']; ?></h3>
            <?php endif;  ?>


          <?php elseif($heading_has_image ): ?>


              <?php
                // conditionally linked header image
                if($heading_link_set):
              ?>
                <a href="<?= $content['phila_heading_link'] ?>" target="<?=$heading_link_new_tab?>">
                  <img src="<?= $content['phila_heading_image']; ?> " alt="">
                </a>
              <?php else: ?>
                <img src="<?= $content['phila_heading_image']; ?> " alt="">
              <?php endif;  ?>


          <?php endif; //END if conditionally linked header wysiwyg content?>
        </div>


        <div class="medium-18 columns pbxl">
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
              ?>

              <?php if ( $is_address == 1 ) : ?>
                <div class="vcard">
                  <span class="street-address"><?php echo $address_1; ?></span><br>
                  <?php if ( !empty($address_2) ) : ?>
                    <span class="street-address"><?php echo $address_2; ?></span><br>
                  <?php endif; ?>
                  <span class="locality"><?php echo $city; ?></span>, <span class="region" title="Pennsylvania"><?php echo $state; ?></span>
                  <span class="postal-code"><?php echo $zip; ?></span>
                </div>
              <?php endif;?>
            <?php endif;?>
            <?php if ( !empty($content['phila_stepped_select']) ) :?>

              <?php $steps =    phila_extract_stepped_content($content['phila_stepped_content']);

              include( locate_template( 'partials/stepped-content.php' ) );
              ?>

            <?php endif;?>
          </div>
        </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php
/*
 *
 * Partial for heading groups
 *
 */
 ?>
<?php
  $heading_groups = rwmb_meta( 'phila_heading_groups' );
  $heading_content = phila_extract_clonable_wysiwyg( $heading_groups );
  if ( !empty($heading_content) ) : ?>
  <?php foreach ( $heading_content as $content ): ?>

  <div class="row">
    <div class="columns">
      <section>
      <?php if ( isset( $content['phila_wysiwyg_heading'] ) ): ?>
        <h3 class="black bg-ghost-gray h2 phm-mu mtl mbm"><?php echo $content['phila_wysiwyg_heading']; ?></h3>
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

            <?php if ( !empty($content['phila_stepped_select']) ) :?>

              <?php $steps =    phila_extract_stepped_content($content['phila_stepped_content']);

              include( locate_template( 'partials/stepped-content.php' ) );
              ?>

            <?php endif;?>

          </div>
        <?php endif;?>
        </section>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

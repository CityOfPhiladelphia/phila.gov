<?php
  /*
  * Partial for displaying content that is stepped
  */ 
?>
<?php foreach ( $step_groups as $step_group ): ?>

<?php $steps = phila_extract_stepped_content($step_group); ?>
  <?php if ( !empty($step_group['service_how_stepped_content_intro'] ) ): ?>
      <h3><?php echo $step_group['service_how_stepped_content_intro'] ?> </h3>
  <?php endif; ?>

<div class="step-group">
  <?php $counter = 0; ?>
  <?php foreach ( $steps as $step ): ?>
    <?php $is_address = isset( $step['phila_address_step'] ) ? $step['phila_address_step'] : '';
    $counter++; ?>
    <div class="step-label"><?php echo $counter; ?></div>

    <div class="step">
      <div class="step-title"><?php echo isset( $step['phila_step_wysiwyg_heading'] ) ? $step['phila_step_wysiwyg_heading'] : ''; ?></div>
      <div class="step-content">
        <?php $step_wysiwyg_content = isset( $step['phila_step_wysiwyg_content'] ) ? $step['phila_step_wysiwyg_content'] : ''; ?>
        <?php echo apply_filters( 'the_content', $step_wysiwyg_content ); ?>
          <?php
          $address_1 = isset( $step['phila_std_address']['address_group']['phila_std_address_st_1'] ) ? $step['phila_std_address']['address_group']['phila_std_address_st_1'] : '';

          $address_2 = isset( $step['phila_std_address']['address_group']['phila_std_address_st_2'] ) ? $step['phila_std_address']['address_group']['phila_std_address_st_2'] : '';

          $city = isset( $step['phila_std_address']['address_group']['phila_std_address_city'] ) ? $step['phila_std_address']['address_group']['phila_std_address_city'] : '';

          $state = isset( $step['phila_std_address']['address_group']['phila_std_address_state'] ) ? $step['phila_std_address']['address_group']['phila_std_address_state'] : '';

          $zip = isset( $step['phila_std_address']['address_group']['phila_std_address_zip'] ) ? $step['phila_std_address']['address_group']['phila_std_address_zip'] : '';
          ?>

          <?php if ( !empty( $is_address ) ) : ?>
          <div class="vcard">
            <span class="street-address"><?php echo $address_1; ?></span><br>
            <?php if ( !empty($address_2) ) : ?>
              <span class="street-address"><?php echo $address_2; ?></span><br>
            <?php endif; ?>
            <span class="locality"><?php echo $city; ?></span>, <span class="region" title="Pennsylvania"><?php echo $state; ?></span>
            <span class="postal-code"><?php echo $zip; ?></span>
          </div>
          <?php endif; ?>

      </div>
    </div>
  <?php endforeach; ?>
</div>
<?php endforeach; ?>

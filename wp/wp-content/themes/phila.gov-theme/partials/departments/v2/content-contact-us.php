<?php
/**
 * Display Contact Page
 *
 * @package phila-gov
 */
?>
<?php
$contact_us_vars = rwmb_meta('phila_contact_us');
$contact_us = phila_loop_clonable_metabox($contact_us_vars);
?>
<?php foreach ($contact_us as $row) : ?>
      <?php
        $row_title = isset( $row['phila_contact_row_title']) ? $row['phila_contact_row_title'] : '';
      ?>
      <?php if ($row_title) :?>
        <div class="row columns">
          <h2 class="black bg-ghost-gray phm-mu mtl mbm"> <?php echo $row_title; ?> </h2>
        </div>
      <?php endif; ?>
      <div class="row phl columns custom-text-multi equal-height">
        <?php foreach($row['phila_contact_group'] as $column ): ?>
          <?php //TODO: Clean up ?>
          <?php $column_title = isset( $column['phila_contact_column_title']) ? $column['phila_contact_column_title'] : '';?>
          <?php $icon = isset( $column['phila_v2_icon']) ? $column['phila_v2_icon'] : '';?>
          <?php $address_1 = isset( $column['address_group']['phila_std_address_st_1']) ? $column['address_group']['phila_std_address_st_1'] : '';?>
          <?php $address_2 = isset( $column['address_group']['phila_std_address_st_2']) ? $column['address_group']['phila_std_address_st_2'] : '';?>
          <?php $address_city = isset( $column['address_group']['phila_std_address_city']) ? $column['address_group']['phila_std_address_city'] : '';?>
          <?php $address_state = isset( $column['address_group']['phila_std_address_state']) ? $column['address_group']['phila_std_address_state'] : '';?>
          <?php $address_zip = isset( $column['address_group']['phila_std_address_zip']) ? $column['address_group']['phila_std_address_zip'] : '';?>
          <?php $email = isset( $column['phila_v2_email']) ? $column['phila_v2_email'] : '';?>
          <?php $email = isset( $column['phila_v2_email']) ? $column['phila_v2_email'] : '';?>
          <?php $phone_area = isset( $column['phila_v2_phone']['area']) ? $column['phila_v2_phone']['area'] : '';?>
          <?php $phone_co = isset( $column['phila_v2_phone']['phone-co-code']) ? $column['phila_v2_phone']['phone-co-code'] : '';?>
          <?php $phone_subscriber = isset( $column['phila_v2_phone']['phone-subscriber-number']) ? $column['phila_v2_phone']['phone-subscriber-number'] : '';?>

          <?php $fax_area = isset( $column['phila_v2_fax']['area']) ? $column['phila_v2_fax']['area'] : '';?>
          <?php $fax_co = isset( $column['phila_v2_fax']['phone-co-code']) ? $column['phila_v2_fax']['phone-co-code'] : '';?>
          <?php $fax_subscriber = isset( $column['phila_v2_fax']['phone-subscriber-number']) ? $column['phila_v2_fax']['phone-subscriber-number'] : '';?>

          <?php $hours_day_start = isset( $column['phila_v2_hours']['day_start']) ? $column['phila_v2_hours']['day_start'] : '';?>

          <?php $hours_day_end = isset( $column['phila_v2_hours']['day_end']) ? $column['phila_v2_hours']['day_end'] : '';?>

          <?php $hours_time_start = isset( $column['phila_v2_hours']['time_start']) ? $column['phila_v2_hours']['time_start'] : '';?>

          <?php $hours_time_end = isset( $column['phila_v2_hours']['time_end']) ? $column['phila_v2_hours']['time_end'] : '';?>

          <?php $hours_other = isset( $column['phila_v2_hours']['hours_other']) ? $column['phila_v2_hours']['hours_other'] : '';?>

          <div class="columns medium-8 end phxl-mu pbl pbn-mu equal">
            <div class="vcard">
              <h3><?php if(!$icon == '') :?><i class="fa <?php echo $icon ?>" aria-hidden="true"></i> <?php endif; ?><?php echo $column_title ?></h3>

              <?php if ( !$address_1 == '' ) : ?>
                <div class="adr">
                  <span class="street-address"><?php echo $address_1; ?></span><br/>
                <?php endif; ?>
                <?php if ( !$address_2 == '' ) : ?>
                  <span class="street-address"><?php echo $address_2 ?></span><br/>
                <?php endif; ?>
                <?php if ( !$address_1 == '' ) : ?>
                  <span class="locality"><?php echo $address_city ?></span>, <span class="region"> <?php echo $address_state ?></span> <span class="postal-code"><?php echo $address_zip ?></span>
                </div>
              <?php endif; ?>

              <?php if( !$email == '' ): ?>
                <div class="email">
                  <span class="type">Email: </span><a href="mailto:<?php echo $email ?>" class="value"><?php echo $email ?></a>
                </div>
              <?php endif; ?>

              <?php if( !$phone_area == '' ) : ?>
                <div class="tel">
                  <span class="type">Phone: </span><a href="tel:<?php echo $phone_area . $phone_co . $phone_subscriber ?>" class="value"><?php echo '(' . $phone_area . ') '. $phone_co . '-' . $phone_subscriber  ?></a>
                </div>
              <?php endif; ?>

              <?php if( !$fax_area == '' ) : ?>
                <div class="tel">
                  <span class="type">Fax: </span><span class="value"><?php echo '(' . $fax_area . ') '. $fax_co . '-' . $fax_subscriber  ?></span>
                </div>
              <?php endif; ?>

              <?php if( !$hours_day_start == '' ) : ?>
                <div class="inline-block">Hours: </div>

                <div class="inline-block valign-top">
                  <?php if (!$hours_day_start == '') :?>
                    <?php echo $hours_day_start . ' &mdash; ' . $hours_day_end ?>, <br>
                    <?php echo str_replace(array('am','pm'),array('a.m.','p.m.'), $hours_time_start . ' &mdash; ' . $hours_time_end); ?>
                  <?php endif; ?>
                </div>
                <?php endif; ?>
              <?php if( !$hours_other == '' ) : ?>
                <div><?php echo $hours_other ?> </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
<?php endforeach; ?>

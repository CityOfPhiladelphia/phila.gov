<?php
/**
 * The template used for displaying Connect Panels
 *
 * @package phila-gov
 */
?>

<div class="large-8 columns connect">
  <h2 class="contrast">Connect</h2>
  <div class="vcard panel no-margin">
    <div>
      <?php if ( ! empty( $connect_vars['social'] ) ):
        $item_count = count( $connect_vars['social'] );
        $columns = phila_grid_column_counter( $item_count );
      ?>
        <div class="row mbn">
          <?php if ( isset( $connect_vars['social']['facebook'] ) && !$connect_vars['social']['facebook'] == '') : ?>
            <div class="small-<?php echo $columns;?> columns center pvxs">
              <a href="<?php echo $connect_vars['social']['facebook']; ?>" class="phs" data-analytics="social">
                <i class="fa fa-facebook fa-2x" title="Facebook" aria-hidden="true"></i>
                <span class="show-for-sr">Facebook</span>
              </a>
            </div>
          <?php endif; ?>
          <?php if ( isset( $connect_vars['social']['twitter'] ) && !$connect_vars['social']['twitter'] == '') : ?>
            <div class="small-<?php echo $columns;?> columns center pvxs">
              <a href="<?php echo $connect_vars['social']['twitter']; ?>" class="phs" data-analytics="social">
                <i class="fa fa-twitter fa-2x" title="Twitter" aria-hidden="true"></i>
                <span class="show-for-sr">Twitter</span>
              </a>
            </div>
          <?php endif; ?>
          <?php if ( isset( $connect_vars['social']['instagram'] ) && !$connect_vars['social']['instagram'] == '') : ?>
            <div class="small-<?php echo $columns;?> columns center pvxs">
              <a href="<?php echo $connect_vars['social']['instagram']; ?>" class="phs" data-analytics="social">
              <i class="fa fa-instagram fa-2x" title="Instagram" aria-hidden="true"></i>
                <span class="show-for-sr">Instagram</span>
              </a>
            </div>
          <?php endif; ?>
        </div>
        <hr>
      <?php endif; ?>
      <div>
        <div class="adr mbs">
          <?php if ( !$connect_vars['address']['st_1'] == '') : ?>
            <span class="street-address"><?php echo $connect_vars['address']['st_1']; ?></span><br/>
          <?php endif; ?>
          <?php if ( !$connect_vars['address']['st_2'] == '') : ?>
            <span class="street-address"><?php echo $connect_vars['address']['st_2']; ?></span><br/>
          <?php endif; ?>
          <?php if ( !$connect_vars['address']['st_1'] == '') : ?>
            <span class="locality"><?php echo $connect_vars['address']['city']; ?></span>, <span class="region" title="Pennsylvania"> <?php echo $connect_vars['address']['state']; ?></span> <span class="postal-code"><?php echo $connect_vars['address']['zip']; ?></span>
          <?php endif; ?>
        </div>
        <?php if ( !phila_util_is_array_empty($connect_vars['phone'])) : ?>
          <div class="tel">
            <?php
            $area = ( $connect_vars['phone']['area'] != '' ) ? '(' .  $connect_vars['phone']['area'] . ') ' : '';

            $co_code = ( $connect_vars['phone']['co-code'] != '' ) ? $connect_vars['phone']['co-code'] : '';

            $subscriber_number = ( $connect_vars['phone']['subscriber-number'] != '' ) ? '-' . $connect_vars['phone']['subscriber-number'] : '';

            $full_phone = $area . $co_code . $subscriber_number;
            ?>
            <span class="type <?php echo ( !$connect_vars['fax'] ) ? 'accessible' : '';?>">Phone: </span><a href="tel:<?php echo preg_replace('/[^A-Za-z0-9]/', '', $full_phone); ?>" class="value"><?php echo $full_phone; ?></a>
          </div>
        <?php endif; ?>
        <?php if ( !$connect_vars['fax'] == '') : ?>
         <div class="fax pbxs">
           <span class="type vcard-label">Fax: </span><?php echo $connect_vars['fax']; ?>
         </div>
        <?php endif; ?>
        <?php if ( !$connect_vars['email'] == '') : ?>
         <div class="email pbxs">
           <span class="vcard-label">Email: </span><a href="mailto:<?php echo $connect_vars['email']; ?>"><?php echo $connect_vars['email']; ?></a>
         </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

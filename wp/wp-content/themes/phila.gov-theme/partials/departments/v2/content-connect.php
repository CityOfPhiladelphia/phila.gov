<?php
/**
 * Display Connect Panels
 * TODO: Pull content from CPT
 *
 * @package phila-gov
 */
?>
<?php
$connect_panel = rwmb_meta('module_row_1_col_2_connect_panel');
$connect_vars = phila_connect_panel($connect_panel);
?>

<div class="large-7 columns connect vcard">
  <div class="row">
    <div class="columns">
      <h2 class="contrast">Connect</h2>
    </div>
  </div>
  <?php if ( !$connect_vars['address']['st_1'] == '') : ?>
    <div class="row collapse equal-height inside-border-group">
      <div class="small-5 columns equal center inside-border-group-item">
        <div class="valign">
          <div class="valign-cell">
            <i class="fa fa-map-marker fa-2x" aria-hidden="true"></i>
          </div>
        </div>
      </div>
      <div class="small-19 columns equal inside-border-group-item">
        <div class="valign">
          <div class="adr valign-cell phm pvl">
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
        </div>
      </div>
    </div>
  <?php endif; ?>
  <?php if ( !$connect_vars['email'] == '') : ?>
    <div class="row collapse equal-height inside-border-group">
      <div class="small-5 columns equal center inside-border-group-item">
        <div class="valign">
          <div class="valign-cell">
            <i class="fa fa-envelope-o fa-2x" aria-hidden="true"></i>
          </div>
        </div>
      </div>
      <div class="small-19 columns equal inside-border-group-item">
        <div class="valign">
          <div class="email valign-cell phm pvl">
            <span class="type accessible">Email: </span><a href="mailto:<?php echo $connect_vars['email']; ?>" class="value"><?php echo $connect_vars['email']; ?></a>
          </div>
      </div>
      </div>
    </div>
  <?php endif; ?>

  <?php if ( ( !$connect_vars['phone'] == '' ) || (!$connect_vars['fax'] == '' ) ) : ?>
  <div class="row collapse equal-height inside-border-group">
    <div class="small-5 columns equal center inside-border-group-item">
      <div class="valign">
        <div class="valign-cell">
          <i class="fa fa-phone fa-2x" aria-hidden="true"></i>
        </div>
      </div>
    </div>
    <div class="small-19 columns equal inside-border-group-item">
      <div class="valign">
        <div class="valign-cell phm pvl">
            <div class="tel">
              <span class="type <?php echo ( !$connect_vars['fax'] ) ? 'accessible' : '';?>">Phone: </span><a href="tel:<?php echo preg_replace('/[^A-Za-z0-9]/', '', $connect_vars['phone']); ?>" class="value"><?php echo $connect_vars['phone']; ?></a>
            </div>
          <?php if ( !$connect_vars['fax'] == '') : ?>
            <div class="fax">
              <span class="type">Fax: </span><span class="value"><?php echo $connect_vars['fax']; ?></span>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>
<?php if ( ! empty( $connect_vars['social'] ) ) :?>
  <div class="row collapse equal-height inside-border-group">
    <div class="small-5 columns equal center inside-border-group-item">
      <div class="valign">
        <div class="valign-cell">
          <i class="fa fa-at fa-2x" aria-hidden="true"></i>
        </div>
      </div>
    </div>
    <div class="small-19 columns equal inside-border-group-item">
      <div class="valign">
        <div class="valign-cell phm pvl inside-border-group-item row collapse">
          <?php if ( ! empty( $connect_vars['social'] ) ):
            $item_count = count( $connect_vars['social'] );
            $columns = phila_grid_column_counter( $item_count );

            if ( $columns == '12' ) :
              $columns = '8 end';
            endif;
          ?>
          <?php if ( isset( $connect_vars['social']['facebook'] ) && !$connect_vars['social']['facebook'] == '') : ?>
            <div class="small-<?php echo $columns;?> columns pvxs">
              <a href="<?php echo $connect_vars['social']['facebook']; ?>" class="phs" data-analytics="social">
                <i class="fa fa-facebook fa-2x" title="Facebook" aria-hidden="true"></i>
                <span class="show-for-sr">Facebook</span>
              </a>
            </div>
          <?php endif; ?>
          <?php if ( isset( $connect_vars['social']['twitter'] ) && !$connect_vars['social']['twitter'] == '') : ?>
            <div class="small-<?php echo $columns;?> columns pvxs">
              <a href="<?php echo $connect_vars['social']['twitter']; ?>" class="phs" data-analytics="social">
                <i class="fa fa-twitter fa-2x" title="Twitter" aria-hidden="true"></i>
                <span class="show-for-sr">Twitter</span>
              </a>
            </div>
          <?php endif; ?>
          <?php if ( isset( $connect_vars['social']['instagram'] ) && !$connect_vars['social']['instagram'] == '') : ?>
            <div class="small-<?php echo $columns;?> columns pvxs">
              <a href="<?php echo $connect_vars['social']['instagram']; ?>" class="phs" data-analytics="social">
              <i class="fa fa-instagram fa-2x" title="Instagram" aria-hidden="true"></i>
                <span class="show-for-sr">Instagram</span>
              </a>
            </div>
          <?php endif; ?>
        <?php endif; ?>
      </div>
      </div>
    </div>
  </div>
  <?php if ( !empty( $connect_vars['see_all'] ) ) : ?>
    <?php $see_all_URL = $connect_vars['see_all']; ?>
    <?php $see_all_content_type = 'contact information';?>
    <?php include( locate_template( 'partials/content-see-all.php' ) ); ?>
  <?php endif; ?>
</div>
<?php endif; ?>

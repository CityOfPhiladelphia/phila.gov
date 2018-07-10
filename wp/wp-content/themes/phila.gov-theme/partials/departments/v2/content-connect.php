<?php
/**
 * Display Connect Panels
 *
 * @package phila-gov
 */
?>
<?php
  $connect_panel = rwmb_meta('module_row_1_col_2_connect_panel');
  if ( !isset( $connect_vars ) ) :
    $connect_vars = phila_connect_panel($connect_panel);
  endif;

  //Check if this is the city govt. dir page. If so, we're only displaying certain pieces.
  $archive = is_archive();
?>

<div class="columns <?php echo ($archive) ? 'small-8' : 'large-8'?>">
  <?php if(!$archive) : ?>
    <div class="row">
      <div class="columns">
        <h2 class="contrast">Connect</h2>
      </div>
    </div>
  <?php endif; ?>
  <table class="no-alternate connect h-card" aria-label="Connect information">
  <?php if(!$archive) : ?>
    <?php if ( !$connect_vars['address']['st_1'] == '') : ?>
      <tr>
        <th scope="row">
          <i class="fa fa-map-marker fa-2x" aria-hidden="true"></i>
          <span class="accessible">Address</span>
        </th>
        <td class="pvl">
          <div class="adr">
            <?php if ( !$connect_vars['address']['st_1'] == '') : ?>
              <span class="p-street-address"><?php echo $connect_vars['address']['st_1']; ?></span><br/>
            <?php endif; ?>
            <?php if ( !$connect_vars['address']['st_2'] == '') : ?>
              <span class="p-extended-address"><?php echo $connect_vars['address']['st_2']; ?></span><br/>
            <?php endif; ?>
            <?php if ( !$connect_vars['address']['st_1'] == '') : ?>
              <span class="p-locality"><?php echo $connect_vars['address']['city']; ?></span>, <span class="region" title="Pennsylvania"> <?php echo $connect_vars['address']['state']; ?></span> <span class="p-postal-code"><?php echo $connect_vars['address']['zip']; ?></span>
            <?php endif; ?>
          </div>
        </td>
      </tr>
    <?php endif; ?>
  <?php endif; ?>
  <?php if ( !$connect_vars['email'] == '') : ?>
    <tr>
      <th scope="row" <?php echo ($archive) ? 'class="hide-for-small-only"' : ''?>>
        <span class="accessible">Email</span>
          <i class="fa fa-envelope-o fa-2x" aria-hidden="true"></i>
      </th>
      <td class="<?php echo (!$archive) ? 'pvl': 'percent-90' ?>">
        <a href="mailto:<?php echo $connect_vars['email']; ?>" class="u-email"><?php echo phila_util_return_parsed_email($connect_vars['email']); ?></a>
      </td>
    </tr>
  <?php endif; ?>
  <?php if ( ( !phila_util_is_array_empty($connect_vars['phone']) ) || (!$connect_vars['fax'] == '' ) ) : ?>
    <tr>
      <th scope="row" <?php echo ($archive) ? 'class="hide-for-small-only"' : ''?>>
        <i class="fa fa-phone fa-2x" aria-hidden="true"></i>
      </th>
      <td class="<?php echo (!$archive) ? 'pvl': 'percent-90' ?>">
        <div class="p-tel">
          <?php
          $area = ( $connect_vars['phone']['area'] != '' ) ? '(' .  $connect_vars['phone']['area'] . ') ' : '';

          $co_code = ( $connect_vars['phone']['co-code'] != '' ) ? $connect_vars['phone']['co-code'] : '';

          $subscriber_number = ( $connect_vars['phone']['subscriber-number'] != '' ) ? '-' . $connect_vars['phone']['subscriber-number'] : '';

          $full_phone = $area . $co_code . $subscriber_number;
          ?>
          <span class="type <?php echo ( !$connect_vars['fax'] ) ? 'accessible' : '';?>">Phone: </span>
          <a href="tel:<?php echo preg_replace('/[^A-Za-z0-9]/', '', $full_phone); ?>" class="value"><?php echo $full_phone; ?></a>
        </div>
      <?php if ( !$connect_vars['fax'] == '') : ?>
        <div class="fax">
          <span class="type">Fax: </span><span class="value"><?php echo $connect_vars['fax']; ?></span>
        </div>
      <?php endif; ?>
      </td>
    </tr>
  <?php endif; ?>
  <?php if(!$archive) : ?>
    <?php if ( !phila_util_is_array_empty($connect_vars['website']) )  : ?>
      <tr>
        <th scope="row">
          <i class="fa fa-globe fa-2x" aria-hidden="true"></i>
        </th>
        <td>
          <a href="<?php echo $connect_vars['website']['url'] ?>" class="<?php echo isset($connect_vars['website']['external']) ? 'external' : ''?>">
            <?php echo $connect_vars['website']['text'] ?>
          </a>
        </td>
      </tr>
    <?php endif; ?>
    <?php if ( ! empty( $connect_vars['social'] ) ) :?>
      <tr>
        <th scope="row">
          <i class="fa fa-at fa-2x" aria-hidden="true"></i>
          <span class="accessible">Social</span>
        </th>
        <td class="pvl">
          <?php if ( ! empty( $connect_vars['social'] ) ):
            $item_count = count( $connect_vars['social'] );
            $columns = phila_grid_column_counter( $item_count );
            if ( $columns == '12' ) :
              $columns = '8';
            endif;
            ?>
          <?php if ( isset( $connect_vars['social']['facebook'] ) && !$connect_vars['social']['facebook'] == '') : ?>
            <div class="small-<?php echo $columns;?> end columns pvxs">
              <a href="<?php echo $connect_vars['social']['facebook']; ?>" class="phs" data-analytics="social">
                <i class="fa fa-facebook fa-2x" title="Facebook" aria-hidden="true"></i>
                <span class="show-for-sr">Facebook</span>
              </a>
            </div>
          <?php endif; ?>
          <?php if ( isset( $connect_vars['social']['twitter'] ) && !$connect_vars['social']['twitter'] == '') : ?>
            <div class="small-<?php echo $columns;?> end columns pvxs">
              <a href="<?php echo $connect_vars['social']['twitter']; ?>" class="phs" data-analytics="social">
                <i class="fa fa-twitter fa-2x" title="Twitter" aria-hidden="true"></i>
                <span class="show-for-sr">Twitter</span>
              </a>
            </div>
          <?php endif; ?>
          <?php if ( isset( $connect_vars['social']['instagram'] ) && !$connect_vars['social']['instagram'] == '') : ?>
            <div class="small-<?php echo $columns;?> end columns pvxs">
              <a href="<?php echo $connect_vars['social']['instagram']; ?>" class="phs" data-analytics="social">
              <i class="fa fa-instagram fa-2x" title="Instagram" aria-hidden="true"></i>
                <span class="show-for-sr">Instagram</span>
              </a>
            </div>
          <?php endif; ?>
          <?php if ( isset( $connect_vars['social']['youtube'] ) && !$connect_vars['social']['youtube'] == '') : ?>
            <div class="small-<?php echo $columns;?> end columns pvxs">
              <a href="<?php echo $connect_vars['social']['youtube']; ?>" class="phs" data-analytics="social">
              <i class="fa fa-youtube fa-2x" title="YouTube" aria-hidden="true"></i>
                <span class="show-for-sr">Youtube channel</span>
              </a>
            </div>
          <?php endif; ?>
          <?php if ( isset( $connect_vars['social']['flickr'] ) && !$connect_vars['social']['flickr'] == '') : ?>
            <div class="small-<?php echo $columns;?> end columns pvxs">
              <a href="<?php echo $connect_vars['social']['flickr']; ?>" class="phs" data-analytics="social">
              <i class="fa fa-flickr fa-2x" title="Flickr" aria-hidden="true"></i>
                <span class="show-for-sr">Flickr stream</span>
              </a>
            </div>
          <?php endif; ?>
        </td>
      </tr>
    <?php endif; ?>
  <?php endif; ?>
<?php endif; ?>
</table>
<?php if(!$archive) : ?>
  <?php if ( !empty( $connect_vars['see_all'] ) ) : ?>
    <div class="row mtm">
      <div class="columns">
        <?php $see_all = array(
          'URL' => $connect_vars['see_all'],
          'content_type' => 'contact information',
          'nice_name' => 'contact information',
          'is_full' => true
        ); ?>
        <?php include( locate_template( 'partials/content-see-all.php' ) ); ?>
      </div>
    </div>
  <?php endif; ?>
<?php endif; ?>
</div>

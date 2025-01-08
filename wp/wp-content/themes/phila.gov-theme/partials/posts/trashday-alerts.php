<?php
/*
 * Trashday alerts
*/
?>

<?php

$status = rwmb_meta( 'phila_collection_status', array( 'object_type' => 'setting' ), 'phila_settings' );
$holidays = rwmb_meta( 'phila_holidays', array( 'object_type' => 'setting' ), 'phila_settings' );

$is_holiday = false;
foreach ( $holidays as $holiday ) {
  $today = new DateTime();
  $today->setTime(0, 0, 0, 0);
  $holiday_date = new DateTime($holiday['start_date']);
  $holiday_date->setTime(0, 0, 0, 0);
  $end_date = clone $holiday_date;
  if ($today->format('N') <= 4 && $holiday_date->format('N') != 5 && $holiday_date->format('N') != 6 && $holiday_date->format('N') != 7 ) {
    $end_date->modify('next friday');
  }
  if (( $today >= $holiday_date) && ($today <= $end_date) && (date('N') <= 5)){
    $is_holiday = true;
  }
}
?>

<?php global $post; ?>
<?php if ( phila_is_department_homepage( $post ) ) { ?>
  <div class="row mtl">
    <div class="columns">
<?php } ?>

<?php if ( !$a['is_in_table'] || $a['is_in_table'] == 0 ) { ?> 
  <table class="service-update">
    <tbody>
<?php } ?>
      <tr class="<?php if ( $status == 3 ) { 
        $flexible_collection = rwmb_meta( 'phila_flexible_collection', array( 'object_type' => 'setting' ), 'phila_settings' );
        if  ( $flexible_collection['phila_flexible_collection_color'] == 0 ) { echo "service-update"; } else if 
            ( $flexible_collection['phila_flexible_collection_color'] == 1 ) { echo "service-update--warning"; } else if 
            ( $flexible_collection['phila_flexible_collection_color'] == 2 ) { echo "service-update--critical"; }
      }
      else {
        if ( $status == 1 || $status == 2 || $is_holiday == true)
          { echo "service-update--warning"; } 
        else if ( $status == 0) 
          { echo "service-update"; } 
      }
        ?>">
        <th class="phl-mu <?php if ( !phila_is_department_homepage( $post ) && !is_home() && $a['icon_padding'] == 0 ) echo 'icon-only';?>">
          <i class="fa-solid fa-xl service-icon service-icon <?php if( $a['icon_padding'] && $a['icon_padding'] == 1) echo 'plm-mu' ?> 
          <?php 
            if ( $status == 1 || $status == 2 || $status == 3 || $is_holiday == true )
            { echo "fa-circle-exclamation"; } 
          else if ( $status == 0) 
            { echo "fa-circle-check"; } 
           ?>" aria-hidden="true"></i>
          <?php if ( $a['icon_text'] && $a['icon_text'] == 1 ) { ?>
            <span class="icon-label">Trash & recycling</span>
          <?php } ?>
        </th>
        <td class="prm pbm ptm">
          <span class="bold">
            <?php if 
              ( $status == 3 ) { echo $flexible_collection['phila_flexible_collection_status']; } else if
              ( $is_holiday == true ) { echo "This week, set trash and recycling out one day after your scheduled day. Trash-only collection (available in some areas) is canceled."; } else if
              ( $status == 0 ) { echo "Trash and recycling collections are on schedule."; } else if 
              ( $status == 1 ) { echo "Trash and recycling collections are delayed in some areas. You should still set materials out on your scheduled day. "; } else if
              ( $status == 2 ) { echo "Trash and recycling collections are delayed in some areas. Set materials out one day after your scheduled day."; } 
            ?>
          </span>
        </td>
      </tr>

<?php if ( !$a['is_in_table'] || $a['is_in_table'] == 0 ) { ?> 
    </tbody>
  </table>
<?php } ?>
<?php if ( phila_is_department_homepage( $post ) ) { ?>
  </div>
</div>
<?php } 
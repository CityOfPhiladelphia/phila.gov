<?php
/*
 * Press release grid
*/
?>

<?php $status = rwmb_meta( 'phila_collection_status', array( 'object_type' => 'setting' ), 'phila_settings' ); ?>
<?php if ( phila_is_department_homepage( 'test' ) ) { ?>
  <div class="row mvl">
    <div class="columns">
<?php } ?>

<?php if ( !$a['is_in_table'] ) { ?> 
  <table class="service-update">
    <tbody>
<?php } ?>
      <tr class="<?php if 
        ( $status == 0) 
          { echo "service-update"; } else if 
        ( $status == 1 || 
          $status == 2 || 
          $status == 3 )
          { echo "service-update--warning"; } else if
        ( $status == 4 ) 
          { 
            $flexible_collection = rwmb_meta( 'phila_flexible_collection', array( 'object_type' => 'setting' ), 'phila_settings' );
            if  ( $flexible_collection['phila_flexible_collection_color'] == 0 ) { echo "service-update"; } else if 
                ( $flexible_collection['phila_flexible_collection_color'] == 1 ) { echo "service-update--warning"; } else if 
                ( $flexible_collection['phila_flexible_collection_color'] == 2 ) { echo "service-update--critical"; }
          }
      ?>">
        <th class="phl-mu <?php if ( !phila_is_department_homepage( 'test' ) && !is_home() ) echo 'icon-only';?>">
          <i class="fa-2x fa-fw fas fa-trash-alt service-icon" aria-hidden="true"></i>
          <?php if ( phila_is_department_homepage( 'test' ) || is_home() ) { ?>
            <span class="icon-label">Trash & Recycling</span>
          <?php } ?>
        </th>
        <td class="pam">
          <span class="bold">
            <?php if 
              ( $status == 0 ) { echo "Trash and recycling collections are on schedule."; } else if 
              ( $status == 1 ) { echo "Trash and recycling collections are delayed in some areas. Set materials out on scheduled day."; } else if
              ( $status == 2 ) { echo "Trash and recycling collections are delayed in some areas. Set materials out one day behind scheduled day."; } else if
              ( $status == 3 ) { echo "Trash and recycling collections are on a holiday schedule. Set materials out one day behind your regular day."; } else if
              ( $status == 4 ) { echo $flexible_collection['phila_flexible_collection_status']; } 
            ?>
          </span>
        </td>
      </tr>

<?php if ( !$a['is_in_table'] ) { ?> 
    </tbody>
  </table>
<?php } ?>
<?php if ( phila_is_department_homepage( 'test' ) ) { ?>
  </div>
</div>
<?php } 
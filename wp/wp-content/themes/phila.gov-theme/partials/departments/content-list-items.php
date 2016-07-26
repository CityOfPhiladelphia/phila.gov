<?php
/*
 *
 * Partial for rendering List Items within a full column row
 *
 */

// Define variables
$row_title = isset( $list_items['phila_row_title'] ) ? $list_items['phila_row_title'] : '';
$summary = isset( $list_items['phila_summary'] ) ? $list_items['phila_summary'] : '';
$list_item_group = isset( $list_items['phila_list'] ) ? $list_items['phila_list'] : '' ;
?>

<section class="row mvl">
  <div class="large-24 columns">
    <h2 class="contrast"><?php echo( $row_title ); ?></h2>
      <div class="row">
        <div class="large-6 columns">
          <?php
            if ($summary != ''):
              echo( $summary );
            else :
              echo '<div class="placeholder">Please enter a summary.</div>';
            endif;
          ?>
        </div>
        <div class="large-18 columns">
          <div class="row collapse" data-equalizer>
            <?php
              foreach ($list_item_group as $key => $value) :
                $item = isset( $list_item_group[$key]['phila_list_items'] ) ? $list_item_group[$key]['phila_list_items'] : '';
            ?>
              <div class="content-list-items large-8 columns" >
                <?php foreach ( $item as $k => $v) :
                  $item_title = isset( $item[$k]['phila_list_item_title'] ) ? $item[$k]['phila_list_item_title'] : '';
                  $item_icon = isset( $item[$k]['phila_list_item_type'] ) ? $item[$k]['phila_list_item_type'] : '';
                  $item_url = isset( $item[$k]['phila_list_item_url'] ) ? $item[$k]['phila_list_item_url'] : '';
                ?>
                   <div class="content-list-item valign pvm phl" data-equalizer-watch>
                     <a href="<?php echo $item_url; ?>" class=" valign-cell">
                       <div>
                         <?php if ( $item_icon != ''):?>
                           <i class="fa <?php echo $item_icon; ?> fa-lg" aria-hidden="true"></i>
                         <?php endif;?>
                       </div>
                       <div>
                         <span class="external">
                           <?php echo $item_title; ?>
                         </span>
                       </div>
                     </a>
                   </div>
                <?php endforeach; ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
  </div>
</section>

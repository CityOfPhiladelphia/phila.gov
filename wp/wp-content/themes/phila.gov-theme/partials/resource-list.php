<?php

/* displays a grouped list of on-site links */

?>
<?php if (function_exists('rwmb_meta')): ?>

  <?php //set the vars
  $resource_list_groups = rwmb_meta( 'phila_resource_list' );

    //we require at least three
    if ( ! empty( $resource_list_groups ) && count($resource_list_groups) >= 3 ) :
      $count = 0;
      //group
      foreach ( $resource_list_groups as $resource_list_group ) : ?>
        <?php if ( $count == 0 ) : ?>
        <div class="row">
        <?php endif; ?>
        <?php //assign vars
        $count++;
        $resource_list_title = isset( $resource_list_group['phila_resource_list_title'] ) ? $resource_list_group['phila_resource_list_title'] : ''; ?>

        <div class="medium-8 small-24 column resource-list end">

          <header>
            <h2 class="h4 phm pvs"><?php echo $resource_list_title; ?></h2>
          </header>
          <ul>

        <?php
        //items
        $list_items_group = $resource_list_group['phila_resource_list_items'];

        foreach ( $list_items_group as $list_items ) :

          $item_title = isset( $list_items['phila_list_item_title'] ) ? $list_items['phila_list_item_title'] : '';
          $item_url = isset( $list_items['phila_list_item_url'] ) ? $list_items['phila_list_item_url'] : '';
          $item_resource_type = isset( $list_items['phila_list_item_type'] ) ? $list_items['phila_list_item_type'] : '';

          switch ($item_resource_type) {
            case ('phila_resource_document'):
              $icon = 'fa-file-text';
              break;

            case ('phila_resource_map'):
              $icon = 'fa-map-marker';
              break;

            case ('phila_resource_link'):
              $icon = 'fa-link';
              break;

            default:
              $icon = 'fa-file-text';
          }

          ?>

          <li class="phm pvs clickable-row" data-href="<?php echo $item_url;
          ?>"><a href="<?php echo $item_url;
          ?>"><div><i class="fa <?php echo $icon ?> fa-lg" aria-hidden="true"></i></div> <div><?php echo $item_title ?> </div></a></li>

        <?php endforeach; ?>

          </ul>
        </div>

      <?php if ( $count == 3 ) : ?>
      </div>
      <?php $count = 0; ?>
      <?php endif; ?>

      <?php endforeach; ?>


    <?php else : ?>
      <div class="row">
        <div class="columns">
          <?php echo 'Please enter at least three groups of links.'; ?>
        </div>
      </div>
    <?php endif; ?>

  <?php endif; ?>
</div>

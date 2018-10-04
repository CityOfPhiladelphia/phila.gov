<?php
/*
 * //NOTE: This template is not in use.
 * Partial for rendering service list
 *
 */
?>
<?php

$service_list = rwmb_meta( 'phila_v2_services_list' );
$services = phila_get_curated_service_list_v2( $service_list );
?>
<div class="row">
  <div class="columns">
    <?php foreach ( $services as $service ) : ?>
      <div class="mbl">
        <div><a class="h4" href="<?php echo get_permalink( $service['phila_v2_service_page'] ) ?>"><i class="fas fa-arrow-right" aria-hidden="true"></i> <?php echo get_the_title( $service['phila_v2_service_page'] ) ?> </a></div>

        <div class="item-fa-left-indent"><?php echo phila_get_item_meta_desc(); ?></div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php
/**
 * Display curated service lists on department homepages
 *
 * @package phila-gov
 */
?>

<?php

  $services_list = rwmb_meta( 'phila_v2_homepage_services' );
  $services = phila_loop_clonable_metabox( $services_list );

?>
<div class="row">
  <div class="columns content-list inverse">
    <h2>Services</h2>
    <div class="row collapse" data-equalizer>
      <?php foreach ( $services as $service ) : ?>
        <div class="content-list-items medium-8 columns">

          <div class="content-list-item valign pvm phm phl-l" data-equalizer-watch>
            <a href="<?php echo get_permalink( $service['phila_v2_service_page'] ) ?>" class="valign-cell">

              <div><i class="fa <?php echo $service['phila_v2_icon'] ?> fa-2x" aria-hidden="true"></i></div>

             <div> <?php echo get_the_title( $service['phila_v2_service_page'] ) ?> </div>
           </a>
         </div>
       </div>
      <?php endforeach; ?>
   </div>
  </div>
</div>

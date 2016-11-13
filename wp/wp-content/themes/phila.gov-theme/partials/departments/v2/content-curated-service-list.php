<?php
/**
 * Display curated service lists on department homepages
 *  
 * @package phila-gov
 */
?>

<?php
  $service_list = rwmb_meta( 'phila_v2_services_list' );
  $services = phila_get_curated_service_list_v2( $service_list );

  $icons = rwmb_meta( 'phila_v2_homepage_services' );
  $icon = phila_loop_clonable_metabox( $icons );

var_dump($icons);
?>

<div class="row">
  <div class="columns content-list inverse">
      <?php foreach ( $services as $service ) : ?>
        <?php var_dump($service); ?>
        <div class="content-list-item valign pvm phm phl-l">
          <a href="<?php echo get_permalink( $service['phila_v2_service_page'] ) ?>" class=" valign-cell">
            <?php foreach ( $icon as $con ): ?>

               <div><i class="fa <?php echo $con ?> fa-lg" aria-hidden="true"></i></div>
             <?php endforeach; ?>

           <div> <?php echo get_the_title( $service['phila_v2_service_page'] ) ?> </div>
         </a>
       </div>
   <?php endforeach; ?>

 </div>
</div>

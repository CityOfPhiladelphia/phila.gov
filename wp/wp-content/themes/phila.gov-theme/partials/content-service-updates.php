<?php
/*
 *
 * Default Services Updates Template
 *
 */
 ?>
 <div class="service-update-row row expanded">
   <div class="columns">
     <div class="row">
       <div class="columns">
         <h2 class="contrast">Service Updates</h2>
       </div>
     </div>
         <?php if ( $service_updates_loop->have_posts() ) : ?>

         <?php $update_array = array(); ?>

         <?php while ( $service_updates_loop->have_posts() ) :?>
           <?php $service_update = $service_updates_loop->the_post(); ?>
           <?php $update_details = phila_get_service_updates(); ?>
           <?php if ( !empty( $update_details ) ) :?>
             <?php array_push($update_array, $update_details); ?>
           <?php endif; ?>
         <?php endwhile; ?>

         <?php if (!empty($update_array) ): ?>

           <?php //Sort by urgency level (critical to normal) and then A-Z ?>
           <?php foreach ( $update_array as $key => $update_item ): ?>
             <?php $urgency[$key] = $update_item['service_level']; ?>
             <?php $type[$key] = $update_item['service_type']; ?>
           <?php endforeach; ?>
           <?php array_multisort($urgency, SORT_DESC, $type , SORT_ASC, $update_array );?>

           <div class="row">
           <?php $i=0; ?>

           <?php foreach ($update_array as $update):?>
             <?php if ($update['service_date_format'] != 'none') :?>
               <?php $start = new DateTime("@" . $update['service_effective_start']['timestamp']); ?>
               <?php $end = new DateTime("@" . $update['service_effective_end']['timestamp']); ?>
               <?php $start_month_format = phila_util_month_format($start); ?>
               <?php $end_month_format = phila_util_month_format($end); ?>
             <?php endif; ?>

             <?php if ($i > 3) break; ?>
               <div class="small-24 columns centered service-update equal-height <?php if ( !$update['service_level_label'] == '' ) echo $update['service_level_label']; ?> ">
                     <div class="service-update-icon equal">
                       <div class="valign">
                         <div class="valign-cell pam">
                           <i class="fa <?php if ( $update['service_icon'] ) echo $update['service_icon']; ?>  fa-2x" aria-hidden="true"></i>
                           <span class="icon-label small-text"><?php if ( $update['service_type'] ) echo $update['service_type']; ?></span>
                         </div>
                       </div>
                     </div>
                     <div class="service-update-details phm equal">
                       <div class="valign">
                         <div class="valign-cell pvm">

                           <?php if ( !$update['service_message'] == '' ):?>
                             <span>
                               <?php  echo $update['service_message']; ?>
                               <?php if ( !$update['service_link_text'] == '' && !$update['service_link'] == '' ):?>
                                 <a href="<?php echo $update['service_link']; ?>" class="external"><?php echo $update['service_link_text']; ?></a>
                               <?php endif;?>
                             </span>
                           <?php endif;?>

                           <?php if ( isset( $update['service_date_format'] ) && $update['service_date_format'] == 'date'):?>

                               <span class="date small-text"><em>
                                 In Effect:
                                 <?php if ($start->format('m-d') === $end->format('m-d') ): ?>
                                   <?php echo $start->format($start_month_format . ' j'); ?>
                                 <?php elseif ($start->format('m') === $end->format('m') ): ?>
                                   <?php echo $start->format($start_month_format . ' j') . '-' . $end->format('j'); ?>
                                 <?php else :?>
                                   <?php echo $start->format($start_month_format . ' j') . ' to ' . $end->format($end_month_format . ' j'); ?>
                                 <?php endif; ?>
                               </em></span>

                           <?php elseif ( isset( $update['service_date_format'] ) && $update['service_date_format'] == 'datetime' ) : ?>

                             <span class="date small-text"><em>
                               In Effect:
                               <?php if ($start->format('m-d') === $end->format('m-d') ): ?>
                                 <?php echo str_replace(array('am','pm',':00'),array('a.m.','p.m.',''), $start->format($start_month_format . ' j \f\r\o\m g:i a') . '-' . $end->format('g:i a') ); ?>
                               <?php elseif (intval($start->format('m')) === intval($end->format('m')) ): ?>
                                 <?php echo str_replace(array('am','pm',':00'),array('a.m.','p.m.',''), $start->format($start_month_format . ' j \a\t g:i a') . ' to ' . $end->format($end_month_format . ' j \a\t g:i a') ); ?>
                               <?php else : ?>
                                   <?php echo str_replace(array('am','pm',':00'),array('a.m.','p.m.',''), $start->format($start_month_format . ' j \a\t g:i a') . ' to ' . $end->format($end_month_format . ' j \a\t g:i a') ); ?>
                               <?php endif; ?>
                             </em></span>

                           <?php endif; ?>
                         </div>
                       </div>
                     </div>
                   </div>
                 <?php ++$i; ?>
               <?php endforeach; ?>
             </div>
           <?php endif; ?>
         <?php else : ?>
           <div class="row">
             <div class="small-24 columns">
               <div class="placeholder">
                 <?php echo 'No <strong>Service Updates</strong> available at this time.'; ?>
                </div>
             </div>
           </div>
         <?php endif; ?>
       </div>
     </div>

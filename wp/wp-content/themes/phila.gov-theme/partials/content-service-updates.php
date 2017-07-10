<?php
/*
 *
 * Default Services Updates Template
 *
 */
 ?>
<?php if ( $service_updates_loop->have_posts() ) : ?>
  <?php $update_array = array(); ?>

  <?php while ( $service_updates_loop->have_posts() ) :?>
    <?php $service_update = $service_updates_loop->the_post(); ?>
    <?php $update_details = phila_get_service_updates(); ?>
    <?php if ( !empty( $update_details ) ) :?>
      <?php array_push( $update_array, $update_details ); ?>
    <?php endif; ?>
  <?php endwhile; ?>

  <?php if ( !empty( $update_array ) ): ?>
  <div class="row">
    <div class="columns">
      <table class="service-update">
        <tbody>
         <?php //Sort by urgency level (critical to normal) and then A-Z ?>
         <?php foreach ( $update_array as $key => $update_item ): ?>
           <?php $urgency[$key] = $update_item['service_level']; ?>
           <?php $type[$key] = $update_item['service_type']; ?>
         <?php endforeach; ?>
         <?php array_multisort( $urgency, SORT_DESC, $type , SORT_ASC, $update_array );?>

         <?php $i=0; ?>

         <?php foreach ( $update_array as $update ):?>

           <?php if ($update['service_date_format'] != 'none') :?>
             <?php $start = new DateTime("@" . $update['service_effective_start']['timestamp']); ?>
             <?php $end = new DateTime("@" . $update['service_effective_end']['timestamp']); ?>
             <?php $start_month_format = phila_util_month_format($start); ?>
             <?php $end_month_format = phila_util_month_format($end); ?>
           <?php endif; ?>

           <?php if ($i > 2) break; ?>

           <tr scope="row" class="service-update--<?php if ( !$update['service_level_label'] == '' ) echo $update['service_level_label']; ?> ">
              <th class="phl-mu">
                <i class="fa  fa-2x fa-fw <?php if ( $update['service_icon'] ) echo $update['service_icon']; ?> " aria-hidden="true"></i>
                  <span class="icon-label"><?php if ( $update['service_type'] ) echo $update['service_type']; ?></span>
              </th>
              <td class="pam">

               <?php if ( !$update['service_message'] == '' ):?>
                 <span>
                   <?php  echo $update['service_message']; ?>
                   <?php if ( !$update['service_link_text'] == '' && !$update['service_link'] == '' ):?>
                     <a href="<?php echo $update['service_link']; ?>" <?php echo ( $update['service_off_site'] == 1 ) ?  'class="external"' : '' ?>><?php echo $update['service_link_text']; ?></a>
                   <?php endif;?>
                 </span>
               <?php endif;?>

               <?php if ( isset( $update['service_date_format'] ) && $update['service_date_format'] == 'date'):?>
                   <span class="date small-text"><em>
                     In Effect:
                     <?php if ($start->format('m-d') === $end->format('m-d') ): ?>
                       <?php echo str_replace(array('Sep'), array('Sept'), $start->format('l, ' . $start_month_format . ' j')); ?>
                     <?php else :?>
                       <?php echo str_replace(array('Sep'), array('Sept'), $start->format('l, ' . $start_month_format . ' j') . ' to ' . $end->format( 'l, ' . $end_month_format . ' j') ); ?>
                     <?php endif; ?>
                   </em></span>

                   <?php elseif ( isset( $update['service_date_format'] ) && $update['service_date_format'] == 'datetime' ) : ?>

                     <span class="date small-text"><em>
                       In Effect:
                       <?php if ($start->format('m-d') === $end->format('m-d') && $start->format('a') === $end->format('a')): ?>
                         <?php echo str_replace(array('Sep','am','pm',':00'),array('Sept','a.m.','p.m.',''), $start->format( 'g:i') . '–' . $end->format('g:i a \o\n l, ' . $start_month_format . ' j') ); ?>
                       <?php elseif ($start->format('m-d') === $end->format('m-d') && $start->format('a') !== $end->format('a')): ?>
                         <?php echo str_replace(array('Sep','12:00 am','12:00 pm','am','pm',':00'),array('Sept','midnight','noon','a.m.','p.m.',''), $start->format( 'g:i a') . '–' . $end->format('g:i a \o\n l, ' . $start_month_format . ' j') ); ?>
                       <?php else : ?>
                         <?php echo str_replace(array('Sep','12:00 am','12:00 pm','am','pm',':00'),array('Sept','midnight','noon','a.m.','p.m.',''), $start->format('g:i a \o\n l, ' . $start_month_format . ' j') . ' to ' . $end->format('g:i a \o\n l, ' . $end_month_format . 'j' ) ); ?>
                       <?php endif; ?>
                     </em></span>

                     <?php endif; ?>
                   </td>
                  </tr><!-- row -->
               <?php ++$i; ?>
             <?php endforeach; ?>
         </tbody>
       </table>
     </div>
   </div>
 <?php endif; ?>
 <?php elseif ( is_home() && empty( $update_array ) ) : ?>
   <div class="service-update-row row ptm">
     <div class="columns">
        <div class="row">
         <div class="columns">
           <h2 class="contrast">Service updates</h2>
         </div>
       </div>
       <div class="row">
         <div class="small-24 columns">
           <div class="placeholder">
             <?php echo 'No <strong>Service Updates</strong> available at this time.'; ?>
            </div>
         </div>
       </div>
     </div>
   </div>
 <?php endif; ?>

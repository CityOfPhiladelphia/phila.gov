<?php
/*
 *
 * Default Services Updates Template
 *
 */
?>

<?php
if (!is_home()) :
  $category = get_the_category();
  $category_slug = $category[0]->slug;
endif;
if ( !isset( $service_args ) ) :
  $service_args = array( 'post_type' => 'service_updates', 'category_name' => $category_slug );
endif;
?>

<?php $service_updates_loop = new WP_Query( $service_args ); ?>

<?php if ( $service_updates_loop->have_posts() ) : ?>
<?php $update_array = array(); ?>

<?php while ( $service_updates_loop->have_posts() ) :?>
  <?php $service_update = $service_updates_loop->the_post(); ?>
  <?php $update_details = phila_get_service_updates(); ?>

  <?php if ( !empty( $update_details ) ) :?>
    <?php array_push( $update_array, $update_details ); ?>
  <?php endif; ?>
<?php endwhile; ?>
<?php endif;?>
<?php wp_reset_query();?>

<?php if ( !empty( $update_array ) ): ?>
<div class="row <?= ( !is_home() ) ? 'mtl' : ''?>">
  <div class="columns">
    <table class="service-update">
      <tbody>
        <?php $type = array()?>
       <?php //Sort by urgency level (critical to normal) and then A-Z ?>
       <?php foreach ($update_array as $key => $row ): ?>
         <?php $urgency[$key] = $row['service_level'];?>
         <?php array_push($type, $row['service_type']);?>
       <?php endforeach; ?>

       <?php array_multisort( $urgency, SORT_DESC, $type, SORT_ASC, $update_array );?>

       <?php
       //Remove duplicate service types. We've already sorted by urgency, so the normal or less urgent alert will drop off.
       $temp_array = array();
        foreach ($update_array as &$v) {
            if (!isset($temp_array[$v['service_type']]))
                $temp_array[$v['service_type']] =& $v;
        }
        $update_array = array_values($temp_array); ?>

       <?php $i=0; ?>

       <?php foreach ( $update_array as $update ):?>

         <?php if ($update['service_date_format'] != 'none') :?>
           <?php $start = new DateTime("@" . $update['service_effective_start']['timestamp']); ?>
           <?php $end = new DateTime("@" . $update['service_effective_end']['timestamp']); ?>
           <?php $start_month_format = phila_util_month_format($start); ?>
           <?php $end_month_format = phila_util_month_format($end); ?>
         <?php endif; ?>

         <?php if ($i > 3) break; ?>

         <tr class="service-update--<?php if ( !$update['service_level_label'] == '' ) echo $update['service_level_label']; ?> ">
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

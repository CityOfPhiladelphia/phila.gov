<?php
/*
 *
 * Partial for rendering Department Row Two Content AKA "Full row"
 * TODO: deprecate this template
 */
?>



<?php
// set category vars for news/blogs
$category = get_the_category();
$category_slug = $category[0]->slug;

//set row 2 vars
$row_two_column_selection = rwmb_meta('phila_module_row_2_column_selection');

$row_two_col_one_module = rwmb_meta( 'module_row_2_col_1');

if (!empty($row_two_column_selection)) {

  $module_row_two_full_cal_col = rwmb_meta( 'phila_module_row_two_full_cal_col');

  if ( $row_two_column_selection == 'phila_module_row_2_full_column' ){
    $row_two_full_col_cal_id = $module_row_two_full_cal_col['phila_module_row_2_full_col_cal_id'];
    $row_two_full_col_cal_url = $module_row_two_full_cal_col['phila_module_row_2_full_col_cal_url'];
  }

  if ( $row_two_column_selection == 'phila_module_row_2_2_column' ){

    if (!empty($row_two_col_one_module)){

      $row_two_col_one_type = isset($row_two_col_one_module['phila_module_row_2_col_1_type']) ? $row_two_col_one_module['phila_module_row_2_col_1_type'] : '';

      if ( $row_two_col_one_type == 'phila_module_row_2_col_1_calendar' ){
        $row_two_col_one_cal_id = $row_two_col_one_module['module_row_2_col_1_options']['phila_module_row_2_col_1_cal_id'];
        $row_two_col_one_cal_url = $row_two_col_one_module['module_row_2_col_1_options']['phila_module_row_2_col_1_cal_url'];
      }
    }

    $row_two_col_two_module = rwmb_meta( 'module_row_2_col_2');
    if (!empty($row_two_col_two_module)){
      $row_two_col_two_type = $row_two_col_two_module['phila_module_row_2_col_2_type'];
      if ( $row_two_col_two_type == 'phila_module_row_2_col_2_calendar' ){
        $row_two_col_two_cal_id = $row_two_col_two_module['module_row_2_col_2_options']['phila_module_row_2_col_2_cal_id'];
        $row_two_col_two_cal_url = $row_two_col_two_module['module_row_2_col_2_options']['phila_module_row_2_col_2_cal_url'];
      }
    }
  }
}
?>
<!-- Begin Row Two MetaBox Modules -->
<?php if ( !empty( $row_two_full_col_cal_id ) ) : ?>
  <div class="row">
    <div class="columns">
      <h2>Events</h2>
    </div>
  </div>

  <div class="row calendar-row mbm ptm">
    <div class="columns">
      <?php echo do_shortcode('[calendar id="' . $row_two_full_col_cal_id . '"]'); ?>
    </div>
  </div>
  <?php if ( !empty($row_two_full_col_cal_url) ):?>
    <?php $see_all_URL = $row_two_full_col_cal_url; ?>
    <?php $see_all_content_type = 'events'; ?>
    <?php include( locate_template( 'partials/content-see-all.php' ) ); ?>
  <?php endif; ?>
 <?php endif; ?>

 <?php if ( ( !empty( $row_two_col_one_module['phila_module_row_2_col_1_type'] ) ) && (!empty( $row_two_col_two_module['phila_module_row_2_col_2_type'] ) ) ): ?>
 <section class="department-module-row-two mvl">
   <div class="row">
     <?php if ( $row_two_col_one_type  == 'phila_module_row_2_col_1_calendar' ): ?>
       <div class="medium-12 columns">
         <h2 class="contrast">Events</h2>
         <div class="event-box">
           <?php echo do_shortcode('[calendar id="' . $row_two_col_one_cal_id .'"]'); ?>
         </div>
         <?php if ($row_two_col_one_cal_url):?>
           <?php $see_all_URL = $row_two_col_one_cal_url;?>
           <?php $see_all_content_type = 'events'; ?>
          <?php include( locate_template( 'partials/content-see-all.php' ) ); ?>
         <?php endif; ?>
       </div>
     <?php elseif ( $row_two_col_one_type  == 'phila_module_row_2_col_1_press_release' ): ?>
         <div class="medium-12 columns">
           <div class="row">
             <?php echo do_shortcode('[press-releases posts=5]');?>
           </div>
           <?php $see_all_URL = '/press-releases/' . $category_slug . '/';
           $see_all_content_type = 'press releases';
           include( locate_template( 'partials/content-see-all.php' ) );?>
         </div>
       <?php endif; ?>
       <?php if ( $row_two_col_two_type  == 'phila_module_row_2_col_2_calendar' ): ?>
         <div class="medium-12 columns">
           <h2 class="contrast">Events</h2>
           <div class="event-box">
             <?php echo do_shortcode('[calendar id="' . $row_two_col_two_cal_id .'"]'); ?>
           </div>
           <?php if ($row_two_col_one_cal_url):?>
             <?php $see_all_URL = $row_two_col_one_cal_url;?>
             <?php $see_all_content_type = 'events'; ?>
             <?php include( locate_template( 'partials/content-see-all.php' ) ); ?>
           <?php endif; ?>
        </div>
       <?php elseif ( $row_two_col_two_type  == 'phila_module_row_2_col_2_press_release' ): ?>
         <div class="medium-12 columns">
           <div class="row">
             <?php echo do_shortcode('[press-releases posts=5]');?>
           </div>
           <?php $see_all_URL = '/press-releases/' . $category_slug . '/';
           $see_all_content_type = 'press releases';
           include( locate_template( 'partials/content-see-all.php' ) );?>
         </div>
       <?php endif; ?>
   </div>
 </section>
 <!-- End Row Two MetaBox Modules -->
 <?php endif; ?>

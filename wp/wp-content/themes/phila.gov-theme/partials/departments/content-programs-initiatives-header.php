<?php
/*
 *
 * Partial for rendering Programs and Initiatives Header Content
 *
 */

?>

<?php
  // Set hero-header vars
  $header_images = rwmb_meta( 'phila_p_i_images', $args = array('type' => 'file_input'));
?>
<div class="row programs-header" >
  <div class="small-24 column">
    <div class="header-wrap">
    <?php if ($header_images['phila_p_i_header'] && !$header_images['phila_p_i_header'] == ''):?>
      <img class="" src="<?php echo $header_images['phila_p_i_header'];?>">

    <?php else: ?>
    <div class="clearfix valign pvxl">
    <?php endif; ?>
      <!-- <img src="<?php echo $header_images['phila_p_i_header']; ?>"> -->
      <?php // TODO: Figure out what to do with the title on Staff Template ?>
      <?php the_title( '<div class="text-wrap"><h2 class="sub-page-title">', '</h2></div>' ); ?>
    </div>
    </div>
  </div>
</div>

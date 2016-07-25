<?php
/*
 *
 * Partial for rendering Programs and Initiatives Header Content
 *
 */

?>

<?php
  // Set hero-header vars
  $header_images = rwmb_meta( 'phila_p_i_images');
?>
<div class="row programs-header" >
  <div class="small-24 column">
    <div class="header-wrap">
    <?php if ( isset( $header_images['phila_p_i_header'] ) && !$header_images['phila_p_i_header'] == ''):?>
      <img alt="" class="" src="<?php echo $header_images['phila_p_i_header'];?>">
    <?php endif; ?>
    <?php the_title( '<div class="text-wrap"><h2 class="sub-page-title">', '</h2></div>' ); ?>
    </div>
  </div>
</div>

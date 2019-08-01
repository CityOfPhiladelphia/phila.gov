<?php 
/* 
  * Template for navigation card, with icon. 
  * @vars - $link - Required
          - $bg_color - Required
          - $icon - Required
          - $h3 - Required
          - $description - Required
  *
*/
?>
<?php if ( !empty( $icon ) ): ?>
  <a class="card card--navigation" href="<?php echo !empty($link) ? $link : ''?>">
    <div class="icon-box" style="background-color: <?php echo !empty($bg_color) ? $bg_color : ''?>">
      <div class="icon-content">
        <i class="<?php echo !empty($icon) ? $icon : ''?>"></i>
      </div>
    </div>
    <div class="card-text">
      <h3><?php echo !empty($h3) ? $h3 : ''?></h3>
      <p><?php echo !empty($description) ? $description : ''?></p>
    </div>
  </a>
<?php endif ?>
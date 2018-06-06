<?php
/*
 * Displays images at fixed widths. Initially created to display a list of sponsors.
*/?>
<?php
  if ( !isset( $image_list_vars ) ) :
    $image_list_vars = phila_image_list($image_list);
  endif;
  $count = count($image_list_vars['urls']);
  $grid_count = phila_grid_column_counter($count);
  if ($count == 5){
    $grid_count = 6;
  }
  if ($count == 6) {
    $grid_count = 8;
  }
  if($count >= 9){
    $grid_count = 6;
  }
?>
<section>
  <div class="grid-container">
    <div class="grid-x">
      <div class="cell">
        <header>
          <h2 class="contrast"><?= $image_list_vars['title']?></h2>
        </header>
      </div>
    </div>
  </div>
  <div class="grid-container">
    <div class="grid-x <?= ( $count == 1 ) ? '' : 'center' ?> align-middle">
        <?php foreach( $image_list_vars['urls'] as $url ) : ?>
          <div class="cell image-list medium-<?= $grid_count ?> mbl">

            <img src=<?= $url ?> alt=""/>
          </div>
        <?php endforeach; ?>
    </div>
  </div>
</section>

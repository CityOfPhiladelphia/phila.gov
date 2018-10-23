<?php
/*
 * Displays images at fixed widths. Initially created to display a list of sponsors.
*/?>
<?php
  if ( !isset( $image_list_vars ) ) :
    $image_list_vars = phila_image_list($image_list);
  endif;

  $image_count = count($image_list_vars['urls']);
  $image_grid_count = phila_grid_column_counter($image_count);

  if ($image_count == 5){
    $grid_count = 6;
  }
  if ($image_count == 6) {
    $grid_count = 8;
  }
  if($image_count >= 9){
    $grid_count = 6;
  }

  $extended_count = count($image_list_vars['extended']);
  $content_grid_count = phila_grid_column_counter($extended_count);

?>
<section>
  <div class="grid-container">
    <div class="grid-x">
      <div class="cell">
        <header>
          <h2 class="contrast"><?php echo $image_list_vars['title']?></h2>
          <?php echo !empty( $image_list_vars['sub_title'] ) ? '<h3>' .  $image_list_vars['sub_title'] . '</h3>' : '';  ?>
        </header>
      </div>
    </div>
  </div>
  <div class="grid-container">
    <div class="grid-x <?php echo ( $image_count == 1 ) ? '' : 'center' ?> align-middle">
        <?php foreach( $image_list_vars['urls'] as $url ) : ?>
          <div class="cell image-list medium-<?php echo $image_grid_count ?> mbl">
            <img src=<?php echo $url ?> alt=""/>
          </div>
        <?php endforeach; ?>
    </div>
  </div>
  <div class="grid-container mtl">
    <div class="grid-x grid-padding-x custom-text-multi">
        <?php foreach( $image_list_vars['extended'] as $content ) : ?>
          <div class="cell image-list medium-<?php echo $content_grid_count ?>">
            <h3><?php echo $content['secondary_title'] ?></h3>
            <div>
              <?php echo $content['secondary_list_content'] ?>
            </div>
          </div>
        <?php endforeach; ?>
    </div>
  </div>
</section>

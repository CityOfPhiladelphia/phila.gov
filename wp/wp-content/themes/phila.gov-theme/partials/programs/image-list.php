<?php
/*
 * Displays images at fixed widths. Initially created to display a list of sponsors.
*/?>
<?php
  if ( !isset( $image_list_vars ) ) :
    $image_list_vars = phila_image_list($image_list);
  endif;

  $image_count = count($image_list_vars['urls']);

  if ($image_count === 5){
    $grid_count = 6;
  }else if ($image_count == 6) {
    $grid_count = 8;
  }else if($image_count >= 9){
    $grid_count = 6;
  }else{
    $grid_count = phila_grid_column_counter($image_count);
  }

  if(isset($image_list_vars['extended'])) {
    $extended_count = count($image_list_vars['extended']);
    $content_grid_count = phila_grid_column_counter($extended_count);
  }

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
          <div class="cell image-list medium-<?php echo $image_count ?> mbl">
            <img src=<?php echo $url ?> alt=""/>
          </div>
        <?php endforeach; ?>
    </div>
  </div>
  <?php if(isset($image_list_vars['extended'])) :?>
    <div class="grid-container mtl">
      <div class="grid-x custom-text-multi">
          <?php foreach( $image_list_vars['extended'] as $content ) : ?>
            <div class="cell image-list medium-<?php echo $content_grid_count ?>">
              <?php echo isset($content['secondary_title']) ? '<h3>'.  $content['secondary_title'] . '</h3>' : ''; ?>
              <div>
                <?php echo apply_filters('the_content', $content['secondary_list_content']) ?>
              </div>
            </div>
          <?php endforeach; ?>
      </div>
    </div>
  <?php endif ?>
</section>

<?php
/*
 * Displays images at fixed widths. Initially created to display a list of sponsors.
*/?>
<?php
  if ( !isset( $image_list_vars ) ) :
    $image_list_vars = phila_image_list($image_list);
  endif;
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
    <div class="grid-x">
      <div class="cell image-list">
        <?php foreach( $image_list_vars['urls'] as $url ) : ?>
          <img src=<?= $url ?> alt=""/>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

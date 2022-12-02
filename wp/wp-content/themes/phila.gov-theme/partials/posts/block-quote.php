<?php
/*
block quote shortcode render
*/
?>

<div class="grid-container block-quote">
    <div class="grid-x mvs-mu">
        <div class="grid-x align-middle">
        <div class="small-24 medium-24 cell">
        <div class="cell medium-auto medium-shrink small-24 align-self-middle">
        <i class="fa-solid fa-quote-left"></i>
        </div>
          <?php if ( !empty( $a['text'] ) ) : ?>
            <h3 class="mbn phl-mu pvxs"><?php echo $a['text'] ?></h3>
          <?php endif; ?>
      </div>
      </div>
    </div>
    </div>
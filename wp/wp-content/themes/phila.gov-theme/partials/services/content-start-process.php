<?php
/*
 *
 * Start a process Template
 *
 */
 ?>
<?php
  $process = rwmb_meta( 'phila_start_process' );
  $start_process = phila_get_start_process( $process );
?>
<p><?php echo phila_get_item_meta_desc() ?></p>
<div class="row columns">
  <div class="panel info small-24 intro-item columns pal">
    <div class="row">
      <div class="medium-2 columns show-for-medium">
        <i class="fa fa-flag fa-2x"></i>
      </div>
      <div class="medium-22 small-24 columns">
        <h2 class="h4 mtn">Before you start</h2>
        <?php echo $start_process['content'] ?>
      </div>
    </div>
    <div class="row">
      <div class="ptl center columns">
        <a data-swiftype-index="false" href="<?php echo $start_process['button_url']; ?>" class="button clearfix">
          <div class="valign">
            <div class="button-label valign-cell"><?php echo $start_process['button_text'] ?>
              <?php if ( $start_process['button_external'] == 1 ) :?>
                <i class="fa fa-external-link" aria-hidden="true"></i> <span class="accessible"> External link</span>
              <?php endif;?></div>
          </div>
        </a>
      </div>
    </div>
  </div>
</div>

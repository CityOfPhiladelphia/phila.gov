<?php
/*
 *
 * Start a process partial
 *
 */
?>
<?php
  $process = !empty( rwmb_meta( 'phila_start_process' ) ) ? rwmb_meta( 'phila_start_process' ) : rwmb_meta( 'service_before_you_begin' ) ;
  $start_process = phila_get_start_process( $process );
?>

<?php if ( !empty( $start_process['content'] ) ) : ?>
  <div class="row columns mvm">
    <div class="panel info small-24 intro-item columns pal">
      <div class="row">
        <div class="medium-2 columns show-for-medium">
          <i class="fas fa-flag fa-2x"></i>
        </div>
        <div class="medium-22 small-24 columns">
          <h2 class="h4 mtn">Before you start</h2>
          <?php echo apply_filters( 'the_content', $start_process['content'] ); ?>
        </div>
      </div>
      <?php if ( !empty( $start_process['button_url'] ) && !empty( $start_process['button_text'] ) ): ?>
        <div class="row">
          <div class="center columns">
            <a data-swiftype-index="false" href="<?php echo $start_process['button_url']; ?>" class="button clearfix">
              <?php echo $start_process['button_text'] ?>
                <?php if ( $start_process['button_external'] == 1 ) :?>
                  <i class="fas fa-external-link-alt" aria-hidden="true"></i> <span class="accessible"> External link</span>
                <?php endif;?>
            </a>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
<?php endif;?>
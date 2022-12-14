<?php
/*
 *
 * Before you begin partial. For Default_v2 services
 *
 */
?>
<?php
  $process = rwmb_meta( 'service_before_you_begin' ) ;
  $button = rwmb_meta( 'phila_start_button' );
?>


<?php if ( !empty( $process ) ) : ?>
  <div class="row columns mvm">
    <div class="panel info small-24 intro-item columns pal">
      <div class="row">
        <div class="medium-2 columns show-for-medium">
          <i class="fas fa-flag fa-2x"></i>
        </div>
        <div class="medium-22 small-24 columns">
          <h2 class="h4 mtn">Before you begin</h2>
          <?php echo apply_filters( 'the_content', $process ); ?>
        </div>
      </div>
      <?php if ( !empty( $button['link_url'] ) && !empty( $button['link_text'] ) ): ?>
        <div class="row">
          <div class="center columns">
            <a data-swiftype-index="false" href="<?php echo $button['link_url']; ?>" class="button clearfix">
              <?php echo $button['link_text'] ?>
                <?php if ( $button['is_external'] == 1 ) :?>
                  <i class="fas fa-external-link-alt" aria-hidden="true"></i> <span class="accessible"> External link</span>
                <?php endif;?>
            </a>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
<?php endif;?>
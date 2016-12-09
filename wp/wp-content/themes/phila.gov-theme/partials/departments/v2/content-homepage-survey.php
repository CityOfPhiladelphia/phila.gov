<?php
/*
 *
 * Link to survey
 *
 */

?>
<?php
  $survey_link = rwmb_meta( 'phila_v2_survey' );
  $survey = phila_survey_display( $survey_link );
?>
<?php if ( !empty( $survey ) ) :?>
  <div class="row mvxl">
    <div class="columns panel survey">
      <div class="row equal-height">
        <div class="small-24 medium-18 columns valign equal">
          <div class="valign-cell">
            <?php if ( !empty( $survey['title'] ) ) : ?>
              <h3><?php echo $survey['title'] ?></h3>
            <?php endif; ?>
            <?php if ( !empty( $survey['description'] ) ) : ?>
              <p><?php echo $survey['description'] ?></p>
            <?php endif; ?>
          </div>
        </div>
        <?php if ( !empty( $survey['url'] ) ) : ?>
        <div class="small-24 medium-6 columns valign equal center">
          <div class="valign-cell">
            <a href="<?php echo $survey['url'] ?>" class="button clearfix">
              <?php if ( !empty( $survey['link_text'] ) ) :?>
                <div class="valign">
                  <div class="button-label valign-cell center"><?php echo $survey['link_text']?>
                    <?php if ( $survey['external'] == 1 ) :?>
                      <i class="fa fa-external-link"></i>
                    <?php endif;?>
                  </div>
                </div>
              <?php endif;?>
            </a>
          </div>
        </div>
      <?php endif; ?>
      </div>
    </div>
  </div>
<?php endif;?>

<?php
/*
 *
 * Link to survey
 *
 */

?>

<?php
  $cta_link = rwmb_meta( 'phila_v2_cta_full' );
  if ( !isset( $link ) ) :
    $link = phila_cta_full_display( $cta_link );
  endif; ?>
<?php if ( !empty( $link ) ) :?>
  <div class="grid-container">
    <div class="grid-x mvxl">
      <div class="cell panel <?php echo (!empty( $link['is_survey'] ) ) ? 'survey' : '' ?>">
        <div class="grid-x align-middle">
          <div class="small-24 medium-18 cell">
            <?php if ( !empty( $link['title'] ) ) : ?>
              <h3 id="<?php echo sanitize_title_with_dashes( $link['title']); ?>" class="mbn"><?php echo $link['title'] ?></h3>
            <?php endif; ?>
            <?php if ( !empty( $link['description'] ) ) : ?>
              <p class="mts"><?php echo $link['description'] ?></p>
            <?php endif; ?>
          </div>
          <div class="small-24 medium-6 cell center cta-button">
            <a <?php echo ( !empty( $link['url'] ) && (empty($link['is_modal'] ) ) ) ?  "href=" . $link['url'] : "" ?> class="button <?php echo ( !empty($link['external']) || !empty($link['modal_icon']) ) ? 'icon ' : '';?> clearfix float-right"
              <?php echo ( !empty($link['is_modal'] ) && ( empty( $link['url'] ) ) ) ? "data-open=action-modal" : "" ?>>
              <?php if ( !empty( $link['link_text'] ) ) :?>
              <span class="valign">
                <?php if ( $link['external'] == 1 ) :?>
                  <i class="fas fa-external-link-alt valign-cell" aria-hidden="true"></i>
                  <span class="accessible">External link</span>
                  <span class="button-label valign-cell">
                    <?php echo $link['link_text']?>
                  </span>
                <?php elseif (!empty( $link['modal_icon'] ) ) :?>
                  <span class="button-label valign-cell">
                    <?php echo $link['link_text']?>
                </span>
                  <i class="fa <?php echo $link['modal_icon'] ?> valign-cell" aria-hidden="true"></i>
                <?php else :?>
                  <span class="valign-cell">
                    <?php echo $link['link_text']?>
                  </span>
                  <?php endif; ?>
                </span>
              </a>
            </div>
          </div>
        <?php endif; ?>
          <?php if ( !empty( $link['is_modal'] ) ) : ?>
            <div class="reveal center" id="action-modal" data-reveal data-deep-link="true">
              <?php echo $link['modal_content']; ?>
              <button class="close-button bg-white" data-close aria-label="Close modal" type="button">
               <span aria-hidden="true">&times;</span>
             </button>
            </div>
          <?php endif; ?>
      </div>
    </div>
  </div>
<?php endif;?>

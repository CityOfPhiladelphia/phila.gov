<?php
/*
call to action shortcode render
*/
?>

<div class="grid-container">
  <div class="grid-x mvxl">
    <div class="cell panel <?php echo (!empty( $a['is_survey'] ) ) ? 'survey' : '' ?>">
      <div class="grid-x align-middle">
        <div class="small-24 medium-18 cell">
          <?php if ( !empty( $a['title'] ) ) : ?>
            <h3 class="mbn"><?php echo $a['title'] ?></h3>
          <?php endif; ?>
          <?php if ( !empty( $a['description'] ) ) : ?>
            <p class="mts"><?php echo $a['description'] ?></p>
          <?php endif; ?>
        </div>
        <div class="small-24 medium-6 cell center">
          <a <?php echo ( !empty( $a['url'] ) && (empty($a['is_modal'] ) ) ) ?  "href=" . $a['url'] : "" ?> class="button <?php echo ( !empty($a['external']) || !empty($a['modal_icon']) ) ? 'icon ' : '';?> clearfix float-right"
            <?php echo ( !empty($a['is_modal'] ) && ( empty( $a['url'] ) ) ) ? "data-open=action-modal" : "" ?>>
            <?php if ( !empty( $a['link_text'] ) ) :?>
              <span class="valign">
                <?php if ( $a['external'] == 1 ) :?>
                  <i class="fas fa-external-link-alt valign-cell" aria-hidden="true"></i>
                  <span class="accessible">External link</span>
                  <span class="button-label valign-cell">
                    <?php echo $a['link_text']?>
                  </span>
                <?php elseif (!empty( $a['modal_icon'] ) ) :?>
                  <i class="fa <?php echo $a['modal_icon'] ?> valign-cell" aria-hidden="true"></i>
                  <span class="accessible">Open modal</span>
                <?php else :?>
                  <span class="valign-cell">
                    <?php echo $a['link_text']?>
                  </span>
                  <?php endif; ?>
                </span>
            </a>
          </div>
        </div>
      <?php endif; ?>
        <?php if ( !empty( $a['is_modal'] ) ) : ?>
          <div class="reveal center" id="action-modal" data-reveal data-deep-link="true">
            <?php echo $a['modal_content']; ?>
            <button class="close-button bg-white" data-close aria-label="Close modal" type="button">
             <span aria-hidden="true">&times;</span>
           </button>
          </div>
        <?php endif; ?>
    </div>
  </div>
</div>

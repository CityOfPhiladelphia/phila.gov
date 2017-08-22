<?php
/*
 *
 * Start a process partial
 *
 */
 ?>
<?php
  $process = rwmb_meta( 'phila_start_process' );
  $start_process = phila_get_start_process( $process );
?>
<p><?php echo phila_get_item_meta_desc() ?></p>

<?php if ( !empty( $start_process['content'] ) ) : ?>
  <div class="row columns mvm">
    <div class="panel info small-24 intro-item columns pal">
      <div class="row">
        <div class="medium-2 columns show-for-medium">
          <i class="fa fa-flag fa-2x"></i>
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
                  <i class="fa fa-external-link" aria-hidden="true"></i> <span class="accessible"> External link</span>
                <?php endif;?>
            </a>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
<?php endif;?>
<?php get_template_part( 'partials/content', 'custom-markup-before-wysiwyg' ); ?>
<div class="row">
  <div class="columns">
    <?php the_content(); ?>
  </div>
</div>
<?php get_template_part( 'partials/content', 'custom-markup-after-wysiwyg' ); ?>

<?php get_template_part( 'partials/content', 'heading-groups' ); ?>
<?php get_template_part( 'partials/content', 'additional' ); ?>

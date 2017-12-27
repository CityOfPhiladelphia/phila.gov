<?php
/*
 *
 * Partial for additional content. Includes, forms, related content, did you know, and questions about this content.
 *
 */
 ?>

<?php

  $additional_content = rwmb_meta('phila_additional_content');
  $more = phila_additional_content( $additional_content );

?>

<?php if ( !empty($more['forms']) ) : ?>
<div class="row">
  <div class="columns">
    <section>
      <h3 class="black bg-ghost-gray phm-mu mtl mbm">Forms & instructions</h3>
      <div class="phm-mu">
        <?php foreach ( $more['forms'] as $form ): ?>
          <div class="pvs">
            <a href="<?php echo get_the_permalink($form);?>"><i class="fa fa-file-text" aria-hidden="true"></i> <?php echo get_the_title($form); ?></a>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  </div>
</div>
<?php endif; ?>

<?php if ( !empty( $more['related'] ) ) : ?>
<div class="row">
  <div class="columns">
    <section>
      <h3 class="black bg-ghost-gray phm-mu mtl mbm">Related content</h3>
      <div class="phm-mu">
        <?php echo apply_filters( 'the_content', $more['related']); ?>
      </div>
    </section>
  </div>
</div>
<?php endif; ?>

<div class="row equal-height mtl">
  <?php if ( !empty($more['aside']['did_you_know'] ) ) : ?>
   <div class="medium-<?php echo (!empty( $more['aside']['questions'] ) ) ? '12' : '24'; ?> columns">
      <div class="panel info equal">
        <aside>
          <h3><i class="fa fa-exclamation-circle" aria-hidden="true"></i> Did you know?</h3>
          <?php echo apply_filters( 'the_content', $more['aside']['did_you_know'] ); ?>
        </aside>
      </div>
  </div>
<?php endif; ?>
<?php if ( !empty( $more['aside']['questions'] ) ) : ?>
 <div class="medium-<?php echo (!empty( $more['aside']['did_you_know'] ) ) ? '12' : '24'; ?> columns">
    <div class="panel info equal">
      <aside>
        <h3><i class="fa fa-comments" aria-hidden="true"></i> Questions?</h3>
        <?php echo apply_filters( 'the_content', $more['aside']['questions'] );?>
      </aside>
    </div>
  </div>
<?php endif; ?>
</div>

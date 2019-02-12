<?php
/*
 *
 * Partial for additional content. Includes, forms, related content, did you know, and questions about this content.
 *
 */
?>

<?php

  $additional_content = rwmb_meta('phila_additional_content');

  if( !empty ($additional_content ) ) {
    $more = phila_additional_content( $additional_content );
  }else{
    $more = array();
    $document_picker = rwmb_meta('phila_document_page_picker');
    $related_content = rwmb_meta('service_related_content');
    $related_content_picker = rwmb_meta('service_related_content_picker');
    $did_you_know = rwmb_meta('service_did_you_know_content');
    $questions = rwmb_meta('service_questions_content');
    $disclaimer = rwmb_meta('service_disclaimer_content');

    $more['forms'] = $document_picker;
    $more['related_picker'] = $related_content_picker;
    $more['related'] = $related_content;
    $more['aside']['did_you_know'] = $did_you_know;
    $more['aside']['questions'] = $questions;
    $more['disclaimer'] = $disclaimer;
  }



?>

<?php if ( !empty($more['forms']) ) : ?>
<div class="row">
  <div class="columns">
    <section>
      <h3 class="black bg-ghost-gray phm-mu mtl mbm">Forms & instructions</h3>
      <div class="phm-mu">
        <?php foreach ( $more['forms'] as $form ): ?>
          <div class="pvs">
            <a href="<?php echo get_the_permalink($form);?>"><i class="far fa-file-alt" aria-hidden="true"></i> <?php echo get_the_title($form); ?></a>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  </div>
</div>
<?php endif; ?>

<?php if ( !empty( $more['related_picker'] ) || !empty( $more['related'] ) ) : ?>
<div class="row">
  <div class="columns">
    <section>
      <h3 class="black bg-ghost-gray phm-mu mtl mbm">Related content</h3>
      <?php if (!empty( $more['related_picker']) ) : ?>
        <div class="phm-mu">
          <ul class="mbn">
            <?php foreach ( $more['related_picker'] as $pick ) :?>
              <li><a href="<?php echo get_permalink($pick)?>"><?php echo get_the_title($pick) ?></a></li>
            <?php endforeach ?>
          </ul>
        </div>
      <?php endif ?>
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
          <h3><i class="fas fa-exclamation-circle" aria-hidden="true"></i> Did you know?</h3>
          <?php echo apply_filters( 'the_content', $more['aside']['did_you_know'] ); ?>
        </aside>
      </div>
  </div>
<?php endif; ?>
<?php if ( !empty( $more['aside']['questions'] ) ) : ?>
 <div class="medium-<?php echo (!empty( $more['aside']['did_you_know'] ) ) ? '12' : '24'; ?> columns">
    <div class="panel info equal">
      <aside>
        <h3><i class="fas fa-comments" aria-hidden="true"></i> Questions?</h3>
        <?php echo apply_filters( 'the_content', $more['aside']['questions'] );?>
      </aside>
    </div>
  </div>
<?php endif; ?>

<?php if ( !empty( $more['disclaimer'] ) ) : ?>
  <div class="row">
     <div class="medium-18 medium-centered columns disclaimer-text">
      <aside>
        <?php echo apply_filters( 'the_content', $more['disclaimer'] );?>
      </aside>
    </div>
  </div>
<?php endif; ?>
</div>

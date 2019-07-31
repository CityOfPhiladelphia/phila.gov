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
    $content = phila_additional_content( $additional_content );
  }else{
    $content = array();
    $document_picker = rwmb_meta('phila_document_page_picker');
    $related_content = rwmb_meta('service_related_content');
    $related_content_picker = rwmb_meta('service_related_content_picker');
    $did_you_know = rwmb_meta('service_did_you_know_content');
    $questions = rwmb_meta('service_questions_content');
    $disclaimer = rwmb_meta('service_disclaimer_content');

    $content['forms'] = $document_picker;
    $content['related_picker'] = $related_content_picker;
    $content['related'] = $related_content;
    $content['aside']['did_you_know'] = $did_you_know;
    $content['aside']['questions'] = $questions;
    $content['disclaimer'] = $disclaimer;
  }



?>

<?php if( !phila_util_is_array_empty($content) ) :  ?>
<!-- Additional Content-->
<div class="mtxl">
  <?php if ( !empty($content['forms']) ) : ?>
  <div class="row">
    <div class="columns">
      <section>
        <h3 class="black bg-ghost-gray phm-mu mtl mbm">Forms & instructions</h3>
        <div class="phm-mu">
          <?php foreach ( $content['forms'] as $form ): ?>
            <div class="pvs">
              <a href="<?php echo get_the_permalink($form);?>"><i class="far fa-file-alt" aria-hidden="true"></i> <?php echo get_the_title($form); ?></a>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    </div>
  </div>
  <?php endif; ?>

  <?php if ( !empty( $content['related_picker'] ) || !empty( $content['related'] ) ) : ?>
  <div class="row">
    <div class="columns">
      <section>
        <h3 class="black bg-ghost-gray phm-mu mtl mbm">Related content</h3>
        <?php if (!empty( $content['related_picker']) ) : ?>
          <div class="phm-mu">
            <ul class="mbn">
              <?php foreach ( $content['related_picker'] as $pick ) :?>
                <li><a href="<?php echo get_permalink($pick)?>"><?php echo get_the_title($pick) ?></a></li>
              <?php endforeach ?>
            </ul>
          </div>
        <?php endif ?>
        <div class="phm-mu">
          <?php echo apply_filters( 'the_content', $content['related']); ?>
        </div>
      </section>
    </div>
  </div>
  <?php endif; ?>

  <div class="row equal-height mtl">
    <?php if ( !empty($content['aside']['did_you_know'] ) ) : ?>
    <div class="medium-<?php echo (!empty( $content['aside']['questions'] ) ) ? '12' : '24'; ?> columns">
        <div class="panel info equal">
          <aside>
            <h3><i class="fas fa-exclamation-circle" aria-hidden="true"></i> Did you know?</h3>
            <?php echo apply_filters( 'the_content', $content['aside']['did_you_know'] ); ?>
          </aside>
        </div>
    </div>
  <?php endif; ?>
  <?php if ( !empty( $content['aside']['questions'] ) ) : ?>
  <div class="medium-<?php echo (!empty( $content['aside']['did_you_know'] ) ) ? '12' : '24'; ?> columns">
      <div class="panel info equal">
        <aside>
          <h3><i class="fas fa-comments" aria-hidden="true"></i> Questions?</h3>
          <?php echo apply_filters( 'the_content', $content['aside']['questions'] );?>
        </aside>
      </div>
    </div>
  <?php endif; ?>

  <?php if ( !empty( $content['disclaimer'] ) ) : ?>
    <div class="row">
      <div class="medium-18 medium-centered columns disclaimer-text">
        <aside>
          <?php echo apply_filters( 'the_content', $content['disclaimer'] );?>
        </aside>
      </div>
    </div>
  <?php endif; ?>
  </div>
</div>
<!-- /Additional Content-->
<?php endif; ?>
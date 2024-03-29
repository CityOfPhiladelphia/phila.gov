<?php
/*
 *
 * Partial for additional content. Includes, forms, related content, did you know, and questions about this content.
 *
 */
?>

<?php
  $additional_content = rwmb_meta('phila_additional_content');
  $page_template = rwmb_meta( 'phila_template_select');
  if( !empty ($additional_content )
    && $page_template != 'default_v2'
    && $page_template != 'topic_page'
    && $page_template != 'service_stub'
    && $page_template != 'custom_content'
  ) {
    $content = phila_additional_content( $additional_content );
  }else{
    $content = array();
    $document_picker = rwmb_meta('phila_document_page_picker');
    $document_text = rwmb_meta('phila_forms_instructions_free_text');
    $related_content = rwmb_meta('service_related_content');
    $related_content_picker = rwmb_meta('service_related_content_picker');
    $did_you_know = rwmb_meta('service_did_you_know_content');
    $questions = rwmb_meta('service_questions_content');
    $disclaimer = rwmb_meta('service_disclaimer_content');

    $content['forms'] = $document_picker;
    $content['form_free'] = $document_text;
    $content['related_picker'] = $related_content_picker;
    $content['related'] = $related_content;
    $content['aside']['did_you_know'] = $did_you_know;
    $content['aside']['questions'] = $questions;
    $content['disclaimer'] = $disclaimer;
  }



?>

<?php if( !phila_util_is_array_empty($content) ) :  ?>
<!-- Additional Content-->
</div>
<section>
  <?php if ( !empty($content['forms']) || !empty( $content['form_free'] )) : ?>
  <div class="row">
    <div class="columns">
      <section>
        <h3 class="black bg-ghost-gray phm-mu mtl mbm">Forms & instructions</h3>
        <?php if (!empty( $content['forms']) ) : ?>
          <div class="phm-mu">
            <?php foreach ( $content['forms'] as $form ): ?>
              <div>
                <a href="<?php echo get_the_permalink($form);?>"><i class="far fa-file-alt fa-fw" aria-hidden="true"></i><?php echo get_the_title($form); ?></a>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        <?php echo apply_filters( 'the_content', $content['form_free']); ?>
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
</section>
<!-- /Additional Content-->
<?php endif; ?>
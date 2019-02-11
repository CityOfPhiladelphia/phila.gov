<?php
/*
 *
 * v2 Partial for additional content. Includes, forms, related content, did you know, and questions about this content.
 *
 */
?>

<?php

  $document_picker = rwmb_meta('phila_document_page_picker');
  $related_content = rwmb_meta('service_related_content');
  $related_content_picker = rwmb_meta('service_related_content_picker');
  $did_you_know = rwmb_meta('service_did_you_know_content');
  $questions = rwmb_meta('service_questions_content');
  $disclaimer = rwmb_meta('service_disclaimer_content');
?>

<?php if ( !empty($document_picker) ) : ?>
<div class="row">
  <div class="columns">
    <section>
      <h3 class="black bg-ghost-gray phm-mu mtl mbm">Forms & instructions</h3>
      <div class="phm-mu">
        <?php foreach ( $document_picker as $form ): ?>
          <div class="pvs">
            <a href="<?php echo get_the_permalink($form);?>"><i class="far fa-file-alt" aria-hidden="true"></i> <?php echo get_the_title($form); ?></a>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  </div>
</div>
<?php endif; ?>

<?php if ( !empty( $related_content ) || !empty( $related_content_picker ) ) : ?>
<div class="row">
  <div class="columns">
    <section>
      <h3 class="black bg-ghost-gray phm-mu mtl mbm">Related content</h3>
      <?php if (!empty( $related_content_picker) ) : ?>
        <div class="phm-mu">
          <ul class="mbn">
            <?php foreach ( $related_content_picker as $pick ) :?>
              <li><a href="<?php echo get_permalink($pick)?>"><?php echo get_the_title($pick) ?></a></li>
            <?php endforeach ?>
          </ul>
        </div>
      <?php endif ?>
      <div class="phm-mu">
        <?php echo apply_filters( 'the_content', $related_content ); ?>
      </div>
    </section>
  </div>
</div>
<?php endif; ?>

<div class="row equal-height mtl">
  <?php if ( !empty($did_you_know ) ) : ?>
  <div class="medium-<?php echo (!empty( $questions ) ) ? '12' : '24'; ?> columns">
      <div class="panel info equal">
        <aside>
          <h3><i class="fas fa-exclamation-circle" aria-hidden="true"></i> Did you know?</h3>
          <?php echo apply_filters( 'the_content', $did_you_know ); ?>
        </aside>
      </div>
  </div>
<?php endif; ?>
<?php if ( !empty( $questions ) ) : ?>
  <div class="medium-<?php echo (!empty( $questions ) ) ? '12' : '24'; ?> columns">
    <div class="panel info equal">
      <aside>
        <h3><i class="fas fa-comments" aria-hidden="true"></i> Questions?</h3>
        <?php echo apply_filters( 'the_content', $questions );?>
      </aside>
    </div>
  </div>
<?php endif; ?>

<?php if ( !empty( $disclaimer ) ) : ?>
  <div class="row">
    <div class="medium-18 medium-centered columns disclaimer-text">
      <aside>
        <?php echo apply_filters( 'the_content', $disclaimer );?>
      </aside>
    </div>
  </div>
<?php endif; ?>
</div>

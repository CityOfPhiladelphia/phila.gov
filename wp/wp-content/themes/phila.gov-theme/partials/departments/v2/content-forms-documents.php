<?php
/*
 *
 *  Forms and Documents Layout
 *
 */
 ?>
<?php
  $forms_documents = rwmb_meta( 'phila_forms_documents_cta' );
?>

<?php if ( !empty($forms_documents) ) : ?>
  <section>
    <div class="forms-documents row">
    <?php foreach ( $forms_documents as $key => $value ): ?>
      <div class="large-8 columns">
        <a href="<?php echo $forms_documents[$key]['phila_action_panel_link_multi']; ?>" class="card action-panel mbn">
          <div class="panel equal" data-equalizer-watch="" style="height: 237px;">
            <header class="">
              <div class="">
                <span class="fa-stack fa-4x center" aria-hidden="true">
                  <i class="fa fa-circle fa-stack-2x"></i>
                  <i class="fa fa-file-text fa-stack-1x fa-inverse"></i>
                </span>
              </div>
              <span class=""><?php echo $forms_documents[$key]['phila_action_panel_cta_text_multi']; ?></span>
            </header>
            <hr class="mll mrl"> <span class="details"><?php echo $forms_documents[$key]['phila_action_panel_summary_multi']; ?></span>
          </div>
        </a>
        <?php if ( !empty( $forms_documents[$key]['phila_featured_documents'] ) ) : ?>
          <?php $featured_documents_array = $forms_documents[$key]['phila_featured_documents']; ?>
          <div class="resource-list">
            <ul>
              <?php foreach ( $featured_documents_array as $i => $v):?>
                <?php $featured_document = get_post( $featured_documents_array[$i] ); ?>
                <li class="phm pvs clickable-row" data-href="http://">
                  <a href="<?php echo get_permalink($featured_document); ?>">
                    <div><i class="fa fa-file-text fa-lg" aria-hidden="true"></i></div>
                    <div><?php echo $featured_document->post_title; ?></div>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
        <?php $see_all = array(
            'URL' => $forms_documents[$key]['phila_action_panel_link_multi'],
            'content_type' => strtolower( $forms_documents[$key]['phila_action_panel_cta_text_multi'] ),
            'nice_name' => $forms_documents[$key]['phila_action_panel_cta_text_multi'],
            'is_full' => true
          ); ?>
        <div class="row mtm">
          <div class="columns">
            <?php include( locate_template( 'partials/content-see-all.php' ) ); ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>
<?php endif; ?>

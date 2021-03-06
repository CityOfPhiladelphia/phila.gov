<?php
/* Displays contents of a Collection Page Template. */

$row_content = rwmb_meta('collection_row');?>

<?php if (!phila_util_is_array_empty($row_content)) :?>
<!-- Collection page -->
  <section class="mtl">

    <div class="one-quarter-layout">

    <?php $last_key = phila_util_is_last_in_array( (array) $row_content ); ?>

    <?php foreach ($row_content as $key => $value ) :
      $current_row = $row_content[$key];
      $current_row_option = $current_row['phila_collection_options']; ?>

      <?php if ($current_row_option === 'service') : ?>
        <?php $headline = isset($current_row['service_pages']['phila_custom_text_title']) ? $current_row['service_pages']['phila_custom_text_title'] : '<span class="placeholder">Please enter heading title</span>';?>
        <div class="row one-quarter-row mvl">
          <div class="columns medium-6">
            <h3 id="<?php echo sanitize_title_with_dashes($headline, null, 'save')?>"><?php echo $headline ?></h3>
          </div>
          <div class="columns medium-18 pbxl">
            <div class="row grid-x fat-gutter">
            <?php foreach( $current_row['service_pages']['phila_v2_service_page'] as $service_page ) : ?>
              <div class="flex-container auto small-24 medium-8 column end">
                <a href="<?php echo get_the_permalink($service_page);?>" class="card sub-topic">
                  <div class="content-block">
                    <h4 class="h3"><?php echo get_the_title($service_page); ?></h3>
                    <?php echo rwmb_meta( 'phila_meta_desc', $args = '', $service_page ); ?>
                  </div>
                </a>
              </div>
          <?php endforeach; ?>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <?php if ($current_row_option === 'document') :  ?>
        <?php $headline = isset($current_row['document_pages']['phila_custom_text_title']) ? $current_row['document_pages']['phila_custom_text_title'] : '<span class="placeholder">Please enter heading title</span>';?>
        <div class="row one-quarter-row mvl">
          <div class="columns medium-6">
            <h3 id="<?php echo sanitize_title_with_dashes($headline, null, 'save')?>"><?php echo $headline ?></h3>
          </div>
          <div class="columns medium-18 pbxl">
            <?php foreach($current_row['document_pages']['document_page_group'] as $group): ?>
              <?php $title =    isset($group['phila_custom_wysiwyg']['phila_wysiwyg_title']) ? $group['phila_custom_wysiwyg']['phila_wysiwyg_title'] : '' ;
              $content = isset ($group['phila_custom_wysiwyg']['phila_wysiwyg_content']) ? $group['phila_custom_wysiwyg']['phila_wysiwyg_content'] : '';
              ?>
              <?php if ( $title ) : ?>
                <h4><?php echo $title?></h4>
              <?php endif; ?>
              <?php if ( $content ) : ?>
                <p><?php echo $content ?></p>
              <?php endif; ?>
              <div class="resource-list mbxl">
                <ul>
                  <?php foreach($group['phila_document_page_picker'] as $doc): ?>
                  <li class="phm pvs clickable-row" data-href="<?php echo get_the_permalink($doc); ?>">
                    <a href="<?php echo get_the_permalink($doc);?>">                <div><i class="fas fa-file-alt" aria-hidden="true"></i></div> <?php echo get_the_title($doc); ?></a></li>
                  <?php endforeach; ?>
                </ul>
              </div>
          <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($current_row_option === 'program') :  ?>

        <?php $headline = isset($current_row['program_pages']['phila_custom_text_title']) ? $current_row['program_pages']['phila_custom_text_title'] : '<span class="placeholder">Please enter heading title</span>';?>

        <div class="row one-quarter-row mvl">
          <div class="columns medium-6">
            <h3 id="<?php echo sanitize_title_with_dashes($headline, null, 'save')?>"><?php echo $headline ?></h3>
          </div>
          <div class="columns medium-18 pbxl">
            <div class="row fat-gutter">
              <?php foreach( $current_row['program_pages']['phila_select_programs'] as $program_page ) : ?>
                <div class="medium-12 columns end mbl">
                  <?php $off_site = rwmb_meta('prog_off_site_link', $args = array(), $post_id =  $program_page); ?>
                  <a href="<?php echo !empty($off_site) ? $off_site : get_the_permalink($post = $program_page); ?>" class="card program-card">
                    <?php
                    $img = rwmb_meta( 'prog_header_img', $args = array( 'size' => 'medium', 'limit' => 1 ), $program_page );
                    $img = reset( $img );?>
                    <img src="<?php echo $img['url'] ?>" alt="<?php echo $img['alt']?>">
                    <div class="content-block">
                      <h4 class="h3"><?php echo get_the_title($program_page); ?></h3>
                      <?php echo rwmb_meta( 'phila_meta_desc', $args = '', $program_page ); ?></h4>
                    </div>
                  </a>
                </div>
            <?php endforeach; ?>
          </div>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($current_row_option === 'free_text') :  ?>

        <?php foreach( $current_row['free_text'] as $free_text ) :
          ?>
          <?php $headline = isset($free_text['phila_custom_wysiwyg']['phila_wysiwyg_title']) ? $free_text['phila_custom_wysiwyg']['phila_wysiwyg_title'] : '<span class="placeholder">Please enter heading title</span>'; ?>
          <?php $expand_collapse = isset($free_text['expand_collapse']) ? $free_text['expand_collapse'] : ''; ?>


          <div class="row one-quarter-row mvl">
            <div class="columns medium-6">
                <h3 id="<?php echo sanitize_title_with_dashes($headline, null, 'save')?>"><?php echo $headline ?></h3>
            </div>
            <div class="columns medium-18 pbxl">
            <?php if ($expand_collapse == 1) :?>
              <div class="expandable" aria-controls="<?php echo sanitize_title_with_dashes($headline, null, 'save') . '-control' ?>" aria-expanded="false">
            <?php endif; ?>
              <?php echo apply_filters( 'the_content', $free_text['phila_custom_wysiwyg']['phila_wysiwyg_content'] ); ?>
              <?php if ($expand_collapse == 1) :?>
                  </div><a href="#" data-toggle="expandable" class="float-right" id="<?php echo sanitize_title_with_dashes($headline, null, 'save') . '-control' ?>"> More + </a>
              <?php endif; ?>
            </div>
            
          </div>      
          <?php endforeach; ?>

      <?php endif; ?>

      <?php if ($last_key != $key) : ?>
        <hr class="margin-auto"/>
      <?php endif; ?>

    <?php endforeach ?>
    </div>
    </section>
  <?php wp_reset_postdata(); ?>
<!-- Collection page -->
<?php endif; ?>
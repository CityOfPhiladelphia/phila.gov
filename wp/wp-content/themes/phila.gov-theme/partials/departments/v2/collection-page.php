<?php
/* Displays contents of a Collection Page Template. */

$row_content = rwmb_meta('collection_row');

foreach ($row_content as $key => $value ) :
  $current_row = $row_content[$key];

  $current_row_option = $current_row['phila_collection_options'];

  if ($current_row_option === 'service') :

    $headline = $current_row['service_pages']['phila_custom_text_title'];

    ?>
    <div class="row">
      <div class="columns medium-6">
        <?php echo $headline ?>
      </div>
      <div class="columns medium-18">
        <?php foreach( $current_row['service_pages']['phila_v2_service_page'] as $service_page ) : ?>
          <a href="<?php echo get_the_permalink($service_page);?>"><?php echo get_the_title($service_page); ?></a>
      <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($current_row_option === 'document') :  ?>

    <?php $headline = $current_row['document_pages']['phila_custom_text_title'];?>
    <div class="row">
      <div class="columns medium-6">
        <?php echo $headline ?>
      </div>
      <div class="columns medium-18">
        <?php foreach( $current_row['document_pages']['phila_document_page_picker'] as $document_page ) : ?>
          <a href="<?php echo get_the_permalink($document_page);?>"><?php echo get_the_title($document_page); ?></a>
      <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($current_row_option === 'program') :  ?>

    <?php $headline = $current_row['program_pages']['phila_custom_text_title'];?>
    <div class="row">
      <div class="columns medium-6">
        <?php echo $headline ?>
      </div>
      <div class="columns medium-18">
        <?php foreach( $current_row['program_pages']['phila_select_programs'] as $program_page ) : ?>
          <a href="<?php echo get_the_permalink($program_page);?>"><?php echo get_the_title($program_page); ?></a>
      <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($current_row_option === 'free_text') :  ?>

    <?php foreach( $current_row['free_text'] as $free_text ) : ?>

      <div class="row">
        <div class="columns medium-6">
          <?php echo $free_text['phila_custom_wysiwyg']['phila_wysiwyg_title'] ?>
        </div>
        <div class="columns medium-18">
          <?php echo $free_text['phila_custom_wysiwyg']['phila_wysiwyg_content'] ?>

        </div>
      </div>
      <?php endforeach; ?>

  <?php endif; ?>


<?php endforeach ?>

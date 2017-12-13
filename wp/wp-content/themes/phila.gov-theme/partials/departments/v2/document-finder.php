<?php
/* Displays list of PDFs assigned to category. */

?>
<?php $cat = get_the_category();
  $attachment_args = array(
    'category' => $cat[0]->term_id,
    'post_type' => 'attachment'
);

$attachments = get_posts($attachment_args);?>
<div class="row mtl">
  <div class="large-24 columns">
    <table class="responsive">
      <thead>
        <tr>
          <th>Title</th>
          <th>Type</th>
          <th>Author</th>
          <th>Date</th>
        </tr>
      </thead>
      <?php foreach( $attachments as $attachment ) : ?>
        <?php
        $attachment_data = wp_prepare_attachment_for_js( $attachment->ID );

        $file_type = $attachment_data['subtype'];

        $attachment_published = rwmb_meta( 'phila_document_page_release_date', $args = array(), $post_id = $attachment->ID );

        $type = get_the_terms($attachment->ID, 'media_type');
        $author = get_the_terms($attachment->ID, 'media_author');
        $types = array();
        $authors = array();
        if ( empty($attachment_published ) ){
          $attachment_published = get_the_date( $d = '', $attachment->ID );
        }?>
        <tr class="clickable-row" data-href="<?php echo $attachment->guid; ?>" id="<?php echo phila_format_uri($attachment->title); ?>">
          <td>
            <?php echo $attachment->post_title;
            echo $file_type;
             ?>
          </td>
          <td>
            <?php foreach ($type as $t) :
              array_push($types, $t->name);
            endforeach;
            echo implode(", ", $types);?>
          </td>
          <td>
            <?php foreach ($author as $a) :
              array_push($authors, $a->name);
            endforeach;
            echo implode(", ", $authors);?>
          </td>
          <td>
            <?php echo $attachment_published ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>

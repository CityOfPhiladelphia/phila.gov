<?php
/*
* Template for displaying document pages
*/
?>
<header class="entry-header small-24 columns">
  <?php the_title( '<h1 class="entry-title contrast">', '</h1>' ); ?>
</header><!-- .entry-header -->
<div data-swiftype-index='true' class="entry-content small-24 columns">
  <?php
  $document_description = rwmb_meta( 'phila_document_description', $args = array( 'type' => 'textarea' ));
  $global_document_published = rwmb_meta( 'phila_document_released', $args = array( 'type' => 'date' ) );

  $documents = rwmb_meta( 'phila_files', $args = array( 'type' => 'file_advanced' ) );
  ?>
  <p class="description"><?php echo $document_description; ?></p>
  <table class="no-borders align-left mvm mvl-mu tablesaw tablesaw-stack" data-tablesaw-mode="stack">
    <thead class="light-head">
      <tr>
        <th>
          <h3 class="alternate">Name</h3>
        </th>
        <th>
          <h3 class="alternate">Description</h3>
        </th>
        <th>
          <h3 class="alternate">Released</h3>
        </th>
        <th>
          <h3 class="alternate">Format</h3>
        </th>
      </tr>
    </thead>
    <tbody>
<?php
  foreach ( $documents as $document ): ?>
    <?php
    $id = phila_get_attachment_id_by_url( $document['url'] );
    $attachment_data = wp_prepare_attachment_for_js( $id[0] );
    $file_type = $attachment_data['subtype'];
    $content = $attachment_data['description'];
    $document_published = rwmb_meta( 'phila_document_page_release_date', $args = array(), $post_id = $id[0] );
    ?>
    <tr class="clickable-row" data-href=" <?php echo $document['url']; ?>">
      <td>
        <a href="<?php echo $document['url'] ?>"><?php echo $document['title']; ?></a>
      </td>
        <td>
          <?php if ( $content ): ?>
            <span class="small-text"> <?php echo $content; ?> </span>
          <?php else:?>
            <span class="small-text"> No description available. </span>
          <?php endif; ?>
        </td>
        <td>
          <?php if( ! $document_published == '' ): ?>
            <span class="small-text"> <?php echo $document_published; ?> </span>
          <?php else: ?>
            <span class="small-text"> <?php echo $global_document_published; ?> </span>
          <?php endif; ?>
        </td>
        <td>
          <?php if ( $file_type ): ?>
            <div class="row">
              <div class="medium-12 columns">
                <span class="small-text file-type"> <?php phila_format_document_type( $file_type ); ?> </span>
              </div>
              <div class="medium-12 columns">
                <a href="<?php echo $document['url'] ?>"><i class="fa fa-download fa-2x"></i></a>
              </div>
            </div>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

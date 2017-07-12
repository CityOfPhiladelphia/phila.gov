<?php
/*
* Template for displaying document pages
*/
?>
<div class="row">
  <header class="small-24 columns">
    <?php the_title( '<h1 class="entry-title contrast">', '</h1>' ); ?>
  </header><!-- .entry-header -->
</div>
<div class="row">
  <div data-swiftype-index="true" class="entry-content small-24 columns">
    <?php
    //Documents are using a wysiwyg editor for body content
    $document_description = rwmb_meta( 'phila_document_description' );
    $date_override = rwmb_meta( 'phila_override_release_date' );
    $global_document_published = rwmb_meta( 'phila_document_released', $args = array( 'type' => 'date' ) );

    $documents = rwmb_meta( 'phila_files', $args = array( 'type' => 'file_advanced' ) );
    ?>
    <p class="description"><?php echo $document_description; ?></p>
    <table class="responsive">
      <thead>
        <tr>
          <th>
            Name
          </th>
          <th>
            Description
          </th>
          <th>
            Released
          </th>
          <th>
            Format
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

      if ( empty($document_published) ){
        $document_published = get_the_date( $d = '', $id[0] );
      }
      ?>
      <tr class="clickable-row" data-href="<?php echo $document['url']; ?>">
        <td>
          <a href="<?php echo $document['url'] ?>"><?php echo $document['title']; ?> <span class="show-for-sr"><?php phila_format_document_type( $file_type ); ?></span></a>
        </td>
          <td>
            <?php if ( $content ): ?>
              <?php echo $content; ?>
            <?php else:?>
               No description available.
            <?php endif; ?>
          </td>
          <td>
            <?php if ($date_override === '1') : ?>
              <?php echo $global_document_published; ?>
            <?php else: ?>
              <?php echo $document_published; ?>
            <?php endif; ?>
          </td>
          <td>
            <?php if ( $file_type ): ?>
            <div aria-hidden="true">
              <span class="file-type prs"><?php phila_format_document_type( $file_type ); ?></span>
              <a href="<?php echo $document['url'] ?>" data-file-name="<?php echo $document['title']; ?>" aria-hidden="true"><i class="fa fa-download fa-2x"></i>
              </a>
            </div>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
/*
* Template for displaying document pages
*/
?>
<div class="row">
  <header class="small-24 columns">
    <?php the_title( '<h1 class="entry-title contrast">', '</h1>' ); ?>
  </header>
</div>
<div class="row">
  <div data-swiftype-index="true" class="entry-content small-24 columns">
    <?php
    //Documents are using a wysiwyg editor for body content
    $document_description = rwmb_meta( 'phila_document_description' );
    $date_override = rwmb_meta( 'phila_override_release_date' );
    $global_document_published = rwmb_meta( 'phila_document_released', $args = array( 'type' => 'date' ) );

    $documents = rwmb_meta( 'phila_files', $args = array( 'type' => 'file_advanced' ) );
    $arr_length = count($documents);
    ?>
    <p class="description"><?php echo apply_filters('the_content', $document_description); ?></p>
    <div id="sortable-table-0" class="search-sort-single-table">
      <?php if ($arr_length >= 5) : ?>
        <div class="search">
          <label for="table-search"><span class="screen-reader-text">Filter documents by title or description</span></label>
          <input type="text" class="table-search search-field" placeholder="Filter documents by title or description" />
          <input type="submit" class="search-submit" />
        </div>
      <?php endif; ?>
      <table class="responsive js-hide-empty">
        <thead>
          <tr>
            <th class="<?php echo ($arr_length >= 5 ) ? 'table-sort" data-sort="title' : '' ?>"><span>Name</span></th>
            <th>Description</th>
            <th class="medium-3 <?php echo ($arr_length >= 5 ) ? 'table-sort" data-sort="date' : '' ?>"><span>Released</span></th>
            <th class="medium-2">Format</th>
          </tr>
        </thead>
        <tbody class="search-sortable">
    <?php
      foreach ( $documents as $document ): ?>

        <?php
        $file = wp_prepare_attachment_for_js($document['ID']);
        $file_type = $file['subtype'];
        $document_published = rwmb_meta( 'phila_document_page_release_date', $args = array(), $document['ID']);
        
        if ( empty($document_published) ){
          $document_published = get_the_date( $d = '', $document['ID'] );
        }
        ?>
        <tr class="clickable-row" data-href="<?php echo $document['url']; ?>" id="<?php echo phila_format_uri($document['title']); ?>">
          <td>
            <a href="<?php echo $document['url'] ?>" class="document"><span class="title"><?php echo $document['title']; ?></span> <span class="show-for-sr"><?php phila_format_document_type( $file_type ); ?></span></a>
          </td>
            <td class="description"><?php echo isset( $file['description'] ) ? $file['description'] : ''; ?></td>
            <td class="date">
              <?php if ($date_override === '1') : ?>
                <?php echo $global_document_published; ?>
              <?php else: ?>
                <?php echo $document_published; ?>
              <?php endif; ?>
            </td>
            <td class="format">
              <?php if ( $file_type ): ?>
              <div aria-hidden="true">
                <span class="file-type prs"><?php phila_format_document_type( $file_type ); ?></span>
                <a href="<?php echo $document['url'] ?>" data-file-name="<?php echo $document['title']; ?>" aria-hidden="true"><i class="fas fa-download fa-lg"></i>
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
</div>
<?php 
wp_localize_script('dataLayer', 'the_data', array(
    'page_title' => get_the_title(),
) );
?>
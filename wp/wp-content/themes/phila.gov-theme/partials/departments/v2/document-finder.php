<?php
/* Displays list of PDFs assigned to this page's category. */

?>
<?php
$tables = rwmb_meta('phila_document_table');
$no_pagination = rwmb_meta('phila_doc_no_paginate');
//ensure 0 index for js initialization
$c = -1;
?>
<!-- Document finder -->
<section class="mtl">
  <div class="row mtl">
    <div class="large-24 columns">
      <?php foreach( $tables as $table ) : ?>
        <?php if( !empty( $table['phila_files'] ) ) :
          $c++;
          ?>
          <?php echo !empty( $table['phila_custom_wysiwyg']['phila_wysiwyg_title'] ) ? '<h2 class="bmn" id="' .  sanitize_title_with_dashes($table['phila_custom_wysiwyg']['phila_wysiwyg_title']) .'">' . $table['phila_custom_wysiwyg']['phila_wysiwyg_title'] . '</h2>' : ''; ?>
          <div id="sortable-table-<?php echo $c?>" class="search-sort-table">
            <?php if ( count($table['phila_files']) >= 6) : ?>
              <div class="search">
                <label for="table-search"><span class="screen-reader-text"><?php echo !empty( $table['phila_search_bar_text'] ) ? $table['phila_search_bar_text']  : 'Begin typing to filter documents';?> </span></label>
                <input type="text" class="table-search search-field" placeholder="<?php echo !empty( $table['phila_search_bar_text'] ) ? $table['phila_search_bar_text']  : 'Begin typing to filter documents';?> " />
                <input type="submit" class="search-submit" />
              </div>
            <?php endif; ?>
            <div class="table-wrapper">

              <table class="mbxl js-hide-empty">
                <?php echo !empty( $table['phila_custom_wysiwyg']['phila_wysiwyg_content'] ) ? '<caption class="ptn accessible">' . $table['phila_custom_wysiwyg']['phila_wysiwyg_content'] . '</caption>' : ''; ?>
                <thead>
                  <tr>
                    <th class="table-sort title" data-sort="title"><span>Title</span></th>
                    <th class="description"><span>Description</span></th>
                    <th class="table-sort category" data-sort="category"><span>Category</span></th>
                    <th class="table-sort author" data-sort="author"><span>Author</span></th>
                    <th class="table-sort date" data-sort="date" width="150"><span>Date</span></th>
                    <th class="format" width="100"><span>Format</span></th>
                  </tr>
                </thead>
                <tbody class="search-sortable">
                  <?php

                  foreach ( $table['phila_files'] as $id ) :
                    $file = wp_prepare_attachment_for_js($id);
                    $file_type = $file['subtype'];
                    $document_published = rwmb_meta( 'phila_document_page_release_date', $args = array(), $post_id = $id );

                    if ( empty($document_published) ){
                      $document_published = get_the_date( $d = '', $id );
                    }

                    $full_url = wp_get_attachment_url( $id );
                    $type = get_the_terms($id, 'media_type');
                    $author = get_the_terms($id, 'media_author');

                    $types = array();
                    $authors = array();
                    ?>
                    <tr class="clickable-row" data-href="<?php echo $full_url; ?>" id="<?php echo phila_format_uri( $file['title'] ); ?>">
                      <td>
                        <a href="<?php echo $full_url ?>"><span class="title"><?php echo $file['title'] ?></span> <span class="show-for-sr"><?php phila_format_document_type( $file_type ); ?></span></a>
                      </td>
                      <td class="description"><?php echo isset( $file['description'] ) ? $file['description'] : ''; ?></td>
                      <td class="category"><?php
                        if( !empty( $type ) ) :
                          foreach ( $type as $t ) :
                            array_push( $types, $t->name );
                          endforeach;
                          echo implode( ', ', $types );
                        endif;?></td>
                      <td class="author"><?php
                        if( !empty( $author ) ) :
                          foreach ( $author as $a ) :
                            array_push( $authors, $a->name );
                          endforeach;
                          echo implode( ', ', $authors );
                        endif;
                        ?></td>
                      <td class="date">
                        <?php echo $document_published ?>
                      </td>
                      <td class="format">
                        <?php if ( $file_type ): ?>
                          <div aria-hidden="true">
                            <span class="file-type prs"><small><?php phila_format_document_type( $file_type ); ?></small></span>
                            <a href="<?php echo $full_url ?>" data-file-name="<?php echo $file['title'] ?>" aria-hidden="true"><i class="fas fa-download fa-fw"></i>
                            </a>
                          </div>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php if ( count($table['phila_files'] ) >= 6 && empty( $no_pagination ) ) : ?>
            <ul class="pagination-wrapper no-js">
              <li class="prev">
                <a class="prev-<?php echo $c?>" href="#">Previous</a>
              </li>
              <ul class="pagination"></ul>
              <li class="next">
                <a class="next-<?php echo $c?>" href="#">Next</a>
              </li>
            </ul>
          <?php endif; ?>
        </div>
        <?php endif;?>
      <?php endforeach; ?>
  </div>
  </section>
<?php wp_reset_postdata(); ?>
<!-- Document finder -->
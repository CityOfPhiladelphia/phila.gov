<?php
/* Displays list of PDFs assigned to this page's category. */

?>
<?php
$tables = rwmb_meta('phila_document_table');
//ensure 0 index for js initialization
$c = -1;
?>
<div class="row mtl">
  <div class="large-24 columns">
    <?php foreach( $tables as $table ) : ?>
      <?php if( !empty( $table['phila_files'] ) ) :
        $c++;
        ?>
        <?php echo !empty( $table['phila_custom_wysiwyg']['phila_wysiwyg_title'] ) ? '<h2 class="bmn" id="' .  sanitize_title_with_dashes($table['phila_custom_wysiwyg']['phila_wysiwyg_title']) .'">' . $table['phila_custom_wysiwyg']['phila_wysiwyg_title'] . '</h2>' : ''; ?>
        <div id="sortable-table-<?php echo $c?>" class="document-table">
          <div class="search">
            <label for="table-search"><span class="screen-reader-text">Filter documents by title, category, or author</span></label>
            <input type="text" class="table-search search-field" placeholder="Filter documents by title, category, or author" />
            <input type="submit" class="search-submit" />
          </div>
          <table class="responsive mbxl">
            <?php echo !empty( $table['phila_custom_wysiwyg']['phila_wysiwyg_content'] ) ? '<caption class="ptn accessible">' . $table['phila_custom_wysiwyg']['phila_wysiwyg_content'] . '</caption>' : ''; ?>
            <thead>
              <tr>
                <th class="table-sort" data-sort="title"><span>Title</span></th>
                <th class="table-sort" data-sort="category"><span>Category</span></th>
                <th class="table-sort" data-sort="author"><span>Author</span></th>
                <th class="table-sort" data-sort="date"><span>Date</span></th>
                <th><span>Format</span></th>
              </tr>
            </thead>
            <tbody class="search-sortable">
              <?php

              foreach ( $table['phila_files'] as $id ) :

                $file = wp_prepare_attachment_for_js($id);
                $file_type = $file['subtype'];

                $published = rwmb_meta( 'phila_document_page_release_date', $args = array(), $post_id = $id );

                if ( empty($published) ){
                  $published = get_the_date( $d = '', $id );
                }
                $url = get_post_meta($id, 'amazonS3_info');
                $full_url = get_site_url() . '/' . $url[0]['key'];
                $type = get_the_terms($id, 'media_type');
                $author = get_the_terms($id, 'media_author');
                $types = array();
                $authors = array();
                ?>
                <tr class="clickable-row" data-href="<?php echo $full_url; ?>" id="<?php echo phila_format_uri( $file['title'] ); ?>">
                  <td>
                    <a href="<?php echo $full_url ?>"><span class="title"><?php echo $file['title'] ?></span> <span class="show-for-sr"><?php phila_format_document_type( $file_type ); ?></span></a>
                  </td>
                  <td class="category">
                    <?php
                    if( !empty( $type ) ) :
                      foreach ( $type as $t ) :
                        array_push( $types, $t->name );
                      endforeach;
                      echo implode( ', ', $types );
                    endif;?>
                  </td>
                  <td class="author">
                    <?php
                    if( !empty( $author ) ) :
                      foreach ( $author as $a ) :
                        array_push( $authors, $a->name );
                      endforeach;
                      echo implode( ', ', $authors );
                    endif;
                    ?>
                  </td>
                  <td class="date">
                    <?php echo $published ?>
                  </td>
                  <td class="format">
                    <?php if ( $file_type ): ?>
                      <div aria-hidden="true">
                        <span class="file-type prs"><?php phila_format_document_type( $file_type ); ?></span>
                        <a href="<?php echo $full_url ?>" data-file-name="<?php echo $file['title'] ?>" aria-hidden="true"><i class="fa fa-download fa-2x"></i>
                        </a>
                      </div>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif;?>
    <?php endforeach; ?>
  </div>
</div>

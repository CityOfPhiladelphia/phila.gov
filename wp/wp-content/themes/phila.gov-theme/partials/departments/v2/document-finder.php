<?php
/* Displays list of PDFs assigned to this page's category. */

?>
<?php
$tables = rwmb_meta('phila_document_table'); ?>
<div class="row mtl">
  <div class="large-24 columns">
    <?php foreach( $tables as $table ) : ?>
      <?php if( !empty( $table['phila_files'] ) ) : ?>
        <?php echo !empty( $table['phila_custom_wysiwyg']['phila_wysiwyg_title'] ) ? '<h2 class="bmn">' . $table['phila_custom_wysiwyg']['phila_wysiwyg_title'] . '</h2>' : ''; ?>

        <table class="responsive mbxl">
          <?php echo !empty( $table['phila_custom_wysiwyg']['phila_wysiwyg_content'] ) ? '<caption class="ptn">' . $table['phila_custom_wysiwyg']['phila_wysiwyg_content'] . '</caption>' : ''; ?>
          <thead>
            <tr>
              <th>Title</th>
              <th>Category</th>
              <th>Author</th>
              <th>Date</th>
              <th>Format</th>
            </tr>
          </thead>
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
                <a href="<?php echo $full_url ?>"><?php echo $file['title'] ?> <span class="show-for-sr"><?php phila_format_document_type( $file_type ); ?></span></a>
              </td>
              <td>
                <?php
                if( !empty( $type ) ) :
                  foreach ( $type as $t ) :
                    array_push( $types, $t->name );
                  endforeach;
                  echo implode( ', ', $types );
                endif;?>
              </td>
              <td>
                <?php
                if( !empty( $author ) ) :
                  foreach ( $author as $a ) :
                    array_push( $authors, $a->name );
                  endforeach;
                  echo implode( ', ', $authors );
                endif;
                ?>
              </td>
              <td>
                <?php echo $published ?>
              </td>
              <td>
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

      </table>
    <?php endif;?>
  <?php endforeach; ?>
  </div>
</div>

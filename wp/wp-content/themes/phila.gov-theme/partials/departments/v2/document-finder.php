<?php
/* Displays list of PDFs assigned to this page's category. */

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
          <th>Category</th>
          <th>Author</th>
          <th>Date</th>
          <th>Format</th>
        </tr>
      </thead>
      <?php foreach( $attachments as $attachment ) : ?>
        <?php
        $attachment_data = wp_prepare_attachment_for_js( $attachment->ID );

        $file_type = $attachment_data['subtype'];

        if ( $file_type == 'pdf' || $file_type ==' docx' ) :
          $attachment_published = rwmb_meta( 'phila_document_page_release_date', $args = array(), $post_id = $attachment->ID );

          $attachment_url = get_post_meta($attachment->ID, 'amazonS3_info');

          $url = get_site_url() . '/' . $attachment_url[0]['key'];

          $date_override = rwmb_meta( 'phila_override_release_date' );

          $type = get_the_terms($attachment->ID, 'media_type');
          $author = get_the_terms($attachment->ID, 'media_author');
          $types = array();
          $authors = array();

          if ( empty( $attachment_published ) ){
            $attachment_published = get_the_date( $d = '', $attachment->ID );
          }
          ?>
          <tr class="clickable-row" data-href="<?php echo $url; ?>" id="<?php echo phila_format_uri($attachment->title); ?>">
            <td>
              <a href="<?php echo $url ?>"><?php echo $attachment->post_title ?> <span class="show-for-sr"><?php phila_format_document_type( $file_type ); ?></span></a>
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
              <?php echo $attachment_published ?>
            </td>
            <td>
              <?php if ( $file_type ): ?>
              <div aria-hidden="true">
                <span class="file-type prs"><?php phila_format_document_type( $file_type ); ?></span>
                <a href="<?php echo $url ?>" data-file-name="<?php echo $attachment->title ?>" aria-hidden="true"><i class="fa fa-download fa-2x"></i>
                </a>
              </div>
              <?php endif; ?>
            </td>
          </tr>
        <?php endif; ?>
      <?php endforeach; ?>
    </table>
  </div>
</div>

<?php
/* Displays list of PDFs assigned to category. */

?>
<?php $cat = get_the_category();
  $attachment_args = array(
    'category' => $cat[0]->term_id,
    'post_type' => 'attachment'
);

$attachments = get_posts($attachment_args);
d($attachments); ?>
<div class="row mtl">
  <div class="large-24 columns">
    <table>
      <thead>
        <tr>
          <th>Title</th>
          <th>Type</th>
          <th>Author</th>
          <th>Date</th>
        </tr>
      </thead>
      <?php foreach( $attachments as $attachment ) : ?>
        <?php d($attachment)?>
        <tr>
          <td>
            <?php echo $attachment->post_title ?>
          </td>
          <td>
            <?php echo $attachment->type ?>
          </td>
          <td>
            <?php echo $attachment->author ?>
          </td>
          <td>
            <?php echo $attachment->post_date ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>

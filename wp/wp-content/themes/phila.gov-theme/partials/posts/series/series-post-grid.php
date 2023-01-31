<?php $series_posts = rwmb_meta('phila_post_picker'); ?>

<div class="columns medium-18 pbxl">
  <div class="mbl">
  <?php foreach( $series_posts as $collection_post_id ) : ?>
    <?php
      global $post;
      $post = get_post( $collection_post_id, OBJECT );
      setup_postdata( $post );
    ?>
    <div class="cell medium-8 align-self-stretch">
      <?php include( locate_template( 'partials/posts/content-card-image.php' ) ); ?>
    </div>
  <?php endforeach; ?>
  </div>
</div>
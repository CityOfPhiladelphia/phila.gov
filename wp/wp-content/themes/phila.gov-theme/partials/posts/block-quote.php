<?php
/*
block quote shortcode render
*/
?>

<?php if ( !empty( $a['text'] ) ) : ?>
  <blockquote>
    <p><?php echo $a['text'] ?></p>
  </blockquote>
<?php endif; ?>
<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package phila-gov
 */
?>

<?php
  $append_before_wysiwyg = rwmb_meta( 'phila_append_before_wysiwyg', $args = array('type' => 'textarea'));
  $append_after_wysiwyg = rwmb_meta( 'phila_append_after_wysiwyg', $args = array('type' => 'textarea'));
?>
<article id="post-<?php the_ID(); ?>">
  <div class="row">
    <header class="entry-header small-24 columns">
      <?php the_title( '<h1 class="entry-title contrast">', '</h1>' ); ?>
    </header><!-- .entry-header -->
  </div>
  <div class="row">
    <div data-swiftype-index='true' class="entry-content columns">
      <!-- If Custom Markup append_before_wysiwyg is present print it -->
      <?php if (!$append_before_wysiwyg == ''):?>
        <?php echo $append_before_wysiwyg; ?>
      <?php endif; ?>

        <?php the_content(); ?>

        <!-- If Custom Markup append_after_wysiwyg is present print it -->
        <?php if (!$append_after_wysiwyg == ''):?>
          <?php echo $append_after_wysiwyg; ?>
        <?php endif; ?>
    </div><!-- .entry-content -->
  </div>
</article><!-- #post-## -->

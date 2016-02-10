<?php
/**
 * The template part for displaying a message that posts cannot be found.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package phila-gov
 */
?>

<section class="no-results not-found">
  <header class="row">
    <div class="small-24 columns">
      <h1 class="h2"><?php _e( 'Nothing Found', 'phila-gov' ); ?></h1>
    </div>
  </header><!-- .page-header -->

  <div class="row">
    <div class="page-content small-24 columns">
      <?php still_migrating_content();?>
    </div><!-- .page-content -->
  </div><!-- .row-->
</section><!-- .no-results -->

<?php
/*
*
* Template part
* for displaying on-site department content
*
*/
?>

<div class="small-24 columns">
  <?php the_title( '<h2 class="entry-title">', '</h2>' ); ?>
</div>


<div class="small-24 columns">
  <div class="row">
    <div data-swiftype-index='true' class="entry-content small-24 columns">
       <?php echo the_content();?>
     </div>
  </div>
</div>

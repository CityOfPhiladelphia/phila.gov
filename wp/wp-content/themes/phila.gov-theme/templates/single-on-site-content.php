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
       <!-- If JotForm Embed is present print it -->
       <?php if (function_exists('rwmb_meta')) {
         $jotform = rwmb_meta( 'phila_jotform_embed', $args = array('type' => 'textarea'));
         if ($jotform != ''){
           echo $jotform;
         }
       }
       ?>
     </div>
  </div>
</div>

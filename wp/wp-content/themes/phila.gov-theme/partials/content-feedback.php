<?php

/*
  Partial for our feedback area.
*/
?>
<div class="row">
  <div class="small-24 columns">
    <div class="panel center mbl mtm mtl-mu">
      <p>We're still working on this page's design and content.
        <?php
        $link_text = "How can we make it better?";
        if ( !is_home() && !is_404() && !is_tax() ) :
          $current_cat = phila_util_echo_current_cat_name();
          $dept = "?dept=" . $current_cat;
          $feedback = '<a href="/feedback/%1$s">%2$s</a>';

          echo sprintf($feedback, $dept, $link_text);
        else :
          $feedback = '<a href="/feedback/">%1$s</a>';

          echo sprintf($feedback, $link_text);
        endif;
        ?>
      </p>
    </div>
  </div>
</div>

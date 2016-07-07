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
        if ( !is_home() && !is_404() && !is_tax() && !is_archive() ) :
          $category = get_the_category();
          $current_cats = phila_get_current_department_name( $category, false, false, true );
          $dept = "?dept=" . $current_cats;
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

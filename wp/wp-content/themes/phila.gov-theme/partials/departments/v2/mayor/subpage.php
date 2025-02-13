<?php

$parent = wp_get_post_parent_id($post);
  /* breaking all the rules */
  $images = rwmb_meta( 'phila_v2_homepage_hero', ['size' => 'full'], $parent );
    function extract_full_url($array) {
      foreach ($array as $key => $value) {
        if (is_array($value) && array_key_exists("full_url", $value)) {
          return $value["full_url"];
        }
      }
    }

    $full_url = extract_full_url($images);
?>
<div class="mayor-page">
  <header class="mbxxl">
    <div class="hero-content">
      <div class="hero-subpage">
        <div class="grid-x grid-padding-x expanded align-center">
          <div class="cell medium-6">
            <div class="image-offset">
              <img src="<?php echo $full_url ?>" alt="Mayor Cherelle L. Parker" class="one-philly-mayor" />
            </div>
          </div>
            <div class="cell medium-14 align-self-middle mr-meeseeks">
              <h1>Office of the <span class="MAYOR">Mayor</span></h1>
                </div>
              </div>
              </div><!-- END .row.expanded   -->
              <?php

                /*
                Our navigation menu. We use categories to drive functionality.
                This checks to make sure a category exists for the given page,
                if it does, we render our menu w/ markup.
                */
                  //TODO: clean up menu rendering
                  //
                  phila_get_menu();
              ?>

      </div> <!-- END .hero-wrap  -->
  </header>
</div>
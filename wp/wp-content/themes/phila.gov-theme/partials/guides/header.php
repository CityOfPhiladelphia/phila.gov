<?php
  /*
   * Guides header
  */
?>
<header>
  <div class="hero-full">
    <div class="grid-x">
      <div class="cell medium-12 bg-ben-franklin-blue white hero-full--container">
        <div class="grid-x grid-container align-right">
          <div class="hero-full--title mvl">
            <h1><?php echo the_title() ?></h1>
          </div>
        </div>
      </div>
      <div class="cell medium-12 align-self-stretch hero-image hide-for-small-only" style="background-image:url(<?php echo $hero['full_url']  ?>) ">
        <?php echo !empty($credit) ? '<div class="photo-credit">' . $credit . '</div>' : '' ?>
      </div>
    </div>
  </div>
</header>

<?php
  /*
   * Programs and initiatives header
  */

  $hero = rwmb_meta('prog_header_img', array( 'limit' => 1 ) );
  $hero = reset($hero);

  $sub_hero = rwmb_meta('prog_header_img_sub', array( 'limit' => 1 ));
  $sub_hero = reset( $sub_hero );

  $credit = rwmb_meta('phila_photo_credit');
  $description = rwmb_meta('phila_meta_desc');

?>
<header class="hero-half">
  <div class="grid-x">
    <div class="cell large-12 bg-shade bg-ben-franklin-blue white hero-half--container">
      <div class="grid-x grid-container align-right">
        <div class="hero-half--title mvm">
          <h1><?php echo the_title() ?></h1>
          <p class="description"><?php echo $description ?></p>
        </div>
      </div>
    </div>
    <div class="cell large-12 align-self-stretch hero-image">
      <img src="<?php echo $hero['full_url'] ?>" alt="" class="show-for-large">
      <?php echo !empty($credit) ? '<div class="photo-credit">' . $credit . '</div>' : '' ?>
    </div>
  </div>
</header>

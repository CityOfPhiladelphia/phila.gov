<?php
  /*
   * Programs and initiatives header
  */
  $parent = phila_util_get_furthest_ancestor($post);
  $hero = rwmb_meta( 'prog_header_img', array( 'limit' => 1 ) );
  $hero = reset($hero);

  $sub_hero = rwmb_meta( 'prog_header_img_sub', array( 'limit' => 1 ), $parent->ID);
  $sub_hero = reset( $sub_hero );

  $owner = rwmb_meta( 'phila_program_owner_logo', array( 'limit' => 1 ) );
  $owner = reset($owner);

  $credit = rwmb_meta( 'phila_photo_credit' );
  $description = rwmb_meta( 'phila_meta_desc' );
?>
<header>
  <?php if ( !empty( get_post_ancestors( $post->ID ) ) ) : ?>
    <div class="hero-subpage" style="background-image:url(<?php echo $sub_hero['full_url']  ?>) ">
      <div class="grid-container pvl">
        <div class="grid-x center">
          <div class="cell">
            <h1><?= $parent->post_title ?></h1>
          </div>
        </div>
      </div>
    </div>
    <?php phila_get_menu(); ?>
    <?php get_template_part( 'partials/breadcrumbs' ); ?>
    <div class="grid-container">
      <div class="grid-x">
        <div class="cell">
          <h2 class="contrast"><?php echo the_title(); ?></h2>
        </div>
      </div>
    </div>
  <?php else: ?>
    <div class="hero-half">
      <div class="grid-x">
        <div class="cell medium-12 bg-shade bg-ben-franklin-blue white hero-half--container">
          <div class="grid-x grid-container align-right">
            <div class="hero-half--title mvl">
              <h1><?php echo the_title() ?></h1>
              <p class="description"><?php echo $description ?></p>
              <?php if ( !empty( $owner ) ) : ?>
                <div class="owner-logo">
                  <div class="sep"></div>
                  <img src="<?= $owner['full_url']?>" alt="<?= $owner['alt']?>">
                </div>
              <?php endif;?>
            </div>
          </div>
        </div>
        <div class="cell medium-12 align-self-stretch hero-image hide-for-small-only" style="background-image:url(<?php echo $hero['full_url']  ?>) ">
          <?php echo !empty($credit) ? '<div class="photo-credit">' . $credit . '</div>' : '' ?>
        </div>
      </div>
    <?php phila_get_menu(); ?>
  <?php endif; ?>
</header>

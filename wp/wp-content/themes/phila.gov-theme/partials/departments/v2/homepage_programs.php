<?php
  /* Department homepage program cards
  $cards - required. Array of post ids.
  */
?>
<?php
  if ( !isset( $cards) ):
    $cards = rwmb_meta('phila_select_programs');
  endif;
?>
<?php if ( !empty($cards) ) :?>
  <div class="row">
    <div class="columns">
      <h2 id="programs" class="contrast"><?php echo (get_post_type() == 'programs') ? 'Related programs' : 'Our programs'; ?></h3>
    </div>
  </div>
  <div class="row">
    <div class="columns">
      <div class="row fat-gutter">
        <?php foreach( $cards as $card ) : ?>
          <?php $template = phila_get_selected_template( $card ); ?>
          <div class="medium-8 columns end mbl">
            <a class="card program-card" href="<?php echo ($template == 'prog_off_site') ? rwmb_meta('prog_off_site_link', '', $card) : get_the_permalink($card); ?>">
              <?php
              $img = rwmb_meta( 'prog_header_img', $args = array( 'size' => 'medium', 'limit' => 1 ), $card );
              $img = reset( $img );?>
              <img src="<?php echo $img['url'] ?>" alt="<?php echo $img['alt']?>">
              <div class="content-block">
                <h4 class="h3"><?php echo get_the_title($card); ?></h3>
                <?php echo rwmb_meta( 'phila_meta_desc', $args = '', $card ); ?></h4>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php $all_programs = rwmb_meta( 'phila_v2_programs_link' ) ?>
<?php if ( $all_programs != '' ) :?>
  <div class="row mtm">
    <div class="columns">
      <?php $see_all = array(
          'URL' => $all_programs,
          'content_type' => 'programs',
          'nice_name' => 'Programs'
        );?>
      <?php include( locate_template( 'partials/content-see-all.php' ) ); ?>
    </div>
  </div>
<?php endif; ?>

<?php
  /* Department homepage program cards */

  $cards = rwmb_meta('phila_select_programs');
?>
<?php if ( !empty($cards) ) :?>
  <div class="row">
    <div class="columns">
      <h2 id="programs" class="contrast">Our programs</h3>
    </div>
  </div>
  <div class="row">
    <div class="columns pbxl">
      <div class="row fat-gutter">
        <?php foreach( $cards as $card ) : ?>
          <div class="medium-8 columns end mbl">
            <a class="card program-card" href="<?php echo get_the_permalink($card);?>">
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

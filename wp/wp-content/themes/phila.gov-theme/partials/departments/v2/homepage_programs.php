<?php
  /* Department homepage program cards
  $cards - required. Array of post ids.
  */
?>
<?php
  if ( !isset($title) ) :
    $title = rwmb_meta('phila_custom_text_title');
  endif;
  if ( !isset( $cards ) ):
    $cards = rwmb_meta('phila_select_programs');
  endif;
  if ( !isset($all_programs) ) :
    $all_programs = rwmb_meta( 'phila_v2_programs_link' ); 
  endif;
?>
<?php if ( !empty($cards) ) :?>
  <div class="row">
    <div class="columns">
      <h2 id="programs" class="contrast"><?php echo isset($title) ? $title : 'Our programs'; ?></h3>
    </div>
  </div>
  <div class="row">
    <div class="columns">
      <div class="row fat-gutter program-card-row">
        <?php foreach( $cards as $card ) : ?>
          <?php $template = phila_get_selected_template( $card ); ?>
          <div class="<?php echo count($cards) == 1  ? '' : 'medium-8'?> columns end mbl">
            <a class="card program-card <?php echo count($cards) == 1  ? 'vertical' : ''?>" href="<?php echo ($template == 'prog_off_site') ? rwmb_meta('prog_off_site_link', '', $card) : get_the_permalink($card); ?>">
              <?php
              $img = rwmb_meta( 'prog_header_img', $args = array( 'size' => 'medium', 'limit' => 1 ), $card );
              $img = reset( $img );?>
              <img src="<?php echo $img['url'] ?>" alt="<?php echo $img['alt']?>" />
              <div class="content-block">
                <h4 class="h3 <?php echo ($template == 'prog_off_site') ? 'external' : ''; ?>"><?php echo get_the_title($card); ?></h4>
                <?php echo rwmb_meta( 'phila_meta_desc', $args = '', $card ); ?></h4>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
<?php endif; ?>

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

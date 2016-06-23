<?php
/*
 *
 * Partial for rendering Department Row Two Content
 *
 */
?>
<?php
// Set module row vars
$row_one_col_one_module = rwmb_meta( 'module_row_1_col_1' );

if ( !empty( $row_one_col_one_module ) ){
  $row_one_col_one_type = isset( $row_one_col_one_module['phila_module_row_1_col_1_type'] ) ? $row_one_col_one_module['phila_module_row_1_col_1_type'] : '';
  if ( $row_one_col_one_type == 'phila_module_row_1_col_1_blog_posts' ){
    $row_one_col_one_post_style = $row_one_col_one_module['module_row_1_col_1_options']['phila_module_row_1_col_1_post_style'];
  } else {
    $row_one_col_one_text_title = isset( $row_one_col_one_module['module_row_1_col_1_options']['phila_module_row_1_col_1_texttitle'] ) ? $row_one_col_one_module['module_row_1_col_1_options']['phila_module_row_1_col_1_texttitle'] : '';
    $row_one_col_one_textarea = isset( $row_one_col_one_module['module_row_1_col_1_options']['phila_module_row_1_col_1_textarea'] ) ? $row_one_col_one_module['module_row_1_col_1_options']['phila_module_row_1_col_1_textarea'] : '';
 }
}

$row_one_col_two_module = rwmb_meta( 'module_row_1_col_2' );
$row_one_col_two_action_panel = rwmb_meta( 'module_row_1_col_2_call_to_action_panel' );
$row_one_col_two_connect_panel = rwmb_meta( 'module_row_1_col_2_connect_panel' );

if ( !empty( $row_one_col_two_module ) ){
  $row_one_col_two_type = isset( $row_one_col_two_module['phila_module_row_1_col_2_type'] ) ? $row_one_col_two_module['phila_module_row_1_col_2_type'] : '';

  if ( $row_one_col_two_type == 'phila_module_row_1_col_2_blog_posts' ){
    $row_one_col_two_post_style = 'phila_module_row_1_col_2_post_style_cards';
  } elseif( $row_one_col_two_type == 'phila_module_row_1_col_2_custom_text' ) {

    $row_one_col_two_text_title = $row_one_col_two_module['module_row_1_col_2_options']['phila_module_row_1_col_2_texttitle'];

    $row_one_col_two_textarea = $row_one_col_two_module['module_row_1_col_2_options']['phila_module_row_1_col_2_textarea'];

  } elseif( $row_one_col_two_type == 'phila_module_row_1_col_2_call_to_action_panel' ) {
    $row_one_col_two_action_panel_title = isset( $row_one_col_two_action_panel['phila_action_section_title'] ) ? $row_one_col_two_action_panel['phila_action_section_title'] : '' ;

    $row_one_col_two_action_panel_summary = isset( $row_one_col_two_action_panel['phila_action_panel_summary'] ) ? $row_one_col_two_action_panel['phila_action_panel_summary'] : '';

    $row_one_col_two_action_panel_cta_text = isset( $row_one_col_two_action_panel['phila_action_panel_cta_text'] ) ? $row_one_col_two_action_panel['phila_action_panel_cta_text'] : '';

    $row_one_col_two_action_panel_link = isset( $row_one_col_two_action_panel['phila_action_panel_link'] ) ? $row_one_col_two_action_panel['phila_action_panel_link'] : '';

    $row_one_col_two_action_panel_link_loc  = isset(  $row_one_col_two_action_panel['phila_action_panel_link_loc'] ) ? $row_one_col_two_action_panel['phila_action_panel_link_loc'] : '';

    $row_one_col_two_action_panel_fa_circle  = isset( $row_one_col_two_action_panel['phila_action_panel_fa_circle'] ) ? $row_one_col_two_action_panel['phila_action_panel_fa_circle'] : '' ;

    $row_one_col_two_action_panel_fa = isset( $row_one_col_two_action_panel['phila_action_panel_fa'] ) ? $row_one_col_two_action_panel['phila_action_panel_fa'] : '';
  } else {
    //Determine social media count and column widths
    $row_one_col_two_connect_panel_social_count = isset($row_one_col_two_connect_panel['phila_connect_social'] ) ? count($row_one_col_two_connect_panel['phila_connect_social'] ) : 0 ;

    //TODO: make this into a function that does the math for us
    if ( $row_one_col_two_connect_panel_social_count == 1 ){
      $row_one_col_two_connect_panel_social_column_width = '24';
    } elseif ( $row_one_col_two_connect_panel_social_count == 2 ) {
      $row_one_col_two_connect_panel_social_column_width = '12';
    } elseif  ( $row_one_col_two_connect_panel_social_count == 3 ) {
      $row_one_col_two_connect_panel_social_column_width = '8';
    }

    $row_one_col_two_connect_panel_facebook = isset( $row_one_col_two_connect_panel['phila_connect_social']['phila_connect_social_facebook'] ) ? $row_one_col_two_connect_panel['phila_connect_social']['phila_connect_social_facebook'] :'';

    $row_one_col_two_connect_panel_twitter = isset( $row_one_col_two_connect_panel['phila_connect_social']['phila_connect_social_twitter'] ) ? $row_one_col_two_connect_panel['phila_connect_social']['phila_connect_social_twitter'] :'';

    $row_one_col_two_connect_panel_instagram = isset( $row_one_col_two_connect_panel['phila_connect_social']['phila_connect_social_instagram'] ) ? $row_one_col_two_connect_panel['phila_connect_social']['phila_connect_social_instagram'] :'';

    $row_one_col_two_connect_panel_st_1 = isset( $row_one_col_two_connect_panel['phila_connect_address']['phila_connect_address_st_1'] ) ? $row_one_col_two_connect_panel['phila_connect_address']['phila_connect_address_st_1'] :'';

    $row_one_col_two_connect_panel_st_2 = isset( $row_one_col_two_connect_panel['phila_connect_address']['phila_connect_address_st_2'] ) ? $row_one_col_two_connect_panel['phila_connect_address']['phila_connect_address_st_2'] :'';

    $row_one_col_two_connect_panel_city = isset( $row_one_col_two_connect_panel['phila_connect_address']['phila_connect_address_city'] ) ? $row_one_col_two_connect_panel['phila_connect_address']['phila_connect_address_city'] :'Philadelphia';

    $row_one_col_two_connect_panel_state = isset( $row_one_col_two_connect_panel['phila_connect_address']['phila_connect_address_state'] ) ? $row_one_col_two_connect_panel['phila_connect_address']['phila_connect_address_state'] :'PA';

    $row_one_col_two_connect_panel_zip = isset( $row_one_col_two_connect_panel['phila_connect_address']['phila_connect_address_zip'] ) ? $row_one_col_two_connect_panel['phila_connect_address']['phila_connect_address_zip'] :'19107';

    if ( isset( $row_one_col_two_connect_panel['phila_connect_general']['phila_connect_phone'] ) && is_array( $row_one_col_two_connect_panel['phila_connect_general']['phila_connect_phone'] ) ) {
      $row_one_col_two_connect_panel_phone = '(' . $row_one_col_two_connect_panel['phila_connect_general']['phila_connect_phone']['area'] . ') ' . $row_one_col_two_connect_panel['phila_connect_general']['phila_connect_phone']['phone-co-code'] . '-' . $row_one_col_two_connect_panel['phila_connect_general']['phila_connect_phone']['phone-subscriber-number'];
    } else {
      $row_one_col_two_connect_panel_phone = '';
    }

    if ( isset( $row_one_col_two_connect_panel['phila_connect_general']['phila_connect_fax'] ) && is_array( $row_one_col_two_connect_panel['phila_connect_general']['phila_connect_fax'] ) ) {
      $row_one_col_two_connect_panel_fax = '(' . $row_one_col_two_connect_panel['phila_connect_general']['phila_connect_fax']['area'] . ') ' . $row_one_col_two_connect_panel['phila_connect_general']['phila_connect_fax']['phone-co-code'] . '-' . $row_one_col_two_connect_panel['phila_connect_general']['phila_connect_fax']['phone-subscriber-number'];
    } else {
      $row_one_col_two_connect_panel_fax = '';
    }

    $row_one_col_two_connect_panel_email = isset( $row_one_col_two_connect_panel['phila_connect_general']['phila_connect_email'] ) ? $row_one_col_two_connect_panel['phila_connect_general']['phila_connect_email'] :'';
  }
}
?>

<?php if ( !empty( $row_one_col_one_module['phila_module_row_1_col_1_type'] ) && !empty( $row_one_col_two_module['phila_module_row_1_col_2_type'] ) ) :?>
<!-- Begin Row One MetaBox Modules -->
<section class="department-module-row-one mvl">
  <div class="row equal-height">
  <?php if ( $row_one_col_one_type  == 'phila_module_row_1_col_1_blog_posts' ): ?>
  <!-- Begin Column One -->
    <div class="large-18 columns">
      <div class="row">
      <?php if ($row_one_col_one_post_style == 'phila_module_row_1_col_1_post_style_list'):?>
      <!-- TURN SHORTCODE STRING INTO VAR -->
        <?php echo do_shortcode('[recent-posts list posts="3"]'); ?>
      <?php else: ?>
        <?php echo do_shortcode('[recent-posts posts="3"]'); ?>
      <?php endif;?>
      </div>
    </div>
  <?php elseif ( $row_one_col_one_type  == 'phila_module_row_1_col_1_custom_text' ): ?>
    <div class="large-18 columns">
      <h2 class="contrast"><?php echo($row_one_col_one_text_title); ?></h2>
      <div>
        <?php echo($row_one_col_one_textarea); ?>
      </div>
    </div>
    <!-- End Column One -->
  <?php endif; ?>
  <?php if ( $row_one_col_two_type  == 'phila_module_row_1_col_2_blog_posts' ): ?>
  <!-- Begin Column Two -->
  <div class="large-6 columns">
    <div class="row">
      <?php echo do_shortcode('[recent-posts posts="1"]'); ?>
    </div>
  </div>
  <?php elseif ( $row_one_col_two_type  == 'phila_module_row_1_col_2_custom_text' ): ?>
  <div class="large-6 columns">
    <h2 class="contrast"><?php echo($row_one_col_two_text_title); ?></h2>
    <div class="panel no-margin">
      <div>
        <?php echo($row_one_col_two_textarea); ?>
      </div>
    </div>
  </div>
  <?php elseif ( $row_one_col_two_type  == 'phila_module_row_1_col_2_call_to_action_panel' ): ?>
  <div class="large-6 columns">
    <h2 class="contrast"><?php echo $row_one_col_two_action_panel_title; ?></h2>
    <?php if (!$row_one_col_two_action_panel_link == ''): ?>
    <a href="<?php echo $row_one_col_two_action_panel_link; ?>"  class="action-panel">
      <div class="panel">
      <header>
      <?php if ($row_one_col_two_action_panel_fa_circle): ?>
        <div>
          <span class="fa-stack fa-4x center" aria-hidden="true">
            <i class="fa fa-circle fa-stack-2x"></i>
            <i class="fa <?php echo $row_one_col_two_action_panel_fa; ?> fa-stack-1x fa-inverse"></i>
          </span>
        </div>
      <?php else: ?>
       <div>
         <span><i class="fa <?php echo $row_one_col_two_action_panel_fa; ?> fa-7x" aria-hidden="true"></i></span>
       </div>
      <?php endif; ?>
      <?php if (!$row_one_col_two_action_panel_cta_text == ''): ?>
        <span class="center <?php if ($row_one_col_two_action_panel_link_loc) echo 'external';?>"><?php echo $row_one_col_two_action_panel_cta_text; ?></span>
      <?php endif; ?>
      </header>
      <hr class="mll mrl">
        <span class="details"><?php echo $row_one_col_two_action_panel_summary; ?></span>
      </div>
    </a>
    <?php endif; ?>
  </div>
  <!-- End Column Two -->
  <?php elseif ( $row_one_col_two_type  == 'phila_module_row_1_col_2_connect_panel' ): ?>
  <div class="large-6 columns connect">
    <h2 class="contrast">Connect</h2>
    <div class="vcard panel no-margin">
      <div>
      <?php if ( !$row_one_col_two_connect_panel_social_count == 0 ) : ?>
        <div class="row mbn">
          <?php if ( !$row_one_col_two_connect_panel_facebook == '') : ?>
            <div class="small-<?php echo $row_one_col_two_connect_panel_social_column_width;?> columns center pvxs">
              <a href="<?php echo $row_one_col_two_connect_panel_facebook; ?>" target="_blank" class="phs">
                <i class="fa fa-facebook fa-2x" title="Facebook" aria-hidden="true"></i>
                <span class="show-for-sr">Facebook</span>
              </a>
            </div>
        <?php endif; ?>
        <?php if ( !$row_one_col_two_connect_panel_twitter == '') : ?>
          <div class="small-<?php echo $row_one_col_two_connect_panel_social_column_width;?> columns center pvxs">
            <a href="<?php echo $row_one_col_two_connect_panel_twitter; ?>" target="_blank" class="phs">
              <i class="fa fa-twitter fa-2x" title="Twitter" aria-hidden="true"></i>
              <span class="show-for-sr">Twitter</span>
            </a>
          </div>
        <?php endif; ?>
        <?php if ( !$row_one_col_two_connect_panel_instagram == '') : ?>
          <div class="small-<?php echo $row_one_col_two_connect_panel_social_column_width;?> columns center pvxs">
            <a href="<?php echo $row_one_col_two_connect_panel_instagram; ?>" target="_blank" class="phs">
            <i class="fa fa-instagram fa-2x" title="Instagram" aria-hidden="true"></i>
              <span class="show-for-sr">Instagram</span>
            </a>
          </div>
        <?php endif; ?>
      </div>
  <hr>
 <?php endif; ?>
             <div>
                 <div class="adr mbs">
                   <?php if ( !$row_one_col_two_connect_panel_st_1 == '') : ?>
                     <span class="street-address"><?php echo $row_one_col_two_connect_panel_st_1; ?></a></span><br/>
                   <?php endif; ?>
                   <?php if ( !$row_one_col_two_connect_panel_st_2 == '') : ?>
                     <span class="street-address"><?php echo $row_one_col_two_connect_panel_st_2; ?></a></span><br/>
                   <?php endif; ?>
                   <?php if ( !$row_one_col_two_connect_panel_st_1 == '') : ?>
                     <span class="locality"><?php echo $row_one_col_two_connect_panel_city; ?><span>, <span class="region" title="Pennsylvania"> <?php echo $row_one_col_two_connect_panel_state; ?></span> <span class="postal-code"><?php echo $row_one_col_two_connect_panel_zip; ?></span>
                   <?php endif; ?>
                 </div>
                     <?php if ( !$row_one_col_two_connect_panel_phone == '') : ?>
                       <div class="tel pbxs"><span class="type vcard-label">Phone:</span><a href="tel:<?php echo preg_replace('/[^A-Za-z0-9]/', '', $row_one_col_two_connect_panel_phone); ?>"> <?php echo  $row_one_col_two_connect_panel_phone; ?></a></div>
                   <?php endif; ?>
                   <?php if ( !$row_one_col_two_connect_panel_fax == '') : ?>
                     <div class="fax pbxs"><span class="type vcard-label">Fax:</span> <?php echo $row_one_col_two_connect_panel_fax; ?></div>
                 <?php endif; ?>
                 <?php if ( !$row_one_col_two_connect_panel_email == '') : ?>
                     <div class="email pbxs"><span class="vcard-label">Email:</span><a href="mailto:<?php echo $row_one_col_two_connect_panel_email; ?>"> <?php echo $row_one_col_two_connect_panel_email; ?></a></div>
               <?php endif; ?>
                 </div>
             </div>
         </div>
     </div>
  </div>
  <?php endif; ?>
  </div>
</section>
<!-- End Row One MetaBox Modules -->
<?php endif; ?>

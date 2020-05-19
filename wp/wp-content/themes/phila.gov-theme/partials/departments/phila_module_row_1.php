<?php
/*
 *
 * Partial for rendering Department Row One Content
 *
 */
?>
<?php $user_selected_template = phila_get_selected_template(); ?>

<?php
// set category vars
$category = get_the_category();
$category_slug = $category[0]->slug;

// Set module row vars
$row_one_col_one_module = rwmb_meta( 'module_row_1_col_1' );

if ( !empty( $row_one_col_one_module ) ){
  $row_one_col_one_type = isset( $row_one_col_one_module['phila_module_row_1_col_1_type'] ) ? $row_one_col_one_module['phila_module_row_1_col_1_type'] : '';
  $row_one_col_one_text_title = isset( $row_one_col_one_module['module_row_1_col_1_options']['phila_module_row_1_col_1_texttitle'] ) ? $row_one_col_one_module['module_row_1_col_1_options']['phila_module_row_1_col_1_texttitle'] : '';
  $row_one_col_one_textarea = isset( $row_one_col_one_module['module_row_1_col_1_options']['phila_module_row_1_col_1_textarea'] ) ? $row_one_col_one_module['module_row_1_col_1_options']['phila_module_row_1_col_1_textarea'] : '';
 
}

$row_one_col_two_module = rwmb_meta( 'module_row_1_col_2' );
$row_one_col_two_connect_panel = rwmb_meta( 'module_row_1_col_2_connect_panel' );

if ( !empty( $row_one_col_two_module ) ){
  $row_one_col_two_type = isset( $row_one_col_two_module['phila_module_row_1_col_2_type'] ) ? $row_one_col_two_module['phila_module_row_1_col_2_type'] : '';

  if ( $row_one_col_two_type == 'phila_module_row_1_col_2_call_to_action_panel' ) {
    $row_one_col_two_action_panel_title = isset( $row_one_col_two_action_panel['phila_action_section_title'] ) ? $row_one_col_two_action_panel['phila_action_section_title'] : '' ;

    $row_one_col_two_action_panel_summary = isset( $row_one_col_two_action_panel['phila_action_panel_summary'] ) ? $row_one_col_two_action_panel['phila_action_panel_summary'] : '';

    $row_one_col_two_action_panel_cta_text = isset( $row_one_col_two_action_panel['phila_action_panel_cta_text'] ) ? $row_one_col_two_action_panel['phila_action_panel_cta_text'] : '';

    $row_one_col_two_action_panel_link = isset( $row_one_col_two_action_panel['phila_action_panel_link'] ) ? $row_one_col_two_action_panel['phila_action_panel_link'] : '';

    $row_one_col_two_action_panel_link_loc  = isset(  $row_one_col_two_action_panel['phila_action_panel_link_loc'] ) ? $row_one_col_two_action_panel['phila_action_panel_link_loc'] : '';

    $row_one_col_two_action_panel_fa_circle  = isset( $row_one_col_two_action_panel['phila_action_panel_fa_circle'] ) ? $row_one_col_two_action_panel['phila_action_panel_fa_circle'] : '' ;

    $row_one_col_two_action_panel_fa = isset( $row_one_col_two_action_panel['phila_action_panel_fa'] ) ? $row_one_col_two_action_panel['phila_action_panel_fa'] : '';
  }
}
?>

<?php if ( !empty( $row_one_col_one_module['phila_module_row_1_col_1_type'] ) && !empty( $row_one_col_two_module['phila_module_row_1_col_2_type'] ) ) :?>
<!-- Begin Row One MetaBox Modules -->
<section class="department-module-row-one mvl">
  <div class="row">
  <?php if ( $row_one_col_one_type  == 'phila_module_row_1_col_1_custom_text' ): ?>
    <!-- Begin Column One - custom text -->
    <div class="large-16 columns">
      <h2 class="contrast"><?php echo($row_one_col_one_text_title); ?></h2>
      <div>
        <?php echo apply_filters( 'the_content', $row_one_col_one_textarea ); ?>
      </div>
      <?php if ( $row_one_col_one_textarea == '' ) :?>
        <div class="placeholder">
          Please enter content.
        </div>
      <?php endif; ?>
    </div>
    <!-- End Column One -->
  <?php endif; ?>
  <?php if ( $row_one_col_two_type  == 'phila_module_row_1_col_2_connect_panel' ): ?>
    <?php if ($user_selected_template == 'homepage_v2') : ?>
      <?php get_template_part( 'partials/departments/v2/content', 'connect' ); ?>
    <?php else: ?>
      <?php
        $connect_panel = rwmb_meta('module_row_1_col_2_connect_panel');
        $connect_vars = phila_connect_panel($connect_panel);
        include(locate_template('partials/departments/content-connect.php'));
      ?>
    <?php endif; ?>

<?php endif; ?>
  </div>
</section>
<!-- End Row One MetaBox Modules -->
<?php endif; ?>

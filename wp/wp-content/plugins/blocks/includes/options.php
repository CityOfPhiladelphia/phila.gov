<?php
function mercurial_menu() {
  add_options_page(__("Mercurial Options"), __("Mercurial"), 'manage_options', 'mercurial', 'mercurial_options');
}

if ( is_admin() ) { // admin actions
  add_action('admin_menu', 'mercurial_menu');
}

function mercurial_options() {
?>

<div class="wrap">

  <h2><?php _e( 'Mercurial ', 'mercurial' ); ?></h2>
  <?php //settings_errors(); ?>
  <form method="post" action="options.php">
    <?php

      settings_fields( 'mercurial_options' );
      do_settings_sections( 'mercurial_options' );

      submit_button();

    ?>
  </form>
</div>

<?php }

/**
 * Provides default values for the Display Options.
 */
function mercurial_default_display_options() {

  $defaults = array(
    'mcu_show_viewport' =>  '',
    'mcu_delete_revisions'  =>  '',
    'mcu_errors'  =>  '',
    'mcu_theme_login' =>  '',
  );

  return apply_filters( 'mercurial_default_display_options', $defaults );

} // end mercurial_default_display_options

/**
 * Initializes the theme's display options page by registering the Sections,
 * Fields, and Settings.
 *
 * This function is registered with the 'admin_init' hook.
 */
function mercurial_initialize_options() {

  // If the theme options don't exist, create them.
  if( false == get_option( 'mercurial_options' ) ) {
    add_option( 'mercurial_options',
    apply_filters( 'mercurial_default_display_options',
    mercurial_default_display_options() ) );
  } // end if

  // First, we register a section. This is necessary since all future options must belong to a
  add_settings_section(
    'general_settings_section',     // ID used to identify this section and with which to register options
    __( 'Display Options', 'mercurial' ),   // Title to be displayed on the administration page
    'mercurial_general_options_callback', // Callback used to render the description of the section
    'mercurial_options'   // Page on which to add this section of options
  );

  // Next, we'll introduce the fields for toggling the visibility of content elements.
  add_settings_field(
    'mcu_show_viewport',            // ID used to identify the field throughout the theme
    __( 'Viewport', 'mercurial' ),              // The label to the left of the option interface element
    'mercurial_toggle_viewport_callback', // The name of the function responsible for rendering the option interface
    'mercurial_options',  // The page on which this option will be displayed
    'general_settings_section',     // The name of the section to which this field belongs
    array(                // The array of arguments to pass to the callback. In this case, just a description.
      __( 'Activate to display viewport size.', 'mercurial' ),
    )
  );

  add_settings_field(
    'mcu_delete_revisions',
    __( 'Delete Revisions', 'mercurial' ),
    'mercurial_toggle_revisions_callback',
    'mercurial_options',
    'general_settings_section',
    array(
      __( 'Activate to delete revisions.', 'mercurial' ),
    )
  );

  add_settings_field(
    'mcu_errors',
    __( 'Error reporting', 'mercurial' ),
    'mercurial_toggle_error_callback',
    'mercurial_options',
    'general_settings_section',
    array(
      __( 'Activate to display errors.', 'mercurial' ),
    )
  );

  add_settings_field(
    'mcu_theme_login',
    __( 'Login skin', 'mercurial' ),
    'mercurial_toggle_login_callback',
    'mercurial_options',
    'general_settings_section',
    array(
      __( 'Activate to theme the login screen.', 'mercurial' ),
    )
  );

  // Finally, we register the fields with WordPress
  register_setting(
    'mercurial_options',
    'mercurial_options'
  );

} // end mercurial_initialize_options
add_action( 'admin_init', 'mercurial_initialize_options' );

/**
 * This function provides a simple description for the General Options page.
 *
 * It's called from the 'mercurial_initialize_options' function by being passed as a parameter
 * in the add_settings_section function.
 */
function mercurial_general_options_callback() {
  echo '<p>' . __( 'Choose your options.', 'mercurial' ) . '</p>';
} // end mercurial_general_options_callback


/* ------------------------------------------------------------------------ *
 * Field Callbacks
 * ------------------------------------------------------------------------ */

/**
 * This function renders the interface elements for toggling the visibility of the header element.
 *
 * It accepts an array or arguments and expects the first element in the array to be the description
 * to be displayed next to the checkbox.
 */
function mercurial_toggle_viewport_callback($args) {

  // First, we read the options collection
  $options = get_option('mercurial_options');

  // Next, we update the name attribute to access this element's ID in the context of the display options array
  // We also access the mcu_show_viewport element of the options collection in the call to the checked() helper function
  $html = '<input type="checkbox" id="mcu_show_viewport" name="mercurial_options[mcu_show_viewport]" value="1" ' . checked( 1, isset( $options['mcu_show_viewport'] ) ? $options['mcu_show_viewport'] : 0, false ) . '/>';

  // Here, we'll take the first argument of the array and add it to a label next to the checkbox
  $html .= '<label for="mcu_show_viewport">&nbsp;'  . $args[0] . '</label>';

  echo $html;

} // end mercurial_toggle_viewport_callback

function mercurial_toggle_revisions_callback($args) {

  $options = get_option('mercurial_options');

  $html = '<input type="checkbox" id="mcu_delete_revisions" name="mercurial_options[mcu_delete_revisions]" value="1" ' . checked( 1, isset( $options['mcu_delete_revisions'] ) ? $options['mcu_delete_revisions'] : 0, false ) . '/>';
  $html .= '<label for="mcu_delete_revisions">&nbsp;'  . $args[0] . '</label>';

  echo $html;

} // end mercurial_toggle_revisions_callback

function mercurial_toggle_error_callback($args) {

  $options = get_option('mercurial_options');

  $html = '<input type="checkbox" id="mcu_errors" name="mercurial_options[mcu_errors]" value="1" ' . checked( 1, isset( $options['mcu_errors'] ) ? $options['mcu_errors'] : 0, false ) . '/>';
  $html .= '<label for="mcu_errors">&nbsp;'  . $args[0] . '</label>';

  echo $html;

} // end mercurial_toggle_error_callback

function mercurial_toggle_login_callback($args) {

  $options = get_option('mercurial_options');

  $html = '<input type="checkbox" id="mcu_theme_login" name="mercurial_options[mcu_theme_login]" value="1" ' . checked( 1, isset( $options['mcu_theme_login'] ) ? $options['mcu_theme_login'] : 0, false ) . '/>';
  $html .= '<label for="mcu_theme_login">&nbsp;'  . $args[0] . '</label>';

  echo $html;

} // end mercurial_toggle_error_callback


$display_options = get_option( 'mercurial_options' );
// var_dump( $display_options ); /* outputs false */

if( isset( $display_options['mcu_show_viewport'] ) && $display_options[ 'mcu_show_viewport' ] ) {

  add_action('wp_footer', 'spartan_viewport_sizes', 40 );

}

if( isset( $display_options['mcu_delete_revisions'] ) && $display_options[ 'mcu_delete_revisions' ] ) {

  // $wpdb->query( "
  //     DELETE FROM $wpdb->posts
  //     WHERE post_type = 'revision'
  // " );

}

if( isset( $display_options['mcu_errors'] ) && $display_options[ 'mcu_errors' ] ) {

  error_reporting(E_ALL);
  ini_set("display_errors", 1);

}

if( isset( $display_options['mcu_theme_login'] ) && $display_options[ 'mcu_theme_login' ] ) {

  add_action('login_enqueue_scripts', 'spartan_login_scripts_and_styless', 999);

}


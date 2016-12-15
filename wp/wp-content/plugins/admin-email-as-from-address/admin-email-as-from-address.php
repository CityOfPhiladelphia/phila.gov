<?php 
/**
 *  Plugin Name: Use Admin Email as From Address
 *  Plugin URI:  https://burobjorn.nl
 *  Description: Use the admin email address and from name set in Settings->General as From sender email name and address
 *  Author:      Bj&ouml;rn Wijers <burobjorn@burobjorn.nl>, Ramon Fincken <advertentie@creativepulses.nl>
 *  Version:     1.2
 *  Author URI:  https://burobjorn.nl
 *  License:     GPL2 or later
 **/

/**
 * NOTE: 
 * This plugin fixes a bug in WordPress 4.6 (and possible version 4.5.3):
 * 
 * If a From email address is *NOT* set *AND* the $_SERVER[ 'SERVER_NAME' ] is empty
 * WordPress will generate an invalid email address 'wordpress@'
 * 
 * by explicitly setting the From email address we prevent this bug from
 * happening. 
 * 
 * See also issue #25239:
 * https://core.trac.wordpress.org/ticket/25239  
 **/

if( ! class_exists( 'AdminEmailAsFromAddress' ) ) {
  class AdminEmailAsFromAddress {

    function __construct() {
      add_filter( 'wp_mail_from',      array( $this, 'set_from_email' ) ); 
      add_filter( 'wp_mail_from_name', array( $this, 'set_from_name'  ) ); 
      add_filter( 'admin_init' , array( &$this , 'register_fields' ) );  
    } 

    /** 
     * Called by constructor
     * Registers the two fields to override sender From email address
     * and sender From name
     *
     * @param void
     * @return void
     *
     **/
    function register_fields() {
        register_setting( 'general', 'aeafa_mail_from', 'is_email' );
        add_settings_field('aeafa_mail_from', '<label for="aeafa_mail_from">'.__('WordPress email sender From address' , 'aeafa' ).'</label>' , array(&$this, 'aeafa_mail_from_field') , 'general' );

        register_setting( 'general', 'aeafa_mail_name', 'esc_html' );
        add_settings_field('aeafa_mail_name', '<label for="aeafa_mail_name">'.__('WordPress email sender From name' , 'aeafa' ).'</label>' , array(&$this, 'aeafa_mail_name_field') , 'general' );
    }

    /** 
     * Called by settings field
     * Shows field to override sender From email address
     *
     * @param void
     * @return void
     *
     **/
    function aeafa_mail_from_field() {
        $value = get_option( 'aeafa_mail_from', '' );
        echo '<input type="text" id="aeafa_mail_from" name="aeafa_mail_from" value="' . $value . '" />';
	echo '<p class="description" id="aeafa-mail-from-description">'. __( 'This address is used as From emailaddress when WordPress sends out an email. When empty .. the From address will be the admin purposes email address used in the general settings.' ).'</p>';
    }

    /** 
     * Called by settings field
     * Shows field to override sender From name
     *
     * @param void
     * @return void
     *
     **/
    function aeafa_mail_name_field() {
        $value = get_option( 'aeafa_mail_name', '' );
        echo '<input type="text" id="aeafa_mail_name" name="aeafa_mail_name" value="' . $value . '" />';
	echo '<p class="description" id="aeafa-mail-name-description">'. __( 'This value is used as From emailaddress sender name when WordPress sends out an email. When empty .. the default "WordPress" sender name will be used.' ).'</p>';
    }

    /** 
     * Called by 'wp_mail_from' filter
     * Allows setting the From email address 
     * uses the admin_email option by design.
     * If the email address needs to be changed 
     * you need to change the 'admin_email' in Settings->General.
     * You can even keep the admin_mail and create a seperate sender
     * from in Settings->General using the 'WordPress email sender 
     * From address' field.
     *
     * @param string current email address used as from address
     * @return string new email address used for from address
     *
     **/
    function set_from_email( $email ) {
      // Grab default admin mail address first
      $admin_email = get_bloginfo( 'admin_email' );  
      $mail = empty( $admin_email ) ? $email : $admin_email;

      // Replace with override mail address?
      $email_from_field = get_option( 'aeafa_mail_from', '' );
      $mail = empty( $email_from_field ) ? $mail : $email_from_field;

      // Apply further filters
      return apply_filters( 'aeafa_mail_from', $mail);
    }


    /**
     * Called by filter 'wp_mail_from_name'  
     * Allows setting the name used in the From email header
     * defaults to 'WordPress'
     * You can create a seperate sender name
     * from in Settings->General using the 'WordPress email 
     * sender From name' field.
     * 
     * @param string current name
     * @return string new name, defaults to WordPress
     *   
     **/ 
    function set_from_name( $name ){
      // Grab default admin from name first
      $name = empty ( $name ) ? 'WordPress' : $name;

      // Replace with override mail address sender?
      $email_name_field = get_option( 'aeafa_mail_name', '' );
      $name = empty( $email_name_field ) ? $name : $email_name_field;

      // Apply further filters
      return apply_filters( 'aeafa_mail_from_name', $name); 
    }
  }

  $admin_email_as_from_address = new AdminEmailAsFromAddress(); 

} else {

  error_log( 'Class AdminEmailAsFromAddress already exists. Plugin activation failed' );

}
?>

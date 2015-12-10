<?php
/**
 * Add sidebars to any part of the content
 * Thanks to jcasabona
 * Reference: http://casabona.org/2014/03/include-sidebar-shortcode-wordpress/
 *
 * @link https://github.com/CityOfPhiladelphia/phila.gov-customization
 *
 * @package phila.gov-customization
 * @since 0.20.0
 */

 if ( class_exists('PhilaGovSidebarShortcode' ) ){
   $phila_load_sidebar_shortcode = new PhilaGovSidebarShortcode();
 }

 class PhilaGovSidebarShortcode {

   public function __construct(){

     add_action( 'init', array( $this, 'register_sidebar_shortcode' ) );

   }

  function sidebar_shortcode( $atts ){
    extract( shortcode_atts( array( 'name' => '' ), $atts ) );

      $categories = get_categories();

      foreach ( $categories as $category ) {
        $cat_slug = $category->slug;
        $cat_name = $category->name;
        $cat_id = $category->cat_ID;

        if ( $name == $cat_slug ){

          $current_sidebar_name = 'sidebar-' . $cat_slug .'-' . $cat_id;
          break;
        }

      }
      ob_start();
      dynamic_sidebar( $current_sidebar_name );
      $sidebar = ob_get_contents();
      ob_end_clean();

      if ($sidebar !== ''){
        return $sidebar;
      }
      return false;
    }

  function register_sidebar_shortcode(){
    add_shortcode('contact-us', array( $this, 'sidebar_shortcode' ) );
  }

}

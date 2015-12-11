<?php
/**
 * Creates shortcode for use on Department sites, to generate their "Service and Infomration See All" pages
 *
 * @link https://github.com/CityOfPhiladelphia/phila.gov-customization
 *
 * @package phila-gov_customization
 * @since 0.21.0
 */

 if ( class_exists('Phila_Gov_Sidebar_Service_Info_Display' ) ){
   $phila_load_service_info_shortcode = new Phila_Gov_Sidebar_Service_Info_Display();
 }

 class Phila_Gov_Sidebar_Service_Info_Display {

   public function __construct(){

     add_action( 'init', array( $this, 'register_service_info_shortcode' ) );

   }

  function service_info_shortcode( ){
    $categories = get_the_category( );

    $cat_slug = $categories[0]->slug;

    $service_pages = array(
      'post_type' => array(
        'service_post',
        'page',
      ),
      'post_parent' => 0,
      'no_found_rows' => true,
      'category_name' => $cat_slug,
      'nopaging' => true,
      'order' => 'ASC',
      'orderby' =>'name',
    );

    $service_info_pages = new WP_Query( $service_pages );

    if ( $service_info_pages->have_posts() ) {
      echo '<ul>';

      while ( $service_info_pages->have_posts() ) {
        $service_info_pages->the_post();
        echo '<li><a href="' . get_the_permalink()  . '">'. get_the_title() . '</a>';

        $current_page_id = get_the_ID();

        $children = array(
        	'child_of' => $current_page_id,
          'sort_column' => 'menu_order',
        );
        $pages = get_pages( $children );

        if ( $pages ) {
          echo '<ul>';
          foreach( $pages as $page ){
            echo '<li><a href="' . get_page_link( $page->ID ) . '">' . $page->post_title . '</a></li>';
          }
          echo '</ul>';
        }else{
          //close open li
          echo '</li>';
        }
      }
      echo '</ul>';
      wp_reset_postdata();
    }
  }

  function register_service_info_shortcode(){
    add_shortcode('all-services-info-list', array( $this, 'service_info_shortcode' ) );
  }

}

<?php
/**
 * Creates shortcode for use on Department sites, to generate their "Service and Information See All" pages
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
      'orderby' =>'title',
    );

    $service_info_pages = new WP_Query( $service_pages );

    if ( $service_info_pages->have_posts() ) {
      $current_letter = '';
      $letters = array();
      echo '<div class="row small-collapse medium-uncollapse all-services-info-list">';
        echo '<div class="small-22 small-push-2 medium-push-0 medium-18 columns">';
          while ( $service_info_pages->have_posts() ) {

            $service_info_pages->the_post();

            $page_title_letter = strtoupper(substr(get_the_title(),0,1));

            if ($page_title_letter !== $current_letter) {
              echo '<h2 id="' . $page_title_letter . '" class="light-stripe">'. $page_title_letter . '</h2>';

              $current_letter = $page_title_letter;
            }

            echo '<div class="list"><a href="' . get_the_permalink()  . '">'. get_the_title() . '</a></div>';

            array_push($letters, $current_letter);

            }
            $unique_letters = array_unique($letters);
          echo '</div>';
        echo '<div class="small-2 small-pull-22 columns show-for-small-only a-z-list">';
          echo '<nav>';
            echo '<ul class="no-bullet">';
            foreach ($unique_letters as $letter) {
             echo '<li><a href="#' . $letter . '">' . $letter . '</a></li>';
            }
            echo '</ul>';
          echo '</nav>';
        echo '</div>';
      echo '</div>';

      }
      wp_reset_postdata();
    }

  function register_service_info_shortcode(){
    add_shortcode('all-services-info-list', array( $this, 'service_info_shortcode' ) );
  }

}

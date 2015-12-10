<?php
/**
 * Functions for Information Finder
 * lives at /browse
 *
 * @link https://github.com/CityOfPhiladelphia/phila.gov-customization
 *
 * @package phila.gov-customization
 */

function get_parent_topics(){
  $args = array(
      'orderby' => 'name',
      'fields'=> 'all',
      'parent' => 0
 );
    $terms = get_terms( 'topics', $args );
    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
         echo '<ul>';
         foreach ( $terms as $term ) {
             echo '<a href="/browse/' . $term->slug . '"><li class="h4 '. $term->slug  . '">' . $term->name . '</li></a>';
         }
         echo '</ul>';
         echo '</nav>';
    }
}


/*Utility function for Master Topics List */
function get_master_topics(){
  $parent_terms = get_terms('topics', array('orderby' => 'slug', 'parent' => 0, 'hide_empty' => 0));
  echo '<ul>';
  foreach($parent_terms as $key => $parent_term) {

    echo '<li><h3>' . $parent_term->name . '</h3>';
    echo  $parent_term->description;

    $child_terms = get_terms('topics', array('orderby' => 'slug', 'parent' => $parent_term->term_id, 'hide_empty' => 0));

    if($child_terms) {
      echo '<ul class="subtopics">';
      foreach($child_terms as $key => $child_term) {
        echo '<li><h4>' . $child_term->name . '</h4>';
        echo  $child_term->description . '</li></li>';
      }

    }
    echo '</ul>';
  }
}

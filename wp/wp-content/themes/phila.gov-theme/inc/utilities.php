<?php
/**
 * Phila.gov utility functions
 */

//this is used throughout the theme and is meant to be updated once the major switch happens
function phila_util_echo_website_url(){
  echo 'beta.phila.gov';
}

//this form is used throughout the theme and can be updated as needed
function phila_util_echo_tester_url(){
  echo '/sign-up-to-be-a-phila-gov-tester';
}

//spits out a nice version of the department category name
function phila_util_get_current_cat_name(){
  $category = get_the_category();
  foreach( $category as $cat){
    return $cat->name;
  }
}

//spits out a nice version of the department category slug
function phila_util_get_current_cat_slug(){
  $category = get_the_category();
  foreach( $category as $cat){
    return $cat->slug;
  }
}

/* Return post data for the furthest parent */

function phila_util_get_furthest_ancestor( $post ) {

  /* Get an array of Ancestors and Parents if they exist */
  $parents = get_post_ancestors( $post->ID );
  /* Get the top Level page->ID count base 1, array base 0 so -1 */
  $id = ($parents) ? $parents[count($parents)-1]: $post->ID;

  return $parent = get_post( $id );
}

function phila_util_month_format($date){
  if (strlen($date->format('F')) > 5){
     return 'M.';
  } else {
    return 'F';
  }
}

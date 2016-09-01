<?php
/**
 * Sample implementation of the Custom Walker Menu
 * https://developer.wordpress.org/reference/classes/walker/
 *
 * Adds Foundation classes to our menus for easy dropdowns
 */

class phila_gov_walker_service_menu extends Walker_Nav_Menu {

  function start_lvl( &$output, $depth = 0, $args = Array() ) {

   $indent = str_repeat("\t", $depth);
   $output .= "$indent<ul class=\"vertical menu\">";

  }

}

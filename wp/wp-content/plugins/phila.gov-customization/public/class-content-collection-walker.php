<?php

/**
* Custom Walker Menu for displaying content collection pages
*
* @link https://github.com/CityOfPhiladelphia/phila.gov-customization
*
* @package phila-gov_customization
* @since 0.9.0
*/

class Content_Collection_Walker extends Walker_Page {
    function start_lvl(&$output, $depth = 0, $args = Array()) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul>\n";
    }
    function start_el(&$output, $page, $depth = 0, $args = Array(), $current_page = 0) {
        if ( $depth )
            $indent = str_repeat("\t", $depth);
        else
            $indent = '';
        extract($args, EXTR_SKIP);
        $class_attr = 'tab-title';
        if ( !empty($current_page) ) {
            $_current_page = get_page( $current_page );
            if ( (isset($_current_page->ancestors) && in_array($page->ID, (array) $_current_page->ancestors)) || ( $page->ID == $current_page ) || ( $_current_page && $page->ID == $_current_page->post_parent ) ) {
                $class_attr = 'tab-title active';
            }
        } elseif ( (is_single() || is_archive()) && ($page->ID == get_option('page_for_posts')) ) {
            $class_attr = 'tab-title';
        }
        if ( $class_attr != '' ) {
            $class_attr = ' class="' . $class_attr . '"';
            $link_before .= '';
            $link_after = '' . $link_after;
        }
        $output .= $indent . '<li' . $class_attr . '><a href="' . get_page_link($page->ID) . '">' . $link_before . apply_filters( 'the_title', $page->post_title, $page->ID ) . $link_after . '</a>';

        if ( !empty($show_date) ) {
            if ( 'modified' == $show_date )
                $time = $page->post_modified;
            else
                $time = $page->post_date;
            $output .= " " . mysql2date($date_format, $time);
        }
    }
}

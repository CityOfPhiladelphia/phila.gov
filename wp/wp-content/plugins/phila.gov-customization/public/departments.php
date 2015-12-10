<?php
/**
 * Functions for Department Finder
 * lives at /browse
 *
 * @link https://github.com/CityOfPhiladelphia/phila.gov-customization
 *
 * @package phila.gov-customization
 */

function the_dept_description(){
        $dept_desc = rwmb_meta( 'phila_dept_desc', $args = array('type' => 'textarea'));

        if (!$dept_desc == ''){
            return $dept_desc;
    }
}

function get_department_category(){
    $category = get_the_category();
    echo $category[0]->cat_name;
}
